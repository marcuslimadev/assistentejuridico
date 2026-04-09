[CmdletBinding()]
param(
    [string]$Message = "deploy: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')",
    [string]$Branch = "main",
    [string]$RemotePath = "/home/u815655858/domains/lexpraxis.lojadaesquina.store/public_html",
    [switch]$SkipCommit,
    [switch]$SkipRemote,
  [switch]$SkipNpm
)

$ErrorActionPreference = "Stop"

$RepoRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$PlinkPath = "C:\Program Files\PuTTY\plink.exe"
$LocalConfigPath = Join-Path $RepoRoot "deploy.local.psd1"

$DeployConfig = [ordered]@{
    SshHost       = "145.223.105.168"
    SshPort       = 65002
    SshUser       = "u815655858"
  SshPassword   = ""
    RepoUrl       = "https://github.com/marcuslimadev/assistentejuridico.git"
    AppUrl        = "https://lexpraxis.lojadaesquina.store"
    DbHost        = "193.203.166.228"
    DbPort        = "3306"
    DbDatabase    = "u815655858_lexpraxis"
    DbUsername    = "u815655858_lexpraxis"
  DbPassword    = ""
}

function Write-Step {
    param([string]$Text)
    Write-Host "`n==> $Text" -ForegroundColor Cyan
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
    if (-not (Test-Path $PlinkPath)) {
        throw "plink.exe not found at $PlinkPath"
    }

    $gitDir = Join-Path $RepoRoot ".git"
    if (-not (Test-Path $gitDir)) {
        throw "Repository metadata not found in $RepoRoot"
    }

    if (-not (Test-Path $LocalConfigPath)) {
      throw "Local deploy config not found at $LocalConfigPath"
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
        Invoke-Checked -FilePath "git" -Arguments @("commit", "-m", $Message)
    }
    else {
        Write-Step "No local changes to commit"
    }

    Write-Step "Pushing branch $Branch to origin"
    Invoke-Checked -FilePath "git" -Arguments @("push", "origin", $Branch)
}

function New-RemoteScript {
    $npmBlock = @"
if command -v npm >/dev/null 2>&1 && [ -f package.json ] && [ "$($SkipNpm.IsPresent.ToString().ToLowerInvariant())" != "true" ]; then
  npm install --no-audit --no-fund
  npm run build
fi
"@

    return @"
set -e

APP_DIR='$RemotePath'
REPO_URL='$($DeployConfig.RepoUrl)'
BRANCH='$Branch'
APP_URL='$($DeployConfig.AppUrl)'
DB_HOST='$($DeployConfig.DbHost)'
DB_PORT='$($DeployConfig.DbPort)'
DB_DATABASE='$($DeployConfig.DbDatabase)'
DB_USERNAME='$($DeployConfig.DbUsername)'
DB_PASSWORD='$($DeployConfig.DbPassword)'

ensure_env_value() {
  file="`$1"
  key="`$2"
  value="`$3"

  if [ ! -f "`$file" ]; then
    return
  fi

  escaped_value=`$(printf '%s' "`$value" | sed 's/[\\/&]/\\\\&/g')
  if grep -q "^`$key=" "`$file"; then
    sed -i "s/^`$key=.*/`$key=`$escaped_value/" "`$file"
  else
    printf '\n%s=%s\n' "`$key" "`$value" >> "`$file"
  fi
}

mkdir -p "`$APP_DIR"
cd "`$APP_DIR"

if [ ! -d .git ]; then
  parent_dir=`$(dirname "`$APP_DIR")
  stamp=`$(date +%Y%m%d_%H%M%S)
  backup_dir="`$parent_dir/predeploy_backup_`$stamp"
  preserved_well_known=""

  if [ -d .well-known ]; then
    preserved_well_known="`$parent_dir/.well-known_predeploy_`$stamp"
    mv .well-known "`$preserved_well_known"
  fi

  mkdir -p "`$backup_dir"
  find . -mindepth 1 -maxdepth 1 -exec mv {} "`$backup_dir"/ \; 2>/dev/null || true
  git clone --branch "`$BRANCH" "`$REPO_URL" .

  if [ -n "`$preserved_well_known" ] && [ -d "`$preserved_well_known" ]; then
    mv "`$preserved_well_known" .well-known
  fi
else
  git fetch origin "`$BRANCH"
  git checkout "`$BRANCH"
  git pull --ff-only origin "`$BRANCH"
fi

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

if [ -f artisan ]; then
  php artisan down || true
fi

if [ -f composer.json ]; then
  composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
fi

$npmBlock

if [ -f artisan ]; then
  if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    php artisan key:generate --force
  fi
  php artisan migrate --force
  php artisan optimize:clear
  php artisan optimize
  php artisan up
fi

echo 'Deploy concluido com sucesso.'
"@
}

function Invoke-RemoteDeploy {
    if ($SkipRemote) {
        Write-Step "Skipping remote deploy"
        return
    }

    Write-Step "Executing remote deploy over plink"
    $remoteScriptPath = Join-Path $env:TEMP ("assistentejuridico-deploy-" + [guid]::NewGuid().ToString() + ".sh")

    try {
        Set-Content -Path $remoteScriptPath -Value (New-RemoteScript) -Encoding ascii

        $plinkArgs = @(
            "-ssh",
            "-P", "$($DeployConfig.SshPort)",
            "-pw", $DeployConfig.SshPassword,
            "-batch",
            "-m", $remoteScriptPath,
            "$($DeployConfig.SshUser)@$($DeployConfig.SshHost)"
        )

        Invoke-Checked -FilePath $PlinkPath -Arguments $plinkArgs -WorkingDirectory $RepoRoot
    }
    finally {
        if (Test-Path $remoteScriptPath) {
            Remove-Item $remoteScriptPath -Force -ErrorAction SilentlyContinue
        }
    }
}

Assert-Prerequisites
Import-DeployConfig

Write-Step "Deploy started"
Write-Host "Repository : $RepoRoot"
Write-Host "Branch     : $Branch"
Write-Host "Remote path: $RemotePath"

Invoke-LocalGitFlow
Invoke-RemoteDeploy

Write-Step "Deploy finished"