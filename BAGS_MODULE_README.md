# Bags (Yarn) Management Module

## Module name

**Bags (Yarn) Management & Stock**

## Short description

The Bags module records yarn-bag purchases, tracks the split between direct-to-sizing deliveries and factory-store stock, logs issues and returns, and shows sizing-slip consumption. It also records yarn purchases in the supplier ledger.

All Bags routes require an authenticated user.

## Where to find it

- **Dashboard:** `/bags` — stock and investment summary plus the latest purchase, movement, and sizing activity.
- **Purchases:** `/bags/purchases` — paginated purchase and dispatch-split history.
- **Stock ledger:** `/bags/ledger` — paginated bag movement history.
- **Sizing usage:** `/bags/sizing-usage` — paginated sizing-slip usage, returns, and held-bag history.

The page's sidebar and quick-action buttons open these sections and the purchase, issue, return, and yarn-quality forms.

## Inputs and what they mean

### New bag purchase

Available from **New Purchase** or **New Bag Purchase (Split)**.

| Input | Required | Meaning and behavior |
|---|---|---|
| Challan / Invoice # (`challan_no`) | Yes | Supplier invoice or delivery reference. Maximum 100 characters. It is also used as the reference for the resulting stock movements and supplier-ledger entry. |
| Purchase Date (`purchase_date`) | Yes | Date of the purchase and the date used for its stock and ledger records. |
| Spinning Mill / Supplier (`supplier_name`) | Yes | Supplier's name; maximum 150 characters. |
| Yarn Quality / Count (`quality`) | Yes | Yarn quality selected from the shared quality list. The quality must already exist; use **+ Create** to add it. |
| Total Bags Purchased (`total_bags`) | Yes | Whole number of bags purchased; at least 1. |
| Rate Per Bag (Rs.) (`rate_per_bag`) | Yes | Purchase price for one bag in rupees; must be zero or more and can include decimals. |
| Direct to Sizing (`bags_to_sizing`) | Yes | Whole-number quantity delivered directly to a sizing mill. This portion does not enter factory-store stock. |
| To Factory Godown (`bags_to_factory`) | Yes | Whole-number quantity received into the factory yarn store. |
| Sizing Mill / Contractor (`sizing_destination`) | Conditional | Required when any bags are sent directly to sizing. Names the sizing recipient and appears in the movement and pending-sizing records. |
| Notes / Contract Details (`notes`) | No | Optional purchase, contract, or dispatch details. |

The two split quantities must add up exactly to the total purchase quantity. When a total is entered and both split quantities are still zero, the form initially proposes a roughly even split; staff can change either amount. The total purchase value is calculated as `total bags × rate per bag` and is not entered separately.

Saving a purchase creates the purchase record, creates a stock movement for each non-zero destination, and records the purchase amount against the supplier in the ledger.

### Issue bags to sizing

Available from **Issue Bags** or **Issue Bags Daily**. This issues bags from factory stock to a sizing recipient.

| Input | Required | Meaning and behavior |
|---|---|---|
| Issue Slip / Ref # (`reference_no`) | Yes | Reference for this issue; maximum 100 characters. |
| Date (`transaction_date`) | Yes | Date the stock movement occurred. |
| Quantity to Issue (`quantity_bags`) | Yes | Quantity in bags; must be greater than zero and have no more than two decimal places. The form shows an approximate weight using 100 lb per bag. |
| Yarn Quality (`quality`) | Yes | Quality to issue. Factory availability is checked for this quality before saving. |
| Sizing Mill / Contractor (`destination`) | Yes | Destination of the bags; maximum 150 characters. |
| Contractor / Person Name (`party_name`) | Yes | Person or party receiving the bags; maximum 150 characters. |
| Remarks (`notes`) | No | Optional issue details. |

The issue is rejected if the factory store does not have enough bags of the selected quality. A successful issue reduces factory stock and creates a pending sizing receipt that can be selected in the Sizing module.

### Receive unused bags back

Available from **Receive Return** or **Receive Bags Return**. This records bags coming back into the factory yarn store.

| Input | Required | Meaning and behavior |
|---|---|---|
| Return Slip / Ref # (`reference_no`) | Yes | Return document reference; maximum 100 characters. |
| Date (`transaction_date`) | Yes | Date the return was received. |
| Quantity Returned (`quantity_bags`) | Yes | Returned quantity; must be greater than zero and have no more than two decimal places. |
| Yarn Quality (`quality`) | Yes | Quality of the returned yarn bags; choose an existing quality or add one with **+ Create**. |
| Returning Sizing Mill / Party (`source`) | Yes | Sizing mill or party sending the bags back; maximum 150 characters. |
| Contractor / Person Name (`party_name`) | Yes | Person associated with the return; maximum 150 characters. |
| Reason / Notes (`notes`) | No | Optional explanation, such as surplus bags from a completed beam set. |

