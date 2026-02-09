# PeShop — E-Commerce Platform Architecture v2

## Assumptions & Configuration Defaults

| Decision | Value |
|---|---|
| Default currency | EUR (configurable via `settings` table) |
| Default country | GR (Greece) |
| Default locale | el_GR (configurable) |
| Tax model | EU VAT — single rate, default 24% (GR standard) |
| Price display | Configurable: gross (VAT-inclusive) or net (VAT-exclusive) |
| Language | English UI (i18n-ready structure) |
| Shipping | Flat rate + free-shipping threshold |
| Stripe approach | **Phase 1: Checkout Sessions (redirect)**. Phase 2: embedded Payment Intents |
| Stock model | **Reservation-based** — reserve on checkout, decrement on payment_succeeded webhook, cron releases expired reservations |
| Image storage | Local disk (`storage/app/public`) with symlink |
| Email | Synchronous SMTP. Order confirmation sent **only from webhook** (idempotent) |
| Sessions | Database driver |
| Cache | File driver |
| Queue | Sync (no workers) |
| Multi-vendor | No — single store |
| Auth | Laravel Breeze (Blade) for customers, Filament for admin |
| Cron | `schedule:run` — sitemap, expired reservation cleanup, waitlist notifications |

---

## 1. System Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────┐
│                    BROWSER                           │
│  HTML/CSS (Tailwind) + Alpine.js + Livewire (sparse)│
└──────────────┬──────────────────────────────────────┘
               │  HTTPS
┌──────────────▼──────────────────────────────────────┐
│              APACHE / LitSpeed (cPanel)              │
│              public/index.php                        │
├─────────────────────────────────────────────────────┤
│                  LARAVEL 11                           │
│                                                      │
│  ┌─────────┐ ┌──────────┐ ┌────────────┐            │
│  │  Blade  │ │ Livewire │ │  Filament  │            │
│  │  Views  │ │Components│ │   Admin    │            │
│  └────┬────┘ └────┬─────┘ └─────┬──────┘            │
│       │           │              │                   │
│  ┌────▼───────────▼──────────────▼──────┐            │
│  │         SERVICE LAYER                 │            │
│  │  CartService, CheckoutService,        │            │
│  │  SearchService, OrderService,         │            │
│  │  ImageService, StripeService,         │            │
│  │  VatService, StockReservationService  │            │
│  └────────────────┬─────────────────────┘            │
│                   │                                  │
│  ┌────────────────▼─────────────────────┐            │
│  │         ELOQUENT MODELS               │            │
│  │  Product, Variant, Category, Brand,   │            │
│  │  Order, OrderItem, Cart, User, etc.   │            │
│  └────────────────┬─────────────────────┘            │
├───────────────────▼─────────────────────────────────┤
│              MySQL / MariaDB                         │
│         (FULLTEXT indexes for search)                │
└─────────────────────────────────────────────────────┘
```

### Key Architectural Decisions

**No queues — synchronous email with safety net.** Emails sent synchronously
via `sync` driver. Order confirmation email is sent ONLY from the Stripe
webhook handler (idempotent — checks if already sent). If SMTP fails, the
error is logged and stored in `pending_emails` for cron retry.

**No Redis — database sessions & file cache.** Session driver = `database`.
Cache driver = `file`. Rate limiting via database cache.

**Livewire used sparingly.** Only for: cart drawer/counter update, product
filters + AJAX pagination, add-to-cart on product page, waitlist signup.
Everything else is standard Blade with full page loads.

**Filament v3 for admin.** Separate `/admin` panel with its own auth guard.

**Image optimization.** On upload, generate 3 sizes (thumb 150px, medium
600px, large 1200px) using Intervention Image. Serve with `<picture>` and
lazy loading. No cloud dependency.

**EU VAT model.** Single VAT rate stored in settings (default 24% for GR).
A `prices_include_vat` setting controls display logic:
- If `true` (default): prices in DB are gross, VAT is extracted for invoices
- If `false`: prices in DB are net, VAT is added at display/checkout

**Stripe Checkout Sessions (Phase 1).** Redirect-based flow. Simpler, more
reliable on shared hosting. No JS payment form to maintain. Stripe handles
the entire payment UI, 3DS, and card validation.

**Reservation-based stock.** Stock is NOT decremented when checkout begins.
Instead, items are "reserved" (reserved_qty incremented + reserved_until
timestamp set). On `checkout.session.completed` webhook, stock is actually
decremented and reservation cleared. A cron job runs every 5 minutes to
release expired reservations (default TTL: 30 minutes).

### Directory Structure

```
app/
├── Models/           # Eloquent models
├── Services/         # CartService, CheckoutService, SearchService, etc.
├── Http/
│   ├── Controllers/
│   │   ├── Shop/     # ProductController, CategoryController, CartController
│   │   ├── Checkout/ # CheckoutController, StripeWebhookController
│   │   ├── Blog/     # PostController
│   │   └── Account/  # AccountController, OrderHistoryController
│   ├── Middleware/
│   └── Requests/     # Form request validation
├── Filament/         # Admin panel resources
├── Mail/             # Mailable classes
├── Observers/        # Model observers (cache busting)
├── Enums/            # OrderStatus, PaymentStatus
└── View/
    └── Components/   # Blade components
