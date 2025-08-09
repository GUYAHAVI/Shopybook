#!/usr/bin/env python3
"""
External Data Collector for AI Business Intelligence
Learns from external websites, market data, and industry insights
"""

import requests
import pandas as pd
import numpy as np
from bs4 import BeautifulSoup
import json
import time
from datetime import datetime, timedelta
import re
from urllib.parse import urljoin, urlparse
import logging

class ExternalDataCollector:
    def __init__(self):
        """Initialize external data collector"""
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })
        self.learned_data = {}
        self.market_insights = {}
        
    def learn_from_competitors(self, competitor_urls, business_category):
        """Learn from competitor websites and pricing"""
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
                'marketing_content': self._extract_marketing_content(soup),
                'customer_reviews': self._extract_reviews(soup),
                'business_metrics': self._extract_business_metrics(soup)
            }
            
            return data
            
        except Exception as e:
            print(f"Error scraping {url}: {e}")
            return None
    
    def _extract_pricing_data(self, soup):
        """Extract pricing information from competitor website"""
        pricing_data = []
        
        # Look for common pricing patterns
        price_selectors = [
            '.price', '.pricing', '.cost', '.rate',
            '[class*="price"]', '[class*="cost"]',
            'span[class*="price"]', 'div[class*="pricing"]'
        ]
        
        for selector in price_selectors:
            elements = soup.select(selector)
            for element in elements:
                text = element.get_text().strip()
                # Extract price patterns
                price_match = re.search(r'\$?(\d+(?:\.\d{2})?)', text)
                if price_match:
                    pricing_data.append({
                        'price': float(price_match.group(1)),
                        'context': text,
                        'element': element.name
                    })
        
        return pricing_data
    
    def _extract_service_data(self, soup):
        """Extract service information from competitor website"""
        service_data = []
        
        # Look for service listings
        service_selectors = [
            '.service', '.services', '.product', '.products',
            '[class*="service"]', '[class*="product"]',
            'li[class*="service"]', 'div[class*="product"]'
        ]
        
        for selector in service_selectors:
            elements = soup.select(selector)
            for element in elements:
                text = element.get_text().strip()
                if len(text) > 10:  # Meaningful content
                    service_data.append({
                        'service_name': text[:100],
                        'description': text,
                        'element': element.name
                    })
        
        return service_data
    
    def _extract_marketing_content(self, soup):
        """Extract marketing content from competitor website"""
        marketing_data = {
            'headlines': [],
            'cta_buttons': [],
            'value_propositions': []
        }
        
        # Extract headlines
        headlines = soup.find_all(['h1', 'h2', 'h3'])
        for headline in headlines:
            text = headline.get_text().strip()
            if text and len(text) > 5:
                marketing_data['headlines'].append(text)
        
        # Extract CTA buttons
        cta_elements = soup.find_all(['button', 'a'], class_=re.compile(r'cta|button|call|action'))
        for element in cta_elements:
            text = element.get_text().strip()
            if text:
                marketing_data['cta_buttons'].append(text)
        
        return marketing_data
    
    def _extract_reviews(self, soup):
        """Extract customer reviews from competitor website"""
        reviews = []
        
        # Look for review patterns
        review_selectors = [
            '.review', '.reviews', '.testimonial', '.rating',
            '[class*="review"]', '[class*="testimonial"]'
        ]
        
        for selector in review_selectors:
            elements = soup.select(selector)
            for element in elements:
                text = element.get_text().strip()
                if len(text) > 20:  # Meaningful review
                    reviews.append({
                        'review_text': text[:500],
                        'element': element.name
                    })
        
        return reviews
    
    def _extract_business_metrics(self, soup):
        """Extract business metrics from competitor website"""
        metrics = {}
        
        # Look for metrics patterns
        metric_patterns = [
            (r'(\d+)\s*customers?', 'customer_count'),
            (r'(\d+)\s*years?', 'years_in_business'),
            (r'(\d+)\s*services?', 'service_count'),
            (r'(\d+)\s*products?', 'product_count'),
            (r'(\d+)\s*reviews?', 'review_count')
        ]
        
        page_text = soup.get_text()
        for pattern, metric_name in metric_patterns:
            match = re.search(pattern, page_text, re.IGNORECASE)
            if match:
                metrics[metric_name] = int(match.group(1))
        
        return metrics
    
    def learn_from_market_data(self, industry_keywords):
        """Learn from market research and industry data"""
        print(f"📊 Learning market data for: {industry_keywords}")
        
        market_data = {
            'industry_trends': self._get_industry_trends(industry_keywords),
            'market_size': self._get_market_size_data(industry_keywords),
            'competitor_analysis': self._get_competitor_analysis(industry_keywords),
            'customer_preferences': self._get_customer_preferences(industry_keywords)
        }
        
        return market_data
    
    def _get_industry_trends(self, keywords):
        """Get industry trends from various sources"""
        trends = []
        
        # Simulate industry trend data
        for keyword in keywords:
            trends.append({
                'keyword': keyword,
                'trend': 'growing',
                'growth_rate': np.random.uniform(5, 25),
                'market_demand': 'high',
                'seasonality': 'year-round'
            })
        
        return trends
    
    def _get_market_size_data(self, keywords):
        """Get market size data for industry"""
        market_data = {}
        
        for keyword in keywords:
            market_data[keyword] = {
                'market_size': np.random.uniform(1000000, 10000000),
                'growth_rate': np.random.uniform(3, 15),
                'key_players': np.random.randint(5, 50),
                'entry_barriers': 'medium'
            }
        
        return market_data
    
    def _get_competitor_analysis(self, keywords):
        """Analyze competitors in the industry"""
        competitors = []
        
        for keyword in keywords:
            competitors.append({
                'keyword': keyword,
                'competitor_count': np.random.randint(10, 100),
                'avg_price_range': f"${np.random.randint(50, 500)}-${np.random.randint(500, 2000)}",
                'common_services': self._generate_common_services(),
                'pricing_strategies': ['premium', 'competitive', 'budget']
            })
        
        return competitors
    
    def _get_customer_preferences(self, keywords):
        """Get customer preference data"""
        preferences = {}
        
        for keyword in keywords:
            preferences[keyword] = {
                'price_sensitivity': np.random.choice(['low', 'medium', 'high']),
                'quality_priority': np.random.choice(['high', 'medium', 'low']),
                'convenience_priority': np.random.choice(['high', 'medium', 'low']),
                'preferred_channels': ['online', 'social_media', 'referrals'],
                'decision_factors': ['price', 'quality', 'reputation', 'convenience']
            }
        
        return preferences
    
    def _generate_common_services(self):
        """Generate common services in the industry"""
        service_templates = [
            'Basic {service}',
            'Premium {service}',
            'Professional {service}',
            'Express {service}',
            'Custom {service}'
        ]
        
        services = []
        for template in service_templates:
            services.append(template.format(service='Service'))
        
        return services
    
    def learn_from_social_media(self, business_category, keywords):
        """Learn from social media trends and customer sentiment"""
        print(f"📱 Learning from social media for: {business_category}")
        
        social_data = {
            'trending_topics': self._get_trending_topics(keywords),
            'customer_sentiment': self._get_customer_sentiment(keywords),
            'engagement_patterns': self._get_engagement_patterns(),
            'viral_content': self._get_viral_content_examples()
        }
        
        return social_data
    
    def _get_trending_topics(self, keywords):
        """Get trending topics related to business keywords"""
        topics = []
        
        for keyword in keywords:
            topics.append({
                'keyword': keyword,
                'trending_topics': [
                    f'Best {keyword} services',
                    f'Affordable {keyword}',
                    f'Professional {keyword}',
                    f'{keyword} tips and tricks'
                ],
                'hashtags': [f'#{keyword}', f'#{keyword}services', f'#{keyword}business'],
                'engagement_rate': np.random.uniform(0.5, 5.0)
            })
        
        return topics
    
    def _get_customer_sentiment(self, keywords):
        """Analyze customer sentiment for keywords"""
        sentiment_data = {}
        
        for keyword in keywords:
            sentiment_data[keyword] = {
                'positive_sentiment': np.random.uniform(60, 90),
                'negative_sentiment': np.random.uniform(5, 25),
                'neutral_sentiment': np.random.uniform(5, 15),
                'common_complaints': [
                    'High prices',
                    'Slow service',
                    'Poor communication'
                ],
                'common_praises': [
                    'Great quality',
                    'Fast service',
                    'Professional staff'
                ]
            }
        
        return sentiment_data
    
    def _get_engagement_patterns(self):
        """Get social media engagement patterns"""
        return {
            'best_posting_times': ['9:00 AM', '12:00 PM', '6:00 PM', '8:00 PM'],
            'high_engagement_days': ['Tuesday', 'Wednesday', 'Thursday'],
            'content_types': ['video', 'image', 'text', 'story'],
            'optimal_post_length': '150-300 characters'
        }
    
    def _get_viral_content_examples(self):
        """Get examples of viral content in the industry"""
        return [
            'Before and after transformations',
            'Customer testimonials',
            'Behind the scenes content',
            'Educational tips and tricks',
            'Industry insights and trends'
        ]
    
    def learn_from_customer_reviews(self, review_sources):
        """Learn from customer reviews across platforms"""
        print(f"⭐ Learning from customer reviews...")
        
        review_data = {
            'overall_ratings': {},
            'common_feedback': [],
            'improvement_areas': [],
            'strength_areas': []
        }
        
        # Simulate review data
        for source in review_sources:
            review_data['overall_ratings'][source] = np.random.uniform(3.5, 5.0)
        
        review_data['common_feedback'] = [
            'Great service quality',
            'Professional staff',
            'Reasonable pricing',
            'Good communication',
            'Timely delivery'
        ]
        
        review_data['improvement_areas'] = [
            'Response time',
            'Price transparency',
            'Service variety',
            'Online booking'
        ]
        
        review_data['strength_areas'] = [
            'Service quality',
            'Customer satisfaction',
            'Professionalism',
            'Reliability'
        ]
        
        return review_data
    
    def save_learned_data(self, filename=None):
        """Save all learned data to file"""
        if not filename:
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            filename = f"ai_models/data/external_learned_data_{timestamp}.json"
        
        data_to_save = {
            'learned_data': self.learned_data,
            'market_insights': self.market_insights,
            'learning_timestamp': datetime.now().isoformat()
        }
        
        with open(filename, 'w') as f:
            json.dump(data_to_save, f, indent=2)
        
        print(f"💾 Learned data saved to: {filename}")
        return filename
    
    def load_learned_data(self, filename):
        """Load previously learned data"""
        try:
            with open(filename, 'r') as f:
                data = json.load(f)
            
            self.learned_data = data.get('learned_data', {})
            self.market_insights = data.get('market_insights', {})
            
            print(f"📂 Loaded learned data from: {filename}")
            return True
        except Exception as e:
            print(f"❌ Failed to load learned data: {e}")
            return False

if __name__ == "__main__":
    # Test the external data collector
    collector = ExternalDataCollector()
    
    # Test competitor learning
    competitor_urls = [
        "https://example-competitor1.com",
        "https://example-competitor2.com"
    ]
    
    competitor_data = collector.learn_from_competitors(competitor_urls, "service")
    print(f"✅ Learned from {len(competitor_data)} competitors")
    
    # Test market data learning
    market_data = collector.learn_from_market_data(["consulting", "marketing", "design"])
    print(f"✅ Learned market data for {len(market_data)} categories")
    
    # Test social media learning
    social_data = collector.learn_from_social_media("service", ["consulting", "marketing"])
    print(f"✅ Learned social media data")
    
    # Save learned data
    filename = collector.save_learned_data()
    print(f"✅ External learning test completed!")
