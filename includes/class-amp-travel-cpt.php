<?php
/**
 * Class for Travel Custom Post Type.
 *
 * @package WPAMPTheme
 */

/**
 * Class AMP_Travel_CPT
 *
 * @package WPAMPTheme
 */
class AMP_Travel_CPT {

	/**
	 * The post type single slug.
	 *
	 * @var string
	 */
	const POST_TYPE_SLUG_SINGLE = 'adventure';

	/**
	 * The post type plural slug.
	 *
	 * @var string
	 */
	const POST_TYPE_SLUG_PLURAL = 'adventures';

	/**
	 * AMP_Travel_CTP constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'setup' ) );
		add_action( 'add_meta_boxes_adventure', array( $this, 'add_adventure_meta_boxes' ) );
		add_action( 'save_post_adventure', array( $this, 'save_adventure_post' ) );
	}

	/**
	 * Setup the Custom post type support.
	 */
	public function setup() {
		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/**
		 * Filter the image sizes to allow manipulating and adding sizes to be registered.
		 *
		 * @param array $sizes The array of sizes to be registered.
		 */
		$image_sizes = apply_filters( 'amp_travel_image_sizes', array(
			'1600x900',
			'1400x787',
			'1200x675',
			'1040x585',
			'768x432',
			'727x409',
			'600x338',
			'500x281',
			'375x211',
			'335x188',
			'320x180',
			'280x158',
			'240x135',
			'160x90',
			'122x67',
		) );

		// Custom image sizes.
		foreach ( $image_sizes as $size ) {
			$dimensions = explode( 'x', $size );
			add_image_size( 'travel-' . $size, $dimensions[0], $dimensions[1], true );
		}

		// Register the post type.
		$this->register_post_type();
	}

	/**
	 * Register 'adventure' post type.
	 */
	private function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Adventures', 'Post type general name', 'travel' ),
			'singular_name'         => _x( 'Adventure', 'Post type singular name', 'travel' ),
			'menu_name'             => _x( 'Adventures', 'Admin Menu text', 'travel' ),
			'name_admin_bar'        => _x( 'Adventure', 'Add New on Toolbar', 'travel' ),
			'add_new'               => __( 'Add New', 'travel' ),
			'add_new_item'          => __( 'Add New Adventure', 'travel' ),
			'new_item'              => __( 'New Adventure', 'travel' ),
			'edit_item'             => __( 'Edit Adventure', 'travel' ),
			'view_item'             => __( 'View Adventure', 'travel' ),
			'all_items'             => __( 'All Adventures', 'travel' ),
			'search_items'          => __( 'Search Adventures', 'travel' ),
			'parent_item_colon'     => __( 'Parent Adventures:', 'travel' ),
			'not_found'             => __( 'No adventures found.', 'travel' ),
			'not_found_in_trash'    => __( 'No adventures found in Trash.', 'travel' ),
			'featured_image'        => _x( 'Adventure Cover Image', 'Overrides the “Featured Image” phrase for this post type.', 'travel' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type.', 'travel' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type.', 'travel' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type.', 'travel' ),
			'archives'              => _x( 'Adventure archives', 'The post type archive label used in nav menus. Default “Post Archives”.', 'travel' ),
			'insert_into_item'      => _x( 'Insert into adventure', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post).', 'travel' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this adventure', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post).', 'travel' ),
			'filter_items_list'     => _x( 'Filter adventures list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”.', 'travel' ),
			'items_list_navigation' => _x( 'Adventures list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”.', 'travel' ),
			'items_list'            => _x( 'Adventures list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”.', 'travel' ),
		);

		$args = array(
			'labels'                => $labels,
			'description'           => __( 'Adventure Custom Post Type for travel theme.', 'travel' ),
			'public'                => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'show_in_menu'          => true,
			'show_in_admin_bar'     => true,
			'menu_position'         => 20,
			'menu_icon'             => 'dashicons-location-alt',
			'capability_type'       => 'post',
			'hierarchical'          => false,
			'supports'              => array(
				'title',
				'editor',
				'thumbnail',
			),
			'has_archive'           => true,
			'rewrite'               => array(
				'slug' => self::POST_TYPE_SLUG_SINGLE,
			),
			'query_var'             => true,
			'can_export'            => true,
			'show_in_rest'          => true,
			'rest_base'             => self::POST_TYPE_SLUG_PLURAL,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);

		register_post_type( self::POST_TYPE_SLUG_SINGLE, $args );
	}

	/**
	 * Adds meta boxes for adventure post type.
	 */
	public function add_adventure_meta_boxes() {
		add_meta_box( 'amp_travel_adventure_meta', __( 'Adventure details' ), array( $this, 'adventure_meta_box_html' ), 'adventure', 'side' );
	}

	/**
	 * Displays meta boxes in admin.
	 */
	public function adventure_meta_box_html() {
		global $post;
		$adventure_custom = get_post_custom( $post->ID );
		$start_date       = isset( $adventure_custom['amp_travel_start_date'][0] ) ? $adventure_custom['amp_travel_start_date'][0] : '';
		$end_date         = isset( $adventure_custom['amp_travel_end_date'][0] ) ? $adventure_custom['amp_travel_end_date'][0] : '';
		?>
		<div>
			<label for='amp_travel_start_date'><?php esc_attr_e( 'Start date', 'travel' ); ?></label><input placeholder='yyyy-mm-dd' pattern='[0-9]{4}-[0-9]{2}-[0-9]{2}' type='date' id='amp_travel_start_date' name='amp_travel_start_date' value='<?php echo $start_date; ?>'>
		</div>
		<div>
			<label for='amp_travel_end_date'><?php esc_attr_e( 'Ending date', 'travel' ); ?></label><input placeholder='yyyy-mm-dd' pattern='[0-9]{4}-[0-9]{2}-[0-9]{2}' type='date' id='amp_travel_end_date' name='amp_travel_end_date' value='<?php echo $end_date; ?>'>
		</div>
		<?php wp_nonce_field( basename( __FILE__ ), 'amp_travel_adventure_nonce' ); ?>
		<p class='description'><?php esc_attr_e( 'Leave empty if the adventure is ongoing', 'travel' ); ?></p>
		<?php
	}

	/**
	 * Saves the custom meta.
	 */
	public function save_adventure_post() {
		if ( ! empty( $_POST ) ) {
			global $post;

			if ( ! wp_verify_nonce( $_POST['amp_travel_adventure_nonce'], basename( __FILE__ ) ) ) {
				return;
			}

			if ( isset( $_POST['amp_travel_start_date'] ) ) {
				update_post_meta( $post->ID, 'amp_travel_start_date', esc_attr( $_POST['amp_travel_start_date'] ) );
			}

			if ( isset( $_POST['amp_travel_end_date'] ) ) {
				update_post_meta( $post->ID, 'amp_travel_end_date', esc_attr( $_POST['amp_travel_end_date'] ) );
			}
		}
	}
}
