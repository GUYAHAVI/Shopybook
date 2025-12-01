# 🛒 POS Dynamic Conversion System - Step by Step Guide

## 🎯 **How to Use Dynamic Conversions in POS**

### **Prerequisites:**
- You must be logged in as "Havi's Greenhouse Materials" (eligible business)
- Products must have conversion rules set up in the Product Conversions section

---

## **Step-by-Step Process:**

### **1. Access the POS**
- Go to: `/sales/pos`
- You'll see a special banner: **"Dynamic Conversion Enabled!"**

### **2. Identify Convertible Products**
- Look for products with a **🔄 conversion badge** (yellow badge with exchange icon)
- These products have conversion rules set up
- You'll see: **"Click for conversion options"** text

### **3. Click on a Convertible Product**
- **For eligible users**: Click → **Conversion Modal Opens**
- **For non-eligible users**: Click → **Direct Add to Cart**

### **4. Use the Conversion Modal**
The modal shows:
- **Product Information**: Name, price, stock
- **Available Conversion Options**: List of possible unit conversions
- **Quick Conversion Form**: 
  - Enter quantity
  - Select "From Unit" (e.g., kg)
  - Select "To Unit" (e.g., sqm)
  - Click "Calculate"

### **5. View Conversion Results**
After calculation, you'll see:
- **Conversion Details**: Formula, converted quantity, factor
- **Financial Analysis**: Purchase cost, sale value, profit, margin
- **Add to Cart Button**: Appears after successful calculation

### **6. Add Converted Product to Cart**
- Click **"Add to Cart"**
- Product appears in cart with conversion details
- Format: `Product Name (quantity from_unit → to_unit)`

### **7. Complete the Sale**
- Continue with normal POS checkout process
- Cart shows converted product with calculated price
- Receipt includes conversion information

---

## **Example Scenario:**

### **Customer Request:**
"I need 50 sqm of greenhouse film"

### **Your Process:**
1. **Click** on "Greenhouse Film" product (has 🔄 badge)
2. **Modal opens** showing conversion options
3. **Enter**: 50 in quantity field
4. **Select**: "sqm" as "To Unit"
5. **Select**: "kg" as "From Unit" (if that's how you bought it)
6. **Click**: "Calculate"
7. **See**: 
   - Formula: `50 sqm × 0.2 microns = 10 kg`
   - Purchase Cost: KSh 8,000 (10 kg × KSh 800/kg)
   - Sale Value: KSh 12,500 (50 sqm × KSh 250/sqm)
   - Profit: KSh 4,500 (56.25% margin)
8. **Click**: "Add to Cart"
9. **Cart shows**: "Greenhouse Film (50 sqm → kg)"
10. **Complete sale** with converted product

---

## **Visual Indicators:**

### **✅ Eligible Users See:**
- Special banner at top
- Conversion badges on products
- "Click for conversion options" text
- Dynamic conversion modal

### **❌ Non-Eligible Users See:**
- Standard POS interface
- No conversion badges
- Direct add to cart on click

---

## **Key Benefits:**

1. **Flexible Selling**: Sell in customer's preferred units
2. **Real-time Calculations**: Instant profit analysis
3. **No Pre-planning**: Convert on-demand during sales
4. **Accurate Pricing**: Based on actual conversion factors
5. **Profit Optimization**: See margins before selling

---

## **Troubleshooting:**

### **No Conversion Badges?**
- Check if you're logged in as eligible business
- Verify products have conversion rules set up
- Go to Product Conversions section to create rules

### **Modal Not Opening?**
- Check browser console for errors
- Ensure JavaScript is enabled
- Verify product has conversion rules

### **Calculation Errors?**
- Check conversion rules are properly set up
- Verify micron values are correct
- Ensure product has valid conversion factors

---

## **Quick Reference:**

| Action | Result |
|--------|--------|
| Click product with 🔄 badge | Opens conversion modal |
| Click product without badge | Direct add to cart |
| Fill conversion form | Real-time calculation |
| Click "Add to Cart" | Adds converted product |
| Complete checkout | Sale with conversion details |

**The system automatically detects your eligibility and shows the appropriate interface!** 🎯






