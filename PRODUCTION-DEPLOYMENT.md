# Production Deployment Guide

## Quick Deploy Script

Run this script to deploy the latest changes to production:

```bash
./deploy-to-production.sh
```

## Manual Deployment Steps

### Files Changed (Blueprint PDF Fix)

The following files were modified to fix the PDF generation issue:

1. **wp-content/mu-plugins/includes/class-pdf-generator.php**
   - Switched from TCPDF to HTML generation
   - More reliable, better quality output

2. **wp-content/mu-plugins/blueprint-auth-bypass.php** (NEW)
   - Allows public access to blueprint endpoints

3. **wp-content/mu-plugins/blueprint-viewer.php** (NEW)
   - Serves HTML blueprints with proper headers

4. **wp-content/themes/saaslauncher/templates/wizard/wizard-scripts.js**
   - Updated download button logic
   - Removed nonce requirement

5. **wp-content/themes/saaslauncher/templates/wizard-subscribe-page.php**
   - Fixed flashing/disappearing content issue

6. **quote-my-workflow.html** (NEW)
   - Standalone quote page without WordPress theme interference

### Deployment Methods

#### Option 1: Git Deployment (Recommended)

```bash
# Commit changes
git add .
git commit -m "Fix: Switch to HTML blueprint generation, fix quote page"

# Push to production
git push production main
```

#### Option 2: FTP/SFTP Upload

Upload these files to your production server:
- All files listed above
- Maintain the same directory structure

#### Option 3: WP Engine/Kinsta

Use their Git push deployment or SFTP tools.

## Post-Deployment Checklist

- [ ] Test wizard flow end-to-end
- [ ] Generate a test blueprint
- [ ] Verify HTML file displays correctly
- [ ] Test "Print to PDF" functionality
- [ ] Check email delivery
- [ ] Verify quote page loads without errors
- [ ] Test on mobile devices

## Rollback Plan

If issues occur, revert these commits:
```bash
git revert HEAD
git push production main
```

## Environment Variables

Ensure these are set in production:
- `MGRNZ_AI_API_KEY` - OpenAI/Anthropic API key
- `MGRNZ_AI_PROVIDER` - 'openai' or 'anthropic'
- `MGRNZ_AI_MODEL` - Model name (e.g., 'gpt-4')

## Testing in Production

1. Visit: https://your-domain.com/start-using-ai/
2. Complete the wizard
3. Generate blueprint
4. Download and verify HTML displays
5. Test print to PDF

## Support

If issues arise:
1. Check WordPress debug.log
2. Check browser console for JavaScript errors
3. Verify API keys are configured
4. Test with different browsers