```

---

## 2. Database Schema

### Entity Relationship Overview

```
categories (self-referencing parent_id)
    └── category_product (pivot)
            └── products
                  ├── brand_id → brands
                  ├── label_product (pivot → labels)
                  ├── product_images
                  ├── product_variants
                  │     └── product_variant_attribute_value (pivot)
                  ├── product_associations (frequently bought together)
                  └── waitlist_entries

attributes ("Size", "Color")
    └── attribute_values ("XL", "Red", hex "#FF0000")

users
    ├── addresses
    ├── orders
    │     ├── order_items
    │     └── order_status_history
    └── carts
          └── cart_items

blog_posts
    ├── blog_category_post (pivot → blog_categories)
    └── blog_post_tag (pivot → blog_tags)

settings (key-value for site config)
pending_emails (retry table)
```

### Core Tables

#### `settings`
| Column | Type | Notes |
|---|---|---|
| key | VARCHAR(100) PK | |
| value | TEXT | |

**Default rows:**
- `currency` → `EUR`
- `currency_symbol` → `€`
- `currency_position` → `right` (e.g. "19,99 €")
- `country` → `GR`
- `locale` → `el_GR`
- `vat_rate` → `24.00`
- `prices_include_vat` → `true`
- `free_shipping_threshold` → `50.00`
- `flat_shipping_rate` → `3.50`
- `low_stock_threshold` → `5`
- `reservation_ttl_minutes` → `30`
- `site_name` → `PeShop`

#### `categories`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| parent_id | BIGINT UNSIGNED NULL FK self | |
| name | VARCHAR(255) | |
| slug | VARCHAR(255) UNIQUE | |
| description | TEXT NULL | |
| image_path | VARCHAR(500) NULL | |
| sort_order | INT UNSIGNED DEFAULT 0 | |
| is_visible | BOOLEAN DEFAULT true | |
| meta_title | VARCHAR(255) NULL | |
| meta_description | VARCHAR(500) NULL | |
| created_at, updated_at | TIMESTAMPS | |

**Indexes:** `UNIQUE(slug)`, `(parent_id, is_visible, sort_order)`

#### `brands`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| name | VARCHAR(255) | |
| slug | VARCHAR(255) UNIQUE | |
| logo_path | VARCHAR(500) NULL | |
| is_visible | BOOLEAN DEFAULT true | |
| created_at, updated_at | TIMESTAMPS | |

#### `labels`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| name | VARCHAR(255) | "New", "Sale", "Best Seller" |
| slug | VARCHAR(255) UNIQUE | |
| color | VARCHAR(7) | hex for badge |
| created_at, updated_at | TIMESTAMPS | |

#### `products`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| brand_id | BIGINT UNSIGNED NULL FK | |
| name | VARCHAR(255) | |
| slug | VARCHAR(255) UNIQUE | |
| sku | VARCHAR(100) NULL UNIQUE | base SKU |
| description | TEXT | WYSIWYG content |
| short_description | VARCHAR(500) NULL | |
| price | DECIMAL(10,2) | base price (gross or net per setting) |
| compare_price | DECIMAL(10,2) NULL | strike-through |
| cost | DECIMAL(10,2) NULL | for margin tracking |
| stock | INT UNSIGNED DEFAULT 0 | for simple (non-variant) products |
| reserved_stock | INT UNSIGNED DEFAULT 0 | currently reserved qty |
| low_stock_threshold | INT UNSIGNED DEFAULT 5 | |
| has_variants | BOOLEAN DEFAULT false | |
| is_active | BOOLEAN DEFAULT true | |
| is_featured | BOOLEAN DEFAULT false | |
| weight | DECIMAL(8,2) NULL | grams |
| meta_title | VARCHAR(255) NULL | |
| meta_description | VARCHAR(500) NULL | |
| published_at | TIMESTAMP NULL | |
| created_at, updated_at | TIMESTAMPS | |
| deleted_at | TIMESTAMP NULL | soft delete |

**Indexes:**
- `FULLTEXT(name, short_description, description)` — main search
- `FULLTEXT(name)` — name-only for boosting
- `UNIQUE(slug)`
- `UNIQUE(sku)` — where not null
- `(is_active, is_featured, published_at)`
- `(brand_id)`
- `(price)`

#### `product_images`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| product_id | BIGINT UNSIGNED FK | |
| path_large | VARCHAR(500) | 1200px |
| path_medium | VARCHAR(500) | 600px |
| path_thumb | VARCHAR(500) | 150px |
| alt_text | VARCHAR(255) | required for a11y |
| sort_order | INT UNSIGNED DEFAULT 0 | |
| created_at, updated_at | TIMESTAMPS | |

#### `attributes`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| name | VARCHAR(255) | "Size", "Color" |
| type | ENUM('select','color') | rendering hint |
| created_at, updated_at | TIMESTAMPS | |

#### `attribute_values`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| attribute_id | BIGINT UNSIGNED FK | |
| value | VARCHAR(255) | "XL", "Red" |
| color_hex | VARCHAR(7) NULL | for color swatches |
| sort_order | INT UNSIGNED DEFAULT 0 | |
| created_at, updated_at | TIMESTAMPS | |

#### `product_variants`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| product_id | BIGINT UNSIGNED FK | |
| sku | VARCHAR(100) UNIQUE | |
| price | DECIMAL(10,2) NULL | NULL = use parent price |
| stock | INT UNSIGNED DEFAULT 0 | |
| reserved_stock | INT UNSIGNED DEFAULT 0 | currently reserved |
| is_active | BOOLEAN DEFAULT true | |
| created_at, updated_at | TIMESTAMPS | |

**Indexes:** `(product_id, is_active)`, `UNIQUE(sku)`

#### `product_variant_attribute_value` (pivot)
| Column | Type |
|---|---|
| product_variant_id | BIGINT UNSIGNED FK |
| attribute_value_id | BIGINT UNSIGNED FK |

**Composite PK:** `(product_variant_id, attribute_value_id)`

#### `category_product` / `label_product` (pivots)
Standard (product_id, category_id) and (product_id, label_id) with composite PKs.

#### `product_associations` (frequently bought together)
| Column | Type |
|---|---|
| product_id | BIGINT UNSIGNED FK |
| associated_product_id | BIGINT UNSIGNED FK |
| sort_order | INT UNSIGNED DEFAULT 0 |

**Composite PK:** `(product_id, associated_product_id)`

#### `carts`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| user_id | BIGINT UNSIGNED NULL FK | NULL = guest |
| session_id | VARCHAR(255) | for guest carts |
| created_at, updated_at | TIMESTAMPS | |

**Indexes:** `(session_id)`, `(user_id)`

#### `cart_items`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| cart_id | BIGINT UNSIGNED FK | |
| product_id | BIGINT UNSIGNED FK | |
| variant_id | BIGINT UNSIGNED NULL FK | |
| quantity | INT UNSIGNED | |
| created_at, updated_at | TIMESTAMPS | |

**Unique:** `(cart_id, product_id, variant_id)`

#### `stock_reservations`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| order_id | BIGINT UNSIGNED FK | |
| product_id | BIGINT UNSIGNED FK | |
| variant_id | BIGINT UNSIGNED NULL FK | |
| quantity | INT UNSIGNED | |
| reserved_until | TIMESTAMP | now + reservation_ttl |
| released_at | TIMESTAMP NULL | set when released or converted |
| created_at | TIMESTAMP | |

**Indexes:** `(order_id)`, `(reserved_until, released_at)` for cleanup query

#### `orders`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| user_id | BIGINT UNSIGNED NULL FK | NULL = guest |
| order_number | VARCHAR(20) UNIQUE | PE-20260206-XXXX |
| status | VARCHAR(30) | pending, paid, processing, shipped, delivered, cancelled |
| payment_status | VARCHAR(30) | pending, paid, failed, refunded |
| stripe_checkout_session_id | VARCHAR(255) NULL | Phase 1 |
| stripe_payment_intent_id | VARCHAR(255) NULL | Phase 2 / populated from webhook |
| email | VARCHAR(255) | denormalized for guest orders |
| phone | VARCHAR(50) NULL | |
| billing_address | JSON | snapshot |
| shipping_address | JSON | snapshot |
| subtotal | DECIMAL(10,2) | |
| vat_rate | DECIMAL(5,2) | snapshot of rate at time of order |
| vat_amount | DECIMAL(10,2) | |
| shipping_amount | DECIMAL(10,2) | |
| total | DECIMAL(10,2) | |
| currency | CHAR(3) DEFAULT 'EUR' | |
| prices_include_vat | BOOLEAN | snapshot of setting at order time |
| notes | TEXT NULL | customer notes |
| confirmation_sent_at | TIMESTAMP NULL | idempotency flag for email |
| paid_at | TIMESTAMP NULL | |
| shipped_at | TIMESTAMP NULL | |
| created_at, updated_at | TIMESTAMPS | |

**Indexes:**
- `UNIQUE(order_number)`
- `(user_id)`
- `(status, created_at)`
- `(stripe_checkout_session_id)`
- `(email)`

#### `order_items`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| order_id | BIGINT UNSIGNED FK | |
| product_id | BIGINT UNSIGNED FK | |
| variant_id | BIGINT UNSIGNED NULL FK | |
| product_name | VARCHAR(255) | snapshot |
| variant_label | VARCHAR(255) NULL | "Size: XL, Color: Red" |
| sku | VARCHAR(100) | snapshot |
| quantity | INT UNSIGNED | |
| unit_price | DECIMAL(10,2) | |
| total | DECIMAL(10,2) | |

#### `order_status_history`
| Column | Type |
|---|---|
| id | BIGINT UNSIGNED PK |
| order_id | BIGINT UNSIGNED FK |
| from_status | VARCHAR(30) NULL |
| to_status | VARCHAR(30) |
| note | TEXT NULL |
| created_at | TIMESTAMP |

#### `waitlist_entries`
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| product_id | BIGINT UNSIGNED FK | |
| variant_id | BIGINT UNSIGNED NULL FK | |
| email | VARCHAR(255) | |
| notified_at | TIMESTAMP NULL | |
| created_at, updated_at | TIMESTAMPS | |

**Unique:** `(product_id, variant_id, email)`

#### `addresses` (saved customer addresses)
| Column | Type |
|---|---|
| id | BIGINT UNSIGNED PK |
| user_id | BIGINT UNSIGNED FK |
| label | VARCHAR(50) |
| line1 | VARCHAR(255) |
| line2 | VARCHAR(255) NULL |
| city | VARCHAR(100) |
| state | VARCHAR(100) NULL |
| postal_code | VARCHAR(20) |
| country | CHAR(2) DEFAULT 'GR' |
| is_default | BOOLEAN DEFAULT false |
| created_at, updated_at | TIMESTAMPS |

#### Blog Tables

**`blog_posts`**: id, author_id (FK users), title, slug (UNIQUE), excerpt
(TEXT NULL), body (TEXT), featured_image (VARCHAR NULL), is_published
(BOOLEAN), published_at (TIMESTAMP NULL), meta_title, meta_description,
timestamps. **FULLTEXT(title, excerpt, body).**

**`blog_categories`**: id, name, slug (UNIQUE), timestamps.
**`blog_tags`**: id, name, slug (UNIQUE), timestamps.
Pivots: `blog_category_post`, `blog_post_tag`.

#### `pending_emails`
| Column | Type |
|---|---|
| id | BIGINT UNSIGNED PK |
| mailable_class | VARCHAR(255) |
| mailable_data | JSON |
| to_email | VARCHAR(255) |
| attempts | INT UNSIGNED DEFAULT 0 |
| last_error | TEXT NULL |
| sent_at | TIMESTAMP NULL |
| created_at, updated_at | TIMESTAMPS |

---

## 3. Search Strategy

### Primary: MySQL FULLTEXT in BOOLEAN MODE

```sql
-- Two FULLTEXT indexes on products:
FULLTEXT idx_products_search (name, short_description, description)
FULLTEXT idx_products_name (name)
```

**Search query with name boosting:**
```sql
SELECT *,
  (MATCH(name) AGAINST(? IN BOOLEAN MODE) * 3 +
   MATCH(name, short_description, description) AGAINST(? IN BOOLEAN MODE)
  ) AS relevance
