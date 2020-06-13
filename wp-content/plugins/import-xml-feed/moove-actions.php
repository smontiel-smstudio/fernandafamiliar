<?php
/**
 * Moove_Importer_Actions File Doc Comment
 *
 * @category  Moove_Importer_Actions
 * @package   moove-feed-importer
 * @author    Gaspar Nemes
 */

/**
 * Moove_Importer_Actions Class Doc Comment
 *
 * @category Class
 * @package  Moove_Importer_Actions
 * @author   Gaspar Nemes
 */
class Moove_Importer_Actions {
	/**
	 * Global cariable used in localization
	 *
	 * @var array
	 */
	var $importer_loc_data;
	/**
	 * Construct
	 */
	function __construct() {
		$this->moove_register_scripts();
		$this->moove_register_ajax_actions();

		add_action( 'xml_licence_action_button', array( 'Moove_Importer_Content', 'xml_licence_action_button' ), 10, 2 );
		add_action( 'xml_premium_update_alert', array( 'Moove_Importer_Content', 'xml_premium_update_alert' ) );
    add_action( 'xml_get_alertbox', array( 'Moove_Importer_Content', 'xml_get_alertbox' ), 10, 3 );
    add_action( 'xml_licence_input_field', array( 'Moove_Importer_Content', 'xml_licence_input_field' ), 10, 2 );



		$xml_default_content 	= new Moove_Importer_Content();
		$option_key           = $xml_default_content->moove_xml_get_key_name();
		$xml_key             	= function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
		
		if ( $xml_key && ! isset( $xml_key['deactivation'] ) ) :
			do_action( 'xml_plugin_loaded' );
		endif;
	}
	/**
	 * Register Front-end / Back-end scripts
	 *
	 * @return void
	 */
	function moove_register_scripts() {
		if ( is_admin() ) :
			add_action( 'admin_enqueue_scripts', array( &$this, 'moove_importer_admin_scripts' ) );
		endif;
	}

	/**
	 * Registe BACK-END Javascripts and Styles
	 *
	 * @return void
	 */
	public function moove_importer_admin_scripts() {
		wp_enqueue_script( 'moove_importer_backend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/js/moove_importer_backend.js', array( 'jquery' ), strtotime('now'), true );
		wp_enqueue_style( 'moove_importer_backend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/css/moove_importer_backend.css', '', MOOVE_XML_VERSION );
	}
	/**
	 * AJAX action used by importer plugin
	 *
	 * @return void
	 */
	public function moove_register_ajax_actions() {
		add_action( 'wp_ajax_moove_read_xml', array( &$this, 'moove_read_xml' ) );

		add_action( 'wp_ajax_moove_create_post', array( &$this, 'moove_create_post' ) );
	}
	/**
	 * Read XML function
	 *
	 * @return void
	 */
	public function moove_read_xml() {

		$args = array(
			'data' 		=> esc_sql( wp_unslash( $_POST['data'] ) ),
			'xmlaction'	=> sanitize_text_field( wp_unslash( $_POST['xmlaction'] ) ),
			'type'		=> sanitize_text_field( wp_unslash( $_POST['type'] ) ),
			'node'		=> sanitize_text_field( wp_unslash( $_POST['node'] ) ),
		);
		$move_importer = new Moove_Importer_Controller;
		$read_xml = $move_importer->moove_read_xml( $args );
		echo $read_xml;
		die();
	}
	/**
	 * Create post function
	 *
	 * @return void
	 */
	public function moove_create_post() {
		$args = array(
			'key'			=> sanitize_text_field( esc_sql( $_POST['key'] ) ),
			'value'			=> wp_unslash( $_POST['value'] ),
			'form_data'		=> esc_sql( wp_unslash( $_POST['form_data'] ) ),
		);
		$move_create_post = new Moove_Importer_Controller;
		$create_post = $move_create_post->moove_create_post( $args );
		echo $create_post;
		die();
	}
}
$moove_importer_actions_provider = new Moove_Importer_Actions();

