# LoomFlow — Grey-Fabric Factory ERP

## Full revised Laravel source-code package

This is an extension of the existing LoomFlow Laravel application—not a replacement or a new skeleton. It supports **grey-fabric weaving only**. It is designed for **one office operator**. Existing Bags, Sizing, Beams, Parts, Production, Fabric Stock, Sales, Invoices, Expenses, Dashboard, and Ledger screens remain present.

### Added factory screens

| Screen | Route | Main purpose |
|---|---|---|
| Factory orders & Parta | `/factory/orders` | Save local Parta and weight, create own or job orders, allocate looms, yarn receipts/issues, beam movements and settlement, close orders |
| Checked Thaans | `/factory/checking` | Record individually marked, measured Thaans after checking; this is the only routine way new cloth enters physical stock |
| Kachi / Pakki Parchi | `/factory/parchis` | Dispatch checked customer-owned Thaans, record acceptance/rejection, invoice accepted metres only, reject/return/resend, own-fabric sales |
| Factory reports | `/factory/reports` | Order performance, accepted/dispatched/checked quantities, yarn reconciliation, wages and stoppages |

### Confirmed textile rules

- **No towel functionality.** All weaving qualities are grey fabric.
- **Weight Parta uses local divisor 800** via the original `YarnQualityWeightCalculator`, with separate taana/baana weights, grams, GSM and cost per metre. Each work order stores a Parta version.
- Both **own production** (optional target) and **job-work** contracts are supported. Customer/factory/mixed ownership of taana and baana is configurable by job order.
- Multiple looms can share one contract. Each loom has **one order per day**. Historical assignments remain intact when changed on a later date.
- Loom meter readings are **provisional** and do not add physical fabric stock.
- Only actual checked **individual Thaan lengths** add A-category stock. No duplicate roll-register receipts.
- **Kachi** contains the dispatch's marked Thaans and metres. **One Pakki per Kachi**, records accepted Thaan count plus accepted/rejected metres.
- **Mazdoori invoice = accepted metres (Pakki) × picks × agreed per-pick rate**. A Kachi does not issue a weaving bill.
- Rejected **whole Thaans** retain original markings/lengths and can be returned, resent on another Kachi, settled/retained, approved for external sale, or carried to another job for the same party/quality.
- A partially used beam stays with its original contract when moved to another loom; reassignment to a different order requires recorded settlement, using **contract warp rate** as predicted rate with manual override.
- **Taxes are optional and off by default**. Legacy sales-contract tax estimates can be individually enabled; no preset rate is applied.

## Install on your developer's local machine

Required: **PHP 8.3+ with standard Laravel extensions (including `mbstring`, `pdo_mysql` or `pdo_sqlite`)**, **Composer**, **MySQL 8+** (or a supported database), **Node.js** and **npm**. Refer to `composer.json` and `package.json` for the dependency set.

```bash
# Extract this ZIP and enter the root folder containing artisan and composer.json.
composer install
cp .env.example .env              # Windows: copy .env.example .env
# Set DB_DATABASE, DB_USERNAME and DB_PASSWORD in .env; create an empty database first.
php artisan key:generate
php artisan migrate
php artisan loomflow:create-operator
npm install
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000` and sign in using the operator email and password you created. For staging/production set `APP_ENV=production`, `APP_DEBUG=false`, serve over HTTPS, restrict database access and configure automated encrypted backups. NEVER run `migrate:fresh` on a database containing real factory records.

**Important:** `DatabaseSeeder` contains original sample records and demo passwords. It is intentionally restricted to local/testing environments and **must never be executed on the live factory database**. Demo quick-login is disabled unless the environment is `local` and debug mode is enabled.

### Suggested daily operating sequence

1. Under **Fabric Stock**, create a quality (grey fabric specifications), then save its **Parta** on the Factory Orders screen.
2. Create a **Job Work** order (with customer/contract, per-pick rate, yarn-supply configuration), or **Own Manufacturing** order (no customer or target required).
3. Allocate one or more looms to that work order. Record yarn owner/weight and sizing receipts if applicable; create and mount the correct beams in **Sizing**.
4. Use **Production** for provisional machine/beam readings. After the checker sends its same-day report, record every marked Thaan and exact measured length on **Checking**; only then does fabric stock increase.
5. For **Job Work**, select checked Thaans for **Kachi Parchi**, then record **Pakki Parchi** for the single Kachi. Invoices are issued only for customer-accepted metres.
6. Register rejected whole Thaans by their original numbers. Choose a disposition before final contract closure. A resent Thaan gets a **new Kachi** and never counts as new loom production.
7. For **Own Manufacturing**, sell checked factory-owned Thaans from the Own Sale form; optional sales invoice and collection are handled in the existing **Invoices** module.
8. Use **Factory Reports**, **Ledgers**, and **Invoices** for production/financial follow-up. Wages and downtime are recorded from Factory Reports; cash payment records remain separate to prevent duplicate postings.

## Test and release

```bash
php artisan optimize:clear
php artisan test --filter=FactoryWorkflowTest
php artisan test
```

The package contains new end-to-end feature tests covering Parta, no-target internal orders, checked-only stock, Kachi/Pakki accepted-only invoices, full-Thaan returns and re-dispatch, and rejected checking without loom allocation. The original production test has been updated to expect **zero stock increase** until checking.

**QA status for this package:** A static `php -l` syntax pass succeeded for **109 PHP source/test/migration/route files**. Runtime migrations, Blade rendering, PHPUnit tests and browser workflows **could not be executed in the creation environment**, which has neither Composer-installed Laravel dependencies nor the required PDO SQLite/mbstring extensions. They MUST be executed before any real factory data is entered.

### Production-readiness checklist

- Back up the current database and test upgrades on a copy. Review existing yarn bags, beam status, active contracts, historical fabric balances and opening ledger balances.
- **Existing legacy fabric stock is not automatically converted into individually marked Thaans.** Agree an opening-stock reconciliation/legacy import before entering new checking reports, or the old and new totals can be counted together incorrectly.
- Confirm per-beam sizing length (the inherited sizing logic allocates total slip length evenly among its beam names), Panna conventions, 800-divisor Parta sample and contracted yarn rates with your factory's records.
- Prove that customer-owned yarn and fabric never enters own-factory open-sale inventory unless authorized, and that settlement amounts are reconciled in cash/bank accounts when paid.
- Verify balance-sheet, income/receipts, supplier ledgers and historical transactions against a live accountant's reconciliation. New material/beam settlement histories are operational records and **do not automatically transfer cash**.
- Test rejected Thaan re-dispatch and remaining contract quantities, short closing reasons, full customer returns, loom switches and beam settlement in a disposable database.
- Compare the historical invoice/contract rows from the old version with the upgraded reports; check pricing and tax settings if enabled.
- Delete/disable unused demo operator accounts before deployment, set secure credentials, and configure automated backups, access restrictions and deployment monitoring.

### Known boundaries

This is source-code delivery, **not a claim that the application has already been deployed or certified production-ready**. The existing manual Accounts/Expenses and historical stock models remain; their full financial reconciliation needs validation against real data. Rare cases where a Pakki arrives before physically returned Thaans are intentionally handled by manual entry, as requested. No external WhatsApp service, tax authority integration, payment gateway, or loom hardware integrations are included.
