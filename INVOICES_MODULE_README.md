# Invoices Module

## Module name

**Customer Invoices, Payment Receipts & Cash/Bank Accounts**

## Short description

The Invoices module manages customer records, issues invoices from sales slips or manual/contract line items, records customer payment receipts, allocates receipts to outstanding invoices, and maintains cash and bank account balances. Receipts post money into a selected account; any amount not allocated to invoices remains available as customer credit.

## Where to find it

- **Dashboard:** `/invoices`
- **Invoice register:** `/invoices/records`
- **Customers:** `/invoices/customers`
- **Payments received:** `/invoices/payments`
- **Cash & bank accounts:** `/invoices/accounts`
- **Controller:** `app/Http/Controllers/InvoiceController.php`
- **View:** `resources/views/invoices/index.blade.php`

## Inputs and what they mean

### Add a customer

| Input | Required | Meaning and behavior |
|---|---|---|
| Customer / party name (`name`) | Yes | Customer name; maximum 150 characters. Names are trimmed and compared case-insensitively to prevent duplicate customers. |
| Phone (`phone`) | No | Optional phone number, maximum 50 characters, unique when supplied. |
| Email (`email`) | No | Optional valid email address, maximum 150 characters. |
| Opening amount due (`opening_balance`) | No | Starting customer receivable in rupees; non-negative, defaults to zero. |
| Address (`address`) | No | Optional customer address; maximum 1000 characters. |

Contract and sales buyer names are synchronized with the customer list by their respective workflows. Existing names should be selected rather than added again.

### Create an invoice

| Input | Required | Meaning and behavior |
|---|---|---|
| Invoice number (`invoice_no`) | Yes | Unique invoice reference; maximum 100 characters. |
| Customer (`customer_id`) | Yes | Existing customer receiving the invoice. |
| Invoice from sales slip (`sale_id`) | No | Optional unbilled sales slip. When selected, the buyer, fabric description, quantity, rate, subtotal, and discount are copied from the slip. A sales slip can have only one invoice. |
| Related contract (`sales_contract_id`) | No | Optional contract link. For a contract invoice, meter line items cannot exceed delivered meters that have not already been invoiced. |
| Invoice date (`invoice_date`) | Yes | Issue date. |
| Due date (`due_date`) | No | Payment deadline; must be on or after invoice date. |
| Line description (`items[*][description]`) | Required unless a sale is selected | Description for a manual invoice line; maximum 200 characters. |
| Line quantity (`items[*][quantity]`) | Required unless a sale is selected | Positive numeric amount for the line. |
| Line unit (`items[*][unit]`) | Required unless a sale is selected | Unit such as Meter, Roll, Lot, or Other; maximum 30 characters. |
| Line rate (`items[*][rate]`) | Required unless a sale is selected | Non-negative rupee rate per unit. |
| Discount (`discount`) | No | Non-negative invoice-level rupee discount. It must be less than the subtotal. |
| Notes (`notes`) | No | Optional internal invoice note; maximum 2000 characters. |

Manual invoices accept 1–50 line items. Each line amount is calculated as `quantity × rate`; the subtotal is the sum of line amounts and the invoice total is `subtotal − discount`. A saved invoice is issued and retains its items and customer/contract/sales-slip links.

When an invoice is linked to a sale, the customer must match the sale buyer. When it is linked to a contract, the customer must match the contract buyer. For contract billing, only delivered meters not previously billed on that contract can be invoiced.

### Receive customer payment

| Input | Required | Meaning and behavior |
|---|---|---|
| Receipt number (`receipt_no`) | Yes | Unique receipt reference; maximum 100 characters. |
| Customer (`customer_id`) | Yes | Customer paying the amount. |
| Deposit into (`financial_account_id`) | Yes | Active cash or bank account receiving the money. |
| Amount (`amount`) | Yes | Positive payment amount in rupees. |
| Payment date (`payment_date`) | Yes | Date received and posted to the account. |
| Method (`method`) | Yes | `cash`, `bank_transfer`, `cheque`, `card`, or `other`. Cash accounts accept only cash; bank accounts accept the non-cash methods. |
| Reference (`reference`) | No | Optional transfer, cheque, or external reference; maximum 120 characters. |
| Apply payment (`allocation_mode`) | Yes | `oldest`, `manual`, `invoice`, or `credit`. |
| Invoice to pay (`invoice_id`) | Required for invoice mode | One issued invoice belonging to the selected customer. Payment is applied up to the outstanding amount; any remainder stays unallocated as customer credit. |
| Manual invoice allocations (`allocations[*][invoice_id]`, `allocations[*][amount]`) | Required for manual mode | One or more distinct issued invoices belonging to the customer, and the amount to allocate to each. Each allocation must fit both the invoice balance and the remaining receipt amount. |
| Notes (`notes`) | No | Optional receipt notes; maximum 2000 characters. |

