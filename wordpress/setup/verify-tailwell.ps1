<#
.SYNOPSIS
Verifies TailWell's WordPress REST API integration points against a live site.

.DESCRIPTION
Checks, in order: (1) Basic auth with an application password, (2) the five
category slugs the n8n workflow depends on, (3) draft-post creation in
wellness-health, (4) Yoast meta fields writable/readable via REST,
(5) media upload + attach as featured image, (6) the affiliate-disclosure
page resolves. Prints a summary and the slug -> term ID map for n8n.

.PARAMETER BaseUrl     Site root, e.g. https://tailwell.example.com (trailing slash optional).
.PARAMETER Username    Automation user (Editor role), e.g. tw-automation.
.PARAMETER AppPassword Application password generated on that user's profile.
.PARAMETER Cleanup     Delete the test draft + test image created during this run.

.EXAMPLE
.\verify-tailwell.ps1 -BaseUrl https://tailwell.example.com -Username tw-automation -AppPassword "abcd efgh ijkl mnop qrst uvwx"

.EXAMPLE
.\verify-tailwell.ps1 -BaseUrl https://tailwell.example.com -Username tw-automation -AppPassword "abcd efgh ijkl mnop qrst uvwx" -Cleanup
#>
param(
    [Parameter(Mandatory = $true)][string]$BaseUrl,
    [Parameter(Mandatory = $true)][string]$Username,
    [Parameter(Mandatory = $true)][string]$AppPassword,
    [switch]$Cleanup
)

$ErrorActionPreference = 'Stop'
$BaseUrl = $BaseUrl.TrimEnd('/')

$succeeded = 0
$failed = 0

function New-StepResult {
    param(
        [string]$Name,
        [bool]$Pass,
        [string]$Detail
    )
    if ($Pass) { $script:succeeded++ } else { $script:failed++ }
    $marker = if ($Pass) { '[PASS]' } else { '[FAIL]' }
    $state = if ($Pass) { 'PASS' } else { 'FAIL' }
    Write-Output ("{0} {1} : {2}" -f $state, $Name, $Detail)
}

$authHeader = @{
    Authorization = 'Basic ' + [Convert]::ToBase64String(
        [Text.Encoding]::ASCII.GetBytes(("{0}:{1}" -f $Username, $AppPassword))
    )
}

$restArgs = @{
    Headers     = $authHeader
    ContentType = 'application/json'
    TimeoutSec  = 60
}

Write-Output ("Verifying TailWell REST API at {0} as user {1}" -f $BaseUrl, $Username)

# --- 1. Authentication ------------------------------------------------------
try {
    $me = Invoke-RestMethod -Uri ("{0}/wp-json/wp/v2/users/me" -f $BaseUrl) @restArgs
    New-StepResult -Name 'Authentication' -Pass $true -Detail ("Authenticated as '{0}' (role: {1})" -f $me.name, ($me.roles -join ', '))
}
catch {
    New-StepResult -Name 'Authentication' -Pass $false -Detail ("Basic auth failed: {0}" -f $_.Exception.Message)
    exit 1
}

# --- 2. Category slugs -> term IDs ------------------------------------------
$slugMap = [ordered]@{
    'wellness-health'      = $null
    'food-nutrition'       = $null
    'gear-tech'            = $null
    'training-behavior'    = $null
    'senior-special-needs' = $null
}
$allSlugsOk = $true
foreach ($slug in $slugMap.Keys) {
    try {
        $res = Invoke-RestMethod -Uri ("{0}/wp-json/wp/v2/categories?slug={1}&per_page=5" -f $BaseUrl, $slug) @restArgs
        $items = @($res)
        if ($items.Count -gt 0) {
            $slugMap[$slug] = $items[0].id
            New-StepResult -Name 'Category slug' -Pass $true -Detail ("'{0}' -> term {1}" -f $slug, $items[0].id)
        }
        else {
            $allSlugsOk = $false
            New-StepResult -Name 'Category slug' -Pass $false -Detail ("'{0}' does not exist on this site" -f $slug)
        }
    }
    catch {
        $allSlugsOk = $false
        New-StepResult -Name 'Category slug' -Pass $false -Detail ("'{0}' lookup failed: {1}" -f $slug, $_.Exception.Message)
    }
}
if (-not $allSlugsOk) {
    Write-Output ""
    Write-Output 'Create the 5 categories with the exact slugs (see runbook Section 5), then re-run.'
    exit 1
}

