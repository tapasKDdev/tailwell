<#
.SYNOPSIS
Life Stage layer verification against a live TailWell site: creates (then
optionally removes) one test draft per life stage per applicable silo.

.DESCRIPTION
Builds the full coverage matrix from the Life Stage layer spec:

  puppy / adult / senior  x  4 general silos  = 12 drafts
  senior                  x  senior-special-needs = 1 draft
  Total: 13 drafts.

Every draft is created via the REST API exactly the way the n8n workflow v6
does it (title/content/status(draft)/categories[numeric id]/life_stage[numeric
term id]) and each is read back to confirm BOTH taxonomies round-trip. Prints a
pass/fail table and the term ID maps for the workflow.

Run AFTER setup/verify-tailwell.ps1 has passed. `[tw_article_list category="X"
life_stage="Y"]` is a theme-render check and is verified manually: drop the
shortcode into a test page and confirm only matching posts appear.

.PARAMETER BaseUrl     Site root, e.g. https://tailwell.example.com (trailing slash optional).
.PARAMETER Username    Automation user (Editor role), e.g. tw-automation.
.PARAMETER AppPassword Application password generated on that user's profile.
.PARAMETER Cleanup     Delete the test drafts created during this run.

.EXAMPLE
.\verify-life-stage.ps1 -BaseUrl https://tailwell.example.com -Username tw-automation -AppPassword "abcd efgh ijkl mnop qrst uvwx" -Cleanup
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
    param([string]$Name, [bool]$Pass, [string]$Detail)
    if ($Pass) { $script:succeeded++ } else { $script:failed++ }
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

Write-Output ("Verifying the Life Stage layer at {0} as user {1}" -f $BaseUrl, $Username)

# --- Resolve silo categories -------------------------------------------------
$silos = [ordered]@{
    'wellness-health'      = $null
    'food-nutrition'       = $null
    'gear-tech'            = $null
    'training-behavior'    = $null
    'senior-special-needs' = $null
}
foreach ($slug in $silos.Keys) {
    $res = Invoke-RestMethod -Uri ("{0}/wp-json/wp/v2/categories?slug={1}&per_page=5" -f $BaseUrl, $slug) @restArgs
    $items = @($res)
    if ($items.Count -eq 0) { throw "Category '{0}' not found on this site — run setup/verify-tailwell.ps1 first." -f $slug }
    $silos[$slug] = $items[0].id
}
New-StepResult -Name 'Resolve 5 silo categories' -Pass $true -Detail (($silos.Keys | ForEach-Object { "$_=$($silos[$_])" }) -join ', ')

# --- Resolve life stage terms ------------------------------------------------
$lifeStages = [ordered]@{
    'puppy'  = $null
    'adult'  = $null
    'senior' = $null
}
foreach ($slug in $lifeStages.Keys) {
    $res = Invoke-RestMethod -Uri ("{0}/wp-json/wp/v2/life_stage?slug={1}&per_page=5" -f $BaseUrl, $slug) @restArgs
    $items = @($res)
    if ($items.Count -eq 0) { throw "Life stage term '{0}' not found — is the theme active and the term seeded (runbook Section 5.5)?" -f $slug }
    $lifeStages[$slug] = $items[0].id
}
New-StepResult -Name 'Resolve 3 life stage terms' -Pass $true -Detail (($lifeStages.Keys | ForEach-Object { "$_=$($lifeStages[$_])" }) -join ', ')

# --- Build the matrix (4 general silos x 3 life stages, senior silo x senior) --
$postsUrl = "{0}/wp-json/wp/v2/posts" -f $BaseUrl
$created = [System.Collections.Generic.List[int]]::new()
$matrix = @(
    @{ silo = 'wellness-health';      ls = 'puppy' },
    @{ silo = 'wellness-health';      ls = 'adult' },
    @{ silo = 'wellness-health';      ls = 'senior' },
    @{ silo = 'food-nutrition';       ls = 'puppy' },
    @{ silo = 'food-nutrition';       ls = 'adult' },
    @{ silo = 'food-nutrition';       ls = 'senior' },
    @{ silo = 'gear-tech';            ls = 'puppy' },
    @{ silo = 'gear-tech';            ls = 'adult' },
    @{ silo = 'gear-tech';            ls = 'senior' },
    @{ silo = 'training-behavior';    ls = 'puppy' },
    @{ silo = 'training-behavior';    ls = 'adult' },
    @{ silo = 'training-behavior';    ls = 'senior' },
    @{ silo = 'senior-special-needs'; ls = 'senior' }
)

foreach ($case in $matrix) {
    $catId = $silos[$case.silo]
    $lsId  = $lifeStages[$case.ls]
    try {
        $body = @{
            title      = ("TailWell Life Stage Test — {0} / {1}" -f $case.silo, $case.ls)
            content    = '<p>Automated draft created by setup/verify-life-stage.ps1. Safe to delete.</p>'
            status     = 'draft'
            categories = @($catId)
            life_stage = @($lsId)
        } | ConvertTo-Json
        $post = Invoke-RestMethod -Uri $postsUrl -Method Post @restArgs -Body $body
        $id = [int]$post.id
        $created.Add($id)

        $check = Invoke-RestMethod -Uri ("{0}/{1}" -f $postsUrl, $id) @restArgs
        $catOk = @($check.categories) -contains $catId
        $lsOk  = @($check.life_stage) -contains $lsId
        if ($catOk -and $lsOk) { $verdict = 'OK' }
        elseif ($catOk) { $verdict = 'life_stage MISMATCH' }
        else { $verdict = 'categories MISMATCH' }
        New-StepResult -Name ("Draft {0} ({1} / {2})" -f $id, $case.silo, $case.ls) -Pass ($catOk -and $lsOk) -Detail $verdict
    }
    catch {
        New-StepResult -Name ("Draft ({0} / {1})" -f $case.silo, $case.ls) -Pass $false -Detail $_.Exception.Message
    }
}

# --- Manual shortcode check (documented, not automatable here) ---------------
Write-Output ""
Write-Output 'Manual render check (do on the live site): drop [tw_article_list category="wellness-health" life_stage="puppy"] into a test page and confirm only puppy-themed wellness posts appear; try each life_stage value and an empty attribute list.'

# --- Cleanup -----------------------------------------------------------------
if ($Cleanup) {
    foreach ($id in $created) {
        try { Invoke-WebRequest -Uri ("{0}/{1}" -f $postsUrl, $id) -Method Delete -Headers $authHeader -TimeoutSec 60 | Out-Null } catch {}
    }
    Write-Output ""
    Write-Output ("Cleanup complete ({0} test drafts removed)." -f $created.Count)
}

Write-Output ""
Write-Output "Life stage term ID map (feed to the n8n workflow v6):"
$lifeStages.GetEnumerator() | ForEach-Object { Write-Output ("  {0} -> {1}" -f $_.Key, $_.Value) }
Write-Output ""
Write-Output ("Result: {0} passed, {1} failed" -f $succeeded, $failed)
if ($failed -gt 0) { exit 1 }
exit 0