<?php
/*
Plugin Name: Lumise - Product Designer Tool
Plugin URI: https://www.lumise.com/
Description: The professional solution for designing & printing online
Author: King-Theme
Version: 1.7.3
Author URI: http://king-theme.com/
*/

if(!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR );
}
if(!defined('LUMISE_WOO')) {
	define('LUMISE_WOO', '1.7.3' );
}
if ( ! defined( 'LUMISE_FILE' ) ) {
	define('LUMISE_FILE', __FILE__ );
	define('LUMISE_PLUGIN_BASENAME', plugin_basename(LUMISE_FILE));
}	

class lumise_woocommerce {
	    
    public $url;
    
    public $admin_url;
    
    public $path;
    
    public $app_path;
    
    public $upload_url;
    
    public $upload_path;
    
    public $assets_url;
	
    public $checkout_url;
    
    public $admin_assets_url;
    
    public $ajax_url;
    
    public $product_id;

    public $prefix;
	
	private $connector_file = 'woo_connector.php';

    public function __construct() {
        
        global $wpdb;
       
        $this->prefix = 'lumise_';
        $this->url = plugin_dir_url(__FILE__).'editor.php';
		
        $this->tool_url = site_url('/?lumise=design');
        $this->admin_url = admin_url('admin.php?page=lumise');
        
        $this->path = dirname(__FILE__).DS;
        
        $this->app_path = $this->path . 'core'.DS;
        
        $this->upload_path = WP_CONTENT_DIR.DS.'uploads'.DS.'lumise_data'.DS;
        
        $this->upload_url = content_url('uploads/lumise_data/');
        
        $this->assets_url = plugin_dir_url(__FILE__) . 'core/';
        
        $this->admin_assets_url = plugin_dir_url(__FILE__) . 'core/admin/assets/';
        
        $this->ajax_url =  site_url('/?lumise=ajax');
        
        $this->admin_ajax_url =  admin_url('?lumise=ajax');
		
        $this->checkout_url =  site_url('/?lumise=cart');

        define('LUMISE_PATH', $this->path . 'core'.DS);
        
        define('LUMISE_ADMIN_PATH', $this->path . 'core'.DS.'admin'.DS);

        register_activation_hook(__FILE__, array($this, 'activation'), 10);
        
		add_action( 'activated_plugin', array($this, 'activation_redirect'), 10 );
		add_action( 'woocommerce_single_product_summary', array($this, 'remove_actions') ,1);

        //process ajax lumise
		
        add_action( 'wp_loaded', array(&$this, 'loaded'), 10);
        add_action( 'init', array(&$this, 'init'), 10);
        add_action( 'template_redirect', array(&$this, 'page_display'), 10);

		if (is_admin()) {

	        // create tab custom field in add min product detail
	
	        add_filter('woocommerce_product_data_tabs', array(&$this, 'woo_add_tab_attr'));
	
	        add_filter('woocommerce_product_data_panels', array(&$this, 'woo_add_product_data_fields'));
	
	        add_action('woocommerce_process_product_meta', array(&$this, 'woo_process_product_meta_fields_save'));
			
			//admin hooks

	        add_action( 'admin_menu', array(&$this, 'menu_page') );
			 
	        add_action( 'woocommerce_after_order_itemmeta', array(&$this, 'woo_admin_after_order_itemmeta'), 999, 3 );
	        add_action( 'woocommerce_before_order_itemmeta', array(&$this, 'woo_admin_before_order_itemmeta'), 999, 3 );
			 
			if (isset($_GET['page']) && $_GET['page'] == 'lumise'){
				add_action( 'wp_print_scripts', array(&$this, 'wpdocs_dequeue_script'), 100 );
			}
			
			add_action( 'admin_footer', array(&$this, 'admin_footer') );    	
	        
			add_action( 'admin_head', array(&$this, 'hide_wp_update_notice'), 1 );
			add_action( 'in_plugin_update_message-' .LUMISE_PLUGIN_BASENAME, array( &$this, 'update_message' ) );
	        add_filter( 'plugin_action_links_' . LUMISE_PLUGIN_BASENAME, array( &$this, 'plugin_action_links' ) );
	        add_filter( 'plugin_row_meta', array( &$this, 'plugin_row_meta' ), 10, 2 );
			add_filter( 'submenu_file', array( &$this, 'submenu_file'));
	        add_filter( 'woocommerce_order_item_get_quantity', array(&$this, 'woo_order_item_get_quantity' ), 10, 2 );
			add_filter( 'manage_edit-shop_order_columns', array(&$this, 'woo_lumise_order_column'), 10, 1 );
			add_action( 'manage_shop_order_posts_custom_column', array(&$this, 'woo_lumise_column_content') );
	        
	        add_action( 'admin_notices', array(&$this, 'admin_notices') );
	        
	        if ($wpdb->get_var("SHOW TABLES LIKE 'lumise_settings'") == 'lumise_settings') {
		        
		        $this->update_core = $wpdb->get_results(
		        	"SELECT `value` from `lumise_settings` WHERE `key`='last_check_update'", 
		        	true
		        ); 
	
				$this->update_core = @json_decode($this->update_core[0]->value);
				
				$current = get_site_transient( 'update_plugins' );
				
				if (
					isset($this->update_core) && 
					version_compare(LUMISE_WOO, $this->update_core->version, '<') && 
					(
						!isset($current->response[LUMISE_PLUGIN_BASENAME]) ||
						$this->update_core->version > $current->response[LUMISE_PLUGIN_BASENAME]->new_version
					)
				) {
					$current->response[LUMISE_PLUGIN_BASENAME] = (Object)array(
						'package' => 'private',
						'new_version' => $this->update_core->version,
						'slug' => 'lumise-hook-sfm'
					);
					set_site_transient('update_plugins', $current);
				}else if (
					isset($current) && 
					isset($current->response[LUMISE_PLUGIN_BASENAME]) &&
					LUMISE_WOO >= $current->response[LUMISE_PLUGIN_BASENAME]->new_version
				) {
					unset($current->response[LUMISE_PLUGIN_BASENAME]);
					set_site_transient('update_plugins', $current);
				}
			}
			
			$role = get_role('administrator');
			
			$role->add_cap('lumise_access');
			$role->add_cap('lumise_can_upload');
			
			$role->add_cap('lumise_read_dashboard');
			$role->add_cap('lumise_read_settings');
			$role->add_cap('lumise_read_products');
			$role->add_cap('lumise_read_cliparts');
			$role->add_cap('lumise_read_templates');
			$role->add_cap('lumise_read_orders');
			$role->add_cap('lumise_read_shapes');
			$role->add_cap('lumise_read_printings');
			$role->add_cap('lumise_read_fonts');
			$role->add_cap('lumise_read_shares');
			$role->add_cap('lumise_read_bugs');
			$role->add_cap('lumise_read_languages');
			$role->add_cap('lumise_read_addons');
			
			$role->add_cap('lumise_edit_settings');
			$role->add_cap('lumise_edit_products');
			$role->add_cap('lumise_edit_cliparts');
			$role->add_cap('lumise_edit_templates');
			$role->add_cap('lumise_edit_orders');
			$role->add_cap('lumise_edit_shapes');
			$role->add_cap('lumise_edit_printings');
			$role->add_cap('lumise_edit_fonts');
			$role->add_cap('lumise_edit_shares');
			$role->add_cap('lumise_edit_languages');
			$role->add_cap('lumise_edit_categories');
			$role->add_cap('lumise_edit_tags');
			$role->add_cap('lumise_edit_bugs');
			$role->add_cap('lumise_edit_addons');
			   
		}
		
		//enqueue style for frontend
		add_action( 'wp_enqueue_scripts', array(&$this, 'frontend_scripts'), 999);
		
        // render data in page cart

        add_filter('woocommerce_cart_shipping_packages', array(&$this, 'shipping_packages'), 999, 2);
        add_filter('woocommerce_get_item_data', array(&$this, 'woo_render_meta'), 999, 2);
        
		
		add_filter('woocommerce_cart_item_name', array(&$this, 'woo_cart_edit_design_btn'), 10, 2);
		add_filter('woocommerce_cart_item_thumbnail', array(&$this, 'woo_cart_design_thumbnails'), 10, 2);
		add_action('woocommerce_after_cart_table', array(&$this, 'woo_after_cart_table'));
		
		// add meta data attr cart to order
        add_action('woocommerce_add_order_item_meta', array(&$this, 'woo_add_order_item_meta'), 1, 3);
		
		//remove cart item
		add_action('woocommerce_cart_item_removed', array(&$this, 'woo_cart_item_removed'), 1, 2);
		
        // save data to table product order
        add_action('woocommerce_new_order', array(&$this, 'woo_order_finish'), 20, 3);
		add_action( 'woocommerce_thankyou', array(&$this, 'woo_thank_you'), 20, 3);
		add_filter('woocommerce_loop_add_to_cart_link', array(&$this, 'woo_customize_link_list'), 999, 2);
		
        add_action( 'woocommerce_product_thumbnails', array(&$this, 'woo_add_template_thumbs' ), 30);
		
		//remove Order again button
		add_action( 'woocommerce_order_details_before_order_table', array(&$this, 'woo_order_details_before_order_table' ), 30);
		
		// Add custom price for items
        add_action('woocommerce_before_calculate_totals', array(&$this, 'woo_calculate_price'), 10, 1);
		
		// Add reorder button
		add_filter( 'woocommerce_my_account_my_orders_actions', array(&$this, 'my_orders_actions'), 999, 2);
		
		/*cart display*/
        add_action( 'woocommerce_cart_item_quantity', array(&$this, 'woo_cart_item_quantity' ), 30, 3);
        add_action( 'woocommerce_checkout_cart_item_quantity', array(&$this, 'woo_checkout_cart_item_quantity' ), 30, 3);
        add_action( 'woocommerce_order_item_quantity_html', array(&$this, 'woo_order_item_quantity_html' ), 30, 3);
        add_action( 'woocommerce_order_item_meta_start', array(&$this, 'woo_order_item_meta_start' ), 30, 3);
		
        add_filter( 'woocommerce_email_order_item_quantity', array(&$this, 'woo_email_order_item_quantity' ), 30, 2);
		
        add_filter( 'woocommerce_get_price_html', array(&$this, 'woo_product_get_price_html' ), 999, 2);
		
		add_action( 'woocommerce_email_order_details', array(&$this, 'email_customer_designs' ), 11, 4 );
		add_action( 'woocommerce_order_details_after_order_table', array(&$this, 'myaccount_customer_designs' ), 10, 1 );
		
		//hook delete order
		
        add_filter( 'before_delete_post', array(&$this, 'woo_remove_order' ), 999, 2);
		
		add_filter( 'display_post_states', array(&$this, 'add_display_post_states' ), 10, 2 );
		
    }

