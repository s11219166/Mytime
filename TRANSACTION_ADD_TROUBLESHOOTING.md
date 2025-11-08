# Transaction Add - Troubleshooting Guide

## Issue: Cannot Add New Transaction

Follow these steps to identify and fix the problem.

---

## Step 1: Check Browser Console for Errors

1. Open the financial page in your browser
2. Press **F12** to open Developer Tools
3. Go to the **Console** tab
4. Try to add a transaction
5. Look for any red error messages

**Common errors and solutions:**

### Error: "CSRF token not found"
- **Cause**: Missing CSRF token in page
- **Solution**: Refresh the page and try again

### Error: "Categories not loaded"
- **Cause**: Financial categories not loading
- **Solution**: Check if categories exist in database

### Error: "Network error" or "Failed to fetch"
- **Cause**: API endpoint not responding
- **Solution**: Check if Laravel server is running

---

## Step 2: Verify Form Fields

1. Click "Add Transaction" button
2. Check if modal opens
3. Verify all fields are visible:
   - [ ] Date picker
   - [ ] Type dropdown
   - [ ] Category dropdown
   - [ ] Amount input
   - [ ] Status dropdown
   - [ ] Reference number field
   - [ ] Description textarea
   - [ ] Submit button

**If fields are missing:**
- Clear browser cache (Ctrl+Shift+R)
- Refresh page
- Try different browser

---

## Step 3: Fill Form Correctly

1. **Date**: Select today's date or earlier
2. **Type**: Select one of:
   - Income
   - Expense
   - Savings
   - Bank Deposit
3. **Category**: Select a category (must match type)
4. **Amount**: Enter a number (e.g., 100.50)
5. **Status**: Select "Completed"
6. **Description**: Optional, add notes

**Important**: All required fields must be filled!

---

## Step 4: Check Network Tab

1. Open Developer Tools (F12)
2. Go to **Network** tab
3. Try to add a transaction
4. Look for a request to `/financial/transaction`
5. Click on it and check:
   - **Status**: Should be 200 or 201
   - **Response**: Should show `"success": true`

**If status is not 200/201:**
- Check the response for error message
- Look for validation errors
- Check server logs

---

## Step 5: Check Server Logs

1. Open terminal/command prompt
2. Navigate to project directory
3. Check Laravel logs:

```bash
# View recent logs
tail -f storage/logs/laravel.log

# Or on Windows
type storage\logs\laravel.log
```

4. Look for error messages related to financial transactions

---

## Step 6: Verify Database

Check if financial tables exist:

```bash
# Connect to database
php artisan tinker

# Check if categories exist
>>> App\Models\FinancialCategory::count()

# Check if transactions table exists
>>> DB::table('financial_transactions')->count()

# Exit tinker
>>> exit
```

---

## Step 7: Test API Endpoint Directly

Use curl or Postman to test the API:

```bash
# Get CSRF token first
curl -X GET http://localhost:8000/financial

# Then POST a transaction
curl -X POST http://localhost:8000/financial/transaction \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -d '{
    "transaction_date": "2024-01-15",
    "type": "expense",
    "category_id": 1,
    "amount": 100.50,
    "description": "Test transaction",
    "status": "completed",
    "reference_number": "TEST-001"
  }'
```

---

## Step 8: Check Form Validation

The form validates these fields:

| Field | Rules | Example |
|-------|-------|---------|
| transaction_date | required, date, before_or_equal:today | 2024-01-15 |
| type | required, in:income,expense,savings,bank_deposit | expense |
| category_id | required, integer, exists:financial_categories,id | 1 |
| amount | required, numeric, min:0.01, max:9999999.99 | 100.50 |
| status | required, in:completed,pending,cancelled | completed |
| description | nullable, string, max:1000 | Optional notes |
| reference_number | nullable, string, max:50 | Optional reference |

**If validation fails:**
- Check error message in notification
- Verify field values match rules
- Try with different values

