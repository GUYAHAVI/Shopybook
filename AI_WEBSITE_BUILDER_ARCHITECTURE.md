# AI Website Builder - System Architecture

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         USER INTERFACE LAYER                             │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐     │
│  │  Setup Page      │  │  Page Editor     │  │  Dashboard       │     │
│  │  setup.blade.php │  │  page-editor     │  │  dashboard       │     │
│  │                  │  │  .blade.php      │  │  .blade.php      │     │
│  │  [AI Recommend   │  │                  │  │                  │     │
│  │   Theme Button]  │  │  [Generate with  │  │  [Get AI         │     │
│  │                  │  │   AI Button]     │  │   Guidance]      │     │
│  └────────┬─────────┘  └────────┬─────────┘  └────────┬─────────┘     │
│           │                     │                      │                │
└───────────┼─────────────────────┼──────────────────────┼────────────────┘
            │                     │                      │
            │ AJAX                │ AJAX                 │ AJAX
            │                     │                      │
┌───────────▼─────────────────────▼──────────────────────▼────────────────┐
│                         ROUTE LAYER (routes/web.php)                     │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  POST /website-builder/ai/recommend-theme    ──┐                        │
│  POST /website-builder/ai/generate-content   ──┤                        │
│  POST /website-builder/ai/generate-seo       ──┼─► WebsiteBuilder      │
│  GET  /website-builder/ai/guidance           ──┤    Controller         │
│  POST /website-builder/ai/suggest-images     ──┘                        │
│                                                                          │
└───────────────────────────────┬──────────────────────────────────────────┘
                                │
                                │ Dependency Injection
                                │
┌───────────────────────────────▼──────────────────────────────────────────┐
│                    CONTROLLER LAYER                                      │
│              (WebsiteBuilderController.php)                              │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌────────────────────────────────────────────────────────────┐         │
│  │  Controller Methods:                                       │         │
│  │                                                             │         │
│  │  recommendTheme()        ──┐                               │         │
│  │  generateSectionContent() ──┤                              │         │
│  │  generateSEO()           ──┼─► Calls ClaudeAPIService     │         │
│  │  getGuidance()           ──┤                               │         │
│  │  suggestImages()         ──┘                               │         │
│  │                                                             │         │
│  │  • Validates requests                                      │         │
│  │  • Checks authorization (user owns website)                │         │
│  │  • Gathers business data                                   │         │
│  │  • Returns JSON responses                                  │         │
│  └────────────────────────────────────────────────────────────┘         │
│                                                                          │
└───────────────────────────────┬──────────────────────────────────────────┘
                                │
                                │ Service Call
                                │
┌───────────────────────────────▼──────────────────────────────────────────┐
│                      SERVICE LAYER                                       │
│                  (ClaudeAPIService.php)                                  │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌────────────────────────────────────────────────────────────┐         │
│  │  AI Methods:                                               │         │
│  │                                                             │         │
│  │  recommendTheme()                                          │         │
│  │    • Analyzes business type, description                   │         │
│  │    • Compares with theme categories & styles               │         │
│  │    • Returns top 3 themes with reasons                     │         │
│  │    • Temp: 0.5, Tokens: 1024                              │         │
│  │                                                             │         │
│  │  generateSectionContent()                                  │         │
│  │    • Gets section specs (hero, about, services, etc.)      │         │
│  │    • Crafts detailed prompt with business context          │         │
│  │    • Enforces character limits for SEO                     │         │
│  │    • Returns structured JSON content                       │         │
│  │    • Temp: 0.7, Tokens: 2048                              │         │
│  │                                                             │         │
│  │  generateSEOMetadata()                                     │         │
│  │    • Creates meta_title (≤60 chars)                       │         │
│  │    • Creates meta_description (≤155 chars)                │         │
│  │    • Generates 10-15 keywords                              │         │
│  │    • Social media tags (og_title, og_description)         │         │
│  │    • Temp: 0.6, Tokens: 512                               │         │
│  │                                                             │         │
│  │  generateWebsiteGuidance()                                 │         │
│  │    • Recommends pages & sections                           │         │
│  │    • Content strategy tips                                 │         │
│  │    • SEO best practices                                    │         │
│  │    • CTA placement advice                                  │         │
│  │    • Theme-specific guidance                               │         │
│  │    • Temp: 0.7, Tokens: 3000                              │         │
│  │                                                             │         │
│  │  suggestImagePrompts()                                     │         │
│  │    • Creates 3-5 AI image generator prompts                │         │
│  │    • Professional quality descriptions                     │         │
│  │    • Business-type relevant                                │         │
│  │    • Kenyan context where appropriate                      │         │
│  │    • Temp: 0.8, Tokens: 1024                              │         │
│  │                                                             │         │
│  │  getSectionContentSpecs()                                  │         │
│  │    • Returns content requirements per section type         │         │
│  │    • Defines field structure & character limits            │         │
│  │                                                             │         │
│  └────────────────────────────────────────────────────────────┘         │
│                                                                          │
└───────────────────────────────┬──────────────────────────────────────────┘
                                │
                                │ HTTP POST
                                │
