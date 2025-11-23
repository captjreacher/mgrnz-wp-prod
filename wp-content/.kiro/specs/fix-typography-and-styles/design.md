# Design Document

## Overview

This design addresses the typography and style issues in the mgrnz-theme by establishing proper WordPress theme structure, implementing a consistent typography system, removing inline styles, and resolving Tailwind CSS reset conflicts. The solution maintains the existing dark theme aesthetic while making styles maintainable and following WordPress best practices.

## Architecture

### File Structure

```
themes/mgrnz-theme/
├── style.css              (NEW - WordPress theme header + typography overrides)
├── functions.php          (NEW - Theme setup and style enqueuing)
├── main.css              (EXISTING - Tailwind compiled CSS)
├── assets/
│   └── css/
│       ├── style.css     (EXISTING - Tailwind compiled CSS - RENAME to tailwind.css)
│       └── custom.css    (EXISTING - Custom styles - ENHANCE)
├── header.php            (MODIFY - Remove inline styles)
├── footer.php            (MODIFY - Remove inline styles)
├── index.php             (MODIFY - Remove inline styles)
├── single.php            (MODIFY - Remove inline styles)
└── sidebar.php           (MODIFY - Remove inline styles)
```

### CSS Load Order

1. **style.css** (theme root) - WordPress theme header + typography base
2. **main.css** - Tailwind CSS compiled output
3. **assets/css/custom.css** - Custom component and layout styles

## Components and Interfaces

### 1. functions.php

**Purpose:** Theme setup, style enqueuing, image support, and WordPress feature support

**Key Functions:**
- `mgrnz_theme_setup()` - Register theme support features (post-thumbnails, title-tag, etc.)
- `mgrnz_enqueue_styles()` - Properly enqueue all CSS files
- `mgrnz_register_menus()` - Register navigation menus
- `mgrnz_register_sidebars()` - Register widget areas
- `mgrnz_register_image_sizes()` - Register custom image sizes

**Theme Support Registration:**
```php
add_theme_support('post-thumbnails');
add_theme_support('title-tag');
add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
add_theme_support('custom-logo');
```

**Image Size Registration:**
```php
add_image_size('mgrnz-featured', 1200, 600, true);  // Featured images
add_image_size('mgrnz-thumbnail', 400, 300, true);  // Thumbnails
add_image_size('mgrnz-medium', 800, 600, false);    // Medium size
```

**Style Enqueuing Strategy:**
```php
wp_enqueue_style('mgrnz-style', get_stylesheet_uri(), array(), '1.0.0');
wp_enqueue_style('mgrnz-tailwind', get_template_directory_uri() . '/main.css', array('mgrnz-style'), '1.0.0');
wp_enqueue_style('mgrnz-custom', get_template_directory_uri() . '/assets/css/custom.css', array('mgrnz-tailwind'), '1.0.0');
```

### 2. style.css (Theme Root)

**Purpose:** WordPress theme identification + typography system foundation

**Sections:**
1. WordPress theme header (required metadata)
2. CSS Custom Properties (design tokens)
3. Typography reset overrides (fix Tailwind stripping)
4. Base element styles (body, headings, links, paragraphs)

**Typography System:**
```css
:root {
  /* Font Families */
  --font-sans: ui-sans-serif, system-ui, sans-serif;
  --font-mono: ui-monospace, 'Courier New', monospace;
  
  /* Font Sizes */
  --text-xs: 0.75rem;
  --text-sm: 0.875rem;
  --text-base: 1rem;
  --text-lg: 1.125rem;
  --text-xl: 1.25rem;
  --text-2xl: 1.5rem;
  --text-3xl: 1.875rem;
  --text-4xl: 2.25rem;
  
  /* Font Weights */
  --font-normal: 400;
  --font-medium: 500;
  --font-semibold: 600;
  --font-bold: 700;
  
  /* Line Heights */
  --leading-tight: 1.25;
  --leading-normal: 1.5;
  --leading-relaxed: 1.7;
  
  /* Colors */
  --color-bg: #000;
  --color-text: #e7e7e7;
  --color-text-muted: #bbb;
  --color-accent: #ff4f00;
  --color-border: #222;
}
```

### 3. custom.css Enhancement

**Purpose:** Component-specific styles and layout utilities

**Reorganization:**
1. Layout Components (container, grid, sidebar)
2. Header Components (site-header, nav, brand)
3. Content Components (article, entry-content, post-meta)
4. Sidebar Components (widget areas, cards)
5. Footer Components
6. Utility Classes

**New Classes to Add:**
- `.site-title` - Replace inline header styles
- `.post-title` - Replace inline h2 styles
- `.post-meta` - Replace inline meta styles
- `.entry-content` - Replace inline content styles
- `.post-navigation` - Replace inline nav styles

## Data Models

### CSS Class Naming Convention

**Pattern:** `.{component}-{element}--{modifier}`

