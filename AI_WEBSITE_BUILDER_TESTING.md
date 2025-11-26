# AI Website Builder - Testing Checklist

## 🔍 Pre-Testing Setup

- [ ] Verify `.env` has `ANTHROPIC_API_KEY` set
- [ ] Confirm business has description and type set
- [ ] Clear Laravel cache: `php artisan cache:clear`
- [ ] Check logs are writable: `storage/logs/`
- [ ] Verify internet connectivity

---

## 1️⃣ Theme Recommendation Testing

### Setup Page (`/website-builder/setup`)

**Test 1: Basic Recommendation**
- [ ] Navigate to setup page
- [ ] Click "AI Recommend Theme" button
- [ ] Verify loading spinner appears
- [ ] Verify recommendations panel opens
- [ ] Confirm 3 themes are shown
- [ ] Check "Top Pick" badge on first recommendation
- [ ] Verify theme thumbnails load
- [ ] Check reasons are relevant to business

**Test 2: Theme Selection**
- [ ] Click on any recommendation card
- [ ] Verify theme radio button gets checked
- [ ] Confirm smooth scroll to theme in grid
- [ ] Check success toast appears
- [ ] Verify theme border changes to selected state

**Test 3: Close Recommendations**
- [ ] Click X button on recommendations panel
- [ ] Verify panel closes
- [ ] Re-click "AI Recommend Theme"
- [ ] Verify panel re-opens with same recommendations

**Test 4: Error Handling**
- [ ] Temporarily remove API key from `.env`
- [ ] Click "AI Recommend Theme"
- [ ] Verify error message appears
- [ ] Check user can still select themes manually
- [ ] Restore API key

**Expected Results:**
- ✅ 3 relevant theme recommendations
- ✅ Clear reasoning for each
- ✅ One-click theme selection
- ✅ Graceful error handling

---

## 2️⃣ Content Generation Testing

### Page Editor (`/website-builder/pages/{page}/edit`)

**Test 5: Hero Section Generation**
- [ ] Click "Add Section" button
- [ ] Select "Hero" section type
- [ ] Click "Generate with AI" button
- [ ] Verify button shows "Generating..." spinner
- [ ] Wait for completion (max 40 seconds)
- [ ] Check section appears in page
- [ ] Verify content includes:
  - [ ] Heading (≤60 chars)
  - [ ] Subheading (≤120 chars)
  - [ ] CTA text (≤25 chars)
  - [ ] CTA link

**Test 6: About Section Generation**
- [ ] Add section → Select "About"
- [ ] Generate with AI
- [ ] Verify content includes:
  - [ ] Heading
  - [ ] Content (200-300 words)
  - [ ] Mission statement
  - [ ] Vision statement
- [ ] Check content relevance to business

**Test 7: Services Section Generation**
- [ ] Add section → Select "Services"
- [ ] Generate with AI
- [ ] Verify content includes:
  - [ ] Heading
  - [ ] Description
  - [ ] 3-6 service cards
  - [ ] Each service has title, description, icon suggestion

**Test 8: Manual Add Still Works**
- [ ] Open section modal
- [ ] Select any section type
- [ ] Click "Add Section" (NOT "Generate with AI")
- [ ] Verify section created with empty/default content
- [ ] Confirm manual workflow unaffected

**Test All Section Types:**
- [ ] Hero
- [ ] About
- [ ] Services
- [ ] Features
- [ ] Team
- [ ] Testimonials
- [ ] Contact
- [ ] Gallery
- [ ] Pricing
- [ ] CTA

**Expected Results:**
- ✅ Content generated in <40 seconds
- ✅ SEO-optimized with proper character limits
- ✅ Business-specific language
- ✅ Structured data ready for use

---

## 3️⃣ SEO Generation Testing

### SEO Endpoint (via Postman/Browser Console)

**Test 9: Generate SEO for Page**
```javascript
fetch('/website-builder/ai/generate-seo', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        page_id: YOUR_PAGE_ID,
        auto_apply: true
    })
})
.then(r => r.json())
.then(console.log)
```

**Verify Response:**
- [ ] `meta_title` present (≤60 chars)
- [ ] `meta_description` present (≤155 chars)
- [ ] `meta_keywords` present (10-15 keywords)
- [ ] `og_title` present
- [ ] `og_description` present
- [ ] Keywords relevant to page and business