┌───────────────────────────────▼──────────────────────────────────────────┐
│                      EXTERNAL API LAYER                                  │
│                  (Claude AI - Anthropic)                                 │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  URL: https://api.anthropic.com/v1/messages                             │
│  Model: claude-sonnet-4-20250514                                        │
│  Headers:                                                                │
│    • x-api-key: {ANTHROPIC_API_KEY}                                     │
│    • anthropic-version: 2023-06-01                                      │
│    • Content-Type: application/json                                     │
│                                                                          │
│  Request Body:                                                           │
│    {                                                                     │
│      "model": "claude-sonnet-4-20250514",                               │
│      "max_tokens": 512-3000,                                            │
│      "temperature": 0.5-0.8,                                            │
│      "messages": [{"role": "user", "content": "..."}]                   │
│    }                                                                     │
│                                                                          │
│  Response:                                                               │
│    {                                                                     │
│      "content": [{"text": "AI generated content..."}],                  │
│      "model": "claude-sonnet-4-20250514",                               │
│      "usage": {...}                                                      │
│    }                                                                     │
│                                                                          │
└───────────────────────────────┬──────────────────────────────────────────┘
                                │
                                │ Response Parsed
                                │
┌───────────────────────────────▼──────────────────────────────────────────┐
│                        DATA PERSISTENCE LAYER                            │
│                      (Laravel Eloquent Models)                           │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐                │
│  │  Website    │◄───│ WebsitePage │◄───│ WebsiteSection│               │
│  │             │    │             │    │               │                │
│  │ • theme_id  │    │ • title     │    │ • type        │                │
│  │ • colors    │    │ • slug      │    │ • content     │ ◄── AI Data   │
│  │ • settings  │    │ • meta_desc │    │ • settings    │                │
│  │ • seo_*     │    │ • meta_keys │    │ • is_visible  │                │
│  └──────┬──────┘    └─────────────┘    └─────────────┘                │
│         │                                                                │
│         │                                                                │
│  ┌──────▼──────┐    ┌─────────────┐                                    │
│  │  Business   │    │WebsiteTheme │                                    │
│  │             │    │             │                                    │
│  │ • name      │    │ • category  │                                    │
│  │ • type      │    │ • style     │                                    │
│  │ • desc      │    │ • sections[]│                                    │
│  │ • location  │    │ • colors    │                                    │
│  └─────────────┘    └─────────────┘                                    │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘


═══════════════════════════════════════════════════════════════════════════
                           DATA FLOW EXAMPLES
═══════════════════════════════════════════════════════════════════════════

EXAMPLE 1: Theme Recommendation Flow
────────────────────────────────────────────────────────────────────────────

1. User clicks "AI Recommend Theme" button on setup page
      │
      ▼
2. JavaScript sends POST to /website-builder/ai/recommend-theme
      │
      ▼
