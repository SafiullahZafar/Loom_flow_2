# Production Module

## Module name

**Loom-wise Production, Roll Register & Efficiency**

## Short description

The Production module records woven output by loom and shift, tracks consumption of a mounted sizing beam, calculates expected output/leakage/efficiency, and adds finished fabric to stock. A separate daily roll-register entry records finished rolls by loom and updates the daily fabric-stock movement.

## Where to find it

- **Dashboard:** `/production`
- **Loom summary:** `/production/loom-summary`
- **Leakage analysis:** `/production/leakage`
- **Production history:** `/production/history`
- **Controller:** `app/Http/Controllers/ProductionController.php`
- **View:** `resources/views/production/index.blade.php`

## Inputs and what they mean

### Record loom shift production

| Input | Required | Meaning and behavior |
|---|---|---|
| Production Date (`production_date`) | Yes | Calendar date for the shift record. |
| Shift (`shift`) | Yes | `Day` or `Night`. |
| Loom # (`loom_no`) | Yes | Loom generating the output; the form offers Loom-01 through Loom-24. |
| Operator / Weaver Name (`operator_name`) | Yes | Person responsible for the shift; maximum 150 characters. |
| Mounted Sizing Beam (`sizing_beam_id`) | Yes | Beam currently mounted on the selected loom. |
| Fabric Quality Being Woven (`fabric_stock_id`) | Yes | Finished-fabric quality to receive the produced meters. |
| Starting Meter (`start_meter`) | Yes | Loom fabric-counter reading at the beginning of the entry; zero or more. |
| Ending Meter (`end_meter`) | Yes | Counter reading at the end; must be greater than the starting reading. |
| Beam Consumed (`beam_meters_consumed`) | Yes | Warp-beam length consumed; at least 1 meter and no greater than the beam's remaining length. |
| Loom Speed (`rpm`) | Yes | Loom revolutions per minute; 100–800. |
| Picks Per Inch (`picks_per_inch`) | Yes | Fabric pick density; 20–200. |
| Shift Notes / Stoppages Remarks (`remarks`) | No | Optional stoppage, run, or production notes. |

The form may preview calculated output, leakage, and efficiency, but the controller recalculates and validates the saved values. The selected beam must be mounted on the selected loom, and the consumed length cannot exceed its remaining warp length.

### Daily loom roll register

Use **Enter daily rolls** when the daily count is provided as rolls/meters rather than full loom-shift meter-counter and beam-consumption details. This form posts through the same production endpoint using its `rolls` array.

| Input | Required | Meaning and behavior |
|---|---|---|
| Date (`production_date`) | Yes | Date assigned to every roll row in the entry. |
| Fabric quality (`fabric_stock_id`) | Yes | Finished-fabric quality receiving the rolled meters. |
| Loom / roll number (`rolls[*][loom_no]`) | Yes per row | Loom or roll identifier recorded for that register row. |
| Roll meters (`rolls[*][meters]`) | Yes per row | Positive meters for the roll. Add a row for each roll. |
| Remarks (`remarks`) | No | Shared note on the register entry; maximum 1000 characters. |

The displayed total meters is the sum of the roll rows. The controller creates production rows for each roll and updates the fabric stock movement for that date. Register-created rows are not beam/shift measurements.

### History filters

| Input | Required | Meaning and behavior |
|---|---|---|
| Loom (`loom_no`) | No | Filters history to one loom. |
| Date (`date`) | No | Filters history to one production date. |

The filters can be combined and are preserved when navigating the paginated results.

## Calculations

For shift production:

- **Net meters produced** = ending meter − starting meter.
- **Expected fabric meters** = beam meters consumed × sizing-slip panna/width multiplier.
- **Leakage/waste meters** = `max(0, expected fabric meters − net meters produced)`.
- **Leakage percentage** = leakage ÷ expected fabric meters × 100, when expected meters are positive.
- **Efficiency** compares actual net meters to a theoretical estimate based on RPM, PPI, and the application's loom constants; the saved percentage is capped below 100%.

Saving a shift:

1. Creates a production record with the calculated results and selected fabric quality.
2. Subtracts consumed meters from the sizing beam; when none remain, the beam becomes exhausted.
3. Adds net produced meters to current and total fabric stock and to the day's received meters.
4. Recalculates open/committed fabric meters against active contracts and adds an estimated roll count.

The roll-register workflow adds the submitted meters to fabric stock and records one production entry per roll. It does not consume a sizing beam.

## What the module displays

- Dashboard KPIs for total produced fabric, beam consumed, leakage meters/percentage, and average efficiency.
- Active mounted beams available for production entry.
- Expected-versus-actual production by beam.
- Loom-wise production and leakage summary, with shift count, meters, expected output, leakage, and average efficiency.
- Leakage analysis by beam.
- Production history with optional loom/date filters.
- Daily roll register with date and fabric-quality filters.

## End-to-end workflow

1. Generate a sizing slip and ready beams in [SIZING_MODULE_README.md](./SIZING_MODULE_README.md).
2. Assign a beam to its loom in Sizing; only mounted beams on that loom can be used for a shift record.
3. Register shift counter readings, beam consumption, loom speed, PPI, and operator.
4. Review the saved output, leakage, efficiency, beam remainder, and fabric-stock update.
5. If the operation is entered as finished rolls rather than shift readings, use the daily roll register instead.
6. Review finished-goods meters and availability in [FABRIC_STOCK_MODULE_README.md](./FABRIC_STOCK_MODULE_README.md).

## Routes

| Method | Path | Purpose |
|---|---|---|
| GET | `/production` | Production dashboard and recent shifts. |
| GET | `/production/loom-summary` | Loom-wise summary. |
| GET | `/production/leakage` | Beam leakage analysis. |
| GET | `/production/history` | Production history; accepts optional `loom_no` and `date` query parameters. |
| POST | `/production` | Save a loom shift record or a daily roll-register batch. |

All routes require authentication.

## Main implementation files

- `routes/web.php` — Production routes.
- `app/Http/Controllers/ProductionController.php` — production validation/calculations, beam consumption, stock updates, roll-register handling, and reporting queries.
- `app/Models/Production.php` — production values and beam/fabric relationships.
- `app/Models/SizingBeam.php` — remaining beam length and status.
- `app/Models/FabricStock.php` and `app/Models/FabricStockMovement.php` — finished-fabric balance and dated stock movement.
- `resources/views/production/index.blade.php` — dashboard, filters, shift form, roll register, and analysis tables.
- `database/migrations/2026_10_16_095059_create_productions_table.php` and later production migrations — production and calculation fields.
