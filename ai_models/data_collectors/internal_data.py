import pandas as pd
import numpy as np
from sqlalchemy import create_engine, text
import json
from datetime import datetime, timedelta
import os
from dotenv import load_dotenv

load_dotenv()

class InternalDataCollector:
    def __init__(self, db_connection_string=None):
        """Initialize data collector with database connection"""
        if db_connection_string:
            self.engine = create_engine(db_connection_string)
        else:
            # Default Laravel database connection
            db_host = os.getenv('DB_HOST', 'localhost')
            db_port = os.getenv('DB_PORT', '3306')
            db_name = os.getenv('DB_DATABASE', 'shopybook')
            db_user = os.getenv('DB_USERNAME', 'root')
            db_password = os.getenv('DB_PASSWORD', '')
            
            self.engine = create_engine(
                f"mysql+mysqlconnector://{db_user}:{db_password}@{db_host}:{db_port}/{db_name}"
            )
    
    def collect_business_metrics(self):
        """Collect comprehensive business performance data"""
        query = """
        SELECT 
            b.id as business_id,
            b.name as business_name,
            b.business_type,
            b.business_category,
            b.created_at as business_created_at,
            
            -- Order metrics
            COUNT(DISTINCT o.id) as total_orders,
            SUM(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE 0 END) as completed_revenue,
            SUM(CASE WHEN o.status = 'pending' THEN o.total_amount ELSE 0 END) as pending_revenue,
            AVG(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE NULL END) as avg_order_value,
            
            -- Service metrics
            COUNT(DISTINCT sb.id) as total_bookings,
            SUM(CASE WHEN sb.payment_status = 'paid' THEN sb.final_amount ELSE 0 END) as service_revenue,
            AVG(CASE WHEN sb.payment_status = 'paid' THEN sb.final_amount ELSE NULL END) as avg_booking_value,
            
            -- Customer metrics
            COUNT(DISTINCT c.id) as total_customers,
            COUNT(DISTINCT CASE WHEN c.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN c.id END) as new_customers_30d,
            
            -- Product metrics
            COUNT(DISTINCT p.id) as total_products,
            SUM(p.stock_quantity) as total_stock,
            COUNT(DISTINCT CASE WHEN p.stock_quantity <= 10 THEN p.id END) as low_stock_products,
            
            -- Service metrics
            COUNT(DISTINCT s.id) as total_services,
            COUNT(DISTINCT CASE WHEN s.is_active = 1 THEN s.id END) as active_services
            
        FROM businesses b
        LEFT JOIN orders o ON b.id = o.business_id
        LEFT JOIN service_bookings sb ON b.id = sb.business_id
        LEFT JOIN customers c ON b.id = c.business_id
        LEFT JOIN products p ON b.id = p.business_id
        LEFT JOIN services s ON b.id = s.business_id
        GROUP BY b.id, b.name, b.business_type, b.business_category, b.created_at
        """
        
        try:
            df = pd.read_sql(query, self.engine)
            return df
        except Exception as e:
            print(f"Error collecting business metrics: {e}")
            return pd.DataFrame()
    
    def collect_customer_behavior(self, business_id=None):
        """Collect detailed customer behavior data"""
        where_clause = f"WHERE b.id = '{business_id}'" if business_id else ""
        
        query = f"""
        SELECT 
            c.id as customer_id,
            c.name as customer_name,
            c.email,
            c.created_at as customer_created_at,
            
            -- Order behavior
            COUNT(DISTINCT o.id) as total_orders,
            SUM(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE 0 END) as total_spent,
            AVG(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE NULL END) as avg_order_value,
            MAX(o.created_at) as last_order_date,
            
            -- Service behavior
            COUNT(DISTINCT sb.id) as total_bookings,
            SUM(CASE WHEN sb.payment_status = 'paid' THEN sb.final_amount ELSE 0 END) as total_service_spent,
            
            -- Engagement metrics
            DATEDIFF(NOW(), c.created_at) as days_since_registration,
            DATEDIFF(NOW(), MAX(o.created_at)) as days_since_last_order
            
        FROM customers c
        LEFT JOIN orders o ON c.id = o.customer_id
        LEFT JOIN service_bookings sb ON c.id = sb.customer_id
        {where_clause}
        GROUP BY c.id, c.name, c.email, c.created_at
        """
        
        try:
            df = pd.read_sql(query, self.engine)
            return df
        except Exception as e:
            print(f"Error collecting customer behavior: {e}")
            return pd.DataFrame()
    
    def collect_product_performance(self, business_id=None):
        """Collect product performance data"""
        where_clause = f"WHERE p.business_id = '{business_id}'" if business_id else ""
        
        query = f"""
        SELECT 
            p.id as product_id,
            p.name as product_name,
            p.category,
            p.price,
            p.stock_quantity,
            p.cost_price,
            
            -- Sales metrics
            COUNT(DISTINCT oi.order_id) as times_ordered,
            SUM(oi.quantity) as total_quantity_sold,
            SUM(oi.total) as total_revenue,
            AVG(oi.quantity) as avg_quantity_per_order,
            
            -- Profitability
            SUM(oi.quantity * p.cost_price) as total_cost,
            SUM(oi.total - (oi.quantity * p.cost_price)) as total_profit,
            
            -- Stock metrics
            (p.stock_quantity - SUM(oi.quantity)) as remaining_stock,
            CASE 
                WHEN p.stock_quantity <= 10 THEN 'Low Stock'
                WHEN p.stock_quantity <= 50 THEN 'Medium Stock'
                ELSE 'High Stock'
            END as stock_status
            
        FROM products p
        LEFT JOIN order_items oi ON p.id = oi.product_id
        LEFT JOIN orders o ON oi.order_id = o.id AND o.status = 'completed'
        {where_clause}
        GROUP BY p.id, p.name, p.category, p.price, p.stock_quantity, p.cost_price
        """
        
        try:
            df = pd.read_sql(query, self.engine)
            return df
        except Exception as e:
            print(f"Error collecting product performance: {e}")
            return pd.DataFrame()
    
    def collect_service_performance(self, business_id=None):
        """Collect service performance data"""
        where_clause = f"WHERE s.business_id = '{business_id}'" if business_id else ""
        
        query = f"""
        SELECT 
            s.id as service_id,
            s.name as service_name,
            s.price,
            s.duration,
            s.commission_rate,
            
            -- Booking metrics
            COUNT(DISTINCT si.service_booking_id) as total_bookings,
            SUM(CASE WHEN sb.payment_status = 'paid' THEN si.amount ELSE 0 END) as total_revenue,
            AVG(CASE WHEN sb.payment_status = 'paid' THEN si.amount ELSE NULL END) as avg_booking_value,
            
            -- Staff performance
            COUNT(DISTINCT si.staff_id) as staff_count,
            
            -- Popularity metrics
            COUNT(DISTINCT si.service_booking_id) / 
            (SELECT COUNT(DISTINCT id) FROM services WHERE business_id = s.business_id) as popularity_score
            
        FROM services s
        LEFT JOIN service_items si ON s.id = si.service_id
        LEFT JOIN service_bookings sb ON si.service_booking_id = sb.id
        {where_clause}
        GROUP BY s.id, s.name, s.price, s.duration, s.commission_rate
        """
        
        try:
            df = pd.read_sql(query, self.engine)
            return df
        except Exception as e:
            print(f"Error collecting service performance: {e}")
            return pd.DataFrame()
    
    def collect_time_series_data(self, business_id=None, days=90):
        """Collect time series data for trend analysis"""
        where_clause = f"AND b.id = '{business_id}'" if business_id else ""
        
        query = f"""
        SELECT 
            DATE(o.created_at) as date,
            COUNT(DISTINCT o.id) as daily_orders,
            SUM(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE 0 END) as daily_revenue,
            COUNT(DISTINCT o.customer_id) as daily_customers,
            AVG(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE NULL END) as avg_order_value
            
        FROM orders o
        JOIN businesses b ON o.business_id = b.id
        WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL {days} DAY)
        {where_clause}
        GROUP BY DATE(o.created_at)
        ORDER BY date
        """
        
        try:
            df = pd.read_sql(query, self.engine)
            return df
        except Exception as e:
            print(f"Error collecting time series data: {e}")
            return pd.DataFrame()
    
    def export_all_data(self, business_id=None):
        """Export all collected data for AI analysis"""
        data = {
            'business_metrics': self.collect_business_metrics(),
            'customer_behavior': self.collect_customer_behavior(business_id),
            'product_performance': self.collect_product_performance(business_id),
            'service_performance': self.collect_service_performance(business_id),
            'time_series': self.collect_time_series_data(business_id)
        }
        
        # Save to JSON for easy processing
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        filename = f"business_data_{business_id or 'all'}_{timestamp}.json"
        
        # Convert DataFrames to JSON-serializable format
        json_data = {}
        for key, df in data.items():
            json_data[key] = df.to_dict('records') if not df.empty else []
        
        with open(f"data/{filename}", 'w') as f:
            json.dump(json_data, f, indent=2, default=str)
        
        print(f"Data exported to: data/{filename}")
        return data

if __name__ == "__main__":
    # Test the data collector
    collector = InternalDataCollector()
    data = collector.export_all_data()
    print("Data collection completed!")