**Test 10: Auto-Apply SEO**
- [ ] Call endpoint with `auto_apply: true`
- [ ] Check page in database updated
- [ ] Verify meta fields populated
- [ ] Confirm no data loss

**Expected Results:**
- ✅ Complete SEO metadata generated
- ✅ Character limits respected
- ✅ Keywords relevant and diverse
- ✅ Auto-apply updates database

---

## 4️⃣ AI Guidance Testing

### Dashboard (`/website-builder/`)

**Test 11: Get AI Guidance**
- [ ] Navigate to website builder dashboard
- [ ] Locate "AI Website Assistant" panel
- [ ] Click "Get AI Guidance" button
- [ ] Verify loading spinner appears
- [ ] Wait for guidance (max 60 seconds)
- [ ] Check guidance content includes:
  - [ ] Recommended pages
  - [ ] Essential sections
  - [ ] Content strategy
  - [ ] SEO best practices
  - [ ] CTA recommendations
  - [ ] Mobile tips
  - [ ] Color suggestions
  - [ ] Key features

**Test 12: Guidance Formatting**
- [ ] Verify bold text for headings
- [ ] Check line breaks render correctly
- [ ] Confirm numbered lists visible
- [ ] Verify scrollable if content long
- [ ] Test "Hide Guidance" button

**Test 13: Theme-Specific Guidance**
- [ ] Generate guidance with no theme selected
- [ ] Change website theme
- [ ] Regenerate guidance
- [ ] Verify guidance references selected theme

**Expected Results:**
- ✅ Comprehensive, actionable guidance
- ✅ Business-type specific recommendations
- ✅ Well-formatted, readable content
- ✅ Theme-aware suggestions

---

## 5️⃣ Image Suggestions Testing

### Image Endpoint (via Postman/Console)

**Test 14: Get Image Prompts**
```javascript
fetch('/website-builder/ai/suggest-images', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        section_type: 'hero',
        website_id: YOUR_WEBSITE_ID
    })
})
.then(r => r.json())
.then(console.log)
```

**Verify Response:**
- [ ] 3-5 image prompts returned
- [ ] Each has `prompt` and `description` fields
- [ ] Prompts are detailed and specific
- [ ] Relevant to section type
- [ ] Professional quality guidance
- [ ] Kenyan context where appropriate

**Test Different Section Types:**
- [ ] hero
- [ ] about
- [ ] services
- [ ] team
- [ ] gallery

**Expected Results:**
- ✅ Detailed AI image generator prompts
- ✅ Business-type relevant
- ✅ Diverse and inclusive
- ✅ Professional aesthetic

---

## 6️⃣ Error Handling & Edge Cases

**Test 15: No Business Data**
- [ ] Create user with no business
- [ ] Try accessing any AI endpoint
- [ ] Verify 404 error with clear message
- [ ] Confirm no crashes

**Test 16: Invalid Website ID**
- [ ] Call AI endpoint with non-existent website_id
- [ ] Verify 404 error
- [ ] Check authorization works

**Test 17: Unauthorized Access**
- [ ] User A creates website
- [ ] User B tries to generate content for User A's website
- [ ] Verify 403 Forbidden error
- [ ] Confirm no data exposed

**Test 18: API Timeout**
- [ ] Temporarily set short timeout (5 seconds)
- [ ] Try generating content
- [ ] Verify timeout error caught
- [ ] Check user gets error message
- [ ] Restore normal timeout

**Test 19: Network Failure**
- [ ] Disconnect internet
- [ ] Try any AI feature
- [ ] Verify graceful error message
- [ ] Check manual workflow still works
- [ ] Reconnect internet

**Test 20: Invalid Section Type**
- [ ] Call generate-content with invalid section_type
- [ ] Verify validation error
- [ ] Check no database changes

**Expected Results:**
- ✅ All errors caught gracefully
- ✅ Clear error messages to users
- ✅ No crashes or white screens
- ✅ Manual workflow always available

---

## 7️⃣ Performance Testing

**Test 21: Response Times**
- [ ] Theme recommendation: <30s
- [ ] Content generation: <40s
- [ ] SEO generation: <30s
- [ ] Guidance generation: <60s
- [ ] Image suggestions: <30s

