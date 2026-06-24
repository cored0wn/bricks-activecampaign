# Bricks ActiveCampaign Newsletter — User Guide

## What this plugin does

Connects a Bricks Builder form to an ActiveCampaign newsletter form. When a visitor submits your Bricks form, their data is sent directly to ActiveCampaign via the same submission channel as AC's own embedded forms — meaning double opt-in, automations, list assignments and tags all work exactly as you configured them inside ActiveCampaign, without any duplicate setup in WordPress.

---

## Requirements

- **Bricks Builder** (any recent version)
- **ActiveCampaign** account with at least one form created
- WordPress 6.0+, PHP 8.0+

---

## Installation

1. Upload the `bricks-activecampaign` folder to `/wp-content/plugins/`.
2. Activate the plugin in **WordPress → Plugins**.

---

## Configuration

### 1. Add your account URL

Go to **Bricks → Settings → API keys**. You will find a new row at the bottom:

| Field | What to enter |
|---|---|
| **ActiveCampaign: Account URL** | Your AC API base URL, e.g. `https://myaccount.api-us1.com` |

Find this URL in ActiveCampaign under **Settings → Developer**.

> No API key is required. The plugin submits data through the same public endpoint AC's own embedded forms use.

### 2. Create a form in ActiveCampaign

Configure everything inside ActiveCampaign's form builder:

- **Which list(s)** subscribers are added to
- **Tags** applied on signup
- **Double opt-in** — enable "Send opt-in confirmation email" on the list and/or in the form settings
- **Automations** triggered on submission

The plugin will trigger all of this automatically.

### 3. Find your AC form ID

In ActiveCampaign, go to **Forms → [your form] → Integrate**. In the embed code you will see something like:

```html
<form id="_form_3_" ...>
```

The number — `3` in this example — is your form ID.

---

## Setting up the Bricks form

1. Open Bricks Builder on any page.
2. Add or select a **Form** element.
3. In the **Actions** setting, add **ActiveCampaign Newsletter**.
4. Scroll down to the **ActiveCampaign Newsletter** section that appears. Fill in:

| Setting | Description |
|---|---|
| **AC Form ID** | The numeric ID from the embed code (e.g. `3`) |
| **Email field** | Which form field contains the visitor's email address |
| **First name field** | Optional — which field contains the first name |
| **Last name field** | Optional — which field contains the last name |
| **Success message** | Text shown after a successful signup |

5. Save the page. Done.

---

## How it works

When the visitor submits the Bricks form, the plugin posts the data to:

```
https://myaccount.activehosted.com/proc.php
```

This is the same endpoint AC's own embedded forms use. ActiveCampaign receives the submission and handles everything: opt-in confirmation emails, list subscriptions, tags, automations — exactly as configured in the AC form.

---

## Troubleshooting

**"Your subscription could not be processed"**
- Check that the **Account URL** is saved correctly under Bricks → Settings → API keys.
- Verify the **AC Form ID** matches an existing, published form in your AC account.
- Make sure the **Email field** mapping points to the correct form field.

**Subscribers appear but double opt-in email is not sent**
- Double opt-in must be enabled on the AC list itself (**Lists → [list] → Settings → Opt-in confirmation**) and/or on the form (**Forms → [form] → Options**).

**Form submits but nothing appears in AC**
- Confirm the account URL is the full URL including `https://` and no trailing slash.
- The account subdomain in the URL must match your AC account (e.g. `myaccount` in `myaccount.api-us1.com`).