**Examples:**
- `.site-header` - Main header component
- `.site-header__brand` - Brand element within header
- `.post-card` - Post listing card
- `.post-card__title` - Title within post card
- `.post-card--featured` - Featured post variant

### Template Structure Mapping

**Current (Inline Styles):**
```php
<header style="background:#000; color:#fff; padding:1rem 0;">
```

**Target (CSS Classes):**
```php
<header class="site-header">
```

## Error Handling

### Tailwind Reset Conflicts

**Problem:** Tailwind's preflight resets strip heading sizes and weights

**Solution:** Override in style.css after Tailwind loads
```css
/* Typography Reset Overrides */
h1, h2, h3, h4, h5, h6 {
  font-weight: var(--font-bold);
  line-height: var(--leading-tight);
  color: var(--color-text);
}

h1 { font-size: var(--text-4xl); }
h2 { font-size: var(--text-3xl); }
h3 { font-size: var(--text-2xl); }
h4 { font-size: var(--text-xl); }
h5 { font-size: var(--text-lg); }
h6 { font-size: var(--text-base); }
```

### WPCode Plugin Conflicts

**Detection Strategy:**
1. Check WordPress admin → WPCode → Snippets
2. Look for snippets with CSS in header/footer
3. Document snippet IDs and purposes

**Resolution Options:**
1. Disable conflicting snippets
2. Increase theme CSS specificity
3. Use `!important` sparingly for critical overrides
4. Move snippet styles into theme CSS

### Missing Header Closing Tag

**Issue:** header.php has unclosed `<div class="container">` tag

**Fix:** Add closing `</div>` before `</header>`

### Images Not Rendering

**Root Cause:** Theme lacks `add_theme_support('post-thumbnails')` registration

**Symptoms:**
1. Featured images don't display on posts
2. `the_post_thumbnail()` returns nothing
3. Media library images may not show in posts

**Solution:**
1. Add `add_theme_support('post-thumbnails')` in functions.php
2. Register custom image sizes for theme needs
3. Add proper image styling in CSS (remove inline styles)
4. Ensure Tailwind's `img { display: block; }` doesn't conflict

**Image CSS Strategy:**
```css
/* Override Tailwind's aggressive image reset */
.entry-content img {
  display: block;
  max-width: 100%;
  height: auto;
  border-radius: 12px;
  margin: 1rem 0;
}

.post-thumbnail {
  width: 100%;
  height: auto;
  border-radius: 12px;
  display: block;
  object-fit: cover;
}
```

## Testing Strategy

### Visual Regression Testing

**Manual Checks:**
1. Homepage layout and typography
2. Single post page typography
3. Header navigation styling
4. Sidebar widget styling
5. Footer styling
6. Mobile responsive behavior

### Typography Verification

**Test Cases:**
1. Verify h1-h6 have distinct sizes
2. Verify headings have proper font-weight
3. Verify body text has readable line-height
4. Verify link colors and hover states
5. Verify code/pre elements use monospace font

### Browser Testing

**Targets:**
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile Safari (iOS)
- Chrome Mobile (Android)

### WordPress Integration Testing

**Checks:**
1. Theme activates without errors
2. Styles load in correct order
3. No console errors
4. No 404s for CSS files
5. Theme appears in Appearance → Themes
6. Theme screenshot displays correctly

## Implementation Notes

### Phase 1: Foundation
1. Create functions.php with style enqueuing
2. Create style.css with WordPress header
3. Fix header.php closing tag

### Phase 2: Typography System
1. Add CSS custom properties to style.css
2. Add typography reset overrides
3. Test heading rendering

### Phase 3: Remove Inline Styles
1. Create CSS classes in custom.css
2. Replace inline styles in templates
3. Test each template after changes

### Phase 4: Cleanup and Optimization
1. Remove duplicate styles
2. Organize custom.css with comments
3. Document WPCode conflicts
4. Final testing across all pages

### Backward Compatibility

**Considerations:**
- Existing Tailwind utility classes remain functional
- Custom.css existing styles preserved
- No breaking changes to template structure
- Gradual migration path for inline styles

### Performance Impact

**Expected Improvements:**
- Reduced HTML size (no inline styles)
- Better browser caching (external CSS)
- Faster page loads (cached stylesheets)

**Potential Concerns:**
- Additional HTTP request for style.css (minimal impact)
- Slightly larger CSS files (offset by caching)

## Documentation Requirements

### Code Comments

**functions.php:**
- Document each hooked function
- Explain style enqueue order
- Note dependencies between styles

**style.css:**
- Document custom property usage
- Explain Tailwind override strategy
- Note color scheme values

**custom.css:**
- Section headers for each component group
- Usage examples for complex classes
- Responsive breakpoint notes

### README Addition

Create `THEME-STYLES.md` documenting:
1. CSS file purposes and load order
2. Typography system usage
3. Custom property reference
4. How to add new styles
5. WPCode snippet guidelines
