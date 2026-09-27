# Verifies that every local href/src in the static prototypes and preview resolves.
# Usage: powershell -File scripts\check-links.ps1
$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot

$htmlFiles = @()
$htmlFiles += Get-ChildItem -Path (Join-Path $root "pages") -Recurse -Filter *.html -File
$htmlFiles += Get-Item -LiteralPath (Join-Path $root "wordpress\preview\index.html")

$rx = '(?:href|src)="([^"]+)"'
$missing = @()
foreach ($f in $htmlFiles) {
    $html = [System.IO.File]::ReadAllText($f.FullName)
    foreach ($m in [regex]::Matches($html, $rx)) {
        $target = $m.Groups[1].Value
        if ($target -match '^(https?:|mailto:|tel:|#|data:|javascript:)') { continue }
        $clean = ($target -split '#')[0]
        if ($clean -eq "") { continue }
        $abs = [System.IO.Path]::GetFullPath((Join-Path $f.DirectoryName $clean))
        if (-not (Test-Path -LiteralPath $abs)) {
            $missing += "{0}  ->  {1}" -f $target, $f.Name
        }
    }
}
if ($missing.Count -eq 0) {
    Write-Host "Links OK ($($htmlFiles.Count) files checked)." -ForegroundColor Green
} else {
    Write-Host "Broken links:" -ForegroundColor Red
    $missing | ForEach-Object { Write-Host "  $_" }
    exit 1
}