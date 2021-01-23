<?php 
/**
 * Plugin Name: Woocommerce Customizations
 * Description : Woocommerce additional Custom Features
 */

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	function your_shipping_method_init() {
		if ( ! class_exists( 'WC_Your_Shipping_Method' ) ) {
			class New_Shipping_Method extends WC_Shipping_Method {
				/**
				 * Constructor for your shipping class
				 *
				 * @access public
				 * @return void
				 */
				public function __construct() {
					$this->id                 = 'new_shipping_method'; // Id for your shipping method. Should be uunique.
					$this->method_title       = __( 'New Shipping Method' );  // Title shown in admin
					$this->method_description = __( 'Description: New custom Shipping Method' ); // Description shown in admin

                    // Availability & Countries
                    $this->availability = 'including';
                    $this->countries = array(
                        'US', // Unites States of America
                        'CA', // Canada
                        'DE', // Germany
                        'GB', // United Kingdom
                        'IT', // Italy
                        'ES', // Spain
                        'HR', // Croatia
                        'IN'  // India 
                    );

					$this->enabled            = 'yes';
					$this->title              = __( 'New Shipping', 'tutsplus' );

					$this->init();
				}

				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					// Load the settings API
					$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
					$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

					// Save settings in admin if you have any defined
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
                }


                function init_form_fields() { 
                
                    $this->form_fields = array(

                    'enabled' => array(
                        'title' => __( 'Enable', 'tutsplus' ),
                        'type' => 'checkbox',
                        'description' => __( 'Enable this shipping.', 'tutsplus' ),
                        'default' => 'yes'
                        ),

                    'title' => array(
                        'title' => __( 'Title', 'tutsplus' ),
                        'type' => 'text',
                        'description' => __( 'Title to be display on site', 'tutsplus' ),
                        'default' => __( 'TutsPlus Shipping', 'tutsplus' )
                        ),
                    
                    'weight' => array(
                        'title' => __( 'Weight (kg)', 'tutsplus' ),
                        'type' => 'number',
                        'description' => __( 'Maximum allowed weight', 'tutsplus' ),
                        'default' => 100
                        ),

                    );

                }

				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package=array() ) {

                    $weight = 0;
                    $cost = 0;
                    $country = $package["destination"]["country"];

                    $countryZones=array(
                        'HR' => 0,
                        'US' => 3,
                        'GB' => 2,
                        'CA' => 3,
                        'ES' => 2,
                        'DE' => 1,
                        'IT' => 1,
                        'IN' => 0,
                    );

                    $zonePrices = array(
                        0 => 10,
                        1 => 30,
                        2 => 50,
                        3 => 70
                    );
                    
                    $zoneFromCountry = $countryZones[ $country ];
                    $priceFromZone = $zonePrices[ $zoneFromCountry ];
                
                    $cost += $priceFromZone;

                    $rate = array(
                        'id' => $this->id,
                        'label' => $this->title,
                        'cost' => $cost,
                    );
 
                    $this->add_rate( $rate );
				}
			}
		}
	}

	add_action( 'woocommerce_shipping_init', 'your_shipping_method_init' );

	function add_your_shipping_method( $methods ) {
		$methods['your_shipping_method'] = 'New_Shipping_Method';
		return $methods;
	}

	add_filter( 'woocommerce_shipping_methods', 'add_your_shipping_method' );
}


add_action('woocommerce_cart_calculate_fees', function() {
	if (is_admin() && !defined('DOING_AJAX')) {
		return;
	}
	
	$percentage = 0.05;  // Percentage (5%) in float
	$percentage_fee = (WC()->cart->get_cart_contents_total() + WC()->cart->get_shipping_total()) * $percentage;
 
	WC()->cart->add_fee(__('A small fee', 'txtdomain'), $percentage_fee);
});



/**
 * Woocommerce Custom meta Fields
 */


function my_custom_checkout_field($fields)
{
    $fields['my_new_field']=array(
        'add_field'=>array(
            'type'=>'text',
            'required'=>true,
            'label'=>'Additional Field',
        )
    );

    return $fields;
}

add_filter("woocommerce_checkout_fields","my_custom_checkout_field");

function add_my_custom_field()
{
    $checkout=WC()->checkout();
    echo "<div>";
        echo "<h1>New Field</h1>";
        foreach($checkout->checkout_fields['my_new_field'] as $key=>$field)
        {
            woocommerce_form_field($key, $field, $checkout->get_value($key));
        }
    echo "</div>";
}

add_action("woocommerce_checkout_after_customer_details","add_my_custom_field");

function save_data_from_my_custom_field($order_id, $posted)
{
    if(isset($posted['add_field']))
    {
        update_post_meta($order_id,'_add_field', sanitize_text_field($posted['add_field']));
    }
}

add_action('woocommerce_checkout_update_order_meta','save_data_from_my_custom_field',10,2);

function display_order_data($order_id)
{?>
    <h2>Extra Additional Information</h2>
    <table class="shop_table shop_table_responsive additional_info">
        <tbody>
            <tr>
                <th>Additional Field</th>
                <td><?php echo get_post_meta($order_id,'_add_field',true); ?></td>
            </tr>
        </tbody>
    </table>

<?php
}
add_action('woocommerce_thankyou','display_order_data',20);
add_action('woocommerce_view_order','display_order_data',20);


