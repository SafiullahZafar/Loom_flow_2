# Sales Module

## Module name

**Fabric Sales Slips & Buyer Contracts**

## Short description

The Sales module records open-market and contract fabric dispatches, calculates sale totals, updates fabric availability, and synchronizes buyer details to the customer list. It also creates conversion/grey-sale contracts, calculates their cost estimates, reserves fabric meters, and tracks contract deliveries.

## Where to find it

- **Dashboard:** `/sales`
- **Sales slips:** `/sales/slips`
- **Contracts:** `/sales/contracts`
- **Controller:** `app/Http/Controllers/SalesController.php`
- **View:** `resources/views/sales/index.blade.php`

## Inputs and what they mean

### Create a sales slip

| Input | Required | Meaning and behavior |
|---|---|---|
| Sale Dispatch Type (`sale_type`) | Yes | `open_market` sells from open stock; `contract` dispatches against a selected active contract. |
| Slip / Invoice # (`slip_no`) | Yes | Unique sales-slip reference; maximum 100 characters. |
| Sale Date (`sale_date`) | Yes | Date of the fabric dispatch and buyer-ledger entry. |
| Select Active Contract (`sales_contract_id`) | For contract sale | Contract whose remaining meters and reserved stock will be reduced. |
| Buyer / Party Name (`buyer_name`) | Yes | Buyer for an open-market sale. For a contract sale, the saved buyer is taken from the contract. |
| Buyer Phone (`buyer_phone`) | No | Buyer phone for an open-market sale. For a contract sale, the contract phone is used. |
| Fabric Quality (`fabric_stock_id`) | Yes | Registered fabric quality dispatched. A contract sale must use the contract's quality. |
| Meters Sold (`meters_sold`) | Yes | Quantity of finished fabric; at least 1 meter. |
| Rolls Count (`rolls_sold`) | Yes | Positive whole-number count of rolls/thaans dispatched. |
| Rate / Meter (`rate_per_meter`) | Yes | Rupees per meter for an open-market sale. For a contract sale, the contract rate is used. |
| Discount (`discount`) | No | Non-negative rupee discount. |
| Payment Type (`payment_type`) | Yes | `cash`, `credit`, or `bank_transfer`. |
| Payment Status (`payment_status`) | Yes | `paid`, `partial`, or `unpaid` status at time of sale. |
| Vehicle # / Gate Pass Notes (`vehicle_no`) | No | Optional vehicle or dispatch gate-pass reference. |
| Notes (`notes`) | No | Optional sale remarks. |

The subtotal is `meters sold × rate per meter`; the net total is the subtotal less the discount (with a floor of zero). The saved sale reduces fabric meters and roll count. For a contract sale, the contract's delivered and remaining meters are updated; a completed contract is marked completed. The sale is recorded in the buyer ledger and the buyer is synchronized to the customer list.

Open-market sales require sufficient open-market meters. Contract sales require the selected contract to match the fabric quality and enough unreserved/current stock for the requested quantity.

### Create a buyer contract

Contract form fields are grouped below by purpose. Fields marked optional may be left blank; controller validation still limits their type and size.

#### Contract and delivery

| Input | Required | Meaning |
|---|---|---|
| Contract # (`contract_no`) | Yes | Unique internal contract reference; maximum 100 characters. |
| Party Contract # (`party_contract_no`) | No | Buyer's external contract reference; maximum 80 characters. |
| Contract Date (`start_date`) | Yes | Contract start date. |
| Delivery Date (`end_date`) | No | Promised end/delivery date; cannot be before the start date. |
| Contract Type (`contract_type`) | Yes | `Conversion` or `Grey Sale`. |
| Loom Type (`loom_type`) | No | Loom process/type label, such as rapier or air jet. |

#### Buyer and broker

| Input | Required | Meaning |
|---|---|---|
| Buyer Name (`buyer_name`) | Yes | Buyer/party name; used to synchronize a customer record. |
| Buyer Code (`buyer_code`) | No | Buyer's code/reference. |
| Buyer Phone (`buyer_phone`) | No | Contact phone; may be copied to the synchronized customer record if its phone is blank. |
| GST / Tax # (`buyer_gst`) | No | Buyer tax identifier. |
| Broker Name (`broker_name`) | No | Broker contact/name. |
| Broker Code (`broker_code`) | No | Broker's code/reference. |
| Commission / Meter (`broker_commission_per_meter`) | No | Non-negative rupee commission per meter; defaults to zero. |

#### Fabric quality and construction

