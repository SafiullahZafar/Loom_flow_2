# Main Dashboard Module

## Module name

**LoomFlow Main Operations Hub**

## Short description

The dashboard is the authenticated landing page and navigation hub for the textile operation. It presents high-level stock, production, and sales figures from other modules and links into their detailed screens. It is a read-only summary; data is entered in the linked modules.

## Where to find it

- **Main hub:** `/` or `/dashboard`
- **Controller:** `app/Http/Controllers/DashboardController.php`
- **View:** `resources/views/dashboard/hub.blade.php`

Both GET routes use the same controller action and require authentication.

## What the dashboard displays

### Bags

- **Total Bags Purchased** — sum of purchase quantities.
- **Factory Godown** — factory-directed purchases, less issues to sizing, plus recorded returns.
- **At Sizing Mills** — direct-to-sizing purchases plus factory issues, less bags used and returns.
- The Bags link opens [BAGS_MODULE_README.md](./BAGS_MODULE_README.md).

### Sizing

- **Beams on Looms** — count of beams with status `mounted_on_loom`.
- **Ready in Store** — count of beams with status `ready_in_store`.
- **Remaining beam meters** — mounted beam length remaining.
- The Sizing link opens [SIZING_MODULE_README.md](./SIZING_MODULE_README.md).

### Parts

- **Total Stock Items** — sum of stock quantities across parts, displayed in units.
- **Low Stock Alert** — count of part records at or below their configured minimum stock threshold.
- The Parts link opens [PARTS_MODULE_README.md](./PARTS_MODULE_README.md).

### Fabric Stock

- **Open Market Ready** — meters available for open-market sales.
- **Committed Contract** — meters reserved for active buyer contracts.
- The Fabric Stock link opens [FABRIC_STOCK_MODULE_README.md](./FABRIC_STOCK_MODULE_README.md).

### Sales

- **Total Revenue** — sum of recorded net sales.
- **Active Contracts** — count of contracts still marked active.
- The Sales link opens [SALES_MODULE_README.md](./SALES_MODULE_README.md).

### Production

- **Total Fabric Made** — total recorded net meters produced.
- **Beam Leakage Loss** — total recorded leakage/waste meters.
- The Production link opens [PRODUCTION_MODULE_README.md](./PRODUCTION_MODULE_README.md).

### Recent activity

The dashboard includes a live activity feed built from the three latest bag purchases, production records, and sales. It also shows an average production efficiency; when no production records exist, the controller supplies a default display value.

## How figures are calculated

The dashboard queries the underlying module models directly; it does not persist separate dashboard records. Bag balances are aggregate totals. Fabric availability and commitment are maintained by the Fabric Stock, Sales, and Production workflows. Beam statuses and production totals come from sizing beams and production records.

These are operational summaries rather than party statements or invoices. Use each module's history page for the underlying records and [LEDGERS_MODULE_README.md](./LEDGERS_MODULE_README.md) or [INVOICES_MODULE_README.md](./INVOICES_MODULE_README.md) for financial detail.

## Route

| Method | Path | Purpose |
|---|---|---|
| GET | `/` | Main operations hub. |
| GET | `/dashboard` | Alternate named route to the same hub. |

## Main implementation files

- `routes/web.php` — `/` and `/dashboard` routes.
- `app/Http/Controllers/DashboardController.php` — summary calculations and latest activity queries.
- `resources/views/dashboard/hub.blade.php` — KPI cards, live feed, and module navigation.
