# Fix: Wizard Showing as Code Instead of Rendering

## The Problem

The wizard HTML is displaying as text on the page instead of rendering properly. This happens when you paste HTML directly into WordPress - it escapes the code for security.

## The Solution: Use WPCode

### Step 1: Install WPCode (if not already installed)
1. Go to **Plugins → Add New**
2. Search for "WPCode"
3. Install and activate

### Step 2: Create the Snippet

1. Go to **WPCode → Add Snippet**
2. Click **"Add Your Custom Code (New Snippet)"**
3. Name it: **"AI Workflow Wizard"**
4. Set Code Type: **PHP Snippet**
5. Paste this code:

```php
<?php
// Output the wizard HTML
$wizard_file = get_template_directory() . '/templates/ai-workflow-wizard.html';

if (file_exists($wizard_file)) {
    include $wizard_file;
} else {
    echo '<div style="padding: 2rem; background: #fee; border: 1px solid #fcc; border-radius: 8px; color: #c00;">';
    echo '<strong>Error:</strong> Wizard template not found at: ' . esc_html($wizard_file);
    echo '</div>';
}
?>
```

6. **Location:** Select **"Page Specific"**
7. Choose your **"Try Using AI Right Now"** page
8. **Insert Method:** Choose **"After Content"** (or "Replace Content" if you want only the wizard)
9. Click **"Save Snippet"**
10. Toggle it to **Active**

### Step 3: Clean Up Your Page

1. Go to **Pages → Try Using AI Right Now**
2. **Remove** any HTML code you pasted in the editor
3. Keep only your intro text (if any)
4. **Update** the page

### Step 4: Test

1. Visit your page: `mgrnz.local/start-using-ai/`
2. The wizard should now render properly!

## Alternative: Use the Shortcode

If you prefer not to use WPCode:

1. Go to **Pages → Try Using AI Right Now**
2. Add a **Shortcode block**
3. Type: `[ai_workflow_wizard]`
4. **Update** the page

The shortcode is already registered in your theme's functions.php.

## Why This Happens

WordPress escapes HTML for security. When you paste raw HTML:
- It converts `<` to `&lt;`
- It converts `>` to `&gt;`
- The browser displays the code as text

Using WPCode or a shortcode tells WordPress "this is safe code, execute it."

## Verification

After fixing, you should see:
- ✅ Styled wizard form (not code)
- ✅ Dark navy background
- ✅ Orange accent colors
- ✅ Interactive form fields
- ✅ No visible HTML tags

## Still Not Working?

If you still see code:

1. **Clear cache:**
   - Browser cache (Ctrl+Shift+R)
   - WordPress cache (if using a caching plugin)

2. **Check file path:**
   - Verify: `themes/saaslauncher/templates/ai-workflow-wizard.html` exists
   - Check file permissions (should be readable)

3. **Check WPCode settings:**
   - Snippet is Active (green toggle)
   - Location is set to your page
   - Insert method is selected

4. **Try incognito mode:**
   - Rules out browser caching issues

## Quick Fix Checklist

- [ ] Remove raw HTML from page editor
- [ ] Create WPCode snippet with PHP code above
- [ ] Set to Page Specific → Your page
- [ ] Activate the snippet
- [ ] Clear all caches
- [ ] Test in incognito mode

---

**This will fix the issue!** The wizard will render properly instead of showing as code.
