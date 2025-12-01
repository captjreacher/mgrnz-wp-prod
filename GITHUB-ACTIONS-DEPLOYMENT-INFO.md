# GitHub Actions Deployment Information

## What Gets Deployed

The GitHub Action (`.github/workflows/deploy.yml`) deploys the **ENTIRE wp-content directory**:

### Synced Directory:
- **Local:** `./wp-content/`
- **Production:** `/public_html/wp/wp-content/`
- **Includes:** 
  - All themes (`themes/`)
  - All mu-plugins (`mu-plugins/`)
  - All plugins (`plugins/`)
  - Wizard files (`themes/saaslauncher/templates/wizard/`)
  - Functions.php and all theme files
  - Everything in wp-content except exclusions

## What Does NOT Get Deployed

❌ **Root-level files** (not in wp-content):
- `*.md` files (README, documentation)
- `*.sql` files
- `*.html` test files in root
- `*.php` test files in root
- Any files in the root directory

❌ **Excluded from wp-content:**
- `uploads/` (user uploads, managed on server)
- `cache/` (cache files, regenerated)
- `backup*/` and `backups/` (backup directories)
- `upgrade/` (WordPress upgrade files)
- `ai2html-output/` (generated content)
- `*.log` and `debug.log` (log files)
- `.git*` files and directories
- `node_modules/` directories
- `tests/` directories
- `README.md` files
- `deploy-wizard-to-production.*` files

## Files That WILL Deploy

✅ **Everything in wp-content:**
- **MU-Plugins:** All files in `mu-plugins/`
  - `mailerlite-webhook-handler.php` ✅
  - `test-mailerlite-webhook.php` ✅
  - `enqueue-mailerlite-populate-script.php` ✅
  - `mailerlite-integration.php` ✅
  - `includes/class-submission-cpt.php` ✅
  - `migrations/*.php` and `*.sql` ✅
  - `DEPLOYMENT-TEST.txt` ✅

- **Theme Files:** All files in `themes/saaslauncher/`
  - `functions.php` ✅
  - `templates/wizard/*.php` ✅
  - `templates/wizard/*.js` ✅
  - `templates/wizard/*.css` ✅
  - `templates/populate-mailerlite-submission-ref.js` ✅
  - All other theme files ✅

- **Plugins:** All files in `plugins/` ✅

## Files That Will NOT Deploy

❌ **Documentation (root level):**
- `AI-SUBMISSION-ID-IMPLEMENTATION.md`
- `MAILERLITE-SETUP-GUIDE.md`
- `CROSS-POPULATION-COMPLETE.md`
- `QUICK-START.md`
- `IMPLEMENTATION-SUMMARY.md`
- etc.

❌ **Test Files (root level):**
- `test-*.php`
- `check-*.php`
- `run-migration-simple.php`
- `create-table-with-ai-id.sql`

❌ **Standalone Files:**
- `quote-my-workflow-standalone.php`
- `wizard-subscribe-standalone.php`

## How to Verify Deployment

### Method 1: Check DEPLOYMENT-TEST.txt
```
https://mgrnz.com/wp-content/mu-plugins/DEPLOYMENT-TEST.txt
```

If you can access this file, deployment is working!

### Method 2: Check Specific Files
```
https://mgrnz.com/wp-content/mu-plugins/mailerlite-webhook-handler.php
https://mgrnz.com/wp-content/mu-plugins/test-mailerlite-webhook.php
```

You should get a blank page or PHP error (not 404).

### Method 3: Check WordPress Admin
1. Go to production WordPress admin
2. Navigate to Settings → MailerLite
3. You should see the webhook setup instructions

### Method 4: Check GitHub Actions Log
1. Go to: https://github.com/captjreacher/mgrnz-wp-prod/actions
2. Click on the latest workflow run
3. Check the "Sync MU-Plugins" step
4. Look for uploaded files in the log

## Deployment Trigger

Deployment happens automatically when you:
1. Commit changes to `main` branch
2. Push to GitHub: `git push origin main`

## Deployment Time

- Usually completes in 2-5 minutes
- Check GitHub Actions for progress
- Files are synced via FTP

## If Files Aren't Deploying

### Check 1: Are commits pushed?
```bash
git log --oneline -5
git log origin/main --oneline -5
```

If they don't match, push:
```bash
git push origin main
```

### Check 2: Is GitHub Action running?
- Visit: https://github.com/captjreacher/mgrnz-wp-prod/actions
- Look for yellow (running) or green (completed) status

### Check 3: Are files in the right directories?
- MU-Plugins: Must be in `wp-content/mu-plugins/`
- Theme files: Must be in `wp-content/themes/saaslauncher/`
- Root files: Will NOT deploy automatically

### Check 4: Check .gitignore
Make sure your files aren't excluded:
```bash
git check-ignore -v wp-content/mu-plugins/your-file.php
```

## Manual Deployment (If Needed)

If automatic deployment fails, you can manually deploy:

### Via FTP:
1. Connect to FTP server
2. Navigate to `/public_html/wp/wp-content/mu-plugins/`
3. Upload the new/modified files

### Via SSH:
```bash
# Copy specific file
scp wp-content/mu-plugins/mailerlite-webhook-handler.php user@server:/public_html/wp/wp-content/mu-plugins/
```

## Current Deployment Status

**Last Successful Deploy:** Check GitHub Actions
**Latest Commit on Production:** Should match `git log origin/main -1`
**Files Deployed:** Theme + MU-Plugins only

---

**Summary:** Only `wp-content/themes/saaslauncher/` and `wp-content/mu-plugins/` are automatically deployed. Root-level documentation and test files must be manually copied if needed on production.