FROM products
WHERE is_active = 1
  AND (published_at IS NULL OR published_at <= NOW())
  AND MATCH(name, short_description, description) AGAINST(? IN BOOLEAN MODE)
ORDER BY relevance DESC
LIMIT 24 OFFSET ?
```

### Fallback Chain

1. **FULLTEXT BOOLEAN MODE** (primary) — fast, supports `+word -word` operators
2. **Prefix LIKE search** — if FULLTEXT returns 0 results:
   `WHERE name LIKE 'searchterm%'` (sargable, uses index on `name`)
3. **Contains LIKE search** — if prefix returns 0 results:
   `WHERE name LIKE '%searchterm%'` (full scan, but only on `name` column)

### MySQL / MariaDB Caveats (IMPORTANT)

| Caveat | Impact | Mitigation |
|---|---|---|
| **Minimum word length** — InnoDB FULLTEXT default `innodb_ft_min_token_size` = 3 chars | Searches for "XL", "S", "M" return nothing | Use LIKE fallback for short terms (< 3 chars). Document that hosting provider may need to adjust `innodb_ft_min_token_size = 1` in my.cnf |
| **Stopwords** — common English words ("the", "and", "for") are ignored | Search for "The North Face" may miss "The" | Use BOOLEAN MODE which has fewer stopword issues. The brand name will still match on "North Face" |
| **50% threshold** — In NATURAL LANGUAGE MODE, words appearing in > 50% of rows are ignored | Early catalog with few products may have issues | Always use BOOLEAN MODE which has no 50% threshold |
| **MariaDB vs MySQL differences** — MariaDB uses Mroonga by default in some configs | Different FULLTEXT behavior | Explicitly use `ENGINE=InnoDB` in migrations. Test on target hosting |
| **No partial/fuzzy matching** — FULLTEXT doesn't support typo tolerance | "shiirt" won't find "shirt" | Prefix LIKE fallback partially helps. True fuzzy search is Phase 2+ |
| **CJK / multibyte** — may need `ngram` parser for non-Latin scripts | Not relevant for GR/EU launch | Default parser is fine for Latin + Greek chars |
| **Index rebuild** — FULLTEXT indexes need time to rebuild after bulk inserts | Slow initial import | Run `OPTIMIZE TABLE products` after bulk seeding |

### Safe Prefix Search Implementation

For the `name` column, a standard B-tree index supports fast `LIKE 'term%'`
queries. This is the primary fallback and is safe on shared hosting:

```sql
-- This uses the index (sargable):
WHERE name LIKE 'search%'

