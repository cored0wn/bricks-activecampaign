# Bricks ActiveCampaign Newsletter — Developer Guide

## Architecture overview

The plugin adds a custom action (`activecampaign`) to the Bricks Builder form element. On submission it posts contact data to ActiveCampaign's `proc.php` form processor — the same public endpoint AC's own embedded forms use. No API key is required.

```
Bricks form submission
  └─ bricks/form/action/activecampaign  (action hook)
       └─ Bricks_AC_Form_Handler::handle()
            └─ Bricks_AC_API::submit_form()
                 └─ POST https://{account}.activehosted.com/proc.php
```

---

## File structure

```
bricks-activecampaign/
├── bricks-activecampaign.php          Entry point, constants, bootstrap
├── includes/
│   ├── class-ac-api.php               proc.php submission wrapper
│   ├── class-admin-settings.php       Injects URL field into Bricks settings page
│   ├── class-bricks-controls.php      Registers form action + builder controls
│   └── class-form-handler.php         Handles form submission action
├── languages/
│   ├── bricks-activecampaign.pot      Translation template
│   ├── bricks-activecampaign-de_DE.po German translations (source)
│   └── bricks-activecampaign-de_DE.mo German translations (compiled)
└── docs/
    ├── user-guide.md
    └── developer-guide.md
```

---

## Constants

| Constant | Value | Purpose |
|---|---|---|
| `BRICKS_AC_VERSION` | `1.0.0` | Plugin version, used for asset versioning |
| `BRICKS_AC_FILE` | `__FILE__` | Absolute path to the entry point |

---

## Classes

### `Bricks_AC_API`

Thin wrapper around the ActiveCampaign `proc.php` endpoint.

**Constructor**

```php
new Bricks_AC_API( string $api_url )
```

`$api_url` — The AC account URL (e.g. `https://myaccount.api-us1.com`). The account subdomain is extracted to derive the `proc.php` URL:

```
https://myaccount.api-us1.com
  → myaccount
  → https://myaccount.activehosted.com/proc.php
```

**`submit_form( int $form_id, array $fields ): true|WP_Error`**

Posts to `proc.php`. The `$fields` array accepts any key that AC's form processor recognises:

```php
$api->submit_form( 3, [
    'email'     => 'user@example.com',
    'firstname' => 'Jane',
    'lastname'  => 'Doe',
] );
```

Standard body params (`u`, `f`, `s`, `c`, `m`, `act`, `v`) are added automatically. Empty string values are filtered out before sending.

Returns `true` on any HTTP 2xx/3xx response, `WP_Error` on network failure or HTTP 4xx/5xx.

---

### `Bricks_AC_Admin_Settings`

Injects a single settings row into Bricks' own **API keys** tab using `admin_footer` JS DOM injection (no server-side hook exists for this tab).

The `apiUrlActiveCampaign` key is stored in Bricks' global settings option (`BRICKS_DB_GLOBAL_SETTINGS`) and read via `\Bricks\Database::get_setting()`.

**Hook**

```php
add_action( 'admin_footer', [ Bricks_AC_Admin_Settings::class, 'inject_settings_rows' ] );
```

Only fires on the `bricks_page_bricks-settings` admin screen.

---

### `Bricks_AC_Controls`

Registers the `activecampaign` action option and its builder panel controls via the Bricks filter API.

**Filters hooked**

```php
add_filter( 'bricks/elements/form/control_groups', ... );
add_filter( 'bricks/elements/form/controls', ... );
```

**Controls registered**

| Key | Type | Purpose |
|---|---|---|
| `acFormId` | `text` | Numeric AC form ID |
| `acEmailField` | `select` + `map_fields` | Maps a Bricks field to `email` |
| `acFirstNameField` | `select` + `map_fields` | Maps a Bricks field to `firstname` |
| `acLastNameField` | `select` + `map_fields` | Maps a Bricks field to `lastname` |
| `acSuccessMsg` | `text` | Success message shown after submission |

`map_fields: true` populates the select with the form's own fields. Bricks stores only the raw field ID (e.g. `abc123`); at submission time the key must be prefixed with `form-field-` to match `get_fields()` output.

---

### `Bricks_AC_Form_Handler`

Handles the `bricks/form/action/activecampaign` hook.

**Hook**

```php
add_action( 'bricks/form/action/activecampaign', [ Bricks_AC_Form_Handler::class, 'handle' ] );
```

**Submission flow**

