---
title: Folder Structure
diataxis: reference
standards:
  - arc42 §5
owner: Staff Software Engineer
update_frequency: on-change
classification: mandatory
---

# Folder Structure

```
app/
├── Console/
│   └── Commands/               # Artisan commands
├── Events/                     # Event classes
├── Exceptions/                 # Custom exception classes
├── Http/
│   ├── Controllers/
│   │   ├── Admin/              # Admin-specific controllers
│   │   └── Auth/               # Authentication controllers
│   ├── Middleware/              # Custom middleware
│   ├── Requests/               # Form request validation classes
│   └── Resources/              # API resource transformers
├── Listeners/                  # Event listeners
├── Mail/                       # Mailable classes
├── Models/                     # Eloquent models
├── Notifications/              # Notification classes
├── Policies/                   # Authorization policies
├── Providers/                  # Service providers
├── Rules/                      # Custom validation rules
├── Services/                   # Business logic services
├── Traits/                     # Shared traits
└── View/
    └── Components/             # Blade components

bootstrap/                      # Framework bootstrap files
config/                         # Configuration files
database/
├── factories/                  # Model factories
├── migrations/                 # Database migrations
└── seeders/                    # Database seeders
docs/                           # Documentation (this structure)
lang/                           # Language files (id, en)
public/                         # Web server document root
├── build/                      # Compiled assets (Vite)
└── storage/                    # Symlink to storage/app/public
resources/
├── views/
│   ├── dashboard/              # Dashboard views
│   ├── owner/                  # Owner-specific views
│   ├── wholesale/              # Wholesale operations views
│   │   ├── customer/           #   Customer portal views
│   ├── layouts/                # Layout templates
│   ├── products/               # Product CRUD views
│   ├── transactions/           # Transaction views
│   ├── settings/               # Settings views
│   └── ...                     # Other domain views
├── css/                        # CSS source files
└── js/                         # JS source files
routes/
├── web.php                     # Web routes
├── auth.php                    # Authentication routes
├── api.php                     # API routes
└── channels.php                # Broadcasting channels
storage/
├── app/public/                 # Public storage (images, uploads)
├── framework/                  # Framework cache, sessions, views
└── logs/                       # Application logs
tests/
├── Unit/                       # Unit tests
├── Feature/                    # Feature/integration tests
└── Browser/                    # Dusk browser tests
```
