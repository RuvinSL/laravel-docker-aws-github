Path: C:\Users\<user>\Documents\WindowsPowerShell\Microsoft.PowerShell_profile.ps1

# Add PowerShell Aliases

Step 1: Check Your Current PowerShell Profile Path
powershell# Check which PowerShell you're using and the profile path
$PSVersionTable.PSVersion
$PROFILE
Step 2: Verify Profile Existence and Content
powershell# Check if profile exists
Test-Path $PROFILE

# If it exists, show content
if (Test-Path $PROFILE) {
    Get-Content $PROFILE
} else {
    Write-Host "Profile does not exist at: $PROFILE" -ForegroundColor Red
}
Step 3: Check Execution Policy
powershell# Check current execution policy
Get-ExecutionPolicy -List
If it shows "Restricted", that's the problem. Fix it:
powershell# Set execution policy to allow local scripts
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
Step 4: Create Profile Manually
Let's create the profile step by step:
powershell# Show exactly where the profile should be
Write-Host "Profile path: $PROFILE" -ForegroundColor Yellow

# Create the directory if needed
$profileDir = Split-Path -Parent $PROFILE
if (!(Test-Path $profileDir)) {
    New-Item -ItemType Directory -Path $profileDir -Force
    Write-Host "Created directory: $profileDir" -ForegroundColor Green
}

# Create the profile file
New-Item -ItemType File -Path $PROFILE -Force
Write-Host "Created profile file: $PROFILE" -ForegroundColor Green
Step 5: Add Content to Profile
powershell# Add Docker aliases to the profile
$aliases = @"
# Docker Compose Aliases
Write-Host 'Loading Docker aliases...' -ForegroundColor Green

function dc { docker-compose `$args }
function dcup { docker-compose up `$args }
function dcdown { docker-compose down `$args }
function dcbuild { docker-compose build `$args }
function dcps { docker-compose ps `$args }
function dclogs { docker-compose logs `$args }
function dcexec { docker-compose exec `$args }
function dctest { docker-compose exec app php artisan test `$args }
function dcartisan { docker-compose exec app php artisan `$args }
function dcup { docker-compose up -d }
function dcdown { docker-compose down }

Write-Host 'Docker aliases loaded successfully!' -ForegroundColor Green
"@

# Write to profile
Set-Content -Path $PROFILE -Value $aliases
Write-Host "Aliases added to profile" -ForegroundColor Green
Step 6: Load the Profile
powershell# Manually load the profile
. $PROFILE
You should see the green messages if it works.
Step 7: Test the Aliases
powershell# Test if the function exists
Get-Command dc -ErrorAction SilentlyContinue

# Test the alias
dc --version
Alternative Solution: Use a Different Approach
If the profile method still doesn't work, try this alternative:
Option A: Create a PowerShell Module
powershell# Create a modules directory
$moduleDir = "$env:USERPROFILE\Documents\WindowsPowerShell\Modules\DockerAliases"
New-Item -ItemType Directory -Path $moduleDir -Force

# Create module file
$moduleContent = @'
function dc { docker-compose $args }
function dcup { docker-compose up $args }
function dcdown { docker-compose down $args }
function dcbuild { docker-compose build $args }
function dcps { docker-compose ps $args }
function dclogs { docker-compose logs $args }
function dcexec { docker-compose exec $args }
function dctest { docker-compose exec app php artisan test $args }
function dcartisan { docker-compose exec app php artisan $args }

Export-ModuleMember -Function *
'@

Set-Content -Path "$moduleDir\DockerAliases.psm1" -Value $moduleContent

# Import the module
Import-Module DockerAliases
Option B: Use Registry-Based Aliases (Batch Files)
If PowerShell functions don't work, create batch files:
powershell# Create a bin directory
$binDir = "$env:USERPROFILE\bin"
New-Item -ItemType Directory -Path $binDir -Force

# Add to PATH if not already there
$currentPath = [Environment]::GetEnvironmentVariable("PATH", "User")
if ($currentPath -notlike "*$binDir*") {
    [Environment]::SetEnvironmentVariable("PATH", "$currentPath;$binDir", "User")
    Write-Host "Added $binDir to PATH. Restart PowerShell." -ForegroundColor Yellow
}

# Create batch files
@'
@echo off
docker-compose %*
'@ | Set-Content "$binDir\dc.bat"

@'
@echo off
docker-compose up %*
'@ | Set-Content "$binDir\dcup.bat"

@'
@echo off
docker-compose down %*
'@ | Set-Content "$binDir\dcdown.bat"

Write-Host "Batch file aliases created in $binDir" -ForegroundColor Green
Write-Host "Restart PowerShell and try 'dc --version'" -ForegroundColor Yellow
Complete Diagnostic Script
Run this to diagnose the issue:
powershellWrite-Host "=== PowerShell Profile Diagnostic ===" -ForegroundColor Cyan

Write-Host "`nPowerShell Version:" -ForegroundColor Yellow
$PSVersionTable.PSVersion

Write-Host "`nProfile Path:" -ForegroundColor Yellow
$PROFILE

Write-Host "`nProfile Exists:" -ForegroundColor Yellow
Test-Path $PROFILE

Write-Host "`nExecution Policy:" -ForegroundColor Yellow
Get-ExecutionPolicy -List

Write-Host "`nProfile Directory Exists:" -ForegroundColor Yellow
$profileDir = Split-Path -Parent $PROFILE
Test-Path $profileDir

if (Test-Path $PROFILE) {
    Write-Host "`nProfile Content:" -ForegroundColor Yellow
    Get-Content $PROFILE
} else {
    Write-Host "`nProfile does not exist" -ForegroundColor Red
}

Write-Host "`nTesting if 'dc' function exists:" -ForegroundColor Yellow
Get-Command dc -ErrorAction SilentlyContinue

Write-Host "`nCurrent working directory:" -ForegroundColor Yellow
Get-Location
Run this diagnostic first, then we can fix the specific issue based on the output.
Most likely, the issue is either:

Execution policy is Restricted
Profile isn't being created in the right location
Profile isn't being loaded automatically
