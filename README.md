# Woo Commerce Payment Gateway

This payment gateway provides [KIN](https://kin.org/) cryptocurrency support to Wordpress sites running Woo Commerce.

NOTE: This is in beta and not ready for production use, yet.

## Overview

This plugin:

* Adds a "KIN" currency to the store
* Adds a new payment gateway provided by https://perk.exchange

## Order Flow

* Customers are redirected to Perk.Exchange to pay for an order using KIN
* Once the order is paid the user is redirected back to the store
* The store backend is notified the order was paid. The order is marked 'Completed' automatically

## Requirements

1. WooCommerce is installed.
2. Latest release of the Perk.Exchange plugin ZIP
3. Your site is setup to use the KIN currency. This currency is available after the plugin is installed.

## Configuration

1. Go to WooCommerce >> Settings >> Payments
2. Click "Perk.Exchange"
3. Update the **Title**, **Description**, and **Instructions** to your needs
4. Provide your **Solana wallet** address that contains a KIN token.
5. Enter your campaign **Secret** from Perk.Exchange. 
6. Click **Save Changes**

## Troubleshooting

Some stores may not receive the IPN update from Perk.Exchange that an order was paid. This is likely to be due to:

1. **Inbound API calls are not allowed**
* Try going, on the Admin Panel, to Settings -> Permalinks. On Default Settings radio group, select the "Post name" one. Save the changes. Refer to https://stackoverflow.com/questions/22710078/woocommerce-rest-api-404-error for more information.
2. **Store is not Internet accessible** or not accessible to https://perk.exchange 
* Make the store reachable to The Internet or manually mark orders as completed.