    public function activation() {
	    
        global $wpdb;
		
		$upload_path = WP_CONTENT_DIR.DS.'uploads'.DS;
		
		if ( !is_dir($upload_path) )
			wp_mkdir_p($upload_path);
		
		if ( !is_dir($this->upload_path) )
			wp_mkdir_p($this->upload_path);
		
		$design_editor = $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'design-editor'", 'ARRAY_A' );
		
		if ( null === $design_editor ) {

			$current_user = wp_get_current_user();
			
			$page = array(
				'post_title'  => __( 'Design Editor' ),
				'post_status' => 'publish',
				'post_author' => $current_user->ID,
				'post_type'   => 'page',
				'post_content'   => 'This is Lumise design page. Go to Lumise > Settings > Shop to change other page when you need.'
			);
			
			$page_id = wp_insert_post( $page );
			update_option('lumise_editor_page', $page_id);
			
		}
			
		return true;
		
    }    
    
    public function activation_redirect($plugin) {
	    
	    if( $plugin == plugin_basename( __FILE__ ) ) {
		    
		    global $wpdb;
		
			if ($wpdb->get_var("SHOW TABLES LIKE 'lumise_settings'") != 'lumise_settings') {
				
				$templine = '';
				$sql_file = $this->path .'woo'.DS.'sample'. DS . 'database.sql';
				
				$handle = @fopen( $sql_file, 'r' );
				$lines = @fread( $handle, @filesize($sql_file) );
	
				$lines = explode("\n", $lines);
				
				foreach ($lines as $line)
				{
					$s1 = substr($line, 0, 2);
					if ($s1 != '--' && $line !== '') {
						
						$templine .= $line;
						
						$line = trim($line);
						$s2 = substr($line, -1, 1);
						
						if ($s2 == ';')
						{
							$sql = $templine;
							$wpdb->query( $sql, false );
							$templine = '';
						}
					}
				}
				
				@fclose($handle);
				
			}
		    
		    return; 
		    
		    
			$setup = get_option('lumise_setup', false);
			
			if ($setup != 'done') {
				exit( wp_redirect( admin_url( 'admin.php?page=lumise-setup' ) ) );
			}
		}
		
    }
    
    public function render() {
	    
		show_admin_bar(false);
        //require bridge for frontend
        require_once($this->path . $this->connector_file);
        
        $editor_index = apply_filters('lumise_editor_index', $this->path . 'core'. DS . 'index.php');
        
        //require cutomize index
        require_once($editor_index);
        
    }
	
	public function woo_remove_order($order_id) {
		
		global $post_type, $lumise;

	    if($post_type !== 'shop_order') {
	        return;
	    }
		$this->load_core();
		$lumise->lib->delete_order_products($order_id);
	}
	
	public function init() {
		
		/*if (is_admin()) {
			if (isset($_GET['page']) && $_GET['page'] == 'lumise-setup') {
				$this->load_core();
				include $this->path.'woo'.DS.'setup.php';
				exit;
			}
		}*/
	}
	
	public function page_display() {
			
		global $wp_query;
		
		$editor = get_option('lumise_editor_page', 0);
		$id = $wp_query->get_queried_object_id();
		
		if ($editor > 0){
				
			if (
				(
					isset($_GET['page_id']) &&
					!empty($_GET['page_id']) &&
					$editor == $_GET['page_id']
				) ||
				(
					isset($_GET['product']) &&
					!empty($_GET['product_cms'])
				) ||
				(
					isset($_GET['product']) &&
					!empty($_GET['order_print'])
				) ||
				$editor == $id
			){
				
				$url = esc_url(get_page_link($editor));
			
				$this->tool_url = (strpos($url, '?') === false) ? $url . '?' : $url;
				$this->render();
				
				exit;
				
			}
		}
	}
	
    public function loaded() {
	    
		global $post, $lumise;
		
		$editor_page = get_option('lumise_editor_page', 0);
		$route = isset($_GET['lumise']) && !empty($_GET['lumise']) ? $_GET['lumise'] : null;
		
		if($editor_page > 0){
			$url = esc_url(get_page_link($editor_page));
			$this->tool_url = (strpos($url, '?') === false)? $url . '?': $url;
		}
		
        if ($route){
			switch ($route) {
				case 'design':
					@ob_end_clean();
					$this->render();
					exit;
				break;	
				case 'ajax':
				case 'cart':
					@ob_end_clean();
					$_GET['lumise-router'] = $route;
					$this->load_core();
				break;	
				default:
				break;
			}
			exit;
		}
        
    }
	
	public function admin_notices() {
		
		return;
		
		if (isset($_GET['lumise-hide-notice']) && $_GET['lumise-hide-notice'] == 'setup') {
			update_option('lumise_setup', 'done');
		} else {
		
			$setup = get_option('lumise_setup', false);
			$current = get_option( 'active_plugins', array() );
			if ($setup != 'done') {
			?>
			<div id="message" class="updated">
				<p>
					<strong><?php _e('Welcome to Lumise', 'lumise'); ?></strong> &#8211; 
					<?php _e('You&lsquo;re almost ready, Please create a Woo Product and link to a Lumise Product Base to start designing.', 'lumise'); ?>
				</p>
				<?php if (!in_array('woocommerce'.DS.'woocommerce.php', $current)) { ?>
				<p style="background: #f4433629;padding: 10px;">
					<?php _e('You need to install and activate the Woocommerce plugin so that Lumise can work', 'lumise'); ?> 
					<a href="<?php echo admin_url('plugins.php'); ?>">Go to plugins &rarr;</a>
				</p>
				<?php } ?>
				<p class="submit">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=lumise-setup' ) ); ?>" class="button-primary">
						<?php _e('Run the Setup Wizard', 'lumise'); ?>
					</a> &nbsp; 
					<a class="button-secondary skip" href="<?php echo esc_url(add_query_arg( 'lumise-hide-notice', 'setup' )); ?>">
						<?php _e('Skip setup', 'lumise'); ?>
					</a>
				</p>
			</div>
			<?php	
			}
		}
		
	}
	
