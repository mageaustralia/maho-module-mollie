> [!WARNING]
> **Alpha — untested in production.** This module is a port of the original [Mollie Magento 1 module](https://github.com/mollie/Magento) but we **cannot perform end-to-end testing ourselves**: MageAustralia is based outside the EU and Mollie does not issue merchant accounts to non-EU entities, so we cannot exercise the live payment flow against a Mollie test/production environment. Community testing, screenshots, and bug reports are very welcome — open an issue or PR.

# Mollie Payment Module for Maho

A comprehensive Mollie payment gateway integration for [Maho](https://github.com/MahoCommerce/maho), supporting 20+ payment methods across Europe.

## Supported Payment Methods

| Method | Code |
|--------|------|
| iDEAL | `mollie_ideal` |
| Credit Card | `mollie_creditcard` |
| Credit Card Components | Embedded card fields |
| Bancontact | `mollie_bancontact` |
| SOFORT Banking | `mollie_sofort` |
| Bank Transfer | `mollie_banktransfer` |
| PayPal | `mollie_paypal` |
| Apple Pay | `mollie_applepay` |
| Klarna Pay Later | `mollie_klarnapaylater` |
| Klarna Pay Now | `mollie_klarnapaynow` |
| Klarna Slice It | `mollie_klarnasliceit` |
| KBC/CBC | `mollie_kbc` |
| Belfius | `mollie_belfius` |
| paysafecard | `mollie_paysafecard` |
| Gift Cards | `mollie_giftcard` |
| EPS | `mollie_eps` |
| Giropay | `mollie_giropay` |
| Przelewy24 | `mollie_przelewy24` |
| in3 | `mollie_in3` |
| MyBank | `mollie_mybank` |
| Payment Link | `mollie_method_paymentlink` |

## Requirements

- Maho 24.12 or later
- PHP 8.3 or later
- Mollie account with API keys ([mollie.com](https://www.mollie.com/))

## Installation

```bash
composer require mageaustralia/maho-module-mollie
```

Then run:

```bash
php maho cache:flush
```

The install script will automatically create the required database tables and attributes on next page load.

## Configuration

1. Go to **System > Configuration > Payment Methods > Mollie**
2. Enter your **Test API Key** and **Live API Key** (from your [Mollie Dashboard](https://www.mollie.com/dashboard))
3. Select **Test** or **Live** mode
4. Enable the payment methods you want to offer
5. Configure status mapping, locale, and other options as needed

### API Keys

API keys are stored encrypted in the database. Test keys start with `test_`, live keys start with `live_`.

### Checkout Types

- **Orders API** (default) — Full order details sent to Mollie, supports Klarna, partial shipments, and order-level refunds
- **Payments API** — Simpler payment-only flow, no line item details

## Features

- Orders API and Payments API support with automatic fallback
- Webhook-based status updates
- Partial and full refunds (online)
- Partial shipments with Klarna capture
- Payment fee / surcharge support
- Credit card components (embedded card fields)
- Multi-store / multi-currency support
- Bank transfer with configurable due dates
- Apple Pay availability detection
- Loading screen with auto-redirect
- Payment link generation for admin orders

## Changes from Original Module

This is a port of [mollie/Magento v5.9.0](https://github.com/mollie/Magento) with the following changes for Maho compatibility:

- `declare(strict_types=1)` on all PHP files
- Removed Prototype.js — all JavaScript rewritten to vanilla ES6
- Removed custom autoloader (uses Composer autoloading)
- API keys stored encrypted (`frontend_type=obscure`)
- CSRF protection on admin controller actions
- All template output properly escaped (`escapeHtml`/`escapeUrl`/`json_encode`)
- Standalone `__()` calls replaced with `Mage::helper('mpm')->__()`
- `%1` placeholders replaced with `%s` (Maho/Magento 1 convention)
- Consolidated 12 install/upgrade scripts into single `install-1.0.0.php`
- Removed Mage_Log dependency
- Removed deprecated void payment methods
- User-Agent reports `Maho/` instead of `Magento/`

## License

OSL-3.0 for the combined work (with Mollie BSD-2-Clause attribution preserved for upstream code) — see [LICENSE](LICENSE).

Original module copyright (c) 2012-2019 Mollie B.V.
Maho port copyright (c) 2026 Mage Australia.
