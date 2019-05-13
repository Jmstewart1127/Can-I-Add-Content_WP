<?php
/*
Plugin Name: Can I Add Content?
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Green means go, Red means don't.
Version: 0.1
Author: Jake Stewart
Author URI: https://www.makedigitalgroup.com
License: A "Slug" license name e.g. GPL2
*/


function get_site_status() {
	$url = 'http://can-i-add-content.herokuapp.com/api/project/get/status/' . $_SERVER['SERVER_NAME'];
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function read_result() {
	$result = get_site_status();
	return json_decode($result)[0];
}

function select_dot_color() {
	$result = read_result();
	if ($result->live_url === $_SERVER['SERVER_NAME']) {
		if ($result->add_content_to_prod === 0) {
			return 'red';
		} elseif ($result->add_content_to_prod === 1) {
			return '#adff2f';
		}
	}
	if ($result->staging_url === $_SERVER['SERVER_NAME']) {
		if ($result->add_content_to_stage == 0) {
			return 'red';
		} elseif ($result->add_content_to_stage == 1) {
			return '#adff2f';
		}
	}
}

function admin_bar_template() {
	$template = '<style>
                    #can-i-add-content-list-item {
                        height: 32px;
                        margin: 0 5px;
                        position: relative;
                    }
                    .can-i-add-content-dot { 
                        display: block; 
                        background: '. select_dot_color() .';
                        border-radius: 50% !important;
                        height: 10px !important;
                        width: 10px !important;  
                        position: absolute !important; 
                        left: 10px;
                        top: 50%;
                        transform: translate(0, -50%);               
                    }
                    .ab-empty-item {
                    	background: transparent !important;                   
                    }
                </style>';
	$template .= '<li>Can I Add Content? </li>';
	$template .= '<li id="can-i-add-content-list-item">';
	$template .= '<div class="can-i-add-content-dot"></div>';
	$template .= '</li>';
	return $template;
}

add_action( 'admin_bar_menu', 'modify_admin_bar', 100 );
function modify_admin_bar( $wp_admin_bar ) {
	$args = array(
		'id'    => 'can_i_add_content',
		'meta'  => array(
			'class' => 'can-i-add-content',
			'html'  => admin_bar_template(),
		),
	);
	$wp_admin_bar->add_node( $args );
}