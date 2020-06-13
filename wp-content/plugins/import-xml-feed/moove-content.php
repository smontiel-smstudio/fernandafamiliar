<?php
/**
 * Moove_Importer_Content File Doc Comment
 *
 * @category 	Moove_Importer_Content
 * @package   moove-feed-importer
 * @author    Gaspar Nemes
 */

load_textdomain( 'moove', plugins_url( __FILE__ ) . DIRECTORY_SEPARATOR . 'languages' );

/**
 * Moove_Importer_Content Class Doc Comment
 *
 * @category Class
 * @package  Moove_Importer_Content
 * @author   Gaspar Nemes
 */
class Moove_Importer_Content {
	/**
	 * Construct
	 */
	public function __construct() {
	}


	public static function get_license_token() {
		$license_token = function_exists('network_site_url') ? network_site_url('/') : home_url('/');
		return $license_token;
	}

	public static function moove_xml_get_key_name() {
		return 'moove_xml_plugin_key';
	}

	public static function xml_licence_action_button( $response, $xml_key ) {
		$type = isset( $response['type'] ) ? $response['type'] : false;
		if ( $type === 'expired' || $type === 'activated' || $type === 'max_activation_reached' ) : ?>
			<button type="submit" name="xml_activate_license" class="button button-primary button-inverse">
				<?php _e('Activate','import-xml-feed'); ?>
			</button>
			<?php
		elseif ( $type === 'invalid' ) :
			?>
			<button type="submit" name="xml_activate_license" class="button button-primary button-inverse">
				<?php _e('Activate','import-xml-feed'); ?>
			</button>
			<?php
		else :
			?>
			<button type="submit" name="xml_activate_license" class="button button-primary button-inverse">
				<?php _e('Activate','import-xml-feed'); ?>
			</button>
			<br /><br />
			<hr />
			<h4 style="margin-bottom: 0;"><?php _e('Buy licence','import-xml-feed'); ?></h4>
			<p>
				<?php 
				$store_link = __('You can buy licences from our [store_link]online store[/store_link].','import-xml-feed');
				$store_link = str_replace('[store_link]', '<a href="https://www.mooveagency.com/wordpress-plugins/import-xml-feed/" target="_blank" class="xml_admin_link">', $store_link );
				$store_link = str_replace('[/store_link]', '</a>', $store_link );
				echo $store_link;
				?>
			</p>
			<p>
				<a href="https://www.mooveagency.com/wordpress-plugins/import-xml-feed/" target="_blank" class="button button-primary">Buy Now</a>
			</p>
			<br />
			<hr />

			<?php
		endif;
	}