**Test 22: Concurrent Requests**
- [ ] Multiple users requesting AI features simultaneously
- [ ] Verify all requests complete successfully
- [ ] Check no race conditions

**Test 23: Database Performance**
- [ ] Generate 10+ sections with AI
- [ ] Verify no duplicate sections
- [ ] Check section order maintained
- [ ] Confirm no data corruption

---

## 8️⃣ Integration Testing

**Test 24: Complete Website Build Flow**
1. [ ] Start from setup page
2. [ ] Get AI theme recommendation
3. [ ] Select recommended theme
4. [ ] Complete setup form
5. [ ] Create website
6. [ ] Navigate to dashboard
7. [ ] Get AI guidance
8. [ ] Create new page
9. [ ] Add 5 sections with AI generation
10. [ ] Generate SEO for page
11. [ ] Preview website
12. [ ] Publish website

**Test 25: Edit Existing Website**
- [ ] Load existing website
- [ ] Change theme via AI recommendation
- [ ] Add new page with AI sections
- [ ] Update existing page SEO
- [ ] Verify all changes persist

**Test 26: Cross-Browser Testing**
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] Mobile browsers

**Test 27: Responsive Design**
- [ ] Desktop (1920x1080)
- [ ] Laptop (1366x768)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)

---

## 9️⃣ User Experience Testing

**Test 28: Loading States**
- [ ] All buttons show spinners during processing
- [ ] Loading messages are clear
- [ ] Buttons disabled during requests
- [ ] No double-submissions possible

**Test 29: Success Feedback**
- [ ] Toast notifications appear
- [ ] Success messages are clear
- [ ] Page reloads when needed
- [ ] Data persists correctly

**Test 30: Error Feedback**
- [ ] Error toasts appear
- [ ] Error messages are user-friendly
- [ ] No technical jargon exposed
- [ ] Retry options available

---

## 🔟 Security Testing

**Test 31: CSRF Protection**
- [ ] Remove CSRF token from request
- [ ] Verify 419 error
- [ ] Check request rejected

**Test 32: SQL Injection**
- [ ] Try SQL injection in section_type
- [ ] Verify input sanitized
- [ ] Check no database corruption

**Test 33: XSS Prevention**
- [ ] AI generates content with `<script>` tags
- [ ] Verify content sanitized before rendering
- [ ] Check no script execution

**Test 34: Authorization Checks**
- [ ] Every endpoint validates user owns website
- [ ] No data leakage between users
- [ ] Business relationships enforced

---

## ✅ Final Validation

**Pre-Production Checklist:**
- [ ] All 34 tests passed
- [ ] No errors in Laravel logs
- [ ] No console errors in browser
- [ ] API key secured in production `.env`
- [ ] Database backups in place
- [ ] Monitoring alerts configured
- [ ] User documentation published
- [ ] Support team trained

**Production Deployment:**
- [ ] Deploy to staging environment first
- [ ] Run full test suite on staging
- [ ] Get stakeholder approval
- [ ] Deploy to production
- [ ] Monitor API usage and costs
- [ ] Track user adoption metrics
- [ ] Gather initial user feedback

---

## 📊 Success Criteria

After 1 week in production:
- [ ] >70% of new websites use AI features
- [ ] <5% error rate on AI endpoints
- [ ] Average website build time <30 minutes
- [ ] User satisfaction score >4.5/5
- [ ] Support tickets related to AI <10
- [ ] API costs within budget

---

## 🐛 Bug Reporting Template

If you find issues, report with:
```
**Test Number:** Test 15
**Feature:** Theme Recommendation
**Steps to Reproduce:**
1. Navigate to setup page
2. Click "AI Recommend Theme"
3. ...

**Expected Result:**
3 theme recommendations appear

**Actual Result:**
Error message "Unable to generate recommendations"

**Environment:**
- Browser: Chrome 120
- OS: Windows 11
- User ID: 123
- Business ID: 456

**Console Errors:**
[Paste any console errors]

**Laravel Logs:**
[Paste relevant log entries]

**Screenshots:**
[Attach if applicable]
```

---

**Happy Testing! 🚀**

Remember: AI features should enhance, not replace, manual workflow. Users should always have fallback options if AI fails.
