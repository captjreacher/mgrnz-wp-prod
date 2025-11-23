-- Fix WordPress URLs from mgrnz.local/wp to mgrnz.local
-- Run this in phpMyAdmin or MySQL command line

-- Update site URL and home URL in options table
UPDATE wp_options SET option_value = 'http://mgrnz.local' WHERE option_name = 'siteurl';
UPDATE wp_options SET option_value = 'http://mgrnz.local' WHERE option_name = 'home';

-- Update all post content URLs
UPDATE wp_posts SET post_content = REPLACE(post_content, 'http://mgrnz.local/wp/', 'http://mgrnz.local/');
UPDATE wp_posts SET guid = REPLACE(guid, 'http://mgrnz.local/wp/', 'http://mgrnz.local/');

-- Update post meta
UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, 'http://mgrnz.local/wp/', 'http://mgrnz.local/');

-- Update comments
UPDATE wp_comments SET comment_content = REPLACE(comment_content, 'http://mgrnz.local/wp/', 'http://mgrnz.local/');
UPDATE wp_comments SET comment_author_url = REPLACE(comment_author_url, 'http://mgrnz.local/wp/', 'http://mgrnz.local/');

-- Note: After running this, you may need to:
-- 1. Clear your WordPress cache
-- 2. Regenerate permalinks (Settings > Permalinks > Save)
-- 3. Clear your browser cache
