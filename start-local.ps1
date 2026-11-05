param(
    [ValidateRange(1024, 65535)]
    [int]$Port = 8000
)

$ErrorActionPreference = 'Stop'
$appDir = Join-Path $PSScriptRoot 'back'
$uploadDir = Join-Path $appDir 'storage\app\private\php-uploads'
$logDir = Join-Path $appDir 'storage\logs'
$router = Join-Path $appDir 'vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php'
$phpExe = (Get-Command php -ErrorAction Stop).Source

New-Item -ItemType Directory -Force -Path $uploadDir, $logDir | Out-Null
$probe = Join-Path $uploadDir ([Guid]::NewGuid().ToString() + '.tmp')
[IO.File]::WriteAllText($probe, 'upload directory check')
Remove-Item -LiteralPath $probe

$phpArguments = @(
    '-d', "upload_tmp_dir=$uploadDir",
    '-d', "sys_temp_dir=$uploadDir",
    '-d', 'upload_max_filesize=4M',
    '-d', 'post_max_size=8M',
    '-S', "127.0.0.1:$Port",
    $router
)
$server = Start-Process -FilePath $phpExe -ArgumentList $phpArguments `
    -WorkingDirectory (Join-Path $appDir 'public') -WindowStyle Hidden `
    -RedirectStandardOutput (Join-Path $logDir 'server-local.log') `
    -RedirectStandardError (Join-Path $logDir 'server-local-error.log') -PassThru

Write-Output "Backoffice : http://127.0.0.1:$Port (PID $($server.Id))"
