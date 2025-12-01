# How to Find the "Quote Requested" Column

## Location

The "Quote Requested" column is in the **AI Submissions** admin page, NOT a separate table.

## Steps to View:

1. **Go to WordPress Admin**
   ```
   http://mgrnz.local/wp-admin/
   ```

2. **Click on "AI Submissions" in the left sidebar**
   - Look for the menu item with a chart/analytics icon
   - It might be labeled "AI Submissions" or "AI Workflow Submissions"

3. **You should see a table with these columns:**
   - ☐ (checkbox)
   - Title
   - Ref ID
   - Submission Date
   - Email
   - Goal
   - **Quote Requested** ← NEW COLUMN
   - Email Status

## If You Don't See It:

### Option 1: Screen Options
1. Click **"Screen Options"** at the top right of the page
2. Make sure **"Quote Requested"** is checked
3. Click "Apply"

### Option 2: Clear Browser Cache
1. Hard refresh the page: `Ctrl+F5` (Windows) or `Cmd+Shift+R` (Mac)
2. Or clear your browser cache completely

### Option 3: Check if AI Submissions Exist
The menu item only appears if you have AI submissions. To create one:
1. Go to `http://mgrnz.local/start-using-ai/`
2. Complete the wizard
3. Generate a blueprint
4. Go back to WordPress admin
5. Check for "AI Submissions" menu item

## What the Column Shows:

- **✓ Yes** (green) = Quote was requested via MailerLite form
- **—** (gray) = No quote requested yet

## To See Full Quote Details:

1. Click on any submission that shows "✓ Yes"
2. Scroll down to see **"📋 Quote Request Details"** section
3. This shows:
   - Requested At
   - Contact Name
   - Contact Email
   - Company
   - Message

## Direct URL:

Try going directly to:
```
http://mgrnz.local/wp-admin/edit.php?post_type=ai_workflow_sub
```

## Troubleshooting:

### "I don't see AI Submissions menu"

**Cause:** No submissions exist yet

**Solution:** Complete the wizard once to create a submission

### "I see AI Submissions but no Quote Requested column"

**Cause:** Code not loaded or cache issue

**Solutions:**
1. Hard refresh: `Ctrl+F5`
2. Check Screen Options (top right)
3. Deactivate and reactivate the plugin
4. Check if file was saved: `wp-content/mu-plugins/includes/class-submission-cpt.php`

### "Column shows but always says —"

**Cause:** No quotes have been requested yet, or webhook not configured

**Solution:**
1. Configure MailerLite webhook (see CROSS-POPULATION-COMPLETE.md)
2. Test by submitting the quote form
3. Check webhook is firing (check error logs)

## Testing:

To test the full flow:

1. **Complete wizard** → Creates AI submission
2. **Visit** `/quote-my-workflow/`
3. **Check** submission_ref field is filled
4. **Submit** the form
5. **Go to** AI Submissions in admin
6. **Look for** "Quote Requested: ✓ Yes"

---

**Still can't find it?** 

Check the WordPress error log:
```
tail -f wp-content/debug.log
```

Or send a screenshot of your AI Submissions page.
