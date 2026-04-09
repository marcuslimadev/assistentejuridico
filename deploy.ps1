[CmdletBinding()]
param(
  [string]$CommitMessage = "deploy: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')",
  [string]$Branch = "main",
  [switch]$SkipCommit,
  [switch]$SkipRemote,
  [switch]$SkipBuild,
  [string]$HealthUrl
)

$ErrorActionPreference = "Stop"

$RepoRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$PlinkPath = "C:\Program Files\PuTTY\plink.exe"
$PscpPath = "C:\Program Files\PuTTY\pscp.exe"
$LocalConfigPath = Join-Path $RepoRoot "deploy.local.psd1"
$LocalBuildPath = Join-Path $RepoRoot "public\build"
$script:BuildAvailable = $false

$DeployConfig = [ordered]@{
  SshHost          = "145.223.105.168"
  SshPort          = 65002
  SshUser          = "u815655858"
  SshPassword      = ""
  RepoUrl          = "https://github.com/marcuslimadev/assistentejuridico.git"
  DomainRoot       = "/home/u815655858/domains/lexpraxis.lojadaesquina.store"
  RemotePublicPath = "/home/u815655858/domains/lexpraxis.lojadaesquina.store/public_html"
  RemoteAppPath    = "/home/u815655858/domains/lexpraxis.lojadaesquina.store/laravel_app"
  PhpBin           = "/opt/alt/php83/usr/bin/php"
  ComposerBin      = "/usr/local/bin/composer"
  AppUrl           = "https://lexpraxis.lojadaesquina.store"
  DbHost           = "193.203.166.228"
  DbPort           = "3306"
  DbDatabase       = "u815655858_lexpraxis"
  DbUsername       = "u815655858_lexpraxis"
  DbPassword       = ""
}

function Write-Step {
  param([string]$Message)
  Write-Host "`n=== $Message ===" -ForegroundColor Cyan
}

function Write-Success {
  param([string]$Message)
  Write-Host "OK  $Message" -ForegroundColor Green
}

function Write-WarningLine {
  param([string]$Message)
  Write-Host "!!  $Message" -ForegroundColor Yellow
}

function Invoke-Checked {
  param(
    [string]$FilePath,
    [string[]]$Arguments,
    [string]$WorkingDirectory = $RepoRoot
  )

  Push-Location $WorkingDirectory
  try {
    & $FilePath @Arguments
    if ($LASTEXITCODE -ne 0) {
      throw "Command failed with exit code ${LASTEXITCODE}: $FilePath $($Arguments -join ' ')"
    }
  }
  finally {
    Pop-Location
  }
}

function Get-GitOutput {
  param([string[]]$Arguments)

  Push-Location $RepoRoot
  try {
    $output = & git @Arguments 2>&1
    if ($LASTEXITCODE -ne 0) {
      throw "Git command failed: git $($Arguments -join ' ')`n$output"
    }

    return ($output | Out-String).Trim()
  }
  finally {
    Pop-Location
  }
}

function Assert-Prerequisites {
  foreach ($requiredPath in @($PlinkPath, $PscpPath, $LocalConfigPath)) {
    if (-not (Test-Path $requiredPath)) {
      throw "Required file not found: $requiredPath"
    }
  }

  if (-not (Test-Path (Join-Path $RepoRoot ".git"))) {
    throw "Repository metadata not found in $RepoRoot"
  }
}

function Import-DeployConfig {
  $localConfig = Import-PowerShellDataFile -Path $LocalConfigPath

  foreach ($entry in $localConfig.GetEnumerator()) {
    $DeployConfig[$entry.Key] = $entry.Value
  }

  foreach ($requiredKey in @("SshPassword", "DbPassword")) {
    if ([string]::IsNullOrWhiteSpace($DeployConfig[$requiredKey])) {
      throw "Missing required deploy setting: $requiredKey"
    }
  }
}

function ConvertTo-BashSingleQuoted {
  param([string]$Value)

  $safeValue = if ($null -eq $Value) { "" } else { [string]$Value }
  $replacement = ([string][char]39) + ([string][char]34) + ([string][char]39) + ([string][char]34) + ([string][char]39)
  return ([string][char]39) + $safeValue.Replace([string][char]39, $replacement) + ([string][char]39)
}

function Invoke-LocalBuild {
  if ($SkipBuild) {
    Write-Step "Skipping local frontend build"
    return
  }

  Write-Step "Building frontend locally"
  try {
    if (-not (Test-Path (Join-Path $RepoRoot "node_modules\.bin\vite.cmd"))) {
      Write-Step "Installing frontend dependencies locally"
      Invoke-Checked -FilePath "npm" -Arguments @("install")
    }

    Invoke-Checked -FilePath "npm" -Arguments @("run", "build")

    if (-not (Test-Path (Join-Path $LocalBuildPath "manifest.json"))) {
      throw "Local build did not generate public/build/manifest.json"
    }

    $script:BuildAvailable = $true
    Write-Success "Local Vite build generated in public/build"
  }
  catch {
    $script:BuildAvailable = $false
    Write-WarningLine "Local frontend build failed. Continuing deploy without Vite assets."
    Write-WarningLine $_
  }
}

