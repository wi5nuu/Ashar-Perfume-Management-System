# 📦 Product Backlog — APMS (Ashar Grosir Perfume Management System)

> **Last Updated:** April 19, 2026  
> **Product Owner:** Ashar  
> **Total Backlog Items:** 12  
> **Priority Scale:** 🔴 High | 🟡 Medium | 🟢 Low  

---

## 📊 Backlog Overview

| ID | Feature | Priority | Status | Story Points |
|----|---------|----------|--------|--------------|
| PB-01 | Authentication & Role-Based Access | 🔴 High | ✅ Done | 8 |
| PB-02 | Product & Category Management | 🔴 High | ✅ Done | 13 |
| PB-03 | Point of Sale (POS) Transaction System | 🔴 High | ✅ Done | 21 |
| PB-04 | Inventory & Stock Management | 🔴 High | ✅ Done | 13 |
| PB-05 | Financial Reports & Analytics Dashboard | 🔴 High | ✅ Done | 13 |
| PB-06 | Wholesale Order Management | 🟡 Medium | ✅ Done | 8 |
| PB-07 | Employee, Shift & Attendance Tracking | 🟡 Medium | ✅ Done | 8 |
| PB-08 | Customer Loyalty & Debt Management | 🟡 Medium | ✅ Done | 5 |
| PB-09 | Expense Tracking & Coupon System | 🟡 Medium | ✅ Done | 5 |
| PB-10 | AI Assistant (Offline Sales Advisor) | 🟡 Medium | ✅ Done | 13 |
| PB-11 | Mobile Responsive UI (All Devices) | 🟡 Medium | 🔄 In Progress | 5 |
| PB-12 | System Settings & Store Configuration | 🟢 Low | ✅ Done | 3 |

---

## 📋 Detailed Product Backlog Stories

---

### PB-01 — Authentication & Role-Based Access
**Priority:** 🔴 High | **Story Points:** 8 | **Status:** ✅ Done

**User Story:**  
*"As a store owner, I want to log in securely and control what each staff role (owner, cashier, admin) can access, so that sensitive data is protected."*

**Goal:**  
Implement a secure multi-role authentication system with access control per user role.

**Features:**
- [x] Login page with validation
- [x] Role-based middleware (Owner, Admin, Cashier)
- [x] Login activity log (tracks IP, time, device)
- [x] Session management & logout
- [x] Password management via profile settings

**Acceptance Criteria:**
- Owner can access all menus
- Cashier only sees POS and daily shift
- Admin sees reports and inventory
- Failed login attempts are recorded

---

### PB-02 — Product & Category Management
**Priority:** 🔴 High | **Story Points:** 13 | **Status:** ✅ Done

**User Story:**  
*"As a store owner, I want to manage my perfume catalog with categories, sizes, and prices, so that all products are organized and easy to sell."*

**Goal:**  
Build a full CRUD product management system with image upload, category color-coding, and size variants.

**Features:**
- [x] Product CRUD (Create, Read, Update, Delete)
- [x] Product categories with color labels
- [x] Product image upload
- [x] Multi-size support (50ml, 100ml, etc.)
- [x] Purchase price & selling price management
- [x] Stock quantity tracking per product

**Acceptance Criteria:**
- Products searchable and filterable by category
- Images display correctly in POS view
- Low stock items are flagged

---

### PB-03 — Point of Sale (POS) Transaction System
**Priority:** 🔴 High | **Story Points:** 21 | **Status:** ✅ Done

**User Story:**  
*"As a cashier, I want to process sales quickly using a POS interface, so that customers are served efficiently and transactions are recorded accurately."*

**Goal:**  
Implement a full-featured POS system with payment options, receipt printing, and discount/coupon support.

