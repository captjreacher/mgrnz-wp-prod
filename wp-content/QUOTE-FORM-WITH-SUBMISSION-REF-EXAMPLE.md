# Quote Form with Submission Reference - Visual Example

## What Users Will See

When someone completes the wizard and goes to the quote page, the form will look like this:

```
┌─────────────────────────────────────────────────┐
│                                                 │
│  BOOM! LOVE YOUR WORK(FLOW)!                   │
│                                                 │
│  How much would my workflow cost?              │
│                                                 │
│  Complete the details below...                 │
│                                                 │
│  ┌───────────────────────────────────────────┐ │
│  │ Submission Reference                      │ │
│  │ ┌───────────────────────────────────────┐ │ │
│  │ │         WIZ-L9X2K4                    │ │ │  ← Read-only, orange border
│  │ └───────────────────────────────────────┘ │ │
│  └───────────────────────────────────────────┘ │
│                                                 │
│  ┌───────────────────────────────────────────┐ │
│  │ Email                                     │ │
│  └───────────────────────────────────────────┘ │
│                                                 │
│  ┌───────────────────────────────────────────┐ │
│  │ Name                                      │ │
│  └───────────────────────────────────────────┘ │
│                                                 │
│  ┌───────────────────────────────────────────┐ │
│  │ Last name                                 │ │
│  └───────────────────────────────────────────┘ │
│                                                 │
│  ┌───────────────────────────────────────────┐ │
│  │ Company                                   │ │
│  └───────────────────────────────────────────┘ │
│                                                 │
│  ┌───────────────────────────────────────────┐ │
│  │ Phone (optional)                          │ │
│  └───────────────────────────────────────────┘ │
│                                                 │
│  ┌───────────────────────────────────────────┐ │
│  │      Quote My Workflow                    │ │
│  └───────────────────────────────────────────┘ │
│                                                 │
└─────────────────────────────────────────────────┘
```

## Key Features

### 1. Submission Reference Field
- **Position**: First field in the form
- **Label**: "Submission Reference"
- **Style**: 
  - Orange border (#ff4f00)
  - Light gray background (#f5f5f5)
  - Monospace font
  - Centered text
  - Read-only (user can't edit)
  - Larger font size for visibility

### 2. Hidden Field
- A hidden input field is also added with the same value
- This is what actually gets sent to MailerLite
- Format: `<input type="hidden" name="fields[submission_ref]" value="WIZ-L9X2K4">`

## Benefits

### For Users:
- ✅ Clear visual confirmation of their submission ID
- ✅ Can reference it in their email/notes
- ✅ Professional appearance
- ✅ Can't accidentally change it (read-only)

### For You:
- ✅ Submission ref is prominently displayed
- ✅ Users are more likely to mention it
- ✅ Easy to match quotes to wizard submissions
- ✅ Professional, polished experience

## Technical Details

### CSS Styling:
```css
.submission-ref-field {
  width: 100%;
  padding: 1rem 1.25rem;
  background: #f5f5f5;
  border: 2px solid #ff4f00;
  border-radius: 12px;
  color: #ff4f00;
  font-family: monospace;
  font-size: 1.25rem;
  font-weight: 700;
  text-align: center;
  cursor: not-allowed;
}
```

### HTML Structure:
```html
<form>
  <!-- Submission Reference (added by script) -->
  <div style="margin-bottom: 1.25rem;">
    <label style="...">Submission Reference</label>
    <input type="text" value="WIZ-L9X2K4" readonly style="..." />
  </div>
  
  <!-- Regular form fields -->
  <input type="email" name="fields[email]" placeholder="Email" />
  <input type="text" name="fields[name]" placeholder="Name" />
  <!-- ... -->
  
  <!-- Hidden field for MailerLite -->
  <input type="hidden" name="fields[submission_ref]" value="WIZ-L9X2K4" />
  
  <button type="submit">Quote My Workflow</button>
</form>
```

## User Experience Flow

1. **User completes wizard** → Gets submission ref `WIZ-L9X2K4`
2. **Clicks "Get a Quote"** → Goes to quote page
3. **Sees submission ref box** at top of page (from earlier script)
4. **Scrolls to form** → Sees submission ref as first field in form
5. **Fills out form** → Can see their ref the whole time
6. **Submits form** → MailerLite receives the ref automatically

## What You Receive in MailerLite

```
New Subscriber:
- Email: john@example.com
- Name: John
- Last Name: Doe
- Company: Acme Corp
- Phone: 555-1234
- Submission Ref: WIZ-L9X2K4  ← Use this to look up full details
```

## Testing

1. Complete wizard at `/start-using-ai`
2. Click "Get a Quote for this Workflow"
3. Verify you see:
   - Submission ref box at top of page
   - Submission ref as first field in form (read-only, orange border)
4. Try to edit the submission ref field → Should not be editable
5. Fill out rest of form and submit
6. Check MailerLite → Should have submission_ref field populated

---

**Status**: ✅ Ready to implement  
**Files Updated**: 
- `quote-page-blueprint-data-script.html`
- `quote-page-COMPLETE-WITH-SUBMISSION-REF.html`