3. WebsiteBuilderController::recommendTheme()
      │
      ├─► Loads user's business (Auth::user()->businesses()->first())
      │
      ├─► Loads all active themes (WebsiteTheme::active()->get())
      │
      ├─► Prepares business data:
      │       {
      │         name: "Joe's Hardware Store",
      │         type: "retail",
      │         description: "Hardware and home improvement...",
      │         location: "Nairobi, Kenya"
      │       }
      │
      └─► Calls ClaudeAPIService::recommendTheme($businessData, $themes)
              │
              ▼
4. ClaudeAPIService builds prompt:
      │
      │   "You are an expert web designer...
      │    Business: Joe's Hardware Store (retail)
      │    Available Themes:
      │      - ModernBiz (business, professional)
      │      - ShopFlow (ecommerce, modern)
      │      - PortfolioPro (portfolio, creative)
      │    Recommend TOP 3..."
      │
      ▼
5. Sends to Claude API (POST https://api.anthropic.com/v1/messages)
      │
      ▼
6. Claude analyzes and returns:
      │
      │   [
      │     {
      │       "slug": "shopflow",
      │       "reason": "Perfect for retail businesses with e-commerce..."
      │     },
      │     {
      │       "slug": "modernbiz",
      │       "reason": "Clean, professional layout for established..."
      │     },
      │     {
      │       "slug": "portfoliopro",
      │       "reason": "If showcasing unique products is priority..."
      │     }
      │   ]
      │
      ▼
7. Controller enriches with theme details (images, metadata)
      │
      ▼
8. Returns JSON response to frontend
      │
      ▼
9. JavaScript displays recommendations with badges, thumbnails
      │
      ▼
10. User clicks recommendation → theme auto-selected ✅


EXAMPLE 2: Content Generation Flow
────────────────────────────────────────────────────────────────────────────

1. User selects "Hero" section type in modal
      │
      ▼
2. User clicks "Generate with AI" button
      │
      ▼
3. JavaScript sends POST to /website-builder/ai/generate-content
      │   Body: {
      │     section_type: "hero",
      │     website_id: 123,
      │     existing_content: null
      │   }
      │
      ▼
4. WebsiteBuilderController::generateSectionContent()
      │
      ├─► Validates request (section_type required, website_id exists)
      │
      ├─► Loads website & checks authorization
      │
      ├─► Gathers business data from website->business
      │
      └─► Calls ClaudeAPIService::generateSectionContent()
              │
              ▼
5. Service gets section specs via getSectionContentSpecs("hero")
      │
      │   Returns: "Generate a compelling hero section with:
      │             - heading (max 60 chars, powerful, SEO-friendly)
      │             - subheading (max 120 chars, value proposition)
      │             - cta_text (max 25 chars, action-oriented)
      │             - cta_link (use #contact as default)"
      │
      ▼
6. Service builds prompt:
      │
      │   "You are an expert content writer and SEO specialist.
      │    Business: Joe's Hardware Store (retail) in Nairobi
      │    Description: Hardware and home improvement supplies...
      │    Section Type: hero
      │    Requirements: [specs from step 5]
      │    Output: Return ONLY a JSON object with content fields."
      │
      ▼
7. Sends to Claude API (Temp: 0.7, Tokens: 2048)
      │
      ▼
8. Claude generates:
      │
      │   {
      │     "heading": "Quality Hardware, Expert Advice",
      │     "subheading": "Nairobi's trusted source for home improvement supplies and professional guidance since 2010",
      │     "cta_text": "Shop Now",
      │     "cta_link": "#contact"
      │   }
      │
      ▼
9. Service extracts JSON from response
      │
      ▼
10. Controller calls WebsiteBuilderService to create section
      │
      │   Creates WebsiteSection record:
      │     type: "hero"
      │     content: [parsed JSON from Claude]
      │     is_visible: true
      │     page_id: [current page]
      │
      ▼
11. Returns success JSON to frontend
      │
      ▼
12. JavaScript shows success toast & reloads page
      │
      ▼
13. User sees new hero section with AI content ✅


═══════════════════════════════════════════════════════════════════════════
                          ERROR HANDLING FLOW
═══════════════════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────────────────┐
│  Any AI Method in ClaudeAPIService                                       │
│                                                                          │
│  try {                                                                   │
│      // Prepare prompt                                                   │
│      // Call Claude API                                                  │
│      // Parse response                                                   │
│      return $result;                                                     │
│  }                                                                       │
│  catch (\Exception $e) {                                                │
│      Log::error('AI Error: ' . $e->getMessage());                       │
│      return null;  ◄──── Graceful failure                              │
│  }                                                                       │
│                                                                          │
└───────────────────────────────┬──────────────────────────────────────────┘
                                │
                                │ null returned
                                │
┌───────────────────────────────▼──────────────────────────────────────────┐
│  Controller Method                                                       │
│                                                                          │
│  $result = $this->claudeAPIService->someMethod(...);                    │
│                                                                          │
│  if (!$result) {                                                        │
│      return response()->json([                                          │
│          'error' => 'Unable to generate content'                        │
│      ], 500);  ◄──── User-friendly error                               │
│  }                                                                       │
│                                                                          │
└───────────────────────────────┬──────────────────────────────────────────┘
                                │
                                │ Error JSON
                                │
┌───────────────────────────────▼──────────────────────────────────────────┐
│  Frontend JavaScript                                                     │
│                                                                          │
│  .catch(error => {                                                      │
│      console.error('Error:', error);                                    │
│      showToast('Failed to generate content. Try manually.', 'danger'); │
│  })  ◄──── Manual workflow still available                             │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘


═══════════════════════════════════════════════════════════════════════════
                        SECURITY & AUTHORIZATION
═══════════════════════════════════════════════════════════════════════════

Every Controller Method:
┌─────────────────────────────────────────────────────────────────────────┐
│                                                                          │
│  1. Middleware: 'auth' (routes/web.php)                                 │
│      └─► User must be logged in                                         │
│                                                                          │
│  2. Load Website:                                                        │
│      $website = Website::findOrFail($request->website_id);              │
│      └─► 404 if website doesn't exist                                   │
│                                                                          │
│  3. Authorization Check:                                                 │
│      $this->authorize('update', $website);                              │
│      └─► Verifies user owns website via business relationship          │
│      └─► 403 Forbidden if not authorized                                │
│                                                                          │
│  4. CSRF Protection:                                                     │
│      X-CSRF-TOKEN header required on all POST requests                  │
│      └─► 419 error if token missing/invalid                             │
│                                                                          │
│  5. Input Validation:                                                    │
│      $request->validate([...])                                          │
│      └─► 422 validation error if invalid                                │
│                                                                          │
│  ✅ ONLY THEN execute AI logic                                          │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘


═══════════════════════════════════════════════════════════════════════════
                            KEY METRICS
═══════════════════════════════════════════════════════════════════════════

Files Modified: 6
Lines Added: ~840

Backend:
  • ClaudeAPIService.php: +350 lines (6 new methods)
  • WebsiteBuilderController.php: +195 lines (5 new endpoints)
  • routes/web.php: +5 routes

Frontend:
  • setup.blade.php: +130 lines (theme recommendations UI)
  • page-editor.blade.php: +75 lines (content generation UI)
  • dashboard.blade.php: +85 lines (AI guidance panel)

Documentation:
  • AI_WEBSITE_BUILDER_GUIDE.md: Complete feature guide
  • AI_WEBSITE_BUILDER_SUMMARY.md: Implementation summary
  • AI_WEBSITE_BUILDER_TESTING.md: 34-point test checklist
  • AI_WEBSITE_BUILDER_QUICK_REFERENCE.md: Quick reference
  • AI_WEBSITE_BUILDER_ARCHITECTURE.md: This file

Status: ✅ Complete & Production-Ready
Model: Claude Sonnet 4 (claude-sonnet-4-20250514)