function Invoke-LocalGitFlow {
  if ($SkipCommit) {
    Write-Step "Skipping local commit and push"
    return
  }

  Write-Step "Checking local git status"
  $status = Get-GitOutput @("status", "--porcelain")

  if ($status) {
    Write-Step "Staging local changes"
    Invoke-Checked -FilePath "git" -Arguments @("add", "-A")

    Write-Step "Creating local commit"
    Push-Location $RepoRoot
    try {
      & git commit -m $CommitMessage
      if ($LASTEXITCODE -eq 0) {
        Write-Success "Commit created"
      }
      else {
        throw "git commit failed with exit code $LASTEXITCODE"
      }
    }
    finally {
      Pop-Location
    }
  }
  else {
    Write-WarningLine "No local changes detected. Continuing with push/deploy."
  }

  Write-Step "Pushing branch $Branch"
  Invoke-Checked -FilePath "git" -Arguments @("push", "origin", $Branch)
  Write-Success "Push completed"
}

function New-RemoteBootstrapScript {
  $appDir = ConvertTo-BashSingleQuoted $DeployConfig.RemoteAppPath
  $publicDir = ConvertTo-BashSingleQuoted $DeployConfig.RemotePublicPath
  $repoUrl = ConvertTo-BashSingleQuoted $DeployConfig.RepoUrl
  $branchValue = ConvertTo-BashSingleQuoted $Branch
  $phpBin = ConvertTo-BashSingleQuoted $DeployConfig.PhpBin
  $composerBin = ConvertTo-BashSingleQuoted $DeployConfig.ComposerBin
  $appUrl = ConvertTo-BashSingleQuoted $DeployConfig.AppUrl
  $dbHost = ConvertTo-BashSingleQuoted $DeployConfig.DbHost
  $dbPort = ConvertTo-BashSingleQuoted $DeployConfig.DbPort
  $dbDatabase = ConvertTo-BashSingleQuoted $DeployConfig.DbDatabase
  $dbUsername = ConvertTo-BashSingleQuoted $DeployConfig.DbUsername
  $dbPassword = ConvertTo-BashSingleQuoted $DeployConfig.DbPassword

  return @"
set -euo pipefail

APP_DIR=$appDir
PUBLIC_DIR=$publicDir
REPO_URL=$repoUrl
BRANCH=$branchValue
PHP_BIN=$phpBin
COMPOSER_BIN=$composerBin
APP_URL=$appUrl
DB_HOST=$dbHost
DB_PORT=$dbPort
DB_DATABASE=$dbDatabase
DB_USERNAME=$dbUsername
DB_PASSWORD=$dbPassword

ensure_env_value() {
  file="`$1"
  key="`$2"
  value="`$3"

  escaped_value=`$(printf '%s' "`$value" | sed 's/[\\/&]/\\\\&/g')
  if grep -q "^`$key=" "`$file"; then
  sed -i "s/^`$key=.*/`$key=`$escaped_value/" "`$file"
  else
  printf '\n%s=%s\n' "`$key" "`$value" >> "`$file"
  fi
}

mkdir -p "`$APP_DIR"
mkdir -p "`$PUBLIC_DIR"

if [ ! -d "`$APP_DIR/.git" ]; then
  rm -rf "`$APP_DIR"
  git clone --branch "`$BRANCH" "`$REPO_URL" "`$APP_DIR"
fi

cd "`$APP_DIR"
git fetch origin "`$BRANCH"
git checkout "`$BRANCH"
git reset --hard "origin/`$BRANCH"

if [ -f .env.example ] && [ ! -f .env ]; then
  cp .env.example .env
fi

ensure_env_value .env APP_ENV production
ensure_env_value .env APP_DEBUG false
ensure_env_value .env APP_URL "`$APP_URL"
ensure_env_value .env DB_CONNECTION mysql
ensure_env_value .env DB_HOST "`$DB_HOST"
ensure_env_value .env DB_PORT "`$DB_PORT"
ensure_env_value .env DB_DATABASE "`$DB_DATABASE"
ensure_env_value .env DB_USERNAME "`$DB_USERNAME"
ensure_env_value .env DB_PASSWORD "`$DB_PASSWORD"

"`$PHP_BIN" "`$COMPOSER_BIN" install --no-dev --prefer-dist --optimize-autoloader --no-interaction

if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
  "`$PHP_BIN" artisan key:generate --force
fi

"`$PHP_BIN" artisan migrate --force
"`$PHP_BIN" artisan optimize:clear
"`$PHP_BIN" artisan optimize

find "`$PUBLIC_DIR" -mindepth 1 -maxdepth 1 ! -name '.well-known' -exec rm -rf {} +
cp -R public/. "`$PUBLIC_DIR"/
rm -rf "`$PUBLIC_DIR/build"
mkdir -p "`$PUBLIC_DIR/build"

cat > "`$PUBLIC_DIR/index.php" <<'PHPFILE'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../laravel_app/storage/framework/maintenance.php')) {
  require $maintenance;
}

