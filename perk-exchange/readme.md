=== Plugin Name ===
Contributors: perkexchange
Donate link: https://example.com/
Tags: kin, cryptocurrency
Requires at least: 5.8
Tested up to: 5.8.3
Stable tag: 1.0
Requires PHP: 7.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Adds a payment gateway to Wordpress sites running Woo Commerce so that KIN cryptocurrency can be used to purchase items. Payment gateway is powered by Perk.Exchange.

== Description ==

This plugin:

- Adds a "KIN" currency to the store
- Adds a new payment gateway provided by https://perk.exchange
- Directs users to https://perk.exchange during checkout. After the invoice is paid, the user clicks back to the shop to continue the order flow.

== Order Flow ==

- Customers are redirected to Perk.Exchange to pay for an order using KIN
- Once the order is paid the user is redirected back to the store
- The store backend is notified the order was paid via IPN. The order is marked 'Completed' automatically
- Links to the invoice and to the Solana blockchain transaction is stored with the order record

== Requirements ==

1. WooCommerce is installed.
2. A user registered on Perk.Exchange having their own campaign. The user requires **campaign manager** access. Reach out on https://twitter.com/perkexchange if you need access or have questions.
3. A **campaign secret** is generated for the campaign.
4. Your site is setup to use the KIN currency. This currency is available after the plugin is installed.

== Configuration ==

1. Go to WooCommerce >> Settings >> Payments
2. Click "Perk.Exchange"
3. Update the **Title**, **Description**, and **Instructions** to your needs
4. Provide your **Solana wallet** address that contains a KIN token.
5. Enter your campaign **Secret** from Perk.Exchange.
6. Click **Save Changes**

== Troubleshooting ==

Some stores may not receive the IPN update from Perk.Exchange that an order was paid. This is likely to be due to:

1. **Inbound API calls are not allowed**

- Try going, on the Admin Panel, to Settings -> Permalinks. On Default Settings radio group, select the "Post name" one. Save the changes. Refer to https://stackoverflow.com/questions/22710078/woocommerce-rest-api-404-error for more information.

2. **Store is not Internet accessible** or not accessible to https://perk.exchange

- Make the store reachable to The Internet or manually mark orders as completed.

== Changelog ==

= 1.0 =

- Initial public release.