-- This does NOT use the index (table scan, but only on name column):
WHERE name LIKE '%search%'
```

**Search input sanitization:** Strip FULLTEXT operators (`+`, `-`, `*`, `~`,
`<`, `>`, `(`, `)`, `"`) from user input before building queries. Wrap terms
for BOOLEAN MODE: `+term1 +term2*` (require all words, wildcard on last word).

### Filter Parameters (applied via WHERE joins)

- Category: `JOIN category_product` with `category_id IN (?)`
- Brand: `WHERE brand_id IN (?)`
- Price range: `WHERE price BETWEEN ? AND ?`
- In-stock: `WHERE (stock - reserved_stock) > 0` (or variant equivalent)
- Sort: relevance (default), price_asc, price_desc, newest

---

## 4. Checkout + Stripe Flow (Phase 1: Checkout Sessions)

### Why Checkout Sessions for Phase 1

- **No JS payment form** — Stripe hosts the entire payment page
- **Automatic 3DS/SCA** — Stripe handles all compliance
- **Simpler webhook** — fewer event types to handle
- **Reliable on shared hosting** — no Stripe.js initialization issues
- **Built-in receipt page** — optional

### Flow Diagram

```
STEP 1: CART REVIEW
┌──────────────────────────────┐
│ Cart items + quantities      │
│ VAT breakdown                │
│ Free shipping progress bar   │
│ [Proceed to Checkout]        │
└──────────────┬───────────────┘
               │
STEP 2: INFORMATION
┌──────────────▼───────────────┐
│ Email (required)             │
│ Shipping address             │
│ Billing = shipping toggle    │
│ Order notes (optional)       │
│ [Continue to Payment]        │
└──────────────┬───────────────┘
               │
STEP 3: SERVER PROCESSING
┌──────────────▼───────────────────────────────────────┐
│ POST /checkout/process                                │
│                                                       │
│  1. Validate cart not empty                           │
│  2. Validate form data (FormRequest)                  │
│  3. DB::transaction:                                  │
│     a. Lock product/variant rows (lockForUpdate)      │
│     b. Check available stock (stock - reserved_stock) │
│     c. Create Order (status=pending, payment=pending) │
│     d. Create OrderItems (snapshot prices)             │
│     e. Create StockReservations                       │
│        (reserved_until = now + 30min)                 │
│     f. Increment reserved_stock on product/variant    │
│  4. Create Stripe Checkout Session:                   │
│     - line_items from order                           │
│     - mode: 'payment'                                 │
│     - success_url: /checkout/success?session={id}     │
│     - cancel_url: /checkout/cancel?order={number}     │
│     - metadata: { order_id, order_number }            │
│     - customer_email: from form                       │
│     - expires_after: 30 minutes                       │
│  5. Save stripe_checkout_session_id on order          │
│  6. Redirect to Stripe Checkout                       │
└──────────────┬───────────────────────────────────────┘
               │
               │  ┌─── User pays on Stripe ───┐
               │  │   (hosted payment page)    │
               │  └────────────┬───────────────┘
               │               │
        ┌──────▼───────┐  ┌───▼────────────────────────┐
        │ CANCEL URL   │  │ SUCCESS URL                 │
        │              │  │ GET /checkout/success        │
        │ Show message │  │   ?session_id=cs_xxx         │
        │ Cart intact  │  │                              │
        │              │  │ Show "processing" message    │
        │              │  │ "Order received, confirming  │
        │              │  │  payment..." with order #    │
        │              │  │                              │
        │              │  │ NOTE: Do NOT mark as paid    │
        │              │  │ here. Wait for webhook.      │
        └──────────────┘  └──────────────────────────────┘

WEBHOOK (source of truth for payment)
┌──────────────────────────────────────────────────────┐
│ POST /stripe/webhook                                  │
│                                                       │
│ 1. Verify Stripe signature (STRIPE_WEBHOOK_SECRET)    │
│ 2. Switch on event type:                              │
│                                                       │
│ ── checkout.session.completed ──                      │
│    a. Extract order_id from metadata                  │
│    b. Find order                                      │
│    c. If payment_status already 'paid' → return 200   │
│       (IDEMPOTENT — prevents double processing)       │
│    d. DB::transaction:                                │
│       - Update order: payment_status=paid, paid_at    │
│       - Update order: status=paid                     │
│       - Decrement actual stock (product/variant)      │
│       - Clear reserved_stock for this order           │
│       - Mark stock_reservations as released           │
│       - Record order_status_history                   │
│    e. Send OrderConfirmationMail                      │
│       - Check confirmation_sent_at is NULL            │
│       - Set confirmation_sent_at = now                │
│       - On SMTP failure: store in pending_emails      │
│    f. Clear user's cart                               │
│    g. Return 200                                      │
│                                                       │
│ ── checkout.session.expired ──                        │
│    a. Find order by session ID                        │
│    b. Release stock reservations                      │
│    c. Mark order as cancelled                         │
│    d. Return 200                                      │
│                                                       │
│ 3. Always return 200 (even on errors — log them)      │
└──────────────────────────────────────────────────────┘
```

