# External Sources Guide for AI Learning

## 🚀 **Current External Sources**

Our AI models learn from the following external sources:

### **1. News APIs**
- **NewsAPI**: `https://newsapi.org/v2/` - Global news articles
- **Guardian API**: `https://content.guardianapis.com/` - UK and international news
- **NYT API**: `https://api.nytimes.com/svc/` - New York Times articles
- **Reuters API**: `https://api.reuters.com/` - Business and financial news
- **Bloomberg API**: `https://api.bloomberg.com/` - Financial market data
- **CNBC API**: `https://api.cnbc.com/` - Business news and market data
- **Forbes API**: `https://api.forbes.com/` - Business and technology news
- **TechCrunch API**: `https://api.techcrunch.com/` - Technology news

### **2. Social Media Platforms**
- **Twitter API**: `https://api.twitter.com/2/` - Real-time tweets and trends
- **Instagram API**: `https://graph.instagram.com/` - Visual content and engagement
- **Facebook API**: `https://graph.facebook.com/` - Social media trends
- **LinkedIn API**: `https://api.linkedin.com/v2/` - Professional networking data
- **TikTok API**: `https://open.tiktokapis.com/v2/` - Short-form video trends
- **YouTube API**: `https://www.googleapis.com/youtube/v3/` - Video content and trends
- **Reddit API**: `https://api.reddit.com/` - Community discussions (Free)
- **HackerNews API**: `https://hacker-news.firebaseio.com/v0/` - Tech community (Free)

### **3. Market Data Sources**
- **Alpha Vantage API**: `https://www.alphavantage.co/` - Stock market data
- **Finnhub API**: `https://finnhub.io/` - Real-time financial data
- **Yahoo Finance API**: `https://query1.finance.yahoo.com/` - Financial data (Free)
- **CoinGecko API**: `https://api.coingecko.com/api/v3/` - Cryptocurrency data (Free)

### **4. Industry Reports**
- **IBISWorld API**: `https://api.ibisworld.com/` - Industry research reports
- **Statista API**: `https://api.statista.com/` - Statistics and market data
- **PitchBook API**: `https://api.pitchbook.com/v1/` - Private market data
- **Crunchbase API**: `https://api.crunchbase.com/v3.1/` - Company information

### **5. E-commerce Platforms**
- **Amazon API**: `https://api.amazon.com/` - Product data and pricing
- **eBay API**: `https://api.ebay.com/` - Marketplace data
- **Etsy API**: `https://openapi.etsy.com/v3/` - Handmade and vintage items
- **Shopify API**: `https://api.shopify.com/` - E-commerce platform data

### **6. Review Platforms**
- **Trustpilot API**: `https://api.trustpilot.com/v1/` - Customer reviews
- **Yelp API**: `https://api.yelp.com/v3/` - Business reviews and ratings
- **Google Reviews API**: `https://maps.googleapis.com/maps/api/place/` - Place reviews
- **Glassdoor API**: `https://api.glassdoor.com/api/api.htm` - Company reviews
- **Indeed API**: `https://api.indeed.com/` - Job and company reviews

### **7. Analytics Platforms**
- **SEMrush API**: `https://api.semrush.com/` - SEO and competitive data
- **SimilarWeb API**: `https://api.similarweb.com/v1/` - Website traffic data
- **Alexa API**: `https://api.alexa.com/` - Website rankings
- **Moz API**: `https://moz.com/api/` - SEO metrics
- **Ahrefs API**: `https://api.ahrefs.com/v3/` - Backlink and SEO data

### **8. Business Directories**
- **Crunchbase API**: `https://api.crunchbase.com/v3.1/` - Company information
- **AngelList API**: `https://api.angel.co/1/` - Startup data
- **PitchBook API**: `https://api.pitchbook.com/v1/` - Private market data
- **Bloomberg API**: `https://api.bloomberg.com/` - Financial data
- **Reuters API**: `https://api.reuters.com/` - News and financial data

