# Peraly - Project Summary & Documentation Index

## Project Overview

**Peraly** is a complete, production-ready GCash transaction tracking application specifically designed for small Filipino businesses. Built with Laravel 11 and Filament 3, it provides a professional admin panel for managing cash flow, tracking expenses, and generating financial reports.

### Key Information

- **Project Name**: Peraly
- **Version**: 1.0.0
- **Status**: Ready for Production
- **License**: MIT
- **Target Users**: Small businesses in the Philippines using GCash

## What's Included

### ✅ Core Features Implemented

1. **Transaction Management**
   - Create, read, update, delete transactions
   - Automatic GCash fee calculation (tiered structure)
   - Date-based filtering
   - Notes and custom fields

2. **Financial Dashboard**
   - Real-time summary cards (Cash In, Cash Out, Fees, Net Profit)
   - 30-day cash flow chart
   - Recent transactions widget
   - Color-coded statistics

4. **Reporting System**
   - Generate daily, weekly, monthly reports
   - Automatic calculation of period totals
   - Custom date range reports
   - View and manage reports

5. **Authentication**
   - Secure Filament admin panel
   - Built-in user authentication
   - Admin user creation on seed
   - Session management

6. **Database**
   - Fully normalized relational schema
   - SQLite, MySQL, and PostgreSQL support
   - Data integrity with foreign keys
   - Optimized for performance

7. **User Interface**
   - Modern, clean design with Tailwind CSS
   - Responsive layout (desktop and tablet)
   - Dark mode support
   - Professional color scheme

## Project Structure

```
gcash-tracker/
│
├── app/
│   ├── Filament/Admin/
│   │   ├── AdminPanelProvider.php      # Main Filament configuration
│   │   ├── Resources/                   # CRUD resources
│   │   │   ├── CategoryResource.php
│   │   │   ├── TransactionResource.php
│   │   │   └── ReportResource.php
│   │   ├── Pages/                       # Admin pages
│   │   │   └── Dashboard.php
│   │   └── Widgets/                     # Dashboard widgets
│   │       ├── StatsOverview.php
│   │       ├── CashFlowChart.php
│   │       └── RecentTransactions.php
│   │
│   ├── Models/
│   │   ├── User.php                     # User model with business fields
│   │   ├── Category.php                 # Transaction categories
│   │   ├── Transaction.php              # Transaction with fee calculation
│   │   └── Report.php                   # Financial reports
│   │
│   ├── Http/
│   │   └── Controllers/
│   │       └── (Filament handles all CRUD)
│   │
│   └── Providers/
│       └── AppServiceProvider.php
│
├── database/
│   ├── migrations/
│   │   ├── create_categories_table.php
│   │   ├── create_transactions_table.php
│   │   ├── create_reports_table.php
│   │   └── add_fields_to_users_table.php
│   │
│   └── seeders/
│       └── DatabaseSeeder.php           # Sample data
│
├── resources/
│   ├── views/
│   │   └── (Blade templates via Filament)
│   └── css/
│       └── app.css                      # Tailwind CSS
│
├── config/
│   └── (Laravel configuration files)
│
├── routes/
│   ├── web.php                          # Web routes
│   └── api.php                          # API routes (future)
│
├── storage/
│   ├── database.sqlite                  # SQLite database
│   └── logs/
│
├── public/
│   └── (Publicly accessible files)
│
├── .env                                 # Environment configuration
├── .env.example                         # Example environment
├── composer.json                        # PHP dependencies
├── package.json                         # Node.js dependencies
⁠├── vite.config.js                      # Vite bundler config
│
└── Documentation Files:
    ├── README.md                        # Main documentation
    ├── QUICKSTART.md                    # Quick start guide
    ├── SETUP.md                         # Detailed setup instructions
    ├── DEPLOYMENT.md                    # Deployment guide
    ├── INSTALLATION_CHECKLIST.md        # Installation checklist
    └── API_REFERENCE.md                 # API documentation (future)
```

## Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **Framework** | Laravel | 11 |
| **Admin Panel** | Filament | 3 |
| **Frontend** | Livewire | 4.1.4 |
| **Styling** | Tailwind CSS | 3 |
| **Database** | SQLite/MySQL/PostgreSQL | - |
| **PHP** | PHP | 8.2+ |
| **Node** | Node.js | 16+ |

