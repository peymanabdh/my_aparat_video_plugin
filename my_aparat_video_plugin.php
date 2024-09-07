<?php
/*
Plugin Name: Aparat Video Embedder
Description: A plugin to embed Aparat videos using a shortcode.
Version: 1.2
Author: peyman rahmani
url:https://www.linkedin.com/in/peyman-rahmani/
*/

function aparat_video_embed_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'username' => '',
            'limit' => 5,
        ),
        $atts,
        'aparat_video'
    );

    $response = wp_remote_get('https://www.aparat.com/etc/api/videoByUser/username/' . $atts['username'] . '/');
    
    if (is_wp_error($response)) {
        return 'Unable to retrieve video data.';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!isset($data['videobyuser']) || isset($data['error'])) {
        return 'Invalid video data.';
    }

    $videos = $data['videobyuser'];

    $html = '<div class="aparat-videos">';
    
    $count = 0;
    foreach ($videos as $video) {
        if ($count >= $atts['limit']) {
            break;
        }

        $html .= '<div class="aparat-video">';
        $html .= '<h3>' . esc_html($video['title']) . '</h3>';
        $html .= '<iframe src="' . esc_url($video['frame']) . '" width="560" height="315" frameborder="0" allowfullscreen></iframe>';
        $html .= '<p>تاریخ: ' . esc_html($video['sdate']) . '</p>';
        $html .= '</div>';

        $count++;
    }
    
    $html .= '</div>';

    return $html;
}

add_shortcode('aparat_video', 'aparat_video_embed_shortcode');
