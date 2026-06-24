=== Bricks ActiveCampaign Newsletter ===
Author: wittor.IT
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 8.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connects a Bricks Builder form to an ActiveCampaign newsletter form including double opt-in, automations and list assignments.

== Description ==

Adds a custom "ActiveCampaign Newsletter" action to the Bricks Builder form element. On submission, contact data is posted to the same endpoint AC's own embedded forms use (proc.php), so double opt-in, automations, list assignments and tags fire exactly as configured inside ActiveCampaign — without any duplicate setup in WordPress.

No API key required.

== Installation ==

1. Upload the `bricks-activecampaign` folder to `/wp-content/plugins/`.
2. Activate the plugin via WordPress → Plugins.
3. Go to Bricks → Settings → API keys and enter your ActiveCampaign account URL.

== Frequently Asked Questions ==

= Where do I find the AC form ID? =

In ActiveCampaign go to Forms → [your form] → Integrate. In the embed code look for `id="_form_3_"` — the number is your form ID.

= Do I need an ActiveCampaign API key? =

No. The plugin submits via the same public endpoint AC's own embedded forms use.

== Changelog ==

= 1.0.0 =
* Initial release.
