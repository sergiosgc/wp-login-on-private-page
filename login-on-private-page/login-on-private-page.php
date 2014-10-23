<?php
/**
 * Plugin Name: Login on private page
 * Plugin URI:
 * Description: Private pages on WordPress return 404 (HTTP not found) errors. This plugin changes the behaviour so that they redirect to the login page. It does not change the hidden behaviour (pages and posts still do not appear in lists and menus)
 * Version: 1.0
 * Author: Sérgio Carvalho
 * Author URI: https://github.com/sergiosgc/
 * License: GPLv2
 */
class LoginOnPrivatePage {
    public static function activate() {
        static $callOnceGuard = false;
        if ($callOnceGuard) return;
        $callOnceGuard = true;
        new LoginOnPrivatePage();
    }
    public function __construct() {
        add_filter('posts_results', array($this, 'posts_results'), 10, 2);
        add_action('login_form_privatecontent', array($this, 'login_form'));
        add_filter('login_message', array($this, 'login_message'), 10, 1);
        $this->message = '';
    }
    public function posts_results($posts, $query) {
        if (!is_singular()) return $posts;
        if (count($posts) < 1) return $posts;
        if ($posts[0]->post_status != 'private') return $posts;
        if (!current_user_can('read_post', $posts[0]->ID)) {
            wp_redirect( site_url( '/wp-login.php?action=privatecontent' ) );
            die();
        }
        return $posts;
    }
    public function login_form() {
        $this->message = __('The content you tried to access is protected and accessible only to authorized users. Please login using an account with the necessary permissions.');
    }
    public function login_message($message) {
        if ($this->message == '') return $message;
        return $message . '<div style="max-width: 60%; margin: 0 auto" class="private-content-message">' . $this->message . "</div>";
    }
}
LoginOnPrivatePage::activate();
