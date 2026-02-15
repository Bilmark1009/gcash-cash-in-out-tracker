# Installation Checklist

Use this checklist to ensure Peraly is properly installed and configured.

## Pre-Installation

- [ ] PHP 8.2+ installed (`php --version`)
- [ ] Composer installed (`composer --version`)
- [ ] Node.js/npm installed (`npm --version`)
- [ ] Git installed (for version control)

## Installation

- [ ] Project directory created
- [ ] Dependencies installed (`composer install`)
- [ ] JavaScript packages installed (`npm install`)
- [ ] Application key generated (`php artisan key:generate`)
- [ ] `.env` file configured
- [ ] Database connection verified
- [ ] Migrations run (`php artisan migrate`)
- [ ] Sample data seeded (`php artisan db:seed`)

## Verification

### Application
- [ ] `php artisan serve` starts without errors
- [ ] Access `http://localhost:8000` - Laravel welcome page loads
- [ ] Access `http://localhost:8000/admin` - Filament login page loads

### Admin Panel
- [ ] Login succeeds with `admin@peraly.com` / `password`
- [ ] Dashboard displays without errors
- [ ] Summary cards show financial data
- [ ] Cash flow chart renders
- [ ] Recent transactions list appears

### Transactions Resource
- [ ] Can view all transactions
- [ ] Can create new transaction
- [ ] Fee calculation works (check when saving)
- [ ] Can edit existing transaction
- [ ] Can delete transaction
- [ ] Filters work (date, category, type)

### Categories Resource
- [ ] Can view all categories
- [ ] Can create new category
- [ ] Category type selection works (Cash In/Out)
- [ ] Can edit category
- [ ] Can delete category
- [ ] Categories appear in transaction selector

### Reports Resource
- [ ] Can view all reports
- [ ] Can create new report
- [ ] Report calculations work
- [ ] Can view report details
- [ ] Can delete report

### Database
- [ ] `database/database.sqlite` file exists (SQLite)
- [ ] Or database connection works (MySQL/PostgreSQL)
- [ ] All tables created:
  - [ ] users
  - [ ] categories
  - [ ] transactions
  - [ ] reports
  - [ ] migrations

## Post-Installation

### Security
- [ ] Change admin password in database or profile
- [ ] Set `APP_DEBUG=false` if deploying
- [ ] Generate new `APP_KEY`
- [ ] Configure `.env` for production

### Configuration
- [ ] App timezone set (optional: `APP_TIMEZONE=Asia/Manila`)
- [ ] Email configured (optional)
- [ ] Session storage configured

### Performance (Optional)
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `npm run build`

## Sample Data Verification

After seeding, verify:
- [ ] Admin user exists (`admin@peraly.com`)
- [ ] 10 categories created (5 cash-in, 5 cash-out)
- [ ] 15+ cash-in transactions exist
- [ ] 12+ cash-out transactions exist
- [ ] Transactions have calculated fees
- [ ] Dashboard shows aggregated data

## Common Issues & Solutions

### Issue: `php artisan serve` fails
**Solutions:**
- [ ] Run `php artisan config:clear`
- [ ] Run `composer install`
- [ ] Check PHP version: `php -v`
- [ ] Check for port conflicts

### Issue: Login fails
**Solutions:**
- [ ] Run `php artisan db:seed` to create admin user
- [ ] Verify database connection
- [ ] Check `.env` `SESSION_DRIVER=database`
- [ ] Clear browser cookies

### Issue: Dashboard shows no data
**Solutions:**
- [ ] Run `php artisan migrate`
- [ ] Run `php artisan db:seed`
- [ ] Refresh page (Ctrl+Shift+R)
- [ ] Check browser console for errors

### Issue: Resources don't appear in admin
**Solutions:**
- [ ] Run `php artisan config:clear`
- [ ] Verify migration completed: `php artisan migrate:status`
- [ ] Check tables exist in database

### Issue: File permission errors
**Solutions:**
- [ ] Run `chmod -R 775 storage`
- [ ] Run `chmod -R 775 bootstrap/cache`
- [ ] Run `chmod 755 artisan`

## Features Checklist

### Transaction Management
- [ ] Create transactions
- [ ] Auto-calculate GCash fees
- [ ] Edit transaction records
- [ ] Delete transactions
- [ ] Filter by date range
- [ ] Filter by category
- [ ] Filter by type (cash-in/out)
- [ ] Add notes to transactions
- [ ] View transaction details

### Category Management
- [ ] View all categories
- [ ] Create new categories
- [ ] Assign type (cash-in/out)
- [ ] Edit categories
- [ ] Delete categories
- [ ] Categories available in transaction form

### Dashboard Analytics
- [ ] Total Cash In card displays
- [ ] Total Cash Out card displays
- [ ] Total Fees card displays
- [ ] Net Profit/Loss card displays
- [ ] Cards show monthly data
- [ ] Color coding works (green/red)
- [ ] Cash flow chart renders
- [ ] Chart shows 30-day trends
- [ ] Recent transactions widget shows

### Reports
- [ ] Generate daily reports
- [ ] Generate weekly reports
- [ ] Generate monthly reports
- [ ] Report calculations accurate
- [ ] View report details
- [ ] List all reports
- [ ] Filter reports by period

## Documentation Checklist

- [ ] README.md completed and accurate
- [ ] QUICKSTART.md clear and helpful
- [ ] SETUP.md detailed and complete
- [ ] DEPLOYMENT.md covers deployment options
- [ ] Code commented where needed
- [ ] Migration files documented
- [ ] Model relationships clear

## Final Quality Checks

- [ ] No console errors when browsing admin
- [ ] No database errors in Laravel logs
- [ ] All form validations working
- [ ] All buttons responsive and functional
- [ ] Mobile view is readable (tablet)
- [ ] Colors match brand palette
- [ ] Typography is readable
- [ ] Loading states work
- [ ] Success messages appear
- [ ] Error messages are helpful

## Deployment Prep

- [ ] `.env.example` matches production needs
- [ ] Database backups working
- [ ] Logging configured
- [ ] SSL certificate ready (if needed)
- [ ] Server resources sufficient
- [ ] Database migrations tested on fresh environment
- [ ] Seeding tested on fresh environment

## User Acceptance Testing

- [ ] Stakeholder can login
- [ ] Dashboard is understandable
- [ ] Transaction creation is intuitive
- [ ] Report generation works
- [ ] Data accuracy verified
- [ ] Performance is acceptable
- [ ] No missing features identified

## Sign-Off

- [ ] Development complete
- [ ] Testing complete
- [ ] Documentation complete
- [ ] Ready for production
- [ ] Team trained on usage

---

**Installation Date:** ___________

**Verified By:** ___________

**Notes:**
```
_____________________________________________________
_____________________________________________________
_____________________________________________________
```

---

Congratulations! Your Peraly GCash Tracker is ready to use! 🎉