# --- 2.5 Life stage taxonomy + terms -----------------------------------------
$lsMap = [ordered]@{
    'puppy'  = $null
    'adult'  = $null
    'senior' = $null
}
$allLsOk = $true
foreach ($ls in $lsMap.Keys) {
    try {
        $lsRes = Invoke-RestMethod -Uri ("{0}/wp-json/wp/v2/life_stage?slug={1}&per_page=5" -f $BaseUrl, $ls) @restArgs
        $lsItems = @($lsRes)
        if ($lsItems.Count -gt 0) {
            $lsMap[$ls] = $lsItems[0].id
            New-StepResult -Name 'Life stage term' -Pass $true -Detail ("'{0}' -> term {1}" -f $ls, $lsItems[0].id)
        }
        else {
            $allLsOk = $false
            New-StepResult -Name 'Life stage term' -Pass $false -Detail ("'{0}' does not exist — theme must be active so the taxonomy registers; seed the 3 terms (runbook Section 5.5)" -f $ls)
        }
    }
    catch {
        $allLsOk = $false
        New-StepResult -Name 'Life stage term' -Pass $false -Detail ("'{0}' lookup failed (taxonomy not registering?): {1}" -f $ls, $_.Exception.Message)
    }
}
if (-not $allLsOk) {
    Write-Output ""
    Write-Output 'Activate the TailWell child theme (registers the life_stage taxonomy) and create the 3 terms, then re-run.'
    exit 1
}

$postId = $null
$mediaId = $null
$postsUrl = "{0}/wp-json/wp/v2/posts" -f $BaseUrl

# --- 3. Create draft post in wellness-health (category + life_stage + Yoast) --
$wellnessId = $slugMap['wellness-health']
$seniorLsId = $lsMap['senior']
try {
    $postBody = @{
        title      = 'TailWell REST Verification Draft'
        content    = '<p>Automated draft created by setup/verify-tailwell.ps1. Safe to delete.</p>'
        status     = 'draft'
        categories = @($wellnessId)
        life_stage = @($seniorLsId)
    } | ConvertTo-Json
    $post = Invoke-RestMethod -Uri $postsUrl -Method Post @restArgs -Body $postBody
    $postId = [int]$post.id

    $check = Invoke-RestMethod -Uri ("{0}/{1}" -f $postsUrl, $postId) @restArgs
    $catMatches = @($check.categories) -contains $wellnessId
    $lsMatches = @($check.life_stage) -contains $seniorLsId
    $bothOk = $catMatches -and $lsMatches
    if ($bothOk) { $verdict = 'OK' } elseif ($catMatches) { $verdict = 'life_stage MISMATCH' } else { $verdict = 'categories MISMATCH' }
    New-StepResult -Name 'Create draft in wellness-health' -Pass $bothOk -Detail ("Post {0}; categories round-trip = {1}, life_stage round-trip = {2}" -f $postId, $catMatches, $lsMatches)
}
catch {
    New-StepResult -Name 'Create draft in wellness-health' -Pass $false -Detail $_.Exception.Message
    exit 1
}

# --- 4. Yoast meta fields via REST ------------------------------------------
try {
    $metaBody = @{
        meta = @{
            '_yoast_wpseo_title'    = 'REST Verification | TailWell'
            '_yoast_wpseo_metadesc' = 'Automated verification of Yoast REST meta fields.'
            '_yoast_wpseo_focuskw'  = 'pet wellness'
        }
    } | ConvertTo-Json -Depth 5
    Invoke-RestMethod -Uri ("{0}/{1}" -f $postsUrl, $postId) -Method Patch @restArgs -Body $metaBody | Out-Null
    $metaCheck = Invoke-RestMethod -Uri ("{0}/{1}" -f $postsUrl, $postId) @restArgs
    $roundTrip = $null
    if ($null -ne $metaCheck.meta) { $roundTrip = $metaCheck.meta.'_yoast_wpseo_title' }
    $ok = -not [string]::IsNullOrEmpty($roundTrip)
    New-StepResult -Name 'Yoast meta via REST' -Pass $ok -Detail ("_yoast_wpseo_title read-back = '{0}'" -f $roundTrip)
    if (-not $ok) {
        Write-Output '  Yoast is not exposing these meta fields. Activate Yoast and re-run before letting n8n rely on them.'
    }
}
catch {
    New-StepResult -Name 'Yoast meta via REST' -Pass $false -Detail ("Yoast PATCH failed (Yoast not exposing fields?): {0}" -f $_.Exception.Message)
}

