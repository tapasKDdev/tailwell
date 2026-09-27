@echo off
title TailWell Preview
setlocal

set "URL=%~dp0preview\index.html"

if not exist "%URL%" (
    echo Preview file not found: %URL%
    pause
    exit /b 1
)

set "FIREFOX="
for %%c in (
    "%ProgramFiles%\Mozilla Firefox\firefox.exe"
    "%ProgramFiles(x86)%\Mozilla Firefox\firefox.exe"
    "%LocalAppData%\Mozilla Firefox\firefox.exe"
) do (
    if exist "%%~c" set "FIREFOX=%%~c"
)

if defined FIREFOX (
    start "" "%FIREFOX%" "%URL%"
) else (
    start "" firefox "%URL%"
)


echo Opening TailWell preview in Firefox...
endlocal