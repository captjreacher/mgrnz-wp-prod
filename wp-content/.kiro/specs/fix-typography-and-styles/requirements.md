# Requirements Document

## Introduction

This specification addresses critical style and typography issues in the mgrnz-theme WordPress theme. The theme currently lacks proper WordPress structure, has Tailwind CSS reset conflicts, uses excessive inline styles, and has no consistent typography system. These issues prevent proper typography rendering and make the theme difficult to maintain.

## Glossary

- **Theme System**: The WordPress theme located at themes/mgrnz-theme
- **Style Enqueuing**: WordPress's proper method for loading CSS files using wp_enqueue_style()
- **Tailwind Reset**: CSS reset rules from Tailwind CSS that strip default browser styling
- **Typography System**: Consistent font families, sizes, weights, and line heights defined in CSS
- **Inline Styles**: Style attributes written directly in HTML/PHP templates
- **WPCode Plugin**: WordPress plugin for inserting custom code snippets

## Requirements

### Requirement 1

**User Story:** As a theme developer, I want proper WordPress theme structure, so that the theme follows WordPress standards and styles load correctly

#### Acceptance Criteria

1. THE Theme System SHALL include a functions.php file in the theme root directory
2. THE Theme System SHALL include a style.css file in the theme root directory with proper WordPress theme headers
3. WHEN the theme is activated, THE Theme System SHALL enqueue all CSS files using wp_enqueue_style()
4. THE Theme System SHALL load main.css, style.css, and custom.css in the correct order
5. THE Theme System SHALL register post-thumbnails support for image rendering
6. THE Theme System SHALL register custom image sizes for featured images and thumbnails

### Requirement 2

**User Story:** As a content editor, I want proper typography rendering, so that headings and text display with appropriate sizes and weights

#### Acceptance Criteria

1. THE Theme System SHALL define font-family rules for body text and headings
2. THE Theme System SHALL define font-size rules for h1 through h6 elements
3. THE Theme System SHALL define font-weight rules for h1 through h6 elements
4. THE Theme System SHALL define line-height rules for body text and headings
5. THE Theme System SHALL override Tailwind CSS reset rules that strip heading styles

### Requirement 3

**User Story:** As a theme developer, I want to remove inline styles from templates, so that styles are maintainable and consistent

#### Acceptance Criteria

1. THE Theme System SHALL move all inline style attributes from header.php to CSS files
2. THE Theme System SHALL move all inline style attributes from footer.php to CSS files
3. THE Theme System SHALL move all inline style attributes from index.php to CSS files
4. THE Theme System SHALL move all inline style attributes from single.php to CSS files
5. THE Theme System SHALL move all inline style attributes from sidebar.php to CSS files
6. THE Theme System SHALL use CSS classes instead of inline styles for all layout and typography

### Requirement 4

**User Story:** As a theme developer, I want a consistent typography system, so that text styling is predictable and maintainable

#### Acceptance Criteria

1. THE Theme System SHALL define CSS custom properties for font families
2. THE Theme System SHALL define CSS custom properties for font sizes
3. THE Theme System SHALL define CSS custom properties for font weights
4. THE Theme System SHALL define CSS custom properties for line heights
5. THE Theme System SHALL define CSS custom properties for color values
6. THE Theme System SHALL apply these custom properties consistently across all elements

### Requirement 5

**User Story:** As a site administrator, I want to identify and remove conflicting WPCode snippets, so that custom code doesn't override theme styles

#### Acceptance Criteria

1. THE Theme System SHALL document any WPCode snippets that inject CSS
2. THE Theme System SHALL provide instructions for removing or updating conflicting snippets
3. WHEN WPCode snippets inject styles, THE Theme System SHALL ensure theme styles have appropriate specificity to override them

### Requirement 6

**User Story:** As a theme developer, I want proper CSS organization, so that styles are easy to find and modify

#### Acceptance Criteria

1. THE Theme System SHALL organize custom.css with clear section comments
2. THE Theme System SHALL separate layout styles from typography styles
3. THE Theme System SHALL separate component styles from utility styles
4. THE Theme System SHALL document the purpose and load order of each CSS file

### Requirement 7

**User Story:** As a content editor, I want images to display correctly in posts, so that visual content renders properly

#### Acceptance Criteria

1. WHEN a featured image is set on a post, THE Theme System SHALL display the image on single post pages
2. WHEN images are inserted in post content, THE Theme System SHALL display them with proper styling
3. THE Theme System SHALL apply responsive image sizing to all post images
4. THE Theme System SHALL apply consistent border-radius and spacing to images
5. THE Theme System SHALL ensure Tailwind CSS image resets do not prevent image display
