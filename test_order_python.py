#!/usr/bin/env python3
"""
Test script for Shopybook order placement
Tests the Chempiski business order functionality
"""

import requests
import json
from datetime import datetime

# Configuration
BASE_URL = "http://localhost:8000"
BUSINESS_SLUG = "chempiski"
BUSINESS_ID = "b97bba8d-338b-4d96-a3c9-ec743b6d67e3"
PRODUCT_ID = "1"

def get_csrf_token():
    """Get CSRF token from the business page"""
    try:
        response = requests.get(f"{BASE_URL}/business/{BUSINESS_SLUG}")
        if response.status_code == 200:
            # Extract CSRF token from meta tag
            content = response.text
            if 'csrf-token' in content:
                start = content.find('name="csrf-token" content="') + 27
                end = content.find('"', start)
                token = content[start:end]
                print(f"✓ CSRF Token obtained: {token[:20]}...")
                return token, response.cookies
        print(f"✗ Failed to get CSRF token: Status {response.status_code}")
        return None, None
    except Exception as e:
        print(f"✗ Error getting CSRF token: {e}")
        return None, None

def test_order_placement(csrf_token, cookies):
    """Test placing an order"""
    print("\n" + "="*60)
    print("TESTING ORDER PLACEMENT")
    print("="*60)
    
    order_data = {
        '_token': csrf_token,
        'business_id': BUSINESS_ID,
        'product_id': PRODUCT_ID,
        'quantity': '1',
        'customer_name': 'Test Customer Python',
        'customer_phone': '0712345678',
        'customer_email': 'test@example.com',
        'delivery_address': 'Test Address 123, Nairobi'
    }
    
    print(f"\nOrder Data:")
    print(json.dumps(order_data, indent=2))
    
    try:
        headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrf_token,
            'X-Requested-With': 'XMLHttpRequest'
        }
        
        print(f"\nSending POST to: {BASE_URL}/orders")
        response = requests.post(
            f"{BASE_URL}/orders",
            data=order_data,
            headers=headers,
            cookies=cookies
        )
        
        print(f"\nResponse Status: {response.status_code}")
        print(f"Response Headers: {dict(response.headers)}")
        
        try:
            response_data = response.json()
            print(f"\nResponse JSON:")
            print(json.dumps(response_data, indent=2))
            
            if response_data.get('success'):
                print(f"\n✓ ORDER PLACED SUCCESSFULLY!")
                print(f"  Order ID: {response_data.get('order_id')}")
                return True
            else:
                print(f"\n✗ ORDER FAILED!")
                print(f"  Message: {response_data.get('message')}")
                return False
                
        except json.JSONDecodeError:
            print(f"\n✗ Response is not JSON:")
            print(response.text[:500])
            return False
            
    except Exception as e:
        print(f"\n✗ Error placing order: {e}")
        return False

def test_business_page():
    """Test if business page loads"""
    print("\n" + "="*60)
    print("TESTING BUSINESS PAGE ACCESS")
    print("="*60)
    
    try:
        response = requests.get(f"{BASE_URL}/business/{BUSINESS_SLUG}")
        print(f"\nStatus: {response.status_code}")
        
        if response.status_code == 200:
            print(f"✓ Business page loaded successfully")
            print(f"  Page size: {len(response.text)} bytes")
            
            # Check for products
            if 'Order Now' in response.text:
                print(f"✓ 'Order Now' button found on page")
            else:
                print(f"✗ 'Order Now' button not found")
                
            return True
        else:
            print(f"✗ Business page failed to load")
            return False
            
    except Exception as e:
        print(f"✗ Error accessing business page: {e}")
        return False

def main():
    print("="*60)
    print("SHOPYBOOK ORDER TESTING SCRIPT")
    print(f"Business: {BUSINESS_SLUG}")
    print(f"Timestamp: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("="*60)
    
    # Test 1: Business Page
    if not test_business_page():
        print("\n✗ FAILED: Cannot access business page")
        return
    
    # Test 2: Get CSRF Token
    csrf_token, cookies = get_csrf_token()
    if not csrf_token:
        print("\n✗ FAILED: Cannot get CSRF token")
        return
    
    # Test 3: Place Order
    success = test_order_placement(csrf_token, cookies)
    
    # Summary
    print("\n" + "="*60)
    print("TEST SUMMARY")
    print("="*60)
    if success:
        print("✓ ALL TESTS PASSED - Order placement working!")
    else:
        print("✗ TESTS FAILED - Order placement not working")
        print("\nNext steps:")
        print("1. Check storage/logs/laravel.log for detailed errors")
        print("2. Verify database connection")
        print("3. Check OrderController for exceptions")
    print("="*60)

if __name__ == "__main__":
    main()
