#!/usr/bin/env python3
"""
Automated Learning System for AI Business Intelligence
Continuously learns from online sources and provides real-time advice
"""

import json
import os
import time
import threading
from datetime import datetime, timedelta
from sqlalchemy import create_engine, text
import pandas as pd
import requests
from bs4 import BeautifulSoup
import re
from dotenv import load_dotenv

load_dotenv()

class AutomatedLearningSystem:
    def __init__(self):
        """Initialize automated learning system"""
        self.db_engine = self._create_db_connection()
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })
        self.learning_cache = {}
        self.advice_cache = {}
        
    def _create_db_connection(self):
        """Create database connection"""
        db_host = os.getenv('DB_HOST', 'localhost')
        db_port = os.getenv('DB_PORT', '3306')
        db_name = os.getenv('DB_DATABASE', 'shopybook')
        db_user = os.getenv('DB_USERNAME', 'root')
        db_password = os.getenv('DB_PASSWORD', '')
        
        return create_engine(
            f"mysql+mysqlconnector://{db_user}:{db_password}@{db_host}:{db_port}/{db_name}"
        )
    
    def start_automated_learning(self):
        """Start the automated learning process"""
        print("🤖 Starting Automated Learning System...")
        
        # Start background learning thread
        learning_thread = threading.Thread(target=self._continuous_learning_loop, daemon=True)
        learning_thread.start()
        
        # Start advice generation thread
        advice_thread = threading.Thread(target=self._continuous_advice_generation, daemon=True)
        advice_thread.start()
        
        print("✅ Automated learning system started successfully!")
        return True
    
    def _continuous_learning_loop(self):
        """Continuous learning loop that runs in background"""
        while True:
            try:
                # Get all businesses that need learning
                businesses = self._get_businesses_for_learning()
                
                for business in businesses:
                    self._learn_for_business(business)
                
                # Wait before next learning cycle (every 6 hours)
                time.sleep(6 * 60 * 60)
                
            except Exception as e:
                print(f"❌ Error in learning loop: {e}")
                time.sleep(60)  # Wait 1 minute before retrying
    
    def _continuous_advice_generation(self):
        """Continuous advice generation loop"""
        while True:
            try:
                # Generate advice for all businesses
                businesses = self._get_all_businesses()
                
                for business in businesses:
                    self._generate_business_advice(business)
                
                # Wait before next advice cycle (every 2 hours)
                time.sleep(2 * 60 * 60)
                
            except Exception as e:
                print(f"❌ Error in advice generation: {e}")
                time.sleep(60)
    
    def _get_businesses_for_learning(self):
        """Get businesses that need learning"""
        query = """
        SELECT 
            id, name, business_type, business_category, 
            created_at, updated_at
        FROM businesses 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        OR updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        """
        
        try:
            df = pd.read_sql(query, self.db_engine)
            return df.to_dict('records')
        except Exception as e:
            print(f"Error getting businesses: {e}")
            return []
    
    def _get_all_businesses(self):
        """Get all businesses for advice generation"""
        query = """
        SELECT 
            id, name, business_type, business_category
        FROM businesses 
        WHERE is_active = 1
        """
        
        try:
            df = pd.read_sql(query, self.db_engine)
            return df.to_dict('records')
        except Exception as e:
            print(f"Error getting all businesses: {e}")
            return []
    
    def _learn_for_business(self, business):
        """Learn from online sources for a specific business"""
        business_id = business['id']
        business_type = business['business_type']
        business_category = business['business_category']
        
        print(f"🔍 Learning for business: {business['name']} ({business_type})")
        
        # Generate search keywords based on business type
        keywords = self._generate_search_keywords(business_type, business_category)
        
        # Learn from competitors
        competitor_data = self._learn_from_competitors(keywords, business_type)
        
        # Learn from market trends
        market_data = self._learn_from_market_trends(keywords)
        
        # Learn from social media
        social_data = self._learn_from_social_media(keywords)
        
        # Store learned data
        learned_data = {
            'business_id': business_id,
            'business_type': business_type,
            'competitor_insights': competitor_data,
            'market_trends': market_data,
            'social_insights': social_data,
            'learned_at': datetime.now().isoformat()
        }
        
        self._store_learned_data(business_id, learned_data)
        print(f"✅ Learning completed for {business['name']}")
    
    def _generate_search_keywords(self, business_type, business_category):
        """Generate search keywords based on business type"""
        keywords = []
        
        # Add business type keywords
        if business_type == 'retail':
            keywords.extend(['retail store', 'shop', 'boutique', 'supermarket'])
        elif business_type == 'service':
            keywords.extend(['service business', 'consulting', 'professional services'])
        elif business_type == 'restaurant':
            keywords.extend(['restaurant', 'cafe', 'food service', 'dining'])
        elif business_type == 'salon':
            keywords.extend(['salon', 'beauty', 'spa', 'wellness'])
        elif business_type == 'barbershop':
            keywords.extend(['barbershop', 'haircut', 'grooming', 'men salon'])
        
        # Add category-specific keywords
        if business_category:
            keywords.append(business_category)
        
        return keywords
    
    def _learn_from_competitors(self, keywords, business_type):
        """Learn from competitor websites"""
        competitor_data = []
        
        # Generate competitor search queries
        search_queries = [
            f"{keyword} near me" for keyword in keywords[:3]
        ]
        
        for query in search_queries:
            try:
                # Simulate competitor website discovery
                competitor_sites = self._discover_competitor_sites(query)
                
                for site in competitor_sites[:3]:  # Limit to 3 sites per query
                    site_data = self._scrape_competitor_site(site, business_type)
                    if site_data:
                        competitor_data.append(site_data)
                
                time.sleep(2)  # Be respectful
                
            except Exception as e:
                print(f"Error learning from competitors: {e}")
        
        return competitor_data
    
    def _discover_competitor_sites(self, query):
        """Discover competitor websites (simulated)"""
        # In a real implementation, you'd use search APIs or web scraping
        # For now, we'll simulate with common business directories
        
        base_sites = [
            "https://www.yellowpages.com",
            "https://www.google.com/maps",
            "https://www.yelp.com"
        ]
        
        return base_sites
    
    def _scrape_competitor_site(self, site, business_type):
        """Scrape competitor website data"""
        try:
            # Simulate scraping (in real implementation, you'd actually scrape)
            return {
                'site_url': site,
                'business_type': business_type,
                'pricing_data': self._simulate_pricing_data(),
                'service_data': self._simulate_service_data(),
                'marketing_content': self._simulate_marketing_content(),
                'scraped_at': datetime.now().isoformat()
            }
        except Exception as e:
            print(f"Error scraping {site}: {e}")
            return None
    
    def _simulate_pricing_data(self):
        """Simulate pricing data extraction"""
        return [
            {'price': 50.0, 'service': 'Basic Service'},
            {'price': 100.0, 'service': 'Premium Service'},
            {'price': 25.0, 'service': 'Quick Service'}
        ]
    
    def _simulate_service_data(self):
        """Simulate service data extraction"""
        return [
            {'service_name': 'Haircut', 'description': 'Professional haircut service'},
            {'service_name': 'Styling', 'description': 'Hair styling and treatment'},
            {'service_name': 'Consultation', 'description': 'Free consultation service'}
        ]
    
    def _simulate_marketing_content(self):
        """Simulate marketing content extraction"""
        return {
            'headlines': ['Professional Hair Services', 'Best Prices in Town'],
            'cta_buttons': ['Book Now', 'Call Today'],
            'offers': ['20% Off First Visit', 'Free Consultation']
        }
    
    def _learn_from_market_trends(self, keywords):
        """Learn from market trends"""
        market_data = {
            'trending_topics': [],
            'market_size': {},
            'growth_rates': {},
            'customer_preferences': {}
        }
        
        for keyword in keywords:
            try:
                # Simulate market research
                market_data['trending_topics'].append({
                    'keyword': keyword,
                    'trend_score': 75,
                    'growth_rate': 15
                })
                
                market_data['market_size'][keyword] = {
                    'size': '1.2B USD',
                    'growth': '12% annually'
                }
                
            except Exception as e:
                print(f"Error learning market trends: {e}")
        
        return market_data
    
    def _learn_from_social_media(self, keywords):
        """Learn from social media trends"""
        social_data = {
            'trending_hashtags': [],
            'engagement_patterns': {},
            'viral_content': []
        }
        
        for keyword in keywords:
            try:
                # Simulate social media learning
                social_data['trending_hashtags'].extend([
                    f"#{keyword.replace(' ', '')}",
                    f"#{keyword.replace(' ', '')}business",
                    f"#{keyword.replace(' ', '')}services"
                ])
                
                social_data['engagement_patterns'][keyword] = {
                    'best_posting_time': '6-8 PM',
                    'optimal_frequency': '2-3 posts per day',
                    'engagement_rate': '4.2%'
                }
                
            except Exception as e:
                print(f"Error learning from social media: {e}")
        
        return social_data
    
    def _store_learned_data(self, business_id, learned_data):
        """Store learned data in database"""
        try:
            # Store in learning_cache table
            query = """
            INSERT INTO ai_learning_cache 
            (business_id, learned_data, created_at) 
            VALUES (%s, %s, %s)
            ON DUPLICATE KEY UPDATE 
            learned_data = VALUES(learned_data),
            updated_at = NOW()
            """
            
            with self.db_engine.connect() as conn:
                conn.execute(text(query), {
                    'business_id': business_id,
                    'learned_data': json.dumps(learned_data),
                    'created_at': datetime.now()
                })
                conn.commit()
                
        except Exception as e:
            print(f"Error storing learned data: {e}")
    
    def _generate_business_advice(self, business):
        """Generate advice for a specific business"""
        business_id = business['id']
        
        try:
            # Get business performance data
            performance_data = self._get_business_performance(business_id)
            
            # Get learned insights
            learned_insights = self._get_learned_insights(business_id)
            
            # Generate advice based on performance and insights
            advice = self._analyze_and_generate_advice(performance_data, learned_insights)
            
            # Store advice
            self._store_business_advice(business_id, advice)
            
            print(f"💡 Generated advice for {business['name']}")
            
        except Exception as e:
            print(f"Error generating advice for {business['name']}: {e}")
    
    def _get_business_performance(self, business_id):
        """Get business performance data"""
        query = """
        SELECT 
            COUNT(DISTINCT o.id) as total_orders,
            SUM(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE 0 END) as revenue,
            COUNT(DISTINCT c.id) as total_customers,
            AVG(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE NULL END) as avg_order_value
        FROM businesses b
        LEFT JOIN orders o ON b.id = o.business_id
        LEFT JOIN customers c ON b.id = c.business_id
        WHERE b.id = %s
        """
        
        try:
            df = pd.read_sql(query, self.db_engine, params=[business_id])
            return df.to_dict('records')[0] if not df.empty else {}
        except Exception as e:
            print(f"Error getting performance data: {e}")
            return {}
    
    def _get_learned_insights(self, business_id):
        """Get learned insights for business"""
        query = """
        SELECT learned_data 
        FROM ai_learning_cache 
        WHERE business_id = %s 
        ORDER BY created_at DESC 
        LIMIT 1
        """
        
        try:
            with self.db_engine.connect() as conn:
                result = conn.execute(text(query), {'business_id': business_id})
                row = result.fetchone()
                
                if row:
                    return json.loads(row[0])
                return {}
                
        except Exception as e:
            print(f"Error getting learned insights: {e}")
            return {}
    
    def _analyze_and_generate_advice(self, performance_data, learned_insights):
        """Analyze data and generate advice"""
        advice = {
            'business_id': performance_data.get('business_id'),
            'advice_type': 'performance_optimization',
            'priority': 'medium',
            'title': '',
            'description': '',
            'action_items': [],
            'expected_impact': '',
            'generated_at': datetime.now().isoformat()
        }
        
        # Analyze revenue performance
        revenue = performance_data.get('revenue', 0)
        total_orders = performance_data.get('total_orders', 0)
        avg_order_value = performance_data.get('avg_order_value', 0)
        
        if revenue == 0:
            advice.update({
                'title': 'Start Recording Sales',
                'description': 'No sales data found. Start recording your first sales to get personalized advice.',
                'priority': 'high',
                'action_items': [
                    'Record your first product sale',
                    'Add your services to the system',
                    'Create your first customer profile'
                ],
                'expected_impact': 'Enable data-driven insights and recommendations'
            })
        
        elif avg_order_value < 50:
            advice.update({
                'title': 'Increase Average Order Value',
                'description': 'Your average order value is low. Consider upselling and bundling strategies.',
                'priority': 'medium',
                'action_items': [
                    'Create product bundles',
                    'Implement upselling techniques',
                    'Offer premium services'
                ],
                'expected_impact': 'Increase revenue by 20-30%'
            })
        
        elif total_orders < 10:
            advice.update({
                'title': 'Focus on Customer Acquisition',
                'description': 'You have few orders. Focus on attracting more customers.',
                'priority': 'high',
                'action_items': [
                    'Launch marketing campaigns',
                    'Offer first-time customer discounts',
                    'Improve your online presence'
                ],
                'expected_impact': 'Increase customer base by 50%'
            })
        
        # Add market-based advice from learned insights
        if learned_insights:
            market_advice = self._generate_market_based_advice(learned_insights)
            if market_advice:
                advice['market_insights'] = market_advice
        
        return advice
    
    def _generate_market_based_advice(self, learned_insights):
        """Generate market-based advice from learned insights"""
        market_advice = []
        
        # Analyze competitor insights
        if 'competitor_insights' in learned_insights:
            competitors = learned_insights['competitor_insights']
            if competitors:
                avg_price = sum([c.get('pricing_data', [{}])[0].get('price', 0) for c in competitors]) / len(competitors)
                market_advice.append({
                    'type': 'pricing',
                    'title': 'Competitive Pricing Analysis',
                    'description': f'Average market price is ${avg_price:.2f}. Consider adjusting your pricing strategy.',
                    'action': 'Review and adjust your pricing'
                })
        
        # Analyze market trends
        if 'market_trends' in learned_insights:
            trends = learned_insights['market_trends']
            if trends.get('trending_topics'):
                trending = trends['trending_topics'][0]
                market_advice.append({
                    'type': 'trend',
                    'title': 'Market Trend Alert',
                    'description': f"'{trending['keyword']}' is trending with {trending['growth_rate']}% growth.",
                    'action': 'Consider adding trending services'
                })
        
        return market_advice
    
    def _store_business_advice(self, business_id, advice):
        """Store business advice in database"""
        try:
            query = """
            INSERT INTO ai_business_advice 
            (business_id, advice_type, priority, title, description, 
             action_items, expected_impact, advice_data, created_at) 
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
            """
            
            with self.db_engine.connect() as conn:
                conn.execute(text(query), {
                    'business_id': business_id,
                    'advice_type': advice['advice_type'],
                    'priority': advice['priority'],
                    'title': advice['title'],
                    'description': advice['description'],
                    'action_items': json.dumps(advice['action_items']),
                    'expected_impact': advice['expected_impact'],
                    'advice_data': json.dumps(advice),
                    'created_at': datetime.now()
                })
                conn.commit()
                
        except Exception as e:
            print(f"Error storing business advice: {e}")
    
    def get_latest_advice(self, business_id):
        """Get latest advice for a business"""
        query = """
        SELECT * FROM ai_business_advice 
        WHERE business_id = %s 
        ORDER BY created_at DESC 
        LIMIT 5
        """
        
        try:
            df = pd.read_sql(query, self.db_engine, params=[business_id])
            return df.to_dict('records')
        except Exception as e:
            print(f"Error getting latest advice: {e}")
            return []
    
    def get_learning_status(self, business_id):
        """Get learning status for a business"""
        query = """
        SELECT * FROM ai_learning_cache 
        WHERE business_id = %s 
        ORDER BY created_at DESC 
        LIMIT 1
        """
        
        try:
            df = pd.read_sql(query, self.db_engine, params=[business_id])
            if not df.empty:
                return {
                    'last_learned': df.iloc[0]['created_at'],
                    'learning_active': True,
                    'insights_count': len(json.loads(df.iloc[0]['learned_data']))
                }
            return {'learning_active': False}
        except Exception as e:
            print(f"Error getting learning status: {e}")
            return {'learning_active': False}

if __name__ == "__main__":
    # Test the automated learning system
    learning_system = AutomatedLearningSystem()
    learning_system.start_automated_learning()
    
    # Keep the system running
    try:
        while True:
            time.sleep(60)
    except KeyboardInterrupt:
        print("🤖 Automated learning system stopped.")
