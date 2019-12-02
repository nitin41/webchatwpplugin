<?php
/**
 * Plugin Name: Botco Webchat Plugin
 * Description: Botco wordpress webchat deploy plugin
 * Version: 1.0.0
 * Author: Dimple Gangwani
 * License: GPL v2 or later
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
if ( ! class_exists( 'BotcoWebchat' ) ) {

	class BotcoWebchat {

		/**
		 * Start things up
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// We only need to register the admin panel on the back-end
			if ( is_admin() ) {
				add_action( 'admin_menu', array( 'BotcoWebchat', 'add_admin_menu' ) );
				add_action( 'admin_init', array( 'BotcoWebchat', 'register_settings' ) );
				if(is_admin() && $_GET["page"]==="webchat-settings"){
					add_action('admin_enqueue_scripts', array( 'BotcoWebchat', 'webchat_custom_scripts' ));
				}	
            }
			add_action('wp_footer', array( 'BotcoWebchat', 'webchat_frontend_scripts' ));

        }

        public static function webchat_custom_scripts() {
            /*
             * I recommend to add additional conditions just to not to load the scipts on each page
             * like:
             * if ( !in_array('post-new.php','post.php') ) return;
             */
			echo '<style>
				#wpcontent{
					background: #fff;
				} 
			</style>';

            if ( ! did_action( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
			}
			wp_enqueue_style( 'colorpickercss', plugin_dir_url( __FILE__ ) . 'assets/css/jquerysctipttop.css', array(), null, false);
			wp_enqueue_style( 'bootstrapcss', plugin_dir_url( __FILE__ ) . 'assets/css/bootstrap.min.css', array(), null, false);
			wp_enqueue_style( 'bcpmincss', plugin_dir_url( __FILE__ ) . 'assets/css/bcp.min.css', array(), null, false);
			
			wp_deregister_script('jquery');
			wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.4.1.min.js', array(), null, true);
            wp_enqueue_script( 'webchatscript', plugin_dir_url( __FILE__ ) . 'assets/js/webchat-js.js', array('jquery'), null, false );
			wp_enqueue_script( 'popperscript', plugin_dir_url( __FILE__ ) . 'assets/js/popper.min.js', array('jquery'), null, false );
			wp_enqueue_script( 'bootstrapscript', plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap.min.js', array('jquery'), null, false );
			wp_enqueue_script( 'bcpscript', plugin_dir_url( __FILE__ ) . 'assets/js/bcp.min.js', array('jquery'), null, false );
			wp_enqueue_script( 'bcpenscript', plugin_dir_url( __FILE__ ) . 'assets/js/bcp.en.min.js', array('jquery'), null, false );
		}
		
		public static function webchat_frontend_scripts(){
			$theme_options = get_option('theme_options');
			if(isset($theme_options['api_key']) && $theme_options['api_key']){
				?>
				<script src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/js'; ?>/widget.js"></script>
					<script>
						BotcoWebchat.mount({
							apiKey: '<?php echo $theme_options['api_key']; ?>',
							headerTitle: '<?php echo $theme_options['header_title']; ?>',
							headerBackgroundColor: '<?php echo $theme_options['header_background_color']; ?>',
							headerTextColor: '<?php echo $theme_options['header_text_color']; ?>',
							chatResponseColor: '<?php echo $theme_options['chat_response_color']; ?>',
							chatResponseTextColor: '<?php echo $theme_options['chat_response_text_color']; ?>',
							logoUrl: '<?php echo wp_get_attachment_image_src( $theme_options['logourl'], 'thumbnail', false)[0]; ?>',
							botUrl: '<?php echo wp_get_attachment_image_src( $theme_options['boturl'], 'thumbnail', false)[0]; ?>',
							welcomeMessage: '<?php echo $theme_options['welcome_message']; ?>',
							welcome_intent: '<?php echo $theme_options['welcome_intent']; ?>',
						});
					</script>
				<?php
			}
		}
		
		public static function webchat_image_uploader_field( $name, $value = '') {
            $image = ' button button-primary">Upload image';
            $image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
            $display = 'none'; // display state ot the "Remove image" button
         
            if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {
         
                // $image_attributes[0] - image URL
                // $image_attributes[1] - image width
                // $image_attributes[2] - image height
         
                $image = '"><img src="' . $image_attributes[0] . '" style="width:60px;border-radius:50px;display:block;" />';
                $display = 'inline-block';
         
            } 
         
            return '
            <div>
                <a href="#" class="webchat_upload_image_button' . $image . '</a>
                <input type="hidden" name="theme_options['.$name.']" id="' . $name . '" value="' . esc_attr( $value ) . '" required/>
                <a href="#" class="webchat_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
            </div>';
        }

        /**
		 * Returns all theme options
		 *
		 * @since 1.0.0
		 */
		public static function get_theme_options() {
			return get_option( 'theme_options' );
		}

		/**
		 * Returns single theme option
		 *
		 * @since 1.0.0
		 */
		public static function get_theme_option( $id ) {
			$options = self::get_theme_options();
			if ( isset( $options[$id] ) ) {
				return $options[$id];
			}
		}

		/**
		 * Add sub menu page
		 *
		 * @since 1.0.0
		 */
		public static function add_admin_menu() {
			add_menu_page(
				esc_html__( 'Webchat', 'text-domain' ),
				esc_html__( 'Webchat', 'text-domain' ),
				'manage_options',
				'webchat-settings',
				array( 'BotcoWebchat', 'create_admin_page' )
			);
		}

		/**
		 * Register a setting and its sanitization callback.
		 *
		 * We are only registering 1 setting so we can store all options in a single option as
		 * an array. You could, however, register a new setting for each option
		 *
		 * @since 1.0.0
		 */
		public static function register_settings() {
			register_setting( 'theme_options', 'theme_options', array( 'BotcoWebchat', 'sanitize' ) );
		}

		/**
		 * Sanitization callback
		 *
		 * @since 1.0.0
		 */
		public static function sanitize( $options ) {

			// If we have options lets sanitize them
			if ( $options ) {

				// Input api_key
				if ( ! empty( $options['api_key'] ) ) {
					$options['api_key'] = sanitize_text_field( $options['api_key'] );
				} else {
					unset( $options['api_key'] ); // Remove from options if empty
                }
                
                if ( ! empty( $options['header_title'] ) ) {
					$options['header_title'] = sanitize_text_field( $options['header_title'] );
				} else {
					unset( $options['header_title'] ); // Remove from options if empty
                }
                
                if ( ! empty( $options['header_background_color'] ) ) {
					$options['header_background_color'] = sanitize_text_field( $options['header_background_color'] );
				} else {
					unset( $options['header_background_color'] ); // Remove from options if empty
                }
                
                if ( ! empty( $options['header_text_color'] ) ) {
					$options['header_text_color'] = sanitize_text_field( $options['header_text_color'] );
				} else {
					unset( $options['header_text_color'] ); // Remove from options if empty
                }
                
                if ( ! empty( $options['chat_response_color'] ) ) {
					$options['chat_response_color'] = sanitize_text_field( $options['chat_response_color'] );
				} else {
					unset( $options['chat_response_color'] ); // Remove from options if empty
                }

                if ( ! empty( $options['chat_response_text_color'] ) ) {
					$options['chat_response_text_color'] = sanitize_text_field( $options['chat_response_text_color'] );
				} else {
					unset( $options['chat_response_text_color'] ); // Remove from options if empty
                }

                if ( ! empty( $options['welcome_message'] ) ) {
					$options['welcome_message'] = sanitize_text_field( $options['welcome_message'] );
				} else {
					unset( $options['welcome_message'] ); // Remove from options if empty
                }

                if ( ! empty( $options['welcome_intent'] ) ) {
					$options['welcome_intent'] = sanitize_text_field( $options['welcome_intent'] );
				} else {
					unset( $options['welcome_intent'] ); // Remove from options if empty
                }

                if ( ! empty( $options['logourl'] ) ) {
					$options['logourl'] = sanitize_text_field( $options['logourl'] );
				} else {
					unset( $options['logourl'] ); // Remove from options if empty
                }

                if ( ! empty( $options['boturl'] ) ) {
					$options['boturl'] = sanitize_text_field( $options['boturl'] );
				} else {
					unset( $options['boturl'] ); // Remove from options if empty
                }
			
			}

			// Return sanitized options
			return $options;

		}

		/**
		 * Settings page output
		 *
		 * @since 1.0.0
		 */
		public static function create_admin_page() { ?>

			<div class="wrap">

				<h1><?php esc_html_e( 'Webchat Settings', 'text-domain' ); ?></h1>

				<form method="post" action="options.php">

					<?php settings_fields( 'theme_options' ); ?>

					<table class="form-table wpex-custom-admin-login-table">

						<?php // Checkbox example ?>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'API Key', 'text-domain' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'api_key' ); ?>
								<input type="text" name="theme_options[api_key]" value="<?php echo esc_attr( $value ); ?>" required>
							</td>
						</tr>

						<?php // Text input example ?>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Header Title', 'text-domain' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'header_title' ); ?>
								<input type="text" name="theme_options[header_title]" value="<?php echo esc_attr( $value ); ?>" required>
							</td>
						</tr>

                        <tr valign="top">
							<th scope="row"><?php esc_html_e( 'Header Background Color', 'text-domain' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'header_background_color' ); ?>
								<input type="text" id="headerBGColorInput" name="theme_options[header_background_color]" value="<?php echo esc_attr( $value ); ?>" required>
								<button type="button" id="headerBGColor" class="bcpcolor btn btn-primary" style="<?php if($value){echo "background-color: ".$value.";border-color: ".$value.";";} ?>"></button>								
							</td>
						</tr>

                        <tr valign="top">
							<th scope="row"><?php esc_html_e( 'Header Text Color', 'text-domain' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'header_text_color' ); ?>
								<input type="text" id="headerTextColorInput" name="theme_options[header_text_color]" value="<?php echo esc_attr( $value ); ?>" required>
								<button type="button" id="headerTextColor" class="bcpcolor btn btn-primary" style="<?php if($value){echo "background-color: ".$value.";border-color: ".$value.";";} ?>"></button>
							</td>
						</tr>

                        <tr valign="top">
							<th scope="row"><?php esc_html_e( 'Chat Response Color', 'text-domain' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'chat_response_color' ); ?>
								<input type="text" id="chatResponseColorInput" name="theme_options[chat_response_color]" value="<?php echo esc_attr( $value ); ?>" required>
								<button type="button" id="chatResponseColor" class="bcpcolor btn btn-primary" style="<?php if($value){echo "background-color: ".$value.";border-color: ".$value.";";} ?>"></button>
							</td>
						</tr>

                        <tr valign="top">
							<th scope="row"><?php esc_html_e( 'Chat Response Text Color', 'text-domain' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'chat_response_text_color' ); ?>
								<input type="text" id="chatResponseTextColorInput" name="theme_options[chat_response_text_color]" value="<?php echo esc_attr( $value ); ?>" required>
								<button type="button" id="chatResponseTextColor" class="bcpcolor btn btn-primary" style="<?php if($value){echo "background-color: ".$value.";border-color: ".$value.";";} ?>"></button>
							</td>
						</tr>

                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Logo URL', 'text-domain' ); ?></th>
							<td>
                                <?php 
                                    $value = self::get_theme_option( 'logourl' ); 
                                    echo self::webchat_image_uploader_field( 'logourl', $value )
                                ?>
								
							</td>
						</tr>

                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Web Chat Fab Icon', 'text-domain' ); ?></th>
							<td>
                                <?php 
                                    $value = self::get_theme_option( 'boturl' ); 
                                    echo self::webchat_image_uploader_field( 'boturl', $value )
                                ?>
								
							</td>
						</tr>

                        <tr valign="top">
							<th scope="row"><?php esc_html_e( 'Welcome Message', 'text-domain' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'welcome_message' ); ?>
								<input type="text" name="theme_options[welcome_message]" value="<?php echo esc_attr( $value ); ?>">
							</td>
						</tr>

                        <tr valign="top">
							<th scope="row"><?php esc_html_e( 'Welcome Intent', 'text-domain' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'welcome_intent' ); ?>
								<input type="text" name="theme_options[welcome_intent]" value="<?php echo esc_attr( $value ); ?>" required>
							</td>
						</tr>

					</table>

					<?php submit_button(); ?>

				</form>

			</div><!-- .wrap -->
            <?php 
        }

	}
}
new BotcoWebchat();

// Helper function to use in your theme to return a theme option value
function myprefix_get_theme_option( $id = '' ) {
	return BotcoWebchat::get_theme_option( $id );
}