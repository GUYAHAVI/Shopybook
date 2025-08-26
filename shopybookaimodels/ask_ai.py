#!/usr/bin/env python3
"""
AI Business Advisor - Question Answering System
Simple command-line tool to ask business questions
"""

import pandas as pd
import os
import sys

class BusinessAI:
    def __init__(self):
        self.data = None
        self.load_data()
    
    def load_data(self):
        if os.path.exists('2016 MSME Survey ver. 1.0.dta'):
            self.data = pd.read_stata('2016 MSME Survey ver. 1.0.dta')
            return True
        return False
    
    def answer(self, question):
        if self.data is None:
            return "❌ No data available"
        
        q = question.lower()
        
        if 'revenue' in q or 'income' in q or 'profit' in q or 'money' in q:
            return self.revenue_insights()
        elif 'sector' in q or 'industry' in q or 'business type' in q:
            return self.sector_insights()
        elif 'technology' in q or 'computer' in q or 'digital' in q:
            return self.tech_insights()
        elif 'training' in q or 'skill' in q or 'education' in q:
            return self.training_insights()
        elif 'employee' in q or 'staff' in q or 'worker' in q:
            return self.employee_insights()
        else:
            return self.general_insights()
    
    def revenue_insights(self):
        revenue = self.data['eh01_1'].dropna()
        return f"""
💰 **REVENUE ANALYSIS** (from {len(revenue):,} businesses)
• Average monthly revenue: KSh {revenue.mean():,.0f}
• Top 25% earn above: KSh {revenue.quantile(0.75):,.0f}
• Top 10% earn above: KSh {revenue.quantile(0.90):,.0f}

🚀 **TO INCREASE REVENUE:**
1. Use technology (computers increase earnings)
2. Invest in employee training
3. Choose good business location
4. Keep proper business records
5. Target top-performing sectors
"""
    
    def sector_insights(self):
        sectors = self.data['eb01_2'].value_counts().head(3)
        sector_revenue = self.data.groupby('eb01_2')['eh01_1'].mean().sort_values(ascending=False).head(3)
        
        result = f"""
🏭 **SECTOR ANALYSIS**
📊 **Most Popular Sectors:**
"""
        for i, (sector, count) in enumerate(sectors.items(), 1):
            result += f"{i}. {sector}: {count:,} businesses\n"
        
        result += f"""
💰 **Top Revenue Sectors:**
"""
        for i, (sector, revenue) in enumerate(sector_revenue.items(), 1):
            result += f"{i}. {sector}: KSh {revenue:,.0f}/month avg\n"
        
        return result
    
    def tech_insights(self):
        if 'ek16' in self.data.columns and 'eh01_1' in self.data.columns:
            tech_users = self.data[self.data['ek16'] == 'Yes']['eh01_1'].mean()
            non_tech = self.data[self.data['ek16'] == 'No']['eh01_1'].mean()
            advantage = ((tech_users - non_tech) / non_tech) * 100
            
            return f"""
💻 **TECHNOLOGY IMPACT**
📊 **Computer Usage ROI:**
• Businesses with computers: KSh {tech_users:,.0f}/month
• Businesses without computers: KSh {non_tech:,.0f}/month
• Technology advantage: {advantage:.1f}% higher revenue

🎯 **RECOMMENDATION:** Invest in computers and digital tools!
"""
        return "Technology data not available"
    
    def training_insights(self):
        if 'b_01' in self.data.columns:
            training_rate = (self.data['b_01'] == 'Yes').mean() * 100
            return f"""
🎓 **TRAINING ANALYSIS**
• {training_rate:.1f}% of businesses provide employee training
• Training correlates with higher business performance
• Improves productivity and customer service

🎯 **RECOMMENDATION:** Invest in employee skill development!
"""
        return "Training data not available"
    
    def employee_insights(self):
        male_owners = self.data['ec01'].sum()
        female_owners = self.data['ec02'].sum()
        return f"""
👥 **WORKFORCE INSIGHTS**
• Male business owners: {male_owners:,}
• Female business owners: {female_owners:,}
• Gender ratio: {female_owners/male_owners:.2f} F:M

💡 **Strategy:** Balance workforce for diverse perspectives
"""
    
    def general_insights(self):
        return f"""
🤖 **AI BUSINESS ADVISOR**
Analyzing {len(self.data):,} real businesses

💡 **Ask me about:**
• "How to increase revenue?"
• "Which sectors perform best?"
• "Should I invest in technology?"
• "Is employee training worth it?"
• "What about workforce strategy?"
"""

def main():
    print("🤖 **AI BUSINESS ADVISOR**")
    print("=" * 40)
    
    ai = BusinessAI()
    if ai.data is None:
        print("❌ Data file not found")
        return
    
    print(f"✅ Loaded {len(ai.data):,} business records")
    
    # Get question from command line or ask
    if len(sys.argv) > 1:
        question = " ".join(sys.argv[1:])
    else:
        question = input("\n💬 Ask a business question: ")
    
    print(f"\n**QUESTION:** {question}")
    print("=" * 50)
    
    answer = ai.answer(question)
    print(answer)
    
    print("=" * 50)
    print("✅ Analysis complete!")

if __name__ == "__main__":
    main()
