# Sizing Yarn-Bag Reconciliation Guide

## Where the shortage appears

On the **Sizing** page, the sizing person enters a slip and reviews the **Bags Reconciliation** section. The system calculates the result and shows a balance status; there is no separate field where the user types a shortage amount.

The bag quantity sent is filled from the selected purchase or factory issue and is read-only. The sizing person records:

| Sizing slip field | What to enter |
|---|---|
| **Used in Beams** | Bags actually consumed to prepare the warp/beams. |
| **Returned to Factory** | Unused bags physically returned to the factory. |
| **Held with Sizing** | Unused bags still physically held at the sizing location. |

Enter quantities from the gate pass and the sizing person's physical count. Do not count the same bag as both returned and held.

## How the system calculates the bag shortage

```text
Bag variance = bags sent − bags used − bags returned − bags held with sizing
```

| Result | Balance status | Meaning |
|---|---|---|
| `0.00` | **Balanced** | Every bag sent is accounted for as used, returned, or still held. |
| Greater than `0.00` | **Unaccounted** | This many bags have not been accounted for in the entered quantities. |
| Less than `0.00` | **Over-reported** | Used, returned, and held quantities add up to more bags than were sent. Check the records and entries. |

**Example:** 20 bags sent − 15 used − 2 returned − 1 held = **2 bags unaccounted**.

An unaccounted result is the system's bag-shortage indicator. It identifies a quantity difference, not its cause. The sizing slip can still be saved with a mismatch, so the responsible staff should verify it against the signed gate pass, return record, and physical stock.

## Important distinction

This calculation is in **bags**. LoomFlow does not use it to calculate an actual shortage in kilograms of yarn; the app does not compare independently measured yarn weights issued and received at the sizing location. The Bags screen's estimate of **100 lb per bag** is a display conversion, not a physical weighing or the shortage formula.

Fabric output has a separate variance: expected cloth from warp length and panna is compared with actual production. That is a cloth-output check, not the sizing person's yarn-bag shortage. It remains provisional until every beam from the slip is exhausted.

See [SIZING_MODULE_README.md](./SIZING_MODULE_README.md) for all sizing fields and workflow, and [BAGS_MODULE_README.md](./BAGS_MODULE_README.md) for purchase, issue, and return records.
