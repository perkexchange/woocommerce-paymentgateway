<?php
/**
 * Plugin Name: Perk.Exchange Payment Gateway
 * Plugin URI: https://perk.exchange
 * Description: Allows payments through Perk.Exchange
 * Author URI: https://perk.exchange
 */

defined('ABSPATH') or exit;

// Make sure WooCommerce is active
if (! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
}

/**
 * Custom currency and currency symbol
 */
add_filter('woocommerce_currencies', 'add_kin_currency');

function add_kin_currency($currencies)
{
    $currencies['KIN'] = __('KIN', 'KIN');
    return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_kin_currency_symbol', 10, 2);

function add_kin_currency_symbol($currency_symbol, $currency)
{
    switch ($currency) {
          case 'KIN': $currency_symbol = 'KIN '; break;
     }
    return $currency_symbol;
}

/**
 * Add the gateway to WC Available Gateways
 *
 * @since 1.0.0
 * @param array $gateways all available WC gateways
 * @return array $gateways all WC gateways + offline gateway
 */
function wc_perkexchange_add_to_gateways($gateways)
{
    $gateways[] = 'WC_Gateway_PerkExchange';
    return $gateways;
}
add_filter('woocommerce_payment_gateways', 'wc_perkexchange_add_to_gateways');


/**
 * Adds plugin page links
 *
 * @since 1.0.0
 * @param array $links all plugin links
 * @return array $links all plugin links + our custom links (i.e., "Settings")
 */
function wc_perkexchange_gateway_plugin_links($links)
{
    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=offline_gateway') . '">' . __('Configure', 'wc-gateway-perkexchange') . '</a>'
    );

    return array_merge($plugin_links, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wc_perkexchange_gateway_plugin_links');

/**
 * PerkExchange Payment Gateway
 *
 * @class 		WC_Gateway_PerkExchange
 * @extends		WC_Payment_Gateway
 * @version		1.0.0
 * @package		WooCommerce/Classes/Payment
 */
add_action('plugins_loaded', 'wc_perkexchange_gateway_init', 11);

function wc_perkexchange_gateway_init()
{
    class WC_Gateway_PerkExchange extends WC_Payment_Gateway
    {
        private $allowedCurrencies = array('KIN');

        /**
         * Constructor for the gateway.
         */
        public function __construct()
        {
            $this->id                 = 'perkexchange_gateway';
            $this->icon               = 'https://perk.exchange/static/perk-icon.png';
            $this->has_fields         = false;
            $this->method_title       = __('Perk.Exchange', 'wc-gateway-perkexchange');
            $this->method_description = __('Allows payments via Perk.Exchange.', 'wc-gateway-perkexchange');

            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables
            $this->title        = $this->get_option('title');
            $this->description  = $this->get_option('description');
            $this->instructions = $this->get_option('instructions', $this->description);

            $this->solana_wallet= $this->get_option('solana_wallet');
            $this->campaign_secret = $this->get_option('campaign_secret');

            // Actions
            add_action('woocommerce_api_perkexchange_webhook', array( $this, 'perkexchange_webhook' ));
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
            add_action('woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ));

            // Customer Emails
            add_action('woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3);
        }


        /**
         * Initialize Gateway Settings Form Fields
         */
        public function init_form_fields()
        {
            $this->form_fields = apply_filters('wc_perkexchange_form_fields', array(

                'enabled' => array(
                    'title'   => __('Enable/Disable', 'wc-gateway-perkexchange'),
                    'type'    => 'checkbox',
                    'label'   => __('Enable Perk.Exchange Payment', 'wc-gateway-perkexchange'),
                    'default' => 'yes'
                ),

                'title' => array(
                    'title'       => __('Title', 'wc-gateway-perkexchange'),
                    'type'        => 'text',
                    'description' => __('This controls the title for the payment method the customer sees during checkout.', 'wc-gateway-offline'),
                    'default'     => __('Perk.Exchange Payment', 'wc-gateway-perkexchange'),
                    'desc_tip'    => true,
                ),

                'description' => array(
                    'title'       => __('Description', 'wc-gateway-perkexchange'),
                    'type'        => 'textarea',
                    'description' => __('Payment method description that the customer will see on your checkout.', 'wc-gateway-perkexchange'),
                    'default'     => __('Please remit payment.', 'wc-gateway-perkexchange'),
                    'desc_tip'    => true,
                ),

                'solana_wallet' => array(
                    'title'   => __('Solana Wallet', 'wc-gateway-perkexchange'),
                    'type'    => 'text',
                    'label'   => __('Solana Wallet', 'wc-gateway-perkexchange'),
                    'default'     => __('H7q8zE2gXsWqraa6UCCLCk31zpFwjigMxBfxNDz3gW6c', 'wc-gateway-perkexchange'),
                    'description' => __('The Solana wallet to accept payments', 'wc-gateway-perkexchange'),
                    'desc_tip'    => true,
                ),

                'campaign_secret' => array(
                    'title'   => __('Secret', 'wc-gateway-perkexchange'),
                    'type'    => 'password',
                    'label'   => __('Secret', 'wc-gateway-perkexchange'),
                    'default'     => __('', 'wc-gateway-perkexchange'),
                    'description' => __('A campaign secret key from Perk.Exchange', 'wc-gateway-perkexchange'),
                    'desc_tip'    => true,
                ),

                'instructions' => array(
                    'title'       => __('Instructions', 'wc-gateway-perkexchange'),
                    'type'        => 'textarea',
                    'description' => __('Instructions that will be added to the thank you page and emails.', 'wc-gateway-perkexchange'),
                    'default'     => '',
                    'desc_tip'    => true,
                ),
            ));
        }

        public function is_valid_for_use()
        {
            return in_array(get_woocommerce_currency(), $this->allowedCurrencies);
        }

        public function admin_options()
        {
            if ($this->is_valid_for_use()) {
                parent::admin_options();
            } else {
                ?>
                <div class="notice error is-dismissible" >
                 <p><?php _e('Perk.Exchange does not support the selected currency ' . get_woocommerce_currency() . '!', 'my-text-domain'); ?></p>
                </div>
            <?php
            }
        }

        public function validate_campaign_secret_field($key, $value)
        {
            $response = wp_remote_get('https://perk.exchange/api/invoices', array(
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => [
                    'Authorization' => 'Bearer '.$value,
                    'Content-Type' => 'application/json',
                ]));

            if (is_wp_error($response)) {
                ?>
                <div class="notice error is-dismissible" >
                    <p><?php _e('Campaign secret is not valid', 'my-text-domain'); ?></p>
                </div>
            	<?php
            } else {
                return $value;
            }
        }

        /**
         * Output for the order received page.
         */
        public function thankyou_page()
        {
            if ($this->instructions) {
                echo wpautop(wptexturize($this->instructions));
            }
        }


        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        public function email_instructions($order, $sent_to_admin, $plain_text = false)
        {
            if ($this->instructions && ! $sent_to_admin && $this->id === $order->payment_method && $order->has_status('on-hold')) {
                echo wpautop(wptexturize($this->instructions)) . PHP_EOL;
            }
        }

        /**
         *
         */
        public function perkexchange_webhook()
        {
            $response = wp_remote_get('https://perk.exchange/api/invoices?order_id='.$_GET['id'], array(
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => [
                    'Authorization' => 'Bearer '.$this->campaign_secret,
                    'Content-Type' => 'application/json',
                ]));

            if (is_wp_error($response)) {
                wc_add_notice('Could not retrieve payment information for order '.$_GET['id'], 'error');
                return false; // Bail early
            }

            $body = json_decode(wp_remote_retrieve_body($response));
            if (count($body->invoices) <= 0) {
                wc_add_notice('No paid invoices found for order '.$_GET['id'], 'error');
                return false;
            }

            if ($body->invoices[0]->transaction == null) {
                wc_add_notice('No transaction found for order '.$_GET['id'], 'error');
                return false;
            }

            $order = wc_get_order($_GET['id']);

            // Mark as completed
            $order->update_status('completed');
            $order->payment_complete();
            $order->reduce_order_stock();
        }

        /**
         * Process the payment and return the result
         *
         * @param int $order_id
         * @return array
         */
        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);

            // Mark as on-hold (we're awaiting the payment)
            $order->update_status('on-hold', __('Awaiting Perk.Exchange payment', 'wc-gateway-perkexchange'));

            // Remove cart
            WC()->cart->empty_cart();

            $body = array(
                    'amount' => $order->get_total(),
                    'order_id' => ''.$order_id.'',
                    'currency' => 'KIN',
                    'recipient_address' => $this->solana_wallet,
                    'ipn_callback' => site_url('/wc-api/perkexchange_webhook/?id='.$order_id),
                    'paid_url' => $this->get_return_url($order),
                );
            $body = wp_json_encode($body);

            $response = wp_remote_post('https://perk.exchange/api/invoices', array(
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => [
                    'Authorization' => 'Bearer '.$this->campaign_secret,
                    'Content-Type' => 'application/json',
                ],
                'data_format' => 'body',
                'body'        => $body));

            if (is_wp_error($response)) {
                wc_add_notice('Could not create an invoice on Perk.Exchange for order '.$_GET['id'], 'error');

                return array(
                    'result' 	=> 'error',
                    'redirect'	=> 'http://google.ca'
                );
            }

            $body = json_decode(wp_remote_retrieve_body($response));

            // Return payment page redirect
            return array(
                'result' 	=> 'success',
                'redirect'	=> $body->payment_url
            );
        }
    } // end WC_Gateway_PerkExchange class
}