1. Read `acFormId`, field mapping keys and `acSuccessMsg` from element settings.
2. Look up submitted values from `$form->get_fields()` using `form-field-{key}` prefixed keys.
3. Validate email with `is_email()`.
4. Instantiate `Bricks_AC_API` with the URL from Bricks global settings.
5. Call `submit_form()`.
6. Call `$form->set_result()` with `type: success` or `type: error`.

**Error visibility**

`WP_Error` messages are only shown verbatim to users with `manage_options` capability. All other users receive a generic "please try again" message.

---

## Key implementation notes

### `map_fields` prefix gotcha

Bricks' `map_fields: true` select stores only the raw field ID (e.g. `abc123`). The submitted fields array from `$form->get_fields()` uses `form-field-abc123` as the key. Always prepend `form-field-` when looking up field values:

```php
$email_key = 'form-field-' . sanitize_key( $settings['acEmailField'] ?? '' );
$email     = sanitize_email( $fields[ $email_key ] ?? '' );
```

### Why proc.php instead of the REST API

ActiveCampaign's REST API gives you fine-grained control over contacts, lists and automations, but that control comes at a cost: **you have to rebuild everything the AC form already knows how to do.**

A complete REST-based signup flow requires at least four separate API calls:

1. `POST /api/3/contact/sync` — create or update the contact
2. `POST /api/3/contactLists` — subscribe the contact to one or more lists, with `status: 0` if double opt-in is desired
3. `POST /api/3/tags` + `POST /api/3/contactTags` — look up or create each tag and attach it to the contact
4. `POST /api/3/contactAutomations` — enrol the contact in an automation to trigger the DOI confirmation email

Each of those steps requires a piece of configuration that has to be duplicated in WordPress: the list ID, the tag names, the automation ID. If the AC account changes — a list is renamed, an automation is rebuilt, a tag is retired — the WordPress settings have to be updated separately. The two systems drift apart.

There is also a problem with double opt-in via the REST API. Setting `status: 0` on a list subscription marks the contact as unconfirmed, but **it does not automatically send a confirmation email**. That only happens if the list has the "Send opt-in confirmation email" option enabled *and* the contact is added in a way AC recognises as a form submission. Triggering it reliably through the API alone requires an additional automation enrolment step, and even then the behaviour depends on list-level settings the plugin cannot inspect.

`proc.php` sidesteps all of this. It is the same endpoint AC's own embedded forms POST to when a visitor fills them out on an external site. From AC's perspective, a `proc.php` submission is indistinguishable from a native form submission, so:

- DOI confirmation emails fire exactly as configured in the form and list settings — no extra automation needed
- List subscriptions, tag assignments and automation enrolments all happen automatically, driven by the AC form configuration
- No API key is required — `proc.php` is a public endpoint

The trade-off is that all subscription logic lives in AC rather than in WordPress. That is intentional: it means a marketing team can change lists, tags or automations inside AC without touching the WordPress plugin, and the behaviour updates immediately.

### URL derivation

`proc.php` lives on `activehosted.com`, not `api-us1.com`. The account subdomain is the same:

```php
$host    = parse_url( $api_url, PHP_URL_HOST ); // myaccount.api-us1.com
$account = strstr( $host, '.', true );           // myaccount
$proc    = "https://{$account}.activehosted.com/proc.php";
```

### Admin settings injection

Bricks has no PHP hook to add rows to its API keys tab. The solution is a `admin_footer` script that reads the current values via PHP (`\Bricks\Database::get_setting()`), JSON-encodes the HTML rows, and appends them to `#tab-api-keys tbody` via DOM manipulation.

---

## Internationalisation

Text domain: `bricks-activecampaign`

Loaded at `init` priority 1 (before Bricks initialises at priority 20):

```php
add_action( 'init', function () {
    load_plugin_textdomain( 'bricks-activecampaign', false, 'bricks-activecampaign/languages' );
}, 1 );
```

To add a new language, copy `languages/bricks-activecampaign.pot`, translate it, and compile with:

```bash
msgfmt bricks-activecampaign-fr_FR.po -o bricks-activecampaign-fr_FR.mo
```

---

## Input sanitisation

All user-submitted values are sanitised before use or storage:

| Value | Function |
|---|---|
| Email | `sanitize_email()` + `is_email()` validation |
| First / last name | `sanitize_text_field()` |
| Form ID | `absint()` |
| API URL | `esc_url_raw()` + `FILTER_VALIDATE_URL` |
| Control keys | `sanitize_key()` |
