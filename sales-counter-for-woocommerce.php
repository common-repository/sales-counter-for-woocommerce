<?php
/*
Plugin Name: Sales Counter For WooCommerce
Plugin URI: https://themepoints.com/sales-counter-for-woocommerce
Description: This plugin showing all the sales report on your WooCommerce products. This plugin only works if the WooCommerce is activate.
Version: 1.0.1
Author: Themepoints
Author URI: https://themepoints.com
Text Domain: sales-counter-for-woocommerce
Domain Path: /languages
License: GPLv2
*/

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Sales_Counter_For_Woocommerce class
 *
 * @class Sales_Counter_For_Woocommerce The class that holds the entire Sales_Counter_For_Woocommerce plugin
 */

final class Sales_Counter_For_Woocommerce {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '1.0.1';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = false;

    /**
     * Constructor for the Sales_Counter_For_Woocommerce class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses add_action()
     */
    public function __construct(){
        $this->define_constants();
        $this->file_includes();

        add_action( 'admin_init' , array( $this, 'register_sales_counter_for_woocommerce_settings' ) );
        add_action( 'admin_menu' , array( $this, 'register_sales_counter_for_woocommerce_counter_menu' ) );

		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		
		} else{
        	add_action( 'admin_notices', array( $this, 'sales_counter_for_woocommerce_missing_notice' ) );
		}
        // Localize our plugin
        add_action( 'init', array( $this, 'localization_setup' ) );
    }

    /**
     * Initializes the Sales_Counter_For_Woocommerce() class
     *
     * Checks for an existing Sales_Counter_For_Woocommerce() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {

        if ( ! self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Adds a submenu page under a custom post type parent.
     */
    public function register_sales_counter_for_woocommerce_counter_menu(){
        add_submenu_page( 'woocommerce','Sales Counter For WooCommerce','Sales Counter','manage_options','sales-coutner-for-woocommerce',array( $this, 'init_sales_counter_for_woocommerce_admin_page' ) );
    }

    /**
     * Register All Settings
     */
    public function register_sales_counter_for_woocommerce_settings(){
         // register a new setting for
        register_setting( 'sales-counter-wc-all-settings', 'sales_counter_enable_check' );
        register_setting( 'sales-counter-wc-all-settings', 'sales_counter_single_enable_check' );
        register_setting( 'sales-counter-wc-all-settings', 'sales_counter_enable_title' );
        register_setting( 'sales-counter-wc-all-settings', 'sales_counter_wc_select_option' );
        register_setting( 'sales-counter-wc-all-settings', 'sales_counter_wc_select_single_option' );
        register_setting( 'sales-counter-wc-all-settings', 'sales_counter_zero_disable_check' );
        register_setting( 'sales-counter-wc-all-settings', 'sales_counter_zero_custom_message' );
    }

    /**
     * Init Plugin Page
     */
    public function init_sales_counter_for_woocommerce_admin_page(){
        ?>
        <div class="wrap">
            <h1><?php _e( 'Sales Counter For WooCommerce', 'textdomain' ); ?></h1>
            <p><?php _e( 'Helpful stuff here', 'textdomain' ); ?></p>

            <form method="post" action="options.php">
                <?php settings_fields( 'sales-counter-wc-all-settings' ); ?>
                <?php do_settings_sections( 'sales-counter-wc-all-settings' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Enable Sales Counter on Product Page', 'sales-counter-for-woocommerce' ); ?></th>
                        <td><input type="checkbox" id="sales_counter_enable_check" name="sales_counter_enable_check" value="1" <?php echo esc_attr( checked( get_option( 'sales_counter_enable_check' ) ), false ); ?>/></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e( 'Default Product Sales Text', 'sales-counter-for-woocommerce' ); ?></th>
                        <td><input type="text" name="sales_counter_enable_title" placeholder="Sales" value="<?php echo esc_attr( get_option('sales_counter_enable_title') ); ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e( 'Sales Counter Position for Product Page', 'sales-counter-for-woocommerce' ); ?></th>
                        <td>
                            <select name="sales_counter_wc_select_option">
                              <option value="1" <?php selected( get_option( 'sales_counter_wc_select_option' ), "1" ); ?>><?php _e( 'After product title', 'sales-counter-for-woocommerce' ); ?></option>
                              <option value="2" <?php selected( get_option( 'sales_counter_wc_select_option' ), "2" ); ?>><?php _e( 'Before product Title', 'sales-counter-for-woocommerce' ); ?></option>
                              <option value="3" <?php selected( get_option( 'sales_counter_wc_select_option' ), "3" ); ?>><?php _e( 'Before product price', 'sales-counter-for-woocommerce' ); ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e( 'Enable Sales Counter on Product Single Page', 'sales-counter-for-woocommerce' ); ?></th>
                        <td><input type="checkbox" id="sales_counter_single_enable_check" name="sales_counter_single_enable_check" value="1" <?php echo esc_attr( checked( get_option( 'sales_counter_single_enable_check' ) ), false ); ?>/></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e( 'Sales Counter Position for Product Single Page', 'sales-counter-for-woocommerce' ); ?></th>
                        <td>
                            <select name="sales_counter_wc_select_single_option">
                              <option value="1" <?php selected( get_option( 'sales_counter_wc_select_single_option' ), "1" ); ?>><?php _e( 'After Add To Cart Button', 'sales-counter-for-woocommerce' ); ?></option>
                              <option value="2" <?php selected( get_option( 'sales_counter_wc_select_single_option' ), "2" ); ?>><?php _e( 'Before Add To Cart Button', 'sales-counter-for-woocommerce' ); ?></option>
                              <option value="3" <?php selected( get_option( 'sales_counter_wc_select_single_option' ), "3" ); ?>><?php _e( 'Single Product Summery', 'sales-counter-for-woocommerce' ); ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e( 'Disable Sales Counter for 0 order', 'sales-counter-for-woocommerce' ); ?></th>
                        <td><input type="checkbox" id="sales_counter_zero_disable_check" name="sales_counter_zero_disable_check" value="1" <?php echo esc_attr( checked( get_option( 'sales_counter_zero_disable_check' ) ), false ); ?>/></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e( 'Custom Message for 0 order', 'sales-counter-for-woocommerce' ); ?></th>
                        <td><input type="text" name="sales_counter_zero_custom_message" placeholder="custom message for 0 order products" value="<?php echo esc_attr( get_option('sales_counter_zero_custom_message') ); ?>" /></td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

	/**
	 * WooCommerce fallback notice.
	 *
	 * @return string
	 */
	public function sales_counter_for_woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'Sales Counter For WooCommerce says "There must be active install of %s to take a flight!"', 'sales-counter-for-woocommerce' ), '<a href="https://woocommerce.com" target="_blank">' . __( 'WooCommerce', 'sales-counter-for-woocommerce' ) . '</a>' ) . '</p></div>';
		if ( isset( $_GET['activate'] ) )
             unset( $_GET['activate'] );
	}

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'sales-counter-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Includes required files
     *
     * @return void
     */
    function file_includes() {
        require_once dirname( __FILE__ ) . '/includes/init.php';
    }

    /**
     * Define some constants
     *
     * @return void
     */
    function define_constants() {
        define( 'WCSALESCOUNTER_PATH', $this->plugin_path() );
        define( 'WCSALESCOUNTER_URL', $this->plugin_url() );
    }

    /**
     * Get the plugin url.
     *
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', __FILE__ ) );
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    
} //Sales_Counter_For_Woocommerce


/**
 * Returns the main instance of wp_sales_counter_sales_report to prevent the need to use globals.
 *
 * @return \Sales_Counter_For_Woocommerce
 */
function wp_sales_counter_sales_report() {
    return Sales_Counter_For_Woocommerce::init();
}

// initialize the plugin
wp_sales_counter_sales_report();