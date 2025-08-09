import json
import pandas as pd
from datetime import datetime
import os
import sys

# Add the ai_models directory to the Python path
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from data_collectors.internal_data import InternalDataCollector
from models.business_intelligence import BusinessIntelligenceEngine
from models.marketing_ai import MarketingAI

class AIBusinessOrchestrator:
    def __init__(self):
        """Initialize the AI Business Orchestrator"""
        self.data_collector = InternalDataCollector()
        self.business_intelligence = BusinessIntelligenceEngine()
        self.marketing_ai = MarketingAI()
        
    def generate_comprehensive_analysis(self, business_id=None):
        """Generate comprehensive business analysis and recommendations"""
        print("🤖 Starting comprehensive AI analysis...")
        
        # Step 1: Collect data
        print("📊 Collecting business data...")
        business_data = self.data_collector.export_all_data(business_id)
        
        # Step 2: Analyze business performance
        print("🧠 Analyzing business performance...")
        analysis = self.business_intelligence.analyze_business_performance(business_data)
        
        # Step 3: Generate recommendations
        print("💡 Generating business recommendations...")
        recommendations = self.business_intelligence.generate_recommendations(analysis)
        
        # Step 4: Generate marketing content
        print("📝 Generating marketing content...")
        marketing_content = self.marketing_ai.generate_marketing_content(business_data)
        
        # Step 5: Compile comprehensive report
        print("📋 Compiling comprehensive report...")
        comprehensive_report = self._compile_comprehensive_report(
            business_data, analysis, recommendations, marketing_content
        )
        
        return comprehensive_report
    
    def _compile_comprehensive_report(self, business_data, analysis, recommendations, marketing_content):
        """Compile all analysis into a comprehensive report"""
        report = {
            'timestamp': datetime.now().isoformat(),
            'executive_summary': self._create_executive_summary(analysis),
            'business_analysis': analysis,
            'recommendations': recommendations,
            'marketing_content': marketing_content,
            'action_plan': self._create_action_plan(recommendations, marketing_content),
            'implementation_timeline': self._create_implementation_timeline(recommendations),
            'expected_outcomes': self._predict_expected_outcomes(analysis, recommendations)
        }
        
        return report
    
    def _create_executive_summary(self, analysis):
        """Create an executive summary of the analysis"""
        summary = {
            'business_health_score': self._calculate_business_health_score(analysis),
            'key_metrics': self._extract_key_metrics(analysis),
            'critical_insights': self._identify_critical_insights(analysis),
            'priority_actions': self._identify_priority_actions(analysis)
        }
        
        return summary
    
    def _calculate_business_health_score(self, analysis):
        """Calculate overall business health score (0-100)"""
        score = 50  # Base score
        
        # Revenue analysis
        if 'revenue_analysis' in analysis:
            revenue_data = analysis['revenue_analysis']
            if revenue_data.get('revenue_growth_rate', 0) > 0:
                score += 15
            if revenue_data.get('current_daily_revenue', 0) > 0:
                score += 10
        
        # Customer analysis
        if 'customer_analysis' in analysis:
            customer_data = analysis['customer_analysis']
            if customer_data.get('total_customers', 0) > 0:
                score += 10
            if len(customer_data.get('high_value_customers', [])) > 0:
                score += 5
        
        # Operational metrics
        if 'operational_metrics' in analysis:
            metrics = analysis['operational_metrics']
            if metrics.get('avg_order_value', 0) > 500:
                score += 10
            if metrics.get('customer_lifetime_value', 0) > 1000:
                score += 10
        
        return min(score, 100)
    
    def _extract_key_metrics(self, analysis):
        """Extract key performance metrics"""
        metrics = {}
        
        if 'operational_metrics' in analysis:
            op_metrics = analysis['operational_metrics']
            metrics.update({
                'total_revenue': op_metrics.get('total_revenue', 0),
                'total_customers': op_metrics.get('total_customers', 0),
                'avg_order_value': op_metrics.get('avg_order_value', 0),
                'customer_lifetime_value': op_metrics.get('customer_lifetime_value', 0)
            })
        
        if 'revenue_analysis' in analysis:
            revenue_data = analysis['revenue_analysis']
            metrics.update({
                'revenue_growth_rate': revenue_data.get('revenue_growth_rate', 0),
                'current_daily_revenue': revenue_data.get('current_daily_revenue', 0)
            })
        
        return metrics
    
    def _identify_critical_insights(self, analysis):
        """Identify critical business insights"""
        insights = []
        
        # Revenue insights
        if 'revenue_analysis' in analysis:
            revenue_data = analysis['revenue_analysis']
            if revenue_data.get('revenue_growth_rate', 0) < 0:
                insights.append("⚠️ Revenue is declining - immediate action needed")
            elif revenue_data.get('revenue_growth_rate', 0) < 5:
                insights.append("📈 Revenue growth is slow - optimization opportunities available")
        
        # Customer insights
        if 'customer_analysis' in analysis:
            customer_data = analysis['customer_analysis']
            if len(customer_data.get('churn_risk_customers', [])) > 0:
                insights.append("🚨 Customers at risk of churning - retention strategies needed")
            if customer_data.get('total_customers', 0) == 0:
                insights.append("👥 No customers yet - focus on customer acquisition")
        
        # Product insights
        if 'product_analysis' in analysis:
            product_data = analysis['product_analysis']
            if product_data.get('stock_optimization', {}).get('low_stock_count', 0) > 0:
                insights.append("📦 Low stock items detected - restocking needed")
        
        return insights
    
    def _identify_priority_actions(self, analysis):
        """Identify priority actions based on analysis"""
        actions = []
        
        # Revenue optimization
        if 'revenue_analysis' in analysis:
            revenue_data = analysis['revenue_analysis']
            if revenue_data.get('revenue_growth_rate', 0) < 5:
                actions.append({
                    'priority': 'High',
                    'action': 'Implement revenue optimization strategies',
                    'timeline': '2-4 weeks',
                    'expected_impact': '15-25% revenue increase'
                })
        
        # Customer retention
        if 'customer_analysis' in analysis:
            customer_data = analysis['customer_analysis']
            if len(customer_data.get('churn_risk_customers', [])) > 0:
                actions.append({
                    'priority': 'High',
                    'action': 'Launch customer retention campaign',
                    'timeline': '1-2 weeks',
                    'expected_impact': 'Reduce churn by 30-40%'
                })
        
        # Inventory management
        if 'product_analysis' in analysis:
            product_data = analysis['product_analysis']
            if product_data.get('stock_optimization', {}).get('low_stock_count', 0) > 0:
                actions.append({
                    'priority': 'Medium',
                    'action': 'Restock low inventory items',
                    'timeline': '1 week',
                    'expected_impact': 'Prevent lost sales'
                })
        
        return actions
    
    def _create_action_plan(self, recommendations, marketing_content):
        """Create a detailed action plan"""
        action_plan = {
            'immediate_actions': [],
            'short_term_goals': [],
            'long_term_strategies': [],
            'marketing_actions': []
        }
        
        # Categorize recommendations
        for rec in recommendations:
            if rec.get('priority') == 'High':
                action_plan['immediate_actions'].append(rec)
            elif rec.get('priority') == 'Medium':
                action_plan['short_term_goals'].append(rec)
            else:
                action_plan['long_term_strategies'].append(rec)
        
        # Add marketing actions
        if marketing_content.get('marketing_strategy'):
            action_plan['marketing_actions'] = [
                'Implement social media strategy',
                'Launch email campaigns',
                'Create advertising content',
                'Develop video content'
            ]
        
        return action_plan
    
    def _create_implementation_timeline(self, recommendations):
        """Create implementation timeline"""
        timeline = {
            'week_1': [],
            'week_2_4': [],
            'month_2_3': [],
            'month_4_6': []
        }
        
        for rec in recommendations:
            timeline_text = rec.get('implementation_time', '2-4 weeks')
            
            if '1 week' in timeline_text or 'immediate' in timeline_text.lower():
                timeline['week_1'].append(rec)
            elif '2-4 weeks' in timeline_text:
                timeline['week_2_4'].append(rec)
            elif '2-3 weeks' in timeline_text:
                timeline['week_2_4'].append(rec)
            elif '3-6 weeks' in timeline_text:
                timeline['month_2_3'].append(rec)
            else:
                timeline['month_4_6'].append(rec)
        
        return timeline
    
    def _predict_expected_outcomes(self, analysis, recommendations):
        """Predict expected outcomes based on recommendations"""
        outcomes = {
            'revenue_impact': 0,
            'customer_impact': 0,
            'operational_impact': 0,
            'timeline': '3-6 months'
        }
        
        # Calculate expected revenue impact
        for rec in recommendations:
            if 'revenue' in rec.get('category', '').lower():
                impact_text = rec.get('expected_impact', '')
                if '15-25%' in impact_text:
                    outcomes['revenue_impact'] += 20
                elif '20-30%' in impact_text:
                    outcomes['revenue_impact'] += 25
        
        # Calculate customer impact
        for rec in recommendations:
            if 'customer' in rec.get('category', '').lower():
                impact_text = rec.get('expected_impact', '')
                if '30-40%' in impact_text:
                    outcomes['customer_impact'] += 35
        
        return outcomes
    
    def save_report(self, report, filename=None):
        """Save the comprehensive report to a file"""
        if not filename:
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            filename = f"ai_business_report_{timestamp}.json"
        
        filepath = os.path.join("ai_models", "data", filename)
        
        with open(filepath, 'w') as f:
            json.dump(report, f, indent=2, default=str)
        
        print(f"📄 Report saved to: {filepath}")
        return filepath
    
    def generate_specific_analysis(self, business_id, analysis_type):
        """Generate specific type of analysis"""
        if analysis_type == 'revenue':
            return self._analyze_revenue_optimization(business_id)
        elif analysis_type == 'marketing':
            return self._analyze_marketing_opportunities(business_id)
        elif analysis_type == 'customers':
            return self._analyze_customer_behavior(business_id)
        elif analysis_type == 'operations':
            return self._analyze_operational_efficiency(business_id)
        else:
            return self.generate_comprehensive_analysis(business_id)
    
    def _analyze_revenue_optimization(self, business_id):
        """Specific revenue optimization analysis"""
        business_data = self.data_collector.export_all_data(business_id)
        analysis = self.business_intelligence.analyze_business_performance(business_data)
        
        return {
            'analysis_type': 'Revenue Optimization',
            'revenue_analysis': analysis.get('revenue_analysis', {}),
            'pricing_recommendations': self._generate_pricing_recommendations(analysis),
            'growth_opportunities': analysis.get('growth_opportunities', [])
        }
    
    def _analyze_marketing_opportunities(self, business_id):
        """Specific marketing analysis"""
        business_data = self.data_collector.export_all_data(business_id)
        marketing_content = self.marketing_ai.generate_marketing_content(business_data)
        
        return {
            'analysis_type': 'Marketing Opportunities',
            'marketing_content': marketing_content,
            'target_audience': marketing_content.get('marketing_strategy', {}).get('target_audience', {}),
            'content_calendar': marketing_content.get('marketing_strategy', {}).get('content_calendar', {})
        }
    
    def _analyze_customer_behavior(self, business_id):
        """Specific customer behavior analysis"""
        business_data = self.data_collector.export_all_data(business_id)
        analysis = self.business_intelligence.analyze_business_performance(business_data)
        
        return {
            'analysis_type': 'Customer Behavior',
            'customer_analysis': analysis.get('customer_analysis', {}),
            'retention_strategies': self._generate_retention_strategies(analysis),
            'loyalty_program': self._design_loyalty_program(analysis)
        }
    
    def _analyze_operational_efficiency(self, business_id):
        """Specific operational efficiency analysis"""
        business_data = self.data_collector.export_all_data(business_id)
        analysis = self.business_intelligence.analyze_business_performance(business_data)
        
        return {
            'analysis_type': 'Operational Efficiency',
            'operational_metrics': analysis.get('operational_metrics', {}),
            'product_analysis': analysis.get('product_analysis', {}),
            'service_analysis': analysis.get('service_analysis', {}),
            'optimization_recommendations': self._generate_optimization_recommendations(analysis)
        }
    
    def _generate_pricing_recommendations(self, analysis):
        """Generate pricing optimization recommendations"""
        recommendations = []
        
        if 'product_analysis' in analysis:
            product_data = analysis['product_analysis']
            if product_data.get('pricing_opportunities', {}).get('low_margin_count', 0) > 0:
                recommendations.append({
                    'type': 'pricing_optimization',
                    'description': 'Optimize pricing for low-margin products',
                    'expected_impact': 'Increase profit margins by 10-15%'
                })
        
        return recommendations
    
    def _generate_retention_strategies(self, analysis):
        """Generate customer retention strategies"""
        strategies = []
        
        if 'customer_analysis' in analysis:
            customer_data = analysis['customer_analysis']
            if len(customer_data.get('churn_risk_customers', [])) > 0:
                strategies.append({
                    'type': 'personalized_engagement',
                    'description': 'Implement personalized engagement campaigns',
                    'target': 'High-risk customers',
                    'expected_impact': 'Reduce churn by 30-40%'
                })
        
        return strategies
    
    def _design_loyalty_program(self, analysis):
        """Design customer loyalty program"""
        if 'customer_analysis' not in analysis:
            return {}
        
        customer_data = analysis['customer_analysis']
        segments = customer_data.get('segments', {})
        
        loyalty_program = {
            'tiers': {
                'bronze': {'requirements': 'New customers', 'benefits': ['Welcome discount', 'Newsletter access']},
                'silver': {'requirements': '3+ orders', 'benefits': ['10% discount', 'Priority support', 'Exclusive offers']},
                'gold': {'requirements': '10+ orders', 'benefits': ['15% discount', 'VIP support', 'Early access', 'Free shipping']},
                'platinum': {'requirements': '20+ orders', 'benefits': ['20% discount', 'Personal account manager', 'Exclusive events', 'Custom offers']}
            },
            'points_system': {
                'earn_rate': '1 point per $1 spent',
                'redemption_rate': '100 points = $1 discount',
                'bonus_events': ['Birthday bonus', 'Anniversary bonus', 'Referral bonus']
            }
        }
        
        return loyalty_program
    
    def _generate_optimization_recommendations(self, analysis):
        """Generate operational optimization recommendations"""
        recommendations = []
        
        # Inventory optimization
        if 'product_analysis' in analysis:
            product_data = analysis['product_analysis']
            if product_data.get('stock_optimization', {}).get('low_stock_count', 0) > 0:
                recommendations.append({
                    'type': 'inventory_management',
                    'description': 'Implement automated inventory management',
                    'expected_impact': 'Reduce stockouts by 80%'
                })
        
        # Service optimization
        if 'service_analysis' in analysis:
            service_data = analysis['service_analysis']
            if service_data.get('pricing_optimization', {}).get('services_to_optimize', 0) > 0:
                recommendations.append({
                    'type': 'service_bundling',
                    'description': 'Create service bundles for better value',
                    'expected_impact': 'Increase service revenue by 25%'
                })
        
        return recommendations

if __name__ == "__main__":
    # Test the AI orchestrator
    orchestrator = AIBusinessOrchestrator()
    print("🤖 AI Business Orchestrator initialized successfully!")
    
    # Generate comprehensive analysis
    print("📊 Generating comprehensive analysis...")
    report = orchestrator.generate_comprehensive_analysis()
    
    # Save report
    orchestrator.save_report(report)
    
    print("✅ AI analysis completed successfully!")