| Input | Required | Meaning |
|---|---|---|
| Fabric Quality / Stock Quality (`fabric_stock_id`) | Yes | Existing fabric stock record tied to this contract. The stored fabric quality name is copied onto the contract. |
| Quality Code (`quality_code`) | No | Optional internal quality code. |
| Contract Quality Detail (`contract_quality`) | No | Buyer-facing specification text. |
| Quality Width (`contract_quality_width`) | No | Optional contract-specific width in inches. |
| Panna (`panna`) | No | Contract width arrangement; defaults to 1 if omitted. |
| Read / EPI (`read`) | Yes | Ends per inch used for fabric/yarn estimates. |
| Pick / PPI (`pick`) | Yes | Picks per inch used for fabric/yarn and conversion calculations. |
| Warp Count (`warp_count`) | Yes | Warp yarn count used in the estimate. |
| Weft Count (`weft_count`) | Yes | Weft yarn count used in the estimate. |
| Fabric Width (`width`) | Yes | Width in inches used in calculated weights and width/GSM estimates. |

#### Quantity, rates, and terms

| Input | Required | Meaning |
|---|---|---|
| Agreed Meters (`total_meters`) | Yes | Contract quantity, at least 100 meters. |
| Warp Yarn Rate (`yarn_warp_rate`) | Yes | Non-negative rupee rate per pound for warp yarn. |
| Weft Yarn Rate (`yarn_weft_rate`) | Yes | Non-negative rupee rate per pound for weft yarn. |
| Conversion / Pick (`conversion_rate`) | Yes | Non-negative conversion charge used per pick. |
| KP % / KP Days (`kp_percentage`, `kp_days`) | No | Contract percentage and day terms recorded as entered; percentages range 0–100 and days 0–3650. |
| PP % / PP Days (`pp_percentage`, `pp_days`) | No | Additional contract percentage and day terms recorded as entered; percentages range 0–100 and days 0–3650. |
| Delivery Instructions (`delivery_instructions`) | No | Delivery terms; maximum 2000 characters. |
| Payment Instructions (`payment_instructions`) | No | Payment terms; maximum 2000 characters. |
| Quality Instructions (`quality_instructions`) | No | Quality requirements; maximum 2000 characters. |
| Other Instructions / Notes (`other_instructions`) | No | Other buyer-facing terms; maximum 2000 characters. |
| Internal Contract Notes (`notes`) | No | Internal notes; maximum 2000 characters. |

The contract rate and total amount are calculated, not manually entered. The estimate derives warp/weft pounds per meter from read, pick, width, and yarn counts, prices those weights using the warp/weft rates, and adds `pick × conversion rate`. Contract amount is rate per meter multiplied by agreed meters. A 17% sales-tax estimate is stored/displayed separately. These values are estimates for contract costing.

Saving a contract creates it as active with zero delivered meters and all agreed meters remaining. The associated fabric's active contract meters are reserved, capped by current fabric stock; open meters are recalculated.

## What the module displays

### Dashboard

- Net sales revenue and meters dispatched.
- Contract-sale revenue and open-market revenue.
- Active contract count and total delivered-versus-agreed contract meters.
- Recent sales slips and contracts.

### Sales slips

Lists slip number, date, sale type, buyer, fabric quality, meters, rolls, rate, discount, net total, payment method/status, and contract link where applicable.

### Contracts

Lists internal and party contract references, buyer, fabric quality, contract type, quantity, delivered and remaining meters, calculated rate/amount, dates, and active/completed status.

## End-to-end workflow

1. Register a fabric quality and maintain stock in [FABRIC_STOCK_MODULE_README.md](./FABRIC_STOCK_MODULE_README.md).
2. For a long-term order, create a contract with its buyer, quality, specifications, quantity, and costing inputs. The contract reserves available meters.
3. Record each contract delivery as a contract sales slip. The system uses the contract buyer/rate, consumes reserved stock, and advances contract delivered/remaining meters.
4. For a spot order, choose open market and dispatch only from available open meters.
5. Review slips and contracts in their registers. Invoices for eligible sales and contract deliveries are created in [INVOICES_MODULE_README.md](./INVOICES_MODULE_README.md).

## Routes

| Method | Path | Purpose |
|---|---|---|
| GET | `/sales` | Sales dashboard. |
| GET | `/sales/slips` | Sales-slip register. |
| GET | `/sales/contracts` | Contract register. |
| POST | `/sales` | Save an open-market or contract sale. |
| POST | `/sales/contract` | Create and reserve a buyer contract. |

All routes require authentication.

## Main implementation files

- `routes/web.php` — Sales routes.
- `app/Http/Controllers/SalesController.php` — sale/contract validation, stock and delivery updates, buyer synchronization, and ledger posting.
- `app/Models/Sale.php` — sales-slip amounts and relationships.
- `app/Models/SalesContract.php` — contract fields and costing calculations.
- `app/Models/FabricStock.php` — open, committed, and current stock affected by Sales.
- `resources/views/sales/index.blade.php` — dashboard, registers, and sale/contract forms.
- `database/migrations/` — sales, contract, stock-link, and contract-costing schemas.
