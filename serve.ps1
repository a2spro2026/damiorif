$PHP84 = 'C:\Users\User\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe'
$env:Path = "$PHP84;$env:Path"
Set-Location $PSScriptRoot
php artisan serve --host=127.0.0.1 --port=8000