### **9. App Stores**
- **Apple App Store API**: `https://itunes.apple.com/lookup` - iOS app data (Free)
- **Google Play Store API**: `https://play.google.com/store/apps/details` - Android app data (Free)
- **Microsoft Store API**: `https://store.rg.microsoft.com/` - Windows app data (Free)

### **10. Developer Platforms**
- **GitHub API**: `https://api.github.com/` - Code repositories (Free)
- **GitLab API**: `https://gitlab.com/api/v4/` - Code repositories (Free)
- **Stack Overflow API**: `https://api.stackexchange.com/2.3/` - Developer Q&A (Free)
- **NPM API**: `https://registry.npmjs.org/` - JavaScript packages (Free)
- **PyPI API**: `https://pypi.org/pypi/` - Python packages (Free)

## 🔧 **How to Add More Sources**

### **Step 1: Add API Keys to .env**
```env
# New API Keys
GOOGLE_SEARCH_API_KEY=your_google_search_api_key
SEMRUSH_API_KEY=your_semrush_api_key
SIMILARWEB_API_KEY=your_similarweb_api_key
ALEXA_API_KEY=your_alexa_api_key
MOZ_API_KEY=your_moz_api_key
AHREFS_API_KEY=your_ahrefs_api_key
CRUNCHBASE_API_KEY=your_crunchbase_api_key
LINKEDIN_API_KEY=your_linkedin_api_key
TWITTER_API_KEY=your_twitter_api_key
INSTAGRAM_API_KEY=your_instagram_api_key
FACEBOOK_API_KEY=your_facebook_api_key
YOUTUBE_API_KEY=your_youtube_api_key
TIKTOK_API_KEY=your_tiktok_api_key
YELP_API_KEY=your_yelp_api_key
GLASSDOOR_API_KEY=your_glassdoor_api_key
INDEED_API_KEY=your_indeed_api_key
AMAZON_API_KEY=your_amazon_api_key
EBAY_API_KEY=your_ebay_api_key
ETSY_API_KEY=your_etsy_api_key
SHOPIFY_API_KEY=your_shopify_api_key
```

### **Step 2: Update Knowledge Sources Configuration**
```php
// In app/Services/ContinuousKnowledgeService.php
protected $knowledgeSources = [
    'news_apis' => [
        'newsapi' => env('NEWS_API_KEY'),
        'guardian' => env('GUARDIAN_API_KEY'),
        'nyt' => env('NYT_API_KEY'),
        'reuters' => env('REUTERS_API_KEY'),
        'bloomberg' => env('BLOOMBERG_API_KEY'),
        'cnbc' => env('CNBC_API_KEY'),
        'forbes' => env('FORBES_API_KEY'),
        'techcrunch' => env('TECHCRUNCH_API_KEY')
    ],
    'social_media' => [
        'twitter' => env('TWITTER_API_KEY'),
        'instagram' => env('INSTAGRAM_API_KEY'),
        'facebook' => env('FACEBOOK_API_KEY'),
        'linkedin' => env('LINKEDIN_API_KEY'),
        'tiktok' => env('TIKTOK_API_KEY'),
        'youtube' => env('YOUTUBE_API_KEY'),
        'reddit' => null, // Free
        'hackernews' => null // Free
    ],
    'market_data' => [
        'alphavantage' => env('ALPHA_VANTAGE_API_KEY'),
        'finnhub' => env('FINNHUB_API_KEY'),
        'yahoo_finance' => null, // Free
        'coingecko' => null // Free
    ],
    'industry_reports' => [
        'ibisworld' => env('IBISWORLD_API_KEY'),
        'statista' => env('STATISTA_API_KEY'),
        'pitchbook' => env('PITCHBOOK_API_KEY'),
        'crunchbase' => env('CRUNCHBASE_API_KEY')
    ],
    'ecommerce_platforms' => [
        'amazon' => env('AMAZON_API_KEY'),
        'ebay' => env('EBAY_API_KEY'),
        'etsy' => env('ETSY_API_KEY'),
        'shopify' => env('SHOPIFY_API_KEY')
    ],
    'review_platforms' => [
        'trustpilot' => env('TRUSTPILOT_API_KEY'),
        'yelp' => env('YELP_API_KEY'),
        'google_reviews' => env('GOOGLE_PLACES_API_KEY'),
        'glassdoor' => env('GLASSDOOR_API_KEY'),
        'indeed' => env('INDEED_API_KEY')
    ],
    'analytics_platforms' => [
        'semrush' => env('SEMRUSH_API_KEY'),
        'similarweb' => env('SIMILARWEB_API_KEY'),
        'alexa' => env('ALEXA_API_KEY'),
        'moz' => env('MOZ_API_KEY'),
        'ahrefs' => env('AHREFS_API_KEY')
    ],
    'business_directories' => [
        'crunchbase' => env('CRUNCHBASE_API_KEY'),
        'angellist' => env('ANGELLIST_API_KEY'),
        'pitchbook' => env('PITCHBOOK_API_KEY'),
        'bloomberg' => env('BLOOMBERG_API_KEY'),
        'reuters' => env('REUTERS_API_KEY')
    ],
    'app_stores' => [
        'appstore' => null, // Free
        'playstore' => null, // Free
        'microsoft_store' => null // Free
    ],
    'developer_platforms' => [
        'github' => null, // Free
        'gitlab' => null, // Free
        'stackoverflow' => null, // Free
        'npm' => null, // Free
        'pypi' => null // Free
    ]
];
```

