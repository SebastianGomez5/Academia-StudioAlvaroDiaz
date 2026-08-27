<?php
/**
 * WooCommerce Integration for Academia Tectonica
 */

if (!defined('ABSPATH')) {
    exit;
}

class Academia_WC {

    public function __construct() {
        // Automatically enroll student when WooCommerce order is completed or processing
        add_action('woocommerce_order_status_completed', array($this, 'handle_order_enrollment'));
        add_action('woocommerce_order_status_processing', array($this, 'handle_order_enrollment'));
    }

    /**
     * Process order items and enroll customer in corresponding courses
     */
    public function handle_order_enrollment($order_id) {
        if (!function_exists('wc_get_order')) {
            return;
        }

        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }

        $user_id = $order->get_user_id();
        if (!$user_id) {
            // Guest checkout: try to find user by billing email
            $billing_email = $order->get_billing_email();
            if ($billing_email) {
                $user = get_user_by('email', $billing_email);
                if ($user) {
                    $user_id = $user->ID;
                }
            }
        }

        if (!$user_id) {
            return;
        }

        global $wpdb;
        $courses_table = Academia_DB::get_table('courses');
        $db = new Academia_DB();

        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            $variation_id = $item->get_variation_id();

            // Find course matching product ID
            $courses = $wpdb->get_results($wpdb->prepare("
                SELECT id FROM {$courses_table} 
                WHERE wc_product_id = %d OR wc_product_id = %d
            ", $product_id, $variation_id), ARRAY_A);

            if (!empty($courses)) {
                foreach ($courses as $c) {
                    $db->enroll_user($user_id, $c['id'], $order_id);
                }
            }
        }
    }

    /**
     * Get checkout / product URL for a course
     */
    public static function get_course_purchase_url($wc_product_id) {
        if (function_exists('wc_get_checkout_url') && $wc_product_id) {
            return add_query_arg('add-to-cart', $wc_product_id, wc_get_checkout_url());
        }
        if ($wc_product_id && function_exists('get_permalink')) {
            return get_permalink($wc_product_id);
        }
        return '#';
    }
}
