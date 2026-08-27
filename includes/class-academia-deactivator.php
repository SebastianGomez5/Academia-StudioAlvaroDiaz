<?php
/**
 * Fired during plugin deactivation
 */

if (!defined('ABSPATH')) {
    exit;
}

class Academia_Deactivator {

    public static function deactivate() {
        // Flush rewrite rules if custom post types or endpoints were registered
        flush_rewrite_rules();
    }
}
