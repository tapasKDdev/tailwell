<#
.SYNOPSIS
    TailWell REST API verification — runs every check the n8n automation depends on.
.DESCRIPTION
    Tests, in order:
      1. Auth (Application Password + user role)
      2. Category slug resolution (default: wellness-health)
      3. Draft post creation with that category attached
      4. Yoast SEO meta write + read-back (_yoast_wpseo_*)
      5. /affiliate-disclosure/ page existence ([tw_disclosure] target)
      6. Media upload + featured_media attach to the test post
      7. Cleanup (delete test post + uploaded media)

    Exits 0 only if ALL checks pass. Designed for Windows PowerShell 5.1.
.EXAMPLE
    powershell -ExecutionPolicy Bypass -File rest-api-verification.ps1 `
      -BaseUrl "https://tailwell.example.com" `
      -User "tailwell-bot" `
      -Password "xxxx xxxx xxxx xxxx xxxx xxxx"
#>
[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)][string]$BaseUrl,
    [Parameter(Mandatory = $true)][string]$User,
    [Parameter(Mandatory = $true)][string]$Password,
    [string]$TestCategory = 'wellness-health',
    [string]$MediaFile = ''
)

$ErrorActionPreference = 'Stop'
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

$RestRoot  = $BaseUrl.TrimEnd('/') + '/wp-json/wp/v2'
$Auth      = [Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes("$User`:$Password"))
$Headers   = @{ Authorization = "Basic $Auth" }
$Results   = @()
$Timestamp = Get-Date

Write-Host "TailWell REST verification @ $RestRoot" -ForegroundColor Cyan
Write-Host "Started: $Timestamp`n" -ForegroundColor DarkGray

function New-TestResult {
    param([string]$Name, [bool]$Passed, [string]$Detail)
    $script:Results += [pscustomobject]@{ Check = $Name; Status = $(if ($Passed) { 'PASS' } else { 'FAIL' }); Detail = $Detail }
    $color = if ($Passed) { 'Green' } else { 'Red' }
    Write-Host ("[{0}] {1}: {2}" -f $(if ($Passed) { 'PASS' } else { 'FAIL' }), $Name, $Detail) -ForegroundColor $color
}

function Invoke-Api {
    param([string]$Method, [string]$Path, [object]$Body = $null)
    $uri = "$RestRoot/$Path"
    try {
        if ($null -ne $Body) {
            return Invoke-RestMethod -Method $Method -Uri $uri -Headers $Headers -ContentType 'application/json' -Body $(ConvertTo-Json $Body -Depth 5) -ErrorAction Stop
        } else {
            return Invoke-RestMethod -Method $Method -Uri $uri -Headers $Headers -ErrorAction Stop
        }
    } catch {
        $resp = $_.Exception.Response
        $status = if ($resp) { [int]$resp.StatusCode } else { 0 }
        throw "HTTP $status : $($_.Exception.Message)"
    }
}

# 1. Auth + role --------------------------------------------------------------
try {
    $me = Invoke-Api -Method GET -Path 'users/me?context=edit'
    $roles = @($me.roles)
    $roleOk = ($roles -contains 'editor') -or ($roles -contains 'author') -or ($roles -contains 'administrator')
    New-TestResult '1. Application password + role' $roleOk ("Authenticated as '$($me.slug)' id=$($me.id), roles: $($roles -join ',')")
} catch {
    New-TestResult '1. Application password + role' $false $_.Exception.Message
    Write-Host "`nStopping — auth is the foundation of every later check." -ForegroundColor Yellow
    exit 1
}

# 2. Category slug ------------------------------------------------------------
$categoryId = $null
try {
    $cats = Invoke-Api -Method GET -Path ("categories?slug={0}&_fields=id,name,slug" -f $TestCategory)
    if (@($cats).Count -gt 0) {
        $categoryId = $cats[0].id
        New-TestResult '2. Category slug resolves' $true "slug='$TestCategory' -> id=$categoryId"
    } else {
        New-TestResult '2. Category slug resolves' $false "No category found for slug '$TestCategory'. Create it (exact slug) in Posts > Categories."
    }
} catch {
    New-TestResult '2. Category slug resolves' $false $_.Exception.Message
}

# 3. Draft post creation in that category -------------------------------------
$postId = $null
if ($categoryId) {
    try {
        $body = @{
            title      = "[TailWell REST test] $Timestamp"
            status     = 'draft'
            content    = '<!-- wp:shortcode -->[tw_disclosure]<!-- /wp:shortcode -->'
            categories = @($categoryId)
        }
        $post = Invoke-Api -Method POST -Path 'posts' -Body $body
        $postId = $post.id
        $catsOnPost = @($post.categories)
        $ok = $catsOnPost -contains $categoryId
        New-TestResult '3. Draft post lands in category' $ok ("post id=$postId, categories sent=[$categoryId], got=[$($catsOnPost -join ',')]")
    } catch {
        New-TestResult '3. Draft post lands in category' $false $_.Exception.Message
    }
} else {
    New-TestResult '3. Draft post lands in category' $false 'Skipped (category check failed).'
}

