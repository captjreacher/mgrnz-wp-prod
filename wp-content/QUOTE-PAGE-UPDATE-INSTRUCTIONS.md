# Quote Page Update Instructions

## What Changed

The quote page now displays the wizard submission reference ID and automatically includes it in the quote form.

## Option 1: Use the Complete File (Easiest)

1. Go to WordPress Admin → Pages → Quote My Workflow
2. Switch to HTML/Code view
3. Replace ALL content with: `quote-page-COMPLETE-WITH-SUBMISSION-REF.html`
4. Update these placeholders:
   - `YOUR-IMAGE-URL` → Your explosion image URL
   - `YOUR-MAILERLITE-ACTION-URL` → Your MailerLite form action
   - Social media links (Facebook, LinkedIn, GitHub)
5. Save

## Option 2: Add Script to Existing Page

If you want to keep your existing quote page layout:

1. Go to WordPress Admin → Pages → Quote My Workflow
2. Add an **HTML Block** at the very TOP of the page
3. Copy/paste from: `quote-page-blueprint-data-script.html`
4. Save

This will:
- Display the submission reference at the top
- Automatically add it as a hidden field to your form

## What Users See

When they arrive from the wizard:

```
┌─────────────────────────────┐
│ Your AI Workflow Submission │
│                             │
│      ┌─────────────┐        │
│      │ WIZ-L9X2K4  │        │
│      └─────────────┘        │
│                             │
│ Reference this ID when      │
│ requesting your quote       │
└─────────────────────────────┘

[Quote Form Below]
```

## What You Receive

In MailerLite, you'll get:
- Name
- Email
- Phone
- Company
- **submission_ref**: WIZ-L9X2K4

Use the submission_ref to look up the full workflow details in WordPress Admin → AI Workflow Submissions.

## MailerLite Setup

Add this custom field:
1. Go to MailerLite → Subscribers → Fields
2. Add new field:
   - Name: `submission_ref`
   - Type: Text
3. Save

## Testing

1. Complete the wizard at `/start-using-ai`
2. Click "Get a Quote for this Workflow"
3. Verify submission reference appears at top
4. Submit the quote form
5. Check MailerLite for the submission_ref field

---

**Files:**
- `quote-page-COMPLETE-WITH-SUBMISSION-REF.html` - Complete page with script
- `quote-page-blueprint-data-script.html` - Just the script to add to existing page
