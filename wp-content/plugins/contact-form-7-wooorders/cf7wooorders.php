<?php
/*
Plugin Name: Contact Form 7 WooCommerce Order
Plugin URI: http://codeboxr.com/product/contact-form-7-woocommerce-orders
Description: Woocommerce Customer Orders Dropdown Selector
Author: Codeboxr
Author URI: http://codeboxr.com
Version: 1.0.3
*/

register_activation_hook(__FILE__, 'wpcf7_wooorders_activation');

function wpcf7_wooorders_activation(){

    //Check if contact form 7 active
    if ( !in_array( 'contact-form-7/wp-contact-form-7.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {

        // Deactivate the plugin
        deactivate_plugins(__FILE__);

        // Throw an error in the wordpress admin console
        $error_message = __('This plugin requires <a target="_blank" href="https://wordpress.org/plugins/contact-form-7/">Contact Form 7</a> plugin to be active!', 'contactform7wooorders');
        die($error_message);

    }

	//Check if woocommerce is active

	if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

		// Deactivate the plugin
		deactivate_plugins(__FILE__);

		// Throw an error in the wordpress admin console
		$error_message = __('This plugin requires <a target="_blank" href="https://wordpress.org/plugins/woocommerce">Woocommerce</a> plugin to be active!', 'contactform7wooorders');
		die($error_message);

	}
}

function wpcf7_wooorderdropdown_load_plugin_textdomain()
{
	load_plugin_textdomain('contactform7wooorders', false, basename(dirname(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'wpcf7_wooorderdropdown_load_plugin_textdomain');


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wpboxr_cf7wooorders' );

function wpboxr_cf7wooorders( $links ) {
	$links[] = '<a href="http://Codeboxr.com/product/contact-form-7-woocommerce-orders" target="_blank">Codeboxr</a>';
	return $links;
}

/* Shortcode handler */

add_action( 'wpcf7_init', 'wpcf7_add_shortcode_wooorders' );

function wpcf7_add_shortcode_wooorders() {
    if(function_exists('wpcf7_add_form_tag')){
        wpcf7_add_form_tag(array( 'wooorders', 'wooorders*' ),	'wpcf7_wooorders_shortcode_handler', true);
    }
    else{
        wpcf7_add_shortcode( array( 'wooorders', 'wooorders*' ),	'wpcf7_wooorders_shortcode_handler', true );
    }

}

function wpcf7_wooorders_shortcode_handler( $tag ) {
    if(class_exists('WPCF7_FormTag')){
        $tag = new WPCF7_FormTag($tag);
    }
    else{

        $tag = new WPCF7_Shortcode( $tag );
    }

	if ( empty( $tag->name ) )
		return '';

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error )
		$class .= ' wpcf7-not-valid';

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

	if ( $tag->is_required() )
		$atts['aria-required'] = 'true';

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$multiple = $tag->has_option( 'multiple' );
	//$include_blank = $tag->has_option( 'include_blank' );
	//$first_as_label = $tag->has_option( 'first_as_label' );

	$values     = array();
	$labels     = array();

	$customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
		'numberposts' => -1,
		'meta_key'    => '_customer_user',
		'meta_value'  => get_current_user_id(),
		'post_type'   => wc_get_order_types( 'view-orders' ),
		'post_status' => array_keys( wc_get_order_statuses() )
	) ) );


	foreach ( $customer_orders as $customer_order ) {

        $order = new WC_Order($customer_order );
		//$order = wc_get_order( $customer_order );
		//$order->populate( $customer_order );
		//$item_count = $order->get_item_count();

		$labels[] = '#'.$order->get_order_number().'( Status: '.wc_get_order_status_name( $order->get_status()) .')';
		$values[] = ''.$order->get_order_number();
	}


	$defaults = array();

	$default_choice = $tag->get_default_option( null, 'multiple=1' );

	foreach ( $default_choice as $value ) {
		$key = array_search( $value, $values, true );

		if ( false !== $key ) {
			$defaults[] = (int) $key + 1;
		}
	}

	if ( $matches = $tag->get_first_match_option( '/^default:([0-9_]+)$/' ) ) {
		$defaults = array_merge( $defaults, explode( '_', $matches[1] ) );
	}

	$defaults = array_unique( $defaults );

	$shifted = false;

	//if ( $include_blank || empty( $values ) ) {
		//array_unshift( $labels, '---' );
	    array_unshift($labels, __('-- Select Order --', 'contactform7wooorders'));
		array_unshift( $values, '' );
		$shifted = true;
	//} elseif ( $first_as_label ) {
		//$values[0] = '';
		//$labels[]
	//}

	$html = '';
	$hangover = wpcf7_get_hangover( $tag->name );

	foreach ( $values as $key => $value ) {
		$selected = false;

		if ( $hangover ) {
			if ( $multiple ) {
				$selected = in_array( esc_sql( $value ), (array) $hangover );
			} else {
				$selected = ( $hangover == esc_sql( $value ) );
			}
		} else {
			if ( ! $shifted && in_array( (int) $key + 1, (array) $defaults ) ) {
				$selected = true;
			} elseif ( $shifted && in_array( (int) $key, (array) $defaults ) ) {
				$selected = true;
			}
		}

		$item_atts = array(
			'value' => $value,
			'selected' => $selected ? 'selected' : '' );

		$item_atts = wpcf7_format_atts( $item_atts );

		$label = isset( $labels[$key] ) ? $labels[$key] : $value;

		$html .= sprintf( '<option %1$s>%2$s</option>',
			$item_atts, esc_html( $label ) );
	}

	if ( $multiple )
		$atts['multiple'] = 'multiple';

	$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
		sanitize_html_class( $tag->name ), $atts, $html, $validation_error );

	return $html;
}


