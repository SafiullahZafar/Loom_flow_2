# Sizing Module

## Module name

**Sizing Slips, Beam Tracking & Bag Reconciliation**

## Short description

The Sizing module turns a pending bag delivery into a sizing slip, records bag consumption and returned materials, generates finished warp beams, and tracks beams from the store onto looms through exhaustion. It also displays bag reconciliation and expected beam/fabric measurements.

## Where to find it

- **Dashboard:** `/sizing`
- **Beam register:** `/sizing/beams`
- **Sizing slip register:** `/sizing/slips`
- **Balance and reconciliation:** `/sizing/balance`

The forms and tables are in `resources/views/sizing/index.blade.php`. A sizing slip must originate from a pending bag transaction in the Bags module.

## Inputs and what they mean

### New sizing slip

| Input | Required | Meaning and behavior |
|---|---|---|
| Perch entry type (`slip_type`) | Yes | `raw_perch` records gate-pass and beam data; `final_perch` records the sizing invoice and returned-material deductions. |
| Bag receipt / issue being sized (`bag_transaction_id`) | Yes | Selects a pending direct-to-sizing purchase or a factory issue. A transaction can be linked to one sizing slip only. |
| Sizing Slip # (`slip_no`) | Yes | Unique slip reference; maximum 100 characters. |
| Slip Date (`slip_date`) | Yes | Date used for the slip and any automatic unused-bag return movement. |
| Sizing Unit / Party (`sizing_party`) | Yes in form | Recipient mill/person. The saved party is taken from the selected bag transaction's destination, party, or source. |
| Yarn Quality (`yarn_quality`) | Yes in form | Selects the yarn count/quality. The saved quality is taken from the selected bag transaction. Add a missing quality using **+ Create**. |
| Total Bags Given to Sizing (`bags_allocated`) | Yes, read-only in form | Prefilled from the chosen receipt/issue and replaced with that transaction's quantity when saved. |
| Used in Beams (`bags_used`) | Yes | Quantity of allocated bags consumed in warp/beam preparation. Allows two decimal places. |
| Returned to Factory (`bags_returned_to_factory`) | No | Unused quantity physically returned. Saving a positive amount also creates a factory-store return movement automatically. |
| Held with Sizing (`bags_held_with_sizing`) | No | Unused quantity still held at the sizing location. |
| Raw / Back Beams (`back_beam_count`) | No | Number of incoming/back beams, when recorded. |
| Finished Full Beams Made (`total_beams`) | Yes | Number of output beams; must be between 1 and 100. The module creates this many beam records. |
| Set Warp Length (`set_length_value`) | Yes | Gate-pass warp length for the entire sizing set; must be greater than zero. |
| Set length unit (`set_length_unit`) | Yes | `meters` or `gaz`. Gaz is converted to meters using 0.9144 meters per gaz. |
| Fabric Panna (`width_multiplier`) | Yes | Arrangement factor: single panna is 1, double panna is 2. Multiplies warp length to calculate expected fabric length. |
| Total Ends / Taarein (`total_ends`) | Yes in form | Total warp ends from the gate pass. The controller also accepts reed count and fabric width, with optional selvedge ends, to derive the total when omitted. |
| Gross Weight on Sizing Bill (`sizing_gross_weight_kg`) | Optional for raw; required for final perch | Gross billed yarn weight before returned-material deductions. |
| Net Warp Yarn Weight (`sizing_weight_kg`) | Optional for raw | Net warp weight in kilograms used for the count reconciliation. For final perch, the net weight is calculated from gross less returned materials. |
| Notes (`notes`) | No | Optional sizing-process or tension notes. |

The reconciliation is `allocated bags − used bags − returned bags − held bags`. A mismatch is flagged for review; the slip may still be saved. It is a record discrepancy, not by itself evidence of misconduct.

### Final Perch invoice-only fields

These fields are shown when **Final Perch** is selected. Enter the count and weight of the materials returned from sizing; the weights are deducted from the gross billed weight.

| Input | Required | Meaning and behavior |
|---|---|---|
| Used cones-bags count (`wet_cone_bags`) | No | Count of returned used cone bags. |
| Used cones-bags weight (`wet_cone_weight_kg`) | No | Kilograms of returned used cone bags. |
| Empty bags count (`empty_bag_count`) | No | Count of returned empty yarn bags. |
| Empty bags weight (`empty_bag_weight_kg`) | No | Kilograms of returned empty bags. |
| Gatta-bags count (`paper_bag_count`) | No | Count of returned paper/gatta bags. |
| Gatta-bags weight (`paper_bag_weight_kg`) | No | Kilograms of returned paper/gatta bags. |
| Salim/unused cones count (`sound_cone_count`) | No | Count of returned unused cones. |
| Salim/unused cones weight (`sound_cone_weight_kg`) | No | Kilograms of returned unused cones. |
| Cartons count (`carton_count`) | No | Number of returned cartons. |
| Cartons weight (`carton_weight_kg`) | No | Kilograms of returned cartons. |
| Sizing rate (`sizing_rate_per_kg`) | Required for final perch | Rupee charge per net kilogram. |