	public function woo_order_item_get_quantity($qty, $item){

		$item_data = $item->get_data();
		
		$lumise_data = array();
		
		if (count($item_data['meta_data']) > 0) {
			
			$is_lumise = false;
			
			foreach ($item_data['meta_data'] as $meta_data) {
				if ($meta_data->key == 'lumise_data') {
					$is_lumise = true;
					break;
				}
			}
			
			if ($is_lumise) {
				
				$product_id = $item->get_product_id();
				$order_id = $item->get_order_id();
				
				global $lumise;
				
				if (!isset($lumise))
					$this->load_core();
				
				$items = $lumise->lib->get_order_products($order_id);
				
				if (count($items) > 0) {
					foreach ($items as $order_item) {
						if ($product_id == $order_item['product_id'])
							return $order_item['qty'];
					}
				}
			}
			
		}
		
		return $qty;
		
	}
	
	public function woo_lumise_order_column($columns) {

	    return array_slice($columns, 0, 3, true) + 
			   array('type' => 'Custom design') + 
			   array_slice($columns, $position, count($columns) - 1, true);
			   
	}
	
	public function woo_lumise_column_content($column) {
		
	    global $post, $wpdb;
	
	    if ( 'type' === $column ) {
			$is_lumise = $wpdb->get_results('SELECT `id` FROM `lumise_order_products` WHERE `order_id`='.$post->ID);
	        if (count($is_lumise) === 0) {
		    	echo '';
		    } else {
			    echo '<a href="'.(esc_url( admin_url( 'post.php?post='.$post->ID) ) ).'&action=edit">&#9733;</a>';
			}    
	        
	    }
	}
	
	public function woo_admin_before_order_itemmeta($item_id, $item, $product) {
		
		global $lumise_printings, $lumise;
		
		if( !isset($lumise) ) $this->load_core();
		if( !isset($lumise_printings) ) {
			$lumise_printings = $lumise->lib->get_prints();
		}
		
	}
	
