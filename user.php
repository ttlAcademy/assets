   
<?php
    
// wp-content/plugins/masterstudy-lms-learning-management-system/lms/classes/user.php

    /*If password is equal*/
        if ($user_password !== $user_password_re) {
            $r['message'] = esc_html__('Passwords do not match', 'masterstudy-lms-learning-management-system');
            wp_send_json($r);
            die;

        /* If Password shorter than 8 characters*/
        } else if (strlen($user_password) < 8) {
            $r['message'] = esc_html__('Password must have at least 8 characters', 'masterstudy-lms-learning-management-system');
            wp_send_json($r);
            die;

        /* if Password longer than 20 */
        } else if (strlen($user_password) > 20) {
            $r['message'] = esc_html__('Password too long', 'masterstudy-lms-learning-management-system');
            wp_send_json($r);
            die;

        /* if contains letter */
        } else if (!preg_match("#[a-z]+#", $user_password)) {
            $r['message'] = esc_html__('Password must include at least one letter!', 'masterstudy-lms-learning-management-system');
            wp_send_json($r);
            die;

        /* if contains number */
        } else if (!preg_match("#[0-9]+#", $user_password)) {
              $r['message'] = esc_html__('Password must include at least one number!', 'masterstudy-lms-learning-management-system');
              wp_send_json($r);
              die;

        /* if contains CAPS */
        } else if (!preg_match("#[A-Z]+#", $user_password)) {
              $r['message'] = esc_html__('Password must include at least one capital letter!', 'masterstudy-lms-learning-management-system');
              wp_send_json($r);
              die;

        }