### Stock Reservation Model

```
Available stock = stock - reserved_stock

On checkout:
  → reserved_stock += quantity
  → stock_reservations row created (reserved_until = now + 30min)

On payment_succeeded webhook:
  → stock -= quantity (actual decrement)
  → reserved_stock -= quantity
  → stock_reservations.released_at = now

On reservation expiry (cron every 5 minutes):
  → Find stock_reservations WHERE reserved_until < now AND released_at IS NULL
  → reserved_stock -= quantity
  → stock_reservations.released_at = now
  → Order status → cancelled (if still pending)

Race condition protection:
  → All stock operations use DB::transaction + lockForUpdate()
  → Available = stock - reserved_stock checked inside lock
```

### Webhook Security

1. **Signature verification** — `\Stripe\Webhook::constructEvent()` with
   raw payload + `Stripe-Signature` header + `STRIPE_WEBHOOK_SECRET`
2. **Idempotency** — check `payment_status` before any mutation
3. **CSRF exclusion** — webhook route excluded from `VerifyCsrfToken`
4. **Always return 200** — log errors but never return 4xx/5xx to Stripe
5. **Metadata linking** — `metadata.order_id` links session to order
6. **Timeout safety** — webhook handler must complete quickly (no heavy
   operations). Email failure is caught and deferred to pending_emails.