require __DIR__.'/../laravel_app/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../laravel_app/bootstrap/app.php';

$app->handleRequest(Request::capture());
PHPFILE

echo 'REMOTE_BOOTSTRAP_OK'
"@
}

function Invoke-RemoteBootstrap {
  Write-Step "Preparing remote Laravel app"
  $remoteScriptPath = Join-Path $env:TEMP ("assistentejuridico-remote-bootstrap-" + [guid]::NewGuid().ToString() + ".sh")

  try {
    Set-Content -Path $remoteScriptPath -Value (New-RemoteBootstrapScript) -Encoding ascii

    $plinkArgs = @(
      "-ssh",
      "-P", "$($DeployConfig.SshPort)",
      "-pw", $DeployConfig.SshPassword,
      "-batch",
      "-m", $remoteScriptPath,
      "$($DeployConfig.SshUser)@$($DeployConfig.SshHost)"
    )

    Invoke-Checked -FilePath $PlinkPath -Arguments $plinkArgs
    Write-Success "Remote bootstrap completed"
  }
  finally {
    if (Test-Path $remoteScriptPath) {
      Remove-Item $remoteScriptPath -Force -ErrorAction SilentlyContinue
    }
  }
}

function Invoke-BuildUpload {
  if (-not $script:BuildAvailable) {
    Write-Step "Skipping asset upload"
    return
  }

  if (-not (Test-Path $LocalBuildPath)) {
    throw "Local build directory not found: $LocalBuildPath"
  }

  Write-Step "Uploading built assets with PSCP"
  Invoke-Checked -FilePath $PlinkPath -Arguments @(
    "-ssh",
    "-P", "$($DeployConfig.SshPort)",
    "-pw", $DeployConfig.SshPassword,
    "-batch",
    "$($DeployConfig.SshUser)@$($DeployConfig.SshHost)",
    "rm -rf '$($DeployConfig.RemotePublicPath)/build' && mkdir -p '$($DeployConfig.RemotePublicPath)/build'"
  )

  Invoke-Checked -FilePath $PscpPath -Arguments @(
    "-P", "$($DeployConfig.SshPort)",
    "-pw", $DeployConfig.SshPassword,
    "-batch",
    "-r",
    (Join-Path $LocalBuildPath "*"),
    "$($DeployConfig.SshUser)@$($DeployConfig.SshHost):$($DeployConfig.RemotePublicPath)/build/"
  )

  Write-Success "Frontend build uploaded"
}

function Invoke-HealthCheck {
  $finalHealthUrl = if ([string]::IsNullOrWhiteSpace($HealthUrl)) {
    "$($DeployConfig.AppUrl.TrimEnd('/'))/up"
  }
  else {
    $HealthUrl.Trim()
  }

  Write-Step "Checking application health"
  $response = Invoke-WebRequest -Uri $finalHealthUrl -TimeoutSec 20 -UseBasicParsing
  if ($response.StatusCode -ne 200) {
    throw "Health check returned HTTP $($response.StatusCode)"
  }

  Write-Success "Health check OK at $finalHealthUrl"
}

function Invoke-RemoteDeploy {
  if ($SkipRemote) {
    Write-Step "Skipping remote deploy"
    return
  }

  Invoke-RemoteBootstrap
  Invoke-BuildUpload
  Invoke-HealthCheck
}

try {
  Assert-Prerequisites
  Import-DeployConfig

  Write-Host ""
  Write-Host "===============================================================" -ForegroundColor Cyan
  Write-Host "            DEPLOY AUTOMATICO - LEXPraxis IA                  " -ForegroundColor Cyan
  Write-Host "===============================================================" -ForegroundColor Cyan

  Write-Host "Repository : $RepoRoot"
  Write-Host "Branch     : $Branch"
  Write-Host "App dir    : $($DeployConfig.RemoteAppPath)"
  Write-Host "Public dir : $($DeployConfig.RemotePublicPath)"

  Invoke-LocalBuild
  Invoke-LocalGitFlow
  Invoke-RemoteDeploy

  Write-Host ""
  Write-Host "===============================================================" -ForegroundColor Green
  Write-Host "                 DEPLOY CONCLUIDO COM SUCESSO                 " -ForegroundColor Green
  Write-Host "===============================================================" -ForegroundColor Green
  Write-Host "URL: $($DeployConfig.AppUrl)" -ForegroundColor Cyan
}
catch {
  Write-Host ""
  Write-Host "===============================================================" -ForegroundColor Red
  Write-Host "                       ERRO NO DEPLOY                         " -ForegroundColor Red
  Write-Host "===============================================================" -ForegroundColor Red
  Write-Host $_ -ForegroundColor Red
  Write-Host $_.ScriptStackTrace -ForegroundColor DarkGray
  exit 1
}