param(
    [switch]$IncludeExamples
)

$ErrorActionPreference = 'Stop'
$root    = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$researchDir = Join-Path $root 'research'
$productCsv  = Join-Path (Join-Path $root 'product-data') 'product-sheet-template.csv'

$typeEnum = @('peer-reviewed study','regulatory document','veterinary-university resource','national veterinary organization','manufacturer labeling','news article')
$tierEnum = @('s1','s2','s3')

$allFiles = Get-ChildItem -Path $researchDir -Filter 'evidence-*.json' -Recurse -File
$files = $allFiles | Where-Object { $IncludeExamples -or $_.FullName -notmatch '\\examples\\' }

$errors = @()
$ids = @{}
foreach ($f in $allFiles) {
    $raw = Get-Content -LiteralPath $f.FullName -Raw | ConvertFrom-Json
    $sid = $raw.source_id
    if ($sid -match '^TWP-EVID-\d{4}$') {
        if ($ids.ContainsKey($sid)) { $errors += "$sid duplicated across $($ids[$sid]) and $($f.Name)" } else { $ids[$sid] = $f.Name }
    }
}

foreach ($f in $files) {
    $data = $null
    try { $data = Get-Content -LiteralPath $f.FullName -Raw | ConvertFrom-Json } catch { $errors += "FILE $($f.Name): not valid JSON - $($_.Exception.Message)"; continue }

    $src = $data.source_id
    if (-not ($src -match '^TWP-EVID-\d{4}$')) { $errors += "$src ($($f.Name)): source_id must match ^TWP-EVID-\d{4}$"; continue }

    foreach ($req in @('source_id','publisher','title','url','type','tier','retrieved_at','claims')) {
        if ($null -eq $data.$req -or ($data.$req -is [System.Array] -and $data.$req.Count -eq 0)) { $errors += "${src}: missing required field '$req'" }
    }
    if ($data.url -notmatch '^https://') { $errors += "${src}: url must be https" }
    if ($typeEnum -notcontains $data.type) { $errors += "${src}: type '$($data.type)' not in enum" }
    if ($tierEnum -notcontains $data.tier) { $errors += "${src}: tier '$($data.tier)' not in enum" }
    foreach ($field in @('retrieved_at','publication_date')) {
        if ($data.$field -and $data.$field -notmatch '^\d{4}-\d{2}-\d{2}$') { $errors += "${src}: $field must be YYYY-MM-DD" }
    }
    $claims = @($data.claims)
    foreach ($c in $claims) {
        if (-not $c.predicate -or ($c.predicate -is [string] -and [string]::IsNullOrWhiteSpace($c.predicate))) { $errors += "${src}: claim missing predicate" }
        if ($null -eq $c.supported -or $c.supported -notin @($true,$false)) { $errors += "${src}: claim '$($c.predicate)': supported must be boolean" }
    }
}

if (Test-Path -LiteralPath $productCsv) {
    $csv = Import-Csv -LiteralPath $productCsv
    foreach ($row in $csv) {
        if ([string]::IsNullOrWhiteSpace($row.source_id)) { continue }
        if (-not $ids.ContainsKey($row.source_id)) { $errors += "PRODUCT-DB: source_id '$($row.source_id)' (row $($row.id)) referenced but no evidence file exists" }
    }
}

if ($errors.Count -gt 0) {
    $errors | ForEach-Object { Write-Output "ERROR: $_" }
    Write-Output "Evidence validator FAILED ($($errors.Count) issue(s)). Files checked: $($files.Count)"
    exit 1
}
$checked = $files.Count
$refIds   = if (Test-Path -LiteralPath $productCsv) { (@(Import-Csv -LiteralPath $productCsv | Where-Object { $_.source_id })).Count } else { 0 }
Write-Output "Evidence validator OK: $checked evidence file(s), $($ids.Count) unique source_id(s), $refIds product references resolve."