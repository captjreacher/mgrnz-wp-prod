# MailerLite submission_ref Field Auto-Population

## ✅ Implementation Complete

The AI submission ID now automatically populates the `submission_ref` field in your MailerLite forms.

## How It Works

When a user completes the AI wizard and visits a page with a MailerLite form:

1. JavaScript retrieves the AI session ID from localStorage
2. Finds the `submission_ref` field in the MailerLite form
3. Automatically fills it with the session ID
4. Makes the field readonly so users can't change it

## Files Created

1. **`wp-content/themes/saaslauncher/templates/populate-mailerlite-submission-ref.js`**
   - Standalone JavaScript file with the population logic
   - Can be included on any page

2. **`wp-content/mu-plugins/enqueue-mailerlite-populate-script.php`**
   - WordPress plugin that loads the script on all pages
   - Includes inline backup version for reliability

## Testing

### 1. Complete the Wizard
```
http://mgrnz.local/start-using-ai/
```

### 2. Go Through Chat
- Answer the AI questions
- Generate your blueprint

### 3. Visit the MailerLite Form Page
```
http://mgrnz.local/quote-my-workflow/
```

### 4. Check the Form
- The `submission_ref` field should be automatically filled
- It should be grayed out (readonly)
- The value should be something like: `sess_abc123xyz...`

## Troubleshooting

### Field Not Populating

**Check Browser Console:**
```javascript
// Open browser console (F12) and check for:
[ML Populate] Script loaded
[ML Populate] Found submission_ref in localStorage: sess_...
[ML Populate] ✅ Successfully populated submission_ref field
```

**Manually Check localStorage:**
```javascript
// In browser console:
JSON.parse(localStorage.getItem('mgrnz_wizard_data'))
// Should show: { submission_ref: "sess_..." }
```

**Manually Trigger Population:**
```javascript
// In browser console:
window.populateMailerLiteSubmissionRef()
// Should return true if successful
```

### Field Name Different

If your MailerLite field has a different name, update the selectors in the script:

```javascript
// Edit: wp-content/themes/saaslauncher/templates/populate-mailerlite-submission-ref.js
// Line ~50, add your field selector:
const selectors = [
    'input[name="fields[submission_ref]"]',
    'input[name="submission_ref"]',
    'input[name="YOUR_FIELD_NAME_HERE"]',  // Add this
    // ...
];
```

## How the Data Flows

```
User Completes Wizard
        ↓
AI Session ID saved to localStorage
   (key: mgrnz_wizard_data.submission_ref)
        ↓
User visits page with MailerLite form
        ↓
JavaScript detects form
        ↓
Reads AI Session ID from localStorage
        ↓
Populates submission_ref field
        ↓
User submits form to MailerLite
        ↓
MailerLite receives submission with AI Session ID
```

## Verification

### Check in MailerLite Dashboard

1. Go to **Subscribers** in MailerLite
2. Find a recent subscriber
3. Check their **Custom Fields**
4. The `submission_ref` field should contain the session ID

### Check in WordPress Database

```sql
SELECT email, ai_submission_id 
FROM wp_mgrnz_blueprint_subscriptions 
ORDER BY id DESC 
LIMIT 5;
```

Both should show the same session ID format: `sess_[32 characters]`

## Advanced: URL Parameter Method

You can also pass the submission ID via URL:

```
http://mgrnz.local/quote-my-workflow/?submission_ref=sess_abc123
```

The script will automatically detect and use it.

## Script Load Order

The script tries to populate the field at multiple times:

1. **Immediately** when script loads
2. **500ms** after DOM ready
3. **1 second** after DOM ready
4. **2 seconds** after DOM ready
5. **Continuously** watches for dynamically loaded forms

This ensures it works even if MailerLite loads its form asynchronously.

## Customization

### Change Field Styling

Edit the script to customize how the readonly field looks:

```javascript
// Line ~85 in populate-mailerlite-submission-ref.js
field.style.backgroundColor = '#your-color';
field.style.cursor = 'not-allowed';
```

### Disable Readonly

If you want users to be able to edit the field:

```javascript
// Comment out these lines:
// field.setAttribute('readonly', 'readonly');
// field.style.backgroundColor = '#f0f0f0';
// field.style.cursor = 'not-allowed';
```

### Add Custom Logic

```javascript
// Add after line ~90:
field.addEventListener('focus', function() {
    alert('This field is auto-filled with your session ID');
});
```

## Support

If the field isn't populating:

1. Check browser console for errors
2. Verify localStorage has the wizard data
3. Inspect the MailerLite form HTML to confirm field name
4. Try manually calling `window.populateMailerLiteSubmissionRef()`

## Production Deployment

The script is already active on your site. No additional deployment needed!

Just ensure:
- ✅ The mu-plugin file exists
- ✅ The JavaScript file exists
- ✅ WordPress is loading the script (check page source)

---

**Status:** ✅ Ready to Use
**Auto-loads:** Yes (on all pages)
**Requires Setup:** No
