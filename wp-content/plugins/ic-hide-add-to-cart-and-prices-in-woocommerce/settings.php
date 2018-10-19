<?php

class IcHdSettings {

    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_ic_hd_settings', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_ic_hd_settings', __CLASS__ . '::update_settings' );
    }
    
    
    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['ic_hd_settings'] = __( 'HACP Plugin', 'ic-hide-add-to-cart' );
        return $settings_tabs;
    }


    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }


    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }




    public static function get_categories() {

        $ic_category_args = array( 
                'taxonomy' => 'product_cat',
                 'orderby'   =>'name',
                  'parent'  => 0
                 );

          $product_name = get_categories($ic_category_args);

        
        
        foreach ($product_name as $term) { 
                $term_array[$term->term_id] = $term->name;
          
        }
        return $term_array;
    }

    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {

 

        $settings = array(
            'section_title' => array(
                'name'     => __( 'IC Hide Add to Cart and prices in WooCommerce', 'ic-hide-add-to-cart' ),
                'type'     => 'title',
                'desc'     => __('Settings of the plugin','ic-hide-add-to-cart'),
                'id'       => 'wc_settings_section_title'
            ),
			
            'hide_add_to_cart' => array(
                'name' => __( 'Turn off WooCommerce', 'ic-hide-add-to-cart' ),
                'type' => 'checkbox',
                'desc' => __( 'Check to disable all Add to Cart buttons', 'ic-hide-add-to-cart' ),
                'id'   => 'wc_settings_checkbox_hide_add_cart'
            ),
			
             'hide_add_to_cart_category' => array(
                'name' => __( 'Turn off WooCommerce by category', 'ic-hide-add-to-cart' ),
                'type' => 'multiselect',
                'class'    => 'wc-enhanced-select',
                'id'   => 'wc_settings_hide_add_to_cart_category',
                'options' => self::get_categories()
                
            ),

			
			 'hide_prices' => array(
                'name' => __( 'Turn off products prices', 'ic-hide-add-to-cart' ),
                'type' => 'checkbox',
                'desc' => __( 'Check to hide prices for all products', 'ic-hide-add-to-cart' ),
                'id'   => 'wc_settings_checkbox_hide_prices'
            ),


            'hide_prices_category' => array(
                'name' => __( 'Turn off products prices by category', 'ic-hide-add-to-cart' ),
                'type' => 'multiselect',
                'class'    => 'wc-enhanced-select',
                'id'   => 'wc_settings_hide_prices_category',
                'options' => self::get_categories()
                
            ),


            'custom_prices_text' => array(
                'name' => __( 'Personalized text for hidden prices', 'ic-hide-add-to-cart' ),
                'type' => 'text',
                'placeholder' => __( 'Insert custom price text here', 'ic-hide-add-to-cart' ),
                'id'   => 'wc_settings_custom_prices_text'
            ),
			
			
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_section_end'
            )
        );

        return apply_filters( 'ic_hd_settings', $settings );
    }


}

IcHdSettings::init();