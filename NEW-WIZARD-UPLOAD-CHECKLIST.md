# New 3-Step Chat Wizard - Upload Checklist

## Files to Upload to Production

### 1. Page Template (1 file)
Upload this file to: `wp-content/themes/saaslauncher/`

- [ ] `page-wizard-clean.php`

### 2. Wizard Components (7 files)
Upload these files to: `wp-content/themes/saaslauncher/templates/wizard/`

**Create the `wizard` folder if it doesn't exist!**

- [ ] `wizard-main.php`
- [ ] `wizard-step-1.php`
- [ ] `wizard-step-2.php`
- [ ] `wizard-step-3-chat.php`
- [ ] `wizard-completion.php`
- [ ] `wizard-scripts.js`
- [ ] `wizard-styles.css`

### 3. Subscribe Page (1 file)
Upload this file to: `wp-content/themes/saaslauncher/templates/`

- [ ] `wizard-subscribe-page.php`

### 4. Standalone Quote Page (1 file)
Upload this file to: **WordPress root directory** (same level as wp-config.php)

- [ ] `quote-my-workflow.html`

## After Upload

### Step 1: Verify Files
1. Check that the `wizard` folder exists at: `wp-content/themes/saaslauncher/templates/wizard/`
2. Check that all 7 files are inside it

### Step 2: Change Page Template
1. Go to WordPress Admin → Pages
2. Find and edit "Try Using AI Right Now" (or your wizard page)
3. In the right sidebar, find "Template" dropdown
4. Select: **"Wizard (Clean - No WP Scripts)"**
5. Click **Update**

### Step 3: Test
1. Visit: https://mgrnz.com/start-using-ai/
2. You should see the new 3-step wizard
3. Complete the wizard and test blueprint generation

## Troubleshooting

### If page is still blank:
- Check browser console (F12) for errors
- Verify all files uploaded correctly
- Check file permissions (should be 644 for files, 755 for folders)

### If template doesn't appear in dropdown:
- The `page-wizard-clean.php` file wasn't uploaded
- Upload it to: `wp-content/themes/saaslauncher/page-wizard-clean.php`

### If wizard shows but looks broken:
- CSS file didn't upload or has wrong path
- Check: `wp-content/themes/saaslauncher/templates/wizard/wizard-styles.css`

## Quick Test
After uploading, visit this URL to verify files exist:
https://mgrnz.com/wp-content/themes/saaslauncher/templates/wizard/wizard-styles.css

If you see CSS code, the files are in the right place!
