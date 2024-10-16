<?php
/*
Plugin Name: Greyhawk Mailchimp Subscribe Form
Description: A simple subscribe form that integrates with Mailchimp.
Version: 1.0
Author: Greyhawk Travel & Tours
*/

function greyhawk_mailchimp_subscribe_form() {
    ?>
    <form id="mailchimp-subscribe-form" method="post">
        <input type="email" name="subscriber_email" placeholder="Enter your email" required>
        <button type="submit">Subscribe</button>
    </form>
    <div id="form-message"></div>
    <?php
}

function greyhawk_handle_subscription() {
    if ( isset($_POST['subscriber_email']) ) {
        $email = sanitize_email($_POST['subscriber_email']);
        // Add logic to connect to Mailchimp API and subscribe user
        $api_key = '4bf5615965fd3d15ee2f0a303c8c67ac-us22';
        $list_id = '158af4ffb4';
        $data_center = substr($api_key,strpos($api_key,'-')+1);
        $url = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/';

        $json = json_encode([
            'email_address' => $email,
            'status'        => 'subscribed', // "subscribed" to add the email to the list
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $api_key);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            echo '<p>Thanks for subscribing!</p>';
        } else {
            echo '<p>There was a problem, please try again.</p>';
        }
    }
}

function greyhawk_enqueue_scripts() {
    wp_enqueue_script('jquery');
}

add_action('wp_enqueue_scripts', 'greyhawk_enqueue_scripts');
add_shortcode('mailchimp_subscribe', 'greyhawk_mailchimp_subscribe_form');
add_action('init', 'greyhawk_handle_subscription');
