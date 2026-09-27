@echo off
title TailWell Preview
setlocal

set "URL=%~dp0pages\index.html"

if not exist "%URL%" (
    echo Preview file not found: %URL%
    pause
    exit /b 1
)

set "CHROME="
for %%c in (
    "%ProgramFiles%\Google\Chrome\Application\chrome.exe"
    "%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe"
    "%LocalAppData%\Google\Chrome\Application\chrome.exe"
) do (
    if exist "%%~c" set "CHROME=%%~c"
)

if defined CHROME (
    start "" "%CHROME%" "%URL%"
) else (
    start "" chrome "%URL%"
)

echo Opening TailWell site in Chrome...
endlocal
