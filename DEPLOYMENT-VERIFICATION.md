# Deployment Verification Guide

## Quick Test

**Check if deployment is working:**

Visit this URL on production:
```
https://mgrnz.com/wp-content/mu-plugins/DEPLOYMENT-TEST.txt
```

✅ **If you see the file:** Deployment is working!
❌ **If you get 404:** Deployment failed or file not synced yet

## What Gets Deployed

### ✅ Automatically Deployed:
1. **MU-Plugins:** `wp-content/mu-plugins/` → Production
2. **Theme Files:** `wp-content/themes/saaslauncher/` → Production

### ❌ NOT Deployed:
- Root-level `.md` files (documentation)
- Root-level test files (`test-*.php`)
- Root-level SQL files
- Any files outside theme/mu-plugins folders

## Folders Excluded from Deployment

According to `.github/workflows/deploy.yml`:

```yaml
exclude: |
  **/.git*
  **/.git*/**
  **/node_modules/**
  **/tests/**
  README.md
  deploy-wizard-to-production.*
```

## Recent Changes That WILL Deploy

✅ **MU-Plugins (will deploy):**
- `mailerlite-webhook-handler.php`
- `test-mailerlite-webhook.php`
- `enqueue-mailerlite-populate-script.php`
- `mailerlite-integration.php`
- `includes/class-submission-cpt.php` (modified)
- `DEPLOYMENT-TEST.txt` (new)

✅ **Theme Files (will deploy):**
- `templates/populate-mailerlite-submission-ref.js`

## Recent Changes That Will NOT Deploy

❌ **Documentation (won't deploy):**
- `AI-SUBMISSION-ID-IMPLEMENTATION.md`
- `MAILERLITE-SETUP-GUIDE.md`
- `CROSS-POPULATION-COMPLETE.md`
- `GITHUB-ACTIONS-DEPLOYMENT-INFO.md`
- `DEPLOYMENT-VERIFICATION.md`
- etc.

## Verification Steps

### Step 1: Check GitHub Actions
1. Go to: https://github.com/captjreacher/mgrnz-wp-prod/actions
2. Look for the latest workflow run
3. Status should be green ✅

### Step 2: Check Test File
```
https://mgrnz.com/wp-content/mu-plugins/DEPLOYMENT-TEST.txt
```

### Step 3: Check Webhook Endpoint
```
https://mgrnz.com/wp-json/mgrnz/v1/mailerlite-webhook
```

Should return JSON (not 404)

### Step 4: Check WordPress Admin
1. Login to production WordPress
2. Go to Settings → MailerLite
3. Should see webhook setup instructions

## Timing

- **Push to GitHub:** Immediate
- **GitHub Action Starts:** ~30 seconds
- **Deployment Completes:** 2-5 minutes
- **Files Available:** Immediately after deployment

## If Deployment Fails

### Check 1: Commits Pushed?
```bash
git log origin/main --oneline -5
```

Should show your latest commits.

### Check 2: GitHub Action Status
Visit: https://github.com/captjreacher/mgrnz-wp-prod/actions

Look for errors in the workflow log.

### Check 3: FTP Credentials
GitHub Action uses these secrets:
- `FTP_SERVER`
- `FTP_USERNAME`
- `FTP_PASSWORD`

Verify they're set correctly in GitHub repo settings.

## Manual Deployment (If Needed)

If automatic deployment isn't working, manually upload:

### Via FTP:
1. Connect to FTP
2. Upload `wp-content/mu-plugins/` files
3. Upload `wp-content/themes/saaslauncher/` files

### Via cPanel File Manager:
1. Navigate to `/public_html/wp/wp-content/mu-plugins/`
2. Upload new/modified files

## Current Status

**Latest Commit:** `5800386`
**Includes:**
- Deployment test file
- Webhook handler
- MailerLite integration
- Submission CPT updates

**Expected on Production:**
- All mu-plugin files
- All theme files
- DEPLOYMENT-TEST.txt (for verification)

---

**Next Steps:**
1. Wait 2-5 minutes for deployment
2. Check DEPLOYMENT-TEST.txt URL
3. Verify webhook endpoint exists
4. Test MailerLite webhook integration
