# -*- coding: utf-8 -*-
"""
Simple Business Advisor - AI Model for Business Growth Questions
Simplified version without complex imports
"""

import pandas as pd
import numpy as np
import os

class SimpleBusinessAdvisor:
    def __init__(self):
        self.data = None
        self.load_data()
        
    def load_data(self):
        """Load the business data"""
        try:
            if os.path.exists('2016 MSME Survey ver. 1.0.dta'):
                self.data = pd.read_stata('2016 MSME Survey ver. 1.0.dta')
                print("✅ Business data loaded successfully!")
                print(f"📊 Analyzing {len(self.data):,} businesses")
            else:
                print("❌ Data file not found. Please ensure '2016 MSME Survey ver. 1.0.dta' exists.")
        except Exception as e:
            print(f"Error loading data: {e}")
    
    def ask_question(self, question):
        """Main interface for asking business questions"""
        if self.data is None:
            return "❌ No data available for analysis. Please check that the data file exists."
        
        question_lower = question.lower()
        
        # Route questions to appropriate handlers
        if any(word in question_lower for word in ['revenue', 'sales', 'income', 'profit', 'money', 'earnings']):
            return self.revenue_insights()
        elif any(word in question_lower for word in ['employee', 'staff', 'workforce', 'hiring', 'workers']):
            return self.employee_insights()
        elif any(word in question_lower for word in ['sector', 'industry', 'business type', 'market']):
            return self.sector_insights()
        elif any(word in question_lower for word in ['growth', 'expand', 'scale', 'improve', 'increase']):
            return self.growth_insights()
        elif any(word in question_lower for word in ['training', 'skills', 'education', 'learning']):
            return self.training_insights()
        elif any(word in question_lower for word in ['technology', 'digital', 'computer', 'tech']):
            return self.technology_insights()
        else:
            return self.general_help()
    
    def revenue_insights(self):
        """Provide revenue and profitability insights"""
        response = "💰 **REVENUE INSIGHTS**\n\n"
        
        try:
            # Revenue analysis
            if 'eh01_1' in self.data.columns:
                revenue_data = self.data['eh01_1'].dropna()
                response += f"📊 **Revenue Statistics:**\n"
                response += f"• Average monthly revenue: KSh {revenue_data.mean():,.0f}\n"
                response += f"• Median monthly revenue: KSh {revenue_data.median():,.0f}\n"
                response += f"• Top 25% earn above: KSh {revenue_data.quantile(0.75):,.0f}\n"
                response += f"• Top 10% earn above: KSh {revenue_data.quantile(0.90):,.0f}\n\n"
            
            # Net income analysis
            if 'eh04_1' in self.data.columns:
                income_data = self.data['eh04_1'].dropna()
                response += f"💵 **Net Income Statistics:**\n"
                response += f"• Average monthly net income: KSh {income_data.mean():,.0f}\n"
                response += f"• Median monthly net income: KSh {income_data.median():,.0f}\n\n"
            
            response += "🚀 **How to Increase Revenue:**\n"
            response += "1. **Focus on High-Performing Sectors**: Retail, manufacturing, and services typically perform well\n"
            response += "2. **Improve Customer Access**: Choose locations with good customer accessibility\n"
            response += "3. **Invest in Training**: Businesses with trained employees earn more\n"
            response += "4. **Use Technology**: Computer users typically have higher revenues\n"
            response += "5. **Keep Good Records**: Track performance to identify improvement areas\n"
            response += "6. **Consider Business Size**: Larger businesses typically have higher revenues\n"
            
        except Exception as e:
            response += f"❌ Error analyzing revenue data: {e}\n"
        
        return response
    
    def employee_insights(self):
        """Provide workforce insights"""
        response = "👥 **WORKFORCE INSIGHTS**\n\n"
        
        try:
            # Basic employee statistics
            if 'ec01' in self.data.columns and 'ec02' in self.data.columns:
                male_owners = self.data['ec01'].sum()
                female_owners = self.data['ec02'].sum()
                response += f"👨‍💼 **Business Ownership:**\n"
                response += f"• Male working owners: {male_owners:,}\n"
                response += f"• Female working owners: {female_owners:,}\n\n"
            
            # Training impact
            if 'b_01' in self.data.columns:
                training_businesses = (self.data['b_01'] == 'Yes').sum()
                total_businesses = len(self.data)
                training_rate = (training_businesses / total_businesses) * 100
                response += f"🎓 **Training Statistics:**\n"
                response += f"• Businesses providing training: {training_rate:.1f}%\n\n"
            
            response += "💡 **Employee Strategy Recommendations:**\n"
            response += "1. **Invest in Training**: Train employees to improve productivity\n"
            response += "2. **Balanced Hiring**: Consider gender balance for diverse perspectives\n"
            response += "3. **Skill Development**: Focus on skills relevant to your sector\n"
            response += "4. **Employee Retention**: Create good working conditions\n"
            response += "5. **Growth Planning**: Plan workforce expansion with business growth\n"
            
        except Exception as e:
            response += f"❌ Error analyzing employee data: {e}\n"
        
        return response
    
    def sector_insights(self):
        """Provide sector and industry insights"""
        response = "🏭 **SECTOR INSIGHTS**\n\n"
        
        try:
            if 'eb01_2' in self.data.columns:
                # Most popular sectors
                sector_counts = self.data['eb01_2'].value_counts().head(5)
                response += f"📊 **Most Popular Sectors:**\n"
                for i, (sector, count) in enumerate(sector_counts.items(), 1):
                    percentage = (count / len(self.data)) * 100
                    response += f"{i}. {sector}: {count:,} businesses ({percentage:.1f}%)\n"
                response += "\n"
                
                # Sector performance (if revenue data available)
                if 'eh01_1' in self.data.columns:
                    sector_revenue = self.data.groupby('eb01_2')['eh01_1'].mean().sort_values(ascending=False)
                    response += f"💰 **Top Revenue Sectors:**\n"
                    for i, (sector, revenue) in enumerate(sector_revenue.head(3).items(), 1):
                        response += f"{i}. {sector}: KSh {revenue:,.0f} avg/month\n"
                    response += "\n"
            
            response += "🎯 **Sector Selection Tips:**\n"
            response += "1. **Market Research**: Study demand in your area\n"
            response += "2. **Competition Analysis**: Assess market saturation\n"
            response += "3. **Skills Match**: Choose sectors matching your expertise\n"
            response += "4. **Growth Potential**: Consider emerging sectors\n"
            response += "5. **Capital Requirements**: Match sector needs with available capital\n"
            
        except Exception as e:
            response += f"❌ Error analyzing sector data: {e}\n"
        
        return response
    
    def growth_insights(self):
        """Provide business growth insights"""
        response = "📈 **GROWTH STRATEGIES**\n\n"
        
        try:
            # Performance analysis
            if 'eh09' in self.data.columns:
                performance = self.data['eh09'].value_counts()
                response += f"📊 **Business Performance Distribution:**\n"
                for rating, count in performance.items():
                    percentage = (count / len(self.data)) * 100
                    response += f"• {rating}: {percentage:.1f}% of businesses\n"
                response += "\n"
            
            # Growth factors
            response += "🚀 **Proven Growth Strategies:**\n"
            response += "1. **Innovation**: Introduce new products, processes, or marketing methods\n"
            response += "2. **Employee Training**: Invest in skill development\n"
            response += "3. **Technology Adoption**: Use computers and digital tools\n"
            response += "4. **Good Record Keeping**: Track performance and finances\n"
            response += "5. **Customer Focus**: Ensure good location and service\n"
            response += "6. **Strategic Financing**: Access appropriate capital for growth\n"
            response += "7. **Market Expansion**: Explore new customer segments\n"
            
            response += "\n💡 **Action Steps:**\n"
            response += "• Assess current performance honestly\n"
            response += "• Identify your top 3 growth opportunities\n"
            response += "• Create a 6-month improvement plan\n"
            response += "• Monitor progress monthly\n"
            
        except Exception as e:
            response += f"❌ Error analyzing growth data: {e}\n"
        
        return response
    
    def training_insights(self):
        """Provide training and skills insights"""
        response = "🎓 **TRAINING & SKILLS INSIGHTS**\n\n"
        
        try:
            if 'b_01' in self.data.columns:
                has_training = (self.data['b_01'] == 'Yes').mean() * 100
                response += f"📊 **Training Statistics:**\n"
                response += f"• Businesses providing employee training: {has_training:.1f}%\n\n"
            
            response += "💡 **Training Benefits:**\n"
            response += "• Improved productivity and efficiency\n"
            response += "• Better customer service\n"
            response += "• Higher employee retention\n"
            response += "• Increased revenue potential\n"
            response += "• Better problem-solving capabilities\n\n"
            
            response += "🎯 **Training Recommendations:**\n"
            response += "1. **Start with Basics**: Focus on core business skills first\n"
            response += "2. **Sector-Specific Training**: Get industry-relevant skills\n"
            response += "3. **Government Programs**: Look for subsidized training options\n"
            response += "4. **On-the-Job Training**: Implement practical learning systems\n"
            response += "5. **Continuous Learning**: Make training an ongoing process\n"
            
        except Exception as e:
            response += f"❌ Error analyzing training data: {e}\n"
        
        return response
    
    def technology_insights(self):
        """Provide technology adoption insights"""
        response = "💻 **TECHNOLOGY INSIGHTS**\n\n"
        
        try:
            # Technology adoption rates
            if 'ek16' in self.data.columns:
                computer_use = (self.data['ek16'] == 'Yes').mean() * 100
                response += f"🖥️ **Technology Adoption:**\n"
                response += f"• Businesses using computers: {computer_use:.1f}%\n"
            
            if 'ej10' in self.data.columns:
                mobile_money = (self.data['ej10'] == 'Yes').mean() * 100
                response += f"• Businesses using mobile money: {mobile_money:.1f}%\n\n"
            
            # Technology impact on revenue
            if 'ek16' in self.data.columns and 'eh01_1' in self.data.columns:
                tech_users = self.data[self.data['ek16'] == 'Yes']['eh01_1'].mean()
                non_tech_users = self.data[self.data['ek16'] == 'No']['eh01_1'].mean()
                if not pd.isna(tech_users) and not pd.isna(non_tech_users):
                    response += f"📊 **Technology ROI:**\n"
                    response += f"• Tech users avg revenue: KSh {tech_users:,.0f}/month\n"
                    response += f"• Non-tech users avg revenue: KSh {non_tech_users:,.0f}/month\n\n"
            
            response += "🎯 **Technology Roadmap:**\n"
            response += "1. **Mobile Payments**: Start with mobile money for transactions\n"
            response += "2. **Basic Computing**: Get a computer for record keeping\n"
            response += "3. **Internet Access**: Connect to reach more customers\n"
            response += "4. **Digital Records**: Use technology for business tracking\n"
            response += "5. **Online Presence**: Create social media profiles\n"
            
        except Exception as e:
            response += f"❌ Error analyzing technology data: {e}\n"
        
        return response
    
    def general_help(self):
        """Provide general help and available topics"""
        response = "🤖 **AI BUSINESS ADVISOR HELP**\n\n"
        response += "I can help you with business questions about:\n\n"
        response += "💰 **Revenue & Profit**: 'How can I increase revenue?'\n"
        response += "👥 **Employees**: 'Should I hire more staff?'\n"
        response += "🏭 **Sectors**: 'What business sectors perform best?'\n"
        response += "📈 **Growth**: 'How can I grow my business?'\n"
        response += "🎓 **Training**: 'Is employee training worth it?'\n"
        response += "💻 **Technology**: 'Should I invest in technology?'\n\n"
        response += "💡 **Try asking:**\n"
        response += "• 'What's the average revenue in my sector?'\n"
        response += "• 'How does training impact business performance?'\n"
        response += "• 'Which sectors have the highest profits?'\n"
        response += "• 'What technology should I invest in?'\n"
        
        return response

def main():
    """Simple interactive interface"""
    print("🤖 **AI Business Advisor**")
    print("=" * 40)
    
    advisor = SimpleBusinessAdvisor()
    
    if advisor.data is None:
        print("Cannot start advisor - no data available.")
        return
    
    print("\nAsk me any business question!")
    print("Type 'quit' to exit.\n")
    
    while True:
        try:
            question = input("💬 Your question: ").strip()
            
            if question.lower() in ['quit', 'exit', 'bye', 'q']:
                print("👋 Thank you for using the AI Business Advisor!")
                break
            
            if not question:
                continue
                
            print("\n" + "="*50)
            answer = advisor.ask_question(question)
            print(answer)
            print("="*50 + "\n")
            
        except KeyboardInterrupt:
            print("\n👋 Goodbye!")
            break
        except Exception as e:
            print(f"❌ Error: {e}")

if __name__ == "__main__":
    main()