	public function woo_order_details_before_order_table($order) {
		
		global $wpdb;
		
		$this->load_core();

        $table_name 	= $this->prefix."order_products";
		$order_id 		= $order->get_id();
		$count_order 	= $wpdb->get_var( " SELECT COUNT( * ) FROM $table_name WHERE order_id = $order_id" );

		if ($count_order > 0)
			remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );
			
	}

	public function woo_admin_after_order_itemmeta($item_id, $item, $product) {
		
		global $lumise, $lumise_printings;
		
		$this->load_core();
		
		$item_data = $item->get_data();
		
		$lumise_data = array();
		
		if (count($item_data['meta_data']) > 0) {
			
			$is_lumise = false;
			
			foreach ($item_data['meta_data'] as $meta_data) {
				if ($meta_data->key == 'lumise_data') {
					$is_lumise = true;
					break;
				}
			}
			
			if ($is_lumise) {
				
				global $post;
				
				$order_id 	= $post->ID;
				$products 	= $lumise->lib->get_order_products($post->ID);
				$product_id = $product->get_id();
				
				foreach ($products as $product) {
					
					if ($product['product_id'] == $product_id) {
						
						$data_obj = $lumise->lib->dejson($product['data']);
						
						if (isset($data_obj->attributes)) {
							
							$attrs = (array) $data_obj->attributes;
							
							foreach ($attrs as $name => $options) {
								
								if (is_object($options) && isset($options->name)) {
									
									if (isset($options->value)) {
										echo '<div><strong>'.$options->name.':</strong> ';
										if ($options->type == 'color' || $options->type == 'product_color') {
											$cols = explode("\n", $options->values);
											$val = trim($options->value);
											$lab = $options->value;
											foreach ($cols as $col) {
												$col = explode('|', $col);
												if (trim($col[0]) == $val && isset($col[1]) && !empty($col[1]))
													$lab = $col[1];
											}
											echo '<span title="'.htmlentities($options->value).'" style="background:'.$options->value.';padding: 3px 8px;border-radius: 12px;">'.htmlentities($lab).'</span>';
										} else echo '<span>'.$options->value.'</span>';
										echo '</div>';
									}
									
								} else {
									echo '<dt class="lumise-variation">'.$name.':</dt>';
									foreach ($options as $option) {
										echo '<dd class="lumise-variation">'.$option.'</dd>';
									}
								}
							}
							
						}
						
						if (isset($data_obj->variation) && !empty($data_obj->variation)) {
							
							echo "<div>";
							echo "<strong>".$lumise->lang('Variation').":</strong> ";
							echo "<span>#".$data_obj->variation."</span>";
							echo "</div>";
							
						}
						
						if (isset($data_obj->printing) && is_array($lumise_printings)) {
							
							foreach ($lumise_printings as $pmethod) {
								if ($pmethod['id'] == $data_obj->printing) {
									echo "<div>";
									echo "<strong>".$lumise->lang('Printing').":</strong> ";
									echo "<span>".$pmethod['title']."</span>";
									echo "</div>";
								}
							}
							
						}
						
						if (isset($product['screenshots']) && !empty($product['screenshots'])) {
							
							$screenshots = json_decode($product['screenshots'], true);
							
							echo '<p>';
							if (count($screenshots) > 4) {
								$more = count($screenshots)-4;
								array_splice($screenshots, 0, 4);
							} else $more = 0;
							
							foreach ($screenshots as $screenshot) {
								echo '<img style="border-radius: 2px;" src="'.($lumise->cfg->upload_url.'orders/'.$screenshot).'" width="80" /> ';
							}
							if ($more > 0)
								echo $more.'+';	
							echo '</p>';
							
						}
						
						$is_query = explode('?', $lumise->cfg->tool_url);
							
						$url = $lumise->cfg->tool_url.(isset($is_query[1])? '&':'?');
						$url .= 'product='.$product['product_base'];
						$url .= (($product['custom'] == 1)? '&design_print='.str_replace('.lumi', '', $product['design']) : '');
						$url .= '&order_print='.$order_id;
						$url .= ($lumise->connector->platform != 'php' ? '&product_cms='.$product['product_id'] : '');
						$url = str_replace('?&', '?', $url);
						
						?>
						<p>
							<a target="_blank" class="btn btn-print-design" href="<?php echo $url; ?>">
								<?php echo $lumise->lang('View in editor'); ?>
							</a>
							&nbsp; | &nbsp;
							<a target="_blank" class="btn btn-print-design" href="<?php echo $url; ?>&print_download=png">
								<?php echo $lumise->lang('Download design'); ?> (PNG)
							</a>
							&nbsp; | &nbsp;
							<a target="_blank" class="btn btn-print-design" href="<?php echo $url; ?>&print_download=pdf">
								<?php echo $lumise->lang('Download design'); ?> (PDF)
							</a>
						<p>
						<?php
						break;
					}
				}
			}
		}
	}
    
    public function menu_page() {
        
        global $wpdb;
        
        $title = 'Lumise';
        
        if (
        	isset($this->update_core) && 
        	version_compare(LUMISE_WOO, $this->update_core->version, '<')
        )
        	$title .= ' <span class="update-plugins"><span class="plugin-count">1</span></span>';
        
        $title .= '<style type="text/css">#toplevel_page_lumise img{height: 20px;box-sizing: content-box;margin-top: -3px;}</style>';
        
        add_menu_page( 
            	__( 'Lumise', 'lumise' ),
                $title,
                'lumise_access',
                'lumise',
                array($this, 'admin_page'),
                $this->assets_url . 'assets/images/icon.png',
            90
        );
        
        add_submenu_page( 
        	'lumise', 
        	'Lumise'.(!empty($_GET['lumise-page']) ? ' '. ucfirst($_GET['lumise-page']) : ''), 
        	__( 'Dashboard', 'lumise' ),
        	'lumise_access', 
        	'lumise'
        );
        
        add_submenu_page( 
        	'lumise', 
        	__( 'Orders', 'lumise' ), 
        	__( 'Orders', 'lumise' ),
        	'lumise_access', 
        	'admin.php?page=lumise&lumise-page=orders'
        );
        
        add_submenu_page( 
        	'lumise', 
        	__( 'Addons', 'lumise' ), 
        	__( 'Addons', 'lumise' ),
        	'lumise_access', 
        	'admin.php?page=lumise&lumise-page=explore-addons'
        );
        
        add_submenu_page( 
        	'lumise', 
        	__( 'Help', 'lumise' ), 
        	__( 'Help', 'lumise' ),
        	'lumise_access', 
        	'https://help.lumise.com'
        );
        
        add_submenu_page( 
        	'lumise', 
        	__( 'Settings', 'lumise' ), 
        	__( 'Settings', 'lumise' ),
        	'lumise_access', 
        	'admin.php?page=lumise&lumise-page=settings'
        );
        
    }
	
    public function admin_page() {
		
		if (!defined('LUMISE_ADMIN'))
			define('LUMISE_ADMIN', true);
			
        $this->load_core();
        require_once($this->path . 'core'.DS . 'admin' .DS .'index.php');
        
    }

    public function woo_add_tab_attr( $product_data_tabs ) {
	    
        global $post;
		$product = wc_get_product( $post->ID );

		$product_data_tabs['lumise'] = array(
			'label' => __( 'Lumise', 'lumise' ),
			'target' => 'lumise_product_data'
		);
		
        return $product_data_tabs;
    }
	
	public function woo_customize_link_list($html){
		
		global $product, $wpdb, $lumise;
		
		$config = get_option('lumise_config', array());
		
		if	(isset($config['btn_list']) && !$config['btn_list']) 
			return $html;
		
		$this->load_core();
		
		$product_id = $product->get_id();
		
		$sql_design = "
					SELECT pm.* FROM " . $wpdb->prefix . "posts as posts INNER JOIN " . $wpdb->prefix . "postmeta as pm  
				  ON ( pm.post_id = " . $product_id . " AND posts.ID = " . $product_id . ") 
				  WHERE  pm.meta_key = 'lumise_product_base' AND  pm.meta_value > 0
				  AND posts.post_type = 'product' AND  posts.post_status = 'publish'
			  ";

		$product_have_design = $wpdb->get_results( $sql_design, ARRAY_A);
		
		if(!count($product_have_design)) return $html;
		
		$sql_custom = "
				  SELECT * FROM " . $wpdb->prefix . "posts as posts  INNER JOIN " . $wpdb->prefix . "postmeta as pm 
				  ON ( pm.post_id = " . $product_id . " AND posts.ID = " . $product_id . ")
				  WHERE ( pm.meta_key = 'lumise_customize' AND  pm.meta_value = 'yes'
				  AND posts.post_type = 'product' AND  posts.post_status = 'publish')
			   ";

		$product_have_custom = $wpdb->get_results( $sql_custom, ARRAY_A);
		$is_product_base = $lumise->lib->get_product($product_have_design[0]['meta_value']);
		
		$sql_custom = "
                  SELECT * FROM " . $wpdb->prefix . "posts as posts  INNER JOIN " . $wpdb->prefix . "postmeta as pm 
                  ON ( pm.post_id = " . $product_id . " AND posts.ID = " . $product_id . ")
                  WHERE ( pm.meta_key = 'lumise_disable_add_cart' AND  pm.meta_value = 'yes'
                  AND posts.post_type = 'product' AND  posts.post_status = 'publish')
               ";
        $disable_add_cart = $wpdb->get_results( $sql_custom, ARRAY_A);
		
		if(
			count($product_have_design) > 0 &&
			count($product_have_custom) > 0 &&
			$is_product_base != null
		){
			$link_design = str_replace('?&', '?', $this->tool_url . '&product='.$product_have_design[0]['meta_value'].'&product_cms=' . $product_id );
			$link_design = apply_filters( 'lumise_customize_link', $link_design );
			return $html = (count($disable_add_cart) > 0 ?'' : $html).'<a class="lumise-button lumise-list-button" href="' . esc_url($link_design ). '">' . (isset($config['btn_text'])? $config['btn_text'] : __('Customize', 'lumise')) .'</a>' ;
		}
		
		return $html;
	}
	
    // add element html to tab custom product
    public function woo_add_product_data_fields() {
        
		$screen = get_current_screen();
		
		if (
			$screen->parent_file == 'edit.php?post_type=product' || 
			$screen->post_type == 'product'
		) {
			
			$this->load_core();
			
			global $lumise;
			global $wpdb;
			
	    	$id = get_the_ID();
	    	$ops = array();
	    	$js_cfg = array();
			
	        $ops['lumise_product_base'] = get_post_meta($id, 'lumise_product_base', true );
	        $ops['lumise_design_template'] = get_post_meta($id, 'lumise_design_template', true );
	        $ops['lumise_customize'] = get_post_meta($id, 'lumise_customize', true );
	        $ops['lumise_disable_add_cart'] = get_post_meta($id, 'lumise_disable_add_cart', true );
				
        	if (!empty($ops['lumise_product_base'])) {
	        	
	        	$query = "SELECT `name`,`stages`,`attributes` FROM `{$lumise->db->prefix}products` WHERE `id`={$ops['lumise_product_base']}";
	        	$data = $wpdb->get_results($query);
	        	
	        	if (isset($data[0]) && isset($data[0]->stages)) {
		        	
		        	$color = $lumise->lib->get_color($data[0]->attributes);
		        	
		        	$js_cfg['current_data'] = array(
						'id' => $ops['lumise_product_base'],
						'name' => $data[0]->name,
						'color' => $color,
						'stages' => $data[0]->stages,
					);
					
					$stage = $lumise->lib->dejson($data[0]->stages);
					
					if (isset($stage) && isset($stage->front) && isset($stage->front->label) && !empty($stage->front->label))
						$js_cfg['_front'] = rawurldecode($stage->front->label);
					if (isset($stage) && isset($stage->back) && isset($stage->back->label) && !empty($stage->back->label))
						$js_cfg['_back'] = rawurldecode($stage->back->label);
					if (isset($stage) && isset($stage->left) && isset($stage->left->label) && !empty($stage->left->label))
						$js_cfg['_left'] = rawurldecode($stage->left->label);
					if (isset($stage) && isset($stage->right) && isset($stage->right->label) && !empty($stage->right->label))
						$js_cfg['_right'] = rawurldecode($stage->right->label);
	        	}
        	}
			
			if (!empty($ops['lumise_design_template'])) {
	        	
	        	$designs = json_decode(rawurldecode($ops['lumise_design_template']));
	        	
	        	foreach($designs as $s => $d) {
		        	
		        	$data = $wpdb->get_results("SELECT `name`,`screenshot` FROM `{$lumise->db->prefix}templates` WHERE `id`=".$d->id);
		        	if (isset($data[0]))
			        	$designs->{$s}->screenshot = $data[0]->screenshot;
			        else unset($designs->{$s});
			        
	        	}
	        	
	        	$js_cfg['current_design'] = $designs;
	        	
        	}
		
			lumise_cms_product_data_fields($ops, $js_cfg, $id);
		
		}
		
    }

	// save value element data tabs

    public function woo_process_product_meta_fields_save($post_id) {
	  	
	    global $wpdb;
	    
	    $product_base = isset($_POST['lumise_product_base']) ? $_POST['lumise_product_base'] : '';
	    $design_template = isset($_POST['lumise_design_template']) ? $_POST['lumise_design_template'] : '';
	    $lumise_customize = isset($_POST['lumise_customize']) ? $_POST['lumise_customize'] : 'no';
	    $addcart = isset($_POST['lumise_disable_add_cart']) ? $_POST['lumise_disable_add_cart'] : 'no';

        update_post_meta($post_id, 'lumise_disable_add_cart', $addcart);
        update_post_meta($post_id, 'lumise_customize', $lumise_customize);
        update_post_meta($post_id, 'lumise_product_base', $product_base);
        update_post_meta($post_id, 'lumise_design_template', $design_template);
		
        
        if (!empty($product_base) && $lumise_customize == 'yes') {
	        $check = $wpdb->get_results("SELECT `product` FROM `lumise_products` WHERE `id` = $product_base", OBJECT);
	        if (isset($check[0])) {
				$wpdb->query("UPDATE `lumise_products` SET `product` = 0 WHERE `product` = $post_id");
		        $wpdb->query("UPDATE `lumise_products` SET `product` = $post_id WHERE `id` = $product_base");
	        }
        }
        
    }

	public function admin_footer() {
		echo '<script type="text/javascript">jQuery(\'a[href="https://help.lumise.com"]\').attr({target: \'_blank\'})</script>';	
	}

	/** Frontend**/
	
	public function frontend_scripts() {
		
		wp_register_script('lumise-frontend', plugin_dir_url(__FILE__) . 'woo/assets/js/frontend.js', array('jquery'), LUMISE_WOO, true);
		
		wp_register_style('lumise-style', plugin_dir_url(__FILE__).'woo/assets/css/frontend.css', false, LUMISE_WOO);
		
		wp_enqueue_style('lumise-style');
		wp_enqueue_script('lumise-frontend');
		
	}

    //Render attributes from lumise
    public function woo_render_meta( $cart_data, $cart_item = null ){
	    
		// get data in cart
		global $lumise;
		
        $custom_items = array();

        if( !empty( $cart_data ) )  $custom_items = $cart_data;	
		
		if(
			function_exists( 'is_cart' ) 
			&& is_cart() 
			&& isset( $cart_item[ 'lumise_data' ] )
		){
			
			if( !isset($lumise) ) 
				$this->load_core();
			
			$cart_item_data = $lumise->lib->get_cart_data( $cart_item['lumise_data'] );
	
			if ( is_array($cart_item_data ) ){
				
				foreach ( $cart_item_data['attributes'] as $aid => $attr ) {
					
					if (isset($attr['value']) ) {
						
						$val = $attr['value'];
						
						if (
							$attr['type'] == 'color' || 
							$attr['type'] == 'product_color'
						) {
							
							$vals = explode("\n", $attr['values']);
							
							foreach ($vals as $v) {
								$v = explode('|', $v);
								if (trim($val) == trim($v[0])) {
									$val = '<span style="background-color: '.$v[0].';padding: 1px 3px;">'.
									(isset($v[1]) ? $v[1] : $v[0]).'</span>';
								}
							}
						}
						
						$custom_items[] = array( 
							"name" => $attr['name'], 
							"value" => $val
						);
					}
					
				}
			}
			
		}
        return $custom_items;
    }
	
	//design thumbnails in cart page
	public function woo_cart_design_thumbnails($product_image, $cart_item) {
		
		global $lumise, $lumise_cart_thumbnails;
		
		$design_thumb = '';
		
		if (
			function_exists('is_cart') && 
			is_cart() && 
			isset($cart_item['lumise_data'])
		) {
			
			if( !isset($lumise) ) $this->load_core();
			
			$cart_item_data = $lumise->lib->get_cart_data( $cart_item['lumise_data'] );
			
			if(
				isset($cart_item_data['screenshots']) 
				&& is_array($cart_item_data['screenshots'])
			){
				$allowed_tags = wp_kses_allowed_html( 'post' );
				$uniq = uniqid();
				$lumise_cart_thumbnails[$uniq] = array();
				$design_thumb = '<div class="lumise-cart-thumbnails lumise-cart-thumbnails-'.$uniq.'"></div>';

				foreach ($cart_item_data['screenshots'] as $screenshot) {
					$lumise_cart_thumbnails[$uniq][] = $screenshot;
				}
			}
		}
		
		return $product_image.$design_thumb;
	}
	
	public function woo_after_cart_table() {
		global $lumise_cart_thumbnails;
		if( is_array($lumise_cart_thumbnails) ) {
			echo "<script>var lumise_cart_thumbnails = ".json_encode($lumise_cart_thumbnails).";</script>";
		}
	}
	
    //Add custom price to product cms
    public function woo_calculate_price($cart_object) {
	    
		global $wpdb, $lumise;
		
        if( !WC()->session->__isset( "reload_checkout" )) {
            $woo_ver = WC()->version;
			
			$this->load_core();

            foreach ($cart_object->cart_contents as $key => $value) {
				
				if( isset($value['lumise_data']) ){
					
					$cart_item_data = $lumise->lib->get_cart_data( $value['lumise_data'] );
					
					$lumise_price = (
						isset($cart_item_data['price']) && 
						isset($cart_item_data['price']['total'])
					) ? $cart_item_data['price']['total'] : 0;
					
					if ( version_compare( $woo_ver, '3.0', '<' ) ) {
			            $cart_object->cart_contents[$key]['data']->price = $lumise_price; // Before WC 3.0
			        } else {
						$cart_object->cart_contents[$key]['data']->price = $lumise_price; // Before WC 3.0
			            $cart_object->cart_contents[$key]['data']->set_price( $lumise_price ); // WC 3.0+
			        }
					
					$cart_object->cart_contents[$key]['quantity'] = 1;
				
				} else {
					
					$product_id = $value['product_id'];
					$product_base_id = $this->get_base_id($product_id);
                    
					if ($product_base_id != null) {
						
						$is_product_base = $lumise->lib->get_product($product_base_id);
						
						if ($is_product_base != null) {
							
							$cms_template = get_post_meta($product_id, 'lumise_design_template', true );
							$product = wc_get_product($product_id);
							$template_price = 0;
							$template_stages = array();
							
							if (
								isset($cms_template) && 
								!empty($cms_template) && 
								$cms_template != '%7B%7D'
							) {
								
								$cms_template = json_decode(urldecode($cms_template), true);
								$templates = array();
								
								foreach ($cms_template as $s => $stage){
									$template_stages[$s] = $stage['id'];
									
									if(!in_array($stage['id'], $templates)){
										$templates[] = $stage['id'];
										$template = $lumise->lib->get_template($stage['id']);
										$template_price += ($template['price'] > 0)? $template['price'] : 0;
									}
								}
								
								$price = $product->get_price();
								$total_price = 0;
								
								if ( version_compare( $woo_ver, '3.0', '<' ) ) {
						            $total_price = $cart_object->cart_contents[$key]['data']->price = $price + $template_price; // Before WC 3.0
						        } else {
						            $cart_object->cart_contents[$key]['data']->set_price( $price + $template_price ); // WC 3.0+
									$total_price = $price + $template_price;
						        }
								
								if(!isset($value['lumise_incart'])){
									//push item to lumise_cart
									$data = array(
										'product_id' => $product_base_id,
										'product_cms' => $product_id,
										'product_name' => $product->get_name(),
										'template' => $lumise->lib->enjson($template_stages),
										'price' => array(
								            'total' => $total_price,
								            'attr' => 0,
								            'printing' => 0,
								            'resource' => 0,
								            'base' => $total_price
								        ),
									);
									
									$item = $lumise->lib->cart_item_from_template($data, null);
									
									if(is_array($item)){
										$item['incart'] = true;
										$lumise->lib->add_item_cart($item);
										$cart_object->cart_contents[$key]['lumise_incart'] = true;
									}
									
								}
								
							}
							
						}
					}
				}
                
            }
            
        }

    }
	
	// Add value custom field to order
    public function woo_add_order_item_meta($item_id, $values, $cart_item_key ) {

        if( isset( $values['lumise_data'] ) )
			wc_add_order_item_meta( $item_id, "lumise_data", $values['lumise_data'] );
    }

    // save data to table order_products
    public function woo_order_finish ($order_id) {

        global $wpdb, $lumise;
		
		$this->load_core();

        $table_name =  $this->prefix."order_products";
        
		$count_order = $wpdb->get_var( " SELECT COUNT( * ) FROM $table_name WHERE order_id = $order_id" );

		$log = 'Lumise Trace Error ID#' . $order_id.' '.date ("Y-m-d H:i:s");

		if ($count_order > 0) {
			$lumise->logger->log( '[FAIL] '.$log.' - order_id exist)');
			header('HTTP/1.1 401 '.'Error: order_id #'.$order_id.' was exist)', true, 401);
			return;
		}
		
		$cart_data = array('items' => array());
		
		foreach( WC()->cart->get_cart() as $cart_item_key => $item ){
			if( 
				isset($item['lumise_data'])
			) { 
				$cart_data['items'][$item['lumise_data']['cart_id']] = $item['lumise_data'];
			}
		}
		
		$cart = $lumise->lib->store_cart($order_id, $cart_data);
		
		if ($cart !== true && $cart['error'] == 1) {
			
			$lumise->logger->log( '[FAIL] '.$log.' - '.$cart['msg']);
			
			wp_delete_post($order_id, true);
			$wpdb->delete( $table_name, array( 'order_id' => $order_id ) );
			
			$msg = $lumise->lang('Sorry, something went wrong when we processed your order. Please contact the administrator')
				   .'.<br><br><em>'.$log.' -  "'.$cart['msg'].'"</em>';
			
			header('HTTP/1.1 401 '.$msg, true, 401);
			exit;
			
		}
		
    }
	
	public function woo_thank_you() {
		echo "<script>localStorage.setItem('LUMISE-CART-DATA', '');</script>";
	}
	
    // Get product have product base
    public function woo_products_assigned() {

        global $wpdb;
        $list_product = array();
        $sql_id_product_design_base = "SELECT meta_value FROM " .  $wpdb->prefix . "postmeta WHERE " . $wpdb->prefix . "postmeta.meta_key = 'lumise_product_base'";

        $list_id_product = $wpdb->get_results( $sql_id_product_design_base, ARRAY_A );


        if( count($list_id_product) > 0 ){
            $list_id_meta_product = array();

            foreach ($list_id_product as $key_meta_product => $meta_product){
                foreach ($meta_product as $key_meta_product_key => $meta_product_value ){
                    if( $meta_product_value == '' || $meta_product_value == '0' || $meta_product_value == 0 ){
                        unset($list_id_product[$key_meta_product]);
                    }else{
                        array_push($list_id_meta_product, $meta_product_value);
                    }
                }
            }

            $list_item_id_product = array_unique($list_id_meta_product);

            $arr_product_ID = implode(',', $list_item_id_product);

            $sql = "
                  SELECT * FROM " . $wpdb->prefix . "posts  INNER JOIN " . $wpdb->prefix . "postmeta
                  ON ( " . $wpdb->prefix . "posts.ID = " . $wpdb->prefix . "postmeta.post_id )
                  WHERE ( " . $wpdb->prefix . "postmeta.meta_key = 'lumise_product_base' AND " . $wpdb->prefix . "postmeta.meta_value IN ($arr_product_ID ))              
                  AND " . $wpdb->prefix . "posts.post_type = 'product' AND (( " .$wpdb->prefix . "posts.post_status = 'publish'))
                  GROUP BY " . $wpdb->prefix . "posts.ID ORDER BY " . $wpdb->prefix . "posts.post_date DESC
              ";

            $list_product = $wpdb->get_results( $sql, ARRAY_A);

        }

        return $list_product;
    }
	
	//get products woo
    public function woo_products() {
        global $wpdb;

        $sql_product = "
                  SELECT " . $wpdb->prefix . "posts.ID, " . $wpdb->prefix . "posts.post_title , ". $wpdb->prefix . "postmeta.meta_value  FROM " . $wpdb->prefix . "posts  INNER JOIN " . $wpdb->prefix . "postmeta
                  ON ( " . $wpdb->prefix . "posts.ID = " . $wpdb->prefix . "postmeta.post_id ) WHERE " . $wpdb->prefix . "postmeta.meta_key = '_regular_price' "
        ;

        $list_product_woocomerce = $wpdb->get_results( $sql_product, ARRAY_A );

        return $list_product_woocomerce ;

    }

	//load core lumise
	public function load_core() {
		
		require_once($this->path . $this->connector_file);
        require_once($this->app_path.'includes'.DS.'main.php');
        
	}
    
    public function get_product() {
        
        global $product;
        
        
        if ($this->product_id != null && function_exists('wc_get_product')) {

            $product = $this->product = wc_get_product($this->product_id);
            
            if ($this->product != null) 
                return $this->product;
            
        }
        return null;
    }
	
	//edti design button in cart page
	public function woo_cart_edit_design_btn ($product_name, $cart_item) {
		
		global $lumise;
		
		if(
			function_exists('is_cart') 
			&& is_cart() 
			&& isset ($cart_item['lumise_data'])
		){
			
			if (!isset($lumise))
				$this->load_core();
				
			$is_query = explode('?', $this->tool_url);
			$cart_id = $cart_item['lumise_data'][ 'cart_id' ];
			$cart_item_data = $lumise->lib->get_cart_data( $cart_item['lumise_data'] );
			
			$url = $this->tool_url.
					((isset($is_query[1]) && !empty($is_query[1]))? '&' : '').
					'product='.$cart_item_data['product_id'].
					'&product_cms='.$cart_item_data['product_cms'].
					'&cart='.$cart_id;
					
			return $product_name . 
					'<div class="lumise-edit-design-wrp">'.
						'<a id="'.$cart_id.'" class="lumise-edit-design button" href="'.$url.'">'.
							__('Edit Design', 'lumise').
						'</a>'.
					'</div>';
		
		} else return $product_name;
		
	}
	
	//change quantity column in cart page
	public function woo_cart_item_quantity($product_quantity, $cart_item_key = null, $cart_item = null) {
		global $lumise;
		
		if( isset($cart_item['lumise_data']) ){
			
			if( !isset($lumise) ) $this->load_core();
			
			$cart_item_data = $lumise->lib->get_cart_data( $cart_item['lumise_data'] );
			
			if( 
				isset($cart_item_data['qtys']) && 
				count($cart_item_data['qtys']) > 0
			){
				
				$product_quantity = array();

				foreach ($cart_item_data['qtys'] as $key => $val) {
					$product_quantity[] = $key .' - '.$val['qty'];
				}
				
				return implode('<br/>', $product_quantity);
				
			}else $product_quantity = $cart_item_data['qty'];
		}
		
		return $product_quantity;
	}
	
	//change quantity column in checkout page
	public function woo_checkout_cart_item_quantity($str, $cart_item, $cart_item_key) {
		global $lumise;
		
		if( !isset($lumise) ) $this->load_core();
		
		$cart_item_data = isset( $cart_item['lumise_data'] ) ? $lumise->lib->get_cart_data( $cart_item['lumise_data'] ) : array();
		
		return isset($cart_item['lumise_data']) ? ' <strong class="product-quantity">' . sprintf( '&times; %s', $cart_item_data['qty'] ) . '</strong>': $str;
		
	}
	
	//change quantity column in order page
	public function woo_order_item_quantity_html( $str, $item ){
		
		global $lumise;
		
		$custom_field = wc_get_order_item_meta( $item->get_id(), 'lumise_data', true );
		if( !isset($lumise) ) $this->load_core();
		
		$cart_item_data = $lumise->lib->get_cart_data( $custom_field );

		if( is_array( $cart_item_data ) 
			&& isset( $cart_item_data[ 'qty' ] ) 
		){
			return ' <strong class="product-quantity">' . sprintf( '&times; %s', $cart_item_data['qty'] ) . '</strong>';
		}
		
		return $str;
		
	}
	
	public function woo_email_order_item_quantity( $qty, $item ) {
		
		$product = $item->get_product();
		if( is_object( $product ) ) {
			
			$product_id = $item->get_product_id();
			$order_id = $item->get_order_id();
			global $lumise;
			
			if( !isset($lumise) ) $this->load_core();
			$items = $lumise->lib->get_order_products( $order_id );
			
			if( count($items) > 0 ):
				foreach ($items as $order_item) {
					 if( $product_id == $order_item['product_id'] ) {
						 return $order_item['qty'];
					 }
				}
			endif;
		}
		
		return $qty;
	}
	
	public function woo_order_item_meta_start( $item_id, $item, $order) {
		unset( $item['lumise_data'] );
	}
		
	public function email_customer_designs( $order, $sent_to_admin = false, $plain_text = false ) {
		
		if ( ! is_a( $order, 'WC_Order' ) || $plain_text) {
			return;
		}
		
		global $lumise, $lumise_printings;
		
		if (!isset($lumise)) 
			$this->load_core();
		
		if (!isset($lumise_printings))
			$lumise_printings = $lumise->lib->get_prints();
		
		if (
			isset($lumise->cfg->settings['email_design']) && 
			$lumise->cfg->settings['email_design'] == 1 
		) {
			
			$order_id 	= $order->get_id();
			
			$order_status = $order->get_status();
			
			if ( 
				$order_status == 'completed' ||
				$sent_to_admin === true
			) {
				
				$items = $lumise->lib->get_order_products($order_id);
					
				if( count($items) > 0 ) :
						
				?>
					<h2><?php echo $lumise->lang("Custom designs");?></h2>
					<div style="margin-bottom: 40px;">
					<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
						<thead>
							<tr>
								<th class="td" scope="col"><?php echo $lumise->lang('Product'); ?></th>
								<th class="td" scope="col"><?php echo $lumise->lang('Quantity'); ?></th>
								<th class="td" scope="col"><?php echo $lumise->lang('Price'); ?></th>
							</tr>
						</thead>
						<tbody>
					<?php
						
						foreach ($items as $item) {
							
							$data = $lumise->lib->dejson($item['data']);
							
							$is_query = explode('?', $lumise->cfg->tool_url);
								
							$url = $lumise->cfg->tool_url.(isset($is_query[1])? '&':'?');
							$url .= 'product='.$item['product_base'];
							$url .= (($item['custom'] == 1)? '&design_print='.str_replace('.lumi', '', $item['design']) : '');
							$url .= '&order_print='.$order_id . '&product_cms='.$item['product_id'];
							$url = str_replace('?&', '?', $url);
							
							$url = apply_filters('lumise_email_customer_download_link', $url);
							
							?>
							<tr class="order_item">
								<td class="td" scope="col">
									<?php echo $item['product_name']; ?>
								</td>
								<td class="td" scope="col">
									<?php echo $item['qty']; ?>
								</td>
								<td class="td" scope="col">
									<?php echo wc_price($item['product_price']); ?>
								</td>
							</tr>
							<?php
								if (isset($data->attributes)) {
									
									foreach ($data->attributes as $i => $attr) {
										if (isset($attr->value)) {
											echo '<tr class="order_item">'.
														'<td class="td" scope="col">'.
														'<span style="font-weight:500;">'.$attr->name.':</span>'.
													 '</td>'.
													 '<td class="td" scope="col" colspan="2">'.
													 	(
															$attr->type == 'color' ?
																'<span style="background:'.$attr->value.
																'; padding: 2px 5px;border-radius: 2px;">'.$attr->value.'</span>' : 
															$attr->value
														).
													'</td>'.
												'</tr>';
										}
									}
									
									if (
										isset($data->variation) && 
										!empty($data->variation)
									) {
										echo '<tr class="order_item">'.
												'<td scope="col" class="td">'.
													'<span style="font-weight:500;">'.
													 $lumise->lang('Variation').
													 ':</span>'.
												 '</td>'.
												 '<td class="td" colspan="2">#'.$data->variation.'</td>'.
											'</tr>';
									}
									
									if (
										isset($data->printing) && 
										!empty($data->printing) && 
										is_array($lumise_printings) &&
										$data->printing !== 0
									) {
										foreach ($lumise_printings as $pmethod) {
											if ($pmethod['id'] == $data->printing) {
												echo '<tr class="order_item">'.
														'<td scope="col" class="td">'.
															'<span style="font-weight:500;">'.
															 $lumise->lang('Printing').
															 ':</span>'.
														 '</td>'.
														 '<td class="td" colspan="2">'.$pmethod['title'].'</td>'.
													'</tr>';
											}
										}
									}
									
								}
									
								if (
									isset($item["screenshots"]) && 
									!empty($item["screenshots"])
								) {
									
									$screenshots = json_decode($item["screenshots"], true);
									
									if (count($screenshots) > 0) {
										
										if (count($screenshots) > 4) {
											$more = count($screenshots)-4;
											array_splice($screenshots, 0, 4);
										} else $more = 0;
										
										echo '<tr class="order_item"><td class="td" scope="col" colspan="3">';
										foreach ($screenshots as $screenshot) {
											echo '<img src="'.($lumise->cfg->upload_url.'orders/'.$screenshot).'" width="80" /> ';
										}
										if ($more > 0) {
											echo $more.'+';	
										}
										echo '</td></tr>';
									}
								}
							?>
							<tr class="order_item">
								<td class="td" scope="col" colspan="3">
									<a href="<?php echo $url; ?>">
										<strong><?php echo $lumise->lang('View in editor'); ?></strong>
									</a>
									&nbsp; | &nbsp; 
									<a href="<?php echo $url; ?>&print_download=png">
										<strong><?php echo $lumise->lang('Download design (PNG)'); ?></strong>
									</a>
									&nbsp; | &nbsp; 
									<a href="<?php echo $url; ?>&print_download=pdf">
										<strong><?php echo $lumise->lang('Download design (PDF)'); ?></strong>
									</a>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<?php
						
				endif;
			
			}
			
		}
		
	}
	
	public function myaccount_customer_designs($order) {
		
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}
		
		global $lumise;
		
		if( !isset($lumise) ) $this->load_core();
		
		if( isset($lumise->cfg->settings['email_design']) && $lumise->cfg->settings['email_design'] == 1 ) {
			
			$order_id 	= $order->get_id();
			
			$order_status = $order->get_status();
			
			if( $order_status == 'completed' ) {
				$items = $lumise->lib->get_order_products($order_id);

				?>
				<h2><?php echo $lumise->lang("Your Designs:");?></h2>
				<div style="margin-bottom: 40px;">
				<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
					<thead>
						<tr>
							<th><?php _e( 'Product', 'woocommerce' ); ?></th>
							<th><?php _e( 'View Design', 'woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
				<?php
				
				foreach ($items as $order_item) {
					$is_query = explode('?', $lumise->cfg->tool_url);
						
					$url = $lumise->cfg->tool_url.(isset($is_query[1])? '&':'?');
					$url .= 'product='.$order_item['product_base'];
					$url .= (($order_item['custom'] == 1)? '&design_print='.str_replace('.lumi', '', $order_item['design']) : '');
					$url .= '&order_print='.$order_id . '&product_cms='.$order_item['product_id'];
								
					$url = str_replace('?&', '?', $url);
					
					$download_url_html = apply_filters( 'lumise_order_download_link', '<a href="' . $url . '" target="_blank" class="lumise-view-design">' . $lumise->lang('View Design') . '</a>', $order_item );
					?>
					<tr class="woocommerce-table__line-item order_item">
						<td class="woocommerce-table__product-name product-name"><?php echo $order_item['product_name'];?></td>
						<td class="woocommerce-table__product-name product-link"><?php echo $download_url_html;?></td>
					</tr>
					<?php
				}

					?>
						</tbody>
					</table>
				</div>
				<?php

			}
		}
		
	}
	
	public function woo_cart_item_removed($cart_key, $cart) {
		
		global $lumise;
		
		if (!isset($lumise))
			$this->load_core();
		
		foreach ($cart->removed_cart_contents as $key => $cart_item){
			if (isset($cart_item['lumise_data'])){
				$lumise->lib->remove_cart_item( $cart_item['lumise_data']['cart_id'], $cart_item['lumise_data'] );
			}
		}
		
	}
	
	//add template thumbnail to product image
	public function woo_add_template_thumbs() {
		
		global $product,  $wpdb, $lumise;
		
		if( !isset($lumise) ) $this->load_core();
		
        $product_id = $product->get_id();

        $product_have_design = $this->has_template($product_id);
		
		if( is_array($product_have_design)){
			$template = $lumise->lib->get_template($product_have_design['meta_value']);
			if(is_array($template)){
				
				$attributes = array(
					'title'                   => $template['name'],
					'data-caption'            => $template['name'],
					'data-src'                => $template['screenshot'],
					'data-large_image'        => $template['screenshot']
				);
				$html  = '<div data-thumb="' . $template['screenshot'] . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $template['screenshot'] ) . '">';
				$html .= '<img src="'.$template['screenshot'].'" '.implode(' ', $attributes).'/>';
				$html .= '</a></div>';
				echo $html;
			}
			
        }
	}
	
	//check product as design?
	public function has_template($product_id) {
		global $wpdb, $lumise;
		
		if( !isset($lumise) ) $this->load_core();
		
		$cms_product = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}posts` WHERE `id`=".$product_id);
		$cms_template = get_post_meta($product_id, 'lumise_design_template', true );
		if (!isset($cms_product[0]))
			return false;
		
		if (isset($cms_template) && !empty($cms_template) && $cms_template != '%7B%7D') {
			return true;
		}
		return false;
	}
	
	public function get_base_id($product_id) {
		global $wpdb;
		
		$sql_design = "
					SELECT pm.* FROM " . $wpdb->prefix . "posts as posts INNER JOIN " . $wpdb->prefix . "postmeta as pm  
				  ON ( pm.post_id = " . $product_id . " AND posts.ID = " . $product_id . ") 
				  WHERE  pm.meta_key = 'lumise_product_base' AND  pm.meta_value > 0
				  AND posts.post_type = 'product' AND  posts.post_status = 'publish'
			  ";
		
		$product_have_design = $wpdb->get_results( $sql_design, ARRAY_A);
		
		if(count($product_have_design)>0)
			return $product_have_design[0]['meta_value'];
		return null;
	}
	
	public function woo_product_get_price_html($price, $product) {
		
		global $wpdb, $lumise;
		
		$this->load_core();
		
		$cms_template = get_post_meta($product->get_id(), 'lumise_design_template', true );
		
		$template_price = 0;
		
		if (
			isset($cms_template) 
			&& !empty($cms_template) 
			&& $cms_template != '%7B%7D'
		) {
			$cms_template = json_decode(urldecode($cms_template), true);
			$templates = array();
			foreach($cms_template as $stage){
				if(!in_array($stage['id'], $templates)){
					$templates[] = $stage['id'];
					$template = $lumise->lib->get_template($stage['id']);
					$template_price += ($template['price'] > 0)? $template['price'] : 0;
				}
			}
			return wc_price( $product->get_price() + $template_price);
		}
		
		return $price;
		
	}
	 
	public function hide_wp_update_notice() {
	   remove_action( 'admin_notices', 'update_nag', 3 );
	} 
	         
	public function wpdocs_dequeue_script() {
		
	    global $wp_scripts;
	    $wp_scripts->queue = array('hoverIntent', 'common', 'admin-bar', 'heartbeat', 'wp-auth-check');
	    
	}

	public function add_display_post_states($post_states, $post){
		
		global $wpdb;
		
		$editor_page = get_option('lumise_editor_page', 0);

		if ( $editor_page == $post->ID ) {
			$post_states['lumise_design_page'] = __( 'Design Editor Page', 'lumise' );
		}
		if($post->post_type == 'product'){
			$product_id = $post->ID;
			$sql_design = "
						SELECT pm.*, pm.meta_value as base_id  FROM " . $wpdb->prefix . "posts as posts INNER JOIN " . $wpdb->prefix . "postmeta as pm  
	                  ON ( pm.post_id = " . $product_id . " AND posts.ID = " . $product_id . ") 
	                  WHERE  pm.meta_key = 'lumise_product_base' AND  pm.meta_value > 0
	                  AND posts.post_type = 'product' AND  posts.post_status = 'publish'
	              ";
	        $product_have_design = $wpdb->get_results( $sql_design, ARRAY_A);
			if(!count($product_have_design)) return $post_states;
			$post_states['lumise_assigned_base'] = __( 'Assigned Lumise Product', 'lumise' ).' #'.$product_have_design[0]['base_id'];
		}
		return $post_states;
	}
	
	public function plugin_action_links( $links ) {
		
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=lumise' ) . '" aria-label="' . esc_attr__( 'Go to Lumise settings', 'woocommerce' ) . '">' . esc_html__( 'Settings', 'lumise' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}
	
	public function plugin_row_meta($links, $file) {
		
		if (LUMISE_PLUGIN_BASENAME == $file) {
			
			$row_meta = array(
				'docs' => '<a href="' . esc_url( 'https://docs.lumise.com/?utm_source=client-site&utm_medium=plugin-meta&utm_campaign=links&utm_term=meta&utm_content=woocommerce' ) . '" target=_blank aria-label="' . esc_attr__( 'View Lumise docs', 'lumise' ) . '">' . esc_html__( 'Documentation', 'lumise' ) . '</a>',
				'blog' => '<a href="' . esc_url( 'https://blog.lumise.com/?utm_source=client-site&utm_medium=plugin-meta&utm_campaign=links&utm_term=meta&utm_content=woocommerce' ) . '" target=_blank aria-label="' . esc_attr__( 'View Lumise docs', 'lumise' ) . '">' . esc_html__( 'Lumise Blog', 'lumise' ) . '</a>',
				'support' => '<a href="' . esc_url( 'https://help.lumise.com/?utm_source=client-site&utm_medium=plugin-meta&utm_campaign=links&utm_term=meta&utm_content=woocommerce' ) . '" target=_blank aria-label="' . esc_attr__( 'Visit premium customer support', 'lumise' ) . '">' . esc_html__( 'Premium support', 'lumise' ) . '</a>'
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
	
	public function submenu_file( $submenu_file ) {
		
		$p = isset($_GET['page']) ? $_GET['page'] : '';
		$lp = isset($_GET['lumise-page']) ? $_GET['lumise-page'] : '';
		
		if ($p == 'lumise' && ($lp == 'addons' || $lp == 'explore-addons')) 
			return 'admin.php?page=lumise&lumise-page=explore-addons';
		
		if ($p == 'lumise' && $lp == 'settings') 
			return 'admin.php?page=lumise&lumise-page=settings';
			
		if ($p == 'lumise' && $lp == 'orders') 
			return 'admin.php?page=lumise&lumise-page=orders';
		
		return $submenu_file;
		
	}
	
	public function update_message($response){
		
		?><script>document.querySelectorAll("#lumise-hook-sfm-update .update-message.notice p")[0].innerHTML = '<?php echo esc_html__('There is a new version of Lumise - Product Designer Tool'); ?>. <a href="https://www.lumise.com/changelogs/woocommerce/?utm_source=client-site&utm_medium=text&utm_campaign=update-page&utm_term=links&utm_content=woocommerce" target=_blank" target=_blank>View version <?php echo $response['new_version']; ?> details</a> or <a href="<?php echo admin_url( 'admin.php?page=lumise&lumise-page=updates' ); ?>">update now</a>.';</script><?php
	}
	
	public function remove_actions() {
		
		global $product, $wpdb, $lumise;
		
		$config = get_option('lumise_config', array());		
		if(
			(isset($config['btn_page']) && !$config['btn_page']) ||
			!method_exists($product, 'get_id')
		) {
			return;
		}
		
		$product_id = $product->get_id();
		$this->load_core();
		
        $sql_custom = "
                  SELECT * FROM " . $wpdb->prefix . "posts as posts  INNER JOIN " . $wpdb->prefix . "postmeta as pm 
                  ON ( pm.post_id = " . $product_id . " AND posts.ID = " . $product_id . ")
                  WHERE ( pm.meta_key = 'lumise_customize' AND  pm.meta_value = 'yes'
                  AND posts.post_type = 'product' AND  posts.post_status = 'publish')
               ";
        $product_have_custom = $wpdb->get_results( $sql_custom, ARRAY_A);
		
		$sql_custom = "
                  SELECT * FROM " . $wpdb->prefix . "posts as posts  INNER JOIN " . $wpdb->prefix . "postmeta as pm 
                  ON ( pm.post_id = " . $product_id . " AND posts.ID = " . $product_id . ")
                  WHERE ( pm.meta_key = 'lumise_disable_add_cart' AND  pm.meta_value = 'yes'
                  AND posts.post_type = 'product' AND  posts.post_status = 'publish')
               ";
        $disable_add_cart = $wpdb->get_results( $sql_custom, ARRAY_A);
		
		if('' === $product->get_price()) $disable_add_cart = 1;

		if( count($product_have_custom) > 0 ){
			if(count($disable_add_cart) > 0){
				remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
				add_action( 'woocommerce_simple_add_to_cart', array($this, 'customize_button'), 30 );
			}else{
				add_action( 'woocommerce_after_add_to_cart_button', array($this, 'customize_button'), 30 );
			}
			
		}
        
	}
	
	public function my_orders_actions($actions, $order) {
		
		global $lumise;
		
		$actions['reorder']   = array(
			'url'  => $lumise->cfg->tool_url.'reorder='.$order->get_id(),
			'name' => __( 'Reorder', 'woocommerce' ),
		);
		
		return $actions;
		
	}
	
	public function customize_button() {
		
		global $product, $wpdb, $lumise;
		
		$config = get_option('lumise_config', array());		
		
		if(
			(isset($config['btn_page']) && !$config['btn_page']) ||
			!method_exists($product, 'get_id')
		) return;
		
		$product_id 	= $product->get_id();
		
		$sql_design = "
			SELECT pm.* FROM " . $wpdb->prefix . "posts as posts INNER JOIN " . $wpdb->prefix . "postmeta as pm  
			ON ( pm.post_id = " . $product_id . " AND posts.ID = " . $product_id . ") 
			WHERE  pm.meta_key = 'lumise_product_base' AND  pm.meta_value > 0
			AND posts.post_type = 'product' AND  posts.post_status = 'publish'
		";
        $product_have_design = $wpdb->get_results( $sql_design, ARRAY_A);
		
		if (!count($product_have_design)) return;
		
		$text 			= isset($config['btn_text'])? $config['btn_text'] : __('Customize', 'lumise');
		$link_design	= str_replace('?&', '?', $this->tool_url . '&product='.$product_have_design[0]['meta_value'].'&product_cms=' . $product_id);
		$product_base 	= $lumise->db->rawQuery("SELECT * FROM `{$lumise->db->prefix}products` WHERE id=" . $product_have_design[0]['meta_value']);
		
		if (count($product_base) > 0){
			do_action( 'lumise_before_customize_button' );
			$class_lumise = apply_filters('lumise_button_customize', 'lumise-button button alt');
			$link_design = apply_filters( 'lumise_customize_link', $link_design );
			echo '<a name="customize" class="'.$class_lumise.'" href="'.esc_url($link_design ).'">'.$text.'</a>';
			do_action( 'lumise_after_customize_button' );
		}
	}
	
	public function shipping_packages($package) {
		
		global $lumise;
		
		if( !isset($lumise) ) $this->load_core();
		
		foreach ( $package[0]['contents'] as $item_id => $values ) {
			if( isset($values['lumise_data']) ){
				$cart_item_data = isset( $values['lumise_data'] ) ? $lumise->lib->get_cart_data( $values['lumise_data'] ) : array();
				$package[0]['contents'][$item_id]['quantity'] = $cart_item_data['qty'];
			}
		}
		
		return $package;
	}

}

global $lumise_woo;

$lumise_woo = new lumise_woocommerce();
