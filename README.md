# Bricks ActiveCampaign Newsletter

WordPress plugin that connects a [Bricks Builder](https://bricksbuilder.io) form to an [ActiveCampaign](https://www.activecampaign.com) newsletter form — including double opt-in, automations and list assignments, without any duplicate configuration in WordPress.

## How it works

Instead of calling the ActiveCampaign REST API directly, the plugin posts contact data to `proc.php` — the same public endpoint AC's own embedded forms use. This means everything you configure inside ActiveCampaign (lists, tags, double opt-in, automations) fires automatically, exactly as if the visitor had filled out the native AC form.

No API key required.

## Requirements

- Bricks Builder
- ActiveCampaign account with at least one published form
- WordPress 6.0+, PHP 8.0+

## Setup

1. Go to **Bricks → Settings → API keys** and enter your AC account URL (e.g. `https://myaccount.api-us1.com`)
2. In the Bricks builder, add the **ActiveCampaign Newsletter** action to your form
3. Enter your AC form ID and map your form fields

The form ID can be found in AC under **Forms → [Form] → Integrate** in the embed code (`id="_form_3_"` → ID is `3`).

## Documentation

- [User Guide](docs/user-guide.md)
- [Developer Guide](docs/developer-guide.md)

## Languages

English and German (de_DE) included.