	public static function xml_licence_input_field( $response, $xml_key ) {
		$type = isset( $response['type'] ) ? $response['type'] : false;
		if ( $type === 'expired' ) :
			// LICENSE EXPIRED
			?>
			<tr>
				<th scope="row" style="padding: 0 0 10px 0;">
					<hr />
					<h4 style="margin-bottom: 0;"><?php _e('Renew your licence','import-xml-feed'); ?></h4>
					<p><?php _e('Your licence has expired. You will not receive the latest updates and features unless you renew your licence.','import-xml-feed'); ?></p>
					<a href="<?php echo MOOVE_SHOP_URL; ?>?renew=<?php echo $response['key']; ?>" class="button button-primary">Renew Licence</a>
					<br /><br />
					<hr />

					<h4 style="margin-bottom: 0;"><?php _e('Enter new licence key','import-xml-feed'); ?></h4>
				</th>
			</tr>
			
			<tr>
				<td style="padding: 0;">
					<input name="moove_xml_license_key" required min="35" type="text" id="moove_xml_license_key" value="" class="regular-text">
				</td>
			</tr>
			<?php
		elseif ( $type === 'activated' || $type === 'max_activation_reached' ) :
			// LICENSE ACTIVATED
			?>
			<tr>
				<th scope="row" style="padding: 0 0 10px 0;">
					<hr />
					<h4 style="margin-bottom: 0;"><?php _e('Buy more licences','import-xml-feed'); ?></h4>
					<p>
						<?php 
						$store_link = __('You can buy more licences from our [store_link]online store[/store_link].','import-xml-feed');
						$store_link = str_replace('[store_link]', '<a href="https://www.mooveagency.com/wordpress-plugins/import-xml-feed/" target="_blank" class="xml_admin_link">', $store_link );
						$store_link = str_replace('[/store_link]', '</a>', $store_link );
						echo $store_link;
						?>
					</p>
					<p>
						<a href="https://www.mooveagency.com/wordpress-plugins/import-xml-feed/" target="_blank" class="button button-primary">Buy Now</a>
					</p>
					<br />
					<hr />

					<h4 style="margin-bottom: 0;"><?php _e('Enter new licence key','import-xml-feed'); ?></h4>
				</th>
			</tr>
			
			<tr>
				<td style="padding: 0;">
					<input name="moove_xml_license_key" required min="35" type="text" id="moove_xml_license_key" value="" class="regular-text">
				</td>
			</tr>
			<?php
		elseif ( $type === 'invalid' ) :
			?>
			<tr>
				<th scope="row" style="padding: 0 0 10px 0;">
					<hr />
					<h4 style="margin-bottom: 0;"><?php _e('Buy licence','import-xml-feed'); ?></h4>
					<p>
						<?php 
						$store_link = __('You can buy licences from our [store_link]online store[/store_link].','import-xml-feed');
						$store_link = str_replace('[store_link]', '<a href="https://www.mooveagency.com/wordpress-plugins/import-xml-feed/" target="_blank" class="xml_admin_link">', $store_link );
						$store_link = str_replace('[/store_link]', '</a>', $store_link );
						echo $store_link;
						?>
					</p>
					<p>
						<a href="https://www.mooveagency.com/wordpress-plugins/import-xml-feed/" target="_blank" class="button button-primary">Buy Now</a>
					</p>
					<br />
					<hr />
				</th>
			</tr>
			<tr>
				<th scope="row" style="padding: 0 0 10px 0;">
					<label><?php _e('Enter your licence key:','import-xml-feed'); ?></label>
				</th>
			</tr>
			
			<tr>
				<td style="padding: 0;">
					<input name="moove_xml_license_key" required min="35" type="text" id="moove_xml_license_key" value="" class="regular-text">
				</td>
			</tr>
			<?php
		else :
			?>
			
			<tr>
				<th scope="row" style="padding: 0 0 10px 0;">
					<label><?php _e('Enter licence key:','import-xml-feed'); ?></label>
				</th>
			</tr>
			
			<tr>
				<td style="padding: 0;">
					<input name="moove_xml_license_key" required min="35" type="text" id="moove_xml_license_key" value="" class="regular-text">
				</td>
			</tr>


			<?php
		endif;
	}

