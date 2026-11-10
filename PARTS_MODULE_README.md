# Parts Module

## Module name

**Parts Store Inventory & Loom Issuance**

## Short description

The Parts module catalogs loom spares and consumables, monitors on-hand quantities against low-stock thresholds, values the inventory, and records parts issued to a loom and responsible person. An issue creates an issuance history record and reduces the store balance.

## Where to find it

- **Dashboard:** `/parts`
- **Inventory:** `/parts/inventory`
- **Issuance history:** `/parts/issuances`
- **View:** `resources/views/parts/index.blade.php`
- **Controller:** `app/Http/Controllers/PartController.php`

## Inputs and what they mean

### Add a part

| Input | Required | Meaning and behavior |
|---|---|---|
| Part Code / Item # (`part_code`) | Yes | Unique store identifier; maximum 50 characters. |
| Category (`category`) | Yes | Category used to classify the part. The form offers Gripper, Rapier, Heald & Dropper, Sensor, Mechanical, Electrical, and Consumables. |
| Part Name / Specification (`part_name`) | Yes | Human-readable name/specification; maximum 150 characters. |
| Opening Stock (`stock_quantity`) | Yes | Initial whole-number stock quantity; zero or more. |
| Low Alert Limit (`min_alert_threshold`) | Yes | Whole-number threshold at or below which the part appears as low stock. |
| Unit (`unit`) | Yes | Unit label such as `pcs`, `set`, or `kg`; maximum 20 characters. |
| Unit Cost (`unit_price`) | Yes | Cost per unit in rupees; zero or more. |
| Rack / Bin Location (`rack_location`) | No | Store location; maximum 100 characters. |
| Notes / Description (`notes`) | No | Compatibility or other part notes. |

Creating a part adds it to the store inventory. It does not create a stock purchase transaction or supplier ledger entry.

### Issue a part

| Input | Required | Meaning and behavior |
|---|---|---|
| Select Part from Store (`part_id`) | Yes | Catalog item to issue. The form displays the available stock and unit. |
| Target Loom # (`loom_no`) | Yes | Loom receiving the part; the page offers Loom-01 through Loom-24. |
| Issuance Date (`issuance_date`) | Yes | Date of the stock issue. |
| Quantity to Issue (`quantity`) | Yes | Positive whole-number quantity. The request is rejected if stock is insufficient. |
| Issued to (Person) (`issued_to_person`) | Yes | Responsible recipient; maximum 150 characters. |
| Reason / Maintenance Remarks (`remarks`) | No | Optional reason or maintenance details. |

The issuance stores the part's current unit price and calculates `quantity × unit price` as the issuance cost. It then decrements on-hand stock.

## What the module displays

### Dashboard summary

- Cataloged item count and total units in stock.
- Count of part records at or below their minimum alert threshold.
- Inventory valuation (`stock quantity × unit price`, summed).
- Total number of issuances and total recorded issuance cost.
- Recent inventory and issuance history.

### Inventory

Shows part code, name, category, stock quantity/unit, minimum threshold, unit price, calculated stock value, rack location, and notes. Low-stock rows are highlighted. The dashboard shows recent parts; the inventory page is paginated.

### Issuances

Shows the part, loom, recipient, quantity and unit, issue date, stored unit price, total cost, and remarks. The dashboard shows recent issues; the issuance register is paginated.

## End-to-end workflow

1. Add each part with a unique item code, opening quantity, unit, cost, and low-stock threshold.
2. Use the inventory page to review stock and low-stock alerts.
3. Record an issue by selecting the part, loom, quantity, person, and date.
4. The issue is rejected if the requested quantity exceeds the item's current stock. On success, the issuance is recorded and inventory decreases.
5. Review the dated history in the Issuances section.

## Routes

| Method | Path | Purpose |
|---|---|---|
| GET | `/parts` | Parts dashboard. |
| GET | `/parts/inventory` | Paginated store inventory. |
| GET | `/parts/issuances` | Paginated issue history. |
| POST | `/parts` | Add a part to inventory. |
| POST | `/parts/issue` | Issue part stock to a loom. |

All routes require authentication.

## Main implementation files

- `routes/web.php` — Parts routes.
- `app/Http/Controllers/PartController.php` — page metrics, validation, stock checks, cost calculation, and stock decrement.
- `app/Models/Part.php` — part catalog and inventory attributes.
- `app/Models/PartIssuance.php` — dated issuance record and part relationship.
- `resources/views/parts/index.blade.php` — dashboard, inventory, and issuance forms/tables.
- `database/migrations/2026_10_16_095054_create_parts_table.php` and `2026_10_16_095055_create_part_issuances_table.php` — table schemas.