### **Step 3: Add New Data Collection Methods**
```php
// In app/Services/ContinuousKnowledgeService.php

/**
 * Collect data from new sources
 */
public function gatherDataFromNewSources()
{
    // E-commerce data
    $this->gatherEcommerceData();
    
    // Review data
    $this->gatherReviewData();
    
    // Analytics data
    $this->gatherAnalyticsData();
    
    // Business directory data
    $this->gatherBusinessDirectoryData();
    
    // App store data
    $this->gatherAppStoreData();
    
    // Developer platform data
    $this->gatherDeveloperPlatformData();
}

/**
 * Gather e-commerce data
 */
protected function gatherEcommerceData()
{
    // Amazon product data
    if ($this->apiKeys['amazon']) {
        $this->gatherAmazonData();
    }
    
    // eBay marketplace data
    if ($this->apiKeys['ebay']) {
        $this->gatherEbayData();
    }
    
    // Etsy handmade data
    if ($this->apiKeys['etsy']) {
        $this->gatherEtsyData();
    }
    
    // Shopify store data
    if ($this->apiKeys['shopify']) {
        $this->gatherShopifyData();
    }
}

/**
 * Gather review data
 */
protected function gatherReviewData()
{
    // Trustpilot reviews
    if ($this->apiKeys['trustpilot']) {
        $this->gatherTrustpilotData();
    }
    
    // Yelp reviews
    if ($this->apiKeys['yelp']) {
        $this->gatherYelpData();
    }
    
    // Google reviews
    if ($this->apiKeys['google_reviews']) {
        $this->gatherGoogleReviewsData();
    }
    
    // Glassdoor reviews
    if ($this->apiKeys['glassdoor']) {
        $this->gatherGlassdoorData();
    }
}

/**
 * Gather analytics data
 */
protected function gatherAnalyticsData()
{
    // SEMrush SEO data
    if ($this->apiKeys['semrush']) {
        $this->gatherSemrushData();
    }
    
    // SimilarWeb traffic data
    if ($this->apiKeys['similarweb']) {
        $this->gatherSimilarWebData();
    }
    
    // Alexa rankings
    if ($this->apiKeys['alexa']) {
        $this->gatherAlexaData();
    }
    
    // Moz SEO data
    if ($this->apiKeys['moz']) {
        $this->gatherMozData();
    }
    
    // Ahrefs backlink data
    if ($this->apiKeys['ahrefs']) {
        $this->gatherAhrefsData();
    }
}
```

