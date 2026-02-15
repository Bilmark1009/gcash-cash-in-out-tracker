# Peraly - Quick Start Guide

## What is Peraly?

Peraly is a GCash transaction tracker specifically designed for small Filipino businesses. It helps you:
- Track all your GCash cash-in and cash-out transactions
- Automatically calculate service fees
- Monitor your cash flow with visual charts
- Generate detailed financial reports
- Export data for accounting purposes

## Getting Started in 5 Minutes

### Step 1: Prerequisites Check
Make sure you have:
- PHP 8.2 or higher installed
- Composer installed
- Node.js 16+ installed (optional, for building assets)

Check versions:
```bash
php --version
composer --version
```

### Step 2: First Run
Navigate to the project folder and run:

```bash
# Install JavaScript packages
npm install

# Run migrations to create database tables
php artisan migrate

# Populate with sample data (optional)
php artisan db:seed

# Start the server
php artisan serve
```

The server will start at `http://localhost:8000`

### Step 3: Login to Admin Panel
1. Visit: `http://localhost:8000/admin`
2. Email: `admin@peraly.com`
3. Password: `password`

⚠️ **IMPORTANT**: Change your password immediately after first login!

## Main Dashboard

When you login, you'll see:

### Summary Cards (Top Section)
- **Total Cash In**: All your incoming GCash with fees deducted
- **Total Cash Out**: All your outgoing GCash plus fees
- **Total Fees**: GCash service fees charged this month
- **Net Profit/Loss**: Your actual profit after all expenses and fees

### Cash Flow Chart (Middle Section)
A line graph showing your cash movements over the last 30 days. This helps you see trends and patterns.

### Recent Transactions (Bottom Section)
A quick list of your 5 most recent transactions

## Managing Transactions

### Adding a New Transaction
1. Click **Transactions** in the left menu
2. Click **Create** button
3. Fill in the form:
   - **Date**: When the transaction happened
   - **Type**: Cash In or Cash Out
   - **Amount**: How much money
   - **Category**: What it's for (Sales, Inventory, etc.)
   - **Notes**: Optional details
4. The **Fee** is automatically calculated
5. Click **Save**

### Viewing All Transactions
1. Click **Transactions** in the left menu
2. You'll see a table of all your transactions
3. Use the filters to find transactions by:
   - Date range
   - Category
   - Type (Cash In/Out)

### Editing a Transaction
1. Click on any transaction in the table
2. Modify the information
3. Click **Save**

### Deleting a Transaction
1. Click on the transaction
2. Click **Delete** button in the top right
3. Confirm

## Managing Categories

### What are Categories?
Categories help you organize where your money comes from and goes to:
- **Cash In Categories**: Sales, Refunds, Deposits
- **Cash Out Categories**: Inventory, Rent, Salaries

### Adding New Categories
1. Click **Categories** in the left menu
2. Click **Create**
3. Enter:
   - **Name**: What's this category for?
   - **Type**: Cash In or Cash Out
4. Click **Save**

### Using Categories
When you add a transaction, select the appropriate category. This helps you analyze spending patterns.

## Understanding the Fee Calculation

### How Fees Work
GCash charges different fees based on the amount and type:

#### Cash-In Fees (Loading money into GCash)
- ₱1-₱5,000: 1% fee
- ₱5,001-₱10,000: 1.5% fee
- ₱10,001+: 2% fee

#### Cash-Out Fees (Withdrawing money from GCash)
- ₱1-₱5,000: 1.5% fee
- ₱5,001-₱10,000: 2% fee
- ₱10,001+: 2.5% fee

### Example
If you cash-in ₱10,000:
- Fee: ₱10,000 × 1.5% = ₱150
- Net amount: ₱9,850

The app calculates this automatically when you create a transaction!

## Generating Reports

### What Reports Do
Reports summarize your transactions over a specific time period and help you:
- See how much you earned and spent
- Calculate your profit
- Identify spending trends
- Archive for accounting purposes

### Creating a Report
1. Click **Reports** in the left menu
2. Click **Create**
3. Fill in:
   - **Name**: What to call this report
   - **Period**: Daily, Weekly, or Monthly
   - **Start Date**: First day of the period
   - **End Date**: Last day of the period
4. The totals calculate automatically
5. Click **Save**

### Viewing All Reports
1. Click **Reports** in the left menu
2. You'll see all your generated reports
3. Click on any report to view details

## Tips for Best Results

### Daily Habits
- **Record transactions same day**: Don't wait - record cash-in/out immediately
- **Use consistent categories**: Makes it easier to find spending patterns
- **Add helpful notes**: "GCash load for inventory" is better than blank

### Weekly Tasks
- Check your cash flow chart for trends
- Review at least one report
- Ensure all transactions are recorded

### Monthly Tasks
- Generate a full monthly report
- Compare with previous months
- Adjust if needed

## Common FAQ

**Q: Where is my database stored?**
A: By default, it uses SQLite stored in `database/database.sqlite`

**Q: Can I use MySQL instead of SQLite?**
A: Yes! Edit your `.env` file and follow the [SETUP.md](SETUP.md) instructions

**Q: How do I backup my data?**
A: Export reports as CSV/PDF, or copy the `database/database.sqlite` file

**Q: Can multiple people use this?**
A: Currently it's single-user. Multi-user support is planned for future versions

**Q: What if I forgot my password?**
A: Run this in your terminal:
```bash
php artisan tinker
App\Models\User::first()->update(['password' => bcrypt('newpassword')])
exit
```

**Q: How do I add more admin users?**
A: This feature is coming in the future. Contact support for now.

## Common Commands

```bash
# Start the application
php artisan serve

# Reset everything (careful!)
php artisan migrate:refresh --seed

# View all transactions in console
php artisan tinker
App\Models\Transaction::all()

# Make someone an admin
php artisan tinker
App\Models\User::create(['name' => 'Name', 'email' => 'user@example.com', 'password' => bcrypt('password')])
```

## Need Help?

### Check your setup
```bash
# Verify everything is installed
php --version
composer --version
npm --version

# Clear cache if things seem broken
php artisan config:clear
php artisan cache:clear
```

### Server not starting?
- Make sure port 8000 is free: `netstat -an | findstr :8000`
- Try a different port: `php artisan serve --port=8001`
- Check logs: `storage/logs/laravel.log`

### Database issues?
- Check database exists: SQLite file should be at `database/database.sqlite`
- Run migrations again: `php artisan migrate`
- Clear cache: `php artisan config:clear`

## Next Steps

1. **Customize**: Add your business information in the admin panel
2. **Populate**: Start entering your GCash transactions
3. **Monitor**: Check your dashboard weekly
4. **Report**: Generate monthly reports for your records

---

**Need more help?** Check [SETUP.md](SETUP.md) for detailed installation instructions, or see [README.md](README.md) for all features.

Happy tracking! 🇵🇭
