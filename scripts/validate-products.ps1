# Validates a TailWell product sheet against product-database.schema.md.
# Usage: powershell -File scripts\validate-products.ps1 [-Path product-data\product-sheet-template.csv]
param(
    [string]$Path = (Join-Path (Split-Path -Parent $PSScriptRoot) "product-data\product-sheet-template.csv")
)

$ErrorActionPreference = "Stop"
$problems = @()

if (-not (Test-Path -LiteralPath $Path)) {
    Write-Host "File not found: $Path" -ForegroundColor Red
    exit 1
}

# BOM is forbidden on the data CSV.
$bytes = [System.IO.File]::ReadAllBytes($Path)
if ($bytes.Length -ge 3 -and $bytes[0] -eq 0xEF -and $bytes[1] -eq 0xBB -and $bytes[2] -eq 0xBF) {
    $problems += "UTF-8 BOM present - forbidden on data CSVs (breaks header parsing)."
}

# Minimal quoted-CSV parse (handles commas inside quotes).
$text = [System.IO.File]::ReadAllText($Path)
$lines = ($text -split "`r?`n") | Where-Object { $_.Trim() -ne "" }
if ($lines.Count -lt 2) { $problems += "No data rows." }

function Split-CsvLine([string]$line) {
    $cells = @(); $cur = ""; $inQ = $false
    for ($i = 0; $i -lt $line.Length; $i++) {
        $c = $line[$i]
        if ($inQ) {
            if ($c -eq '"') {
                if ($i + 1 -lt $line.Length -and $line[$i+1] -eq '"') { $cur += '"'; $i++ }
                else { $inQ = $false }
            } else { $cur += $c }
        }
        elseif ($c -eq '"') { $inQ = $true }
        elseif ($c -eq ',') { $cells += $cur; $cur = "" }
        else { $cur += $c }
    }
    $cells += $cur
    return $cells
}

$headerRow = @(Split-CsvLine $lines[0]) | ForEach-Object { $_.Trim() }
$requiredColumns = @('id','name','brand','category','silo','why','price','price_updated','url','affiliate_partner','vet_verified','status','enabled')
foreach ($col in $requiredColumns) {
    if ($headerRow -notcontains $col) { $problems += "Missing required column: $col" }
}
$colIndex = @{}
for ($i = 0; $i -lt $headerRow.Count; $i++) { $colIndex[$headerRow[$i]] = $i }

$useCases = @('joint-supplement','fresh-food','cbd-wellness','insurance','gear-tracker','gear-feeder','dental','odor-control')
$silos    = @('wellness-health','food-nutrition','gear-tech','training-behavior','senior-special-needs')
$statuses = @('pending','approved','rejected','archived')
$ids = @{}

for ($r = 1; $r -lt $lines.Count; $r++) {
    $cells = @(Split-CsvLine $lines[$r]) | ForEach-Object { $_.Trim() }
    if ($cells.Count -eq 1 -and $cells[0] -eq "") { continue }
    $get = { param($name) if ($colIndex.ContainsKey($name) -and $colIndex[$name] -lt $cells.Count) { $cells[$colIndex[$name]] } else { "" } }
    $id = & $get 'id'; $cat = & $get 'category'; $silo = & $get 'silo'; $st = & $get 'status'
    $url = & $get 'url'; $price = & $get 'price'; $pu = & $get 'price_updated'
    $en = & $get 'enabled'; $vv = & $get 'vet_verified'

    if ($id -eq "") { $problems += "Row $($r+1): empty id"; continue }
    if ($ids.ContainsKey($id)) { $problems += "Row $($r+1): duplicate id '$id'" }
    $ids[$id] = $true

    if ($useCases -notcontains $cat) { $problems += "Row $($r+1) ($id): unknown category '$cat'" }
    if ($silos -notcontains $silo)   { $problems += "Row $($r+1) ($id): unknown silo '$silo'" }
    if ($statuses -notcontains $st)  { $problems += "Row $($r+1) ($id): unknown status '$st'" }
    if ($en -notin @('true','false')) { $problems += "Row $($r+1) ($id): enabled must be true/false, got '$en'" }
    if ($vv -notin @('true','false')) { $problems += "Row $($r+1) ($id): vet_verified must be true/false, got '$vv'" }

    if ($st -eq 'approved') {
        if ($en -ne 'true') { $problems += "Row $($r+1) ($id): approved but enabled != true" }
        if ($price -eq "")  { $problems += "Row $($r+1) ($id): approved but empty price" }
        if ($pu -match '^\d{4}-\d{2}-\d{2}$') {
            $parsed = $null
            if (-not [DateTime]::TryParseExact($pu, 'yyyy-MM-dd', [Globalization.CultureInfo]::InvariantCulture, [Globalization.DateTimeStyles]::None, [ref]$parsed)) {
                $problems += "Row $($r+1) ($id): invalid price_updated date '$pu'"
            }
        } else {
            $problems += "Row $($r+1) ($id): price_updated must be YYYY-MM-DD, got '$pu'"
        }
        if ($url -notmatch '^https://') { $problems += "Row $($r+1) ($id): approved URL must be https, got '$url'" }
        if ($url -match 'example\.com') { $problems += "Row $($r+1) ($id): approved row still uses example.com - replace with the real affiliate URL" }
    }
}

# Expectations for the shipped template itself: every row pending + disabled + example.com placeholder.
if ($Path -match 'product-sheet-template\.csv$') {
    if ($ids.Keys.Count -gt 0) {
        $approvedCount = 0
        for ($r = 1; $r -lt $lines.Count; $r++) {
            $cells = @(Split-CsvLine $lines[$r])
            $st = if ($colIndex.ContainsKey('status') -and $colIndex['status'] -lt $cells.Count) { $cells[$colIndex['status']].Trim() } else { "" }
            if ($st -eq 'approved') { $approvedCount++ }
        }
        if ($approvedCount -gt 0) { $problems += "Template file must not ship approved rows (found $approvedCount)" }
    }
}

if ($problems.Count -eq 0) {
    Write-Host "Product sheet OK: $Path ($($ids.Keys.Count) rows)" -ForegroundColor Green
    exit 0
}
Write-Host "Product sheet validation failed ($($problems.Count) issue(s)):" -ForegroundColor Red
$problems | ForEach-Object { Write-Host "  - $_" }
exit 1