Allocation modes:

- **Oldest open invoices first:** apply payment across issued invoices from oldest to newest until the receipt is used or no due remains.
- **Allocate across invoices:** use the submitted manual invoice/amount rows.
- **Choose one invoice:** apply as much of the receipt as possible to the selected invoice; excess remains as credit.
- **Keep as customer credit:** record the receipt without allocating it to invoices.

Every successful receipt creates a payment receipt and a money-in account transaction. For invoices linked to a sales slip, the sale's payment status is updated from the invoice's remaining due after the allocation.

### Add a cash/bank account

| Input | Required | Meaning and behavior |
|---|---|---|
| Account name (`name`) | Yes | Label for the account; maximum 120 characters. |
| Type (`type`) | Yes | `cash` or `bank`. |
| Account / last digits (`account_number`) | No | Optional account identifier; maximum 80 characters. |
| Opening balance (`opening_balance`) | No | Non-negative starting rupee balance; defaults to zero. |

### Record a deposit or withdrawal

Use this form for account movements other than customer receipts; customer receipts are posted automatically.

| Input | Required | Meaning and behavior |
|---|---|---|
| Account (`financial_account_id`) | Yes | Active account receiving or paying the funds. |
| Direction (`direction`) | Yes | `in` increases the balance; `out` decreases it. |
| Date (`transaction_date`) | Yes | Date of the movement. |
| Amount (`amount`) | Yes | Positive amount in rupees. |
| Description (`description`) | Yes | Reason for the movement; maximum 200 characters. |
| Reference (`reference`) | No | Optional supporting reference; maximum 120 characters. |

## What the module displays

- **Dashboard:** total issued invoices, total receipts, outstanding amount, total cash/bank balances, and recent invoices.
- **Invoice register:** invoice/customer/date/due date, linked sale or contract, line items, subtotal, discount, total, amount allocated, and remaining due.
- **Customer register:** customer contact details, status, invoice count, invoiced total, receipts total, and displayed balance.
- **Payments page:** dated receipts, customer, receiving account, method/reference, allocations, and receipt amount.
- **Cash & bank page:** each account's opening balance, money in, money out, current balance, and recent dated transactions.

Account balance is calculated as:

`opening balance + money in − money out`

Invoice due is calculated from issued invoice total less allocations. The dashboard's aggregate outstanding figure also includes customer opening balances.

## End-to-end workflow

1. Add a customer, or create a sale/contract that synchronizes its buyer into the customer list.
2. Create an invoice from an unbilled sales slip, or enter manual invoice lines and optionally link a contract.
3. Add a cash or bank account before posting receipts.
4. Receive a payment, select the receiving account and method, and choose how to allocate it.
5. Review the invoice balance, customer account, receipt history, and cash/bank balance.
6. Use account deposit/withdrawal entries for non-customer movements.

Sales slips originate in [SALES_MODULE_README.md](./SALES_MODULE_README.md). This module handles billing and receipts; it does not dispatch fabric.

## Routes

| Method | Path | Purpose |
|---|---|---|
| GET | `/invoices` | Billing dashboard. |
| GET | `/invoices/records` | Invoice register. |
| GET | `/invoices/customers` | Customer register and add-customer form. |
| GET | `/invoices/payments` | Payment receipts and receive-payment form. |
| GET | `/invoices/accounts` | Cash/bank account balances and entries. |
| POST | `/invoices/customers` | Create a customer. |
| POST | `/invoices` | Issue an invoice. |
| POST | `/invoices/payments` | Record a customer receipt and allocations. |
| POST | `/invoices/accounts` | Create a cash/bank account. |
| POST | `/invoices/accounts/transactions` | Record an account deposit or withdrawal. |

All routes require authentication.

## Main implementation files

- `routes/web.php` — Invoice, customer, payment, and account routes.
- `app/Http/Controllers/InvoiceController.php` — billing queries, form validation, invoice arithmetic, payment allocation, and account transaction posting.
- `app/Models/Customer.php`, `Invoice.php`, `InvoiceItem.php` — customer and invoice records.
- `app/Models/PaymentReceipt.php`, `InvoicePaymentAllocation.php` — receipt and invoice allocation records.
- `app/Models/FinancialAccount.php`, `AccountTransaction.php` — account balance records and movements.
- `resources/views/invoices/index.blade.php` and `resources/views/invoices/partials/` — dashboard, forms, and registers.
- `database/migrations/2026_10_22_082605_create_invoice_module_tables.php` and later invoice/customer migrations — billing schemas and customer migration.