For final perch, **net sizing weight = gross bill weight − all returned-material weights**. The deductions must be less than the gross weight; the resulting net weight must be positive. **Sizing bill amount = net sizing weight × rate per kg**. Raw perch sets returned-material deductions to zero and does not create this final sizing bill.

### Mount a beam on a loom

| Input | Required | Meaning and behavior |
|---|---|---|
| Beam Selected | Display only | The beam selected from the beam register; it is not editable in the mount action. |
| Assign to Loom # (`assigned_loom_no`) | Yes | Target loom, selected from the application's Loom-01 through Loom-24 list. |
| Mount Date (`mounted_date`) | Yes | Date the beam is put on the loom. |

Only a non-exhausted beam with remaining length can be mounted. Exhausted beams cannot be assigned.

### Mark a beam exhausted

The **Exhaust Beam** action requires no extra form fields. It marks the selected beam `exhausted` and sets its remaining length to zero.

## What the module displays

- **Dashboard:** recent sizing slips and beams; allocated, used, returned, and held bags; beam counts by status; total beam length; and remaining mounted-beam length.
- **Bags Waiting for Sizing Slips:** eligible bag receipts/issues that have not yet been linked to a slip.
- **Beam register:** beam number, quality, source slip, original and remaining length, status, assigned loom, and production progress.
- **Sizing slip register:** party, yarn quality, bag reconciliation, beam count and length, status, and weight/count details.
- **Balance page:** slips and beam production totals used to compare expected fabric, beam consumption, and leakage.

## End-to-end workflow

1. Record a yarn purchase or factory-to-sizing issue in [BAGS_MODULE_README.md](./BAGS_MODULE_README.md).
2. Select its pending movement in the New Sizing Slip form. Party, quality, and allocated quantity are filled from that source movement.
3. Enter the bag reconciliation, gate-pass length, panna, warp ends, and weights. Choose raw or final perch depending on whether a sizing invoice is being recorded.
4. Save the slip. The system links the source movement, generates one ready-in-store beam per finished beam count, and records positive bag returns back to the factory.
5. Mount a ready beam onto its loom. Record loom output in [PRODUCTION_MODULE_README.md](./PRODUCTION_MODULE_README.md).
6. Mark the beam exhausted when it is finished.

## Calculations and units

- Gaz length is converted to meters at `0.9144 m per gaz`.
- Expected fabric per slip is `set length in meters × panna factor`.
- Expected fabric per beam is expected fabric per slip divided by finished beam count.
- Warp count is estimated from total ends, set length, and net yarn weight as a reconciliation check. It does not modify the declared yarn quality.
- Bag weight previews use 100 lb (45.359237 kg) per bag.
- If used + returned + held does not equal allocated, the slip records a variance notice rather than rejecting the slip.

## Routes

| Method | Path | Purpose |
|---|---|---|
| GET | `/sizing` | Sizing dashboard and recent slips/beams. |
| GET | `/sizing/beams` | Beam register. |
| GET | `/sizing/slips` | Sizing slip register. |
| GET | `/sizing/balance` | Sizing reconciliation and balance view. |
| POST | `/sizing/slip` | Save a sizing slip and generate its beams. |
| POST | `/sizing/beams/{beam}/assign` | Mount/assign a beam to a loom. |
| POST | `/sizing/beams/{beam}/exhaust` | Mark a beam exhausted. |

## Main implementation files

- `routes/web.php` — Sizing routes.
- `app/Http/Controllers/SizingController.php` — page queries, slip validation, calculations, beam generation, assignment, and exhaustion.
- `app/Models/SizingSlip.php` — slip data, bag receipt relation, and expected-length/count helpers.
- `app/Models/SizingBeam.php` — beam record and production relationship.
- `app/Models/BagTransaction.php` — source movement and one-slip relationship.
- `resources/views/sizing/index.blade.php` — sizing dashboard, forms, beam controls, and registers.
- `database/migrations/` — sizing slip and sizing beam schemas and calculation-field migrations.
