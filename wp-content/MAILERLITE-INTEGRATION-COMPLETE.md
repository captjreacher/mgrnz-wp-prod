# MailerLite Integration - Complete

## ✅ What's Been Implemented

The "Get a Quote for this Workflow" button now triggers your MailerLite popup form.

## 🎯 How It Works

### 1. MailerLite Script Loaded
Added to the `<head>` section:
```html
<script>
  (function(w,d,e,u,f,l,n){w[f]=w[f]||function(){(w[f].q=w[f].q||[])
  .push(arguments);},l=d.createElement(e),l.async=1,l.src=u,
  n=d.getElementsByTagName(e)[0],n.parentNode.insertBefore(l,n);})
  (window,document,'script','https://assets.mailerlite.com/js/universal.js','ml');
  ml('account', '1096596');
</script>
```

### 2. Button Click Handler
When user clicks "Get a Quote for this Workflow":
```javascript
document.getElementById('btn-get-quote').addEventListener('click', () => {
  // Trigger MailerLite popup form
  if (typeof ml === 'function') {
    ml('show', 'E0CY8N', true);
  } else {
    console.error('MailerLite not loaded');
    alert('Quote form is loading. Please try again in a moment.');
  }
});
```

### 3. Form ID
Your MailerLite form ID: **E0CY8N**

## 🎬 User Experience

1. User completes the 5-step wizard
2. Sees their personalized blueprint
3. Completion screen appears with 4 buttons
4. Clicks "💰 Get a Quote for this Workflow"
5. **MailerLite popup form appears** (clean overlay)
6. User fills out the form
7. Form submits to your MailerLite account

## ✨ Benefits of Popup vs Embedded

✅ **Cleaner UX** - No scrolling required
✅ **Professional** - Modal overlay focuses attention
✅ **Mobile-friendly** - Works perfectly on all devices
✅ **Faster** - No need to load embedded form HTML
✅ **Maintained by MailerLite** - Form updates automatically

## 🔧 Configuration

### Your Current Settings
- **Account ID:** 1096596
- **Form ID:** E0CY8N
- **Trigger:** Button click
- **Display:** Popup modal

### To Update Form
1. Log into MailerLite
2. Edit form E0CY8N
3. Changes apply automatically (no code update needed)

## 📱 Testing

To test the integration:
1. Open the wizard page
2. Complete all 5 steps
3. Click "Build my AI workflow"
4. Wait for blueprint to appear
5. Click "Get a Quote for this Workflow"
6. MailerLite popup should appear

### Troubleshooting

**If popup doesn't appear:**
- Check browser console for errors
- Verify MailerLite script loaded (check Network tab)
- Confirm form ID E0CY8N is active in your MailerLite account
- Try in incognito mode to rule out caching

**If form appears but doesn't submit:**
- Check MailerLite form settings
- Verify form is published and active
- Check for any field validation issues

## 🎨 Form Styling

The popup form uses your MailerLite theme settings. To customize:
1. Go to MailerLite → Forms
2. Select form E0CY8N
3. Click "Design" tab
4. Customize colors, fonts, etc.
5. Save changes

## 📊 Data Collection

When users submit the form, you'll receive:
- Name
- Email
- Phone (if provided)
- Any custom fields you've added
- Submission timestamp
- Source (this wizard page)

All data goes directly to your MailerLite account.

## 🚀 Ready to Use

The integration is complete and ready to use. Just deploy the updated `ai-workflow-wizard-complete.html` file to your WordPress site.

## 📝 Alternative: Embedded Form

If you prefer an embedded form instead of popup, the HTML file also includes a styled embedded form section at `#workflow-quote-form`. To use it instead:

1. Find this line in the JavaScript:
   ```javascript
   ml('show', 'E0CY8N', true);
   ```

2. Replace with:
   ```javascript
   const quoteFormBlock = document.getElementById('workflow-quote-form');
   quoteFormBlock.classList.add('show');
   quoteFormBlock.scrollIntoView({ behavior: 'smooth', block: 'start' });
   ```

But the popup is recommended for better UX.