**Features:**
- [x] Real-time cart system
- [x] Multiple payment methods (Cash, Transfer, E-Wallet)
- [x] Tax toggle (PPN 11%)
- [x] Coupon / discount code application
- [x] Loyalty points redemption
- [x] Bonus item feature (buy X get Y free)
- [x] Print receipt / struk
- [x] Transaction history with filters

**Acceptance Criteria:**
- Transaction completes in under 2 minutes
- Change calculation is automatic for cash
- Receipt shows correct totals after discounts/tax

---

### PB-04 — Inventory & Stock Management
**Priority:** 🔴 High | **Story Points:** 13 | **Status:** ✅ Done

**User Story:**  
*"As a store owner, I want to track stock levels and receive alerts for low inventory, so that I never run out of popular products."*

**Goal:**  
Create a real-time inventory tracking system with stock audit and supplier management.

**Features:**
- [x] Real-time stock level monitoring
- [x] Stock in / stock out logging
- [x] Stock audit (physical count reconciliation)
- [x] Supplier management
- [x] Low stock threshold alerts
- [x] Inventory history / movement log

**Acceptance Criteria:**
- Stock updates automatically after each sale
- Stock audit detects discrepancies
- Supplier list linked to products

---

### PB-05 — Financial Reports & Analytics Dashboard
**Priority:** 🔴 High | **Story Points:** 13 | **Status:** ✅ Done

**User Story:**  
*"As a store owner, I want to see my daily, weekly, and monthly revenue and profit reports, so that I can make informed business decisions."*

**Goal:**  
Build a comprehensive reporting module with charts, filters, and exportable data.

**Features:**
- [x] Revenue & profit summary (daily/weekly/monthly)
- [x] Top-selling products chart
- [x] Sales by payment method breakdown
- [x] Expense vs revenue comparison
- [x] Dashboard KPI cards (total sales, orders, customers)
- [x] Date range filtering for all reports

**Acceptance Criteria:**
- Dashboard loads within 3 seconds
- Charts display correct data from database
- Reports reflect real-time transactions

---

### PB-06 — Wholesale Order Management
**Priority:** 🟡 Medium | **Story Points:** 8 | **Status:** ✅ Done

**User Story:**  
*"As a store owner, I want to manage bulk/wholesale orders separately from retail sales, so that B2B customers are tracked with their own pricing."*

**Goal:**  
Implement a wholesale module with custom pricing, delivery, and packing cost tracking.

**Features:**
- [x] Wholesale order CRUD
- [x] Wholesale customer management
- [x] Custom wholesale pricing per product
- [x] Packing cost & delivery fee input
- [x] Order status tracking (pending, confirmed, delivered)
- [x] Wholesale order detail view

**Acceptance Criteria:**
- Wholesale orders don't affect retail stock until confirmed
- Separate revenue tracking for wholesale vs retail

---

### PB-07 — Employee, Shift & Attendance Tracking
**Priority:** 🟡 Medium | **Story Points:** 8 | **Status:** ✅ Done

**User Story:**  
*"As a store owner, I want to track my employees' shifts and attendance, so that I can manage payroll and ensure store coverage."*

**Goal:**  
Build an HR-lite module for shift scheduling, attendance logs, and cash closing.

**Features:**
- [x] Employee profile management
- [x] Shift open / close workflow
- [x] Daily attendance logging (time in/out)
- [x] Attendance status (present, absent, late, leave)
- [x] Shift closing photo upload
- [x] Owner vs cashier attendance separation

**Acceptance Criteria:**
- Only one active shift allowed at a time
- Shift close requires photo confirmation
- Attendance records filterable by date and employee

---

### PB-08 — Customer Loyalty & Debt Management
**Priority:** 🟡 Medium | **Story Points:** 5 | **Status:** ✅ Done

**User Story:**  
*"As a store owner, I want to reward loyal customers with points and track customer debts, so that I can build long-term relationships and manage credit sales."*

**Goal:**  
Implement customer loyalty points system and credit/debt tracking with payment history.