	public static function xml_get_alertbox( $type, $response, $xml_key ) {
		if ( $type === 'error' ) :
			$messages = isset( $response['message'] ) && is_array( $response['message'] ) ? implode( '</p><p>', $response['message'] ) : '';
			if ( $response['type'] === 'inactive' ) :
				$xml_default_content 	= new Moove_Importer_Content();
				$option_key           = $xml_default_content->moove_xml_get_key_name();
				$xml_key             	= function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
				if ( function_exists( 'update_site_option' ) ) :
					update_site_option(
						$option_key,
						array(
							'key'          => $response['key'],
							'deactivation' => strtotime( 'now' ),
						)
					);
				else :
					update_option(
						$option_key,
						array(
							'key'          => $response['key'],
							'deactivation' => strtotime( 'now' ),
						)
					);
				endif;
				$xml_key = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
			endif;
			?>
			<div class="xml-admin-alert xml-admin-alert-error">
				<div class="xml-alert-content">        
					<p>License key: <strong><?php echo isset( $response['key'] ) ? $response['key'] : ( isset( $xml_key['key'] ) ? $xml_key['key'] : $xml_key ) ; ?></strong></p>
					<p><?php echo $messages; ?></p>
				</div>
				<span class="dashicons dashicons-dismiss"></span>
			</div>
			<!--  .xml-admin-alert xml-admin-alert-success -->
			<?php
		else :
			$messages       = isset( $response['message'] ) && is_array( $response['message'] ) ? implode( '</p><p>', $response['message'] ) : '';
			?>
			<div class="xml-admin-alert xml-admin-alert-success">    
				<div class="xml-alert-content">         
					<p>License key: <strong><?php echo isset( $response['key'] ) ? $response['key'] : ( isset( $xml_key['key'] ) ? $xml_key['key'] : $xml_key ) ; ?></strong></p>
					<p><?php echo $messages; ?></p>
				</div>
				<span class="dashicons dashicons-yes-alt"></span>
			</div>
			<!--  .xml-admin-alert xml-admin-alert-success -->
			<?php
		endif;
		do_action('xml_plugin_updater_notice');
	}

	public static function xml_premium_update_alert() {

		$plugins = get_site_transient( 'update_plugins' );
		$lm                 = new Moove_XML_License_Manager();
		$plugin_slug        = $lm->get_add_on_plugin_slug();

		if ( isset( $plugins->response[$plugin_slug] ) && is_plugin_active( $plugin_slug ) ) :
			$version = $plugins->response[$plugin_slug]->new_version;

			$current_user         = wp_get_current_user();
			$user_id              = isset( $current_user->ID ) ? $current_user->ID : 0;
			$dismiss              = get_option( 'xml_hide_update_notice_' . $user_id );
			if ( isset( $plugins->response[$plugin_slug]->package ) && ! $plugins->response[$plugin_slug]->package ) :
				$xml_default_content  = new Moove_Importer_Content();
				$option_key           = $xml_default_content->moove_xml_get_key_name();
				$xml_key              = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
				$license_key          = isset( $xml_key['key'] ) ? sanitize_text_field( $xml_key['key'] ) : false;
				$renew_link           = MOOVE_SHOP_URL . '?renew='.$license_key;
				$license_manager      = admin_url('options-general.php') . '?page=moove-importer&amp;tab=licence';
				$purchase_link        = 'https://www.mooveagency.com/wordpress-plugins/import-xml-feed/';
				$notice_text          = '';
				if ( $license_key && isset( $xml_key['activation'] ) ) :
					// Expired.
					$notice_text = 'Update is not available until you <a href="'.$renew_link.'" target="_blank">renew your licence</a>. You can also update your licence key in the <a href="'.$license_manager.'">Licence Manager</a>.';
				elseif ( $license_key && isset( $xml_key['deactivation'] ) ) :
					// Deactivated.
					$notice_text = 'Update is not available until you <a href="'.$purchase_link.'" target="_blank">purchase a licence</a>. You can also update your licence key in the <a href="'.$license_manager.'">Licence Manager</a>.';
				elseif ( ! $license_key ) :
					// No license key installed.
					$notice_text = 'Update is not available until you <a href="'.$purchase_link.'" target="_blank">purchase a licence</a>. You can also update your licence key in the <a href="'.$license_manager.'">Licence Manager</a>.';
				endif;  
				?>
					<div class="xml-cookie-alert xml-cookie-update-alert" style="display: inline-block;">
						<h4><?php _e('There is a new version of Import XML feed - Premium Add-On.','import-xml-feed'); ?></h4>
						<p><?php echo $notice_text; ?></p>
					</div>
					<!--  .xml-cookie-alert -->
				<?php      
			endif;
		endif;
	}
}
$moove_importer_content_provider = new Moove_Importer_Content();
