---
title: Service Layer Reference
diataxis: reference
standards:
  - arc42 §5
owner: Staff Software Engineer
update_frequency: on-change
classification: mandatory
---

# Service Layer Reference

## Pattern

Services encapsulate business logic that operates across multiple models or repositories. They are injected into controllers via constructor injection.

## Convention

```
app/Services/
├── Ai/                       # AI/ML services
│   └── CopilotIntents/       # Intent handlers for co-pilot feature
├── Contracts/                # Service interfaces
├── Security/                 # Security-domain services
└── {Domain}Service.php
```

- Service classes are named after the domain they operate on
- Methods return typed results or throw typed exceptions
- Services may depend on other Services
- Services do NOT depend on HTTP concerns (Request, Response, Session)

## Service Inventory

### Business Services

| Service | Responsibility |
|---|---|
| `WholesaleLoyaltyService` | Credit calculation, rank management, redemptions |
| `SmartInsightService` | Dashboard insights (top seller, sales growth, peak hours) |
| `AiStrategicService` | Strategic advice (selling advice, cross-sell, stock optimization, pricing) |
| `AiCopilotService` | Claude API-powered co-pilot with tool execution |
| `RuleBasedCopilotEngine` | Rule-based fallback for co-pilot (fuzzy intent matching) |
| `ApmsKnowledgeBase` | Static knowledge base for AI responses |

### Security Services

| Service | Responsibility |
|---|---|
| `ActivityMonitor` | Login attempt tracking, suspicious login detection, active session count |
| `BackupService` | Database backup creation, restore, listing, cleanup |
| `RbacService` | Role/permission sync, gate registration, permission checks |
| `EncryptionService` | Field encryption/decryption for PII data |
| `DataIntegrityService` | Transaction checksum verification, anomaly scanning, integrity score |
| `LogViewerService` | Application log parsing, level filtering, error rate stats |
| `FileUploadSecurityService` | File upload validation (image content, PHP code detection) |
| `SecurityAlertService` | Security event notifications (suspicious login, account lock, brute force) |
| `PosAntiTamperingService` | Cart validation, stock deduction verification, anti-tampering |

### Copilot Intent Handlers (app/Services/CopilotIntents/)

| Handler | Purpose |
|---|---|
| `BestSellingHandler` | Best-selling products query |
| `SalesSummaryHandler` | Sales summary query |
| `ProfitLossHandler` | Profit/loss query |
| `IncomingStockHandler` | Incoming stock query |
| `CriticalStockHandler` | Critical/out-of-stock query |
| `StockSummaryHandler` | Stock summary query |
| `BranchInfoHandler` | Branch information query |
| `CustomerCountHandler` | Customer count query |
| `CustomerOriginHandler` | Customer origin query |
| `EmployeeInfoHandler` | Employee information query |
| `ShiftStatusHandler` | Shift status query |
| `ActivePromosHandler` | Active promotions query |
| `AttendanceHandler` | Attendance information query |
| `WholesaleOrderHandler` | Wholesale order status query |
| `DailyRecapHandler` | Daily recap query |
| `ExpenseHandler` | Expense analytics query |

## Example Usage

```php
class TransactionController extends Controller
{
    public function __construct(
        private WholesaleLoyaltyService $loyaltyService
    ) {}

    public function store(StoreTransactionRequest $request)
    {
        $result = $this->loyaltyService->earnCredits(
            $request->user(),
            $request->validated()
        );
        return response()->json($result, 201);
    }
}
```
