# PHP syntax lint for the production theme. Run from the repo root.
# Usage: powershell -File scripts\php-lint.ps1
$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
$theme = Join-Path $root "wordpress\tailwell-theme"

if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Host "php not installed - linting skipped. Install PHP CLI to run this." -ForegroundColor Yellow
    exit 0
}

$files = Get-ChildItem -Path $theme -Recurse -Filter *.php -File
$failed = 0
foreach ($f in $files) {
    $out = & php -l $f.FullName 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Host $out
        $failed++
    }
}
if ($failed -eq 0) {
    Write-Host "PHP lint OK ($($files.Count) files)." -ForegroundColor Green
} else {
    Write-Host "$failed file(s) failed lint." -ForegroundColor Red
    exit 1
}