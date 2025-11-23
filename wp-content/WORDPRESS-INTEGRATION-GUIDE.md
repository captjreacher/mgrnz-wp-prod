# WordPress Integration Guide for AI Workflow Wizard

## 🎯 Quick Start - 3 Methods

### Method 1: WPCode Snippet (Recommended)

1. Go to **WPCode → Add Snippet**
2. Choose **"Add Your Custom Code (New Snippet)"**
3. Name it: `AI Workflow Wizard`
4. Set Type: **HTML Snippet**
5. Copy the ENTIRE contents of `ai-workflow-wizard-complete.html`
6. Paste into the code editor
7. Set Location: **Page Specific** → Select your wizard page
8. Set Insert Method: **Replace Page Content** or **After Content**
9. Click **Save Snippet** and **Activate**

### Method 2: Custom HTML Block (Gutenberg)

1. Edit your page in WordPress
2. Add a **Custom HTML** block
3. Copy the ENTIRE contents of `ai-workflow-wizard-complete.html`
4. Paste into the Custom HTML block
5. Update/Publish the page

### Method 3: Page Template

1. Copy `ai-workflow-wizard-complete.html` contents
2. Create new file: `wp-content/themes/mgrnz-theme/template-wizard.php`
3. Add WordPress template header:
   ```php
   <?php
   /**
    * Template Name: AI Workflow Wizard
    */
   get_header();
   ?>
   
   <!-- Paste wizard HTML here -->
   
   <?php get_footer(); ?>
   ```
4. Create a new page and select "AI Workflow Wizard" template

## 📝 Adding Your MailerLite Form

After pasting the wizard code, find this section (search for `mlb2-33155148`):

```html
<div id="workflow-quote-form">
  <h3>Request a Quote</h3>
  <div id="mlb2-33155148">
    <!-- 
      ============================================
      PASTE YOUR MAILERLITE EMBED CODE HERE
      ============================================
    -->
    <p style="text-align: center; color: var(--color-text-muted);">
      [MailerLite form will appear here]
    </p>
  </div>
</div>
```

Replace the placeholder paragraph with your MailerLite embed code.

### Getting Your MailerLite Embed Code

1. Log into MailerLite
2. Go to **Forms** → Select your form
3. Click **Embed** or **Share**
4. Copy the **Embedded form** code
5. Paste it where indicated above

## 🎨 Customizing Colors

Find the CSS variables at the top of the `<style>` section:

```css
:root {
  --color-bg: #0f172a;        /* Page background */
  --color-card: #0b0b0b;      /* Card backgrounds */
  --color-border: #1f1f1f;    /* Border colors */
  --color-accent: #ff4f00;    /* Orange CTA color */
  --color-text: #ffffff;      /* Main text */
  --color-text-muted: #bbb;   /* Secondary text */
  --color-text-dim: #666;     /* Tertiary text */
}
```

Change these hex values to match your exact brand colors.

## 🔧 Configuration Options

### Option 1: Use Modal Instead of Scroll for Quote Form

Find this line (around line 520):

```javascript
// Button 3: Get a Quote for this Workflow
document.getElementById('btn-get-quote').addEventListener('click', () => {
  // Option 1: Scroll to MailerLite form block
  quoteFormBlock.classList.add('show');
  quoteFormBlock.scrollIntoView({ behavior: 'smooth', block: 'start' });
  
  // Option 2: Show modal (uncomment if you prefer modal over scroll)
  // quoteModal.classList.add('show');
});
```

To use modal instead:
- Comment out lines 2-3
- Uncomment line 6

### Option 2: Change Animation Speed

Find the progress animation section:

```javascript
messageIndex++;
setTimeout(showNextMessage, 2000); // Change 2000 to adjust speed (milliseconds)
```

- `1000` = 1 second (faster)
- `2000` = 2 seconds (default)
- `3000` = 3 seconds (slower)

### Option 3: Customize Blueprint Template

Find the `generateBlueprint` function:

```javascript
function generateBlueprint(data) {
  return `
    <h3>🎯 Your Goal</h3>
    <p>${data.goal}</p>
    
    <!-- Add or modify sections here -->
  `;
}
```

Edit the HTML template to match your preferred blueprint structure.

