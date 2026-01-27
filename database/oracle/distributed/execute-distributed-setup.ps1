# ============================================================================
# PowerShell Helper Script for Distributed Database Setup
# Description: Automates the execution of SQL scripts in the correct order
# Usage: .\execute-distributed-setup.ps1
# ============================================================================

Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║   UrbanHoops - Distributed Database Setup                     ║" -ForegroundColor Cyan
Write-Host "║   Automated Deployment Script                                  ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Configuration
$PROD_HOST = "192.168.1.115"
$PROD_PORT = "1521"
$PROD_SERVICE = "prod"
$PROD_USER = "u_prod"
$PROD_PASS = "secreto123"

$COMEE_HOST = "192.168.1.125"
$COMEE_PORT = "1521"
$COMEE_SERVICE = "comee"
$COMEE_USER = "u_comee"
$COMEE_PASS = "secreto123"

$SCRIPT_DIR = "$PSScriptRoot\database\oracle\distributed"

# Helper function to execute SQL script
function Execute-SQLScript {
    param(
        [string]$Host,
        [string]$Port,
        [string]$Service,
        [string]$User,
        [string]$Pass,
        [string]$ScriptPath,
        [string]$Description
    )
    
    Write-Host ""
    Write-Host "════════════════════════════════════════════════════════════════" -ForegroundColor Yellow
    Write-Host "Executing: $Description" -ForegroundColor Yellow
    Write-Host "════════════════════════════════════════════════════════════════" -ForegroundColor Yellow
    Write-Host "Connection: $User@$Host`:$Port/$Service" -ForegroundColor Gray
    Write-Host "Script: $ScriptPath" -ForegroundColor Gray
    Write-Host ""
    
    $connectionString = "${User}/${Pass}@${Host}:${Port}/${Service}"
    
    # Execute SQL script with sqlplus
    $command = "echo exit | sqlplus -S $connectionString @`"$ScriptPath`""
    
    Write-Host "Press Enter to execute or Ctrl+C to cancel..." -ForegroundColor Cyan
    Read-Host
    
    Invoke-Expression $command
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Script executed successfully" -ForegroundColor Green
        return $true
    } else {
        Write-Host "✗ Script execution failed" -ForegroundColor Red
        return $false
    }
}

# Main execution flow
Write-Host "This script will execute all distributed database setup scripts in order." -ForegroundColor White
Write-Host ""
Write-Host "Prerequisites:" -ForegroundColor Yellow
Write-Host "  [√] Both PDBs are running and accessible" -ForegroundColor White
Write-Host "  [√] Database links exist (link_comee and link_prod)" -ForegroundColor White
Write-Host "  [√] SQL*Plus is installed and in PATH" -ForegroundColor White
Write-Host "  [√] Laravel application is stopped" -ForegroundColor White
Write-Host ""

$continue = Read-Host "Continue with setup? (yes/no)"
if ($continue -ne "yes") {
    Write-Host "Setup cancelled." -ForegroundColor Red
    exit
}

# ============================================================================
# PHASE 1: Setup COMEE
# ============================================================================
Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Magenta
Write-Host "║   PHASE 1: Setting up COMEE PDB                                ║" -ForegroundColor Magenta
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Magenta

$success = Execute-SQLScript `
    -Host $COMEE_HOST `
    -Port $COMEE_PORT `
    -Service $COMEE_SERVICE `
    -User $COMEE_USER `
    -Pass $COMEE_PASS `
    -ScriptPath "$SCRIPT_DIR\01_create_tables_comee.sql" `
    -Description "Creating main tables in COMEE"

if (-not $success) {
    Write-Host "Failed to create tables in COMEE. Aborting." -ForegroundColor Red
    exit 1
}

$success = Execute-SQLScript `
    -Host $COMEE_HOST `
    -Port $COMEE_PORT `
    -Service $COMEE_SERVICE `
    -User $COMEE_USER `
    -Pass $COMEE_PASS `
    -ScriptPath "$SCRIPT_DIR\02_create_replica_tables_comee.sql" `
    -Description "Creating replica tables in COMEE"

if (-not $success) {
    Write-Host "Failed to create replica tables. Aborting." -ForegroundColor Red
    exit 1
}

$success = Execute-SQLScript `
    -Host $COMEE_HOST `
    -Port $COMEE_PORT `
    -Service $COMEE_SERVICE `
    -User $COMEE_USER `
    -Pass $COMEE_PASS `
    -ScriptPath "$SCRIPT_DIR\03_create_synonyms_comee.sql" `
    -Description "Creating synonyms in COMEE"

if (-not $success) {
    Write-Host "Failed to create synonyms in COMEE. Aborting." -ForegroundColor Red
    exit 1
}

# ============================================================================
# PHASE 2: Setup PROD
# ============================================================================
Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Magenta
Write-Host "║   PHASE 2: Setting up PROD PDB                                 ║" -ForegroundColor Magenta
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Magenta

$success = Execute-SQLScript `
    -Host $PROD_HOST `
    -Port $PROD_PORT `
    -Service $PROD_SERVICE `
    -User $PROD_USER `
    -Pass $PROD_PASS `
    -ScriptPath "$SCRIPT_DIR\04_create_synonyms_prod.sql" `
    -Description "Creating synonyms in PROD"

if (-not $success) {
    Write-Host "Failed to create synonyms in PROD. Aborting." -ForegroundColor Red
    exit 1
}

$success = Execute-SQLScript `
    -Host $PROD_HOST `
    -Port $PROD_PORT `
    -Service $PROD_SERVICE `
    -User $PROD_USER `
    -Pass $PROD_PASS `
    -ScriptPath "$SCRIPT_DIR\05_triggers_replication_prod.sql" `
    -Description "Creating replication triggers in PROD"

if (-not $success) {
    Write-Host "Failed to create triggers in PROD. Aborting." -ForegroundColor Red
    exit 1
}

# ============================================================================
# PHASE 3: Complete COMEE Setup
# ============================================================================
Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Magenta
Write-Host "║   PHASE 3: Completing COMEE Setup                              ║" -ForegroundColor Magenta
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Magenta

$success = Execute-SQLScript `
    -Host $COMEE_HOST `
    -Port $COMEE_PORT `
    -Service $COMEE_SERVICE `
    -User $COMEE_USER `
    -Pass $COMEE_PASS `
    -ScriptPath "$SCRIPT_DIR\06_triggers_replication_comee.sql" `
    -Description "Creating replication triggers in COMEE"

if (-not $success) {
    Write-Host "Failed to create triggers in COMEE. Aborting." -ForegroundColor Red
    exit 1
}

# ============================================================================
# PHASE 4: Data Migration
# ============================================================================
Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Magenta
Write-Host "║   PHASE 4: Data Migration                                      ║" -ForegroundColor Magenta
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Magenta
Write-Host ""
Write-Host "IMPORTANT: This phase requires disabling triggers temporarily." -ForegroundColor Yellow
Write-Host "The migration script will guide you through the process." -ForegroundColor Yellow
Write-Host ""

$success = Execute-SQLScript `
    -Host $PROD_HOST `
    -Port $PROD_PORT `
    -Service $PROD_SERVICE `
    -User $PROD_USER `
    -Pass $PROD_PASS `
    -ScriptPath "$SCRIPT_DIR\07_migrate_data.sql" `
    -Description "Migrating data from PROD to COMEE"

if (-not $success) {
    Write-Host "Data migration encountered issues. Please review the output." -ForegroundColor Yellow
}

# ============================================================================
# PHASE 5: Verification
# ============================================================================
Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Magenta
Write-Host "║   PHASE 5: Verification                                        ║" -ForegroundColor Magenta
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Magenta

$success = Execute-SQLScript `
    -Host $PROD_HOST `
    -Port $PROD_PORT `
    -Service $PROD_SERVICE `
    -User $PROD_USER `
    -Pass $PROD_PASS `
    -ScriptPath "$SCRIPT_DIR\99_verification_queries.sql" `
    -Description "Running verification tests"

# ============================================================================
# Summary
# ============================================================================
Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║   Setup Complete!                                              ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "  1. Review verification test results above" -ForegroundColor White
Write-Host "  2. Test Laravel application:" -ForegroundColor White
Write-Host "     php artisan migrate:status" -ForegroundColor Gray
Write-Host "  3. Test database operations in Tinker" -ForegroundColor White
Write-Host "  4. If everything works, you can optionally run:" -ForegroundColor White
Write-Host "     database\oracle\distributed\08_drop_original_tables_prod.sql" -ForegroundColor Gray
Write-Host ""
Write-Host "Documentation:" -ForegroundColor Yellow
Write-Host "  database\oracle\distributed\README.md" -ForegroundColor Gray
Write-Host ""
