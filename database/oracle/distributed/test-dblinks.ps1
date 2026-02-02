# ============================================================================
# Quick Test Script - Test Database Link Connectivity
# Description: Verifies that both database links are working before setup
# Usage: .\test-dblinks.ps1
# ============================================================================

Write-Host "════════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "Testing Database Link Connectivity" -ForegroundColor Cyan
Write-Host "════════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

# Configuration
$PROD_HOST = "172.16.8.125"
$PROD_PORT = "1521"
$PROD_SERVICE = "prod"
$PROD_USER = "u_prod"
$PROD_PASS = "secreto123"

$COMEE_HOST = "172.16.18.125"
$COMEE_PORT = "1521"
$COMEE_SERVICE = "comee"
$COMEE_USER = "u_comee"
$COMEE_PASS = "secreto123"

# Test PROD connectivity
Write-Host "Testing PROD connection..." -ForegroundColor Yellow
$testQuery = "SELECT 'PROD_OK' as status FROM dual;"
$prodConnection = "${PROD_USER}/${PROD_PASS}@${PROD_HOST}:${PROD_PORT}/${PROD_SERVICE}"
$result = echo "SET HEADING OFF`n$testQuery`nexit;" | sqlplus -S $prodConnection 2>&1

if ($result -match "PROD_OK") {
    Write-Host "✓ PROD connection successful" -ForegroundColor Green
} else {
    Write-Host "✗ PROD connection failed" -ForegroundColor Red
    Write-Host "Error: $result" -ForegroundColor Red
}

Write-Host ""

# Test COMEE connectivity
Write-Host "Testing COMEE connection..." -ForegroundColor Yellow
$testQuery = "SELECT 'COMEE_OK' as status FROM dual;"
$comeeConnection = "${COMEE_USER}/${COMEE_PASS}@${COMEE_HOST}:${COMEE_PORT}/${COMEE_SERVICE}"
$result = echo "SET HEADING OFF`n$testQuery`nexit;" | sqlplus -S $comeeConnection 2>&1

if ($result -match "COMEE_OK") {
    Write-Host "✓ COMEE connection successful" -ForegroundColor Green
} else {
    Write-Host "✗ COMEE connection failed" -ForegroundColor Red
    Write-Host "Error: $result" -ForegroundColor Red
}

Write-Host ""

# Test PROD -> COMEE database link
Write-Host "Testing PROD -> COMEE database link..." -ForegroundColor Yellow
$testQuery = "SELECT 'LINK_OK' as status FROM dual``@link_comee;"
$result = echo "SET HEADING OFF`n$testQuery`nexit;" | sqlplus -S $prodConnection 2>&1

if ($result -match "LINK_OK") {
    Write-Host "✓ Database link PROD -> COMEE working" -ForegroundColor Green
} else {
    Write-Host "✗ Database link PROD -> COMEE failed" -ForegroundColor Red
    Write-Host "Error: $result" -ForegroundColor Red
    Write-Host ""
    Write-Host "Database link might not exist. Expected link name: link_comee" -ForegroundColor Yellow
}

Write-Host ""

# Test COMEE -> PROD database link
Write-Host "Testing COMEE -> PROD database link..." -ForegroundColor Yellow
$testQuery = "SELECT 'LINK_OK' as status FROM dual``@link_prod;"
$result = echo "SET HEADING OFF`n$testQuery`nexit;" | sqlplus -S $comeeConnection 2>&1

if ($result -match "LINK_OK") {
    Write-Host "✓ Database link COMEE -> PROD working" -ForegroundColor Green
} else {
    Write-Host "✗ Database link COMEE -> PROD failed" -ForegroundColor Red
    Write-Host "Error: $result" -ForegroundColor Red
    Write-Host ""
    Write-Host "Database link might not exist. Expected link name: link_prod" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "════════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "Test Complete" -ForegroundColor Cyan
Write-Host "════════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Host "If all tests passed, you can proceed with the setup:" -ForegroundColor Green
Write-Host "  .\database\oracle\distributed\execute-distributed-setup.ps1" -ForegroundColor Gray
Write-Host ""
