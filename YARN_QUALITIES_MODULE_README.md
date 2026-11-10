# Yarn Qualities Module

## Module name

**Shared Yarn Quality List**

## Short description

Yarn Qualities is a small shared reference-data feature used by Bags and Sizing. It stores a unique yarn quality/count label once, then provides that list to purchase, issue, return, and sizing-slip forms.

## Where to find it

- **Manage qualities:** use **Manage Yarn Qualities** in Bags or **+ Create** beside a yarn-quality selector.
- **Add quality route:** `POST /yarn-qualities`
- **Controller:** `app/Http/Controllers/YarnQualityController.php`
- **Model/table:** `app/Models/YarnQuality.php`, `yarn_qualities`
- **Shared modal:** `resources/views/yarn-qualities/modal.blade.php`

The route is inside the authenticated route group.

## Input and what it means

| Input | Required | Meaning and behavior |
|---|---|---|
| New Yarn Quality (`name`) | Yes | Yarn count/quality label, such as a count and yarn type. Maximum 100 characters; must be unique in the shared list. |

Saving adds the quality to the database. The modal lists saved qualities, and the newly created quality is selected in all matching quality selectors on the current page. Validation errors reopen the modal.

## Where the list is used

- **Bags:** purchase, factory stock issue, and returned-bag forms use the shared quality list.
- **Sizing:** sizing slips use the quality associated with their selected pending bag movement; the shared list is also available in the form.
- **Sales/production:** those modules use fabric qualities, not yarn-quality reference records.

Existing yarn qualities from purchase, transaction, slip, and beam records are imported into the shared table by the yarn-quality migration.

## Route

| Method | Path | Purpose |
|---|---|---|
| POST | `/yarn-qualities` | Create a unique yarn-quality label. |

## Main implementation files

- `routes/web.php` — authenticated quality creation route.
- `app/Http/Controllers/YarnQualityController.php` — validation and record creation.
- `app/Models/YarnQuality.php` — shared quality reference model.
- `resources/views/yarn-qualities/modal.blade.php` — quality list and create form.
- `database/migrations/2026_10_19_124157_create_yarn_qualities_table.php` — reference table and existing-quality import.
