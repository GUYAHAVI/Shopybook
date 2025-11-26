# Website Builder - Quick Test Guide

## Testing the Preview Functionality

### Step 1: Access Website Builder
1. Login to your Shopybook account
2. Navigate to **Website Builder** from the main menu
3. If you don't have a website yet, you'll see the Setup page

### Step 2: Test Theme Preview (Setup Page)

**Test Case 1: Visual Theme Cards**
- [ ] Each theme card shows a mini mockup (not just an emoji)
- [ ] Mini mockup has:
  - Header bar with colored elements
  - Content sections
  - CTA button in theme's accent color
- [ ] Colors match the theme's gradient background

**Test Case 2: Theme Preview Button**
- [ ] Click "Preview Theme" on any theme card
- [ ] New browser tab opens
- [ ] Preview shows:
  - Purple banner at top saying "Theme Preview: [Theme Name]"
  - Your business name in navigation
  - Sample hero section with your business info
  - Sample features section
  - Sample CTA section
  - Footer with your contact info
- [ ] Colors match the selected theme
- [ ] Close button works to close preview tab

**Test Case 3: Theme Selection**
- [ ] Click anywhere on theme card (not the preview button)
- [ ] Card gets blue border
- [ ] Checkmark appears in top-right
- [ ] Other cards lose selection
- [ ] Can still preview other themes without changing selection

### Step 3: Create Website
1. Select your preferred theme
2. Fill in the website information:
   - Business Name (pre-filled)
   - Tagline
   - Contact Email
   - Contact Phone
   - About Your Business
3. Click "Create My Website"
4. Wait for success message

### Step 4: Test Website Preview (Dashboard)

**Test Case 4: Dashboard Preview**
- [ ] After website creation, see Website Builder Dashboard
- [ ] "Preview" button visible in top-right area
- [ ] Click "Preview" button
- [ ] Website preview opens (not in new tab)
- [ ] Yellow/orange banner at top says "PREVIEW MODE"
- [ ] Your actual website content shows
- [ ] Navigation works
- [ ] Sections display correctly

**Test Case 5: Preview Before Publishing**
- [ ] Website status shows "Draft" (if not published)
- [ ] Preview button still works in draft mode
- [ ] Preview shows current state of website
- [ ] No errors or blank pages

### Step 5: Test Published Website Preview
1. Click "Publish Website" button
2. Wait for success message
3. Status changes to "Published"
4. Click "Preview" again
5. Should see website as public visitors would see it

## Expected Results

### Theme Preview Should Show:
```
┌─────────────────────────────────────────┐
│ 🎨 Theme Preview: Modern Business       │ ← Purple banner
│                    ✕ Close Preview      │
├─────────────────────────────────────────┤
│ [Logo] YourBusiness  Home About Contact │ ← Navigation
├─────────────────────────────────────────┤
│                                         │
│     Welcome to Your Business Name       │ ← Hero
│     Your tagline or description         │
│           [Get Started]                 │
│                                         │
├─────────────────────────────────────────┤
│   💼 Feature 1  ⚡ Feature 2  🎯 Feature 3│ ← Features
├─────────────────────────────────────────┤
│       Ready to Get Started?             │ ← CTA
│       [Contact Us Today]                │
├─────────────────────────────────────────┤
│ Footer with links and contact info     │ ← Footer
└─────────────────────────────────────────┘
```

### Website Preview Should Show:
```
┌─────────────────────────────────────────┐
│    PREVIEW MODE - Your Website Preview  │ ← Yellow banner
├─────────────────────────────────────────┤
│                                         │
│        Your Actual Website              │
│        With Real Content                │
│        And Sections                     │
│                                         │
└─────────────────────────────────────────┘
```

## Troubleshooting

### Problem: Preview button doesn't work
**Fix:**
1. Open browser console (F12)
2. Check for JavaScript errors
3. Verify CSRF token is present
4. Try hard refresh (Ctrl+Shift+R)

### Problem: Theme preview shows blank page
**Fix:**
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify route exists: `php artisan route:list | grep preview`
3. Clear cache: `php artisan cache:clear`

### Problem: Colors don't appear correctly
**Fix:**
1. Check theme has default_colors in database
2. Verify CSS variables are loading
3. Inspect element to see computed styles

### Problem: Preview opens external URL that doesn't exist
**Fix:**
- This was the old bug - should be fixed now
- If still happening, verify you're using updated controller code
- Check that `preview()` method renders a view, not redirects

## Quick Commands

### Clear cache after updates:
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### Check routes:
```bash
php artisan route:list | grep "website.builder"
```

### View logs:
```bash
tail -f storage/logs/laravel.log
```

## Success Criteria

✅ All checkboxes above are checked
✅ No errors in browser console
✅ No errors in Laravel logs
✅ Preview opens without issues
✅ Colors and fonts match theme
✅ Business information displays correctly
✅ Responsive design works on mobile

## Browser Compatibility

Test in:
- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari (if on Mac)
- [ ] Mobile browser

## Performance Check

- [ ] Theme cards load quickly
- [ ] Preview opens in < 2 seconds
- [ ] No console warnings
- [ ] Images load properly
- [ ] Fonts load correctly

## Report Issues

If you find any issues:

1. **Note the issue:** What went wrong?
2. **Check console:** Any errors in browser console (F12)?
3. **Check logs:** Any errors in `storage/logs/laravel.log`?
4. **Screenshot:** Take screenshot of the issue
5. **Report:** Provide details of steps to reproduce

---

## Next Steps After Testing

Once everything works:

1. **Customize Themes:**
   - Add more themes to database
   - Create unique color schemes
   - Design different layouts

2. **Add Content:**
   - Create additional pages
   - Add sections to pages
   - Upload images and media

3. **Configure SEO:**
   - Set meta titles
   - Set meta descriptions
   - Add keywords

4. **Publish:**
   - Review preview one final time
   - Click "Publish Website"
   - Share your website URL!

---

**Document Version:** 1.0
**Last Updated:** {{ date('Y-m-d') }}
**Tested By:** _____________
**Status:** _____________
