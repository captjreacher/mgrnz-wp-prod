# Quick Implementation Guide - Wizard Fixes

## 🎯 What Was Fixed

1. **PDF Download** - Now generates proper PDF files (not TXT)
2. **Email Removed** - Step 5 is now a review screen (no redundant email)
3. **Quote Integration** - Quote page receives and displays blueprint data

---

## ⚡ 3-Minute Setup

### Step 1: Subscribe Page (1 min)

1. Go to: **WordPress Admin → Pages → Wizard Subscribe Page**
2. Add **HTML Block** anywhere on the page
3. Copy/paste from: `subscribe-page-download-script.html`
4. **Save**

### Step 2: Quote Page (1 min)

1. Go to: **WordPress Admin → Pages → Quote My Workflow**
2. Add **HTML Block** at the **TOP** of the page
3. Copy/paste from: `quote-page-blueprint-data-script.html`
4. **Save**

### Step 3: MailerLite Field (1 min)

1. Go to: **MailerLite → Subscribers → Fields**
2. Add this custom field (type: Text):
   - `submission_ref` (the submission ID)
3. **Save**

---

## ✅ Test It

1. Complete the wizard at `/start-using-ai`
2. Click "Download My Blueprint" → Should download PDF
3. Click "Get a Quote" → Should show your workflow data
4. Submit quote form → Check MailerLite for wizard data

---

## 📋 What Changed in the Wizard

**Step 5 Before:**
```
Email: [optional input field]
```

**Step 5 Now:**
```
Review Your Information:
✓ Goal: [your goal]
✓ Workflow: [your workflow]
✓ Tools: [your tools]
✓ Pain Points: [your pain points]

[Generate My Blueprint]
```

---

## 🔧 Files Modified

- ✅ `themes/saaslauncher/templates/ai-workflow-wizard-wp.php` (already updated)
- ✅ `subscribe-page-download-script.html` (paste to subscribe page)
- ✅ `quote-page-blueprint-data-script.html` (paste to quote page)
- ✅ `WPCODE-WIZARD-JAVASCRIPT-UPDATED.js` (optional WPCode update)

---

## 💡 How Quote Data Works

```
User completes wizard
    ↓
Data saved to localStorage
    ↓
User clicks "Get a Quote"
    ↓
Quote page loads
    ↓
Script finds wizard data
    ↓
Displays summary box
    ↓
Adds hidden fields to form
    ↓
User submits form
    ↓
MailerLite receives:
  - Name, Email, Phone (visible fields)
  - Submission Ref (e.g., WIZ-ABC123) - use this to look up the full submission in WordPress
```

---

## 🎨 What Users See on Quote Page

**With Wizard Data:**
```
┌─────────────────────────────────────┐
│   Your AI Workflow Submission       │
│                                     │
│        ┌─────────────┐              │
│        │ WIZ-ABC123  │              │
│        └─────────────┘              │
│                                     │
│   Reference this ID when            │
│   requesting your quote             │
└─────────────────────────────────────┘

[Quote Form Below]
```

**Without Wizard Data:**
```
┌─────────────────────────────────────┐
│   Haven't completed the wizard yet? │
│                                     │
│   For the most accurate quote,      │
│   complete our AI Workflow Wizard   │
│                                     │
│   [Start the Wizard]                │
└─────────────────────────────────────┘

[Quote Form Below]
```

---

## 🐛 Troubleshooting

**PDF downloads as TXT:**
- Clear browser cache
- Try different browser
- Check console for errors (F12)

**Quote page doesn't show data:**
- Complete wizard first
- Check localStorage: `localStorage.getItem('mgrnz_wizard_data')`
- Ensure script is in HTML block (not text)

**MailerLite doesn't receive wizard data:**
- Verify custom fields exist in MailerLite
- Field names must match exactly
- Check browser console for "Wizard data added" message

---

## 📞 Need Help?

Check the full documentation: `WIZARD-FIXES-COMPLETE.md`

---

**Status:** ✅ Ready to implement  
**Time Required:** 3 minutes  
**Difficulty:** Easy (copy/paste)
