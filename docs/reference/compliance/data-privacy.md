---
title: Data Privacy & GDPR Alignment
diataxis: reference
standards:
  - GDPR
owner: Project Lead
update_frequency: quarterly
classification: mandatory
---

# Data Privacy & GDPR Alignment

## Personal Data Inventory

| Data Category | Collected | Purpose | Retention |
|---|---|---|---|
| Name | Users, Customers | Identification | Indefinite (until deletion request) |
| Email | Users | Login, notifications | Indefinite |
| Phone | Users, Customers, Wholesale | Contact, shipping | Indefinite |
| Address | Customers, Wholesale | Shipping | Indefinite |
| Transaction history | Transactions | Records, reporting | 10 years (tax) |
| IP address | Session logs | Security | 30 days |
| Browser user-agent | Session logs | Security | 30 days |

## Data Subject Rights

### Access Request
- User can view their data via profile/download endpoint
- Admin can export any user's data on request

### Deletion Request
- Soft delete immediately (can_login = false, scope hidden)
- Hard delete scheduled after 90-day retention
- Transaction data retained for legal/tax requirements (anonymized after deletion)

### Data Portability
- Users can export: profile data, transaction history
- Format: CSV, JSON
- Timeframe: Completed within 30 days

## Data Processing Register

| Processing Activity | Legal Basis | Data Categories | Third-party Sharing |
|---|---|---|---|
| Order fulfillment | Contract execution | Name, address, phone | Courier (RajaOngkir) |
| Payment processing | Contract execution | Transaction amount | N/A (in-house) |
| Marketing (future) | Consent | Email | None |
| Analytics | Legitimate interest | Aggregated/ anonymized | None |