### **Step 4: Add Competitor Analysis Dashboard**
```php
// In app/Http/Controllers/CompetitorAnalysisController.php

class CompetitorAnalysisController extends Controller
{
    protected $competitorService;
    
    public function __construct(CompetitorAnalysisService $competitorService)
    {
        $this->competitorService = $competitorService;
    }
    
    /**
     * Show competitor analysis dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $businesses = $user->businesses;
        
        return view('competitor.dashboard', compact('businesses'));
    }
    
    /**
     * Analyze competitors for a business
     */
    public function analyzeCompetitors(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'competitors' => 'nullable|array'
        ]);
        
        $businessId = $request->input('business_id');
        $competitors = $request->input('competitors', []);
        
        $analysis = $this->competitorService->analyzeCompetitors($businessId, $competitors);
        
        return response()->json([
            'success' => true,
            'analysis' => $analysis
        ]);
    }
    
    /**
     * Get competitor analysis results
     */
    public function getAnalysis($businessId)
    {
        $analysis = DB::table('competitor_analysis')
            ->where('business_id', $businessId)
            ->orderBy('created_at', 'desc')
            ->first();
        
        return response()->json([
            'success' => true,
            'analysis' => $analysis ? json_decode($analysis->analysis_data, true) : null
        ]);
    }
}
```

