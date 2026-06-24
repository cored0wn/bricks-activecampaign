=== Bricks ActiveCampaign Newsletter ===
Author: wittor.IT
Requires at least: 6.0
Tested up to: 7.0
Stable tag: 1.0.0
Requires PHP: 8.0
License: GNU AGPLv3
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connects a Bricks Builder form to an ActiveCampaign newsletter form — including double opt-in, automations and list assignments, without any duplicate configuration in WordPress.

== Description ==

Instead of calling the ActiveCampaign REST API directly, the plugin posts contact data to `proc.php` — the same public endpoint AC's own embedded forms use. This means everything you configure inside ActiveCampaign (lists, tags, double opt-in, automations) fires automatically, exactly as if the visitor had filled out the native AC form.

No API key required.

== Requirements ==

* Bricks Builder
* ActiveCampaign account with at least one published form
* WordPress 6.0+, PHP 8.0+

== Installation ==

1. Upload the `bricks-activecampaign` folder to `/wp-content/plugins/`.
2. Activate the plugin via WordPress → Plugins.
3. Go to Bricks → Settings → API keys and enter your AC account URL (e.g. `https://myaccount.api-us1.com`).
4. In the Bricks builder, add the **ActiveCampaign Newsletter** action to your form.
5. Enter your AC form ID and map your form fields.

== Frequently Asked Questions ==

= Where do I find the AC form ID? =

In ActiveCampaign go to Forms → [your form] → Integrate. In the embed code look for `id="_form_3_"` — the number is your form ID.

= Do I need an ActiveCampaign API key? =

No. The plugin submits data through the same public endpoint AC's own embedded forms use (`proc.php`). Only the account URL is required.

= Which languages are included? =

English and German (de_DE).

== Changelog ==

= 1.0.0 =
* Initial release.
