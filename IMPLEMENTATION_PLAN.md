# Peraly Cash Management System - Implementation Plan

## Overview
Upgrade Peraly from a basic transaction tracker to a complete cash-in/cash-out business accounting system with real-time balance tracking and profit management.

---

## 1. Database Schema Changes

### 1.1 Update `users` Table
Add balance tracking fields:
```sql
ALTER TABLE users ADD COLUMN (
    gcash_balance DECIMAL(12, 2) DEFAULT 0,
    cash_balance DECIMAL(12, 2) DEFAULT 0,
    default_fee_percentage DECIMAL(5, 2) DEFAULT 2.0
);
```

### 1.2 Update `transactions` Table
Add detailed balance and fee tracking:
```sql
ALTER TABLE transactions ADD COLUMN (
    fee_percentage DECIMAL(5, 2) NOT NULL DEFAULT 2.0,
    fee_amount DECIMAL(12, 2) GENERATED ALWAYS AS (
        ROUND(amount * (fee_percentage / 100), 2)
    ) STORED,
    gcash_balance_before DECIMAL(12, 2),
    gcash_balance_after DECIMAL(12, 2),
    cash_balance_before DECIMAL(12, 2),
    cash_balance_after DECIMAL(12, 2),
    profit_portion DECIMAL(12, 2) GENERATED ALWAYS AS (ROUND(amount * (fee_percentage / 100), 2)) STORED
);
```

### 1.3 Create `balance_history` Table
Track balance changes over time for auditing:
```sql
CREATE TABLE balance_history (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    transaction_id BIGINT NULLABLE,
    balance_type ENUM('gcash', 'cash', 'profit'),
    amount_before DECIMAL(12, 2),
    amount_after DECIMAL(12, 2),
    change_amount DECIMAL(12, 2),
    reason VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(id)
);
```

### 1.4 Create `profit_tracking` Table
Track cumulative profit and fees:
```sql
CREATE TABLE profit_tracking (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL UNIQUE,
    total_profit DECIMAL(12, 2) DEFAULT 0,
    profit_from_cash_in DECIMAL(12, 2) DEFAULT 0,
    profit_from_cash_out DECIMAL(12, 2) DEFAULT 0,
    transaction_count INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## 2. Model Updates

### 2.1 User Model
- Add balance relationships
- Add profit relationship
- Add methods: `addCashIn()`, `addCashOut()`, `getProfit()`, `getGCashBalance()`, `getCashBalance()`

### 2.2 Transaction Model
- Add fee calculation logic
- Add balance tracking relationships
- Add scopes: `byType()`, `profitableOnly()`, `byDateRange()`

### 2.3 New Models
- `BalanceHistory` - Track all balance changes
- `ProfitTracking` - Track cumulative profit

---

## 3. View/Filament Resource Updates

### 3.1 TransactionResource Updates
**Form Changes:**
- Add `fee_percentage` with slider or number input
- Auto-calculate `fee_amount` in real-time
- Display `gcash_balance_after` and `cash_balance_after` as read-only preview
- Add validation: amount > 0, fee_percentage >= 0 and <= 100

**Table Changes:**
- Display columns: Type, Amount, Fee %, Fee Amount, GCash Balance After, Cash Balance After
- Add color coding for transaction types
- Add badge for profit tier

### 3.2 Dashboard Updates
**Summary Cards:**
- Total Cash In (this month)
- Total Cash Out (this month)
- Total Fees/Profit (this month)
- Current GCash Balance (read-only)
- Current Cash Balance (read-only)
- Total Cumulative Profit (all time)

**Charts:**
- Cash In vs Cash Out trend (daily/weekly/monthly)
- GCash vs Cash balance trend
- Profit accumulation chart

**Widgets:**
- Recent transactions with fee info
- Balance alerts (if low)
- Quick stats on profit per transaction type

---

## 4. Service Layer (Business Logic)

### 4.1 TransactionService
Handles all transaction processing:
```php
- processTransaction(User, array data)
- calculateBalances(Transaction)
- updateUserBalances(User, array changes)
- recordBalanceHistory(Transaction)
- validateTransaction(array data)
```

### 4.2 ProfitService
Manages profit tracking:
```php
- addProfit(User, amount, type, transaction)
- getTotalProfit(User, dateRange)
- getProfitByType(User, type)
- getProfitReport(User, dateRange)
```

### 4.3 BalanceService
Manages balance operations:
```php
- getCurrentBalances(User)
- getBalanceHistory(User, dateRange)
- getBalanceTrend(User, dateRange)
- validateBalance(User, type, amount)
```

---

## 5. Business Logic Implementation

### 5.1 Cash In Transaction Flow
```
User creates transaction:
  amount = 1000
  fee_percentage = 2% (default or custom)

