# Subscription Modal Fix

## Issue
The blueprint download "Subscribe" button was triggering the MailerLite waitlist modal instead of the proper subscription modal that collects name and email for blueprint download.

## Changes Made

### 1. Updated WPCode JavaScript (`WPCODE-WIZARD-JAVASCRIPT-UPDATED.js`)

**Before:**
```javascript
if (subscribeBtn) {
    subscribeBtn.addEventListener("click", function (e) {
        e.preventDefault();
        try {
            if (window.ml) {
                window.ml("show", "qyrDmy", true);
            }
        } catch (err) {
            console.warn("MailerLite not available:", err);
        }
        showDecision("Nice. I'll keep you in the loop with practical AI updates.");
    });
}
```

**After:**
```javascript
if (subscribeBtn) {
    subscribeBtn.addEventListener("click", function (e) {
        e.preventDefault();
        // Use the subscription modal instead of MailerLite waitlist
        if (window.mgrnzSubscriptionModal) {
            window.mgrnzSubscriptionModal.show();
        } else {
            console.warn("Subscription modal not available");
            // Fallback to MailerLite if subscription modal not loaded
            try {
                if (window.ml) {
                    window.ml("show", "qyrDmy", true);
                }
            } catch (err) {
                console.warn("MailerLite not available:", err);
            }
        }
        showDecision("Nice. I'll keep you in the loop with practical AI updates.");
    });
}
```

### 2. Updated Wizard Controller (`themes/mgrnz-theme/assets/js/wizard-controller.js`)

**Before:**
```javascript
if (subscribeBtn) {
    subscribeBtn.addEventListener("click", function (e) {
        e.preventDefault();
        // If MailerLite popup is available, trigger it
        try {
            if (window.ml) {
                window.ml("show", "qyrDmy", true);
            }
        } catch (err) {
            console.warn("MailerLite not available:", err);
        }
    });
}
```

**After:**
```javascript
if (subscribeBtn) {
    subscribeBtn.addEventListener("click", function (e) {
        e.preventDefault();
        // Use the subscription modal instead of MailerLite waitlist
        if (window.mgrnzSubscriptionModal) {
            window.mgrnzSubscriptionModal.show();
        } else {
            console.warn("Subscription modal not available");
            // Fallback to MailerLite if subscription modal not loaded
            try {
                if (window.ml) {
                    window.ml("show", "qyrDmy", true);
                }
            } catch (err) {
                console.warn("MailerLite not available:", err);
            }
        }
    });
}
```

## How It Works

1. **Subscription Modal HTML** is already present in `page-start-using-ai.php` (lines 197-227)
2. **Subscription Modal JavaScript** is loaded from `themes/mgrnz-theme/assets/js/subscription-modal.js`
3. **Modal Initialization** happens on page load and is stored globally as `window.mgrnzSubscriptionModal`
4. **Subscribe Button Click** now triggers `window.mgrnzSubscriptionModal.show()` instead of the MailerLite waitlist
5. **Fallback** to MailerLite is still available if the subscription modal fails to load

## Expected Behavior

When users click the "Subscribe for AI Tips" or "Download Blueprint" button:

1. The subscription modal appears with fields for:
   - Name (required)
   - Email (required)
   - Privacy notice

2. Upon form submission:
   - Data is sent to `/wp-json/mgrnz/v1/subscribe-blueprint`
   - Blueprint download is triggered
   - Success message is displayed
   - User receives email with blueprint

## Testing

To test the fix:

1. Complete the AI workflow wizard
2. Wait for the blueprint to be displayed
3. Click the "Subscribe for AI Tips" button
4. Verify the subscription modal appears (not the MailerLite waitlist)
5. Fill in name and email
6. Submit the form
7. Verify blueprint download starts
8. Check that success message appears

## Files Modified

- `WPCODE-WIZARD-JAVASCRIPT-UPDATED.js` - Updated subscribe button handler
- `themes/mgrnz-theme/assets/js/wizard-controller.js` - Updated subscribe button handler

## Files Already in Place (No Changes Needed)

- `themes/mgrnz-theme/page-start-using-ai.php` - Contains modal HTML and initialization
- `themes/mgrnz-theme/assets/js/subscription-modal.js` - Modal component
- `themes/mgrnz-theme/assets/css/subscription-modal.css` - Modal styles
- `themes/mgrnz-theme/assets/js/blueprint-subscription-integration.js` - Integration logic

## Notes

- The subscription modal is the same one used in the header for blueprint downloads
- It properly integrates with the backend API for lead capture
- The modal includes proper form validation and error handling
- Mobile responsive design is included
