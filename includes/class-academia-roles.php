<?php
/**
 * Roles and capabilities management for Academia Tectonica
 */

if (!defined('ABSPATH')) {
    exit;
}

class Academia_Roles {

    public function __construct() {
        add_action('init', array($this, 'setup_roles_and_caps'));
    }

    public function setup_roles_and_caps() {
        // Add Instructor / Mentor Role
        add_role(
            'academia_instructor',
            __('Profesor / Mentor Tectónico', 'academia-tectonica'),
            array(
                'read'                         => true,
                'upload_files'                 => true,
                'academia_review_deliverables' => true,
                'academia_view_students'       => true,
            )
        );

        // Grant capabilities to administrator
        $admin = get_role('administrator');
        if ($admin) {
            $admin->add_cap('academia_manage_all');
            $admin->add_cap('academia_review_deliverables');
            $admin->add_cap('academia_view_students');
        }
    }
}
