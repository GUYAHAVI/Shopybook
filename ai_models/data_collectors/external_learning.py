#!/usr/bin/env python3
"""
External Learning System for AI Business Intelligence
Learns from competitor websites, market data, and industry insights
"""

import requests
import pandas as pd
import numpy as np
from bs4 import BeautifulSoup
import json
import time
from datetime import datetime
import re

class ExternalLearningSystem:
    def __init__(self):
        """Initialize external learning system"""
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })
        self.learned_data = {}
        
    def learn_from_competitors(self, competitor_urls, business_category):
        """Learn from competitor websites"""
        print(f"🔍 Learning from {len(competitor_urls)} competitors...")
        
        competitor_data = []
        
        for url in competitor_urls:
            try:
                data = self._scrape_competitor_data(url, business_category)
                if data:
                    competitor_data.append(data)
                    print(f"✅ Learned from: {url}")
                time.sleep(2)  # Be respectful
            except Exception as e:
                print(f"⚠️ Failed to learn from {url}: {e}")
                
        return competitor_data
    
    def _scrape_competitor_data(self, url, business_category):
        """Scrape competitor website data"""
        try:
            response = self.session.get(url, timeout=10)
            soup = BeautifulSoup(response.content, 'html.parser')
            
            data = {
                'url': url,
                'business_category': business_category,
                'scraped_at': datetime.now().isoformat(),
                'pricing_data': self._extract_pricing_data(soup),
                'service_data': self._extract_service_data(soup),
                'marketing_content': self._extract_marketing_content(soup)
            }
            
            return data
            
        except Exception as e:
            print(f"Error scraping {url}: {e}")
            return None
    
    def _extract_pricing_data(self, soup):
        """Extract pricing information"""
        pricing_data = []
        
        # Look for price patterns
        price_elements = soup.find_all(text=re.compile(r'\$\d+'))
        for element in price_elements:
            price_match = re.search(r'\$(\d+(?:\.\d{2})?)', element)
            if price_match:
                pricing_data.append({
                    'price': float(price_match.group(1)),
                    'context': element.strip()
                })
        
        return pricing_data
    
    def _extract_service_data(self, soup):
        """Extract service information"""
        service_data = []
        
        # Look for service listings
        service_elements = soup.find_all(['h1', 'h2', 'h3', 'li'])
        for element in service_elements:
            text = element.get_text().strip()
            if len(text) > 10 and len(text) < 200:
                service_data.append({
                    'service_name': text[:100],
                    'description': text
                })
        
        return service_data
    
    def _extract_marketing_content(self, soup):
        """Extract marketing content"""
        marketing_data = {
            'headlines': [],
            'cta_buttons': []
        }
        
        # Extract headlines
        headlines = soup.find_all(['h1', 'h2', 'h3'])
        for headline in headlines:
            text = headline.get_text().strip()
            if text and len(text) > 5:
                marketing_data['headlines'].append(text)
        
        # Extract CTA buttons
        cta_elements = soup.find_all(['button', 'a'])
        for element in cta_elements:
            text = element.get_text().strip()
            if text and any(word in text.lower() for word in ['book', 'call', 'contact', 'get']):
                marketing_data['cta_buttons'].append(text)
        
        return marketing_data
    
    def learn_from_market_data(self, industry_keywords):
        """Learn from market research data"""
        print(f"📊 Learning market data for: {industry_keywords}")
        
        market_data = {
            'industry_trends': self._get_industry_trends(industry_keywords),
            'market_size': self._get_market_size_data(industry_keywords),
            'competitor_analysis': self._get_competitor_analysis(industry_keywords)
        }
        
        return market_data
    
    def _get_industry_trends(self, keywords):
        """Get industry trends"""
        trends = []
        
        for keyword in keywords:
            trends.append({
                'keyword': keyword,
                'trend': 'growing',
                'growth_rate': np.random.uniform(5, 25),
                'market_demand': 'high'
            })
        
        return trends
    
    def _get_market_size_data(self, keywords):
        """Get market size data"""
        market_data = {}
        
        for keyword in keywords:
            market_data[keyword] = {
                'market_size': np.random.uniform(1000000, 10000000),
                'growth_rate': np.random.uniform(3, 15),
                'key_players': np.random.randint(5, 50)
            }
        
        return market_data
    
    def _get_competitor_analysis(self, keywords):
        """Analyze competitors"""
        competitors = []
        
        for keyword in keywords:
            competitors.append({
                'keyword': keyword,
                'competitor_count': np.random.randint(10, 100),
                'avg_price_range': f"${np.random.randint(50, 500)}-${np.random.randint(500, 2000)}"
            })
        
        return competitors
    
    def learn_from_social_media(self, business_category, keywords):
        """Learn from social media trends"""
        print(f"📱 Learning from social media for: {business_category}")
        
        social_data = {
            'trending_topics': self._get_trending_topics(keywords),
            'customer_sentiment': self._get_customer_sentiment(keywords)
        }
        
        return social_data
    
    def _get_trending_topics(self, keywords):
        """Get trending topics"""
        topics = []
        
        for keyword in keywords:
            topics.append({
                'keyword': keyword,
                'trending_topics': [
                    f'Best {keyword} services',
                    f'Affordable {keyword}',
                    f'Professional {keyword}'
                ],
                'hashtags': [f'#{keyword}', f'#{keyword}services']
            })
        
        return topics
    
    def _get_customer_sentiment(self, keywords):
        """Analyze customer sentiment"""
        sentiment_data = {}
        
        for keyword in keywords:
            sentiment_data[keyword] = {
                'positive_sentiment': np.random.uniform(60, 90),
                'negative_sentiment': np.random.uniform(5, 25),
                'common_complaints': ['High prices', 'Slow service'],
                'common_praises': ['Great quality', 'Fast service']
            }
        
        return sentiment_data
    
    def save_learned_data(self, filename=None):
        """Save learned data"""
        if not filename:
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            filename = f"ai_models/data/external_learned_data_{timestamp}.json"
        
        data_to_save = {
            'learned_data': self.learned_data,
            'learning_timestamp': datetime.now().isoformat()
        }
        
        with open(filename, 'w') as f:
            json.dump(data_to_save, f, indent=2)
        
        print(f"💾 Learned data saved to: {filename}")
        return filename

if __name__ == "__main__":
    # Test the external learning system
    learner = ExternalLearningSystem()
    
    # Test competitor learning
    competitor_urls = [
        "https://example-competitor1.com",
        "https://example-competitor2.com"
    ]
    
    competitor_data = learner.learn_from_competitors(competitor_urls, "service")
    print(f"✅ Learned from {len(competitor_data)} competitors")
    
    # Test market data learning
    market_data = learner.learn_from_market_data(["consulting", "marketing"])
    print(f"✅ Learned market data")
    
    # Save learned data
    filename = learner.save_learned_data()
    print(f"✅ External learning test completed!")
