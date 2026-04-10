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
$LocalEnvPath = Join-Path $RepoRoot ".env"
$LocalLegacyBackendEnvPath = Join-Path $RepoRoot "backend\.env"
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
  OpenAIApiKey               = ""
  DataJudKey                 = ""
  StripePublishableKey       = ""
  StripeSecretKey            = ""
  StripeWebhookSecret        = ""
  StripeSuccessUrl           = ""
  StripeCancelUrl            = ""
  ConsultaUnitPriceCents     = "5"
  GoogleCalendarClientId     = ""
  GoogleCalendarClientSecret = ""
  GoogleCalendarId           = "primary"
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

function Get-DotEnvValue {
  param(
    [string]$FilePath,
    [string]$Key
  )

  if (-not (Test-Path $FilePath)) {
    return $null
  }

  $line = Get-Content $FilePath | Where-Object { $_ -match "^$([regex]::Escape($Key))=" } | Select-Object -First 1
  if (-not $line) {
    return $null
  }

  return ($line -split '=', 2)[1].Trim().Trim('"')
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

  $DeployConfig.OpenAIApiKey = Get-DotEnvValue -FilePath $LocalEnvPath -Key "OPENAI_API_KEY"
  $DeployConfig.DataJudKey = Get-DotEnvValue -FilePath $LocalEnvPath -Key "DATAJUD_KEY"
  if ([string]::IsNullOrWhiteSpace($DeployConfig.DataJudKey)) {
    $DeployConfig.DataJudKey = Get-DotEnvValue -FilePath $LocalLegacyBackendEnvPath -Key "DATAJUD_KEY"
  }
  $DeployConfig.StripePublishableKey = Get-DotEnvValue -FilePath $LocalEnvPath -Key "STRIPE_PUBLISHABLE_KEY"
  $DeployConfig.StripeSecretKey = Get-DotEnvValue -FilePath $LocalEnvPath -Key "STRIPE_SECRET_KEY"
  $DeployConfig.StripeWebhookSecret = Get-DotEnvValue -FilePath $LocalEnvPath -Key "STRIPE_WEBHOOK_SECRET"
  $DeployConfig.StripeSuccessUrl = Get-DotEnvValue -FilePath $LocalEnvPath -Key "STRIPE_SUCCESS_URL"
  $DeployConfig.StripeCancelUrl = Get-DotEnvValue -FilePath $LocalEnvPath -Key "STRIPE_CANCEL_URL"

  $consultaUnitPriceCents = Get-DotEnvValue -FilePath $LocalEnvPath -Key "CONSULTA_UNIT_PRICE_CENTS"
  if (-not [string]::IsNullOrWhiteSpace($consultaUnitPriceCents)) {
    $DeployConfig.ConsultaUnitPriceCents = $consultaUnitPriceCents
  }
  $DeployConfig.GoogleCalendarClientId = Get-DotEnvValue -FilePath $LocalEnvPath -Key "GOOGLE_CALENDAR_CLIENT_ID"
  $DeployConfig.GoogleCalendarClientSecret = Get-DotEnvValue -FilePath $LocalEnvPath -Key "GOOGLE_CALENDAR_CLIENT_SECRET"

  $googleCalendarId = Get-DotEnvValue -FilePath $LocalEnvPath -Key "GOOGLE_CALENDAR_ID"
  if (-not [string]::IsNullOrWhiteSpace($googleCalendarId)) {
    $DeployConfig.GoogleCalendarId = $googleCalendarId
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
  $openAIApiKey = ConvertTo-BashSingleQuoted $DeployConfig.OpenAIApiKey
  $dataJudKey = ConvertTo-BashSingleQuoted $DeployConfig.DataJudKey
  $stripePublishableKey = ConvertTo-BashSingleQuoted $DeployConfig.StripePublishableKey
  $stripeSecretKey = ConvertTo-BashSingleQuoted $DeployConfig.StripeSecretKey
  $stripeWebhookSecret = ConvertTo-BashSingleQuoted $DeployConfig.StripeWebhookSecret
  $stripeSuccessUrl = ConvertTo-BashSingleQuoted $DeployConfig.StripeSuccessUrl
  $stripeCancelUrl = ConvertTo-BashSingleQuoted $DeployConfig.StripeCancelUrl
  $consultaUnitPriceCents = ConvertTo-BashSingleQuoted $DeployConfig.ConsultaUnitPriceCents
  $googleCalendarClientId = ConvertTo-BashSingleQuoted $DeployConfig.GoogleCalendarClientId
  $googleCalendarClientSecret = ConvertTo-BashSingleQuoted $DeployConfig.GoogleCalendarClientSecret
  $googleCalendarId = ConvertTo-BashSingleQuoted $DeployConfig.GoogleCalendarId
  $googleCalendarRedirect = ConvertTo-BashSingleQuoted ($DeployConfig.AppUrl.TrimEnd('/') + '/google-calendar/callback')

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
OPENAI_API_KEY=$openAIApiKey
DATAJUD_KEY=$dataJudKey
STRIPE_PUBLISHABLE_KEY=$stripePublishableKey
STRIPE_SECRET_KEY=$stripeSecretKey
STRIPE_WEBHOOK_SECRET=$stripeWebhookSecret
STRIPE_SUCCESS_URL=$stripeSuccessUrl
STRIPE_CANCEL_URL=$stripeCancelUrl
CONSULTA_UNIT_PRICE_CENTS=$consultaUnitPriceCents
GOOGLE_CALENDAR_CLIENT_ID=$googleCalendarClientId
GOOGLE_CALENDAR_CLIENT_SECRET=$googleCalendarClientSecret
GOOGLE_CALENDAR_REDIRECT_URI=$googleCalendarRedirect
GOOGLE_CALENDAR_ID=$googleCalendarId

ensure_env_value() {
  file="`$1"
  key="`$2"
  value="`$3"

  if grep -q "^`$key=" "`$file"; then
    temp_file=`$(mktemp)
    while IFS= read -r line; do
      if [[ "`$line" == "`$key="* ]]; then
        printf '%s=%s\n' "`$key" "`$value" >> "`$temp_file"
      else
        printf '%s\n' "`$line" >> "`$temp_file"
      fi
    done < "`$file"
    mv "`$temp_file" "`$file"
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
ensure_env_value .env OPENAI_API_KEY "`$OPENAI_API_KEY"
ensure_env_value .env DATAJUD_KEY "`$DATAJUD_KEY"
ensure_env_value .env STRIPE_PUBLISHABLE_KEY "`$STRIPE_PUBLISHABLE_KEY"
ensure_env_value .env STRIPE_SECRET_KEY "`$STRIPE_SECRET_KEY"
ensure_env_value .env STRIPE_WEBHOOK_SECRET "`$STRIPE_WEBHOOK_SECRET"
ensure_env_value .env STRIPE_SUCCESS_URL "`$STRIPE_SUCCESS_URL"
ensure_env_value .env STRIPE_CANCEL_URL "`$STRIPE_CANCEL_URL"
ensure_env_value .env CONSULTA_UNIT_PRICE_CENTS "`$CONSULTA_UNIT_PRICE_CENTS"
ensure_env_value .env GOOGLE_CALENDAR_CLIENT_ID "`$GOOGLE_CALENDAR_CLIENT_ID"
ensure_env_value .env GOOGLE_CALENDAR_CLIENT_SECRET "`$GOOGLE_CALENDAR_CLIENT_SECRET"
ensure_env_value .env GOOGLE_CALENDAR_REDIRECT_URI "`$GOOGLE_CALENDAR_REDIRECT_URI"
ensure_env_value .env GOOGLE_CALENDAR_ID "`$GOOGLE_CALENDAR_ID"

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

if (file_exists(`$maintenance = __DIR__.'/../laravel_app/storage/framework/maintenance.php')) {
  require `$maintenance;
}

require __DIR__.'/../laravel_app/vendor/autoload.php';

/** @var Application `$app */
`$app = require_once __DIR__.'/../laravel_app/bootstrap/app.php';

`$app->handleRequest(Request::capture());
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