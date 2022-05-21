=== Plugin Name ===
Contributors: perkexchange
Tags: kin, crypto, payment
Requires at least: 5.8
Tested up to: 5.8.3, 6.3.1, 6.4.1, 6.5.1
Stable tag: 1.0.1
Requires PHP: 7.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Adds a payment gateway to Wordpress sites running WooCommerce so that [KIN](https://kin.org) cryptocurrency can be used to purchase items. Payment gateway is powered by [Perk.Exchange](https://perk.exchange).

== Description ==

Adds a payment gateway to Wordpress sites running WooCommerce so that [KIN](https://kin.org) cryptocurrency can be used to purchase items. Payment gateway is powered by [Perk.Exchange](https://perk.exchange).

= Order Flow =

- Customers add items to their shopping cart as normal
- At checkout the customer can pay with the Perk.Exchange payment gateway
- Customers are redirected to [Perk.Exchange](https://perk.exchange) to pay for an order using KIN
- Once the order is paid the user is redirected back to the store
- The store backend is notified the order was paid via IPN. The order is marked 'Completed' automatically
- Links to the invoice and to the Solana blockchain transaction is stored with the order record

== Installation ==

= Requirements =

1. Store owner must be registered on Perk.Exchange having their own campaign. The user requires **campaign manager** access. Reach out on <https://twitter.com/perkexchange> if you need access or have questions.
2. A **campaign secret** is generated for the campaign.
3. Your site is setup to use the KIN currency. This currency is available after the plugin is installed.

= Configuration =

1. Go to **WooCommerce** >> **Settings** >> **Payments**
2. Click "Perk.Exchange"
3. Update the **Title**, **Description**, and **Instructions** to your needs
4. Provide your **Solana wallet** address that contains a KIN token.
5. Enter your campaign **Secret** from Perk.Exchange.
6. Click **Save Changes**

= Troubleshooting =

Some stores may not receive the IPN update from Perk.Exchange that an order was paid. This is likely to be due to:

1. **Inbound API calls are not allowed**

- Try going, on the Admin Panel, to Settings -> Permalinks. On Default Settings radio group, select the "Post name" one. Save the changes. Refer to <https://stackoverflow.com/questions/22710078/woocommerce-rest-api-404-error> for more information.

2. **Store is not Internet accessible** or not accessible to <https://perk.exchange>

- Make the store reachable to The Internet or manually mark orders as completed.

== Frequently Asked Questions ==

= What is KIN? =

[KIN](https://kin.org) is the world's most used cryptocurrency. The KIN ecosystem is made of a growing list of consumer-focused applications.

= What is Perk.Exchange? =

[Perk.Exchange](https://perk.exchange) is a platform to reward users for their engagement in: surveys, programming tasks, and many other activities. Any user can create a campaign to offer cryptocurrency for their participation.

== Changelog ==

= 1.0 =

- Initial public release.
