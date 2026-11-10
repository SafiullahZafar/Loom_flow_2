# Fabric Stock Module

## Module name

**Finished Fabric Stock, Daily Movements & Contract Reservations**

## Short description

The Fabric Stock module defines finished-fabric qualities and tracks meters and rolls in the mill store. For each quality, it separates open-market-available stock from meters reserved for active buyer contracts. Production adds finished fabric; Sales consumes it; daily stock entries record receipts, adjustments, and dispatches.

## Where to find it

- **Dashboard:** `/fabric-stock`
- **Inventory:** `/fabric-stock/inventory`
- **Active contracts:** `/fabric-stock/contracts`
- **Add quality / update stock:** **Add Quality Spec**
- **Controller:** `app/Http/Controllers/FabricStockController.php`
- **View:** `resources/views/fabric/index.blade.php`

## Inputs and what they mean

### Fabric stock entry

The entry type determines whether the form uses a saved fabric quality or creates a new one.

| Input | Required | Meaning and behavior |
|---|---|---|
| Entry type (`quality_mode`) | Yes | `existing` updates today's stock movement for a saved quality; `new` creates a quality at zero starting stock and then records its movement. |
| Fabric quality (`fabric_stock_id`) | For existing quality | Quality whose current stock is being updated. Existing stock and area are loaded from the saved record. |
| New quality name (`quality_name`) | For new quality | Unique quality name; maximum 150 characters. |
| Construction (`construction`) | No | Optional construction string, such as a weave/thread construction. |
| Read (`read`) | For new quality | Ends per inch; positive numeric value. |
| Pick (`pick`) | For new quality | Picks per inch; positive numeric value. |
| Warp yarn count (`warp_count`) | For new quality | Positive warp yarn count used for the calculated warp weight per meter. |
| Weft yarn count (`weft_count`) | For new quality | Positive weft yarn count used for the calculated weft weight per meter. |
| Width (`width_inches`) | For new quality | Fabric width in inches; 20 to 150 inches. |
| Rate per meter (`unit_cost_meter`) | No | Non-negative rupee cost per meter. Applied as the initial unit cost when creating a quality. |
| Previous stock (`previous_stock_meters`) | Display only | Read-only preview loaded from the selected quality; the saved balance is read from the database, not trusted from this form field. |
| Received today (`daily_received_meters`) | No | Meters added to stock for the current date. |
| Adjustment (`adjustment_meters`) | No | Signed meter correction: positive adds stock; negative reduces stock. |
| Dispatch (`dispatch_meters`) | No | Meters removed from stock. Dispatch cannot make the closing balance negative. |
| Rolls received (`rolls_received`) | No | Non-negative whole-number rolls/thaans added to the stock count. |
| Area (`area`) | No | Optional storage area/location label. |
| Remarks (`notes`) | No | Optional movement remarks. |

For a new quality, read, pick, warp count, weft count, and width are required. The quality starts at zero stock; any meters and rolls in the same submission are recorded as today's receipt.

For an existing quality, the selected quality's stored specifications are retained. The entry updates daily stock movement quantities and area; construction and quality-specification inputs are only used when creating a new quality.

### Calculated fabric weight (new quality)

The page previews and stores the estimated weight contribution per meter:

- Warp lb/m = `TRUNC(Read × Width ÷ Warp Count ÷ 20 × 1.0936 ÷ 40, 4)`
- Weft lb/m = `TRUNC(Pick × Width ÷ Weft Count ÷ 20 × 1.0936 ÷ 40, 4)`
- Total lb/m = warp lb/m + weft lb/m

These are estimated fabric-quality specifications, not measured finished-roll weights.

## What the module displays

### Dashboard summary

- Open-market-available meters.
- Meters committed to contracts.
- Total fabric meters and rolls/thaans in store.
- Estimated inventory value calculated from current meters and unit cost per meter.
- Recent quality inventory, active contracts, and stock movement history.

### Inventory

Lists fabric quality, construction/specifications, current meters, committed contract meters, open/available meters, roll count, area, unit cost, and calculated value. Dashboard inventory is a recent snapshot; the inventory page is paginated.

### Active contracts

Shows active buyer contracts and their delivery progress. The Contracts page lists active contracts; contract creation and meter delivery are handled in [SALES_MODULE_README.md](./SALES_MODULE_README.md).

### Stock movement history

Movement records show the quality, date, previous stock, meters received, signed adjustment, dispatched meters, closing balance, rolls received, area, and remarks. Repeated entries for the same quality on the same calendar date are merged into that day's movement.

## How stock changes

For a daily movement:

`closing meters = previous meters + received meters + adjustment meters − dispatch meters`

The module rejects a movement if this calculation would put closing stock below zero. Available open meters are recalculated as:

`available open meters = max(0, current stock meters − committed contract meters)`

When a new contract reserves fabric, the Sales module updates committed/open meters. When production records fabric, it increases stock and rebalances the current active-contract commitments against available meters. When a sale is saved, the Sales module decreases current stock and the relevant open or committed balance.

## End-to-end workflow

1. Define a fabric quality with its construction, width, read/pick, and warp/weft counts.
2. Record daily receipts, adjustments, dispatches, roll receipts, and area for the applicable date.
3. Use Fabric Stock to review current, open, and committed quantities.
4. Record woven output in [PRODUCTION_MODULE_README.md](./PRODUCTION_MODULE_README.md); production adds meters and rolls to stock.
5. Create buyer contracts or sales in [SALES_MODULE_README.md](./SALES_MODULE_README.md); these reserve and consume fabric.

## Routes

| Method | Path | Purpose |
|---|---|---|
| GET | `/fabric-stock` | Fabric dashboard and recent activity. |
| GET | `/fabric-stock/inventory` | Paginated fabric inventory. |
| GET | `/fabric-stock/contracts` | Active contract list. |
| POST | `/fabric-stock` | Create a fabric quality or record a stock movement. |

All routes require authentication.

## Main implementation files

- `routes/web.php` — Fabric Stock routes.
- `app/Http/Controllers/FabricStockController.php` — page summaries, validation, weight estimates, and daily movement updates.
- `app/Models/FabricStock.php` — quality specifications, stock, committed/open balances, and unit cost.
- `app/Models/FabricStockMovement.php` — dated stock movement and fabric-quality relationship.
- `resources/views/fabric/index.blade.php` — dashboard, inventory, movement history, and stock-entry modal.
- `database/migrations/` — fabric stock, movement, estimated weight, area, and daily-receipt fields.