/* Validation filter */

add_filter( 'wpcf7_validate_wooorders', 'wpcf7_wooorders_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_wooorders*', 'wpcf7_wooorders_validation_filter', 10, 2 );
//add_filter( 'wpcf7_validate_select*', 'wpcf7_select_validation_filter', 10, 2 );

function wpcf7_wooorders_validation_filter( $result, $tag ) {
	//$tag = new WPCF7_Shortcode( $tag );
    if(class_exists('WPCF7_FormTag')){
        $tag = new WPCF7_FormTag($tag);
    }
    else{

        $tag = new WPCF7_Shortcode( $tag );
    }

	$name = $tag->name;

	if ( isset( $_POST[$name] ) && is_array( $_POST[$name] ) ) {
		foreach ( $_POST[$name] as $key => $value ) {
			if ( '' === $value )
				unset( $_POST[$name][$key] );
		}
	}

	$empty = ! isset( $_POST[$name] ) || empty( $_POST[$name] ) && '0' !== $_POST[$name];

	if ( $tag->is_required() && $empty ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	}

	return $result;
}


/* Tag generator */

add_action( 'admin_init', 'wpcf7_add_tag_generator_wooorders', 25 );

function wpcf7_add_tag_generator_wooorders() {
    if(!class_exists('WPCF7_TagGenerator')) return;

	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'wooorders', __( 'WooCommerce Order Dropdown', 'contact-form-7' ), 'wpcf7_tag_generator_wooorders' );
}

function wpcf7_tag_generator_wooorders( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );

	$description = __( "Generate a form-tag for a drop-down menu. For more details, see %s.", 'contact-form-7' );

	$desc_link = wpcf7_link( __( 'http://contactform7.com/checkboxes-radio-buttons-and-menus/', 'contact-form-7' ), __( 'Checkboxes, Radio Buttons and Menus', 'contact-form-7' ) );

	?>
	<div class="control-box">
		<fieldset>
			<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
							<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
					<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
				</tr>

				<tr>
					<th scope="row"><?php echo esc_html( __( 'Options', 'contact-form-7' ) ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><?php echo esc_html( __( 'Options', 'contact-form-7' ) ); ?></legend>
							<!--textarea name="values" class="values" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"></textarea>
							<label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><span class="description"><?php echo esc_html( __( "One option per line.", 'contact-form-7' ) ); ?></span></label><br /-->
							<label><input type="checkbox" name="multiple" class="option" /> <?php echo esc_html( __( 'Allow multiple selections', 'contact-form-7' ) ); ?></label><br />
							<!--label><input type="checkbox" name="include_blank" class="option" /> <?php echo esc_html( __( 'Insert a blank item as the first option', 'contact-form-7' ) ); ?></label-->
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
					<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
				</tr>

				<tr>
					<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
					<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
				</tr>

				</tbody>
			</table>
		</fieldset>
	</div>

	<div class="insert-box">
		<input type="text" name="wooorders" class="tag code" readonly="readonly" onfocus="this.select()" />

		<div class="submitbox">
			<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
		</div>

		<br class="clear" />

		<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
	</div>
<?php
}

	add_shortcode('cbxcf7wooporder_url', 'cbxcf7wooporder_url_function');

	function cbxcf7wooporder_url_function($atts)
	{
		global $wpdb;

		if (empty($atts)) {
			return 'N/A';
		}

		if(!class_exists('WC_Order')) return 'N/A';


		$atts = shortcode_atts(array(
			'id'            => '',
			'rich'          => 0, // 1 = rich text hyperlink, 0 = plain text link
			'edit'          => 0

		), $atts);

		if (isset($atts['id']) && $atts['id'] != '') {

			$ids = explode(',', $atts['id']);
			$output =  array();
			foreach ($ids as $id){
				if (intval($atts['edit'])) {
					$link = esc_url(get_edit_post_link($id));
				} else {
					//product frontend link
					$order      = wc_get_order( $id);
					$link 		= esc_url($order->get_view_order_url());
				}

				$output[] =  ($atts['rich']) ? '<a href="' . $link . '">#' .$id . '</a>' : '#'.$id . ' - ' . $link;

			}
			return implode(', ',$output);

		} else {
			return 'N/A';
		}
	}

	if(!function_exists('cbxcf7wooporder_mail_components_body_do_shortcode')){
		add_filter('wpcf7_mail_components', 'cbxcf7wooporder_mail_components_body_do_shortcode', 50, 2);

		/**
		 * Parse any shortcode in cf7 email body. normally regular cf7 shortcode is parsed by it's own, but if you use any other wordpress
		 * shortcode in the email body this method will do the job using do_shortcode
		 *
		 * @param $mail_params
		 * @param null $form
		 *
		 * @return mixed
		 */
		function cbxcf7wooporder_mail_components_body_do_shortcode($mail_params, $form = null)
		{

			$mail_params['body'] = do_shortcode($mail_params['body']);

			return $mail_params;
		}
	}