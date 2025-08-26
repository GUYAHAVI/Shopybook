#!/usr/bin/env python3
"""
Quick Business Insights - Function-based AI advisor
Simple functions you can call directly for business insights
"""

import pandas as pd
import numpy as np
import os

def load_business_data():
    """Load and return the business data"""
    try:
        if os.path.exists('2016 MSME Survey ver. 1.0.dta'):
            data = pd.read_stata('2016 MSME Survey ver. 1.0.dta')
            print(f"✅ Data loaded: {len(data):,} businesses")
            return data
        else:
            print("❌ Data file not found")
            return None
    except Exception as e:
        print(f"❌ Error loading data: {e}")
        return None

def revenue_analysis():
    """Analyze revenue patterns and provide insights"""
    data = load_business_data()
    if data is None:
        return "No data available"
    
    print("\n💰 **REVENUE ANALYSIS**")
    print("=" * 40)
    
    # Revenue statistics
    if 'eh01_1' in data.columns:
        revenue = data['eh01_1'].dropna()
        print(f"📊 Revenue Statistics:")
        print(f"• Average monthly revenue: KSh {revenue.mean():,.0f}")
        print(f"• Median monthly revenue: KSh {revenue.median():,.0f}")
        print(f"• Top 25% earn above: KSh {revenue.quantile(0.75):,.0f}")
        print(f"• Top 10% earn above: KSh {revenue.quantile(0.90):,.0f}")
    
    # Net income statistics
    if 'eh04_1' in data.columns:
        income = data['eh04_1'].dropna()
        print(f"\n💵 Net Income Statistics:")
        print(f"• Average monthly net income: KSh {income.mean():,.0f}")
        print(f"• Median monthly net income: KSh {income.median():,.0f}")
    
    print(f"\n🚀 Key Revenue Growth Strategies:")
    print("1. Focus on high-performing sectors")
    print("2. Invest in employee training")
    print("3. Use technology for efficiency")
    print("4. Choose accessible locations")
    print("5. Keep good business records")

def sector_performance():
    """Analyze which business sectors perform best"""
    data = load_business_data()
    if data is None:
        return "No data available"
    
    print("\n🏭 **SECTOR PERFORMANCE ANALYSIS**")
    print("=" * 40)
    
    if 'eb01_2' in data.columns:
        # Most popular sectors
        sectors = data['eb01_2'].value_counts().head(5)
        print(f"📊 Most Popular Sectors:")
        for i, (sector, count) in enumerate(sectors.items(), 1):
            percentage = (count / len(data)) * 100
            print(f"{i}. {sector}: {count:,} businesses ({percentage:.1f}%)")
        
        # Sector revenue performance
        if 'eh01_1' in data.columns:
            sector_revenue = data.groupby('eb01_2')['eh01_1'].mean().sort_values(ascending=False)
            print(f"\n💰 Top Revenue-Generating Sectors:")
            for i, (sector, revenue) in enumerate(sector_revenue.head(3).items(), 1):
                print(f"{i}. {sector}: KSh {revenue:,.0f} avg/month")
    
    print(f"\n🎯 Sector Selection Tips:")
    print("1. Research market demand in your area")
    print("2. Assess competition levels")
    print("3. Match your skills to sector requirements")
    print("4. Consider capital requirements")