## Installation Summary

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 16+ (optional)
- SQLite, MySQL, or PostgreSQL

### Quick Install (5 minutes)
```bash
cd gcash-tracker
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

Access at: `http://localhost:8000/admin`
- Email: `admin@peraly.com`
- Password: `password`

## Database Schema

### Users (from Laravel)
- id, name, email, password
- phone_number (new)
- business_name (new)
- email_verified_at, remember_token
- created_at, updated_at

### Categories
- id, name, type (cash-in/cash-out)
- created_at, updated_at

### Transactions
- id, transaction_date, type (cash-in/cash-out)
- amount, fee (auto-calculated)
- category_id (foreign key)
- notes
- created_at, updated_at

### Reports
- id, name, period (daily/weekly/monthly)
- start_date, end_date
- total_cash_in, total_cash_out, total_fees, net_profit
- user_id (foreign key)
- created_at, updated_at

## Features by User Story

### As a Business Owner
- ✅ Track my daily GCash income
- ✅ Monitor my cash expenses
- ✅ See automatic fee calculations
- ✅ Understand my monthly profit/loss
- ✅ Generate financial reports
- ✅ Organize transactions by category

### As an Accountant
- ✅ Access detailed transaction history
- ✅ Filter transactions by date and type
- ✅ Generate period-based reports
- ✅ Export reports for analysis
- ✅ View category breakdowns
- ✅ Track fee patterns

### As an Admin
- ✅ Manage user accounts
- ✅ Create and edit categories
- ✅ Monitor all transactions
- ✅ Generate and archive reports
- ✅ Access the secure admin panel

## GCash Fee Calculation

Automatic tiered fees are calculated on every transaction:

### Cash-In
- ₱1-₱5,000: 1.0%
- ₱5,001-₱10,000: 1.5%
- ₱10,001+: 2.0%

### Cash-Out
- ₱1-₱5,000: 1.5%
- ₱5,001-₱10,000: 2.0%
- ₱10,001+: 2.5%

**Customizable** in `app/Models/Transaction.php`

## Color Palette

Professional business color scheme:
- **Primary**: #4A90E2 (Sky Blue)
- **Accent**: #50E3C2 (Cyan)
- **Success**: #34D399 (Green)
- **Danger**: #F87171 (Red)
- **Warning**: #FBBF24 (Yellow)
- **Background**: #F9FAFB (Off-White)
- **Text**: #1F2937 (Charcoal)

## Sample Data Included

After running `php artisan db:seed`:
- ✅ 1 admin user created
- ✅ 10 categories (Sales, Refund, Inventory, Rent, etc.)
- ✅ 15 sample cash-in transactions
- ✅ 12 sample cash-out transactions
- ✅ All transactions have calculated fees
- ✅ Dashboard shows aggregated data

## Filament Resources

### CategoryResource
- List all categories
- Create new categories
- Edit categories
- Delete categories
- Filter by type

### TransactionResource
- List all transactions (sortable, filterable)
- Create new transactions with auto-fee calculation
- Edit transaction details
- Delete transactions
- Filters: date range, category, type
- Columns: date, type, category, amount, fee

### ReportResource
- List all generated reports
- Create new reports (auto-calculates totals)
- View report details
- Delete reports
- Filter by period

## Dashboard Widgets

### StatsOverview (4 cards)
- Total Cash In (monthly)
- Total Cash Out (monthly)
- Total Fees (monthly)
- Net Profit/Loss (color-coded)

### CashFlowChart (line chart)
- Shows last 30 days
- Cash In vs Cash Out trends
- Interactive legend

### RecentTransactions (table widget)
- Last 5 transactions
- Date, type, category, amount, fee
- Sortable, responsive

## File Permissions

Set after installation:
```bash
chmod -R 775 storage bootstrap/cache
chmod 755 artisan
chmod 755 database/database.sqlite (SQLite only)
```

## Configuration Files

### .env (Environment)
- Database connection details
- Application name (Peraly)
- Debug mode (true for dev, false for prod)
- Session driver (database)

### config/app.php
- Application name
- Timezone
- Debug mode
- Encryption

### config/database.php
- Database driver selection
- Connection details

## Common Commands

