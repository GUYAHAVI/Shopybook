import pandas as pd
import numpy as np
import json
import random
from datetime import datetime, timedelta
import re

class MarketingAI:
    def __init__(self):
        """Initialize the marketing AI system"""
        self.business_templates = self._load_business_templates()
        self.content_templates = self._load_content_templates()
        self.marketing_strategies = self._load_marketing_strategies()
        
    def generate_marketing_content(self, business_data, target_audience=None):
        """Generate personalized marketing content"""
        business_info = self._extract_business_info(business_data)
        
        content = {
            'social_media_posts': self._generate_social_posts(business_info, target_audience),
            'email_campaigns': self._generate_email_content(business_info, target_audience),
            'ad_copy': self._generate_ad_copy(business_info, target_audience),
            'video_scripts': self._generate_video_scripts(business_info),
            'marketing_strategy': self._create_marketing_strategy(business_info, target_audience)
        }
        
        return content
    
    def _extract_business_info(self, business_data):
        """Extract key business information for content generation"""
        business_metrics = pd.DataFrame(business_data.get('business_metrics', []))
        
        if business_metrics.empty:
            return {
                'business_name': 'Your Business',
                'business_type': 'general',
                'business_category': 'product',
                'total_revenue': 0,
                'total_customers': 0,
                'avg_order_value': 0
            }
        
        metrics = business_metrics.iloc[0]
        
        return {
            'business_name': metrics.get('business_name', 'Your Business'),
            'business_type': metrics.get('business_type', 'general'),
            'business_category': metrics.get('business_category', 'product'),
            'total_revenue': metrics.get('completed_revenue', 0) + metrics.get('service_revenue', 0),
            'total_customers': metrics.get('total_customers', 0),
            'avg_order_value': metrics.get('avg_order_value', 0),
            'total_products': metrics.get('total_products', 0),
            'total_services': metrics.get('total_services', 0)
        }
    
    def _generate_social_posts(self, business_info, target_audience):
        """Generate social media posts"""
        posts = []
        
        # Post 1: Business highlight
        posts.append({
            'platform': 'Facebook',
            'content': f"🎉 Exciting news from {business_info['business_name']}! We've served {business_info['total_customers']} happy customers and counting. Thank you for your trust and support! #SmallBusiness #CustomerAppreciation",
            'hashtags': ['#SmallBusiness', '#CustomerAppreciation', '#LocalBusiness'],
            'best_time': '9:00 AM - 11:00 AM',
            'engagement_tips': 'Ask customers to share their experience in comments'
        })
        
        # Post 2: Product/Service showcase
        if business_info['business_category'] == 'product':
            posts.append({
                'platform': 'Instagram',
                'content': f"🛍️ Discover our amazing products at {business_info['business_name']}! From quality items to exceptional service, we've got everything you need. What's your favorite product? #ProductShowcase #QualityProducts",
                'hashtags': ['#ProductShowcase', '#QualityProducts', '#LocalShopping'],
                'best_time': '2:00 PM - 4:00 PM',
                'engagement_tips': 'Use high-quality product photos'
            })
        else:
            posts.append({
                'platform': 'Instagram',
                'content': f"✨ Professional services you can trust at {business_info['business_name']}! Our team is dedicated to delivering excellence. Ready to experience the difference? #ProfessionalServices #Excellence",
                'hashtags': ['#ProfessionalServices', '#Excellence', '#QualityService'],
                'best_time': '2:00 PM - 4:00 PM',
                'engagement_tips': 'Share before/after results'
            })
        
        # Post 3: Customer testimonial style
        posts.append({
            'platform': 'LinkedIn',
            'content': f"💼 Building relationships, one customer at a time. At {business_info['business_name']}, we believe in the power of personalized service. What makes your business stand out? #BusinessGrowth #CustomerService",
            'hashtags': ['#BusinessGrowth', '#CustomerService', '#ProfessionalNetworking'],
            'best_time': '8:00 AM - 10:00 AM',
            'engagement_tips': 'Share business insights and tips'
        })
        
        return posts
    
    def _generate_email_content(self, business_info, target_audience):
        """Generate email campaign content"""
        campaigns = []
        
        # Welcome campaign
        campaigns.append({
            'campaign_type': 'Welcome Series',
            'subject_line': f"Welcome to {business_info['business_name']} - Let's Get Started!",
            'preheader': f"Discover what makes {business_info['business_name']} special",
            'content': f"""
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <h2>Welcome to {business_info['business_name']}! 🎉</h2>
                
                <p>Hi there!</p>
                
                <p>Welcome to the {business_info['business_name']} family! We're thrilled to have you on board.</p>
                
                <p>Here's what you can expect from us:</p>
                <ul>
                    <li>🎯 Personalized service tailored to your needs</li>
                    <li>💎 Quality products/services you can trust</li>
                    <li>🚀 Exclusive offers and early access to deals</li>
                    <li>📧 Regular updates and helpful tips</li>
                </ul>
                
                <p>Ready to get started? Check out our latest offerings!</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="#" style="background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">Explore Now</a>
                </div>
                
                <p>Best regards,<br>The {business_info['business_name']} Team</p>
            </div>
            """,
            'send_frequency': 'Immediate',
            'target_audience': 'New customers'
        })
        
        # Promotional campaign
        campaigns.append({
            'campaign_type': 'Promotional Offer',
            'subject_line': f"🎁 Special Offer Just for You - {business_info['business_name']}",
            'preheader': f"Limited time offer from {business_info['business_name']}",
            'content': f"""
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <h2>🎉 Special Offer Alert!</h2>
                
                <p>Hi valued customer!</p>
                
                <p>We've got something special just for you! As a valued customer of {business_info['business_name']}, you're entitled to an exclusive discount.</p>
                
                <div style="background-color: #f8f9fa; padding: 20px; border-radius: 10px; text-align: center; margin: 20px 0;">
                    <h3 style="color: #dc3545; margin: 0;">20% OFF</h3>
                    <p style="margin: 10px 0;">Your next purchase</p>
                    <p style="font-size: 12px; color: #6c757d;">Use code: WELCOME20</p>
                </div>
                
                <p>This offer expires in 7 days, so don't wait!</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="#" style="background-color: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">Claim Offer</a>
                </div>
                
                <p>Best regards,<br>The {business_info['business_name']} Team</p>
            </div>
            """,
            'send_frequency': 'Weekly',
            'target_audience': 'Active customers'
        })
        
        return campaigns
    
    def _generate_ad_copy(self, business_info, target_audience):
        """Generate advertising copy for different platforms"""
        ad_copies = []
        
        # Google Ads
        ad_copies.append({
            'platform': 'Google Ads',
            'headline_1': f"{business_info['business_name']} - Quality Products",
            'headline_2': f"Professional Service & Support",
            'headline_3': f"Trusted by {business_info['total_customers']} Customers",
            'description_1': f"Discover amazing products and services at {business_info['business_name']}. Quality guaranteed, customer satisfaction assured.",
            'description_2': f"Professional service, competitive prices. Visit {business_info['business_name']} today for the best experience.",
            'keywords': [business_info['business_name'], 'quality products', 'professional service', 'customer satisfaction'],
            'target_audience': 'Local customers searching for products/services'
        })
        
        # Facebook Ads
        ad_copies.append({
            'platform': 'Facebook Ads',
            'ad_type': 'Carousel Ad',
            'headline': f"Discover {business_info['business_name']}",
            'primary_text': f"Looking for quality products and exceptional service? {business_info['business_name']} has served {business_info['total_customers']} satisfied customers. Experience the difference today!",
            'call_to_action': 'Shop Now',
            'targeting': {
                'age': '25-54',
                'interests': ['shopping', 'local business', 'quality products'],
                'location': 'Local area'
            }
        })
        
        # Instagram Ads
        ad_copies.append({
            'platform': 'Instagram',
            'ad_type': 'Story Ad',
            'headline': f"Quality Meets Service at {business_info['business_name']}",
            'description': f"Join {business_info['total_customers']} happy customers who trust {business_info['business_name']} for their needs. Swipe up to explore!",
            'visual_elements': ['Product showcase', 'Customer testimonials', 'Behind-the-scenes'],
            'call_to_action': 'Swipe Up'
        })
        
        return ad_copies
    
    def _generate_video_scripts(self, business_info):
        """Generate video content scripts"""
        scripts = []
        
        # Brand introduction video
        scripts.append({
            'video_type': 'Brand Introduction',
            'duration': '30 seconds',
            'script': f"""
            [Opening - 0-5 seconds]
            "Welcome to {business_info['business_name']}, where quality meets excellence."
            
            [Main Content - 5-25 seconds]
            "We've been serving our community with dedication and passion. 
            From our carefully selected products to our professional services, 
            we're committed to delivering the best experience for our customers.
            
            With {business_info['total_customers']} satisfied customers and counting, 
            we're proud to be your trusted partner in business."
            
            [Call to Action - 25-30 seconds]
            "Visit us today and discover the {business_info['business_name']} difference!"
            """,
            'visual_elements': [
                'Business logo and branding',
                'Product/service showcase',
                'Happy customers',
                'Team members at work'
            ],
            'music_suggestion': 'Upbeat, professional background music',
            'target_audience': 'New and potential customers'
        })
        
        # Product showcase video
        scripts.append({
            'video_type': 'Product Showcase',
            'duration': '60 seconds',
            'script': f"""
            [Opening - 0-10 seconds]
            "Discover the amazing products at {business_info['business_name']}!"
            
            [Product Highlights - 10-45 seconds]
            "From premium quality items to everyday essentials, 
            we've got everything you need to succeed.
            
            Our products are carefully selected for their quality, 
            durability, and value for money.
            
            Whether you're looking for professional tools, 
            lifestyle products, or unique gifts, 
            we have something special just for you."
            
            [Call to Action - 45-60 seconds]
            "Visit {business_info['business_name']} today and explore our complete collection!"
            """,
            'visual_elements': [
                'Product close-ups',
                'Product in use',
                'Customer testimonials',
                'Store/website interface'
            ],
            'music_suggestion': 'Energetic, modern background music',
            'target_audience': 'Potential customers interested in products'
        })
        
        return scripts
    
    def _create_marketing_strategy(self, business_info, target_audience):
        """Create comprehensive marketing strategy"""
        strategy = {
            'overview': f"Comprehensive marketing strategy for {business_info['business_name']}",
            'target_audience': self._define_target_audience(business_info, target_audience),
            'channels': self._recommend_marketing_channels(business_info),
            'content_calendar': self._create_content_calendar(business_info),
            'budget_allocation': self._suggest_budget_allocation(business_info),
            'kpis': self._define_kpis(business_info)
        }
        
        return strategy
    
    def _define_target_audience(self, business_info, target_audience):
        """Define target audience based on business data"""
        if target_audience:
            return target_audience
        
        # Default audience based on business type
        if business_info['business_category'] == 'product':
            return {
                'primary': 'Local customers aged 25-54 interested in quality products',
                'secondary': 'Online shoppers looking for reliable products',
                'demographics': {
                    'age': '25-54',
                    'income': 'Middle to upper-middle class',
                    'location': 'Local area + online',
                    'interests': ['shopping', 'quality products', 'local business']
                }
            }
        else:
            return {
                'primary': 'Local businesses and individuals seeking professional services',
                'secondary': 'Online customers looking for reliable services',
                'demographics': {
                    'age': '30-60',
                    'income': 'Middle to upper class',
                    'location': 'Local area + online',
                    'interests': ['professional services', 'business growth', 'quality service']
                }
            }
    
    def _recommend_marketing_channels(self, business_info):
        """Recommend marketing channels based on business type"""
        channels = {
            'digital_marketing': {
                'social_media': ['Facebook', 'Instagram', 'LinkedIn'],
                'email_marketing': 'High priority for customer retention',
                'google_ads': 'Recommended for local search',
                'content_marketing': 'Blog posts and educational content'
            },
            'traditional_marketing': {
                'local_advertising': 'Local newspapers and magazines',
                'networking': 'Business networking events',
                'referral_program': 'Customer referral incentives'
            },
            'budget_allocation': {
                'digital': '70%',
                'traditional': '20%',
                'miscellaneous': '10%'
            }
        }
        
        return channels
    
    def _create_content_calendar(self, business_info):
        """Create content calendar for consistent posting"""
        calendar = {
            'weekly_schedule': {
                'monday': 'Motivational post + Product highlight',
                'tuesday': 'Customer testimonial + Behind-the-scenes',
                'wednesday': 'Educational content + Service showcase',
                'thursday': 'Throwback Thursday + Team spotlight',
                'friday': 'Weekend special offer + Fun content',
                'saturday': 'Customer appreciation + Local events',
                'sunday': 'Weekly recap + Community engagement'
            },
            'monthly_themes': [
                'Customer Appreciation Month',
                'Product Showcase Month',
                'Service Excellence Month',
                'Community Engagement Month'
            ],
            'posting_times': {
                'facebook': '9:00 AM, 2:00 PM, 7:00 PM',
                'instagram': '11:00 AM, 3:00 PM, 8:00 PM',
                'linkedin': '8:00 AM, 12:00 PM, 5:00 PM'
            }
        }
        
        return calendar
    
    def _suggest_budget_allocation(self, business_info):
        """Suggest budget allocation for marketing"""
        total_revenue = business_info['total_revenue']
        marketing_budget = total_revenue * 0.1  # 10% of revenue
        
        return {
            'total_budget': marketing_budget,
            'allocation': {
                'social_media_ads': '30%',
                'google_ads': '25%',
                'email_marketing': '15%',
                'content_creation': '15%',
                'traditional_advertising': '10%',
                'tools_and_software': '5%'
            },
            'monthly_breakdown': {
                'social_media_ads': marketing_budget * 0.3 / 12,
                'google_ads': marketing_budget * 0.25 / 12,
                'email_marketing': marketing_budget * 0.15 / 12,
                'content_creation': marketing_budget * 0.15 / 12,
                'traditional_advertising': marketing_budget * 0.1 / 12,
                'tools_and_software': marketing_budget * 0.05 / 12
            }
        }
    
    def _define_kpis(self, business_info):
        """Define key performance indicators"""
        return {
            'awareness_kpis': {
                'social_media_reach': 'Increase by 20% monthly',
                'website_traffic': 'Increase by 15% monthly',
                'brand_mentions': 'Track and increase positive mentions'
            },
            'engagement_kpis': {
                'social_media_engagement': 'Maintain 5% engagement rate',
                'email_open_rate': 'Target 25% open rate',
                'click_through_rate': 'Target 3% CTR'
            },
            'conversion_kpis': {
                'lead_generation': 'Generate 50 new leads monthly',
                'sales_conversion': 'Convert 10% of leads to customers',
                'customer_retention': 'Maintain 80% retention rate'
            },
            'roi_kpis': {
                'customer_acquisition_cost': 'Keep under $50 per customer',
                'customer_lifetime_value': 'Increase by 20% annually',
                'marketing_roi': 'Target 300% ROI'
            }
        }
    
    def _load_business_templates(self):
        """Load business-specific content templates"""
        return {
            'product_business': {
                'value_proposition': 'Quality products for every need',
                'key_benefits': ['Quality guaranteed', 'Competitive pricing', 'Excellent service'],
                'call_to_actions': ['Shop Now', 'Learn More', 'Get Quote']
            },
            'service_business': {
                'value_proposition': 'Professional services you can trust',
                'key_benefits': ['Expert team', 'Reliable service', 'Customer satisfaction'],
                'call_to_actions': ['Book Now', 'Get Started', 'Contact Us']
            },
            'hybrid_business': {
                'value_proposition': 'Complete solutions for your business',
                'key_benefits': ['Products and services', 'One-stop solution', 'Comprehensive support'],
                'call_to_actions': ['Explore Now', 'Get Quote', 'Learn More']
            }
        }
    
    def _load_content_templates(self):
        """Load content templates for different platforms"""
        return {
            'social_media': {
                'post_lengths': {
                    'facebook': '1-2 sentences',
                    'instagram': '1-3 sentences',
                    'linkedin': '2-4 sentences'
                },
                'hashtag_strategies': {
                    'brand_hashtags': ['#BusinessName', '#BrandName'],
                    'industry_hashtags': ['#Industry', '#Professional'],
                    'trending_hashtags': ['#Trending', '#Popular']
                }
            },
            'email_marketing': {
                'subject_line_templates': [
                    '🎉 [Offer] Just for You',
                    '💡 [Tip] Improve Your Business',
                    '📢 [News] Latest Updates'
                ],
                'content_structure': [
                    'Personalized greeting',
                    'Value proposition',
                    'Call to action',
                    'Professional closing'
                ]
            }
        }
    
    def _load_marketing_strategies(self):
        """Load marketing strategy templates"""
        return {
            'growth_strategy': {
                'awareness': 'Social media advertising and content marketing',
                'consideration': 'Email campaigns and retargeting ads',
                'conversion': 'Special offers and personalized messaging',
                'retention': 'Loyalty programs and customer appreciation'
            },
            'seasonal_campaigns': {
                'q1': 'New Year, New Goals',
                'q2': 'Spring Refresh',
                'q3': 'Summer Success',
                'q4': 'Holiday Specials'
            }
        }

if __name__ == "__main__":
    # Test the marketing AI
    marketing_ai = MarketingAI()
    print("Marketing AI initialized successfully!")
