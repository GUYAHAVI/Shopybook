import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestRegressor, RandomForestClassifier
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler
from sklearn.model_selection import train_test_split
import json
from datetime import datetime, timedelta
import warnings
warnings.filterwarnings('ignore')

class BusinessIntelligenceEngine:
    def __init__(self):
        """Initialize the business intelligence engine"""
        self.revenue_predictor = RandomForestRegressor(n_estimators=100, random_state=42)
        self.customer_segmenter = KMeans(n_clusters=5, random_state=42)
        self.churn_predictor = RandomForestClassifier(n_estimators=100, random_state=42)
        self.scaler = StandardScaler()
        
    def analyze_business_performance(self, business_data):
        """Comprehensive business performance analysis"""
        analysis = {
            'revenue_analysis': self._analyze_revenue_trends(business_data),
            'customer_analysis': self._analyze_customer_segments(business_data),
            'product_analysis': self._analyze_product_performance(business_data),
            'service_analysis': self._analyze_service_performance(business_data),
            'operational_metrics': self._calculate_operational_metrics(business_data),
            'growth_opportunities': self._identify_growth_opportunities(business_data)
        }
        return analysis
    
    def _analyze_revenue_trends(self, data):
        """Analyze revenue trends and patterns"""
        if 'time_series' not in data or data['time_series'].empty:
            return {'status': 'insufficient_data', 'message': 'No time series data available'}
        
        ts_data = pd.DataFrame(data['time_series'])
        ts_data['date'] = pd.to_datetime(ts_data['date'])
        ts_data = ts_data.sort_values('date')
        
        # Calculate growth rates
        ts_data['revenue_growth'] = ts_data['daily_revenue'].pct_change() * 100
        ts_data['order_growth'] = ts_data['daily_orders'].pct_change() * 100
        
        # Identify trends
        recent_data = ts_data.tail(30)
        avg_daily_revenue = recent_data['daily_revenue'].mean()
        revenue_trend = recent_data['revenue_growth'].mean()
        
        # Seasonal patterns
        ts_data['month'] = ts_data['date'].dt.month
        monthly_avg = ts_data.groupby('month')['daily_revenue'].mean()
        
        return {
            'current_daily_revenue': avg_daily_revenue,
            'revenue_growth_rate': revenue_trend,
            'trend_direction': 'increasing' if revenue_trend > 0 else 'decreasing',
            'seasonal_patterns': monthly_avg.to_dict(),
            'volatility': ts_data['daily_revenue'].std(),
            'best_performing_days': self._identify_best_days(ts_data)
        }
    
    def _analyze_customer_segments(self, data):
        """Analyze customer behavior and create segments"""
        if 'customer_behavior' not in data or data['customer_behavior'].empty:
            return {'status': 'insufficient_data', 'message': 'No customer data available'}
        
        customer_df = pd.DataFrame(data['customer_behavior'])
        
        # Check if we have enough data for clustering
        if len(customer_df) < 5:
            return {
                'status': 'insufficient_data',
                'message': f'Only {len(customer_df)} customers available, need at least 5 for segmentation',
                'total_customers': len(customer_df),
                'basic_analysis': {
                    'avg_spent': customer_df['total_spent'].mean() if 'total_spent' in customer_df.columns else 0,
                    'avg_orders': customer_df['total_orders'].mean() if 'total_orders' in customer_df.columns else 0,
                    'total_revenue': customer_df['total_spent'].sum() if 'total_spent' in customer_df.columns else 0
                }
            }
        
        # Create customer segments based on behavior
        features = ['total_orders', 'total_spent', 'avg_order_value', 'days_since_registration']
        available_features = [f for f in features if f in customer_df.columns]
        
        if len(available_features) < 2:
            return {
                'status': 'insufficient_features',
                'message': f'Only {len(available_features)} features available, need at least 2 for segmentation',
                'total_customers': len(customer_df),
                'basic_analysis': {
                    'avg_spent': customer_df['total_spent'].mean() if 'total_spent' in customer_df.columns else 0,
                    'avg_orders': customer_df['total_orders'].mean() if 'total_orders' in customer_df.columns else 0
                }
            }
        
        customer_features = customer_df[available_features].fillna(0)
        
        # Normalize features
        customer_features_scaled = self.scaler.fit_transform(customer_features)
        
        # Determine number of clusters (max 5, but not more than data points)
        n_clusters = min(5, len(customer_df))
        
        # Create a new KMeans instance with the appropriate number of clusters
        customer_segmenter = KMeans(n_clusters=n_clusters, random_state=42)
        
        # Segment customers
        segments = customer_segmenter.fit_predict(customer_features_scaled)
        customer_df['segment'] = segments
        
        # Analyze segments
        segment_analysis = {}
        for segment in range(n_clusters):
            segment_data = customer_df[customer_df['segment'] == segment]
            segment_analysis[f'segment_{segment}'] = {
                'count': len(segment_data),
                'avg_spent': segment_data['total_spent'].mean() if 'total_spent' in segment_data.columns else 0,
                'avg_orders': segment_data['total_orders'].mean() if 'total_orders' in segment_data.columns else 0,
                'loyalty_score': self._calculate_loyalty_score(segment_data)
            }
        
        return {
            'total_customers': len(customer_df),
            'segments': segment_analysis,
            'high_value_customers': self._identify_high_value_customers(customer_df),
            'churn_risk_customers': self._identify_churn_risk_customers(customer_df)
        }
    
    def _analyze_product_performance(self, data):
        """Analyze product performance and profitability"""
        if 'product_performance' not in data or data['product_performance'].empty:
            return {'status': 'insufficient_data', 'message': 'No product data available'}
        
        product_df = pd.DataFrame(data['product_performance'])
        
        # Calculate key metrics
        product_df['profit_margin'] = (product_df['total_profit'] / product_df['total_revenue']) * 100
        product_df['turnover_rate'] = product_df['total_quantity_sold'] / product_df['stock_quantity']
        
        # Identify top performers
        top_products = product_df.nlargest(5, 'total_revenue')
        low_performers = product_df.nsmallest(5, 'total_revenue')
        
        return {
            'total_products': len(product_df),
            'avg_profit_margin': product_df['profit_margin'].mean(),
            'top_performing_products': top_products.to_dict('records'),
            'low_performing_products': low_performers.to_dict('records'),
            'stock_optimization': self._analyze_stock_levels(product_df),
            'pricing_opportunities': self._identify_pricing_opportunities(product_df)
        }
    
    def _analyze_service_performance(self, data):
        """Analyze service performance and optimization opportunities"""
        if 'service_performance' not in data or data['service_performance'].empty:
            return {'status': 'insufficient_data', 'message': 'No service data available'}
        
        service_df = pd.DataFrame(data['service_performance'])
        
        # Calculate service metrics
        service_df['revenue_per_booking'] = service_df['total_revenue'] / service_df['total_bookings']
        service_df['utilization_rate'] = service_df['total_bookings'] / service_df['total_bookings'].max()
        
        # Identify opportunities
        top_services = service_df.nlargest(5, 'total_revenue')
        underperforming_services = service_df.nsmallest(5, 'total_revenue')
        
        return {
            'total_services': len(service_df),
            'avg_booking_value': service_df['avg_booking_value'].mean(),
            'top_services': top_services.to_dict('records'),
            'underperforming_services': underperforming_services.to_dict('records'),
            'pricing_optimization': self._optimize_service_pricing(service_df),
            'bundle_opportunities': self._identify_bundle_opportunities(service_df)
        }
    
    def _calculate_operational_metrics(self, data):
        """Calculate key operational metrics"""
        business_metrics = pd.DataFrame(data.get('business_metrics', []))
        
        if business_metrics.empty:
            return {'status': 'insufficient_data'}
        
        metrics = business_metrics.iloc[0] if len(business_metrics) > 0 else {}
        
        return {
            'total_revenue': metrics.get('completed_revenue', 0) + metrics.get('service_revenue', 0),
            'total_customers': metrics.get('total_customers', 0),
            'avg_order_value': metrics.get('avg_order_value', 0),
            'avg_booking_value': metrics.get('avg_booking_value', 0),
            'customer_acquisition_cost': self._estimate_cac(metrics),
            'customer_lifetime_value': self._calculate_clv(metrics),
            'inventory_turnover': self._calculate_inventory_turnover(metrics)
        }
    
    def _identify_growth_opportunities(self, data):
        """Identify specific growth opportunities"""
        opportunities = []
        
        # Revenue opportunities
        if 'business_metrics' in data and not data['business_metrics'].empty:
            metrics = pd.DataFrame(data['business_metrics']).iloc[0]
            
            # Pricing optimization
            if metrics.get('avg_order_value', 0) < 1000:
                opportunities.append({
                    'type': 'pricing_optimization',
                    'priority': 'high',
                    'description': 'Average order value is low. Consider upselling and cross-selling strategies.',
                    'expected_impact': '15-25% revenue increase',
                    'implementation_time': '2-4 weeks'
                })
            
            # Customer retention
            if metrics.get('total_customers', 0) > 0:
                repeat_customer_rate = self._calculate_repeat_customer_rate(data)
                if repeat_customer_rate < 0.3:
                    opportunities.append({
                        'type': 'customer_retention',
                        'priority': 'high',
                        'description': 'Low repeat customer rate. Implement loyalty program.',
                        'expected_impact': '30-40% retention improvement',
                        'implementation_time': '3-6 weeks'
                    })
        
        # Product opportunities
        if 'product_performance' in data and not data['product_performance'].empty:
            product_df = pd.DataFrame(data['product_performance'])
            low_stock_products = product_df[product_df['stock_status'] == 'Low Stock']
            
            if len(low_stock_products) > 0:
                opportunities.append({
                    'type': 'inventory_management',
                    'priority': 'medium',
                    'description': f'{len(low_stock_products)} products are low on stock. Restock to avoid lost sales.',
                    'expected_impact': 'Prevent 10-15% revenue loss',
                    'implementation_time': '1-2 weeks'
                })
        
        return opportunities
    
    def generate_recommendations(self, analysis):
        """Generate actionable business recommendations"""
        recommendations = []
        
        # Revenue optimization
        if 'revenue_analysis' in analysis:
            revenue_data = analysis['revenue_analysis']
            if revenue_data.get('revenue_growth_rate', 0) < 5:
                recommendations.append({
                    'category': 'Revenue Growth',
                    'priority': 'High',
                    'action': 'Implement dynamic pricing and promotional campaigns',
                    'expected_impact': '15-25% revenue increase',
                    'implementation_time': '2-4 weeks',
                    'specific_steps': [
                        'Analyze competitor pricing',
                        'Create targeted promotions',
                        'Optimize product mix'
                    ]
                })
        
        # Customer retention
        if 'customer_analysis' in analysis:
            customer_data = analysis['customer_analysis']
            if customer_data.get('total_customers', 0) > 0:
                recommendations.append({
                    'category': 'Customer Retention',
                    'priority': 'High',
                    'action': 'Launch personalized loyalty program',
                    'expected_impact': 'Reduce churn by 30-40%',
                    'implementation_time': '3-6 weeks',
                    'specific_steps': [
                        'Create tiered loyalty system',
                        'Implement personalized rewards',
                        'Set up automated engagement campaigns'
                    ]
                })
        
        # Operational efficiency
        if 'operational_metrics' in analysis:
            metrics = analysis['operational_metrics']
            if metrics.get('avg_order_value', 0) < 1000:
                recommendations.append({
                    'category': 'Order Value Optimization',
                    'priority': 'Medium',
                    'action': 'Implement upselling and cross-selling strategies',
                    'expected_impact': 'Increase AOV by 20-30%',
                    'implementation_time': '2-3 weeks',
                    'specific_steps': [
                        'Train staff on upselling techniques',
                        'Create product bundles',
                        'Implement recommendation engine'
                    ]
                })
        
        return recommendations
    
    # Helper methods
    def _identify_best_days(self, ts_data):
        """Identify best performing days of the week"""
        ts_data['day_of_week'] = ts_data['date'].dt.day_name()
        daily_performance = ts_data.groupby('day_of_week')['daily_revenue'].mean()
        return daily_performance.nlargest(3).to_dict()
    
    def _calculate_loyalty_score(self, segment_data):
        """Calculate customer loyalty score"""
        if len(segment_data) == 0:
            return 0
        return (segment_data['total_orders'].mean() * segment_data['total_spent'].mean()) / 1000
    
    def _identify_high_value_customers(self, customer_df):
        """Identify high-value customers"""
        high_value_threshold = customer_df['total_spent'].quantile(0.8)
        high_value_customers = customer_df[customer_df['total_spent'] >= high_value_threshold]
        return high_value_customers.to_dict('records')
    
    def _identify_churn_risk_customers(self, customer_df):
        """Identify customers at risk of churning"""
        churn_risk = customer_df[
            (customer_df['days_since_last_order'] > 30) & 
            (customer_df['total_orders'] > 0)
        ]
        return churn_risk.to_dict('records')
    
    def _analyze_stock_levels(self, product_df):
        """Analyze stock levels and recommend actions"""
        low_stock = product_df[product_df['stock_status'] == 'Low Stock']
        return {
            'low_stock_count': len(low_stock),
            'products_to_restock': low_stock['product_name'].tolist(),
            'estimated_lost_revenue': low_stock['total_revenue'].sum() * 0.1
        }
    
    def _identify_pricing_opportunities(self, product_df):
        """Identify pricing optimization opportunities"""
        low_margin_products = product_df[product_df['profit_margin'] < 20]
        return {
            'low_margin_count': len(low_margin_products),
            'products_to_reprice': low_margin_products['product_name'].tolist(),
            'potential_margin_improvement': (30 - low_margin_products['profit_margin'].mean()) * len(low_margin_products)
        }
    
    def _optimize_service_pricing(self, service_df):
        """Optimize service pricing based on performance"""
        underperforming = service_df[service_df['total_bookings'] < service_df['total_bookings'].median()]
        return {
            'services_to_optimize': len(underperforming),
            'pricing_strategies': [
                'Bundle underperforming services',
                'Offer introductory pricing',
                'Create premium tiers'
            ]
        }
    
    def _identify_bundle_opportunities(self, service_df):
        """Identify service bundling opportunities"""
        popular_services = service_df.nlargest(3, 'total_bookings')
        return {
            'bundle_candidates': popular_services['service_name'].tolist(),
            'expected_bundle_value': popular_services['avg_booking_value'].sum() * 0.8
        }
    
    def _estimate_cac(self, metrics):
        """Estimate customer acquisition cost"""
        # Simplified calculation
        return metrics.get('total_revenue', 0) * 0.1 / max(metrics.get('total_customers', 1), 1)
    
    def _calculate_clv(self, metrics):
        """Calculate customer lifetime value"""
        avg_order_value = metrics.get('avg_order_value', 0)
        avg_orders_per_customer = metrics.get('total_orders', 0) / max(metrics.get('total_customers', 1), 1)
        return avg_order_value * avg_orders_per_customer * 2  # Assuming 2-year customer lifetime
    
    def _calculate_inventory_turnover(self, metrics):
        """Calculate inventory turnover rate"""
        total_stock = metrics.get('total_stock', 0)
        total_orders = metrics.get('total_orders', 0)
        return total_orders / max(total_stock, 1)
    
    def _calculate_repeat_customer_rate(self, data):
        """Calculate repeat customer rate"""
        if 'customer_behavior' not in data:
            return 0
        
        customer_df = pd.DataFrame(data['customer_behavior'])
        repeat_customers = customer_df[customer_df['total_orders'] > 1]
        return len(repeat_customers) / max(len(customer_df), 1)

if __name__ == "__main__":
    # Test the business intelligence engine
    engine = BusinessIntelligenceEngine()
    print("Business Intelligence Engine initialized successfully!")
