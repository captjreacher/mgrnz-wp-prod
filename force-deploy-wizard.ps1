# Force Deploy Wizard Files to Production
# This uploads ONLY the wizard files that need updating

Write-Host "🚀 Force Deploy Wizard Files" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Files to upload
$filesToUpload = @(
    "wp-content/themes/saaslauncher/templates/wizard/wizard-main.php",
    "wp-content/themes/saaslauncher/templates/wizard/wizard-scripts.js",
    "wp-content/themes/saaslauncher/templates/wizard/wizard-step-3-chat.php",
    "wp-content/mu-plugins/includes/class-pdf-generator.php",
    "wizard-subscribe-standalone.php"
)

Write-Host "Files to upload:" -ForegroundColor Yellow
foreach ($file in $filesToUpload) {
    if (Test-Path $file) {
        Write-Host "  ✅ $file" -ForegroundColor Green
    } else {
        Write-Host "  ❌ $file (NOT FOUND)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Options:" -ForegroundColor Cyan
Write-Host "1. Use GitHub Actions (recommended - automatic)"
Write-Host "2. Use FTP client (manual - requires credentials)"
Write-Host "3. Create ZIP file for manual upload"
Write-Host ""

$choice = Read-Host "Select option (1-3)"

switch ($choice) {
    "1" {
        Write-Host ""
        Write-Host "Triggering GitHub Actions deployment..." -ForegroundColor Yellow
        Write-Host ""
        
        # Make a small change to trigger deployment
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        Add-Content -Path "wp-content/themes/saaslauncher/templates/wizard/wizard-scripts.js" -Value "`n// Force deploy: $timestamp"
        
        git add -A
        git commit -m "Force deploy wizard files - $timestamp"
        git push
        
        Write-Host ""
        Write-Host "✅ Pushed to GitHub!" -ForegroundColor Green
        Write-Host ""
        Write-Host "Check deployment status at:" -ForegroundColor Cyan
        Write-Host "https://github.com/captjreacher/mgrnz-wp-prod/actions" -ForegroundColor Blue
        Write-Host ""
        Write-Host "Wait 2-3 minutes for deployment to complete." -ForegroundColor Yellow
    }
    
    "2" {
        Write-Host ""
        Write-Host "FTP Upload requires credentials from GitHub Secrets" -ForegroundColor Yellow
        Write-Host "Check your GitHub repository settings → Secrets → Actions" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "You need:" -ForegroundColor Yellow
        Write-Host "  - FTP_SERVER"
        Write-Host "  - FTP_USERNAME"
        Write-Host "  - FTP_PASSWORD"
        Write-Host ""
        Write-Host "Use an FTP client like FileZilla or WinSCP to upload manually." -ForegroundColor Cyan
    }
    
    "3" {
        Write-Host ""
        Write-Host "Creating ZIP file..." -ForegroundColor Yellow
        
        $zipPath = "wizard-files-$(Get-Date -Format 'yyyyMMdd-HHmmss').zip"
        
        # Create temp directory structure
        $tempDir = "temp-wizard-deploy"
        if (Test-Path $tempDir) {
            Remove-Item $tempDir -Recurse -Force
        }
        New-Item -ItemType Directory -Path $tempDir | Out-Null
        
        foreach ($file in $filesToUpload) {
            if (Test-Path $file) {
                $destPath = Join-Path $tempDir $file
                $destDir = Split-Path $destPath -Parent
                if (-not (Test-Path $destDir)) {
                    New-Item -ItemType Directory -Path $destDir -Force | Out-Null
                }
                Copy-Item $file $destPath
            }
        }
        
        # Create ZIP
        Compress-Archive -Path "$tempDir\*" -DestinationPath $zipPath -Force
        Remove-Item $tempDir -Recurse -Force
        
        Write-Host ""
        Write-Host "✅ ZIP file created: $zipPath" -ForegroundColor Green
        Write-Host ""
        Write-Host "Upload instructions:" -ForegroundColor Cyan
        Write-Host "1. Extract the ZIP file"
        Write-Host "2. Upload files to your production server maintaining the directory structure"
        Write-Host "3. Files should go to: /public_html/wp/" -ForegroundColor Yellow
    }
    
    default {
        Write-Host "Invalid option" -ForegroundColor Red
    }
}

Write-Host ""
