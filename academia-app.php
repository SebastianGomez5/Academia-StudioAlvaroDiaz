<?php
/**
 * Plugin Name: Academia Tectónica - LMS & Laboratorio ALMA
 * Plugin URI:  https://alvarodiaz.com
 * Description: Sistema integral de formación, entregables, laboratorio interactivo Studio, certificación y Panel de Control Docente & Mentoría para la metodología de Álvaro Díaz (Estructurador de Negocios Digitales). Integrado con WooCommerce y FluentBooking.
 * Version:     1.2.0
 * Author:      Álvaro Díaz & Equipo Tectónico
 * Author URI:  https://alvarodiaz.com
 * Text Domain: academia-tectonica
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('ACADEMIA_VERSION', '1.2.0');
define('ACADEMIA_PLUGIN_FILE', __FILE__);
define('ACADEMIA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ACADEMIA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ACADEMIA_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoload / Include required core classes
require_once ACADEMIA_PLUGIN_DIR . 'includes/class-academia-db.php';
require_once ACADEMIA_PLUGIN_DIR . 'includes/class-academia-activator.php';
require_once ACADEMIA_PLUGIN_DIR . 'includes/class-academia-deactivator.php';
require_once ACADEMIA_PLUGIN_DIR . 'includes/class-academia-roles.php';
require_once ACADEMIA_PLUGIN_DIR . 'includes/class-academia-wc.php';
require_once ACADEMIA_PLUGIN_DIR . 'includes/class-academia-ajax.php';
require_once ACADEMIA_PLUGIN_DIR . 'includes/class-academia-shortcodes.php';
require_once ACADEMIA_PLUGIN_DIR . 'includes/class-academia-admin.php';

/**
 * Main Academia Tectonica Plugin Class
 */
final class Academia_Tectonica {

    private static $instance = null;
    public $db;
    public $roles;
    public $wc;
    public $ajax;
    public $shortcodes;
    public $admin;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->db         = new Academia_DB();
        $this->roles      = new Academia_Roles();
        $this->wc         = new Academia_WC();
        $this->ajax       = new Academia_AJAX();
        $this->shortcodes = new Academia_Shortcodes();
        
        if (is_admin()) {
            $this->admin = new Academia_Admin();
        }

        add_action('init', array($this, 'init'));
    }

    public function init() {
        load_plugin_textdomain('academia-tectonica', false, dirname(ACADEMIA_PLUGIN_BASENAME) . '/languages');
    }
}

// Activation hook
register_activation_hook(__FILE__, array('Academia_Activator', 'activate'));

// Deactivation hook
register_deactivation_hook(__FILE__, array('Academia_Deactivator', 'deactivate'));

// Instantiate Plugin
function academia_tectonica() {
    return Academia_Tectonica::get_instance();
}

academia_tectonica();
