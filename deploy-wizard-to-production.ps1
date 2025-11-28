# Deploy New Wizard to Production (PowerShell)
# This script helps you upload the new 3-step chat wizard files

Write-Host "🚀 Deploy New Wizard to Production" -ForegroundColor Cyan
Write-Host "====================================" -ForegroundColor Cyan
Write-Host ""

# Configuration - UPDATE THESE
$PROD_HOST = "your-production-server.com"
$PROD_USER = "your-ftp-username"
$PROD_PASS = "your-ftp-password"
$PROD_PATH = "/public_html/wp-content/themes/saaslauncher"

Write-Host "⚠️  Configuration Check" -ForegroundColor Yellow
Write-Host "Host: $PROD_HOST"
Write-Host "User: $PROD_USER"
Write-Host "Path: $PROD_PATH"
Write-Host ""

$continue = Read-Host "Have you updated the configuration in this script? (y/n)"
if ($continue -ne "y") {
    Write-Host "❌ Please edit deploy-wizard-to-production.ps1 and update the configuration" -ForegroundColor Red
    exit
}

Write-Host ""
Write-Host "📁 Files to upload:" -ForegroundColor Yellow
Write-Host "1. page-wizard-clean.php"
Write-Host "2. templates/wizard/ (entire directory)"
Write-Host ""

# Check if files exist
$pageTemplate = "wp-content\themes\saaslauncher\page-wizard-clean.php"
$wizardDir = "wp-content\themes\saaslauncher\templates\wizard"

if (-not (Test-Path $pageTemplate)) {
    Write-Host "❌ Error: $pageTemplate not found" -ForegroundColor Red
    exit
}

if (-not (Test-Path $wizardDir)) {
    Write-Host "❌ Error: $wizardDir not found" -ForegroundColor Red
    exit
}

Write-Host "✅ All files found locally" -ForegroundColor Green
Write-Host ""

# List files to upload
Write-Host "Files in wizard directory:" -ForegroundColor Cyan
Get-ChildItem $wizardDir | ForEach-Object { Write-Host "  - $($_.Name)" }
Write-Host ""

$upload = Read-Host "Continue with upload via FTP? (y/n)"
if ($upload -ne "y") {
    Write-Host "❌ Upload cancelled" -ForegroundColor Red
    exit
}

Write-Host ""
Write-Host "📤 Uploading files..." -ForegroundColor Yellow
Write-Host ""

# FTP Upload Function
function Upload-FTP {
    param($LocalPath, $RemotePath)
    
    try {
        $ftp = [System.Net.FtpWebRequest]::Create("ftp://$PROD_HOST$RemotePath")
        $ftp.Credentials = New-Object System.Net.NetworkCredential($PROD_USER, $PROD_PASS)
        $ftp.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        
        $content = [System.IO.File]::ReadAllBytes($LocalPath)
        $ftp.ContentLength = $content.Length
        
        $stream = $ftp.GetRequestStream()
        $stream.Write($content, 0, $content.Length)
        $stream.Close()
        
        Write-Host "  ✅ Uploaded: $(Split-Path $LocalPath -Leaf)" -ForegroundColor Green
        return $true
    }
    catch {
        Write-Host "  ❌ Failed: $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

# Upload page template
Write-Host "Uploading page-wizard-clean.php..." -ForegroundColor Cyan
Upload-FTP -LocalPath $pageTemplate -RemotePath "$PROD_PATH/page-wizard-clean.php"

# Upload wizard files
Write-Host ""
Write-Host "Uploading wizard directory..." -ForegroundColor Cyan
Get-ChildItem $wizardDir -File | ForEach-Object {
    $remotePath = "$PROD_PATH/templates/wizard/$($_.Name)"
    Upload-FTP -LocalPath $_.FullName -RemotePath $remotePath
}

Write-Host ""
Write-Host "=====================================" -ForegroundColor Green
Write-Host "✅ Upload Complete!" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Go to WordPress Admin → Pages"
Write-Host "2. Edit your wizard page"
Write-Host "3. Change Template to: 'Wizard (Clean - No WP Scripts)'"
Write-Host "4. Click Update"
Write-Host ""
Write-Host "The new 3-step chat wizard will then be live!" -ForegroundColor Cyan
