# Ledgers Module

## Module name

**Party Ledgers & Statements**

## Short description

The Ledgers module presents a paginated, filterable audit trail of financial entries recorded by other workflows. It summarizes total debits, total credits, and the overall net receivable/payable position. It is a ledger viewer; transactions are created by purchasing, sales, and sizing-related workflows rather than entered on this page.

## Where to find it

- **Party ledgers:** `/ledgers`
- **Controller:** `app/Http/Controllers/LedgerController.php`
- **View:** `resources/views/ledgers/index.blade.php`
- **Model:** `app/Models/Ledger.php`

The route requires authentication.

## Inputs and what they mean

The page has a GET filter form; changing either select submits the filter immediately.

| Input | Required | Meaning and behavior |
|---|---|---|
| Party (`party_name`) | No | Select a party name from ledger records. |
| Party type (`party_type`) | No | Filter by the selected category, such as yarn supplier, sizing mill, or fabric buyer. |

The filters can be combined. **Clear** removes both filters. The current filters remain in the URL while paging through results.

## What the module displays

### Summary figures

- **Total Debit** — sum of all ledger debit entries.
- **Total Credit** — sum of all ledger credit entries.
- **Net Balance Standing** — total debit minus total credit. A non-negative result is labeled net receivable; a negative result is labeled net payable to suppliers.

### Party statement

The table displays transaction date, voucher number, party name, party type, description, debit, credit, and the balance stored on each ledger entry. It shows 20 entries per page, newest date/ID first.

The party dropdown is populated from distinct party-name/type combinations already present in ledger records. A party without a ledger entry is not added from this screen.

## How ledger entries are created

This screen reads ledger records; other modules create them as part of their workflows. For example:

- Bag purchases create supplier-side yarn purchase entries.
- Fabric sales create buyer-side entries based on sale amount and payment status.
- Sizing workflows can record sizing-related party costs.

Cash/bank account transactions and invoice payment allocations are maintained in the Invoices module and are not the same thing as party-ledger entries. For receipt and account balances, see [INVOICES_MODULE_README.md](./INVOICES_MODULE_README.md).

## Route

| Method | Path | Purpose |
|---|---|---|
| GET | `/ledgers` | View, filter, and paginate party-ledger entries. |

## Main implementation files

- `routes/web.php` — authenticated ledger route.
- `app/Http/Controllers/LedgerController.php` — filtering, pagination, and global debit/credit totals.
- `app/Models/Ledger.php` — stored party/voucher/debit/credit/balance fields.
- `resources/views/ledgers/index.blade.php` — summary cards, filters, and statement table.
- `database/migrations/2026_10_16_095100_create_ledgers_table.php` — ledger schema.
