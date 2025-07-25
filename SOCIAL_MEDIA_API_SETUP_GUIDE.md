# Social Media API Configuration Guide for Shopybook

This guide will help you obtain API keys for all supported social media platforms, starting with free ones.

## 🆓 FREE PLATFORMS (No Review Required)

### 1. TELEGRAM BOT API
**Cost:** Free  
**Setup Time:** 2-3 minutes  
**Requirements:** Telegram account

**Steps:**
1. Open Telegram and search for `@BotFather`
2. Send `/start` then `/newbot`
3. Choose a name and username for your bot
4. Copy the bot token provided
5. Add to `.env`: `TELEGRAM_BOT_TOKEN=your_bot_token`

**Redirect URI:** Not needed (uses bot token)

---

### 2. DISCORD WEBHOOKS
**Cost:** Free  
**Setup Time:** 1-2 minutes  
**Requirements:** Discord account

**Steps:**
1. Go to [Discord Developer Portal](https://discord.com/developers/applications)
2. Click "New Application"
3. Name your application "Shopybook Integration"
4. Go to OAuth2 > General
5. Copy Client ID and Client Secret
6. Add redirect URI: `https://yourapp.com/social/callback/discord`

**Environment Variables:**
```
DISCORD_CLIENT_ID=your_client_id
DISCORD_CLIENT_SECRET=your_client_secret
```

---

### 3. REDDIT API
**Cost:** Free  
**Setup Time:** 5 minutes  
**Requirements:** Reddit account (14+ days old)

**Steps:**
1. Go to [Reddit App Preferences](https://www.reddit.com/prefs/apps)
2. Click "Create App" or "Create Another App"
3. Choose "web app"
4. Name: "Shopybook Social Media Manager"
5. Description: "Social media automation for businesses"
6. About URL: Your website
7. Redirect URI: `https://yourapp.com/social/callback/reddit`
8. Copy Client ID (under app name) and Secret

**Environment Variables:**
```
REDDIT_CLIENT_ID=your_client_id
REDDIT_CLIENT_SECRET=your_client_secret
REDDIT_USER_AGENT="Shopybook Social Media Manager v1.0"
```

---

## 💳 EASY APPROVAL PLATFORMS

### 4. TWITTER API (X)
**Cost:** $100/month for basic plan  
**Setup Time:** 1-2 hours  
**Requirements:** Phone-verified Twitter account

**Steps:**
1. Go to [Twitter Developer Portal](https://developer.twitter.com/)
2. Sign up for developer account
3. Create a new project and app
4. Generate API keys in "Keys and Tokens"
5. Add redirect URI: `https://yourapp.com/social/callback/twitter`

**Environment Variables:**
```
TWITTER_CLIENT_ID=your_client_id
TWITTER_CLIENT_SECRET=your_client_secret
TWITTER_BEARER_TOKEN=your_bearer_token
```

---

### 5. LINKEDIN API
**Cost:** Free  
**Setup Time:** 30 minutes  
**Requirements:** LinkedIn account

**Steps:**
1. Go to [LinkedIn Developer Portal](https://www.linkedin.com/developers/)
2. Create new app
3. Fill company information
4. Verify company page (may require LinkedIn Company Page)
5. Request access to "Share on LinkedIn" API
6. Add redirect URI: `https://yourapp.com/social/callback/linkedin`

**Environment Variables:**
```
LINKEDIN_CLIENT_ID=your_client_id
LINKEDIN_CLIENT_SECRET=your_client_secret
```

---

### 6. PINTEREST API
**Cost:** Free  
**Setup Time:** 20 minutes  
**Requirements:** Pinterest business account

**Steps:**
1. Convert to Pinterest Business account (free)
2. Go to [Pinterest Developers](https://developers.pinterest.com/)
3. Create new app
4. Add redirect URI: `https://yourapp.com/social/callback/pinterest`
5. Copy App ID and App Secret

**Environment Variables:**
```
PINTEREST_CLIENT_ID=your_app_id
PINTEREST_CLIENT_SECRET=your_app_secret
```

---

## 🔐 BUSINESS VERIFICATION REQUIRED

### 7. FACEBOOK & INSTAGRAM (META)
**Cost:** Free  
**Setup Time:** 1-2 weeks (review process)  
**Requirements:** Business verification

**Steps:**
1. Go to [Meta for Developers](https://developers.facebook.com/)
2. Create new app > Business type
3. Add Facebook Login and Instagram Basic Display products
4. Submit for Business Verification
5. Add redirect URI: `https://yourapp.com/social/callback/facebook`

**Environment Variables:**
```
FACEBOOK_CLIENT_ID=your_app_id
FACEBOOK_CLIENT_SECRET=your_app_secret
FACEBOOK_APP_ID=your_app_id
INSTAGRAM_CLIENT_ID=your_app_id
INSTAGRAM_CLIENT_SECRET=your_app_secret
```

---

### 8. YOUTUBE API (GOOGLE)
**Cost:** Free  
**Setup Time:** 30 minutes  
**Requirements:** Google account

**Steps:**
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create new project or select existing
3. Enable YouTube Data API v3
4. Create credentials (OAuth 2.0 Client ID)
5. Add redirect URI: `https://yourapp.com/social/callback/youtube`

**Environment Variables:**
```
YOUTUBE_CLIENT_ID=your_google_client_id
YOUTUBE_CLIENT_SECRET=your_google_client_secret
YOUTUBE_API_KEY=your_youtube_api_key
```

---

### 9. TIKTOK API
**Cost:** Free  
**Setup Time:** 2-4 weeks (review process)  
**Requirements:** Business verification

**Steps:**
1. Go to [TikTok for Developers](https://developers.tiktok.com/)
2. Register as developer
3. Create new app
4. Submit for review (requires business justification)
5. Add redirect URI: `https://yourapp.com/social/callback/tiktok`

**Environment Variables:**
```
TIKTOK_CLIENT_ID=your_client_key
TIKTOK_CLIENT_SECRET=your_client_secret
```

---

## 🏢 ENTERPRISE/RESTRICTED PLATFORMS

### 10. WHATSAPP BUSINESS API
**Cost:** Usage-based pricing  
**Setup Time:** 2-4 weeks  
**Requirements:** Business verification + approval

**Steps:**
1. Go to [Meta for Developers](https://developers.facebook.com/)
2. Create business app
3. Add WhatsApp Business API product
4. Complete business verification
5. Request access (requires business use case)

**Environment Variables:**
```
WHATSAPP_APP_ID=your_app_id
WHATSAPP_APP_SECRET=your_app_secret
WHATSAPP_VERIFY_TOKEN=your_verify_token
```

---

### 11. SNAPCHAT API
**Cost:** Advertising focused  
**Setup Time:** Not applicable for organic posts  
**Note:** Snapchat doesn't allow organic posting via API

---

## 🚀 QUICK START GUIDE

### Phase 1: Start with Free Platforms (Today)
1. ✅ **Telegram** - Set up in 5 minutes
2. ✅ **Discord** - Set up in 10 minutes  
3. ✅ **Reddit** - Set up in 15 minutes

### Phase 2: Easy Approvals (This Week)
4. **Twitter** - If budget allows ($100/month)
5. **LinkedIn** - Free, requires company page
6. **Pinterest** - Free with business account

### Phase 3: Business Platforms (2-4 weeks)
7. **Facebook & Instagram** - Business verification required
8. **YouTube** - Google Cloud setup
9. **TikTok** - Business review process

### Phase 4: Enterprise (1-2 months)
10. **WhatsApp Business** - Enterprise approval

---

## 📋 IMPLEMENTATION CHECKLIST

### Immediate Setup (Today)
- [ ] Set up Telegram bot
- [ ] Create Discord application  
- [ ] Register Reddit application
- [ ] Test basic posting functionality

### Development Setup
- [ ] Copy environment variables to `.env`
- [ ] Test OAuth flows for each platform
- [ ] Verify webhook endpoints work
- [ ] Test content publishing

### Production Preparation
- [ ] Set up proper domain with SSL
- [ ] Configure production redirect URIs
- [ ] Prepare business verification documents
- [ ] Set up monitoring and error handling

---

## 💡 PRO TIPS

### Free Platform Priority
Start with **Telegram, Discord, and Reddit** since they:
- Have no approval process
- Are completely free
- Have excellent APIs
- Support media uploads
- Have active business communities

### Cost-Effective Strategy
1. Begin with free platforms to validate the feature
2. Add Twitter if your audience is there ($100/month)
3. Focus on Facebook/Instagram for main social presence
4. Add YouTube for video content
5. Consider TikTok for younger demographics

### Business Verification Tips
- Use consistent business information across all platforms
- Have a professional website ready
- Prepare business registration documents
- Create detailed use case descriptions
- Be patient with review processes

This guide prioritizes platforms by ease of setup and cost, helping you launch social media automation quickly while building toward comprehensive platform support.