Fee Calculation:
  fee_amount = 1000 * 0.02 = 20

Balance Updates:
  gcash_balance_after = gcash_balance_before + 1000
  cash_balance_after = cash_balance_before + 20
  profit_portion = 20 (added to cumulative profit)

Record:
  - Save transaction with all balance fields
  - Create BalanceHistory entries for audit trail
  - Update user's cumulative profit
```

### 5.2 Cash Out Transaction Flow
```
User creates transaction:
  amount = 500
  fee_percentage = 1.5% (default or custom)

Fee Calculation:
  fee_amount = 500 * 0.01.5 = 7.50

Balance Updates:
  gcash_balance_after = gcash_balance_before - 500
  cash_balance_after = cash_balance_before - (500 - 7.50) = cash_balance_before - 492.50
  profit_portion = 7.50 (added to cumulative profit)

Record:
  - Save transaction with all balance fields
  - Create BalanceHistory entries
  - Update user's cumulative profit
```

---

## 6. Feature Details

### 6.1 Real-time Balance Updates
- All balance calculations done before transaction save
- Use database transactions to ensure consistency
- Display preview before confirmation in form

### 6.2 Fee Management
- Default fee: 2% (configurable per user)
- Allow per-transaction override
- Store both percentage and calculated amount

### 6.3 Profit Tracking
- Calculate profit per transaction
- Separate tracking for cash-in vs cash-out fees
- Cumulative profit display on dashboard
- Profit-based reports

### 6.4 Reporting Enhancements
- Include fee/profit columns
- Show balance progression
- GCash vs Cash breakdown
- Profit per transaction type
- Custom date range filtering
- Export with all balance details

---

## 7. Implementation Checklist

- [ ] Create migrations for new tables and columns
- [ ] Update User model with balance fields and methods
- [ ] Create BalanceHistory model
- [ ] Create ProfitTracking model
- [ ] Create TransactionService
- [ ] Create ProfitService
- [ ] Create BalanceService
- [ ] Update Transaction model with fee logic
- [ ] Update TransactionResource form
- [ ] Update TransactionResource table
- [ ] Create new StatsOverview widget with balance cards
- [ ] Create CashFlowChart widget with GCash/Cash trends
- [ ] Create BalanceAlert widget
- [ ] Create ProfitTracking widget
- [ ] Update DatabaseSeeder with default balances
- [ ] Add validation rules
- [ ] Test all transaction flows
- [ ] Test balance calculations
- [ ] Test reporting
- [ ] Create git commits for each major change

---

## 8. File Structure (New/Updated)

```
NEW:
  app/Services/
    ├── TransactionService.php
    ├── ProfitService.php
    └── BalanceService.php
  
  app/Models/
    ├── BalanceHistory.php (NEW)
    └── ProfitTracking.php (NEW)
  
  database/migrations/
    ├── *_add_balance_fields_to_users_table.php (NEW)
    ├── *_update_transactions_table_for_fees.php (NEW)
    ├── *_create_balance_history_table.php (NEW)
    └── *_create_profit_tracking_table.php (NEW)

UPDATED:
  app/Models/
    ├── User.php
    ├── Transaction.php
    └── Report.php
  
  app/Filament/Admin/Resources/
    └── TransactionResource.php
  
  app/Filament/Admin/Widgets/
    ├── StatsOverview.php (UPDATED)
    ├── CashFlowChart.php (UPDATED)
    └── Widgets/* (NEW balance/profit widgets)
  
  database/seeders/
    └── DatabaseSeeder.php (UPDATED)
```

---

## 9. Testing Strategy

1. Unit Tests:
   - TransactionService balance calculations
   - ProfitService profit tracking
   - BalanceService validations

2. Feature Tests:
   - Create transaction and verify balances
   - Create multiple transactions and verify cumulative profit
   - Test fee percentage variations
   - Test balance updates in dashboard
   - Test reports with fee/profit data

3. Integration Tests:
   - Full transaction workflow
   - Balance history accuracy
   - Profit tracking across multiple transactions
   - Export with balance data

---

## 10. Estimated Timeline

- Phase 1 (Migrations & Models): 2 hours
- Phase 2 (Services & Logic): 3 hours
- Phase 3 (Filament Updates): 4 hours
- Phase 4 (Dashboard & Widgets): 3 hours
- Phase 5 (Testing & Refinement): 2 hours
- **Total: ~14 hours**

---

## 11. Success Criteria

✅ Transactions calculate fees correctly
✅ Balances update in real-time
✅ Profit tracking is accurate
✅ Dashboard shows current balances and profit
✅ Reports include fee and profit data
✅ Balance history is maintained
✅ No orphaned or inconsistent data
✅ All validations work
✅ User experience is smooth and intuitive
