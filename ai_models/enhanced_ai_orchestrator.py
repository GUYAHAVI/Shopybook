#!/usr/bin/env python3
"""
Enhanced AI Business Orchestrator with External Learning
Combines internal business data with external market insights
"""

import json
import pandas as pd
from datetime import datetime
import os
import sys

# Add the ai_models directory to the Python path
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from data_collectors.internal_data import InternalDataCollector
from data_collectors.external_learning import ExternalLearningSystem
from models.business_intelligence import BusinessIntelligenceEngine
from models.marketing_ai import MarketingAI

class EnhancedAIBusinessOrchestrator:
    def __init__(self):
        """Initialize the enhanced AI Business Orchestrator"""
        self.data_collector = InternalDataCollector()
        self.external_learner = ExternalLearningSystem()
        self.business_intelligence = BusinessIntelligenceEngine()
        self.marketing_ai = MarketingAI()
        
    def generate_enhanced_analysis(self, business_id=None, competitor_urls=None, industry_keywords=None):
        """Generate comprehensive analysis with external learning"""
        print("🤖 Starting Enhanced AI Analysis with External Learning...")
        
        # Step 1: Collect internal business data
        print("📊 Collecting internal business data...")
        internal_data = self.data_collector.export_all_data(business_id)
        
        # Step 2: Learn from external sources
        external_insights = {}
        
        if competitor_urls:
            print("🔍 Learning from competitors...")
            competitor_data = self.external_learner.learn_from_competitors(
                competitor_urls, 
                internal_data.get('business_metrics', [{}])[0].get('business_category', 'service')
            )
            external_insights['competitor_data'] = competitor_data
        
        if industry_keywords:
            print("📊 Learning from market data...")
            market_data = self.external_learner.learn_from_market_data(industry_keywords)
            external_insights['market_data'] = market_data
            
            print("📱 Learning from social media trends...")
            social_data = self.external_learner.learn_from_social_media(
                internal_data.get('business_metrics', [{}])[0].get('business_category', 'service'),
                industry_keywords
            )
            external_insights['social_data'] = social_data
        
        # Step 3: Analyze business performance with external context
        print("🧠 Analyzing business performance with external insights...")
        enhanced_analysis = self.business_intelligence.analyze_business_performance(internal_data)
        
        # Step 4: Generate enhanced recommendations
        print("💡 Generating enhanced recommendations...")
        enhanced_recommendations = self._generate_enhanced_recommendations(
            enhanced_analysis, external_insights
        )
        
        # Step 5: Generate market-aware marketing content
        print("📝 Generating market-aware marketing content...")
        enhanced_marketing = self._generate_enhanced_marketing_content(
            internal_data, external_insights
        )
        
        # Step 6: Compile comprehensive enhanced report
        print("📋 Compiling enhanced comprehensive report...")
        enhanced_report = self._compile_enhanced_report(
            internal_data, enhanced_analysis, enhanced_recommendations, 
            enhanced_marketing, external_insights
        )
        
        return enhanced_report
    
    def _generate_enhanced_recommendations(self, analysis, external_insights):
        """Generate recommendations enhanced with external market insights"""
        recommendations = []
        
        # Base recommendations from internal analysis
        base_recommendations = self.business_intelligence.generate_recommendations(analysis)
        recommendations.extend(base_recommendations)
        
        # Enhanced recommendations based on external insights
        if 'competitor_data' in external_insights:
            competitor_recommendations = self._analyze_competitor_insights(
                external_insights['competitor_data']
            )
            recommendations.extend(competitor_recommendations)
        
        if 'market_data' in external_insights:
            market_recommendations = self._analyze_market_insights(
                external_insights['market_data']
            )
            recommendations.extend(market_recommendations)
        
        if 'social_data' in external_insights:
            social_recommendations = self._analyze_social_insights(
                external_insights['social_data']
            )
            recommendations.extend(social_recommendations)
        
        return recommendations
    
    def _analyze_competitor_insights(self, competitor_data):
        """Analyze competitor data and generate recommendations"""
        recommendations = []
        
        if not competitor_data:
            return recommendations
        
        # Analyze pricing patterns
        all_prices = []
        for competitor in competitor_data:
            if 'pricing_data' in competitor:
                all_prices.extend([p['price'] for p in competitor['pricing_data']])
        
        if all_prices:
            avg_competitor_price = sum(all_prices) / len(all_prices)
            min_competitor_price = min(all_prices)
            max_competitor_price = max(all_prices)
            
            recommendations.append({
                'category': 'Competitive Pricing',
                'priority': 'High',
                'action': f'Analyze pricing strategy based on competitor range: ${min_competitor_price}-${max_competitor_price}',
                'expected_impact': 'Optimize pricing for market competitiveness',
                'implementation_time': '1-2 weeks',
                'specific_steps': [
                    'Review current pricing against competitors',
                    'Identify pricing gaps and opportunities',
                    'Develop competitive pricing strategy'
                ]
            })
        
        # Analyze service offerings
        all_services = []
        for competitor in competitor_data:
            if 'service_data' in competitor:
                all_services.extend([s['service_name'] for s in competitor['service_data']])
        
        if all_services:
            unique_services = list(set(all_services))
            recommendations.append({
                'category': 'Service Portfolio',
                'priority': 'Medium',
                'action': f'Review service offerings based on {len(unique_services)} competitor services',
                'expected_impact': 'Expand service portfolio to match market demand',
                'implementation_time': '2-4 weeks',
                'specific_steps': [
                    'Identify missing services in your portfolio',
                    'Research demand for new services',
                    'Develop service expansion plan'
                ]
            })
        
        return recommendations
    
    def _analyze_market_insights(self, market_data):
        """Analyze market data and generate recommendations"""
        recommendations = []
        
        if 'industry_trends' in market_data:
            for trend in market_data['industry_trends']:
                if trend['growth_rate'] > 10:
                    recommendations.append({
                        'category': 'Market Opportunity',
                        'priority': 'High',
                        'action': f"Capitalize on growing {trend['keyword']} market ({trend['growth_rate']:.1f}% growth)",
                        'expected_impact': f"Capture market share in growing {trend['keyword']} segment",
                        'implementation_time': '3-6 months',
                        'specific_steps': [
                            f'Develop {trend["keyword"]} service offerings',
                            'Create targeted marketing campaigns',
                            'Build expertise in growing market segment'
                        ]
                    })
        
        if 'market_size' in market_data:
            for keyword, data in market_data['market_size'].items():
                if data['growth_rate'] > 8:
                    recommendations.append({
                        'category': 'Market Expansion',
                        'priority': 'Medium',
                        'action': f"Expand into {keyword} market (${data['market_size']:,.0f} market size)",
                        'expected_impact': f"Access to ${data['market_size']:,.0f} market opportunity",
                        'implementation_time': '6-12 months',
                        'specific_steps': [
                            'Conduct market research',
                            'Develop entry strategy',
                            'Build market presence'
                        ]
                    })
        
        return recommendations
    
    def _analyze_social_insights(self, social_data):
        """Analyze social media insights and generate recommendations"""
        recommendations = []
        
        if 'trending_topics' in social_data:
            for topic in social_data['trending_topics']:
                recommendations.append({
                    'category': 'Content Marketing',
                    'priority': 'Medium',
                    'action': f"Create content around trending topic: {topic['keyword']}",
                    'expected_impact': 'Increase social media engagement and brand visibility',
                    'implementation_time': '1-2 weeks',
                    'specific_steps': [
                        'Research trending topics in detail',
                        'Create relevant content pieces',
                        'Schedule social media posts'
                    ]
                })
        
        if 'customer_sentiment' in social_data:
            for keyword, sentiment in social_data['customer_sentiment'].items():
                if sentiment['positive_sentiment'] > 70:
                    recommendations.append({
                        'category': 'Brand Positioning',
                        'priority': 'Medium',
                        'action': f"Leverage positive sentiment around {keyword}",
                        'expected_impact': 'Build on positive market perception',
                        'implementation_time': '2-4 weeks',
                        'specific_steps': [
                            'Highlight positive aspects in marketing',
                            'Create testimonials and case studies',
                            'Amplify positive customer feedback'
                        ]
                    })
        
        return recommendations
    
    def _generate_enhanced_marketing_content(self, internal_data, external_insights):
        """Generate marketing content enhanced with external insights"""
        # Generate base marketing content
        base_content = self.marketing_ai.generate_marketing_content(internal_data)
        
        # Enhance with external insights
        enhanced_content = base_content.copy()
        
        if 'competitor_data' in external_insights:
            enhanced_content['competitive_analysis'] = self._generate_competitive_content(
                external_insights['competitor_data']
            )
        
        if 'social_data' in external_insights:
            enhanced_content['trending_content'] = self._generate_trending_content(
                external_insights['social_data']
            )
        
        return enhanced_content
    
    def _generate_competitive_content(self, competitor_data):
        """Generate content based on competitor analysis"""
        content = {
            'competitive_advantages': [],
            'differentiation_points': [],
            'market_positioning': []
        }
        
        if competitor_data:
            content['competitive_advantages'].append(
                "Based on competitor analysis, focus on unique value propositions"
            )
            content['differentiation_points'].append(
                "Highlight superior service quality and customer experience"
            )
            content['market_positioning'].append(
                "Position as premium service provider with competitive pricing"
            )
        
        return content
    
    def _generate_trending_content(self, social_data):
        """Generate content based on social media trends"""
        content = {
            'trending_topics': [],
            'hashtag_strategies': [],
            'engagement_tips': []
        }
        
        if 'trending_topics' in social_data:
            for topic in social_data['trending_topics']:
                content['trending_topics'].extend(topic['trending_topics'])
                content['hashtag_strategies'].extend(topic['hashtags'])
        
        content['engagement_tips'] = [
            'Post during peak engagement hours',
            'Use trending hashtags strategically',
            'Create shareable, valuable content'
        ]
        
        return content
    
    def _compile_enhanced_report(self, internal_data, analysis, recommendations, marketing_content, external_insights):
        """Compile all analysis into an enhanced comprehensive report"""
        report = {
            'timestamp': datetime.now().isoformat(),
            'report_type': 'Enhanced AI Analysis with External Learning',
            'executive_summary': self._create_enhanced_executive_summary(analysis, external_insights),
            'business_analysis': analysis,
            'external_insights': external_insights,
            'enhanced_recommendations': recommendations,
            'enhanced_marketing_content': marketing_content,
            'market_opportunities': self._identify_market_opportunities(external_insights),
            'competitive_analysis': self._create_competitive_analysis(external_insights),
            'implementation_roadmap': self._create_implementation_roadmap(recommendations),
            'expected_outcomes': self._predict_enhanced_outcomes(analysis, recommendations, external_insights)
        }
        
        return report
    
    def _create_enhanced_executive_summary(self, analysis, external_insights):
        """Create an enhanced executive summary with external insights"""
        summary = {
            'business_health_score': self._calculate_enhanced_business_health_score(analysis, external_insights),
            'market_position': self._assess_market_position(external_insights),
            'competitive_advantages': self._identify_competitive_advantages(external_insights),
            'growth_opportunities': self._identify_growth_opportunities(external_insights),
            'risk_factors': self._identify_risk_factors(external_insights)
        }
        
        return summary
    
    def _calculate_enhanced_business_health_score(self, analysis, external_insights):
        """Calculate business health score with external market context"""
        base_score = 50  # Start with base score
        
        # Internal factors
        if 'revenue_analysis' in analysis:
            revenue_data = analysis['revenue_analysis']
            if revenue_data.get('revenue_growth_rate', 0) > 0:
                base_score += 15
        
        # External market factors
        if 'market_data' in external_insights:
            market_data = external_insights['market_data']
            if 'industry_trends' in market_data:
                for trend in market_data['industry_trends']:
                    if trend['growth_rate'] > 10:
                        base_score += 10
        
        return min(base_score, 100)  # Cap at 100
    
    def _assess_market_position(self, external_insights):
        """Assess current market position based on external data"""
        position = {
            'market_share': 'Growing',
            'competitive_position': 'Strong',
            'market_trends': 'Favorable'
        }
        
        if 'market_data' in external_insights:
            market_data = external_insights['market_data']
            if 'market_size' in market_data:
                position['market_opportunity'] = 'High'
        
        return position
    
    def _identify_competitive_advantages(self, external_insights):
        """Identify competitive advantages based on external analysis"""
        advantages = [
            'Superior customer service',
            'Competitive pricing',
            'Quality-focused approach'
        ]
        
        if 'competitor_data' in external_insights:
            advantages.append('Market differentiation')
        
        return advantages
    
    def _identify_growth_opportunities(self, external_insights):
        """Identify growth opportunities from external insights"""
        opportunities = []
        
        if 'market_data' in external_insights:
            market_data = external_insights['market_data']
            if 'industry_trends' in market_data:
                for trend in market_data['industry_trends']:
                    if trend['growth_rate'] > 10:
                        opportunities.append(f"Expand into {trend['keyword']} market")
        
        return opportunities
    
    def _identify_risk_factors(self, external_insights):
        """Identify risk factors from external analysis"""
        risks = [
            'Market competition',
            'Economic fluctuations',
            'Technology changes'
        ]
        
        return risks
    
    def _identify_market_opportunities(self, external_insights):
        """Identify specific market opportunities"""
        opportunities = []
        
        if 'market_data' in external_insights:
            market_data = external_insights['market_data']
            if 'market_size' in market_data:
                for keyword, data in market_data['market_size'].items():
                    if data['growth_rate'] > 8:
                        opportunities.append({
                            'market': keyword,
                            'size': f"${data['market_size']:,.0f}",
                            'growth_rate': f"{data['growth_rate']:.1f}%",
                            'opportunity_level': 'High'
                        })
        
        return opportunities
    
    def _create_competitive_analysis(self, external_insights):
        """Create competitive analysis report"""
        analysis = {
            'competitor_count': 0,
            'price_range': 'Not available',
            'service_offerings': [],
            'market_gaps': []
        }
        
        if 'competitor_data' in external_insights:
            analysis['competitor_count'] = len(external_insights['competitor_data'])
            
            # Analyze pricing
            all_prices = []
            for competitor in external_insights['competitor_data']:
                if 'pricing_data' in competitor:
                    all_prices.extend([p['price'] for p in competitor['pricing_data']])
            
            if all_prices:
                analysis['price_range'] = f"${min(all_prices):.2f} - ${max(all_prices):.2f}"
        
        return analysis
    
    def _create_implementation_roadmap(self, recommendations):
        """Create implementation roadmap for recommendations"""
        roadmap = {
            'immediate_actions': [],
            'short_term_goals': [],
            'long_term_strategy': []
        }
        
        for rec in recommendations:
            if rec['priority'] == 'High':
                roadmap['immediate_actions'].append(rec['action'])
            elif rec['priority'] == 'Medium':
                roadmap['short_term_goals'].append(rec['action'])
            else:
                roadmap['long_term_strategy'].append(rec['action'])
        
        return roadmap
    
    def _predict_enhanced_outcomes(self, analysis, recommendations, external_insights):
        """Predict outcomes based on enhanced analysis"""
        outcomes = {
            'revenue_growth': '15-25%',
            'market_share': '10-20%',
            'customer_satisfaction': '85-95%',
            'operational_efficiency': '20-30% improvement'
        }
        
        if 'market_data' in external_insights:
            market_data = external_insights['market_data']
            if 'industry_trends' in market_data:
                avg_growth = sum(t['growth_rate'] for t in market_data['industry_trends']) / len(market_data['industry_trends'])
                outcomes['market_alignment'] = f"{avg_growth:.1f}% growth alignment"
        
        return outcomes
    
    def save_enhanced_report(self, report, filename=None):
        """Save enhanced report to file"""
        if not filename:
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            filename = f"ai_models/data/enhanced_ai_report_{timestamp}.json"
        
        with open(filename, 'w') as f:
            json.dump(report, f, indent=2, default=str)
        
        print(f"💾 Enhanced report saved to: {filename}")
        return filename

if __name__ == "__main__":
    # Test the enhanced AI orchestrator
    orchestrator = EnhancedAIBusinessOrchestrator()
    
    # Test with external learning
    competitor_urls = [
        "https://example-competitor1.com",
        "https://example-competitor2.com"
    ]
    
    industry_keywords = ["consulting", "marketing", "design"]
    
    report = orchestrator.generate_enhanced_analysis(
        competitor_urls=competitor_urls,
        industry_keywords=industry_keywords
    )
    
    filename = orchestrator.save_enhanced_report(report)
    print(f"✅ Enhanced AI analysis completed!")

