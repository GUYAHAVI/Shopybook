# Inventory Costing Integration

## Overview
Inventory purchase costs from stock receipts are now fully integrated into the profit/loss calculations across the entire system. This ensures accurate financial reporting by accounting for the actual cost of goods purchased.

## What Changed

### 1. **Business Model - New Methods** (`app/Models/Business.php`)

Added inventory cost calculation methods:

```php
// Get total inventory costs (all time)
$business->total_inventory_costs

// Get inventory costs for today
$business->getTodayInventoryCosts()

// Get inventory costs for specific month
$business->getInventoryCostsForMonth($year, $month)

// Get inventory costs for date range
$business->getInventoryCostsForDateRange($startDate, $endDate)
```

### 2. **Dashboard Controller** (`app/Http/Controllers/DashboardController.php`)

**Updated Profit Calculation:**
```php
// OLD (incorrect):
$netProfit = $todaySales;

// NEW (correct):
$todayInventoryCosts = $business->getTodayInventoryCosts();
$todayOtherCosts = $business->costs()->whereDate('date', today())->sum('amount');
$netProfit = $todaySales - $todayInventoryCosts - $todayOtherCosts;

// Combined total net profit
$totalNetProfit = $netProfit + $serviceProfit;
```

**New Dashboard Variables:**
- `$todayInventoryCosts` - Inventory purchases made today
- `$todayOtherCosts` - Other operating costs (rent, utilities, etc.)
- `$totalNetProfit` - Combined net profit from products and services

### 3. **Business Analysis Controller** (`app/Http/Controllers/BusinessAnalysisController.php`)

**Updated Financial Data Calculation:**
```php
// OLD:
$productCosts = $business->products()
    ->selectRaw('sum(stock_quantity * cost_price) as total_cost')
    ->value('total_cost') ?? 0;

// NEW (using actual purchase costs):
$inventoryPurchaseCosts = $business->total_inventory_costs;

// Total costs now include:
$totalCosts = $inventoryPurchaseCosts + $businessExpenses + $totalSalaryCosts;
```

### 4. **New Costs Summary View** (`resources/views/business/costs-summary.blade.php`)

A comprehensive dashboard showing:
- Total inventory costs (from stock receipts)
- Total salary costs
- Operating expenses
- Total combined costs
- Cost breakdown chart (doughnut)
- Monthly inventory purchase trend (bar chart)
- Quick links to:
  - Stock receipt history
  - Operating costs
  - Staff & salaries

## Cost Categories

### 1. **Inventory Purchase Costs**
- Source: `stock_receipts` table
- Calculation: Sum of all `total_cost` from stock receipts
- Represents: **Actual money spent buying inventory**
- Example: Bought 100 units @ KSh 50/unit = KSh 5,000

### 2. **Salary Costs**
- Source: `staff` table + `costs` table (type='salary')
- Calculation: Sum of staff salaries + manual salary entries
- Represents: Employee compensation

### 3. **Operating Expenses**
- Source: `costs` table (type != 'salary')
- Types: utility, rent, water, misc, activity, renovation, other
- Represents: Day-to-day business expenses

## Profit/Loss Formula

### **Product Sales Profit:**
```
Net Profit = Revenue - Inventory Costs - Operating Costs - Salary Costs
```

### **Complete Business Profit:**
```
Total Revenue = Product Sales + Service Revenue
Total Costs = Inventory Purchases + Operating Expenses + Salaries
Net Profit = Total Revenue - Total Costs
Profit Margin = (Net Profit / Total Revenue) × 100
```

## Usage Examples

### Dashboard Display

```php
// Today's performance
Revenue: KSh 50,000
- Inventory Purchases: KSh 20,000
- Operating Costs: KSh 5,000
- Salary Costs: KSh 10,000
= Net Profit: KSh 15,000
```

### Monthly Report

```php
$year = 2025;
$month = 10;

$revenue = $business->orders()
    ->where('status', 'completed')
    ->whereYear('created_at', $year)
    ->whereMonth('created_at', $month)
    ->sum('total_amount');

$inventoryCosts = $business->getInventoryCostsForMonth($year, $month);
$salaryCosts = $business->getSalaryCostsForMonth($year, $month);
$operatingCosts = $business->costs()
    ->whereYear('date', $year)
    ->whereMonth('date', $month)
    ->where('type', '!=', 'salary')
    ->sum('amount');

$profit = $revenue - $inventoryCosts - $salaryCosts - $operatingCosts;
```

### Date Range Analysis

```php
$startDate = '2025-01-01';
$endDate = '2025-12-31';

$inventoryCosts = $business->getInventoryCostsForDateRange($startDate, $endDate);
```

## Reports & Analytics

### Dashboard Metrics
✅ Today's sales revenue
✅ Today's inventory purchases (cost)
✅ Today's other costs
✅ Today's net profit (accurate)
✅ Combined product + service profit

### Financial Reports
✅ Total revenue
✅ Inventory purchase costs (replacing old inventory value calculation)
✅ Operating expenses
✅ Salary costs
✅ Net profit
✅ Profit margin

### Cost Summary Page
✅ Inventory costs breakdown
✅ Salary costs
✅ Operating expenses
✅ Total costs
✅ Visual charts
✅ Monthly trends

## Benefits

1. **Accurate Profit Calculations**: Net profit now reflects actual costs, not just theoretical inventory values
2. **Better Cash Flow Tracking**: See exactly how much money is spent on inventory purchases
3. **Cost Analysis**: Identify which cost category is highest
4. **Trend Analysis**: Track inventory purchase patterns over time
5. **Informed Decisions**: Make better purchasing decisions based on actual cost data

## Integration Points

### Existing Features
- ✅ Dashboard profit calculations
- ✅ Financial analysis reports
- ✅ Business intelligence
- ✅ Cost tracking

### New Features
- ✅ Stock receipt recording
- ✅ Receipt history
- ✅ Inventory cost tracking
- ✅ Cost summary dashboard

## Database Schema

### Stock Receipts Table
```sql
- total_cost (decimal) - Automatically calculated from quantity × unit_cost
- receipt_date (date) - When the stock was received
- business_id (string) - Links to business
```

This ensures that every stock receipt contributes to the business's cost calculations.

## Future Enhancements

Potential improvements:
- **COGS (Cost of Goods Sold)**: Track specific costs for specific sales
- **FIFO/LIFO**: Implement inventory costing methods
- **Profit by Product**: Calculate profit per product
- **Cost Alerts**: Notify when costs exceed thresholds
- **Budget Tracking**: Set and track cost budgets
- **Expense Categories**: More granular cost categorization
- **Tax Calculations**: Integrate with tax requirements

## Testing

To verify the integration:

1. **Record a stock receipt**:
   - Go to Products → Receive Stock
   - Record a receipt with cost information
   - Check that it appears in receipt history

2. **Check dashboard**:
   - Verify `$todayInventoryCosts` shows the receipt cost
   - Verify net profit is reduced by the cost

3. **View financial reports**:
   - Check Business Analysis page
   - Verify inventory costs are included in total costs

4. **Cost Summary**:
   - Visit the costs summary page (if implemented)
   - Verify all costs are displayed correctly

## Conclusion

The inventory costing integration provides **accurate, real-world profit/loss calculations** by tracking actual money spent on inventory purchases. This replaces the previous method of using theoretical inventory values and gives business owners a true picture of their profitability.







