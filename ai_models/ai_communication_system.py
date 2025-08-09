#!/usr/bin/env python3
"""
AI Communication System for Business Intelligence
Handles user interactions and provides business insights
"""

import json
import os
import re
from datetime import datetime
from sqlalchemy import create_engine, text
from dotenv import load_dotenv
import logging
from typing import Dict, List, Any, Optional
import random

load_dotenv()

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class AICommunicationSystem:
    def __init__(self):
        """Initialize AI communication system"""
        self.db_engine = self._create_db_connection()
        self.conversation_history = {}
        
        # Response templates
        self.templates = {
            'greeting': [
                "Hello! I'm your AI business assistant. How can I help you today?",
                "Hi there! I'm here to help with your business insights. What would you like to know?",
                "Welcome! I'm your AI business advisor. What can I assist you with?"
            ],
            'sales': [
                "Based on your sales data, {insight}. Here's what I recommend: {recommendation}",
                "Your sales performance shows {insight}. Consider this: {recommendation}",
                "Looking at your sales trends, {insight}. My suggestion: {recommendation}"
            ],
            'market': [
                "The market is showing {trend}. This could impact your business by {impact}",
                "I've identified a market trend: {trend}. Here's how it affects you: {impact}",
                "Current market analysis reveals {trend}. For your business, this means {impact}"
            ],
            'recommendation': [
                "Based on the data, I recommend {action} because {reason}",
                "Here's my recommendation: {action}. The reasoning: {reason}",
                "I suggest {action} based on {reason}"
            ],
            'error': [
                "I'm having trouble accessing that information right now. Could you try rephrasing your question?",
                "I didn't quite understand that. Can you be more specific about what you're looking for?",
                "Let me help you better. Could you clarify what you'd like to know about your business?"
            ]
        }
    
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
    
    def process_message(self, user_id: str, message: str, business_id: Optional[str] = None) -> Dict[str, Any]:
        """Process user message and generate response"""
        try:
            # Store conversation
            if user_id not in self.conversation_history:
                self.conversation_history[user_id] = []
            
            self.conversation_history[user_id].append({
                'message': message,
                'timestamp': datetime.now().isoformat(),
                'type': 'user'
            })
            
            # Analyze intent
            intent = self._analyze_intent(message)
            
            # Generate response
            response = self._generate_response(intent, message, business_id)
            
            # Store response
            self.conversation_history[user_id].append({
                'message': response['text'],
                'timestamp': datetime.now().isoformat(),
                'type': 'ai',
                'intent': intent
            })
            
            return {
                'response': response['text'],
                'intent': intent,
                'confidence': response.get('confidence', 0.8),
                'suggestions': response.get('suggestions', [])
            }
            
        except Exception as e:
            logger.error(f"Error processing message: {e}")
            return {
                'response': "I'm having trouble processing your request right now. Please try again.",
                'intent': 'error',
                'confidence': 0.0,
                'suggestions': []
            }
    
    def _analyze_intent(self, message: str) -> str:
        """Analyze user intent"""
        message_lower = message.lower()
        
        if any(word in message_lower for word in ['hi', 'hello', 'hey', 'start']):
            return 'greeting'
        elif any(word in message_lower for word in ['sales', 'revenue', 'profit', 'income']):
            return 'sales'
        elif any(word in message_lower for word in ['market', 'trend', 'competitor', 'industry']):
            return 'market'
        elif any(word in message_lower for word in ['recommend', 'suggestion', 'advice', 'should']):
            return 'recommendation'
        elif any(word in message_lower for word in ['bye', 'goodbye', 'thanks', 'thank']):
            return 'goodbye'
        else:
            return 'general'
    
    def _generate_response(self, intent: str, message: str, business_id: Optional[str] = None) -> Dict[str, Any]:
        """Generate response based on intent"""
        
        if intent == 'greeting':
            return self._generate_greeting()
        elif intent == 'sales':
            return self._generate_sales_response(business_id)
        elif intent == 'market':
            return self._generate_market_response(business_id)
        elif intent == 'recommendation':
            return self._generate_recommendation_response(business_id)
        elif intent == 'goodbye':
            return self._generate_goodbye()
        else:
            return self._generate_general_response()
    
    def _generate_greeting(self) -> Dict[str, Any]:
        """Generate greeting response"""
        text = random.choice(self.templates['greeting'])
        return {
            'text': text,
            'confidence': 0.9,
            'suggestions': [
                "How are my sales performing?",
                "What market trends should I know?",
                "Give me business recommendations"
            ]
        }
    
    def _generate_sales_response(self, business_id: Optional[str] = None) -> Dict[str, Any]:
        """Generate sales response"""
        try:
            # Get sales data
            sales_data = self._get_sales_data(business_id)
            
            if sales_data:
                analysis = self._analyze_sales(sales_data)
                template = random.choice(self.templates['sales'])
                text = template.format(
                    insight=analysis['insight'],
                    recommendation=analysis['recommendation']
                )
            else:
                text = "I don't have enough sales data yet. Start recording your sales to get detailed insights and recommendations."
            
            return {
                'text': text,
                'confidence': 0.8,
                'suggestions': [
                    "Show me sales trends",
                    "What's my best product?",
                    "How can I improve sales?"
                ]
            }
            
        except Exception as e:
            logger.error(f"Error generating sales response: {e}")
            return self._generate_error_response()
    
    def _generate_market_response(self, business_id: Optional[str] = None) -> Dict[str, Any]:
        """Generate market response"""
        try:
            # Get market knowledge
            market_data = self._get_market_knowledge()
            
            if market_data:
                analysis = self._analyze_market(market_data)
                template = random.choice(self.templates['market'])
                text = template.format(
                    trend=analysis['trend'],
                    impact=analysis['impact']
                )
            else:
                text = "I'm gathering market intelligence. Check back soon for detailed market insights and trends."
            
            return {
                'text': text,
                'confidence': 0.7,
                'suggestions': [
                    "Tell me more about trends",
                    "What are competitors doing?",
                    "Show me industry insights"
                ]
            }
            
        except Exception as e:
            logger.error(f"Error generating market response: {e}")
            return self._generate_error_response()
    
    def _generate_recommendation_response(self, business_id: Optional[str] = None) -> Dict[str, Any]:
        """Generate recommendation response"""
        try:
            # Get business data
            business_data = self._get_business_data(business_id)
            
            # Generate recommendations
            recommendations = self._generate_recommendations(business_data)
            
            template = random.choice(self.templates['recommendation'])
            text = template.format(
                action=recommendations['action'],
                reason=recommendations['reason']
            )
            
            return {
                'text': text,
                'confidence': 0.8,
                'suggestions': [
                    "Tell me more about this",
                    "What are the risks?",
                    "How long will this take?"
                ]
            }
            
        except Exception as e:
            logger.error(f"Error generating recommendation response: {e}")
            return self._generate_error_response()
    
    def _generate_goodbye(self) -> Dict[str, Any]:
        """Generate goodbye response"""
        text = "Thanks for chatting! Feel free to ask me anything about your business anytime."
        return {
            'text': text,
            'confidence': 0.9,
            'suggestions': []
        }
    
    def _generate_general_response(self) -> Dict[str, Any]:
        """Generate general response"""
        text = "I understand you're asking about your business. Let me help you get the most relevant information. Could you be more specific about what you'd like to know?"
        return {
            'text': text,
            'confidence': 0.5,
            'suggestions': [
                "How are my sales performing?",
                "What market trends should I know?",
                "Give me business recommendations"
            ]
        }
    
    def _generate_error_response(self) -> Dict[str, Any]:
        """Generate error response"""
        text = random.choice(self.templates['error'])
        return {
            'text': text,
            'confidence': 0.3,
            'suggestions': [
                "Try asking about sales",
                "Ask about market trends",
                "Request business advice"
            ]
        }
    
    def _get_sales_data(self, business_id: Optional[str] = None) -> List[Dict[str, Any]]:
        """Get sales data from database"""
        try:
            if not business_id:
                return []
            
            query = """
            SELECT id, product_name, quantity, unit_price, total_amount, created_at
            FROM sales
            WHERE business_id = :business_id
            ORDER BY created_at DESC
            LIMIT 50
            """
            
            with self.db_engine.connect() as conn:
                result = conn.execute(text(query), {'business_id': business_id})
                rows = result.fetchall()
                
                return [
                    {
                        'id': row[0],
                        'product_name': row[1],
                        'quantity': row[2],
                        'unit_price': float(row[3]),
                        'total_amount': float(row[4]),
                        'created_at': row[5]
                    }
                    for row in rows
                ]
                
        except Exception as e:
            logger.error(f"Error getting sales data: {e}")
            return []
    
    def _get_market_knowledge(self) -> List[Dict[str, Any]]:
        """Get market knowledge from database"""
        try:
            query = """
            SELECT data_type, source, category, data, relevance_score, sentiment_score
            FROM knowledge_data
            WHERE data_type IN ('news', 'market_data', 'social_media')
            ORDER BY created_at DESC
            LIMIT 20
            """
            
            with self.db_engine.connect() as conn:
                result = conn.execute(text(query))
                rows = result.fetchall()
                
                return [
                    {
                        'data_type': row[0],
                        'source': row[1],
                        'category': row[2],
                        'data': json.loads(row[3]),
                        'relevance_score': float(row[4]),
                        'sentiment_score': float(row[5]) if row[5] else 0.0
                    }
                    for row in rows
                ]
                
        except Exception as e:
            logger.error(f"Error getting market knowledge: {e}")
            return []
    
    def _get_business_data(self, business_id: Optional[str] = None) -> Dict[str, Any]:
        """Get business data"""
        try:
            if not business_id:
                return {}
            
            query = """
            SELECT id, name, business_type, business_category
            FROM businesses
            WHERE id = :business_id
            """
            
            with self.db_engine.connect() as conn:
                result = conn.execute(text(query), {'business_id': business_id})
                row = result.fetchone()
                
                if row:
                    return {
                        'id': row[0],
                        'name': row[1],
                        'business_type': row[2],
                        'business_category': row[3]
                    }
                
                return {}
                
        except Exception as e:
            logger.error(f"Error getting business data: {e}")
            return {}
    
    def _analyze_sales(self, sales_data: List[Dict[str, Any]]) -> Dict[str, str]:
        """Analyze sales data"""
        try:
            if not sales_data:
                return {
                    'insight': 'no sales data available',
                    'recommendation': 'start recording your sales'
                }
            
            total_sales = sum(item['total_amount'] for item in sales_data)
            avg_order = total_sales / len(sales_data) if sales_data else 0
            
            # Find top product
            product_sales = {}
            for item in sales_data:
                product = item['product_name']
                if product not in product_sales:
                    product_sales[product] = 0
                product_sales[product] += item['total_amount']
            
            top_product = max(product_sales.items(), key=lambda x: x[1]) if product_sales else ('None', 0)
            
            insight = f"total sales of ${total_sales:.2f} with average order value of ${avg_order:.2f}"
            recommendation = f"focus on promoting {top_product[0]} as your best performer"
            
            return {'insight': insight, 'recommendation': recommendation}
            
        except Exception as e:
            logger.error(f"Error analyzing sales: {e}")
            return {
                'insight': 'unable to analyze sales',
                'recommendation': 'check your data'
            }
    
    def _analyze_market(self, market_data: List[Dict[str, Any]]) -> Dict[str, str]:
        """Analyze market data"""
        try:
            if not market_data:
                return {
                    'trend': 'insufficient market data',
                    'impact': 'continue monitoring'
                }
            
            # Calculate average sentiment
            sentiments = [item['sentiment_score'] for item in market_data if item['sentiment_score'] is not None]
            avg_sentiment = sum(sentiments) / len(sentiments) if sentiments else 0
            
            # Get most relevant category
            most_relevant = max(market_data, key=lambda x: x['relevance_score'])
            
            if avg_sentiment > 0.1:
                trend = f"positive trend in {most_relevant['category']}"
                impact = "opportunities for growth"
            elif avg_sentiment < -0.1:
                trend = f"negative trend in {most_relevant['category']}"
                impact = "challenges to address"
            else:
                trend = f"stable conditions in {most_relevant['category']}"
                impact = "steady business environment"
            
            return {'trend': trend, 'impact': impact}
            
        except Exception as e:
            logger.error(f"Error analyzing market: {e}")
            return {
                'trend': 'market analysis unavailable',
                'impact': 'continue gathering data'
            }
    
    def _generate_recommendations(self, business_data: Dict[str, Any]) -> Dict[str, str]:
        """Generate business recommendations"""
        try:
            business_type = business_data.get('business_type', 'general')
            
            if business_type == 'retail':
                action = "implement inventory management"
                reason = "retail businesses benefit from optimized stock levels"
            elif business_type == 'service':
                action = "focus on customer retention"
                reason = "service businesses thrive on repeat customers"
            elif business_type == 'technology':
                action = "invest in digital marketing"
                reason = "tech businesses need strong online presence"
            else:
                action = "analyze customer data regularly"
                reason = "data-driven decisions lead to better outcomes"
            
            return {'action': action, 'reason': reason}
            
        except Exception as e:
            logger.error(f"Error generating recommendations: {e}")
            return {
                'action': 'review business strategy',
                'reason': 'periodic reviews help maintain business health'
            }
    
    def get_conversation_history(self, user_id: str) -> List[Dict[str, Any]]:
        """Get conversation history"""
        return self.conversation_history.get(user_id, [])

def main():
    """Test the AI communication system"""
    ai_system = AICommunicationSystem()
    
    # Test conversation
    test_messages = [
        "Hello",
        "How are my sales performing?",
        "What market trends should I know?",
        "Give me business recommendations",
        "Goodbye"
    ]
    
    user_id = "test_user"
    business_id = "1"
    
    print("🤖 AI Communication System Test")
    print("=" * 50)
    
    for message in test_messages:
        print(f"\n👤 User: {message}")
        response = ai_system.process_message(user_id, message, business_id)
        print(f"🤖 AI: {response['response']}")
        print(f"📊 Intent: {response['intent']} (confidence: {response['confidence']:.2f})")

if __name__ == "__main__":
    main()