def technology_impact():
    """Analyze the impact of technology on business performance"""
    data = load_business_data()
    if data is None:
        return "No data available"
    
    print("\n💻 **TECHNOLOGY IMPACT ANALYSIS**")
    print("=" * 40)
    
    # Technology adoption rates
    if 'ek16' in data.columns:
        computer_use = (data['ek16'] == 'Yes').mean() * 100
        print(f"🖥️ Technology Adoption:")
        print(f"• Businesses using computers: {computer_use:.1f}%")
    
    if 'ej10' in data.columns:
        mobile_money = (data['ej10'] == 'Yes').mean() * 100
        print(f"• Businesses using mobile money: {mobile_money:.1f}%")
    
    # Technology ROI
    if 'ek16' in data.columns and 'eh01_1' in data.columns:
        tech_users = data[data['ek16'] == 'Yes']['eh01_1'].mean()
        non_tech = data[data['ek16'] == 'No']['eh01_1'].mean()
        if not pd.isna(tech_users) and not pd.isna(non_tech):
            advantage = ((tech_users - non_tech) / non_tech) * 100
            print(f"\n📊 Technology ROI:")
            print(f"• Tech users earn {advantage:.1f}% more on average")
            print(f"• Tech users: KSh {tech_users:,.0f}/month")
            print(f"• Non-tech users: KSh {non_tech:,.0f}/month")
    
    print(f"\n🎯 Technology Roadmap:")
    print("1. Start with mobile money payments")
    print("2. Invest in basic computer for records")
    print("3. Get internet access")
    print("4. Use digital tools for marketing")

def training_benefits():
    """Analyze the benefits of employee training"""
    data = load_business_data()
    if data is None:
        return "No data available"
    
    print("\n🎓 **EMPLOYEE TRAINING ANALYSIS**")
    print("=" * 40)
    
    if 'b_01' in data.columns:
        training_rate = (data['b_01'] == 'Yes').mean() * 100
        print(f"📊 Training Statistics:")
        print(f"• Businesses providing training: {training_rate:.1f}%")
        
        # Training impact on revenue
        if 'eh01_1' in data.columns:
            trained = data[data['b_01'] == 'Yes']['eh01_1'].mean()
            not_trained = data[data['b_01'] == 'No']['eh01_1'].mean()
            if not pd.isna(trained) and not pd.isna(not_trained):
                advantage = ((trained - not_trained) / not_trained) * 100
                print(f"\n💰 Training ROI:")
                print(f"• Businesses with training earn {advantage:.1f}% more")
                print(f"• With training: KSh {trained:,.0f}/month")
                print(f"• Without training: KSh {not_trained:,.0f}/month")
    
    print(f"\n🎯 Training Recommendations:")
    print("1. Start with job-relevant skills")
    print("2. Include both technical and soft skills")
    print("3. Look for government training programs")
    print("4. Implement on-the-job learning")

def business_overview():
    """Get a complete overview of business insights"""
    data = load_business_data()
    if data is None:
        return "No data available"
    
    print("\n🏢 **BUSINESS INTELLIGENCE OVERVIEW**")
    print("=" * 50)
    print(f"📊 Dataset: {len(data):,} businesses analyzed")
    
    # Quick stats
    if 'eh01_1' in data.columns:
        avg_revenue = data['eh01_1'].mean()
        print(f"💰 Average monthly revenue: KSh {avg_revenue:,.0f}")
    
    if 'eb01_2' in data.columns:
        top_sector = data['eb01_2'].value_counts().index[0]
        print(f"🏭 Most common sector: {top_sector}")
    
    if 'ek16' in data.columns:
        tech_rate = (data['ek16'] == 'Yes').mean() * 100
        print(f"💻 Technology adoption: {tech_rate:.1f}%")
    
    if 'b_01' in data.columns:
        training_rate = (data['b_01'] == 'Yes').mean() * 100
        print(f"🎓 Training programs: {training_rate:.1f}%")
    
    print(f"\n🎯 **Available Analysis Functions:**")
    print("• revenue_analysis() - Revenue patterns and growth strategies")
    print("• sector_performance() - Best performing business sectors")
    print("• technology_impact() - Technology ROI and adoption")
    print("• training_benefits() - Employee training impact")
    print("• business_overview() - Complete business intelligence summary")

if __name__ == "__main__":
    print("🤖 **AI Business Advisor - Function Library**")
    print("=" * 50)
    print("Available functions:")
    print("• business_overview()")
    print("• revenue_analysis()")
    print("• sector_performance()")
    print("• technology_impact()")
    print("• training_benefits()")
    print("\nExample usage:")
    print(">>> revenue_analysis()")
    
    # Run overview by default
    business_overview()
