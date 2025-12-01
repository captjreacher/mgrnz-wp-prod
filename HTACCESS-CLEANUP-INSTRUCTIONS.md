# .htaccess Cleanup Instructions

## What Was Removed

All **SpeedyCache** directives have been removed from the .htaccess file since the plugin was deleted.

### Removed Sections:
1. `# BEGIN SpeedyCacheheaders` - Header modifications
2. `# BEGIN Gzipspeedycache` - Gzip compression rules
3. `# BEGIN LBCspeedycache` - Browser caching rules
4. `# BEGIN WEBPspeedycache` - WebP image rules
5. `# BEGIN speedycache` - Cache rewrite rules

### What Remains:
- ✅ LiteSpeed Cache directives (still active)
- ✅ WordPress core directives
- ✅ Security block directives
- ✅ LiteSpeed module settings

---

## How to Update Production .htaccess

### Option 1: Via SSH (Recommended)
```bash
# Connect to server
ssh mgrnz@mgrnz.com

# Backup current .htaccess
cp /home/mgrnz/public_html/.htaccess /home/mgrnz/public_html/.htaccess.backup

# Edit the file
nano /home/mgrnz/public_html/.htaccess
```

Then:
1. Delete all sections marked with "SpeedyCache" or "speedycache"
2. Keep LiteSpeed Cache sections
3. Keep WordPress sections
4. Keep Security Block
5. Save and exit (Ctrl+X, Y, Enter)

### Option 2: Via FTP
1. Download current `.htaccess` from production
2. Make a backup copy
3. Open in text editor
4. Delete all SpeedyCache sections
5. Upload the cleaned version
6. Test the site

### Option 3: Copy from Clean File
The cleaned .htaccess content is in `htaccess-cleaned.txt` in this repository.

1. Copy content from `htaccess-cleaned.txt`
2. Upload to production as `.htaccess`
3. Test the site

---

## What SpeedyCache Was Doing

### Headers (Now Handled by LiteSpeed)
- Setting cache control headers
- Managing ETags
- CDN cache control

### Gzip Compression (Now Handled by LiteSpeed)
- Compressing CSS, JS, HTML
- Compressing fonts
- Compressing images

### Browser Caching (Now Handled by LiteSpeed)
- Setting expiration times for static assets
- 1 year cache for images, fonts, CSS, JS

### WebP Images (Now Handled by LiteSpeed)
- Serving WebP images when supported
- Fallback to original format

### Cache Rewrite Rules (Now Handled by LiteSpeed)
- Serving cached HTML files
- Bypassing cache for logged-in users
- Bypassing cache for admin pages

---

## Why This Is Safe

**LiteSpeed Cache handles all of these functions:**
- LiteSpeed Cache is more efficient than SpeedyCache
- LiteSpeed Cache is server-level, not PHP-level
- LiteSpeed Cache has better WordPress integration
- No functionality is lost by removing SpeedyCache

---

## Testing After Cleanup

### 1. Check Site Loads
```
https://mgrnz.com
```

### 2. Check Caching Works
```bash
# Check response headers
curl -I https://mgrnz.com
```

Look for:
- `x-litespeed-cache: hit` (cache is working)
- `cache-control` headers present

### 3. Check Gzip Works
```bash
# Check compression
curl -H "Accept-Encoding: gzip" -I https://mgrnz.com
```

Look for:
- `content-encoding: gzip`

### 4. Test Admin Access
```
https://mgrnz.com/wp-admin/
```

Should load normally without cache.

### 5. Check Page Speed
- Run Google PageSpeed Insights
- Should maintain or improve scores

---

## Rollback Plan

If anything breaks:

### Quick Rollback via SSH:
```bash
ssh mgrnz@mgrnz.com
cp /home/mgrnz/public_html/.htaccess.backup /home/mgrnz/public_html/.htaccess
```

### Via FTP:
1. Upload the backup .htaccess file
2. Overwrite the current one

---

## Expected Results

✅ Site loads normally
✅ Caching still works (via LiteSpeed)
✅ Gzip compression still works (via LiteSpeed)
✅ Page speed maintained or improved
✅ No 500 errors
✅ Admin area accessible

---

## Notes

- The local .htaccess file is already clean (only WordPress directives)
- This cleanup is for the **production server** only
- SpeedyCache directives are safe to remove since the plugin is deleted
- LiteSpeed Cache will continue to handle all caching needs

---

## Quick Command to Apply

If you have SSH access:

```bash
# Backup and clean in one command
ssh mgrnz@mgrnz.com "cd /home/mgrnz/public_html && cp .htaccess .htaccess.backup && sed -i '/# BEGIN SpeedyCache/,/# END speedycache/d' .htaccess && sed -i '/# BEGIN Gzipspeedycache/,/# END Gzipspeedycache/d' .htaccess && sed -i '/# BEGIN LBCspeedycache/,/# END LBCspeedycache/d' .htaccess && sed -i '/# BEGIN WEBPspeedycache/,/# END WEBPspeedycache/d' .htaccess"
```

This will:
1. Create backup
2. Remove all SpeedyCache sections
3. Keep everything else intact
