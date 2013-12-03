<?php
/**
 * Register Post2Post Relationships
 *
 * @package   My_First_Plugin
 * @author    Brad Vincent <bradvin@gmail.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins
 * @copyright 2013 Brad Vincent
 */

define('P2P_REQUIRED_VERSION', '1.6.2');

if ( !class_exists( 'myfirstplugin_post_relationships' ) ) {

	class myfirstplugin_post_relationships {

		private $_plugin_name;
		private $_plugin_slug;

		function __construct($plugin_name, $plugin_slug) {
			$this->_plugin_name = $plugin_name;
			$this->_plugin_slug = $plugin_slug;

			// show a notice for when Posts 2 Posts plugin is not installed or not correct version
			add_action( 'admin_notices', array($this, 'show_admin_notice') );

			//register P2P connections
			add_action( 'p2p_init', array($this, 'register_connections'), 1 );
		}

		function check_P2P_installed() {
			return (class_exists( 'P2P_Autoload' ));
		}

		function check_P2P_version() {
			if ( self::check_P2P_installed() ) {
				return version_compare( P2P_PLUGIN_VERSION, P2P_REQUIRED_VERSION ) >= 0;
			}

			return false;
		}

		public function register_connections() {

			p2p_register_connection_type( array(
				'name' => 'posts_to_pages',
				'from' => 'post',
				'to'   => 'page'
			) );

			p2p_register_connection_type( array(
				'name'        => 'user_manager',
				'from'        => 'user',
				'to'          => 'user',
				'cardinality' => 'one-to-many',
				'title'       => array(
					'from' => __( 'Managed by', $this->_plugin_slug ),
					'to'   => __( 'Manages', $this->_plugin_slug )
				)
			) );

			p2p_register_connection_type( array(
				'name'        => 'users_to_stuff',
				'from'        => 'user',
				'to'          => array('car', 'house'),
				'title'       => array(
					'from' => __( 'People', $this->_plugin_slug ),
					'to'   => __( 'Items', $this->_plugin_slug )
				),
				'from_labels' => array(
					'singular_name' => __( 'Person', $this->_plugin_slug ),
					'search_items'  => __( 'Search people', $this->_plugin_slug ),
					'not_found'     => __( 'No people found.', $this->_plugin_slug ),
					'create'        => __( 'Create Connections', $this->_plugin_slug ),
				),
				'to_labels'   => array(
					'singular_name' => __( 'Item', $this->_plugin_slug ),
					'search_items'  => __( 'Search items', $this->_plugin_slug ),
					'not_found'     => __( 'No items found.', $this->_plugin_slug ),
					'create'        => __( 'Create Connections', $this->_plugin_slug ),
				),
			) );

			do_action( 'myfirstplugin_registered_connections' );
		}

		function show_admin_notice() {
			$message = false;

			$p2p_name = __( 'Posts 2 Posts', $this->_plugin_slug );

			$url = admin_url( 'plugin-install.php?tab=search&s=posts+2+posts&plugin-search-input=Search+Plugins' );

			$link = sprintf( '<a href="%s">%s</a>', $url, $p2p_name );

			if ( !$this->check_P2P_installed() ) {
				$message = sprintf( __( 'The %s plugin is required for %s to work. Please install the %s plugin now!', $this->_plugin_slug ), $p2p_name, $this->_plugin_name, $link );
			} else if ( !$this->check_P2P_version() ) {
				$message = sprintf( __( 'The %s plugin version is not up to date. %s requires %s version %s in order to function correctly. Please update the %s plugin now!', $this->_plugin_slug ), $p2p_name, $this->_plugin_name, $p2p_name, FOOLIC_P2P_VERSION, $link );
			} else {
				return; //all good - get out!
			}

			if ( $message !== false ) {
				echo '<div class="error"><p>';
				echo '<strong>' . sprintf( __( '%s Notice : ', $this->_plugin_slug ), $this->_plugin_name ) . '</strong>';
				echo $message;
				echo '</p></div>';
			}
		}
	}
}



