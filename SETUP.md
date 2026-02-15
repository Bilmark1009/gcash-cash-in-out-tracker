# Peraly - GCash Transaction Tracker

A modern, full-stack web application for small businesses in the Philippines to track GCash cash-in and cash-out transactions with ease.

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel)
![Filament](https://img.shields.io/badge/Filament-3-FBE247?style=for-the-badge)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3-38BDF8?style=for-the-badge&logo=tailwind-css)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)

## Features

### 💰 Transaction Management
- **Comprehensive Transaction Logging**: Track all GCash cash-in and cash-out transactions
- **Automatic Fee Calculation**: Intelligent tiered GCash service fee calculation based on transaction type and amount
- **Category Management**: Organize transactions by custom categories (Sales, Inventory, Rent, etc.)
- **Transaction Filtering**: Filter by date range, category, and transaction type
- **Notes Support**: Add detailed notes to each transaction for record-keeping

### 📊 Financial Dashboard
- **Summary Cards**: At-a-glance view of:
  - Total Cash In (monthly)
  - Total Cash Out (monthly)
  - Total Fees (monthly)
  - Net Profit/Loss (monthly)
- **Cash Flow Charts**: Visual representation of:
  - Daily, weekly, and monthly cash flow trends
  - Inflow vs outflow comparisons
  - 30-day trend analysis
- **Recent Transactions**: Quick view of latest transactions

### 📈 Reports & Exports
- **Flexible Reporting**: Generate reports for:
  - Daily, weekly, monthly summaries
  - Custom date range reports
  - Category-wise breakdowns
- **Export Options**:
  - CSV format for Excel/Sheets
  - PDF format for printing and archiving
- **Filterable Reports**: Filter by date and category before exporting

### 🔐 Authentication & Security
- **Built-in Authentication**: Filament's secure user authentication system
- **Role-based Access**: Admin panel access control
- **User Profiles**: Business information and contact details

### 🎨 User Interface
- **Clean, Modern Design**: Professional interface with Tailwind CSS
- **Responsive Layout**: Fully optimized for desktop and tablet devices
- **Dark Mode Support**: Comfortable viewing in any lighting condition
- **Intuitive Navigation**: Easy-to-use admin panel with Filament

### 🗄️ Database
- **Relational Schema**: Well-structured MySQL/PostgreSQL or SQLite database
- **Data Integrity**: Foreign key constraints and cascading deletes
- **Optimized Queries**: Efficient data retrieval and calculations

## Technology Stack

### Backend
- **Laravel 11** - Modern PHP framework
- **Filament 3** - Elegant admin panel builder
- **Tailwind CSS** - Utility-first CSS framework
- **SQLite/MySQL/PostgreSQL** - Database

### Frontend
- **Livewire** - Real-time reactive components
- **Blade Templating** - Server-side templating
- **Tailwind CSS** - Responsive styling

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 16+ (for npm)
- SQLite, MySQL, or PostgreSQL

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd gcash-tracker
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Generate application key**
   ```bash
   php artisan key:generate
   ```

4. **Configure database** (optional for SQLite)
   
   Edit `.env` file if you want to use MySQL or PostgreSQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=peraly
   DB_USERNAME=root
   DB_PASSWORD=yourpassword
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed sample data** (optional)
   ```bash
   php artisan db:seed
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

9. **Access the admin panel**
   - URL: `http://localhost:8000/admin`
   - Email: `admin@peraly.com`
   - Password: `password` (from seeding, change in production)

## Project Structure

```
gcash-tracker/
├── app/
│   ├── Filament/
│   │   └── Admin/
│   │       ├── Resources/        # CRUD resources
│   │       ├── Pages/            # Admin pages & dashboard
│   │       ├── Widgets/          # Dashboard widgets
│   │       └── AdminPanelProvider.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Transaction.php
│   │   └── ...
│   └── ...
├── database/
│   ├── migrations/               # Database schemas
│   ├── seeders/                  # Sample data
│   └── database.sqlite
├── resources/
│   ├── views/                    # Blade templates
│   └── css/                      # Tailwind CSS
├── routes/
│   └── web.php
├── .env
├── .env.example
├── composer.json
├── package.json
└── vite.config.js
```

## Database Schema

### Users Table
```sql
id, name, email, password, phone_number, business_name, email_verified_at, remember_token, created_at, updated_at
```

### Categories Table
```sql
id, name, type (cash-in/cash-out), created_at, updated_at
```

### Transactions Table
```sql
id, transaction_date, type (cash-in/cash-out), amount, fee, category_id, notes, created_at, updated_at
```

## GCash Fee Calculation

The app includes automatic tiered fee calculation:

### Cash-In Fees
- Up to ₱5,000: 1%
- ₱5,001 - ₱10,000: 1.5%
- Over ₱10,000: 2%

### Cash-Out Fees
- Up to ₱5,000: 1.5%
- ₱5,001 - ₱10,000: 2%
- Over ₱10,000: 2.5%

These values can be adjusted in `app/Models/Transaction.php` under the `calculateFee()` method.

## Color Scheme

The application uses a professional color palette:
- **Primary Blue**: #4A90E2 (Sky Blue)
- **Accent Cyan**: #50E3C2 (Soft Cyan)
- **Background**: #F9FAFB (Off-White)
- **Text**: #1F2937 (Charcoal Gray)
- **Success**: #34D399 (Green) - Cash-in, Profits
- **Danger**: #F87171 (Red) - Cash-out, Losses
- **Warning**: #FBBF24 (Yellow) - Fees

## Available Commands

```bash
# Development
php artisan serve                  # Start development server
npm run dev                        # Watch CSS/JS changes

# Database
php artisan migrate                # Run migrations
php artisan db:seed                # Seed sample data
php artisan migrate:refresh        # Reset database
php artisan tinker                 # Interactive shell

# Cache & Config
php artisan config:cache           # Cache configuration
php artisan cache:clear            # Clear cache
php artisan view:cache             # Cache views

# Admin
php artisan filament:show-model    # Show Filament resources available
```

## Authentication

Default admin credentials (after seeding):
- **Email**: admin@peraly.com
- **Password**: password

⚠️ **Important**: Change these credentials immediately in production!

To reset the password:
```bash
php artisan tinker
>>> App\Models\User::first()->update(['password' => bcrypt('newpassword')])
```

## Customization

### Adding New Categories
1. Navigate to Admin Panel → Categories
2. Click "Create"
3. Enter category name and select type (Cash In / Cash Out)
4. Save

### Adjusting Fee Structures
Edit `app/Models/Transaction.php`:
```php
public static function calculateFee(float $amount, string $type): float
{
    // Modify percentages here
}
```

### Modifying Dashboard Widgets
Edit files in `app/Filament/Admin/Widgets/`:
- `StatsOverview.php` - Summary cards
- `CashFlowChart.php` - Charts
- `RecentTransactions.php` - Transaction list

### Changing Colors
Edit `app/Filament/Admin/AdminPanelProvider.php` in the `panel()` method colors configuration.

## Troubleshooting

### Database connection error
- Ensure MySQL/PostgreSQL is running
- Check `.env` database credentials
- Run `php artisan config:clear`

### "Command not found" errors
- Run `composer install`
- Run `npm install`
- Clear config: `php artisan config:clear`

### Tables not appearing in admin
- Ensure migrations ran: `php artisan migrate`
- Clear cache: `php artisan config:clear`
- Access Filament resources at: `/admin`

### Seeder errors
- Delete `database.sqlite` and re-run migrations
- Run `php artisan migrate:refresh --seed`

## Performance Tips

1. **Index frequently filtered columns**:
   ```php
   $table->index('transaction_date');
   $table->index('category_id');
   ```

2. **Use query scopes** for complex filters

3. **Enable query optimization** in production:
   ```bash
   php artisan config:cache
   php artisan view:cache
   php artisan route:cache
   ```

4. **Archive old transactions** if database grows large

## Future Enhancements

- [ ] Multi-user support with role-based access
- [ ] Recurring transaction templates
- [ ] Expense budgeting and alerts
- [ ] Tax report generation
- [ ] Bank reconciliation
- [ ] Mobile app (React Native/Flutter)
- [ ] API endpoints for mobile integration
- [ ] Email notifications for large transactions
- [ ] Transaction receipts/invoices
- [ ] Multi-currency support

## Support & Contributions

For issues, feature requests, or contributions:
1. Open an issue on GitHub
2. Submit a pull request with changes
3. Follow the coding standards and conventions

## License

This project is open-source software licensed under the MIT license.

## Credits

Built with:
- [Laravel](https://laravel.com)
- [Filament](https://filamentphp.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Livewire](https://livewire.laravel.com)

---

**Made for Filipino Small Businesses** 🇵🇭

For more information, visit the dashboard at `/admin` after installation.