## 🚨 Common Issues & Solutions

### Issue: Styles Not Applying

**Solution:** Make sure your WordPress theme isn't overriding the styles. Add this to the top of the `<style>` section:

```css
/* Force styles to take precedence */
.wizard-container * {
  all: revert;
}
```

### Issue: MailerLite Form Not Showing

**Solution:** Check that:
1. You've pasted the MailerLite embed code correctly
2. The MailerLite script is loading (check browser console)
3. The form ID matches your MailerLite form

### Issue: Buttons Not Working

**Solution:** Check browser console for JavaScript errors. Make sure no other plugins are conflicting.

### Issue: Mobile Layout Broken

**Solution:** Ensure your theme's viewport meta tag is set:

```html
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```

## 📱 Testing Checklist

Before going live, test:

- [ ] All 5 wizard steps navigate correctly
- [ ] Form validation works on each step
- [ ] Character counters update in real-time
- [ ] Progress animation plays smoothly
- [ ] Blueprint generates with user data
- [ ] All 4 completion buttons work:
  - [ ] Edit my Workflow reloads with data
  - [ ] Download creates .txt file
  - [ ] Get a Quote scrolls to form
  - [ ] Go Back returns to blueprint
- [ ] MailerLite form appears only on button click
- [ ] Mobile responsive (test on phone)
- [ ] No console errors

## 🎯 WordPress-Specific Tips

### Tip 1: Disable Theme Styles on Wizard Page

Add this to your theme's `functions.php`:

```php
function disable_theme_styles_on_wizard() {
  if (is_page('your-wizard-page-slug')) {
    // Dequeue theme styles that might conflict
    wp_dequeue_style('theme-style');
  }
}
add_action('wp_enqueue_scripts', 'disable_theme_styles_on_wizard', 100);
```

### Tip 2: Add Custom Body Class

```php
function wizard_body_class($classes) {
  if (is_page('your-wizard-page-slug')) {
    $classes[] = 'wizard-page';
  }
  return $classes;
}
add_filter('body_class', 'wizard_body_class');
```

### Tip 3: Track Wizard Completions

Add this to the form submission section:

```javascript
// After collecting wizardData
if (typeof gtag !== 'undefined') {
  gtag('event', 'wizard_completed', {
    'event_category': 'AI Workflow',
    'event_label': 'Wizard Completion'
  });
}
```

## 🔗 Integration with Your Existing System

If you want to integrate with your existing WordPress backend:

### Send Data to WordPress REST API

Replace the form submission section with:

```javascript
form.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  if (!validateCurrentStep()) return;
  
  wizardData = {
    goal: document.getElementById('goal').value.trim(),
    workflow: document.getElementById('workflow').value.trim(),
    tools: document.getElementById('tools').value.trim(),
    pain_points: document.getElementById('pain_points').value.trim(),
    email: document.getElementById('email').value.trim()
  };
  
  // Send to your WordPress API
  try {
    const response = await fetch('/wp-json/mgrnz/v1/ai-workflow', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(wizardData)
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Use real blueprint from API
      blueprintContent.innerHTML = data.blueprint;
    }
  } catch (error) {
    console.error('API Error:', error);
    // Fallback to local blueprint generation
  }
  
  // Continue with animation...
  form.style.transition = 'opacity 0.3s ease';
  form.style.opacity = '0';
  setTimeout(() => {
    form.style.display = 'none';
    showProgressAnimation();
  }, 300);
});
```

## 📊 Analytics Integration

### Google Analytics 4

Add after form submission:

```javascript
if (typeof gtag !== 'undefined') {
  gtag('event', 'generate_lead', {
    'currency': 'USD',
    'value': 0
  });
}
```

### Facebook Pixel

```javascript
if (typeof fbq !== 'undefined') {
  fbq('track', 'Lead');
}
```

## 🎉 You're Ready!

The wizard is now fully integrated with WordPress. Just add your MailerLite form code and you're good to go!

## 📞 Need Help?

If you encounter issues:
1. Check browser console for errors
2. Verify MailerLite embed code is correct
3. Test in incognito mode to rule out caching
4. Check for plugin conflicts by disabling other plugins temporarily
