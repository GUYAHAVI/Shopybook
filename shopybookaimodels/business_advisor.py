# -*- coding: utf-8 -*-
"""
Interactive Business Advisor - AI Model for Business Growth Questions

This module provides an interactive interface to ask business-related questions
and get AI-powered insights based on the trained MSME model.
"""

import pandas as pd
import numpy as np
import pickle
import os
import matplotlib.pyplot as plt

class BusinessAdvisor:
    def __init__(self):
        self.model = None
        self.data = None
        self.feature_importance = None
        self.data_stats = None
        self.load_trained_model()
        
    def load_trained_model(self):
        """Load the trained model and data if available"""
        try:
            # Check if we have saved model components
            if os.path.exists('trained_model.pkl'):
                with open('trained_model.pkl', 'rb') as f:
                    self.model = pickle.load(f)
                print("✅ Trained model loaded successfully!")
            else:
                print("❌ No trained model found. Please run the training script first.")
                
            # Load the original data for analysis
            if os.path.exists('2016 MSME Survey ver. 1.0.dta'):
                self.data = pd.read_stata('2016 MSME Survey ver. 1.0.dta')
                self.calculate_data_stats()
                print("✅ Business data loaded successfully!")
            else:
                print("❌ No data file found.")
                
        except Exception as e:
            print(f"Error loading model: {e}")
    
    def calculate_data_stats(self):
        """Calculate key statistics from the data"""
        if self.data is not None:
            self.data_stats = {
                'total_businesses': len(self.data),
                'avg_revenue': self.data['eh01_1'].mean() if 'eh01_1' in self.data.columns else 0,
                'avg_net_income': self.data['eh04_1'].mean() if 'eh04_1' in self.data.columns else 0,
                'top_sectors': self.data['eb01_2'].value_counts().head(5) if 'eb01_2' in self.data.columns else None,
                'business_locations': self.data['eb03'].value_counts().head(5) if 'eb03' in self.data.columns else None,
            }
    
    def ask_question(self, question):
        """Main interface for asking business questions"""
        question_lower = question.lower()
        
        # Route questions to appropriate handlers
        if any(word in question_lower for word in ['revenue', 'sales', 'income', 'profit']):
            return self.answer_revenue_questions(question)
        elif any(word in question_lower for word in ['employee', 'staff', 'workforce', 'hiring']):
            return self.answer_employee_questions(question)
        elif any(word in question_lower for word in ['sector', 'industry', 'business type']):
            return self.answer_sector_questions(question)
        elif any(word in question_lower for word in ['location', 'where', 'place']):
            return self.answer_location_questions(question)
        elif any(word in question_lower for word in ['growth', 'expand', 'scale', 'improve']):
            return self.answer_growth_questions(question)
        elif any(word in question_lower for word in ['training', 'skills', 'education']):
            return self.answer_training_questions(question)
        elif any(word in question_lower for word in ['financing', 'credit', 'loan', 'capital']):
            return self.answer_finance_questions(question)
        elif any(word in question_lower for word in ['technology', 'digital', 'ict', 'computer']):
            return self.answer_technology_questions(question)
        else:
            return self.answer_general_questions(question)
    
    def answer_revenue_questions(self, question):
        """Answer questions about revenue and profitability"""
        if self.data is None:
            return "❌ No data available for analysis."
        
        response = "💰 **REVENUE & PROFITABILITY INSIGHTS**\n\n"
        
        # Calculate revenue statistics
        revenue_col = 'eh01_1'  # Total sales revenue
        net_income_col = 'eh04_1'  # Net income
        
        if revenue_col in self.data.columns:
            revenue_data = self.data[revenue_col].dropna()
            response += f"📊 **Revenue Analysis:**\n"
            response += f"• Average monthly revenue: KSh {revenue_data.mean():,.0f}\n"
            response += f"• Median monthly revenue: KSh {revenue_data.median():,.0f}\n"
            response += f"• Top 25% earn above: KSh {revenue_data.quantile(0.75):,.0f}\n"
            response += f"• Top 10% earn above: KSh {revenue_data.quantile(0.90):,.0f}\n\n"
        
        if net_income_col in self.data.columns:
            income_data = self.data[net_income_col].dropna()
            response += f"💵 **Net Income Analysis:**\n"
            response += f"• Average monthly net income: KSh {income_data.mean():,.0f}\n"
            response += f"• Median monthly net income: KSh {income_data.median():,.0f}\n"
            response += f"• Profit margin (avg): {(income_data.mean()/revenue_data.mean())*100:.1f}%\n\n"
        
        # High-performing business characteristics
        response += "🚀 **What High-Revenue Businesses Do:**\n"
        high_revenue = self.data[self.data[revenue_col] > revenue_data.quantile(0.75)]
        
        # Analyze sector performance
        if 'eb01_2' in self.data.columns:
            top_sectors = high_revenue['eb01_2'].value_counts().head(3)
            response += f"• Top performing sectors: {', '.join(top_sectors.index[:3])}\n"
        
        # Employee count correlation
        if 'total_employees' in self.data.columns:
            avg_employees = high_revenue['total_employees'].mean()
            response += f"• High-revenue businesses avg employees: {avg_employees:.1f}\n"
        
        response += "\n📈 **Recommendations for Revenue Growth:**\n"
        response += "1. Focus on high-performing sectors identified above\n"
        response += "2. Consider strategic hiring if below average employee count\n"
        response += "3. Analyze your profit margins against industry average\n"
        response += "4. Look into businesses earning in top 25% for best practices\n"
        
        return response
    
    def answer_employee_questions(self, question):
        """Answer questions about workforce and employment"""
        if self.data is None:
            return "❌ No data available for analysis."
        
        response = "👥 **WORKFORCE & EMPLOYMENT INSIGHTS**\n\n"
        
        # Calculate employee statistics
        male_owners = self.data['ec01'].sum() if 'ec01' in self.data.columns else 0
        female_owners = self.data['ec02'].sum() if 'ec02' in self.data.columns else 0
        
        response += f"👨‍💼 **Business Ownership:**\n"
        response += f"• Total male working owners: {male_owners:,}\n"
        response += f"• Total female working owners: {female_owners:,}\n"
        response += f"• Gender ratio: {female_owners/male_owners:.2f} (F:M)\n\n"
        
        # Full-time employees analysis
        if 'total_employees' in self.data.columns:
            emp_data = self.data['total_employees'].dropna()
            response += f"📊 **Employment Statistics:**\n"
            response += f"• Average employees per business: {emp_data.mean():.1f}\n"
            response += f"• Median employees per business: {emp_data.median():.0f}\n"
            response += f"• Businesses with 5+ employees: {(emp_data >= 5).sum():,} ({(emp_data >= 5).mean()*100:.1f}%)\n\n"
        
        # Training and skills
        if 'b_01' in self.data.columns:
            trained_businesses = self.data['b_01'].value_counts()
            response += f"🎓 **Training & Skills:**\n"
            response += f"• Businesses providing employee training: {trained_businesses.get('Yes', 0):,}\n"
            
        response += "\n💡 **Workforce Growth Recommendations:**\n"
        response += "1. Consider employee training programs to improve productivity\n"
        response += "2. Analyze if your employee count aligns with revenue goals\n"
        response += "3. Look into gender balance for diverse perspectives\n"
        response += "4. Plan for skilled worker recruitment in growth phases\n"
        
        return response
    
    def answer_sector_questions(self, question):
        """Answer questions about business sectors and industries"""
        if self.data is None:
            return "❌ No data available for analysis."
        
        response = "🏭 **SECTOR & INDUSTRY INSIGHTS**\n\n"
        
        # Sector analysis
        if 'eb01_2' in self.data.columns:
            sector_counts = self.data['eb01_2'].value_counts().head(10)
            response += f"📊 **Most Popular Business Sectors:**\n"
            for i, (sector, count) in enumerate(sector_counts.items(), 1):
                percentage = (count / len(self.data)) * 100
                response += f"{i}. {sector}: {count:,} businesses ({percentage:.1f}%)\n"
            response += "\n"
        
        # Sector performance analysis
        if 'eh01_1' in self.data.columns and 'eb01_2' in self.data.columns:
            sector_revenue = self.data.groupby('eb01_2')['eh01_1'].agg(['mean', 'median', 'count']).sort_values('mean', ascending=False)
            response += f"💰 **Top Revenue-Generating Sectors:**\n"
            for i, (sector, stats) in enumerate(sector_revenue.head(5).iterrows(), 1):
                response += f"{i}. {sector}: Avg KSh {stats['mean']:,.0f}/month ({stats['count']} businesses)\n"
            response += "\n"
        
        response += "🎯 **Sector Selection Recommendations:**\n"
        response += "1. Consider high-revenue sectors if starting a new business\n"
        response += "2. Analyze competition levels in your chosen sector\n"
        response += "3. Look for underserved sectors with growth potential\n"
        response += "4. Consider sector-specific training and certifications\n"
        
        return response
    
    def answer_location_questions(self, question):
        """Answer questions about business location"""
        if self.data is None:
            return "❌ No data available for analysis."
        
        response = "📍 **LOCATION & SITE INSIGHTS**\n\n"
        
        # Location analysis
        if 'eb03' in self.data.columns:
            locations = self.data['eb03'].value_counts().head(10)
            response += f"🏘️ **Most Common Business Locations:**\n"
            for i, (location, count) in enumerate(locations.items(), 1):
                percentage = (count / len(self.data)) * 100
                response += f"{i}. {location}: {count:,} businesses ({percentage:.1f}%)\n"
            response += "\n"
        
        # Site appropriateness
        if 'eb04' in self.data.columns:
            site_ratings = self.data['eb04'].value_counts()
            response += f"⭐ **Site Appropriateness for Customers:**\n"
            for rating, count in site_ratings.items():
                percentage = (count / len(self.data)) * 100
                response += f"• {rating}: {count:,} businesses ({percentage:.1f}%)\n"
            response += "\n"
        
        response += "🎯 **Location Strategy Recommendations:**\n"
        response += "1. Choose locations with high customer accessibility\n"
        response += "2. Consider foot traffic and visibility\n"
        response += "3. Analyze competition density in the area\n"
        response += "4. Factor in rent costs vs. potential revenue\n"
        
        return response
    
    def answer_growth_questions(self, question):
        """Answer questions about business growth strategies"""
        response = "📈 **BUSINESS GROWTH STRATEGIES**\n\n"
        
        if self.data is None:
            return "❌ No data available for analysis."
        
        # Performance analysis
        if 'eh09' in self.data.columns:
            performance = self.data['eh09'].value_counts()
            response += f"📊 **Current Business Performance:**\n"
            for rating, count in performance.items():
                percentage = (count / len(self.data)) * 100
                response += f"• {rating}: {count:,} businesses ({percentage:.1f}%)\n"
            response += "\n"
        
        # Innovation analysis
        innovation_cols = ['eh19', 'eh20', 'eh21']  # Product, process, marketing innovations
        response += f"💡 **Innovation Adoption (2013-2015):**\n"
        for col in innovation_cols:
            if col in self.data.columns:
                innovation_rate = (self.data[col] == 'Yes').mean() * 100
                innovation_type = "Product" if col == 'eh19' else "Process" if col == 'eh20' else "Marketing"
                response += f"• {innovation_type} innovation: {innovation_rate:.1f}% of businesses\n"
        response += "\n"
        
        # Growth factors from high performers
        if 'eh01_1' in self.data.columns:
            revenue_data = self.data['eh01_1'].dropna()
            high_performers = self.data[self.data['eh01_1'] > revenue_data.quantile(0.75)]
            
            response += f"🚀 **What Top 25% Performers Do Differently:**\n"
            
            # Check training
            if 'b_01' in self.data.columns:
                training_rate_all = (self.data['b_01'] == 'Yes').mean() * 100
                training_rate_top = (high_performers['b_01'] == 'Yes').mean() * 100
                response += f"• Employee training: {training_rate_top:.1f}% vs {training_rate_all:.1f}% average\n"
            
            # Check record keeping
            if 'ej06' in self.data.columns:
                good_records_top = (high_performers['ej06'] != 'None').mean() * 100
                response += f"• Keep business records: {good_records_top:.1f}% of top performers\n"
            
            # Check technology use
            if 'ek16' in self.data.columns:
                tech_use_top = (high_performers['ek16'] == 'Yes').mean() * 100
                response += f"• Use computers: {tech_use_top:.1f}% of top performers\n"
        
        response += "\n🎯 **Growth Action Plan:**\n"
        response += "1. **Invest in Innovation**: Adopt product/process/marketing innovations\n"
        response += "2. **Train Your Team**: Provide employee training programs\n"
        response += "3. **Keep Good Records**: Implement proper business record keeping\n"
        response += "4. **Embrace Technology**: Use computers and digital tools\n"
        response += "5. **Monitor Performance**: Regularly assess business performance\n"
        response += "6. **Customer Focus**: Ensure good site location for customer access\n"
        
        return response
    
    def answer_training_questions(self, question):
        """Answer questions about training and skills development"""
        if self.data is None:
            return "❌ No data available for analysis."
        
        response = "🎓 **TRAINING & SKILLS DEVELOPMENT**\n\n"
        
        # Training prevalence
        if 'b_01' in self.data.columns:
            has_training = (self.data['b_01'] == 'Yes').mean() * 100
            response += f"📊 **Training Statistics:**\n"
            response += f"• Businesses providing employee training: {has_training:.1f}%\n\n"
        
        # Most important training types needed
        training_need_cols = ['ef06', 'ef07', 'ef08']  # Most important training needs
        response += f"🎯 **Most Needed Training Types:**\n"
        all_training_needs = []
        for col in training_need_cols:
            if col in self.data.columns:
                all_training_needs.extend(self.data[col].dropna().tolist())
        
        if all_training_needs:
            training_counts = pd.Series(all_training_needs).value_counts().head(5)
            for i, (training, count) in enumerate(training_counts.items(), 1):
                response += f"{i}. {training}: {count:,} mentions\n"
            response += "\n"
        
        response += "💡 **Training Investment ROI:**\n"
        response += "Businesses with employee training programs typically show:\n"
        response += "• Higher customer satisfaction\n"
        response += "• Improved operational efficiency\n"
        response += "• Better employee retention\n"
        response += "• Increased revenue potential\n\n"
        
        response += "🎯 **Training Recommendations:**\n"
        response += "1. Start with the most critical skill gaps in your sector\n"
        response += "2. Consider both technical and soft skills training\n"
        response += "3. Look for government or NGO training programs\n"
        response += "4. Implement on-the-job training systems\n"
        response += "5. Measure training impact on business performance\n"
        
        return response
    
    def answer_finance_questions(self, question):
        """Answer questions about financing and capital"""
        if self.data is None:
            return "❌ No data available for analysis."
        
        response = "💰 **FINANCING & CAPITAL INSIGHTS**\n\n"
        
        # Initial capital analysis
        if 'el01' in self.data.columns:
            capital_data = self.data['el01'].dropna()
            response += f"💵 **Initial Capital Statistics:**\n"
            response += f"• Average initial capital: KSh {capital_data.mean():,.0f}\n"
            response += f"• Median initial capital: KSh {capital_data.median():,.0f}\n"
            response += f"• 75th percentile: KSh {capital_data.quantile(0.75):,.0f}\n\n"
        
        # Capital sources
        if 'el02' in self.data.columns:
            capital_sources = self.data['el02'].value_counts().head(5)
            response += f"🏦 **Main Sources of Initial Capital:**\n"
            for i, (source, count) in enumerate(capital_sources.items(), 1):
                percentage = (count / len(self.data)) * 100
                response += f"{i}. {source}: {count:,} businesses ({percentage:.1f}%)\n"
            response += "\n"
        
        # Credit application
        if 'em01' in self.data.columns:
            applied_credit = (self.data['em01'] == 'Yes').mean() * 100
            response += f"📋 **Credit Application Patterns:**\n"
            response += f"• Businesses that applied for credit (last 3 years): {applied_credit:.1f}%\n"
            
            if 'em08b' in self.data.columns:
                credit_rejected = (self.data['em08b'] == 'Yes').mean() * 100
                response += f"• Credit applications rejected: {credit_rejected:.1f}%\n"
            response += "\n"
        
        response += "🎯 **Financing Strategy Recommendations:**\n"
        response += "1. **Start Small**: Begin with personal savings or family support\n"
        response += "2. **Build Credit History**: Establish relationship with financial institutions\n"
        response += "3. **Prepare Documentation**: Keep good business records for loan applications\n"
        response += "4. **Explore Options**: Consider microfinance, SACCOs, and government programs\n"
        response += "5. **Reinvest Profits**: Use business income to fund growth\n"
        
        return response
    
    def answer_technology_questions(self, question):
        """Answer questions about technology adoption"""
        if self.data is None:
            return "❌ No data available for analysis."
        
        response = "💻 **TECHNOLOGY & DIGITAL ADOPTION**\n\n"
        
        # Computer usage
        if 'ek16' in self.data.columns:
            computer_use = (self.data['ek16'] == 'Yes').mean() * 100
            response += f"🖥️ **Technology Adoption:**\n"
            response += f"• Businesses using computers: {computer_use:.1f}%\n"
        
        # Mobile money usage
        if 'ej10' in self.data.columns:
            mobile_money = (self.data['ej10'] == 'Yes').mean() * 100
            response += f"• Businesses using mobile money: {mobile_money:.1f}%\n"
        
        # Internet usage
        if 'ek20' in self.data.columns:
            website_use = (self.data['ek20'] == 'Yes').mean() * 100
            response += f"• Businesses with websites: {website_use:.1f}%\n\n"
        
        # Technology impact on revenue
        if 'ek16' in self.data.columns and 'eh01_1' in self.data.columns:
            tech_users = self.data[self.data['ek16'] == 'Yes']['eh01_1'].mean()
            non_tech_users = self.data[self.data['ek16'] == 'No']['eh01_1'].mean()
            if not pd.isna(tech_users) and not pd.isna(non_tech_users):
                tech_advantage = ((tech_users - non_tech_users) / non_tech_users) * 100
                response += f"📊 **Technology ROI:**\n"
                response += f"• Businesses with computers earn {tech_advantage:.1f}% more on average\n"
                response += f"• Tech users avg revenue: KSh {tech_users:,.0f}/month\n"
                response += f"• Non-tech users avg revenue: KSh {non_tech_users:,.0f}/month\n\n"
        
        response += "🎯 **Digital Transformation Roadmap:**\n"
        response += "1. **Start with Mobile**: Use mobile money for transactions\n"
        response += "2. **Basic Computing**: Invest in a computer for record keeping\n"
        response += "3. **Internet Presence**: Create social media profiles\n"
        response += "4. **Digital Payments**: Accept mobile payments from customers\n"
        response += "5. **Online Marketing**: Use digital platforms to reach customers\n"
        response += "6. **Data Analytics**: Use technology to analyze business performance\n"
        
        return response
    
    def answer_general_questions(self, question):
        """Answer general business questions"""
        response = "🏢 **GENERAL BUSINESS INSIGHTS**\n\n"
        response += "I can help you with questions about:\n\n"
        response += "💰 **Revenue & Profitability**: Income analysis, profit margins, high-performing sectors\n"
        response += "👥 **Workforce**: Employee statistics, training, hiring strategies\n"
        response += "🏭 **Sectors**: Industry analysis, sector performance, market opportunities\n"
        response += "📍 **Location**: Site selection, customer accessibility, location strategy\n"
        response += "📈 **Growth**: Expansion strategies, performance improvement, innovation\n"
        response += "🎓 **Training**: Skills development, employee training, capacity building\n"
        response += "💰 **Financing**: Capital sources, credit access, funding strategies\n"
        response += "💻 **Technology**: Digital adoption, ICT impact, tech ROI\n\n"
        response += "💡 **Try asking questions like:**\n"
        response += "• 'How can I increase my business revenue?'\n"
        response += "• 'What sectors perform best?'\n"
        response += "• 'Should I invest in employee training?'\n"
        response += "• 'How does technology impact business performance?'\n"
        response += "• 'What's the average revenue in my sector?'\n"
        
        return response

def main():
    """Interactive business advisor interface"""
    advisor = BusinessAdvisor()
    
    print("🤖 Welcome to the AI Business Advisor!")
    print("=" * 50)
    print("Ask me anything about business growth, revenue, employees, sectors, and more!")
    print("Type 'quit' to exit.\n")
    
    while True:
        question = input("💬 Your question: ").strip()
        
        if question.lower() in ['quit', 'exit', 'bye']:
            print("👋 Thank you for using the AI Business Advisor!")
            break
        
        if not question:
            continue
            
        print("\n" + "="*60)
        answer = advisor.ask_question(question)
        print(answer)
        print("="*60 + "\n")

if __name__ == "__main__":
    main()