**Features:**
- [x] Customer CRUD with profile data
- [x] Loyalty points accumulation per purchase
- [x] Points redemption at checkout
- [x] Debt recording for credit sales
- [x] Debt payment logging with history
- [x] Customer purchase history

**Acceptance Criteria:**
- Points balance updates after each purchase
- Debt balance decreases after payment recording
- Customer list shows outstanding debt amount

---

### PB-09 — Expense Tracking & Coupon System
**Priority:** 🟡 Medium | **Story Points:** 5 | **Status:** ✅ Done

**User Story:**  
*"As a store owner, I want to log operational expenses and create discount coupons for promotions, so that I can track spending and run sales campaigns effectively."*

**Goal:**  
Create expense management with categories and a coupon/promo code system.

**Features:**
- [x] Expense CRUD with categories
- [x] Expense logged by staff member
- [x] Coupon code generation
- [x] Coupon type (percentage / fixed discount)
- [x] Coupon validity period & usage limit
- [x] Coupon application at POS

**Acceptance Criteria:**
- Expenses appear in financial report calculations
- Expired coupons are auto-rejected at checkout
- Coupon usage count tracked correctly

---

### PB-10 — AI Assistant (Offline Sales Advisor)
**Priority:** 🟡 Medium | **Story Points:** 13 | **Status:** ✅ Done

**User Story:**  
*"As a cashier, I want an AI assistant that can answer customer questions about perfume scents and recommend products, so that I can provide better service even without extensive perfume knowledge."*

**Goal:**  
Embed an offline-capable AI assistant that gives perfume recommendations and business insights.

**Features:**
- [x] Offline AI model integration (no internet needed)
- [x] Perfume scent recommendation engine
- [x] Sales analytics insights (AI-generated summaries)
- [x] Chat interface within dashboard
- [x] Context-aware responses based on inventory data

**Acceptance Criteria:**
- AI responds within 5 seconds without internet
- Recommendations based on available stock
- Chat history preserved per session

---

### PB-11 — Mobile Responsive UI (All Devices)
**Priority:** 🟡 Medium | **Story Points:** 5 | **Status:** 🔄 In Progress

**User Story:**  
*"As a cashier using a tablet or phone, I want the application to look and work correctly on smaller screens, so that I can operate the POS from any device in the store."*

**Goal:**  
Ensure all pages are fully responsive across mobile, tablet, and desktop screen sizes.

**Features:**
- [x] Login page — mobile centered ✅
- [x] Dashboard layout — responsive grid ✅
- [ ] POS transaction page — mobile optimized
- [ ] Product list — card/grid view on mobile
- [ ] Reports — scrollable tables on small screens
- [ ] Sidebar navigation — collapsible hamburger menu

**Acceptance Criteria:**
- No horizontal scrolling on 375px screen width
- All buttons meet 44x44px touch target size
- Forms are usable without zooming

---

### PB-12 — System Settings & Store Configuration
**Priority:** 🟢 Low | **Story Points:** 3 | **Status:** ✅ Done

**User Story:**  
*"As a store owner, I want to configure store name, logo, tax settings, and other system preferences, so that the application reflects my brand identity."*

**Goal:**  
Build a settings module for store configuration, appearance, and operational defaults.

**Features:**
- [x] Store name & logo upload
- [x] Tax rate toggle (enable/disable PPN 11%)
- [x] Receipt header/footer customization
- [x] User profile editing
- [x] Currency format settings

**Acceptance Criteria:**
- Changes in settings reflect immediately in POS receipts
- Logo appears on login page and dashboard header

---

## 📈 Sprint Velocity

| Sprint | Points Planned | Points Completed | Velocity |
|--------|---------------|-----------------|---------|
| Sprint 1 (Week 1) | 100 | 92 | 92% |
| Sprint 2 (Week 2) | 8 | — | — |

---

*Generated: April 19, 2026 | APMS Project Documentation*