# 4. Yoast meta write + read-back ---------------------------------------------
if ($postId) {
    try {
        $meta = @{
            _yoast_wpseo_title      = "TailWell REST test title $Timestamp"
            _yoast_wpseo_metadesc   = 'TailWell REST verification meta description.'
            _yoast_wpseo_focuskw    = 'tailwell rest check'
        }
        Invoke-Api -Method PATCH -Path ("posts/{0}" -f $postId) -Body @{ meta = $meta } | Out-Null
        $readBack = Invoke-Api -Method GET -Path ("posts/{0}?context=edit&_fields=meta" -f $postId)
        $got = @($readBack.meta.PSObject.Properties | Where-Object { $_.Name -like '_yoast_wpseo_*' })
        $missing = @('_yoast_wpseo_title', '_yoast_wpseo_metadesc', '_yoast_wpseo_focuskw') | Where-Object { -not ($got.Name -contains $_) }
        if (-not $missing) {
            New-TestResult '4. Yoast meta writable via REST' $true "All 3 keys read back on post $postId."
        } else {
            New-TestResult '4. Yoast meta writable via REST' $false ("Keys missing on read-back: {0}. Yoast meta not registered for REST on this install — fix plugin version/config." -f ($missing -join ', '))
        }
    } catch {
        New-TestResult '4. Yoast meta writable via REST' $false $_.Exception.Message
    }
} else {
    New-TestResult '4. Yoast meta writable via REST' $false 'Skipped (no test post).'
}

# 5. /affiliate-disclosure/ page exists ---------------------------------------
try {
    $pages = Invoke-Api -Method GET -Path 'pages?slug=affiliate-disclosure&_fields=id,slug,link'
    $ok = @($pages).Count -gt 0
    New-TestResult '5. Disclosure page resolves' $ok $(if ($ok) { "slug=affiliate-disclosure -> $($pages[0].link)" } else { 'Page /affiliate-disclosure/ does not exist yet — create it and keep the slug.' })
} catch {
    New-TestResult '5. Disclosure page resolves' $false $_.Exception.Message
}

# 6. Media upload + attach ----------------------------------------------------
$mediaId = $null
try {
    $file = $MediaFile
    $mime  = 'application/octet-stream'
    if (-not $file -or -not (Test-Path -LiteralPath $file)) {
        $file = Join-Path $env:TEMP "tailwell-rest-test-$([guid]::NewGuid().ToString('N')).png"
        # 1x1 transparent PNG
        $png = [Convert]::FromBase64String('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==')
        [IO.File]::WriteAllBytes($file, $png)
        $mime = 'image/png'
    } elseif ($file -match '\.png$') { $mime = 'image/png' }
    elseif ($file -match '\.jpe?g$') { $mime = 'image/jpeg' }
    elseif ($file -match '\.gif$')   { $mime = 'image/gif' }

    $fileName = [IO.Path]::GetFileName($file)
    $mediaHeaders = @{}
    $Headers.GetEnumerator() | ForEach-Object { $mediaHeaders[$_.Key] = $_.Value }
    $mediaHeaders['Content-Disposition'] = "attachment; filename=`"$fileName`""
    $media = Invoke-RestMethod -Method POST -Uri "$RestRoot/media" -Headers $mediaHeaders `
        -ContentType $mime -InFile $file -ErrorAction Stop
    $mediaId = $media.id
    New-TestResult '6a. Media upload' $true ("id=$mediaId, source=$($media.source_url)")

    Invoke-Api -Method PATCH -Path ("posts/{0}" -f $postId) -Body @{ featured_media = $mediaId } | Out-Null
    $postAfter = Invoke-Api -Method GET -Path ("posts/{0}?context=edit&_fields=featured_media" -f $postId)
    $attachOk = ($postAfter.featured_media -eq $mediaId)
    New-TestResult '6b. Media attached to post' $attachOk "featured_media=$($postAfter.featured_media)"
} catch {
    New-TestResult '6. Media upload + attach' $false $_.Exception.Message
}

# 7. Cleanup ------------------------------------------------------------------
try {
    if ($postId) { Invoke-Api -Method DELETE -Path ("posts/{0}?force=true" -f $postId) | Out-Null }
    if ($mediaId) { Invoke-Api -Method DELETE -Path ("media/{0}?force=true" -f $mediaId) | Out-Null }
    New-TestResult '7. Cleanup' $true 'Test post + media removed.'
} catch {
    New-TestResult '7. Cleanup' $false "Manual cleanup needed: post=$postId media=$mediaId ($($_.Exception.Message))"
}

# Summary ---------------------------------------------------------------------
Write-Host "`n=== Summary ===" -ForegroundColor Cyan
$Results | Format-Table -AutoSize Check, Status, Detail
$failed = @($Results | Where-Object { $_.Status -ne 'PASS' }).Count
if ($failed -gt 0) {
    Write-Host "$failed check(s) FAILED. See runbook section 7." -ForegroundColor Red
    exit 1
} else {
    Write-Host "All checks PASSED. The automation's REST contract is verified." -ForegroundColor Green
    exit 0
}