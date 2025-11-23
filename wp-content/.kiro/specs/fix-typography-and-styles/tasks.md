# Implementation Plan

- [x] 1. Create WordPress theme foundation files




- [x] 1.1 Create functions.php with theme setup and style enqueuing


  - Create functions.php in themes/mgrnz-theme/ root
  - Add mgrnz_theme_setup() function with add_theme_support() calls
  - Add post-thumbnails, title-tag, html5, and custom-logo support
  - Register custom image sizes (mgrnz-featured, mgrnz-thumbnail, mgrnz-medium)
  - Hook setup function to after_setup_theme action
  - _Requirements: 1.1, 1.5, 1.6, 7.1, 7.2_

- [x] 1.2 Create style enqueuing function in functions.php

  - Add mgrnz_enqueue_styles() function
  - Enqueue style.css as primary stylesheet
  - Enqueue main.css with dependency on style.css
  - Enqueue custom.css with dependency on main.css
  - Hook enqueue function to wp_enqueue_scripts action
  - _Requirements: 1.3, 1.4_

- [x] 1.3 Create root style.css with WordPress theme header


  - Create style.css in themes/mgrnz-theme/ root
  - Add WordPress theme header (Theme Name, Author, Description, Version)
  - Add theme metadata and tags
  - _Requirements: 1.2_

- [x] 2. Implement typography system with CSS custom properties




- [x] 2.1 Add CSS custom properties to style.css


  - Define --font-sans and --font-mono variables
  - Define --text-xs through --text-4xl size variables
  - Define --font-normal through --font-bold weight variables
  - Define --leading-tight, --leading-normal, --leading-relaxed variables
  - Define --color-bg, --color-text, --color-text-muted, --color-accent, --color-border variables
  - _Requirements: 2.1, 4.1, 4.2, 4.3, 4.4, 4.5_

- [x] 2.2 Add typography reset overrides to style.css


  - Override Tailwind reset for h1-h6 elements with font-size
  - Override Tailwind reset for h1-h6 elements with font-weight
  - Override Tailwind reset for h1-h6 elements with line-height
  - Override Tailwind reset for h1-h6 elements with color
  - Add body font-family and line-height rules
  - Add paragraph margin and line-height rules
  - Add link color and hover state rules
  - _Requirements: 2.2, 2.3, 2.4, 2.5, 4.6_

- [x] 2.3 Add image styling to style.css


  - Add .post-thumbnail class with responsive sizing
  - Add .entry-content img rules with border-radius and margins
  - Override Tailwind image display rules if needed
  - Add figure element styling
  - _Requirements: 7.2, 7.3, 7.4, 7.5_

- [x] 3. Reorganize and enhance custom.css





- [x] 3.1 Add section comments to custom.css


  - Add "Layout Components" section header
  - Add "Header Components" section header
  - Add "Content Components" section header
  - Add "Sidebar Components" section header
  - Add "Footer Components" section header
  - Add "Utility Classes" section header
  - _Requirements: 6.1, 6.2, 6.3_


- [x] 3.2 Create CSS classes for header components


  - Create .site-header class (move styles from inline)
  - Create .header-inner class (move styles from inline)
  - Create .site-title class (move styles from inline)
  - Ensure classes use CSS custom properties where applicable
  - _Requirements: 3.1, 4.6_




- [x] 3.3 Create CSS classes for content components



  - Create .mgrnz-main class (move styles from inline)
  - Create .post-card class (move styles from inline)
  - Create .post-title class (move styles from inline)
  - Create .post-meta class (move styles from inline)
  - Create .entry-content class (move styles from inline)
  - Create .post-header class (move styles from inline)
  - Create .post-footer class (move styles from inline)
  - Create .post-navigation class (move styles from inline)
  - Create .post-thumbnail class (move styles from inline)
  - _Requirements: 3.3, 4.6_






- [x] 3.4 Create CSS classes for sidebar components


  - Create .sidebar class (move styles from inline)
  - Create .sidebar-placeholder class (move styles from inline)
  - _Requirements: 3.5, 4.6_




- [x] 3.5 Create CSS classes for footer components


  - Create .site-footer class (move styles from inline)
  - Create .footer-text class (move styles from inline)
  - Create .footer-link class (move styles from inline)
  - _Requirements: 3.2, 4.6_

- [x] 4. Remove inline styles from template files




- [x] 4.1 Update header.php to use CSS classes


  - Replace inline styles on .site-header with class
  - Replace inline styles on .container with class
  - Replace inline styles on .site-title with class
  - Fix unclosed div tag before closing header
  - _Requirements: 3.1_

- [x] 4.2 Update footer.php to use CSS classes


  - Replace inline styles on .site-footer with class
  - Replace inline styles on footer paragraph with class
  - Replace inline styles on footer link with class
  - _Requirements: 3.2_

- [x] 4.3 Update index.php to use CSS classes


  - Replace inline styles on main element with class
  - Replace inline styles on article elements with class
  - Replace inline styles on h2 elements with class
  - Replace inline styles on post-meta div with class
  - Replace inline styles on entry-excerpt div with class
  - _Requirements: 3.3_

- [x] 4.4 Update single.php to use CSS classes


  - Replace inline styles on main element with class
  - Replace inline styles on article element with class
  - Replace inline styles on header element with class
  - Replace inline styles on h1 element with class
  - Replace inline styles on post-meta div with class
  - Replace inline styles on figure element with class
  - Remove inline style attribute from the_post_thumbnail()
  - Replace inline styles on entry-content div with class
  - Replace inline styles on footer element with class
  - Replace inline styles on post-nav element with class
  - Replace inline styles on comments section with class
  - _Requirements: 3.4_

- [x] 4.5 Update sidebar.php to use CSS classes


  - Replace inline styles on aside element with class
  - Replace inline styles on h3 element with class
  - _Requirements: 3.5_

- [x] 5. Document and test the implementation






- [x] 5.1 Create THEME-STYLES.md documentation

  - Document CSS file purposes and load order
  - Document typography system and custom properties
  - Document how to add new styles
  - Document WPCode snippet guidelines
  - _Requirements: 6.4_

- [x] 5.2 Test typography rendering


  - Verify h1-h6 display with distinct sizes on single post page
  - Verify headings have proper font-weight
  - Verify body text has readable line-height
  - Verify link colors and hover states work
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

- [x] 5.3 Test image rendering


  - Verify featured images display on single posts
  - Verify images in post content display correctly
  - Verify image responsive sizing works
  - Verify image border-radius and spacing applied
  - _Requirements: 7.1, 7.2, 7.3, 7.4_

- [x] 5.4 Test layout and styling across pages


  - Test homepage layout and styling
  - Test single post page layout and styling
  - Test header navigation styling
  - Test sidebar styling
  - Test footer styling
  - Test mobile responsive behavior
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [x] 5.5 Check for WPCode plugin conflicts











  - Document any WPCode snippets that inject CSS
  - Test if theme styles properly override snippet styles
  - Provide instructions for resolving conflicts if found
  - _Requirements: 5.1, 5.2, 5.3_
