# Quick Start Guide: Free Social Media Platforms

## 🚀 Get Started in 15 Minutes with FREE Platforms

This guide will help you set up the three easiest and completely FREE social media platforms: **Telegram**, **Discord**, and **Reddit**.

---

## 1. Telegram Bot (100% FREE - No Limits)

### Step 1: Create a Telegram Bot
1. Open Telegram and search for `@BotFather`
2. Start a chat and send: `/newbot`
3. Choose a name for your bot (e.g., "Shopybook Marketing Bot")
4. Choose a username ending in "bot" (e.g., "shopybookbot")
5. Copy the **Bot Token** provided

### Step 2: Add to Your .env
```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
```

### Step 3: Test Your Bot
- Search for your bot in Telegram
- Send `/start` to activate it
- Your bot can now post to channels it's added to!

**Time to setup: 3 minutes**

---

## 2. Discord Bot (100% FREE - Generous Limits)

### Step 1: Create Discord Application
1. Go to https://discord.com/developers/applications
2. Click "New Application"
3. Name it "Shopybook Bot"
4. Go to "Bot" section
5. Click "Add Bot"
6. Copy the **Bot Token**

### Step 2: Add to Your .env
```env
DISCORD_BOT_TOKEN=your_bot_token_here
```

### Step 3: Invite Bot to Your Server
1. Go to "OAuth2" > "URL Generator"
2. Select scopes: `bot`
3. Select permissions: `Send Messages`, `Manage Messages`
4. Use the generated URL to invite your bot

**Time to setup: 5 minutes**

---

## 3. Reddit API (FREE with Rate Limits)

### Step 1: Create Reddit App
1. Go to https://www.reddit.com/prefs/apps
2. Click "Create App" or "Create Another App"
3. Choose "script" type
4. Name: "Shopybook Marketing"
5. Description: "Social media automation"
6. Redirect URI: `http://localhost:8000/auth/reddit/callback`

### Step 2: Add to Your .env
```env
REDDIT_CLIENT_ID=your_client_id_here
REDDIT_CLIENT_SECRET=your_client_secret_here
REDDIT_REDIRECT_URI=http://localhost:8000/auth/reddit/callback
```

**Time to setup: 4 minutes**

---

## 🎯 Environment Setup (.env file)

Add these to your `.env` file:

```env
# Telegram (FREE)
TELEGRAM_BOT_TOKEN=your_telegram_bot_token

# Discord (FREE)
DISCORD_BOT_TOKEN=your_discord_bot_token

# Reddit (FREE with limits)
REDDIT_CLIENT_ID=your_reddit_client_id
REDDIT_CLIENT_SECRET=your_reddit_client_secret
REDDIT_REDIRECT_URI=http://localhost:8000/auth/reddit/callback
```

---

## 🚀 Quick Test

1. **Clear Laravel cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Visit your social media page:**
   ```
   http://localhost:8000/marketing/social-media
   ```

3. **Connect accounts:**
   - Click "Connect" for each platform
   - Authorize your bots/apps
   - Start posting!

---

## 📝 Usage Examples

### Telegram
- Post to channels your bot is admin of
- Send messages to users who started your bot
- Schedule marketing messages

### Discord
- Post to channels in servers where your bot has permissions
- Create automated announcements
- Engage with gaming communities

### Reddit
- Post to relevant subreddits (follow each subreddit's rules!)
- Share product announcements
- Engage in community discussions

---

## 💡 Pro Tips

1. **Start with Telegram** - It's the easiest and most reliable
2. **Test with small posts** before automating everything
3. **Follow platform rules** to avoid getting banned
4. **Engage authentically** - don't just spam promotional content

---

## 🔧 Next Steps (Optional Business Platforms)

Once you're comfortable with the free platforms, you can add:
- **Twitter/X API** (Paid: $100/month)
- **Facebook/Instagram API** (Free but requires business verification)
- **LinkedIn API** (Apply for partnership program)
- **TikTok API** (Apply for business access)

---

## 🆘 Troubleshooting

### Common Issues:

1. **"Bot token not found"**
   - Make sure you added the token to `.env`
   - Run `php artisan config:clear`

2. **"Unauthorized"**
   - Check if your bot has permissions
   - For Discord: reinvite with correct permissions
   - For Telegram: make sure bot is admin of the channel

3. **"Route not found"**
   - Make sure you've run `php artisan route:clear`
   - Check that your Laravel server is running

### Get Help:
- Check Laravel logs: `storage/logs/laravel.log`
- Test individual APIs using their documentation
- Use Postman to test API endpoints manually

---

## 🎉 You're Ready!

With these three platforms set up, you can:
- ✅ Automatically post to Telegram channels
- ✅ Send Discord announcements  
- ✅ Share content on Reddit
- ✅ Schedule posts across all platforms
- ✅ Track engagement and analytics

**Total setup time: ~12 minutes**

Start with these free platforms and expand to paid ones as your business grows!
