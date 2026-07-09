---
title: Reporting & Analytics Approach
diataxis: explanation
owner: Project Lead
update_frequency: quarterly
classification: recommended
---

# Reporting & Analytics Approach

## Why Built-in Reporting Instead of BI Tool?

| Factor | Built-in (Laravel) | External BI (Metabase, Tableau) |
|---|---|---|
| Cost | Free (existing stack) | $2k-15k/year |
| Maintenance | Simple (SQL queries) | Separate server + ETL |
| Real-time | Direct DB access | Latency from ETL |
| Customization | Full control | Tool limitations |
| Access control | Role middleware | Separate auth |
| Mobile access | Responsive web | App dependency |

Built-in reporting covers the current needs (daily sales, inventory reports, commission calculations). If the business grows to require self-service analytics, Metabase can be added as a read-replica consumer.

## Report Types

| Report | Frequency | Purpose |
|---|---|---|
| Daily Sales Summary | Daily at 23:00 | Cashier reconciliation, daily revenue |
| Monthly Revenue | Monthly | P&L, tax preparation |
| Inventory Valuation | Weekly | Stock value, shrinkage detection |
| Low Stock Alerts | Hourly | Reorder triggers |
| Commission Report | Monthly | Employee commission calculation |
| Wholesale Order Summary | Daily | B2B order fulfillment tracking |
| Expense Report | Monthly | Cost center analysis |
| Profit Margin | Monthly | Product profitability |

## Why Smart Insights?

Smart Insights uses simple trend analysis (not ML) to highlight:
- Unusual sales patterns (e.g., a product selling 3x normal)
- Slow-moving inventory
- Peak hours for staffing decisions
- Customer buying patterns

The goal is surfacing actionable information without requiring the owner to dig through spreadsheets.
