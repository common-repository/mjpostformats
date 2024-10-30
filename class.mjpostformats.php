<?php
if ( !class_exists( 'MJPostFormats' ) ) {
	class MJPostFormats {

		/**
			*
			* @var array $_strings
			*/
			private $_strings;


		/**
			*
			* @var array $_post_types
			*/
			private $_post_types;


		/**
			* Global used to init class with singleton function
			*
			* @var null
			*/
			private static $_instance = null;


		/**
			* Constructor
			*
			*/
			public function __construct() {
				// Load up the localization file if we're using WordPress in a different language
	      load_plugin_textdomain( 'mjpostformats', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

				$this->_strings = array(
				  'standard' => _x( 'Standard', 'mjpostformats' ),
				  'aside'    => _x( 'Aside',    'mjpostformats' ),
				  'chat'     => _x( 'Chat',     'mjpostformats' ),
				  'gallery'  => _x( 'Gallery',  'mjpostformats' ),
				  'link'     => _x( 'Link',     'mjpostformats' ),
				  'image'    => _x( 'Image',    'mjpostformats' ),
				  'quote'    => _x( 'Quote',    'mjpostformats' ),
				  'status'   => _x( 'Status',   'mjpostformats' ),
				  'video'    => _x( 'Video',    'mjpostformats' ),
				  'audio'    => _x( 'Audio',    'mjpostformats' ),
				);

				$this->_post_types = array('post');

				add_action( 'init', array( &$this, 'disableDefaultPostformats' ) );

				add_action( 'add_meta_boxes', array( &$this, 'register_meta_boxes_post_formats' ) );

				add_action( 'save_post', array( &$this, 'save' ) );

				//ACF COMPATIBILITY
				add_filter( 'acf/location/rule_values/post_format', array( &$this, 'getStrings' ) );

				add_action( 'admin_enqueue_scripts', array( &$this, 'my_enqueue_script' ) );

	    }


		/**
			*
			* Singleton
			*
			* @return class instance
			*/
		  public static function get_instance() {
		  	if( self::$_instance === null ){
		  		self::$_instance = new MJCaroussel;
		  	}

		  	return self::$_instance;
		  }


		/**
			*
			* plugin_activation
			*
			* @return N/A
			*/
			public function plugin_activation() {
	      $textdomain = 'mjpostformats';

	      if ( version_compare( $GLOBALS['wp_version'], MJPOSTFORMATS_MINIMUM_WP_VERSION, '<' ) ) {
	        $message = '<strong>' . sprintf(esc_html__( 'MJPostFormats %s requires WordPress %s or higher.' , $textdomain), MJPOSTFORMATS_VERSION, MJPOSTFORMATS_MINIMUM_WP_VERSION ). '</strong>';
	  		}
			}


		/**
			*
			* plugin_desactivation
			*
			* @return N/A
			*/
			public function plugin_desactivation() {
			}


		/**
			*
			* Disable default post formats
			*
			* @return N/A
			*/
			public function disableDefaultPostformats() {
				remove_theme_support( 'post-formats' );
			}


		/**
			*
			* Getter $_strings
			*
			* @return N/A
			*/
			public function getStrings() {
				return $this->_strings;
			}


		/**
			*
			* Getter $_strings
			*
			* @return N/A
			*/
			public function getPostType() {
				return $this->_post_types;
			}


		/**
			*
			* Register meta box(es).
			*
			*/
			public function register_meta_boxes_post_formats() {
				$post_type = $this->_post_types;

        add_meta_box(
            'mj_post_format',
            __( 'Formats', 'mjpostformats' ),
            array( &$this, 'meta_boxes_post_formats_callback' ),
            $post_type,
            'side',
            'high'
        );
			}


		/**
			* Display script & style in BO
			*
			*/
			function my_enqueue_script(){
				wp_enqueue_style( 'mjpostformats', plugins_url( 'public/css/mjpostformats.min.css', __FILE__ ), array(), '', false );
				wp_enqueue_script('mjpostformats', plugins_url('public/js/mjpostformats.min.js', __FILE__), array(), '', true);
			}


		/**
			* Meta box display callback.
			*
			* @param WP_Post $post Current post object.
			*/
			public function meta_boxes_post_formats_callback( $post ) {
				wp_nonce_field( 'mjpostformats_inner_custom_box', 'mjpostformats_inner_custom_box_nonce' );

				$post_formats = $this->_strings;
				$value = get_post_meta( $post->ID, 'mj-post-format', true );
				//var_dump( $value );
				?>
				<div id="post-formats-select">
					<fieldset>
						<legend class="screen-reader-text"><?php echo _x( 'Post Formats', 'mjpostformats' ); ?></legend>
						<input type="radio" name="post_format" class="post-format" id="post-format-0" value="0" <?php checked( $value, '0' ); ?> /> <label for="post-format-0" class="post-format-icon post-format-standard"><?php echo get_post_format_string( 'standard' ); ?></label>
						<?php
						array_shift($post_formats);
						foreach ( $post_formats as $key => $format ) : ?>
						<br /><input type="radio" name="post_format" class="post-format" id="post-format-<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $value, $key ); ?> /> <label for="post-format-<?php echo esc_attr( $key ); ?>" class="post-format-icon post-format-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $this->get_post_format_string( $key ) ); ?></label>
						<?php endforeach; ?>
					</fieldset>
				</div>
				<?php
			}


	  /**
      * Save the meta when the post is saved.
      *
      * @param int $post_id The ID of the post being saved.
      */
	    public function save( $post_id ) {
	        // Check if our nonce is set.
	        if ( ! isset( $_POST['mjpostformats_inner_custom_box_nonce'] ) ) {
            return $post_id;
	        }

	        $nonce = $_POST['mjpostformats_inner_custom_box_nonce'];

	        // Verify that the nonce is valid.
	        if ( ! wp_verify_nonce( $nonce, 'mjpostformats_inner_custom_box' ) ) {
            return $post_id;
	        }

	        /*
	         * If this is an autosave, our form has not been submitted,
	         * so we don't want to do anything.
	         */
	        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
	        }

	        // Check the user's permissions.
	        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
              return $post_id;
            }
	        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
              return $post_id;
            }
	        }

	        // Sanitize the user input.
	        $mydata = sanitize_text_field( $_POST['post_format'] );

	        // Update the meta field.
	        update_post_meta( $post_id, 'mj-post-format', $mydata );
	    }


		/**
		  * Returns a pretty, translated version of a post format slug
		  *
		  *
		  * @param string $slug A post format slug.
		  * @return string The translated post format name.
		  */
			public function get_post_format_string( $slug ) {
				$strings = $this->_strings;
				if ( !$slug )
					return $strings['standard'];
				else
					return ( isset( $strings[$slug] ) ) ? $strings[$slug] : '';
			}


		/**
			*
			* Setter $_strings
			*
			*/
			public function setStrings( array $_strings ) {
				$this->_strings = $_strings;
			}


		/**
			*
			* Setter $_post_type
			*
			*/
			public function setPostType( array $_post_types ) {
				$this->_post_types = $_post_types;
			}
	}
}