### VAT Calculation in Checkout

```
Settings: vat_rate = 24.00, prices_include_vat = true

If prices_include_vat = true:
  DB price = 24.99 (gross)
  net = 24.99 / 1.24 = 20.15
  vat = 24.99 - 20.15 = 4.84
  Display: "24,99 €" (price shown is what customer pays)
  Order total: subtotal(gross) + shipping

If prices_include_vat = false:
  DB price = 20.15 (net)
  vat = 20.15 * 0.24 = 4.84
  gross = 20.15 + 4.84 = 24.99
  Display: "24,99 € (incl. VAT)" or "20,15 € + VAT"
  Order total: subtotal(net) + vat + shipping
```

---

## 5. SEO + WCAG Checklist

### SEO Checklist

| # | Item | Implementation |
|---|---|---|
| 1 | Clean URLs | `/products/{slug}`, `/categories/{slug}`, `/brands/{slug}`, `/blog/{slug}` |
| 2 | Canonical tags | `<link rel="canonical">` on every page; filtered/paginated pages point to base URL |
| 3 | Meta tags | Custom `meta_title` + `meta_description` per entity, fallback to auto-generated |
| 4 | Open Graph | OG title, description, image on product + blog pages |
| 5 | Schema.org | `Product` (with `Offer`), `BreadcrumbList`, `Organization`, `BlogPosting` — JSON-LD |
| 6 | XML Sitemap | Cron-generated daily at `/sitemap.xml`, includes products, categories, blog |
| 7 | robots.txt | Disallow `/admin`, `/checkout`, `/cart`; allow everything else |
| 8 | Breadcrumbs | Visible UI + `BreadcrumbList` structured data |
| 9 | Heading hierarchy | One `<h1>` per page, strict H1→H2→H3 nesting |
| 10 | Image alt text | Required field; fallback to product name |
| 11 | Page speed | Lazy images, purged Tailwind, gzip via .htaccess |
| 12 | 404 page | Custom branded with search + popular products |
| 13 | Slug redirects | Old slug → 301 redirect (store old slugs) |
| 14 | EUR currency | Prices use `€` symbol, formatted as `19,99 €` |
| 15 | hreflang | Not needed Phase 1 (single language) |