A successful return creates a stock movement into the factory yarn store. The return form records the movement but does not ask the user to select a specific earlier issue or sizing slip.

### Manage yarn qualities

The shared **Yarn Quality List** modal is opened by **Manage Yarn Qualities** or **+ Create** beside a quality selector.

| Input | Required | Meaning and behavior |
|---|---|---|
| New Yarn Quality (`name`) | Yes | Quality/count label used by purchase, issue, return, and sizing forms. Maximum 100 characters; duplicate names are not allowed. |

After a new quality is saved, it is added to the quality list and selected in the quality selectors on the page.

## What the module displays

### Dashboard summary

- **Total Bags Purchased** — total purchased bag count, with total yarn investment shown in rupees.
- **Factory Stock Store** — calculated factory-store balance.
- **With Sizing Persons** — calculated sizing balance.
- **Bags Used In Beams** — total consumption recorded on sizing slips.
- **Unused Bags Status** — sizing-slip quantities reported as returned to the factory or held with sizing.

The dashboard also shows the five latest purchase records, stock transactions, and sizing slips. **View all** opens the corresponding paginated history.

### Purchase history

Shows challan number, purchase date, supplier, yarn quality/count, total bags, direct-to-sizing quantity and recipient, factory-store quantity, rate per bag, and total amount. Direct-sizing quantity also has an approximate weight display.

### Stock ledger

Shows transaction date and type, reference, yarn quality, quantity, source, destination, party/mill, and notes. Transaction types include factory purchase, direct-to-sizing purchase, issue to sizing, and return from sizing.

### Sizing usage

Shows sizing slip number, sizing party, quality, allocated bags, bags used in beams, bags returned to the factory, bags held with sizing, beams/length, and slip status. These consumption and reconciliation values are entered through the **Sizing** module, not through the Bags purchase, issue, or return forms. The **Open Sizing** link takes the user there.

## End-to-end workflow

1. Add any missing yarn quality from **Manage Yarn Qualities**.
2. Record a purchase with its supplier, challan, date, quality, quantity, and rate.
3. Split the purchased quantity between direct-to-sizing delivery and factory-store receipt. The split must equal the total.
4. Direct-to-sizing quantities create a sizing receipt in the movement history. Factory quantities increase the available factory stock.
5. Use **Issue Bags** to send factory stock to a sizing mill. The selected quality's available stock is checked before the issue is saved.
6. In the **Sizing** module, select a pending direct purchase or issue receipt to create a sizing slip. The receipt's quantity, quality, and sizing party are prefilled. Record usage, returns, and held bags on the sizing slip.
7. Use **Receive Return** to separately record unused bags physically received back into the factory yarn store.
8. Review balances and history from the Bags dashboard, purchase history, stock ledger, and sizing-usage page.

## How balances are calculated

- Factory stock is calculated from factory-directed purchase movements, minus issues to sizing, plus recorded returns.
- Sizing stock is calculated from direct-to-sizing purchases plus factory issues, minus bags used on sizing slips and returns recorded as returned to the factory.
- Bags held with sizing are reported from sizing slips as a separate unused-bag total.
- The dashboard calculations are aggregate totals; issue authorization checks available factory stock by the selected yarn quality.

Each bag is displayed as an approximate **100 lb / 45.359237 kg** for weight previews. Bag movement quantities may include up to two decimal places.

## Main implementation files

- `routes/web.php` — authenticated Bags and yarn-quality routes.
- `app/Http/Controllers/BagController.php` — page data, validation, purchase/issue/return handling, and balance calculations.
- `app/Http/Controllers/YarnQualityController.php` — adds a shared yarn quality.
- `app/Models/BagPurchase.php` — purchase records.
- `app/Models/BagTransaction.php` — stock movement records and sizing-slip relationship.
- `resources/views/bags/index.blade.php` — dashboard, histories, and purchase/issue/return forms.
- `resources/views/yarn-qualities/modal.blade.php` — shared yarn-quality list and add form.
- `app/Http/Controllers/SizingController.php` and `resources/views/sizing/` — pending sizing receipts and sizing-slip consumption/reconciliation.
- `database/migrations/` — schemas for bag purchases, bag transactions, sizing slips, and yarn qualities.

## Routes

| Method | Path | Purpose |
|---|---|---|
| GET | `/bags` | Dashboard |
| GET | `/bags/purchases` | Purchase history |
| GET | `/bags/ledger` | Stock movement history |
| GET | `/bags/sizing-usage` | Sizing usage history |
| POST | `/bags/purchase` | Save purchase and dispatch split |
| POST | `/bags/issue` | Issue factory stock to sizing |
| POST | `/bags/return` | Record unused bags returned to factory |
| POST | `/yarn-qualities` | Add a shared yarn quality |
