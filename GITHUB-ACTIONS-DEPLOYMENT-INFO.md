# GitHub Actions Deployment Information

## What Gets Deployed

The GitHub Action (`.github/workflows/deploy.yml`) deploys **ONLY** these directories:

### 1. Theme Files
- **Local:** `./wp-content/themes/saaslauncher/`
- **Production:** `/public_html/wp/wp-content/themes/saaslauncher/`
- **Includes:** All theme templates, CSS, JS, PHP files

### 2. MU-Plugins
- **Local:** `./wp-content/mu-plugins/`
- **Production:** `/public_html/wp/wp-content/mu-plugins/`
- **Includes:** All must-use plugins and their subdirectories

## What Does NOT Get Deployed

❌ **Root-level files** (not deployed automatically):
- `*.md` files (README, documentation)
- `*.sql` files
- `*.html` test files
- `*.php` test files in root
- Any other files in the root directory

❌ **Excluded by .gitignore:**
- `wp-config.php`
- `.env` files
- `wp-content/uploads/`
- `wp-content/cache/`
- `*.log` files
- `node_modules/`
- Test files (`test-*.php`, `test-*.html`)
- Standalone files (`*-standalone.php`)
- Debug files (`debug-*.html`)

❌ **Excluded by deploy.yml:**
- `.git*` files and directories
- `node_modules/`
- `tests/`
- `README.md`
- `deploy-wizard-to-production.*`

## Files That WILL Deploy

✅ **MU-Plugins (all new files):**
- `mailerlite-webhook-handler.php` ✅
- `test-mailerlite-webhook.php` ✅
- `enqueue-mailerlite-populate-script.php` ✅
- `mailerlite-integration.php` ✅
- `create-subscriptions-table.php` ✅
- `auto-run-migration.php` ✅
- `migration-admin-page.php` ✅
- `includes/class-submission-cpt.php` ✅ (modified)
- `migrations/*.php` ✅
- `migrations/*.sql` ✅
- `DEPLOYMENT-TEST.txt` ✅ (new test file)

✅ **Theme Files:**
- `templates/populate-mailerlite-submission-ref.js` ✅
- `templates/wizard/*.php` ✅
- `templates/wizard/*.js` ✅
- `templates/wizard/*.css` ✅

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