---

## Step 9: Debug JavaScript

Add this to browser console to debug:

```javascript
// Check if Alpine.js is loaded
console.log(window.Alpine);

// Check if component is initialized
console.log(window.financialDashboard);

// Check categories
console.log(window.financialCategories);

// Manually test form submission
// (Open console and run this)
const component = document.querySelector('[x-data]').__x;
console.log('Form data:', component.formData);
console.log('All categories:', component.allCategories);
console.log('Filtered categories:', component.filteredCategories);
```

---

## Step 10: Common Issues and Solutions

### Issue: Modal doesn't open
**Solution:**
```javascript
// In browser console:
// Check if showModal is working
document.querySelector('[x-data]').__x.showModal = true;
```

### Issue: Categories dropdown is empty
**Solution:**
```javascript
// In browser console:
// Check if categories loaded
console.log(window.financialCategories);
// If empty, categories not loaded from server
```

### Issue: Form submits but nothing happens
**Solution:**
- Check Network tab for response
- Check browser console for errors
- Check server logs for exceptions

### Issue: "Please select a category" error
**Solution:**
- Make sure you selected a type first
- Then select a category that matches the type
- Category must exist in database

### Issue: Amount field won't accept input
**Solution:**
- Use decimal format: 100.50
- Don't use currency symbols: ✓ 100.50 ✗ $100.50
- Must be between 0.01 and 9999999.99

---

## Step 11: Check Database Connection

```bash
# Test database connection
php artisan tinker

# Check if connected
>>> DB::connection()->getPdo()

# Check financial_categories table
>>> DB::table('financial_categories')->get()

# Check financial_transactions table
>>> DB::table('financial_transactions')->get()

# Exit
>>> exit
```

---

## Step 12: Verify Routes

Check if routes are registered:

```bash
# List all routes
php artisan route:list | grep financial

# Should show:
# GET|HEAD  /financial
# GET|HEAD  /financial/transaction/{id}
# POST      /financial/transaction
# PUT       /financial/transaction/{id}
# DELETE    /financial/transaction/{id}
# GET|HEAD  /financial/chart-data
# GET|HEAD  /financial/summary
# GET|HEAD  /financial/export
```

---

## Step 13: Check Authentication

Make sure you're logged in:

```javascript
// In browser console:
// Check if user is authenticated
fetch('/api/user')
  .then(r => r.json())
  .then(data => console.log('User:', data));
```

---

## Step 14: Clear Cache and Restart

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Restart Laravel
php artisan serve
```

---

## Step 15: Check Browser Compatibility

Try in different browsers:
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge

If it works in one browser but not another, it's a browser-specific issue.

---

## Final Checklist

Before asking for help, verify:

- [ ] Browser console has no errors
- [ ] Network tab shows 200 response
- [ ] Database has categories
- [ ] All form fields are filled
- [ ] Date is today or earlier
- [ ] Amount is valid number
- [ ] Category matches type
- [ ] Laravel server is running
- [ ] You are logged in
- [ ] CSRF token is present

---

## Getting Help

If you still can't add transactions, provide:

1. **Browser console errors** (screenshot or text)
2. **Network response** (from Network tab)
3. **Server logs** (from storage/logs/laravel.log)
4. **Form data** (what you entered)
5. **Database status** (categories count)

---

## Quick Test

Try this minimal test:

```bash
# 1. Start Laravel
php artisan serve

# 2. Open browser
# http://localhost:8000/financial

# 3. Open console (F12)

# 4. Run this:
fetch('/financial/transaction', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  },
  body: JSON.stringify({
    transaction_date: '2024-01-15',
    type: 'expense',
    category_id: 1,
    amount: 100,
    status: 'completed'
  })
})
.then(r => r.json())
.then(d => console.log(d));

# 5. Check response in console
```

---

**Status**: Troubleshooting guide ready
**Last Updated**: 2024
