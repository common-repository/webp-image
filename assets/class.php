<?php defined('ABSPATH') or die('No script kiddies please!');
/**
 * @package WordPress
 * @subpackage WebP
 *
 * @author RapidDev
 * @copyright Copyright (c) 2019-2020, RapidDev
 * @link https://www.rdev.cc/webp
 * @license https://opensource.org/licenses/MIT
 */
	/**
	*
	* RDEV_WEBP
	*
	* @author   Leszek Pomianowski <https://rdev.cc>
	* @access   public
	*/
	if(!class_exists('RDEV_WEBP'))
	{
		class RDEV_WEBP
		{

			private $pngactive = false;
			private $jpgactive = false;
			private $gifactive = false;

			/**
			* __construct
			* The constructor registers the language domain, filters and other actions
			*
			* @access   public
			*/
			public function __construct()
			{
				//Reset verify for debug purposes
				update_option('rdev_webp_verify', false);

				$this->IsActive();

				//Register languages
				add_action( 'plugins_loaded', function()
				{
					load_plugin_textdomain( RDEV_WEBP_DOMAIN, false, basename( RDEV_WEBP_PATH ) . '/languages/' );
				} );

				if( $this->Verify() )
				{
					$this->InitWebP();
					$this->InitAdmin();
				}
			}

			/**
			* IsActive
			* Check if there are any supported formats
			*
			* @access   private
			*/
			private function IsActive()
			{
				if( function_exists( 'imagecreatefrompng' ) )
					$this->pngactive = true;

				if( function_exists( 'imagecreatefromjpeg' ) )
					$this->jpgactive = true;

				if( function_exists( 'imagecreatefromgif' ) )
					$this->gifactive = true;
			}

			/**
			* InitWebP
			* Add filters when adding images
			*
			* @access   private
			*/
			private function InitWebP()
			{
				//Create WEBP
				add_filter( 'wp_handle_upload', array( $this, 'MirrorImage' ), 10, 2 );

				//Add custom meta
				add_filter( 'wp_generate_attachment_metadata', array( $this, 'MetaGenerate' ), 10, 2 );

				//Display custom meta
				add_filter( 'attachment_fields_to_edit', array( $this, 'MetaFields' ), 10, 4 );
			}

			/**
			* InitAdmin
			* Add an admin panel
			*
			* @access   private
			*/
			private function InitAdmin()
			{
				if ( is_admin() )
				{
					//Admin page
					add_action( 'admin_head', function()
					{
						echo '<style>.tools_page_webp-panel #wpcontent, .tools_page_webp-panel.auto-fold #wpcontent {padding-left: 0;}</style>';
					} );

					add_action( 'admin_menu', function()
					{
						add_submenu_page(
							'tools.php',
							__('WebP Panel', RDEV_WEBP_DOMAIN),
							__('WebP Panel', RDEV_WEBP_DOMAIN),
							'manage_options',
							'webp-panel',
							array($this, 'AdminPageHTML'),
							'dashicons-format-video'
						);
					} );

					//Plugin links
					add_filter( 'plugin_action_links_' . RDEV_WEBP_BASENAME, function( $data )
					{
						$links = array();
						$links[] = '<a href="' . admin_url('tools.php?page=webp-panel') . '">' . __('JPEG to WebP converter', RDEV_WEBP_DOMAIN) . '</a>';
						return array_merge( $links, $data );
					} );

					//Ajax force add WebP to existing jpeg
					add_action( 'wp_ajax_WebPForceJpeg', array( $this, 'WebPForceJpeg' ) );
					add_action( 'wp_ajax_nopriv_WebPForceJpeg', array( $this, 'WebPForceJpeg' ) );
				}
			}

			/**
			* MirrorImage
			* If the new medium being uploaded in JPEG format, the method creates copy in the WebP format
			*
			* @access	public
			* @param	array $upload
			* @param	array $context
			* @return	array $upload
			*/
			public function MirrorImage( $upload, $context )
			{
				if( $upload[ 'type' ] == 'image/jpeg' && $this->jpgactive )
					$this->ConvertToWebP( $upload[ 'file' ], 'jpeg' );

				if( $upload[ 'type' ] == 'image/png' && $this->pngactive )
					$this->ConvertToWebP( $upload[ 'file' ], 'png' );

				if( $upload[ 'type' ] == 'image/gif' && $this->gifactive )
					$this->ConvertToWebP( $upload[ 'file' ], 'gif' );

				return $upload;
			}

			/**
			* MetaGenerate
			* If the uploaded file is in JPEG format, add a new meta with the url to the WebP format alternative
			*
			* @access	public
			* @param	array $metadata
			* @param	int   $attachment_id
			* @return	array $metadata
			*/
			public function MetaGenerate( $metadata, $attachment_id )
			{
				$post = get_post( $attachment_id );
				
				if( $post->post_mime_type == 'image/jpeg' && $this->jpgactive || $post->post_mime_type == 'image/png' && $this->pngactive || $post->post_mime_type == 'image/gif' && $this->gifactive )
				{
					$filepath = get_attached_file( $attachment_id );
					$filename = wp_get_attachment_url( $post->ID );

					$meta_content = '';

					if( file_exists( $filepath . '.webp' ) )
					{
						$meta_content = wp_get_attachment_url($post->ID) . '.webp';
					}

					update_post_meta( $attachment_id, '_webp_alternative', $meta_content );
				}

				return $metadata;
			}

			/**
			* MetaFields
			* If the displayed medium is in JPEG format, add a new field with the url address to the equivalent in WebP format
			*
			* @access	public
			* @param	array  $form_fields
			* @param	object $post
			* @return	array  $form_fields
			*/
			public function MetaFields( $form_fields, $post )
			{
				//Display WebP meta box only for jpeg
				if( $post->post_mime_type == 'image/jpeg' || $post->post_mime_type == 'image/png' || $post->post_mime_type == 'image/gif' )
				{
					$form_fields[ 'webp_alternative' ] = array(
						'label' => __( 'WebP Alternative', RDEV_WEBP_DOMAIN ),
						'input' => 'html',
						'html' => "<input type='text' class='text urlfield' readonly='readonly' name='attachments[$post->ID][webp_alt]' value='" . esc_attr( get_post_meta( $post->ID, '_webp_alternative', true ) ) . "' /><br />",
						'helps' => __( 'If the image has its alternative version in the WebP format, a link to it will be displayed above.', RDEV_WEBP_DOMAIN ),
					);
				}
				return $form_fields;
			}

			/**
			* ConvertToWebP
			* Create a WebP file based on JPEG/PNG/GIF
			*
			* @access	public
			* @param	string	$path
			* @param	string	$type
			* @return	bool	true/false
			*/
			public function ConvertToWebP( $path, $type )
			{
				try
				{
					$image = null;
					//Generate image
					if( $type == 'png')
					{
						$image = imagecreatefrompng( $path );
					}
					else if ( $type == 'gif' )
					{
						$image = imagecreatefromgif( $path );
					}
					else if( $type == 'jpeg' )
					{
						$image = imagecreatefromjpeg( $path );
					}
					
					if( $image != null )
					{
						/*ob_start();
						imagejpeg( $image, null, 100 );
						
						$image_content = ob_get_contents();
						ob_end_clean();

						//Clear up memory
						imagedestroy( $image );
						$content = imagecreatefromstring( $image );*/

						//Save webp image
						imagewebp ( $image, $path . '.webp', 100 );

						//Clear up memory
						imagedestroy( $image );

						return true;   // <-- This will never happen if an exception is raised
					}
					else
					{
						return false;
					}
				}
				catch( Exception $e )
				{
					return false;
				}
			}

			/**
			* AdminPageHTML
			* JPEG to Webp converter page template
			*
			* @access	public
			*/
			public function AdminPageHTML()
			{
				if ( is_file( RDEV_WEBP_PATH.'assets/adminpage.php' ) )
				{
					include( RDEV_WEBP_PATH.'assets/adminpage.php' );
				}
			}

			/**
			* AdminPageHTML
			* JPEG to Webp converter page template
			*
			* @access	public
			*/
			public function WebPForceJpeg()
			{
				//Debug converter
				//update_option('rdev_webp_lock_converter', false);

				//Verify salt	
				check_ajax_referer( 'nonce_rdev_webp', 'salt' );

				//Response array for json
				$response = array(
					'status' => 1,
					'response' => 'error_0'
				);

				//Check request
				if ( isset( $_POST[ 'request' ] ) )
				{
					//Lock converter to prevent server lagging and errors
					if(!get_option( 'rdev_webp_lock_converter', false ))
					{
						update_option('rdev_webp_lock_converter', true );

						//Start querying all jpeg images
						$query_jpeg = new WP_Query(
							array(
								'post_type' => 'attachment',
								'post_mime_type' => array(
									'jpg|jpeg|jpe' => 'image/jpeg',
								),
								'post_status' => 'inherit',
								'posts_per_page' => -1,
							)
						);

						//Check if image has webp
						$count_converted = $count_errors = 0;
						foreach ($query_jpeg->posts as $image)
						{
							$path = realpath(get_attached_file($image->ID, true));
							if( !is_file( $path . '.webp' ))
							{
								if( $this->JpegToWebP( $path ) )
								{
									$count_converted++;
									update_post_meta( $image->ID, '_webp_alternative', $image->guid . '.webp' );
								}
								else
								{
									$count_errors++;
								}
							}
						}

						$response[ 'c_err' ] = $count_errors;
						$response[ 'c_cov' ] = $count_converted;
						$response[ 'response' ] = 'success';

						update_option( 'rdev_webp_lock_converter', false );
					}
					else
					{
						$response['response'] = 'error_2';
					}
				}
				else
				{
					$response['response'] = 'error_1';
				}

				exit( json_encode( $response, JSON_UNESCAPED_UNICODE ) );
			}

			/**
			* AdminAlert
			* Display error alert in administration panel
			*
			* @access	public
			*/
			public function AdminAlert()
			{
				switch( RDEV_WEBP_ERROR )
				{
					case 1:
						$message = str_replace( '%s', RDEV_WEBP_PHP_VERSION,__('Your PHP version is outdated. Please, upgrade your PHP to a higher or equal version than %s.', RDEV_WEBP_DOMAIN ) );
						break;

					case 2:
						$message = __( 'Your server does not have the proper libraries to process JPEG images into WebP.<br/>Missing functions: <i>imagecreatefromjpeg, imagecreatefrompng, imagewebp, imagedestroy</i>', RDEV_WEBP_DOMAIN );
						$message = $message.'<br /><strong><i>php_gd2.dll</i></strong>';
						break;

					default:
						$message = __( 'There was an unidentified error. We should look deeper...', RDEV_WEBP_DOMAIN );
						break;
				}
				delete_option( 'rdev_webp_verify' );
				echo '<div class="error notice"><p><strong>' . RDEV_WEBP_NAME . '</strong><br />' . $message . '</p><p><i>'.__('ERROR ID', RDEV_WEBP_DOMAIN).': ' . RDEV_WEBP_ERROR . '</i></p></div>';
			}

			/**
			* AdminNotice
			* Set the error code and then display the error
			*
			* @access	private
			* @param	int $id
			*/
			private function AdminNotice( $id = 0 )
			{
				define( 'RDEV_WEBP_ERROR', $id );
				add_action( 'admin_notices', array( $this, 'AdminAlert' ) );
			}

			/**
			* Verify
			* Check if the server and wordpress support everything necessary for the plugin to work
			*
			* @access	private
			*/
			private function Verify()
			{
				//If the support already checked, skip
				$compatibility = get_option( 'rdev_webp_verify', false );
				if ( $compatibility )
					return true;

				//Check PHP
				$php = false;
				if ( version_compare( PHP_VERSION, RDEV_WEBP_PHP_VERSION, '>=' ) )
					$php = true;

				//Check libs
				$libs = false;
				if( ( $this->pngactive || $this->jpgactive || $this->gifactive ) && function_exists( 'imagewebp' ) && function_exists( 'imagedestroy' ) )
					$libs = true;

				//Update status
				if( $php && $libs )
				{
					update_option( 'rdev_webp_verify', true );
					return true;
				}
				else
				{
					if( !$php )
					{
						$this->AdminNotice( 1 );
					}
					else if( !$libs )
					{
						$this->AdminNotice( 2 );
					}

					return false;
				}
			}
		}
	}