### WCAG 2.1 AA Checklist

| # | Item | Implementation |
|---|---|---|
| 1 | Skip to content | Hidden link, first focusable element |
| 2 | Keyboard navigation | All interactive elements focusable, logical tab order |
| 3 | Focus indicators | `focus-visible:ring-2 focus-visible:ring-offset-2` on all elements |
| 4 | ARIA labels | `aria-label` on icon buttons, `aria-live="polite"` on dynamic regions |
| 5 | Color contrast | 4.5:1 text, 3:1 large text |
| 6 | Form labels | Every `<input>` has `<label>`, errors via `aria-describedby` |
| 7 | Error identification | Not color-only; icon + text + aria |
| 8 | Alt text | All `<img>` have descriptive alt; decorative use `alt=""` |
| 9 | Touch targets | Minimum 44x44px |
| 10 | Responsive text | No text < 16px on mobile, `rem` units |
| 11 | Reduced motion | `prefers-reduced-motion` respected |
| 12 | Language attr | `<html lang="en">` |
| 13 | Semantic HTML | `<nav>`, `<main>`, `<article>`, `<header>`, `<footer>` |
| 14 | Cart announcements | `aria-live` for "Item added to cart" |
| 15 | Swatch a11y | Color swatches have text labels, `role="radiogroup"` |
| 16 | Mobile nav | Hamburger menu with `aria-expanded`, focus trap |

---

## 6. Implementation Plan (Phase 1 Tickets)

### Phase 0: Project Scaffolding

**PR-01: Laravel project + dependencies**
- `laravel new peshop` with Laravel 11
- Install: Tailwind CSS, Livewire v3, Alpine.js, Filament v3
- Install: `stripe/stripe-php`, `intervention/image`, Pest
- Configure: MySQL, file cache, database sessions, sync mail
- Configure: EUR defaults, timezone Europe/Athens
- cPanel .htaccess configuration
- Base Pest setup

**PR-02: Base layout + design system**
- Master Blade layout (mobile-first, semantic HTML)
- Header: logo, nav, search icon, cart icon with count
- Mobile hamburger menu (Alpine.js, focus trap, aria-expanded)
- Footer
- Skip-to-content link
- Tailwind config: brand colors, focus ring utilities
- `<x-seo-meta>` Blade component
- Breadcrumb component
- EUR price formatting helper

**PR-03: Admin panel setup**
- Filament panel at `/admin`
- Admin user seeder
- Dashboard page (stub)
- Settings management page (key-value from settings table)

### Phase 1: Catalog

**PR-04: Categories + Brands + Labels**
- Migrations + models + relationships
- Filament resources for each
- Category tree (parent_id, scopes)
- Seed data

**PR-05: Products + Variants + Images**
- Migrations for products, images, attributes, attribute_values,
  variants, variant_attribute_value pivot, category_product, label_product,
  product_associations
- Models + relationships + scopes
- FULLTEXT index migrations
- ImageService (upload + resize to 3 sizes via Intervention)

**PR-06: Product admin (Filament)**
- ProductResource: info, pricing, images, variants, categories/brand/labels,
  SEO, associations tabs
- WYSIWYG editor integration
- Image upload with preview and reorder

**PR-07: Storefront pages**
- Homepage: featured products, category grid
- Category page: product grid
- Product page: gallery, variant swatches, price, add-to-cart button,
  frequently bought together, breadcrumbs, Schema.org JSON-LD
- Responsive `<picture>` component with lazy loading
- Brand page

### Phase 2: Search

