<h2><?php _e( 'Licence Manager', 'import-xml-feed' ); ?></h2>
<hr />
<?php 
	$xml_default_content 	= new Moove_Importer_Content();
	$option_key         	= $xml_default_content->moove_xml_get_key_name();
	$xml_key       				= function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
?>
<form action="<?php echo admin_url( '/options-general.php?page=moove-importer&tab=licence' ) ?>" method="post" id="moove_xml_license_settings" data-key="<?php echo $xml_key && isset( $xml_key['key'] ) && isset( $xml_key['activation'] ) ? $xml_key['key'] : ''; ?>">
	<table class="form-table">
		<tbody>
			<tr>
				<td colspan="2" class="xml_license_log_alert" style="padding: 0;">
					<?php
						$is_valid_license = false;
						if ( isset( $_POST['moove_xml_license_key'] ) && isset( $_POST['xml_activate_license'] ) ) :
							$license_key  = sanitize_text_field( $_POST['moove_xml_license_key'] );
							if ( $license_key ) :
								$license_manager    = new Moove_XML_License_Manager();
								$is_valid_license   = $license_manager->get_premium_add_on( $license_key, 'activate' );

								if ( $is_valid_license && isset( $is_valid_license['valid'] ) && $is_valid_license['valid'] === true ) : 
									if ( function_exists( 'update_site_option' ) ) :
										update_site_option( 
											$option_key, 
											array( 
												'key'         => $is_valid_license['key'],
												'activation'  => $is_valid_license['data']['today']
											)
										);
									else :
										update_option( 
											$option_key, 
											array( 
												'key'         => $is_valid_license['key'],
												'activation'  => $is_valid_license['data']['today']
											)
										);
									endif;
									// VALID
									$xml_key       = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
									$messages       = isset( $is_valid_license['message'] ) && is_array( $is_valid_license['message'] ) ? implode( '<br>', $is_valid_license['message'] ) : '';
									do_action( 'xml_get_alertbox', 'success', $is_valid_license, $license_key ); 
								else :
									// INVALID
									do_action( 'xml_get_alertbox', 'error', $is_valid_license, $license_key );
								endif;
							endif;
						elseif ( isset( $_POST['moove_xml_license_key'] ) && isset( $_POST['xml_deactivate_license'] ) ) :
							$license_key  = sanitize_text_field( $_POST['moove_xml_license_key'] );
							if ( $license_key ) :
								$license_manager    = new Moove_XML_License_Manager();
								$is_valid_license   = $license_manager->premium_deactivate( $license_key );
								if ( function_exists( 'update_site_option' ) ) :
									update_site_option( 
										$option_key, 
										array(
											'key'           => $license_key,
											'deactivation'  => strtotime('now')
										)
									);
								else :
									update_option( 
										$option_key, 
										array(
											'key'           => $license_key,
											'deactivation'=> strtotime('now')
										)
									);
								endif;
								$xml_key       = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );

								if ( $is_valid_license && isset( $is_valid_license['valid'] ) && $is_valid_license['valid'] === true ) :
									// VALID
									do_action( 'xml_get_alertbox', 'success', $is_valid_license, $license_key ); 
								else :
									// INVALID
									do_action( 'xml_get_alertbox', 'error', $is_valid_license, $license_key );
								endif;
							endif;
						elseif ( $xml_key && isset( $xml_key['key'] ) && isset( $xml_key['activation'] ) ) :
							$license_manager    = new Moove_XML_License_Manager();
							$is_valid_license   = $license_manager->get_premium_add_on( $xml_key['key'], 'check' );
							$xml_key           = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
							if ( $is_valid_license && isset( $is_valid_license['valid'] ) && $is_valid_license['valid'] === true ) :
								// VALID
								do_action( 'xml_get_alertbox', 'success', $is_valid_license, $xml_key ); 
							else :
								// INVALID
								do_action( 'xml_get_alertbox', 'error', $is_valid_license, $xml_key );
							endif;
						endif;  
					?>
				</td>
			</tr>
			<?php do_action( 'xml_licence_input_field', $is_valid_license, $xml_key ); ?>

		</tbody>
	</table>
	
	<br />
	<?php do_action( 'xml_licence_action_button', $is_valid_license, $xml_key ); ?>
	<br />
	<?php do_action('xml_cc_general_buttons_settings'); ?>
</form>
<div class="xml-admin-popup xml-admin-popup-deactivate" style="display: none;">
	<span class="xml-popup-overlay"></span>
	<div class="xml-popup-content">
		<div class="xml-popup-content-header">
			<a href="#" class="xml-popup-close"><span class="dashicons dashicons-no-alt"></span></a>
		</div>
		<!--  .xml-popup-content-header -->
		<div class="xml-popup-content-content">
			<h4><strong>Please confirm that you would like to de-activate this licence. </strong></h4><p><strong>This action will remove all of the premium features from your website.</strong></p>
			<button class="button button-primary button-deactivate-confirm">
				<?php _e('Deactivate Licence','import-xml-feed'); ?>
			</button>
		</div>
		<!--  .xml-popup-content-content -->    
	</div>
	<!--  .xml-popup-content -->
</div>
<!--  .xml-admin-popup -->
