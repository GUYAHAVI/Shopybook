#!/usr/bin/env python3
"""
AI Model Training Script
Trains the AI system with internal and external data
"""

import json
import os
from datetime import datetime
from main_ai_orchestrator import AIBusinessOrchestrator
from data_collectors.external_learning import ExternalLearningSystem

def train_with_internal_data(business_id=None):
    """Train the model with your business data"""
    print("🎯 Training with Internal Business Data...")
    
    orchestrator = AIBusinessOrchestrator()
    
    # Generate comprehensive analysis (this trains the model)
    report = orchestrator.generate_comprehensive_analysis(business_id)
    
    # Save the training results
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    filename = f"data/internal_training_report_{timestamp}.json"
    
    os.makedirs("data", exist_ok=True)
    with open(filename, 'w') as f:
        json.dump(report, f, indent=2)
    
    print(f"✅ Internal training completed! Report saved to: {filename}")
    print(f"📊 Business Health Score: {report['executive_summary']['business_health_score']}")
    print(f"💡 Generated {len(report['recommendations'])} recommendations")
    
    return report

def train_with_external_data(competitor_urls=None, industry_keywords=None):
    """Train the model with external market data"""
    print("🌐 Training with External Market Data...")
    
    if not competitor_urls:
        competitor_urls = [
            "https://example-competitor1.com",
            "https://example-competitor2.com"
        ]
    
    if not industry_keywords:
        industry_keywords = [
            "business consulting",
            "digital marketing",
            "web development"
        ]
    
    external_learner = ExternalLearningSystem()
    
    # Learn from competitors
    print("🔍 Learning from competitors...")
    competitor_data = external_learner.learn_from_competitors(
        competitor_urls, 
        business_category="service"
    )
    
    # Learn from market data
    print("📈 Learning from market data...")
    market_data = external_learner.learn_from_market_data(industry_keywords)
    
    # Learn from social media
    print("📱 Learning from social media trends...")
    social_data = external_learner.learn_from_social_media(
        business_category="service",
        keywords=industry_keywords
    )
    
    # Save external learning data
    external_data = {
        'competitor_insights': competitor_data,
        'market_trends': market_data,
        'social_insights': social_data,
        'training_timestamp': datetime.now().isoformat()
    }
    
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    filename = f"data/external_training_data_{timestamp}.json"
    
    os.makedirs("data", exist_ok=True)
    with open(filename, 'w') as f:
        json.dump(external_data, f, indent=2)
    
    print(f"✅ External training completed! Data saved to: {filename}")
    print(f"🔍 Learned from {len(competitor_data)} competitors")
    print(f"📊 Collected market trends for {len(industry_keywords)} keywords")
    
    return external_data

def train_with_custom_data(custom_data_file):
    """Train the model with your custom data"""
    print("📝 Training with Custom Data...")
    
    try:
        with open(custom_data_file, 'r') as f:
            custom_data = json.load(f)
        
        # Process custom data
        orchestrator = AIBusinessOrchestrator()
        
        # You can extend this to process custom data
        print(f"✅ Custom data loaded: {len(custom_data)} datasets")
        
        return custom_data
        
    except Exception as e:
        print(f"❌ Error loading custom data: {e}")
        return None

def run_comprehensive_training():
    """Run all training methods"""
    print("🚀 Starting Comprehensive AI Training...")
    print("=" * 50)
    
    # 1. Internal training
    internal_report = train_with_internal_data()
    
    # 2. External training
    external_data = train_with_external_data()
    
    # 3. Generate enhanced analysis
    print("🤖 Generating Enhanced Analysis...")
    orchestrator = AIBusinessOrchestrator()
    
    # Combine internal and external data for enhanced analysis
    enhanced_report = orchestrator.generate_comprehensive_analysis()
    
    # Save comprehensive training results
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    filename = f"data/comprehensive_training_{timestamp}.json"
    
    comprehensive_results = {
        'internal_training': internal_report,
        'external_training': external_data,
        'enhanced_analysis': enhanced_report,
        'training_summary': {
            'total_recommendations': len(enhanced_report['recommendations']),
            'business_health_score': enhanced_report['executive_summary']['business_health_score'],
            'training_timestamp': datetime.now().isoformat()
        }
    }
    
    os.makedirs("data", exist_ok=True)
    with open(filename, 'w') as f:
        json.dump(comprehensive_results, f, indent=2)
    
    print("=" * 50)
    print("🎉 Comprehensive Training Completed!")
    print(f"📁 Results saved to: {filename}")
    print(f"📊 Business Health Score: {enhanced_report['executive_summary']['business_health_score']}")
    print(f"💡 Total Recommendations: {len(enhanced_report['recommendations'])}")
    
    return comprehensive_results

def show_training_options():
    """Show available training options"""
    print("🎯 AI Model Training Options:")
    print("=" * 40)
    print("1. Internal Training - Learn from your business data")
    print("2. External Training - Learn from competitors & market")
    print("3. Custom Training - Use your own data")
    print("4. Comprehensive Training - All methods combined")
    print("5. Exit")
    print("=" * 40)

def main():
    """Main training interface"""
    print("🤖 AI Business Intelligence - Model Training")
    print("=" * 50)
    
    while True:
        show_training_options()
        choice = input("\nSelect training option (1-5): ").strip()
        
        if choice == "1":
            train_with_internal_data()
            
        elif choice == "2":
            # Get competitor URLs
            urls_input = input("Enter competitor URLs (comma-separated, or press Enter for defaults): ").strip()
            competitor_urls = [url.strip() for url in urls_input.split(",")] if urls_input else None
            
            # Get industry keywords
            keywords_input = input("Enter industry keywords (comma-separated, or press Enter for defaults): ").strip()
            industry_keywords = [kw.strip() for kw in keywords_input.split(",")] if keywords_input else None
            
            train_with_external_data(competitor_urls, industry_keywords)
            
        elif choice == "3":
            custom_file = input("Enter path to your custom data file: ").strip()
            if os.path.exists(custom_file):
                train_with_custom_data(custom_file)
            else:
                print("❌ File not found!")
                
        elif choice == "4":
            run_comprehensive_training()
            
        elif choice == "5":
            print("👋 Training session ended. Goodbye!")
            break
            
        else:
            print("❌ Invalid option. Please try again.")
        
        input("\nPress Enter to continue...")

if __name__ == "__main__":
    main()
