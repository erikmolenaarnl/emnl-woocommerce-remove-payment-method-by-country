<?php 
/**
 * Plugin Name: Woocommerce Remove Payment Method by Country
 * Description: This plugin will remove payment methods by country code(s). You can choose wheteter do remove it for the specified country code(s) or show it only to other countries. Please note: rules are hardcoded in this plugin.
 * Author: Erik Molenaar
 * Author URI: https://erikmolenaar.nl
 * Version: 1.1
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** 
 * Returns modified payment method array.
 * 
 * @param   array   $payment_methods    Original array from the WC filter.
 * @return  array   $payment_methods    Modified array for WC filter.
 */
add_filter( 'woocommerce_available_payment_gateways', 'emnl_rpmbc_modify_available_payment_gateways' );
function emnl_rpmbc_modify_available_payment_gateways( $payment_methods ) {

    // Stop on admin pages
    if ( is_admin() ) return $payment_methods;

    // Remove if NOT Netherlands
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'ideal', 'NL', false ); // Sisow
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'pay_gateway_ideal', 'NL', false ); // Pay.nl
    
    // Remove if NOT Belgium
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'mistercash', 'BE', false ); // Sisow
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'pay_gateway_mistercash', 'BE', false ); // Pay.nl

    // Remove if NOT Germany
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'giropay', 'DE', false ); // Sisow
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'pay_gateway_giropay', 'DE', false ); // Pay.nl

    // Remove EPS if NOT Austria
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'pay_gateway_eps', 'AT', false );

    // Remove if NOT Portugal
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'pay_gateway_multibanco', 'PT', false );
    
    // Remove if NOT Poland
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'pay_gateway_p24', 'PL', false );

    // Remove if NOT France
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'pay_gateway_cartebleue', 'FR', false );

    // Remove if NOT Denmark
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'pay_gateway_dankort', 'DK', false );

    // Remove if NOT Italy
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'pay_gateway_postepay', 'IT', false );
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'pay_gateway_cartasi', 'IT', false );

    // Remove Mybank if NOT Italy, Spain, Greece, France and Luxembourg
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'pay_gateway_mybank', array( 'IT', 'ES', 'GR', 'FR', 'LU' ), false );
    
    // Remove if NOT Germany or Austria
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'pay_gateway_sofortbanking', array( 'DE', 'AT' ), false );

    // Remove Paypal for countries that have a local payment method available
    $payment_methods = emnl_rpmbc_remove_payment_method( $payment_methods, 'paypal', array( 'NL', 'BE', 'DE', 'AT', 'PT', 'IT', 'ES', 'GR', 'FR', 'LU', 'PL', 'DK' ), true );

    // Return the (modified) payment methods
    return $payment_methods;
    
}

/**
 * Returns modified payment method array where specified payment method is unset if country code is NOT matched.
 * 
 * @param array     $payment_methods    Array of the WC filter containing available payment methods.
 * @param string    $payment_method     Payment method, e.g. 'ideal' or 'paypal'.
 * @param mixed     $countries          String or array of country code slug(s), e.g. 'NL' or array( 'NL','BE' ).
 * @param bolean    $remove_for_these   How to handle $countries. Remove for these countries or only show to others?
 * 
 * @return array    $payment_methods    Modified array for the WC filter.
 *  */
function emnl_rpmbc_remove_payment_method( $payment_methods, $payment_method, $countries, $remove_for_these ) {

    // If argument $countries is an string, convert to array
    if ( ! is_array ( $countries ) ) {
        $countries = array( $countries );
    }

    // Get billing country from session (if it exists)
    if ( ! isset( WC()->customer ) ) return $payment_methods;
    $billing_country = WC()->customer->get_billing_country();

    // Check if there is a country match
    $country_match = in_array( $billing_country, $countries );

    // How to handle $countries. Remove for these countries or only show to others?
    if ( $remove_for_these === false ) {
        $country_match = ! $country_match;
    }

    // If payment method is in array, remove it based on previous set condition
    if ( isset( $payment_methods[$payment_method] ) && $country_match ) {
        unset( $payment_methods[$payment_method] );
    }

    return $payment_methods;

}