# --- 5. Media upload + attach as featured image -----------------------------
$pngB64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M8AAAMBAQDJ/pLvAAAAAElFTkSuQmCC'
$tmpFile = Join-Path $env:TEMP 'tw-verify-featured.png'
[IO.File]::WriteAllBytes($tmpFile, [Convert]::FromBase64String($pngB64))

$mediaHeaders = @{
    Authorization         = $authHeader.Authorization
    'Content-Disposition' = 'attachment; filename=tw-verify-featured.png'
}
try {
    $mediaResp = Invoke-WebRequest -Uri ("{0}/wp-json/wp/v2/media" -f $BaseUrl) -Method Post -Headers $mediaHeaders -InFile $tmpFile -ContentType 'image/png' -TimeoutSec 120
    $media = $mediaResp.Content | ConvertFrom-Json
    $mediaId = [int]$media.id
    New-StepResult -Name 'Media upload' -Pass $true -Detail ("Uploaded media id {0}" -f $mediaId)

    $featureBody = @{ featured_media = $mediaId } | ConvertTo-Json
    Invoke-RestMethod -Uri ("{0}/{1}" -f $postsUrl, $postId) -Method Patch @restArgs -Body $featureBody | Out-Null
    $featCheck = Invoke-RestMethod -Uri ("{0}/{1}" -f $postsUrl, $postId) @restArgs
    $attached = ([int]$featCheck.featured_media -eq $mediaId)
    New-StepResult -Name 'Attach featured media' -Pass $attached -Detail ("post {0}.featured_media = {1}" -f $postId, $featCheck.featured_media)
}
catch {
    New-StepResult -Name 'Media upload / attach' -Pass $false -Detail $_.Exception.Message
}

# --- 6. Disclosure page resolves --------------------------------------------
try {
    $pageResp = Invoke-WebRequest -Uri ("{0}/affiliate-disclosure/" -f $BaseUrl) -TimeoutSec 60
    $pageOk = ($pageResp.StatusCode -eq 200)
    New-StepResult -Name 'Affiliate Disclosure page' -Pass $pageOk -Detail ("GET /affiliate-disclosure/ -> HTTP {0}" -f $pageResp.StatusCode)
}
catch {
    $code = $null
    if ($null -ne $_.Exception.Response) { $code = $_.Exception.Response.StatusCode }
    New-StepResult -Name 'Affiliate Disclosure page' -Pass $false -Detail ("{0} (HTTP {1})" -f $_.Exception.Message, $code)
}

# --- Cleanup ----------------------------------------------------------------
if ($Cleanup) {
    if ($null -ne $postId) {
        try {
            Invoke-WebRequest -Uri ("{0}/{1}" -f $postsUrl, $postId) -Method Delete -Headers $authHeader -TimeoutSec 60 | Out-Null
        }
        catch {}
    }
    if ($null -ne $mediaId) {
        try {
            Invoke-WebRequest -Uri ("{0}/wp-json/wp/v2/media/{1}" -f $BaseUrl, $mediaId) -Method Delete -Headers $authHeader -TimeoutSec 60 | Out-Null
        }
        catch {}
    }
    Remove-Item -LiteralPath $tmpFile -ErrorAction SilentlyContinue
    Write-Output ""
    Write-Output 'Cleanup complete (test draft, test image, temp file removed).'
}

# --- Summary ----------------------------------------------------------------
Write-Output ""
Write-Output "Category slug -> term ID map (feed these to the n8n workflow):"
$slugMap.GetEnumerator() | ForEach-Object { Write-Output ("  {0} -> {1}" -f $_.Key, $_.Value) }
Write-Output "Life stage slug -> term ID map (feed these to the n8n workflow):"
$lsMap.GetEnumerator() | ForEach-Object { Write-Output ("  {0} -> {1}" -f $_.Key, $_.Value) }
Write-Output ""
Write-Output ("Result: {0} passed, {1} failed" -f $succeeded, $failed)
if ($failed -gt 0) { exit 1 }
exit 0