**PR-08: Search + Filters + AJAX Pagination**
- SearchService (FULLTEXT + prefix fallback + contains fallback)
- Livewire ProductFilter component
- Debounced search input, category/brand checkboxes, price range,
  in-stock toggle, sort dropdown
- AJAX pagination via Livewire
- Search results page
- `aria-live` region for result count

### Phase 3: Cart + Checkout

**PR-09: Cart system**
- Migrations: carts, cart_items
- CartService: add, update, remove, merge guest→user, totals
- Session-based guest cart + DB cart
- Livewire cart drawer / slide-out
- Cart page with quantity controls
- Free shipping progress bar
- VatService (calculate VAT from settings)

**PR-10: Checkout + Stripe Checkout Sessions**
- Migrations: orders, order_items, order_status_history,
  stock_reservations
- CheckoutController: information form → process → redirect to Stripe
- Stock reservation logic (lockForUpdate, reserved_stock, reserved_until)
- Stripe Checkout Session creation
- Success page ("order received, confirming payment...")
- Cancel page (cart preserved)
- StripeWebhookController:
  - checkout.session.completed (idempotent: mark paid, decrement stock,
    release reservation, send confirmation email)
  - checkout.session.expired (release reservation, cancel order)
- OrderConfirmationMail (sent ONLY from webhook)
- pending_emails table + cron retry
- Cron: release expired reservations every 5 min

**PR-11: Order emails + customer order view**
- OrderConfirmation email template (responsive, branded)
- OrderShipped email template
- Customer account: order history list + detail view

### Phase 4: Accounts + Waitlist

**PR-12: Customer auth + account**
- Laravel Breeze (Blade stack)
- Login, register, forgot password
- Account dashboard: orders, addresses, profile
- Cart merge on login

**PR-13: Waitlist**
- Migration: waitlist_entries
- Livewire form on out-of-stock products
- Cron: notify waitlist when stock > 0
- WaitlistNotificationMail

### Phase 5: Blog

**PR-14: Blog backend + frontend**
- Migrations: blog_posts, blog_categories, blog_tags + pivots
- Models + Filament resources (with WYSIWYG)
- Blog listing, post page, sidebar
- Schema.org BlogPosting, meta tags

### Phase 6: Admin + Analytics

**PR-15: Order management**
- Filament OrderResource: list, view, status transitions, mark shipped
- Order status history display
- Customer list resource

**PR-16: Analytics dashboard**
- Revenue today/week/month widgets
- Order count by status
- Top selling products
- Low stock alerts

### Phase 7: SEO + Performance + Testing

**PR-17: SEO finalization**
- XML sitemap generator (cron)
- robots.txt, canonical tags, OG tags
- Schema.org audit
- Old slug → 301 redirect table

**PR-18: Performance**
- File cache for category tree, homepage data
- Eager loading audit (N+1 elimination)
- Tailwind purge, .htaccess gzip/caching
- Query optimization (EXPLAIN key queries)

**PR-19: Test suite**
- Pest feature tests:
  - AddToCartTest
  - CheckoutCreatesOrderTest
  - StripeWebhookTest (idempotent)
  - StockReservationTest (reserve, decrement, expire)
  - WaitlistSignupTest
- Factories for all models
- Stripe mock helpers

**PR-20: Deployment prep**
- cPanel deployment config
- .htaccess for public routing
- Storage symlink
- Cron configuration doc
- Deployment checklist

### Ticket Summary

| PR | Title | Depends On |
|---|---|---|
| 01 | Project setup + dependencies | — |
| 02 | Base layout + design system | 01 |
| 03 | Admin panel setup | 01 |
| 04 | Categories + Brands + Labels | 03 |
| 05 | Products + Variants schema | 04 |
| 06 | Product admin (Filament) | 05 |
| 07 | Storefront pages | 05 |
| 08 | Search + Filters | 05 |
| 09 | Cart system | 07 |
| 10 | Checkout + Stripe | 09 |
| 11 | Order emails + views | 10 |
| 12 | Customer auth | 09 |
| 13 | Waitlist | 05, 12 |
| 14 | Blog | 03 |
| 15 | Order management | 10 |
| 16 | Analytics dashboard | 15 |
| 17 | SEO finalization | 07, 14 |
| 18 | Performance | 08, 10 |
| 19 | Test suite | 10, 13 |
| 20 | Deployment prep | all |

---

## Phase 2+ Backlog (NOT in scope)

- Embedded Stripe Payment Intents (replace Checkout Sessions)
- Abandoned cart recovery emails
- Carrier/shipping integrations
- Countdown timers on products
- Advanced discount engine (coupons, rules)
- Multi-currency support
- Multi-language (i18n)
- Product reviews/ratings
- Wishlist
- Social login
- Advanced analytics
