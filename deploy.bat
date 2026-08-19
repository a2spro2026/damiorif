@echo off
cd /d "%~dp0"
if not defined VPS_SSH_PASSWORD (
  set "VPS_SSH_PASSWORD=A2sprVps2026!Secure"
)
echo Deploying damiorif.a2spr.com ...
python deploy.py
echo.
pause
