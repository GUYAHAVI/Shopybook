#!/usr/bin/env python3
"""
Demo script showing the AI Business Advisor in action
"""

from simple_advisor import SimpleBusinessAdvisor

def demo():
    print("🚀 **AI BUSINESS ADVISOR DEMO**")
    print("=" * 50)
    
    # Initialize the advisor
    advisor = SimpleBusinessAdvisor()
    
    if advisor.data is None:
        print("❌ Demo cannot run - data file not found")
        return
    
    # Demo questions
    questions = [
        "How can I increase my business revenue?",
        "What sectors perform best?", 
        "Should I invest in employee training?",
        "How does technology impact business performance?",
        "What are proven growth strategies?"
    ]
    
    print(f"\n🤖 Analyzing {len(advisor.data):,} real businesses...")
    print("\n" + "=" * 60)
    
    for i, question in enumerate(questions, 1):
        print(f"\n**QUESTION {i}: {question}**")
        print("-" * 40)
        answer = advisor.ask_question(question)
        print(answer)
        print("\n" + "=" * 60)
    
    print("\n✅ **Demo Complete!**")
    print("\nTo ask your own questions, run:")
    print("python simple_advisor.py")

if __name__ == "__main__":
    demo()