function display_order_data_in_admin($order) 
{?>
    <div class="order_data_column">
        <h4>Additional Information<a href="#" class="edit_address">Edit</a></h4>
        <div class="address">
            <p><strong>Additional Field</strong> : <?php echo get_post_meta($order_id,'_add_field', true); ?></p>
        </div>
        <div class="edit_address">
            <?php woocommerce_wp_text_input(array(
                'id'=>'_add_field',
                'label'=>'Additional Field',
                'wrapper_class'=>'_billing_company_field'
            )); ?>
        </div>
    </div>

<?php
}

add_action("woocommerce_admin_order_data_after_order_details",'display_order_data_in_admin');

function save_additional_field($post_id, $post)
{
    update_post_meta($post_id,'_add_field',wc_clean($_POST['_add_field']));
}
add_action("woocommerce_process_shop_order_meta",'save_additional_field',45,2);




























// Register main datetimepicker jQuery plugin script
add_action( 'wp_enqueue_scripts', 'enabling_date_time_picker' );
function enabling_date_time_picker() {

    // Only on front-end and checkout page
    if( is_checkout() && ! is_wc_endpoint_url() ) :

    // Load the datetimepicker jQuery-ui plugin script
    wp_enqueue_style( 'datetimepicker', get_stylesheet_directory_uri() . '/assets/css/jquery.datetimepicker.min.css', array());
    wp_enqueue_script('datetimepicker', get_stylesheet_directory_uri() . '/js/jquery.datetimepicker.full.min.js', array('jquery'), '1.0', false );
    endif;
}

// Display custom checkout fields (+ datetime picker)
add_action('woocommerce_before_order_notes', 'display_custom_checkout_fields', 10, 1 );
function display_custom_checkout_fields( $checkout ) {
    // Define the time zone
    date_default_timezone_set('Europe/Paris'); // <== Set the time zone (http://php.net/manual/en/timezones.php)

    echo '<div id="my_custom_checkout_field">
    <h3>'.__('Delivery Info').'</h3>';

    // Hide datetimepicker container field
    echo'<style> #datetimepicker_field.off { display:none; } </style>';

    // Checkbox ASAP
    woocommerce_form_field( 'delivery_asap', array(
        'type'          => 'checkbox',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __("As Soon As Possible", "woocommerce"),
        'checked'       => '',
        'default'       => 0,
    ), '');


    // Checkbox Delivery Date
    woocommerce_form_field( 'delivery_option', array(
        'type'          => 'checkbox',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __("Choose a delivery Date", "woocommerce"),
        'checked'       => '',
        'default'       => 0,
    ), '');

    // DateTimePicker
    woocommerce_form_field( 'delivery_date', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide off'),
        'id'            => 'datetimepicker',
        'required'      => false,
        'label'         => __('Select date'),
        'placeholder'   => __(''),
        'options'       => array('' => __('', 'woocommerce' ))
    ),'');

    echo '</div>';
}

// The jQuery script
add_action( 'wp_footer', 'checkout_delivery_jquery_script');
function checkout_delivery_jquery_script() {
    // Only on front-end and checkout page
    if( is_checkout() && ! is_wc_endpoint_url() ) :

    ?>
    <script>
    jQuery(function($){
        var a = 'input[name="delivery_asap"]',
            c = 'input[name="delivery_option"]',
            d = '#datetimepicker',
            f = d+'_field';

        $(f).hide();
        $(a).prop('checked', true);

        // First checkbox
        $(a).change(function(){
            if( $(this).prop('checked') == true ){
                $(f).hide();
                $(c).prop('checked', false);
            } else {
                $(f).show();
                $(c).prop('checked', true);
            }
        });

        // Second checkbox
        $(c).change(function(){
            if( $(this).prop('checked') == true ){
                $(f).show();
                $(a).prop('checked', false);
            } else {
                $(f).hide();
                $(a).prop('checked', true);
            }
        });

        $(d).datetimepicker({
            format: 'd.m.Y H:i',
            allowTimes:[ '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30',
                '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30',
                '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00', '22:30']
        });
    });
    </script>
    <?php

    endif;
}

// Check that the delivery date is not empty when it's selected
add_action( 'woocommerce_checkout_process', 'check_datetimepicker_field' );
function check_datetimepicker_field() {
    if ( isset($_POST['delivery_option']) && empty($_POST['delivery_date']) ) {
        wc_add_notice( __( 'Error: You must choose a delivery date and time', 'woocommerce' ), 'error' );
    }
}

// Check that the delivery date is not empty when it's selected
add_action( 'woocommerce_checkout_create_order', 'save_order_delivery_data', 10, 2 );
function save_order_delivery_data( $order, $data ) {
    if ( isset($_POST['delivery_option']) && $_POST['delivery_option'] && ! empty($_POST['delivery_date']) ) {
        $order->update_meta_data( '_delivery_datetime', sanitize_text_field( $_POST['delivery_date'] ) );
        $order->update_meta_data( '_delivery_option', 'date' );
    } elseif( isset($_POST['delivery_asap']) && $_POST['delivery_asap'] ) {
        $order->update_meta_data( '_delivery_option', 'azap' );
    }
}