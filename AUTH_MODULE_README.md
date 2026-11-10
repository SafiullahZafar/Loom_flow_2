# Authentication Module

## Module name

**LoomFlow Authentication**

## Short description

The authentication module provides the sign-in screen and session lifecycle used to protect the mill-management screens. Successful sign-in redirects to the page the user originally requested, or to the main dashboard.

## Where to find it

- **Sign in:** `/login`
- **Submit credentials:** `POST /login`
- **Demo quick login:** `POST /quick-login`
- **Sign out:** `POST /logout`

The login screen is rendered by `resources/views/auth/login.blade.php`. Routes are declared in `routes/web.php`; request handling is in `app/Http/Controllers/AuthController.php`.

## Inputs and what they mean

### Sign in

| Input | Required | Meaning and behavior |
|---|---|---|
| Email Address (`email`) | Yes | Must be a syntactically valid email address. It is used with the password to find and authenticate the account. |
| Password (`password`) | Yes | Password for the account. It is not retained in the form when authentication fails. |
| Remember credentials (`remember`) | No | Checkbox passed to Laravel's remember-me authentication. |

The application validates email and password presence, attempts authentication, regenerates the session on success, and returns an email validation error for invalid credentials. A server-side exception during sign-in is logged and shown as a temporary sign-in error.

### Demo quick login

The login screen provides **Admin**, **Store**, and **Master** demo buttons. Each posts a selected account email to `/quick-login`; the account must exist for the action to complete. These buttons are demo shortcuts, not a substitute for entering credentials.

### Sign out

The logout form sends no user-entered fields. It logs out the current session, invalidates the session, regenerates the CSRF token, and redirects to `/login`.

## Access and session flow

1. A guest opens `/login`. An already-authenticated user is redirected to the dashboard.
2. The user submits the email, password, and optional remember-me choice.
3. On success, the session is regenerated and the user is redirected to their intended page, falling back to the dashboard.
4. Routes inside the authenticated route group require a signed-in session.
5. Signing out clears that session and returns the user to the login page.

## Routes

| Method | Path | Purpose |
|---|---|---|
| GET | `/login` | Show the sign-in page to guests. |
| POST | `/login` | Authenticate using email and password. |
| POST | `/quick-login` | Sign in as an existing account selected by the demo UI. |
| POST | `/logout` | End the current session. |

## Main implementation files

- `routes/web.php` — guest and logout routes, and the authenticated route group.
- `app/Http/Controllers/AuthController.php` — login, quick login, session regeneration, and logout.
- `app/Models/User.php` — authenticated user model.
- `resources/views/auth/login.blade.php` — login fields, remember checkbox, validation feedback, and demo buttons.
- `app/Http/Controllers/DashboardController.php` — destination for successful login.