```bash
# Development
php artisan serve                           # Start server
php artisan tinker                          # Interactive shell
php artisan make:model ModelName --migration  # Create model

# Database
php artisan migrate                         # Run migrations
php artisan migrate:refresh                 # Reset database
php artisan db:seed                         # Seed sample data
php artisan migrate:status                  # Check status

# Maintenance
php artisan config:clear                    # Clear cache
php artisan cache:clear                     # Clear all cache
php artisan view:cache                      # Cache views
php artisan optimize                        # Optimize framework

# Testing
php artisan test                            # Run tests
php artisan tinker                          # Test code interactively
```

## Customization Points

### Fee Structure
Edit `app/Models/Transaction.php`:
```php
public static function calculateFee(float $amount, string $type): float
```

### Dashboard Colors
Edit `app/Filament/Admin/AdminPanelProvider.php`:
```php
->colors([...])
```

### Dashboard Widgets
Modify files in `app/Filament/Admin/Widgets/`:
- StatsOverview.php
- CashFlowChart.php
- RecentTransactions.php

### Categories
Modify seeder in `database/seeders/DatabaseSeeder.php`

## Documentation files

| Document | Purpose |
|----------|---------|
| [README.md](README.md) | Main documentation and feature overview |
| [QUICKSTART.md](QUICKSTART.md) | 5-minute quick start guide for new users |
| [SETUP.md](SETUP.md) | Detailed installation and configuration |
| [DEPLOYMENT.md](DEPLOYMENT.md) | Production deployment guide |
| [INSTALLATION_CHECKLIST.md](INSTALLATION_CHECKLIST.md) | Verification checklist |
| [API_REFERENCE.md](API_REFERENCE.md) | Future REST API documentation |

## Security Considerations

✅ **Implemented:**
- Filament's built-in authentication
- CSRF protection
- Password hashing (bcrypt)
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade escaping)

⚠️ **To-Do for Production:**
- [ ] Change default admin password
- [ ] Set APP_DEBUG=false
- [ ] Configure HTTPS/SSL
- [ ] Set up database backups
- [ ] Configure email for notifications
- [ ] Review and update security headers
- [ ] Set up monitoring and logging

## Performance Optimization

Already implemented:
- Efficient database queries with Eloquent ORM
- Pagination ready
- Tailwind CSS (no unused CSS)
- Lazy-loaded relationships

Further optimizations:
- Add database indexing
- Implement caching
- Use CDN for static assets
- Optimize images

## Potential Future Enhancements

- [ ] Multi-user with role-based access control
- [ ] Email notifications for large transactions
- [ ] Recurring transaction templates
- [ ] Bank reconciliation
- [ ] Tax report generation
- [ ] Expense budgeting and alerts
- [ ] Mobile app (iOS/Android)
- [ ] REST API for integrations
- [ ] Import/Export from CSV
- [ ] Multi-language support

## Support Resources

1. **Official Documentation**
   - Laravel: https://laravel.com/docs
   - Filament: https://filamentphp.com/docs
   - Tailwind: https://tailwindcss.com/docs

2. **Community**
   - Laravel Forums: https://laracasts.com
   - Filament Discussions: https://github.com/filamentphp/filament/discussions

3. **This Project**
   - See README.md for feature overview
   - See SETUPMD for installation
   - See QUICKSTART.md for usage guide

## Final Notes

### What's Ready for Use
- ✅ Admin panel is fully functional
- ✅ All CRUD operations work
- ✅ Database is optimized
- ✅ Responsive design complete
- ✅ Documentation is comprehensive
- ✅ Sample data included
- ✅ Production ready (with security updates)

### What's Coming
- 🔄 REST API endpoints
- 🔄 Mobile app integration
- 🔄 Advanced reporting
- 🔄 Multi-user support
- 🔄 Email notifications

### Project Statistics
- **Lines of Code**: ~2,000+
- **Database Tables**: 6 (including Laravel defaults)
- **Models**: 4
- **Filament Resources**: 3
- **Dashboard Widgets**: 3
- **Documentation Files**: 6
- **Package Dependencies**: 20+

---

## Contact & Support

For issues, enhancements, or questions:
1. Check the documentation files first
2. Review the code comments
3. Test with sample data
4. Consult Laravel/Filament documentation

---

**Peraly** - Making GCash tracking simple for Filipino businesses 🇵🇭

**Version**: 1.0.0  
**Last Updated**: February 15, 2026  
**Status**: Ready for Production
