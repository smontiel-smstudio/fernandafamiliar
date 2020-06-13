<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://themeisle.com/plugins/feedzy-rss-feed/
 * @since      1.0.0
 *
 * @package    feedzy-rss-feeds
 * @subpackage feedzy-rss-feeds/includes/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    feedzy-rss-feeds
 * @subpackage feedzy-rss-feeds/includes/admin
 * @author     Bogdan Preda <bogdan.preda@themeisle.com>
 */

/**
 * Class Feedzy_Rss_Feeds_Import
 */
class Feedzy_Rss_Feeds_Import {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The settings for Feedzy PRO services.
	 *
	 * @since   1.3.2
	 * @access  public
	 * @var     array $settings The settings for Feedzy PRO.
	 */
	private $settings;

	/**
	 * The settings for Feedzy free.
	 *
	 * @access  public
	 * @var     array $settings The settings for Feedzy free.
	 */
	private $free_settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since       1.0.0
	 * @access      public
	 *
	 * @param       string $plugin_name The name of this plugin.
	 * @param       string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->settings = get_option( 'feedzy-rss-feeds-settings', array() );
		$this->free_settings = get_option( 'feedzy-settings', array() );
	}

	/**
	 * Adds the class to the div that shows the upsell.
	 *
	 * @since       ?
	 * @access      public
	 */
	public function upsell_class( $class ) {
		if ( ! feedzy_is_pro() ) {
			$class = 'only-pro';
		}
		return $class;
	}

	/**
	 * Adds the content to the div that shows the upsell.
	 *
	 * @since       ?
	 * @access      public
	 */
	public function upsell_content( $content ) {
		if ( ! feedzy_is_pro() ) {
			$content = '
			<div>
				<div class="only-pro-content">
					<div class="only-pro-container">
						<div class="only-pro-inner">
							<p>' . __( 'This feature is only enabled in the Pro version! To learn more about the benefits of Pro and how you can upgrade', 'feedzy-rss-feeds' ) . '
							<a target="_blank" href="' . FEEDZY_UPSELL_LINK . '" title="' . __( 'Buy Now', 'feedzy-rss-feeds' ) . '">' . __( 'Click here!', 'feedzy-rss-feeds' ) . '</a>
							</p>
						</div>
					</div>
				</div>
			</div>';
		}
		return $content;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since       1.0.0
	 * @access      public
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, FEEDZY_ABSURL . 'css/feedzy-rss-feed-import.css', array(), $this->version, 'all' );
		if ( get_current_screen()->post_type === 'feedzy_imports' ) {
			wp_enqueue_style( $this->plugin_name . '_chosen', FEEDZY_ABSURL . 'includes/views/css/chosen.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '_metabox_edit', FEEDZY_ABSURL . 'includes/views/css/import-metabox-edit.css', array(), $this->version, 'all' );
			wp_enqueue_script( $this->plugin_name . '_chosen_scipt', FEEDZY_ABSURL . 'includes/views/js/chosen.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script(
				$this->plugin_name . '_metabox_edit_script',
				FEEDZY_ABSURL . 'includes/views/js/import-metabox-edit.js',
				array(
					'jquery',
					$this->plugin_name . '_chosen_scipt',
				),
				$this->version,
				true
			);
			wp_localize_script(
				$this->plugin_name . '_metabox_edit_script',
				'feedzy',
				array(
					'ajax' => array(
						'security'  => wp_create_nonce( FEEDZY_NAME ),
					),
				)
			);
		}
	}

	/**
	 * Add attributes to $itemArray.
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param   array  $itemArray The item attributes array.
	 * @param   object $item The feed item.
	 * @param   array  $sc The shorcode attributes array.
	 * @param   int    $index The item number.
	 *
	 * @return mixed
	 */
	public function add_data_to_item( $itemArray, $item, $sc = null, $index = null ) {
		$itemArray['item_categories'] = $this->retrieve_categories( null, $item );

		// If set to true, SimplePie will return a unique MD5 hash for the item.
		// If set to false, it will check <guid>, <link>, and <title> before defaulting to the hash.
		$itemArray['item_id']   = $item->get_id( false );

		$itemArray['item']      = $item;
		return $itemArray;
	}

	/**
	 * Retrieve the categories.
	 *
	 * @since   ?
	 * @access  public
	 *
	 * @param   string $dumb The initial categories (only a placeholder argument for the filter).
	 * @param   object $item The feed item.
	 *
	 * @return string
	 */
	public function retrieve_categories( $dumb, $item ) {
		$cats       = array();
		$categories = $item->get_categories();
		if ( $categories ) {
			foreach ( $categories as $category ) {
				$cats[] = $category->get_label();
			}
		}
		return apply_filters( 'feedzy_categories', implode( ', ', $cats ), $cats, $item );
	}

	/**
	 * Register a new post type for feed imports.
	 *
	 * @since   1.2.0
	 * @access  public
	 */
	public function register_import_post_type() {
		$labels   = array(
			'name'               => __( 'Import Posts', 'feedzy-rss-feeds' ),
			'singular_name'      => __( 'Import Post', 'feedzy-rss-feeds' ),
			'add_new'            => __( 'New Import', 'feedzy-rss-feeds' ),
			'add_new_item'       => __( 'New Import', 'feedzy-rss-feeds' ),
			'edit_item'          => __( 'Edit Import', 'feedzy-rss-feeds' ),
			'new_item'           => __( 'New Import Post', 'feedzy-rss-feeds' ),
			'view_item'          => __( 'View Import', 'feedzy-rss-feeds' ),
			'search_items'       => __( 'Search Imports', 'feedzy-rss-feeds' ),
			'not_found'          => __( 'No imports found', 'feedzy-rss-feeds' ),
			'not_found_in_trash' => __( 'No imports in the trash', 'feedzy-rss-feeds' ),
		);
		$supports = array(
			'title',
		);
		$args     = array(
			'labels'               => $labels,
			'supports'             => $supports,
			'public'               => true,
			'exclude_from_search'  => true,
			'publicly_queryable'   => false,
			'capability_type'      => 'post',
			'show_in_nav_menus'    => false,
			'rewrite'              => array(
				'slug' => 'feedzy-import',
			),
			'show_in_menu'         => 'feedzy-admin-menu',
			'register_meta_box_cb' => array( $this, 'add_feedzy_import_metaboxes' ),
		);
		$args     = apply_filters( 'feedzy_imports_args', $args );
		register_post_type( 'feedzy_imports', $args );
	}

	/**
	 * Method to add a meta box to `feedzy_imports`
	 * custom post type.
	 *
	 * @since   1.2.0
	 * @access  public
	 */
	public function add_feedzy_import_metaboxes() {
		add_meta_box(
			'feedzy_import_feeds',
			__( 'Feed Import Options', 'feedzy-rss-feeds' ),
			array(
				$this,
				'feedzy_import_feed_options',
			),
			'feedzy_imports',
			'normal',
			'high'
		);
	}

	/**
	 * Method to display metabox for import post type.
	 *
	 * @since   1.2.0
	 * @access  public
	 * @return mixed
	 */
	public function feedzy_import_feed_options() {
		global $post;
		$args                 = array(
			'post_type'      => 'feedzy_categories',
			'posts_per_page' => 100,
		);
		$feed_categories      = get_posts( $args );
		$post_types           = get_post_types( '', 'names' );
		$post_types           = array_diff( $post_types, array( 'feedzy_imports', 'feedzy_categories' ) );
		$published_status     = array( 'publish', 'draft' );
		$import_post_type     = get_post_meta( $post->ID, 'import_post_type', true );
		$import_post_term     = get_post_meta( $post->ID, 'import_post_term', true );
		if ( metadata_exists( $import_post_type, $post->ID, 'import_post_status' ) ) {
			$import_post_status  = get_post_meta( $post->ID, 'import_post_status', true );
		} else {
			add_post_meta( $post->ID, 'import_post_status', 'publish' );
			$import_post_status  = get_post_meta( $post->ID, 'import_post_status', true );
		}
		$source               = get_post_meta( $post->ID, 'source', true );
		$inc_key              = get_post_meta( $post->ID, 'inc_key', true );
		$exc_key              = get_post_meta( $post->ID, 'exc_key', true );
		$import_title         = get_post_meta( $post->ID, 'import_post_title', true );
		$import_date          = get_post_meta( $post->ID, 'import_post_date', true );
		$import_content       = get_post_meta( $post->ID, 'import_post_content', true );
		$import_featured_img  = get_post_meta( $post->ID, 'import_post_featured_img', true );

		$import_link_author_admin         = get_post_meta( $post->ID, 'import_link_author_admin', true );
		$import_link_author_public        = get_post_meta( $post->ID, 'import_link_author_public', true );

		// admin, public
		$import_link_author = array( '', '' );
		if ( $import_link_author_admin === 'yes' ) {
			$import_link_author[0] = 'checked';
		}
		if ( $import_link_author_public === 'yes' ) {
			$import_link_author[1] = 'checked';
		}

		$import_custom_fields = get_post_meta( $post->ID, 'imports_custom_fields', true );
		$import_feed_limit    = get_post_meta( $post->ID, 'import_feed_limit', true );
		$import_feed_delete_days    = intval( get_post_meta( $post->ID, 'import_feed_delete_days', true ) );
		$post_status          = $post->post_status;
		$nonce                = wp_create_nonce( FEEDZY_BASEFILE );
		$output               = '
            <input type="hidden" name="feedzy_import_noncename" id="feedzy_category_meta_noncename" value="' . $nonce . '" />
        ';
		include FEEDZY_ABSPATH . '/includes/views/import-metabox-edit.php';
		echo $output;
	}

	/**
	 * Change number of posts imported.
	 */
	public function items_limit( $range, $post ) {
		if ( ! feedzy_is_pro() ) {
			$range = range( 10, 10, 10 );
		}
		return $range;
	}

	/**
	 * Save method for custom post type
	 * import feeds.
	 *
	 * @since   1.2.0
	 * @access  public
	 *
	 * @param   integer $post_id The post ID.
	 * @param   object  $post The post object.
	 *
	 * @return bool
	 */
	public function save_feedzy_import_feed_meta( $post_id, $post ) {
		if (
			empty( $_POST ) ||
			get_post_type( $post_id ) !== 'feedzy_imports' ||
			( isset( $_POST['feedzy_import_noncename'] ) && ! wp_verify_nonce( $_POST['feedzy_import_noncename'], FEEDZY_BASEFILE ) ) ||
			! current_user_can( 'edit_post', $post_id )
		) {
			return $post_id;
		}
		$data_meta            = isset( $_POST['feedzy_meta_data'] ) ? ( is_array( $_POST['feedzy_meta_data'] ) ? $_POST['feedzy_meta_data'] : array() ) : array();
		$custom_fields_keys   = isset( $_POST['custom_vars_key'] ) ? ( is_array( $_POST['custom_vars_key'] ) ? $_POST['custom_vars_key'] : array() ) : array();
		$custom_fields_values = isset( $_POST['custom_vars_value'] ) ? ( is_array( $_POST['custom_vars_value'] ) ? $_POST['custom_vars_value'] : array() ) : array();
		$custom_fields        = array();
		foreach ( $custom_fields_keys as $index => $key_value ) {
			$value = '';
			if ( isset( $custom_fields_values[ $index ] ) ) {
				$value = implode( ',', (array) $custom_fields_values[ $index ] );
			}
			$custom_fields[ $key_value ] = $value;
		}
		if ( $post->post_type !== 'revision' ) {
			foreach ( $data_meta as $key => $value ) {
				$value = is_array( $value ) ? implode( ',', $value ) : implode( ',', (array) $value );
				if ( get_post_meta( $post_id, $key, false ) ) {
					update_post_meta( $post_id, $key, $value );
				} else {
					add_post_meta( $post_id, $key, $value );
				}
				if ( ! $value ) {
					delete_post_meta( $post_id, $key );
				}
			}
			// Added this to activate post if publish is clicked and sometimes it does not change status.
			if ( isset( $_POST['custom_post_status'] ) && $_POST['custom_post_status'] === 'Publish' ) {
				$activate = array(
					'ID'          => $post_id,
					'post_status' => 'publish',
				);
				remove_action( 'save_post_feedzy_imports', array( $this, 'save_feedzy_import_feed_meta' ), 1, 2 );
				wp_update_post( $activate );
				add_action( 'save_post_feedzy_imports', array( $this, 'save_feedzy_import_feed_meta' ), 1, 2 );
			}

			do_action( 'feedzy_save_fields', $post_id, $post );
		}
		return true;
	}

	/**
	 * Redirect save post to post listing.
	 *
	 * @access  public
	 *
	 * @param   string $location   The url to redirect to.
	 * @param   int    $post_id    The post ID.
	 *
	 * @return string
	 */
	public function redirect_post_location( $location, $post_id ) {
		$post = get_post( $post_id );
		if ( 'feedzy_imports' === $post->post_type ) {
			return admin_url( 'edit.php?post_type=feedzy_imports' );
		}
		return $location;
	}

	/**
	 * Method to add header columns to import feeds table.
	 *
	 * @since   1.2.0
	 * @access  public
	 *
	 * @param   array $columns The columns array.
	 *
	 * @return array|bool
	 */
	public function feedzy_import_columns( $columns ) {
		$columns['title'] = __( 'Import Job Title', 'feedzy-rss-feeds' );
		if ( $new_columns = $this->array_insert_before( 'date', $columns, 'status', __( 'Status', 'feedzy-rss-feeds' ) ) ) {
			$columns = $new_columns;
		} else {
			$columns['status'] = __( 'Status', 'feedzy-rss-feeds' );
		}

		if ( $new_columns = $this->array_insert_before( 'date', $columns, 'last_run', __( 'Last Run Status', 'feedzy-rss-feeds' ) ) ) {
			$columns = $new_columns;
		} else {
			$columns['last_run'] = __( 'Last Run Status', 'feedzy-rss-feeds' );
		}

		if ( $new_columns = $this->array_insert_before( 'date', $columns, 'next_run', __( 'Next Run', 'feedzy-rss-feeds' ) ) ) {
			$columns = $new_columns;
		} else {
			$columns['next_run'] = __( 'Next Run', 'feedzy-rss-feeds' );
		}

		return $columns;
	}

	/**
	 * Utility method to insert before specific key
	 * in an associative array.
	 *
	 * @since   1.2.0
	 * @access  public
	 *
	 * @param   string $key The key before to insert.
	 * @param   array  $array The array in which to insert the new key.
	 * @param   string $new_key The new key name.
	 * @param   mixed  $new_value The new key value.
	 *
	 * @return array|bool
	 */
	protected function array_insert_before( $key, &$array, $new_key, $new_value ) {
		if ( array_key_exists( $key, $array ) ) {
			$new = array();
			foreach ( $array as $k => $value ) {
				if ( $k === $key ) {
					$new[ $new_key ] = $new_value;
				}
				$new[ $k ] = $value;
			}

			return $new;
		}

		return false;
	}

	/**
	 * Method to add a toggle checkbox on the status column
	 * in the import feeds table.
	 *
	 * @since   1.2.0
	 * @access  public
	 *
	 * @param   string  $column The current column to check.
	 * @param   integer $post_id The post ID.
	 */
	public function manage_feedzy_import_columns( $column, $post_id ) {
		global $post;
		switch ( $column ) {
			case 'status':
				$status = $post->post_status;
				if ( empty( $status ) ) {
					echo __( 'Undefined', 'feedzy-rss-feeds' );
				} else {
					if ( $status === 'publish' ) {
						$checked = 'checked';
					} else {
						$checked = '';
					}
					echo '
                    <div class="switch">
                        <input id="feedzy-toggle_' . $post->ID . '" class="feedzy-toggle feedzy-toggle-round" type="checkbox" value="' . $post->ID . '" ' . $checked . '>
                        <label for="feedzy-toggle_' . $post->ID . '"></label>
                    </div>
                    ';
				}
				break;
			case 'last_run':
				$last   = get_post_meta( $post_id, 'last_run', true );
				$msg    = __( 'Never Run', 'feedzy-rss-feeds' );
				if ( $last ) {
					$items_count  = get_post_meta( $post_id, 'imported_items_count', true );
					$items      = get_post_meta( $post_id, 'imported_items_hash', true );
					if ( empty( $items ) ) {
						$items      = get_post_meta( $post_id, 'imported_items', true );
					}
					$count  = $items_count;
					if ( '' === $count && $items ) {
						// backward compatibility where imported_items_count post_meta has not been populated yet
						$count  = count( $items );
					}
					$now  = new DateTime();
					$then = new DateTime();
					$then = $then->setTimestamp( $last );
					$in   = $now->diff( $then );
					$msg  = sprintf( __( 'Imported %1$d item(s)<br>%2$d hours %3$d minutes ago', 'feedzy-rss-feeds' ), $count, $in->format( '%h' ), $in->format( '%i' ) );
					// show total imported only if imported_items_count exists
					if ( ctype_digit( $items_count ) ) {
						$msg    .= '<br>' . sprintf( __( 'Total items imported: %1$d', 'feedzy-rss-feeds' ), count( $items ) );
					}

					$import_errors = get_post_meta( $post_id, 'import_errors', true );
					if ( $import_errors ) {
						$msg .= '<div class="feedzy-error feedzy-api-error">';
						foreach ( $import_errors as $error ) {
							$msg    .= '<br>' . $error;
						}
						$msg .= '</div>';
					}

					$msg    = apply_filters( 'feedzy_import_column', $msg, 'last_run', $post_id );
				}
				echo $msg;
				break;
			case 'next_run':
				$next = wp_next_scheduled( 'feedzy_cron' );
				if ( $next ) {
					$now  = new DateTime();
					$then = new DateTime();
					$then = $then->setTimestamp( $next );
					$in   = $now->diff( $then );
					echo sprintf( __( 'In %1$d hours %2$d minutes', 'feedzy-rss-feeds' ), $in->format( '%h' ), $in->format( '%i' ) );
					if ( 'publish' === $post->post_status ) {
						 echo sprintf( '<br/><input type="button" class="button button-primary feedzy-run-now" data-id="%d" value="%s"><span class="feedzy-spinner spinner"></span>', $post_id, __( 'Run Now', 'feedzy-rss-feeds' ) );
					}
				}
				break;
			default:
				break;
		}
	}

	/**
	 * AJAX called method to update post status.
	 *
	 * @since   1.2.0
	 * @access  public
	 */
	public function import_status() {
		global $wpdb;
		$id      = $_POST['id'];
		$status  = $_POST['status'];
		$publish = 'draft';
		if ( $status === 'true' ) {
			$publish = 'publish';
		}
		$new_post_status = array(
			'ID'          => $id,
			'post_status' => $publish,
		);

		remove_action( 'save_post_feedzy_imports', array( $this, 'save_feedzy_import_feed_meta' ), 1, 2 );
		$post_id         = wp_update_post( $new_post_status );
		add_action( 'save_post_feedzy_imports', array( $this, 'save_feedzy_import_feed_meta' ), 1, 2 );

		if ( is_wp_error( $post_id ) ) {
			$errors = $post_id->get_error_messages();
			foreach ( $errors as $error ) {
				echo $error;
			}
		}
		wp_die(); // this is required to terminate immediately and return a proper response
	}

	/**
	 * AJAX method to get taxonomies for a given post_type.
	 *
	 * @since   1.2.0
	 * @access  public
	 */
	public function get_taxonomies() {
		$post_type  = $_POST['post_type'];
		$taxonomies = get_object_taxonomies(
			array(
				'post_type' => $post_type,
			)
		);
		$results    = array();
		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$terms                = get_terms(
					array(
						'taxonomy'   => $taxonomy,
						'hide_empty' => false,
					)
				);
				$results[ $taxonomy ] = $terms;
			}
		}
		echo json_encode( $results );
		wp_die();
	}

	/**
	 * Run a specific job.
	 *
	 * @since   1.6.1
	 * @access  public
	 */
	public function run_now() {
		check_ajax_referer( FEEDZY_NAME, 'security' );

		$job    = get_post( $_POST['id'] );
		$count  = $this->run_job( $job, 100 );

		$msg    = $count > 0 ? sprintf( __( 'Successfully run! %d items imported.', 'feedzy-rss-feeds' ), $count ) : __( 'Nothing imported!', 'feedzy-rss-feeds' );

		$msg    = apply_filters( 'feedzy_run_status_errors', $msg, $job->ID );

		$import_errors = get_post_meta( $job->ID, 'import_errors', true );
		if ( $import_errors ) {
			$msg .= '<div class="feedzy-error feedzy-api-error">';
			foreach ( $import_errors as $error ) {
				$msg    .= '<br>' . $error;
			}
			$msg .= '</div>';
		}

		wp_send_json_success( array( 'msg' => $msg ) );
	}

	/**
	 * The Cron Job.
	 *
	 * @since   1.2.0
	 * @access  public
	 */
	public function run_cron( $max = 100 ) {
		if ( empty( $max ) ) {
			$max = 10;
		}
		global $post;
		$args           = array(
			'post_type'   => 'feedzy_imports',
			'post_status' => 'publish',
			'numberposts' => 300,
		);
		$feedzy_imports = get_posts( $args );
		foreach ( $feedzy_imports as $job ) {
			$this->run_job( $job, $max );
			do_action( 'feedzy_run_cron_extra', $job );
		}
	}

	/**
	 * Runs a specific job.
	 *
	 * @since   1.6.1
	 * @access  private
	 * @return  int
	 */
	private function run_job( $job, $max ) {
		$source               = get_post_meta( $job->ID, 'source', true );
		$inc_key              = get_post_meta( $job->ID, 'inc_key', true );
		$exc_key              = get_post_meta( $job->ID, 'exc_key', true );
		$import_title         = get_post_meta( $job->ID, 'import_post_title', true );
		$import_date          = get_post_meta( $job->ID, 'import_post_date', true );
		$import_content       = get_post_meta( $job->ID, 'import_post_content', true );
		$import_featured_img  = get_post_meta( $job->ID, 'import_post_featured_img', true );
		$import_post_type     = get_post_meta( $job->ID, 'import_post_type', true );
		$import_post_term     = get_post_meta( $job->ID, 'import_post_term', true );
		$import_feed_limit    = get_post_meta( $job->ID, 'import_feed_limit', true );
		$max                  = $import_feed_limit;
		if ( metadata_exists( $import_post_type, $job->ID, 'import_post_status' ) ) {
			$import_post_status  = get_post_meta( $job->ID, 'import_post_status', true );
		} else {
			add_post_meta( $job->ID, 'import_post_status', 'publish' );
			$import_post_status  = get_post_meta( $job->ID, 'import_post_status', true );
		}

		// the array of imported items that uses the old scheme of custom hashing the url and date
		$imported_items = array();
		$imported_items_old       = get_post_meta( $job->ID, 'imported_items', true );
		if ( ! is_array( $imported_items_old ) ) {
			$imported_items_old = array();
		}

		// the array of imported items that uses the new scheme of SimplePie's hash/id
		$imported_items_new       = get_post_meta( $job->ID, 'imported_items_hash', true );
		if ( ! is_array( $imported_items_new ) ) {
			$imported_items_new = array();
		}

		// Note: this implementation will only work if only one of the fields is allowed to provide
		// the date, because if the title can have UTC date and content can have local date then it
		// all goes sideways.
		// also if the user provides multiple date types, local will win.
		$meta               = 'yes';
		if ( strpos( $import_title, '[#item_date_local]' ) !== false ) {
			$meta           = 'author, date, time, tz=local';
		} elseif ( strpos( $import_title, '[#item_date_feed]' ) !== false ) {
			$meta           = 'author, date, time, tz=no';
		}

		$options = apply_filters(
			'feedzy_shortcode_options', array(
				'feeds'          => $source,
				'max'            => $max,
				'feed_title'     => 'no',
				'target'         => '_blank',
				'title'          => '',
				'meta'           => $meta,
				'summary'        => 'yes',
				'summarylength'  => '',
				'thumb'          => 'auto',
				'default'        => '',
				'size'           => '250',
				'keywords_title' => $inc_key,
				'keywords_ban'   => $exc_key,
				'columns'        => 1,
				'offset'         => 0,
				'multiple_meta'  => 'no',
			), $job
		);
		$results = $this->get_job_feed( $options, $import_content, true );
		$result = $results['items'];
		update_post_meta( $job->ID, 'last_run', time() );

		delete_post_meta( $job->ID, 'import_errors' );

		// let's increase this time in case spinnerchief/wordai is being used.
		ini_set( 'max_execution_time', apply_filters( 'feedzy_max_execution_time', 500 ) );

		$count = $index = $import_image_errors = 0;
		$import_errors = array();

		do_action( 'feedzy_run_job_pre', $job, $result );

		// check if we should be using the old scheme of custom hashing the url and date
		// or the new scheme of depending on SimplePie's hash/id
		// basically if the old scheme hasn't be used before, use the new scheme
		// BUT if the old scheme has been used, continue with it.
		$use_new_hash = empty( $imported_items_old );
		$imported_items = $use_new_hash ? $imported_items_new : $imported_items_old;

		foreach ( $result as $item ) {
			$item_hash = $use_new_hash ? $item['item_id'] : hash( 'sha256', $item['item_url'] . '_' . $item['item_date'] );
			$is_duplicate = $use_new_hash ? in_array( $item_hash, $imported_items_new, true ) : in_array( $item_hash, $imported_items_old, true );
			if ( $is_duplicate ) {
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Ignoring %s as it is a duplicate (%s hash).', $item_hash, $use_new_hash ? 'new' : 'old' ), 'warn', __FILE__, __LINE__ );
				$index++;
				continue;
			}

			$import_image = strpos( $import_content, '[#item_image]' ) !== false || strpos( $import_featured_img, '[#item_image]' ) !== false;
			if ( $import_image && empty( $item['item_img_path'] ) ) {
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Unable to find an image for item title %s.', $item['item_title'] ), 'warn', __FILE__, __LINE__ );
				$import_image_errors++;
			}

			$author     = '';
			if ( $item['item_author'] ) {
				if ( is_string( $item['item_author'] ) ) {
					$author = $item['item_author'];
				} elseif ( is_object( $item['item_author'] ) ) {
					$author = $item['item_author']->get_name();
					if ( empty( $author ) ) {
						$author = $item['item_author']->get_email();
					}
				}
			} else {
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Author is empty for %s.', $item['item_title'] ), 'warn', __FILE__, __LINE__ );
			}

			// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			$item_date = date( get_option( 'date_format' ) . ' at ' . get_option( 'time_format' ), $item['item_date'] );
			$item_date = $item['item_date_formatted'];

			$post_title = str_replace(
				array(
					'[#item_title]',
					'[#item_author]',
					'[#item_date]',
					'[#item_date_local]',
					'[#item_date_feed]',
					'[#item_source]',
				),
				array(
					$item['item_title'],
					$author,
					$item_date,
					$item_date,
					$item_date,
					$item['item_source'],
				),
				$import_title
			);

			if ( $this->feedzy_is_business() ) {
				$post_title = apply_filters( 'feedzy_parse_custom_tags', $post_title, $results['feed'], $index );
			}

			$post_title = apply_filters( 'feedzy_invoke_services', $post_title, 'title', $item['item_title'], $job );

			$item_link = '<a href="' . $item['item_url'] . '" target="_blank">' . __( 'Read More', 'feedzy-rss-feeds' ) . '</a>';
			$image_html   = '<img src="' . $item['item_img_path'] . '" title="' . $item['item_title'] . '" />';
			$post_content = str_replace(
				array(
					'[#item_description]',
					'[#item_content]',
					'[#item_image]',
					'[#item_url]',
					'[#item_categories]',
					'[#item_source]',
				),
				array(
					$item['item_description'],
					! empty( $item['item_content'] ) ? $item['item_content'] : $item['item_description'],
					$image_html,
					$item_link,
					$item['item_categories'],
					$item['item_source'],
				),
				$import_content
			);

			if ( $this->feedzy_is_business() ) {
				$full_content = ! empty( $item['item_full_content'] ) ? $item['item_full_content'] : $item['item_content'];
				if ( false !== strpos( $post_content, '[#item_full_content]' ) ) {
					$post_content = str_replace(
						array(
							'[#item_full_content]',
						),
						array(
							$full_content,
						),
						$post_content
					);
				}
				$post_content = apply_filters( 'feedzy_invoke_services', $post_content, 'full_content', $full_content, $job );
			}

			if ( $this->feedzy_is_business() ) {
				$post_content = apply_filters( 'feedzy_parse_custom_tags', $post_content, $results['feed'], $index );
			}

			$post_content   = apply_filters( 'feedzy_invoke_services', $post_content, 'content', $item['item_description'], $job );

			// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			$item_date = date( 'Y-m-d H:i:s', $item['item_date'] );
			// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			$now       = date( 'Y-m-d H:i:s' );
			if ( trim( $import_date ) === '' ) {
				$post_date = $now;
			}
			$post_date   = str_replace( '[#item_date]', $item_date, $import_date );
			$post_date   = str_replace( '[#post_date]', $now, $post_date );

			$new_post    = apply_filters(
				'feedzy_insert_post_args', array(
					'post_type'    => $import_post_type,
					'post_title'   => $post_title,
					'post_content' => $post_content,
					'post_date'    => $post_date,
					'post_status'  => $import_post_status,
				),
				$item,
				$post_title,
				$post_content,
				$index,
				$job
			);

			// no point creating a post if either the title or the content is null.
			if ( is_null( $post_title ) || is_null( $post_content ) ) {
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'NOT creating a new post as title (%s) or content (%s) is null.', $post_title, $post_content ), 'info', __FILE__, __LINE__ );
				$index++;
				continue;
			}

			$new_post_id = wp_insert_post( $new_post );
			if ( $new_post_id === 0 ) {
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Unable to create a new post with params %s.', print_r( $new_post, true ) ), 'error', __FILE__, __LINE__ );
				$index++;
				continue;
			}
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'created new post with ID %d with post_content %s', $new_post_id, $post_content ), 'debug', __FILE__, __LINE__ );

			$imported_items[] = $item_hash;
			$count++;

			if ( $import_post_term !== 'none' && strpos( $import_post_term, '_' ) > 0 ) {
				// let's get the slug of the uncategorized category, even if it renamed.
				$uncategorized = get_category( 1 );
				$terms = explode( ',', $import_post_term );
				foreach ( $terms as $term ) {
					// this handles both x_2, where 2 is the term id and x is the taxonomy AND x_2_3_4 where 4 is the term id and the taxonomy name is "x 2 3 4".
					$array = explode( '_', $term );
					$term_id = array_pop( $array );
					$taxonomy = implode( '_', $array );
					wp_remove_object_terms( $new_post_id, $uncategorized->slug, 'category' );
					$result = wp_set_object_terms( $new_post_id, intval( $term_id ), $taxonomy, true );
					do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'After creating post in %s/%d, result = %s', $taxonomy, $term_id, print_r( $result, true ) ), 'debug', __FILE__, __LINE__ );
				}
			}

			do_action( 'feedzy_import_extra', $job, $results, $new_post_id, $index );

			$index++;

			if ( trim( $import_featured_img ) !== '' && ! empty( $item['item_img_path'] ) ) {
				$image_url = str_replace( '[#item_image]', $item['item_img_path'], $import_featured_img );
				if ( $image_url !== '' && isset( $item['item_img_path'] ) && $item['item_img_path'] !== '' ) {
					$this->generate_featured_image( $image_url, $new_post_id, $item['item_title'] );
				} else {
					$this->generate_featured_image( $import_featured_img, $new_post_id, $item['item_title'] );
				}
			}

			// indicate that this post was imported by feedzy.
			update_post_meta( $new_post_id, 'feedzy', 1 );
			update_post_meta( $new_post_id, 'feedzy_item_url', $item['item_url'] );
			update_post_meta( $new_post_id, 'feedzy_job', $job->ID );
			update_post_meta( $new_post_id, 'feedzy_item_author', $author );

			do_action( 'feedzy_after_post_import', $new_post_id, $item, $this->settings );
		}

		if ( $use_new_hash ) {
			update_post_meta( $job->ID, 'imported_items_hash', $imported_items );
		} else {
			update_post_meta( $job->ID, 'imported_items', $imported_items );
		}
		update_post_meta( $job->ID, 'imported_items_count', $count );

		if ( $import_image_errors > 0 ) {
			$import_errors[] = sprintf( __( 'Unable to find an image for %1$d out of %2$d items imported', 'feedzy-rss-feeds' ), $import_image_errors, $count );
		}
		update_post_meta( $job->ID, 'import_errors', $import_errors );

		return $count;
	}

	/**
	 * Method to return feed items to use on cron job.
	 *
	 * @since   1.2.0
	 * @access  public
	 *
	 * @param   array  $options The options for the job.
	 * @param   string $import_content The import content (along with the magic tags).
	 * @param   bool   $raw_feed_also Whether to return the raw SimplePie object as well.
	 *
	 * @return mixed
	 */
	public function get_job_feed( $options, $import_content = null, $raw_feed_also = false ) {
		$admin = Feedzy_Rss_Feeds::instance()->get_admin();
		if ( ! method_exists( $admin, 'normalize_urls' ) ) {
			return array();
		}
		$feedURL = $admin->normalize_urls( $options['feeds'] );

		$feedURL = apply_filters( 'feedzy_import_feed_url', $feedURL, $import_content, $options );

		$feed    = $admin->fetch_feed( $feedURL, '12_hours', $options );

		if ( is_string( $feed ) ) {
			return array();
		}
		$sizes      = array(
			'width'  => $options['size'],
			'height' => $options['size'],
		);
		$sizes      = apply_filters( 'feedzy_thumb_sizes', $sizes, $feedURL );
		$feed_items = apply_filters( 'feedzy_get_feed_array', array(), $options, $feed, $feedURL, $sizes );

		if ( $raw_feed_also ) {
			return array(
				'items' => $feed_items,
				'feed'  => $feed,
			);
		}
		return $feed_items;
	}

	/**
	 * Modifies the feed object before it is processed.
	 *
	 * @access  public
	 *
	 * @param   SimplePie $feed SimplePie object.
	 */
	public function feedzy_modify_feed_config( $feed ) {
		// @codingStandardsIgnoreStart
		// set_time_limit(0);
		// @codingStandardsIgnoreEnd
		$feed->set_timeout( 60 );
	}

	/**
	 * Downloads and sets a post featured image if possible.
	 *
	 * @since   1.2.0
	 * @access  private
	 *
	 * @param   string  $file The file URL.
	 * @param   integer $post_id The post ID.
	 * @param   string  $desc Description.
	 */
	private function generate_featured_image( $file, $post_id, $desc ) {
		do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Trying to generate featured image for %s and postID %d', $file, $post_id ), 'debug', __FILE__, __LINE__ );

		require_once( ABSPATH . 'wp-admin' . '/includes/image.php' );
		require_once( ABSPATH . 'wp-admin' . '/includes/file.php' );
		require_once( ABSPATH . 'wp-admin' . '/includes/media.php' );

		$file_array     = array();
		$local_file     = download_url( $file );
		if ( is_wp_error( $local_file ) ) {
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Unable to download file = %s and postID %d', print_r( $local_file, true ), $post_id ), 'error', __FILE__, __LINE__ );
			return;
		}

		$type           = mime_content_type( $local_file );
		// the file is downloaded with a .tmp extension
		// if the URL mentions the extension of the file, the upload succeeds
		// but if the URL is like https://source.unsplash.com/random, then the upload fails
		// so let's determine the file's mime type and then rename the .tmp file with that extension
		if ( in_array( $type, array_values( get_allowed_mime_types() ), true ) ) {
			$new_local_file = str_replace( '.tmp', str_replace( 'image/', '.', $type ), $local_file );
			$renamed        = rename( $local_file, $new_local_file );
			if ( $renamed ) {
				$local_file = $new_local_file;
			} else {
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Unable to rename file for postID %d', $post_id ), 'error', __FILE__, __LINE__ );
			}
		}

		$file_array['tmp_name'] = $local_file;
		$file_array['name']     = basename( $local_file );

		$id                 = media_handle_sideload( $file_array, $post_id, $desc );
		if ( is_wp_error( $id ) ) {
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Unable to attach file for postID %d = %s', $post_id, print_r( $id, true ) ), 'error', __FILE__, __LINE__ );
			unlink( $file_array['tmp_name'] );
			return;
		}

		$success = set_post_thumbnail( $post_id, $id );
		if ( false === $success ) {
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Unable to attach file for postID %d for no apparent reason', $post_id ), 'error', __FILE__, __LINE__ );
		} else {
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Attached file as featured image for postID %d', $post_id ), 'info', __FILE__, __LINE__ );
		}
	}

	/**
	 * Registers a cron schedule.
	 *
	 * @since   1.2.0
	 * @access  public
	 */
	public function add_cron() {
		if ( false === wp_next_scheduled( 'feedzy_cron' ) ) {
			wp_schedule_event( time(), 'hourly', 'feedzy_cron' );
		}
	}

	/**
	 * Checks if WP Cron is enabled and if not, shows a notice.
	 *
	 * @access  public
	 */
	public function admin_notices() {
		$screen = get_current_screen();
		$allowed    = array( 'edit-feedzy_categories', 'edit-feedzy_imports', 'feedzy-rss_page_feedzy-settings' );
		// only show in the feedzy screens.
		if ( ! in_array( $screen->id, $allowed, true ) ) {
			return;
		}

		if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
			echo '<div class="notice notice-error feedzy-error-critical is-dismissible"><p>' . __( 'WP Cron is disabled. Your feeds would not get updated. Please contact your hosting provider or system administrator', 'feedzy-rss-feeds' ) . '</p></div>';
		}

		if ( false === wp_next_scheduled( 'feedzy_cron' ) ) {
			echo '<div class="notice notice-error"><p>' . __( 'Unable to register cron job. Your feeds might not get updated', 'feedzy-rss-feeds' ) . '</p></div>';
		}

	}

	/**
	 * Method to return license status.
	 * Used to filter PRO version types.
	 *
	 * @since   1.2.0
	 * @access  public
	 * @return bool
	 */
	public function feedzy_is_business() {
		return $this->feedzy_is_license_of_type( false, 'business' );
	}

	/**
	 * Method to return if licence is agency.
	 *
	 * @since   1.3.2
	 * @access  public
	 * @return bool
	 */
	public function feedzy_is_agency() {
		return $this->feedzy_is_license_of_type( false, 'agency' );
	}

	/**
	 * Method to return the type of licence.
	 *
	 * @access  public
	 * @return bool
	 */
	public function feedzy_is_license_of_type( $default, $type ) {
		// proceed to check the plan only if the license is active.
		if ( ! ( defined( 'TI_UNIT_TESTING' ) || defined( 'TI_CYPRESS_TESTING' ) ) ) {
			$status = apply_filters( 'feedzy_rss_feeds_pro_license_status', false );
			if ( $status !== 'valid' ) {
				return $default;
			}
		}
		$plan = get_option( 'feedzy_rss_feeds_pro_license_plan', 1 );
		$plan = intval( $plan );

		switch ( $type ) {
			case 'agency':
				return ( $plan > 2 );
			case 'business':
				return ( $plan > 1 );
			case 'pro':
				return ( $plan > 0 );
		}
		return $default;
	}


	/**
	 * Method for updating settings page via AJAX.
	 *
	 * @since   1.3.2
	 * @access  public
	 */
	public function update_settings_page() {
		$post_data = $_POST['feedzy_settings'];
		$this->save_settings();
		wp_die();
	}

	/**
	 * Display settings fields for the tab.
	 *
	 * @since   1.3.2
	 * @access  public
	 */
	public function display_tab_settings( $fields, $tab ) {
		$this->free_settings = get_option( 'feedzy-settings', array() );

		$fields[] = array(
			'content'   => $this->render_view( $tab ),
			'ajax'      => false,
		);
		return $fields;
	}

	/**
	 * Method to save settings.
	 *
	 * @since   1.3.2
	 * @access  private
	 */
	private function save_settings() {
		update_option( 'feedzy-rss-feeds-settings', $this->settings );
	}

	/**
	 * Add settings tab.
	 *
	 * @since   1.3.2
	 * @access  public
	 */
	public function settings_tabs( $tabs ) {
		$tabs['misc'] = __( 'Miscellaneous', 'feedzy-rss-feeds' );
		return $tabs;
	}

	/**
	 * Save settings for the tab.
	 *
	 * @access  public
	 */
	public function save_tab_settings( $settings, $tab ) {
		if ( 'misc' === $tab ) {
			$settings['canonical'] = isset( $_POST['canonical'] ) ? $_POST['canonical'] : 0;
		}
		return $settings;
	}

	/**
	 * Render a view page.
	 *
	 * @since   1.3.2
	 * @access  public
	 *
	 * @param   string $name The name of the view.
	 *
	 * @return string
	 */
	private function render_view( $name ) {
		$file = null;
		switch ( $name ) {
			case 'misc':
				$file = FEEDZY_ABSPATH . '/includes/views/' . $name . '-view.php';
				break;
			default:
				$file = apply_filters( 'feedzy_render_view', $file, $name );
				break;
		}

		if ( ! $file ) {
			return;
		}

		ob_start();
		include $file;
		return ob_get_clean();
	}

	/**
	 * Renders the HTML for the tags.
	 *
	 * @since   1.4.2
	 * @access  public
	 */
	public function render_magic_tags( $default, $tags, $type ) {
		if ( $tags ) {
			$disabled = array();
			foreach ( $tags as $tag => $label ) {
				if ( strpos( $tag, ':disabled' ) !== false ) {
					$disabled[ str_replace( ':disabled', '', $tag ) ] = $label;
					continue;
				}
				$default    .= '<a class="dropdown-item" href="#" data-field-name="' . $type . '" data-field-tag="' . $tag . '">' . $label . ' -- <small>[#' . $tag . ']</small></a>';
			}

			if ( $disabled ) {
				foreach ( $disabled as $tag => $label ) {
					$default    .= '<span disabled title="' . __( 'Upgrade your license to use this tag', 'feedzy-rss-feeds' ) . '" class="dropdown-item">' . $label . ' -- <small>[#' . $tag . ']</small></span>';
				}
			}
		}
		return $default;
	}

	/**
	 * Renders the tags for the title.
	 *
	 * @since   1.4.2
	 * @access  public
	 *
	 * @param   array $default The default tags, empty.
	 */
	public function magic_tags_title( $default ) {
		$default['item_title']  = __( 'Item Title', 'feedzy-rss-feeds' );
		$default['item_author'] = __( 'Item Author', 'feedzy-rss-feeds' );
		$default['item_date']   = __( 'Item Date (UTC/GMT)', 'feedzy-rss-feeds' );
		$default['item_date_local']   = __( 'Item Date (local timezone)', 'feedzy-rss-feeds' );
		$default['item_date_feed']   = __( 'Item Date (feed timezone)', 'feedzy-rss-feeds' );
		$default['item_source']        = __( 'Item Source', 'feedzy-rss-feeds' );

		// disabled tags
		if ( ! feedzy_is_pro() ) {
			$default['title_spinnerchief:disabled']    = __( 'Title from SpinnerChief', 'feedzy-rss-feeds' );
			$default['title_wordai:disabled']    = __( 'Title from WordAI', 'feedzy-rss-feeds' );
		}
		return $default;
	}

	/**
	 * Renders the tags for the date.
	 *
	 * @since   1.4.2
	 * @access  public
	 *
	 * @param   array $default The default tags, empty.
	 */
	public function magic_tags_date( $default ) {
		$default['item_date']   = __( 'Item Date', 'feedzy-rss-feeds' );
		$default['post_date']   = __( 'Post Date', 'feedzy-rss-feeds' );
		return $default;
	}

	/**
	 * Renders the tags for the content.
	 *
	 * @since   1.4.2
	 * @access  public
	 *
	 * @param   array $default The default tags, empty.
	 */
	public function magic_tags_content( $default ) {
		$default['item_content']    = __( 'Item Content', 'feedzy-rss-feeds' );
		$default['item_description']    = __( 'Item Description', 'feedzy-rss-feeds' );
		$default['item_image']      = __( 'Item Image', 'feedzy-rss-feeds' );
		$default['item_url']        = __( 'Item URL', 'feedzy-rss-feeds' );
		$default['item_categories']        = __( 'Item Categories', 'feedzy-rss-feeds' );
		$default['item_source']        = __( 'Item Source', 'feedzy-rss-feeds' );

		// disabled tags
		if ( ! feedzy_is_pro() ) {
			$default['item_full_content:disabled']    = __( 'Item Full Content', 'feedzy-rss-feeds' );
			$default['content_spinnerchief:disabled']    = __( 'Content from SpinnerChief', 'feedzy-rss-feeds' );
			$default['full_content_spinnerchief:disabled']    = __( 'Full content from SpinnerChief', 'feedzy-rss-feeds' );
			$default['content_wordai:disabled']    = __( 'Content from WordAI', 'feedzy-rss-feeds' );
			$default['full_content_wordai:disabled']    = __( 'Full content from WordAI', 'feedzy-rss-feeds' );
		}
		return $default;
	}

	/**
	 * Renders the tags for the featured image.
	 *
	 * @since   1.4.2
	 * @access  public
	 *
	 * @param   array $default The default tags, empty.
	 */
	public function magic_tags_image( $default ) {
		$default['item_image']      = __( 'Item Image', 'feedzy-rss-feeds' );
		return $default;
	}

	/**
	 * Register the meta tags.
	 *
	 * @access      public
	 */
	public function wp() {
		global $wp_version;

		$free_settings = get_option( 'feedzy-settings', array() );
		if ( ! isset( $free_settings['canonical'] ) || 1 !== intval( $free_settings['canonical'] ) ) {
			return;
		}

		// Yoast.
		add_filter( 'wpseo_canonical', array( $this, 'get_canonical_url' ) );

		// All In One SEO.
		add_filter( 'aioseop_canonical_url', array( $this, 'get_canonical_url' ) );

		if ( version_compare( $wp_version, '4.6.0', '>=' ) ) {
			// Fallback if none of the above plugins is present.
			add_filter( 'get_canonical_url', array( $this, 'get_canonical_url' ) );
		}
	}

	/**
	 * Return the canonical URL.
	 *
	 * @access      public
	 */
	public function get_canonical_url( $canonical_url ) {
		if ( ! is_singular() ) {
			return $canonical_url;
		}

		global $post;
		if ( ! $post ) {
			return $canonical_url;
		}

		// let's check if the post has been imported by feedzy.
		if ( 1 === intval( get_post_meta( $post->ID, 'feedzy', true ) ) ) {
			$url    = get_post_meta( $post->ID, 'feedzy_item_url', true );
			if ( ! empty( $url ) ) {
				$canonical_url = $url;
			}
		}
		return $canonical_url;
	}

}
