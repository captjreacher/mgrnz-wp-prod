# AI Workflow Wizard - Block Pattern Setup Complete ✅

## What's Been Done

I've set up the AI Workflow Wizard as both a **shortcode** and a **block pattern** in your WordPress theme.

## Files Created/Updated

1. ✅ **`themes/saaslauncher/functions.php`** - Added shortcode and pattern registration
2. ✅ **`themes/saaslauncher/templates/ai-workflow-wizard.html`** - Wizard template file

## How to Use

### Method 1: Block Pattern (Easiest)

1. Edit your "Try Using AI Right Now" page
2. Click the **+** button to add a block
3. Search for **"AI Workflow Wizard"**
4. Click to insert the pattern
5. Publish!

The wizard will appear in the **"MGRNZ"** category in your block patterns.

### Method 2: Shortcode

Simply add this anywhere in your page content:

```
[ai_workflow_wizard]
```

### Method 3: PHP Template

In any template file:

```php
<?php echo do_shortcode('[ai_workflow_wizard]'); ?>
```

## What You Get

- ✅ 5-step wizard with validation
- ✅ AI progress animation
- ✅ Detailed blueprint with data flow diagram
- ✅ 4 completion buttons
- ✅ MailerLite integration (Form ID: E0CY8N)
- ✅ Fully responsive design
- ✅ Dark navy + orange MGRNZ branding

## Testing

1. Go to your WordPress admin
2. Edit the "Try Using AI Right Now" page
3. Add the pattern or shortcode
4. Click **Preview** to see it in action
5. Test the full wizard flow

## Updating the Wizard

To update the wizard in the future:

1. Edit: `themes/mgrnz-theme/templates/ai-workflow-wizard.html`
2. Make your changes
3. Save
4. Refresh your page - changes appear immediately!

## Troubleshooting

### Pattern doesn't appear in block inserter

1. Go to **Appearance → Editor**
2. Click **Patterns** in the sidebar
3. Look for "AI Workflow Wizard" in the MGRNZ category

### Shortcode shows as text

- Make sure you're in a **Shortcode block**, not a Paragraph block
- Or use the pattern instead (easier)

### Wizard doesn't display

1. Check that the file exists: `themes/saaslauncher/templates/ai-workflow-wizard.html`
2. Check file permissions (should be readable)
3. Check browser console for JavaScript errors

## File Structure

```
themes/saaslauncher/
├── functions.php (updated with shortcode + pattern)
└── templates/
    └── ai-workflow-wizard.html (complete wizard)
```

## Next Steps

1. **Test the wizard** on your page
2. **Customize colors** if needed (edit the CSS variables in the HTML file)
3. **Test MailerLite form** by clicking "Get a Quote"
4. **Go live!** 🚀

## Benefits of This Setup

✅ **Reusable** - Use the pattern on multiple pages
✅ **Easy to update** - Edit one file, changes everywhere
✅ **Clean code** - Separated from page content
✅ **Version controlled** - Part of your theme
✅ **Professional** - Proper WordPress integration

## Support

If you need to make changes:
- **Styling**: Edit CSS in `templates/ai-workflow-wizard.html`
- **Content**: Edit HTML in `templates/ai-workflow-wizard.html`
- **Functionality**: Edit JavaScript in `templates/ai-workflow-wizard.html`

Everything is in one file for easy maintenance!

---

**Ready to use!** Just insert the pattern or shortcode on your page. 🎉