### **Step 5: Create Competitor Analysis Dashboard View**
```php
// resources/views/competitor/dashboard.blade.php

@extends('layouts.dash')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-chart-line"></i> Competitor Analysis Dashboard</h4>
                    <p class="text-muted">Analyze your competitors from multiple sources</p>
                </div>
                <div class="card-body">
                    <!-- Business Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="business-select">Select Business:</label>
                            <select id="business-select" class="form-select">
                                <option value="">Choose a business...</option>
                                @foreach($businesses as $business)
                                    <option value="{{ $business->id }}">{{ $business->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="competitors-input">Competitors (optional):</label>
                            <input type="text" id="competitors-input" class="form-control" 
                                   placeholder="Enter competitor names separated by commas">
                        </div>
                    </div>
                    
                    <!-- Analysis Controls -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <button class="btn btn-primary" onclick="analyzeCompetitors()">
                                <i class="fas fa-search"></i> Analyze Competitors
                            </button>
                            <button class="btn btn-secondary" onclick="loadPreviousAnalysis()">
                                <i class="fas fa-history"></i> Load Previous Analysis
                            </button>
                        </div>
                    </div>
                    
                    <!-- Analysis Results -->
                    <div id="analysis-results" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Competitor Analysis Results</h5>
                                <div id="competitors-list"></div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6>Market Insights</h6>
                                <div id="market-insights"></div>
                            </div>
                            <div class="col-md-6">
                                <h6>Recommendations</h6>
                                <div id="recommendations"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function analyzeCompetitors() {
    const businessId = document.getElementById('business-select').value;
    const competitors = document.getElementById('competitors-input').value;
    
    if (!businessId) {
        alert('Please select a business first');
        return;
    }
    
    const competitorsArray = competitors ? competitors.split(',').map(c => c.trim()) : [];
    
    fetch('/competitor/analyze', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            business_id: businessId,
            competitors: competitorsArray
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayAnalysisResults(data.analysis);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error analyzing competitors: ' + error);
    });
}

function displayAnalysisResults(analysis) {
    document.getElementById('analysis-results').style.display = 'block';
    
    // Display competitors
    const competitorsList = document.getElementById('competitors-list');
    competitorsList.innerHTML = '';
    
    analysis.competitors.forEach(competitor => {
        const competitorDiv = document.createElement('div');
        competitorDiv.className = 'card mb-3';
        competitorDiv.innerHTML = `
            <div class="card-body">
                <h6>${competitor.name}</h6>
                <div class="row">
                    <div class="col-md-3">
                        <strong>Strengths:</strong>
                        <ul>${competitor.strengths.map(s => `<li>${s}</li>`).join('')}</ul>
                    </div>
                    <div class="col-md-3">
                        <strong>Weaknesses:</strong>
                        <ul>${competitor.weaknesses.map(w => `<li>${w}</li>`).join('')}</ul>
                    </div>
                    <div class="col-md-3">
                        <strong>Opportunities:</strong>
                        <ul>${competitor.opportunities.map(o => `<li>${o}</li>`).join('')}</ul>
                    </div>
                    <div class="col-md-3">
                        <strong>Threats:</strong>
                        <ul>${competitor.threats.map(t => `<li>${t}</li>`).join('')}</ul>
                    </div>
                </div>
            </div>
        `;
        competitorsList.appendChild(competitorDiv);
    });
    
    // Display market insights
    const marketInsights = document.getElementById('market-insights');
    marketInsights.innerHTML = `
        <div class="card">
            <div class="card-body">
                <p><strong>Market Position:</strong> ${analysis.market_insights.market_position}</p>
                <p><strong>Competitive Landscape:</strong> ${analysis.market_insights.competitive_landscape}</p>
                <p><strong>Pricing Trends:</strong> ${analysis.market_insights.pricing_trends}</p>
            </div>
        </div>
    `;
    
    // Display recommendations
    const recommendations = document.getElementById('recommendations');
    recommendations.innerHTML = `
        <div class="card">
            <div class="card-body">
                <p><strong>Pricing Strategy:</strong> ${analysis.recommendations.pricing_strategy}</p>
                <p><strong>Feature Development:</strong> ${analysis.recommendations.feature_development}</p>
                <p><strong>Marketing Strategy:</strong> ${analysis.recommendations.marketing_strategy}</p>
            </div>
        </div>
    `;
}

function loadPreviousAnalysis() {
    const businessId = document.getElementById('business-select').value;
    
    if (!businessId) {
        alert('Please select a business first');
        return;
    }
    
    fetch(`/competitor/analysis/${businessId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.analysis) {
            displayAnalysisResults(data.analysis);
        } else {
            alert('No previous analysis found for this business');
        }
    })
    .catch(error => {
        alert('Error loading previous analysis: ' + error);
    });
}
</script>
@endsection
```

## 📊 **Free vs Paid Sources**

### **Free Sources (No API Key Required):**
- ✅ Reddit API
- ✅ HackerNews API
- ✅ ProductHunt API
- ✅ IndieHackers API
- ✅ Apple App Store API
- ✅ Google Play Store API
- ✅ GitHub API
- ✅ GitLab API
- ✅ Stack Overflow API
- ✅ NPM API
- ✅ PyPI API
- ✅ Yahoo Finance API
- ✅ CoinGecko API

### **Paid Sources (API Key Required):**
- 💰 News APIs (NewsAPI, Guardian, NYT, etc.)
- 💰 Social Media APIs (Twitter, Instagram, LinkedIn, etc.)
- 💰 Market Data APIs (Alpha Vantage, Finnhub, etc.)
- 💰 Analytics APIs (SEMrush, SimilarWeb, Ahrefs, etc.)
- 💰 Business Directory APIs (Crunchbase, PitchBook, etc.)
- 💰 Review Platform APIs (Trustpilot, Yelp, Glassdoor, etc.)
- 💰 E-commerce APIs (Amazon, eBay, Etsy, Shopify, etc.)

## 🎯 **Recommended Setup for Shared Hosting**

### **Free Sources to Start With:**
1. **Reddit** - Community discussions and trends
2. **HackerNews** - Technology trends
3. **GitHub** - Open source projects and trends
4. **App Stores** - Mobile app market data
5. **Yahoo Finance** - Financial market data
6. **Stack Overflow** - Developer trends

### **Paid Sources to Add Later:**
1. **NewsAPI** - Comprehensive news coverage
2. **Twitter API** - Real-time social media trends
3. **SEMrush** - SEO and competitive data
4. **Crunchbase** - Company information
5. **Trustpilot** - Customer reviews

## 🚀 **Implementation Steps**

1. **Add API keys to .env file**
2. **Update knowledge sources configuration**
3. **Add new data collection methods**
4. **Create competitor analysis controller**
5. **Create competitor analysis dashboard**
6. **Test with free sources first**
7. **Add paid sources gradually**

This setup gives you comprehensive competitor analysis using both free and paid sources, perfect for shared hosting environments!

