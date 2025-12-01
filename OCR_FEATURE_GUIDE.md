# OCR Data Capture Feature - Complete Guide

## Overview
The OCR (Optical Character Recognition) feature uses Claude AI's vision capabilities to extract handwritten or printed records from images and automatically add them to your database.

## Supported Record Types

### 1. Product Inventory
**What you can scan:**
- Handwritten inventory lists
- Product stock sheets
- Supplier delivery notes
- Warehouse count records

**Extracted fields:**
- Product Name
- SKU/Product Code
- Quantity
- Unit Price
- Category
- Notes

### 2. Sales Records
**What you can scan:**
- Sales receipts
- Daily sales logs
- Transaction notebooks
- Cash register tapes

**Extracted fields:**
- Product/Service Name
- Customer Name
- Quantity Sold
- Unit Price
- Total Amount
- Date
- Payment Method (Cash/M-PESA/Card)

### 3. Service Bookings
**What you can scan:**
- Appointment books
- Booking registers
- Service schedules
- Customer appointment cards

**Extracted fields:**
- Customer Name
- Service Name
- Appointment Date
- Appointment Time
- Service Price
- Phone Number
- Duration

## How to Use

### Step 1: Access OCR Feature
- Navigate to **Dashboard → Scan Records (OCR)**
- Or use the quick action menu

### Step 2: Select Record Type
Choose what type of data you want to extract:
- **Product Inventory** - For stock and product data
- **Sales Records** - For transaction history
- **Service Bookings** - For appointments

### Step 3: Upload Image
- Click "Choose Image"
- Take a photo with your phone camera OR
- Upload an existing image
- Supported formats: JPG, PNG, HEIC (max 10MB)

### Step 4: Extract Data
- Click "Extract Data"
- Wait 15-30 seconds for AI processing
- Claude AI will analyze the image and extract all visible records

### Step 5: Review & Edit
- Review all extracted data in the table
- Edit any incorrect entries
- Remove unwanted rows
- Add missing information

### Step 6: Save
- Click "Save All Records"
- Data is automatically added to your database
- Redirects to appropriate section (Products/Sales/Services)

## Tips for Best Results

### Image Quality
✅ **DO:**
- Use good lighting
- Take clear, focused photos
- Capture the entire document
- Use a steady hand or flat surface
- Ensure text is readable

❌ **DON'T:**
- Use blurry or dark images
- Cut off parts of the document
- Have glare or reflections
- Use extremely low resolution

### Handwriting
- Write clearly and legibly
- Use block letters when possible
- Separate entries with lines or spacing
- Include labels (Name:, Qty:, Price:)

### Document Layout
- Organize data in columns or lists
- Keep related information together
- Use consistent formatting
- Include units (KSh, pcs, kg, etc.)

## Technical Details

### API Requirements
- **Claude API Key** - Required (Sonnet 4 model)
- **No additional API keys** needed
- Uses Claude's vision capabilities

### Processing Time
- Small images (1-5 items): ~10-15 seconds
- Medium images (5-15 items): ~15-25 seconds
- Large images (15+ items): ~25-40 seconds

### Limitations
- Max image size: 10MB
- Recommended max items per image: 30-40
- Very complex or messy documents may require manual review

## Database Integration

### Product Inventory
- Creates new products in your inventory
- Auto-generates SKU if not provided
- Sets category and pricing
- Updates stock quantities

### Sales Records
- Creates completed orders
- Auto-creates customers if not exist
- Auto-creates products if not exist
- Records payment method and date
- Updates inventory automatically

### Service Bookings
- Creates service appointments
- Auto-creates customers if not exist
- Auto-creates services if not exist
- Sets booking status to "confirmed"
- Assigns staff if mentioned

## Error Handling

### If extraction fails:
1. Check image quality
2. Ensure text is readable
3. Try with better lighting
4. Simplify the document
5. Break into smaller sections

### If data is incorrect:
1. Edit entries before saving
2. Remove incorrect rows
3. Manually add missing data
4. Save and verify in database

## Security & Privacy

- Images are temporarily stored for processing
- Deleted immediately after extraction
- No images are permanently saved
- All data encrypted in transit
- GDPR and data protection compliant

## Use Cases

### Retail Stores
- Digitize old inventory records
- Capture supplier delivery notes
- Record daily cash sales
- Import historical data

### Service Businesses
- Import appointment book entries
- Capture booking forms
- Record customer contacts
- Migrate from paper system

### Restaurants
- Record menu items and prices
- Capture daily sales summaries
- Import ingredient stock lists
- Track customer orders

### Salons/Spas
- Import client appointment books
- Record service pricing
- Capture booking details
- Track staff schedules

## Advanced Features

### Batch Processing
- Scan multiple pages
- Process separately
- Review all before saving
- Bulk import capability

### Smart Defaults
- Auto-fills missing prices with 0
- Guesses appropriate categories
- Assigns default payment methods
- Uses today's date if not specified

### Data Validation
- Checks for duplicate products
- Validates price formats
- Ensures required fields
- Prevents invalid entries

## Integration Points

### Connected To:
- Product Management System
- Sales & Orders Module
- Service Booking System
- Customer Database
- Inventory Tracking

### Affected Systems:
- Dashboard statistics
- Analytics reports
- Stock levels
- Sales history
- Customer records

## Troubleshooting

**Problem:** "Could not extract data from image"
**Solution:** Image may be too unclear. Try:
- Better lighting
- Higher resolution
- Clearer handwriting
- Different angle

**Problem:** Wrong data extracted
**Solution:** Review and edit before saving:
- Check all fields
- Correct prices
- Fix quantities
- Verify names

**Problem:** Missing records
**Solution:** Some items not detected:
- Ensure all visible
- Check spacing
- Add manually
- Re-scan if needed

## API Endpoints

```
GET  /ocr           - Show OCR interface
POST /ocr/extract   - Process image and extract data
POST /ocr/save      - Save extracted records to database
```

## Future Enhancements

### Planned Features:
- [ ] Multi-page PDF support
- [ ] Batch image upload
- [ ] Auto-categorization improvement
- [ ] Historical scan archive
- [ ] Export extracted data
- [ ] Mobile app integration
- [ ] Voice notes + OCR combo
- [ ] Receipt template recognition

## Support

For issues or questions:
1. Check this documentation
2. Review extracted data carefully
3. Contact support if persistent issues
4. Provide sample images for improvement

---

**Version:** 1.0.0
**Last Updated:** November 25, 2025
**Requires:** Claude API (Sonnet 4), PHP 8.1+, Laravel 10+
