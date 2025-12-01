# MailerLite Webhook Quick Setup

## 🎯 The Missing Piece

Your webhook handler is deployed and working, but **MailerLite doesn't know to send webhooks yet!**

---

## ⚡ Quick Setup (2 minutes)

### Step 1: Go to MailerLite
https://dashboard.mailerlite.com/integrations/webhooks

### Step 2: Add Webhook
Click **"Add Webhook"** or **"Create Webhook"**

### Step 3: Configure
```
URL: https://mgrnz.com/wp-json/mgrnz/v1/mailerlite-webhook
Event: Subscriber created
```

### Step 4: Save
Click **Save** or **Create**

---

## ✅ Test It

### 1. Submit Quote Form
- Go to: https://mgrnz.com/quote-my-workflow
- Fill out the form
- Submit

### 2. Check WordPress Admin
- Go to: AI Submissions
- Find the submission
- Check "Quote Requested" column
- Should show: ✅ Yes

### 3. Check Debug Log (Optional)
Look for: `[MailerLite Webhook] ✅ Updated AI submission`

---

## 🔍 Troubleshooting

### Webhook Not Firing?

**Check MailerLite Webhook Logs:**
1. Go to MailerLite → Integrations → Webhooks
2. Click on your webhook
3. View delivery logs
4. Look for failed attempts

**Common Issues:**
- Wrong URL (must be exact)
- Wrong event type (use "Subscriber created")
- Webhook disabled
- Form not connected to webhook

### Still Not Working?

**Test the endpoint directly:**
```
https://mgrnz.com/wp-json/mgrnz/v1/mailerlite-webhook
```

Should return: `{"code":"rest_no_route","message":"No route was found..."}`

This is GOOD - it means the endpoint exists but needs POST data.

---

## 📋 What Happens When It Works

1. User submits quote form on your site
2. MailerLite receives the form data
3. MailerLite sends webhook to your WordPress
4. WordPress finds the AI submission by `submission_ref`
5. WordPress updates the submission with:
   - Quote Requested: Yes
   - Contact Name
   - Contact Email
   - Company
   - Message
   - Timestamp

---

## 🎉 Success Indicators

- ✅ "Quote Requested" column shows "Yes"
- ✅ Contact info appears in submission details
- ✅ Debug log shows webhook received
- ✅ MailerLite webhook logs show 200 OK

---

**That's it!** Once the webhook is configured in MailerLite, everything will work automatically.
