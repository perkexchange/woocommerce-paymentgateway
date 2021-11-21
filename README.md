# Woo Commerce Payment Gateway

This payment gateway provides [KIN](https://kin.org/) cryptocurrency support to Wordpress sites running Woo Commerce.

NOTE: This is in beta and not ready for production use, yet.

## Overview

This plugin:

* Adds a "KIN" currency to the store
* Adds a new payment gateway provided by https://perk.exchange

## Requirements

1. Install the plugin
2. Setup your site to use the KIN currency
3. Configure the plugin

## Configuration

1. Go to WooCommerce >> Settings >> Payments
2. Click "Perk.Exchange"
3. Update the **Title**, **Description**, and **Instructions** to your needs
4. Provide your **Solana wallet** address that contains a KIN token.
5. Enter your campaign **Secret** from Perk.Exchange. 
6. Click **Save Changes**

## Troubleshooting

Some stores may not receive the IPN update from Perk.Exchange that an order was paid. This is likely to be due to:

1. The store is not hosted on The Internet or not accessible to https://perk.exchange
2. Inbound API calls are not allowed. Try going, on the Admin Panel, to Settings -> Permalinks. On Default Settings radio group, select the "Post name" one. Save the changes. Refer to https://stackoverflow.com/questions/22710078/woocommerce-rest-api-404-error for more information.
