<?php

/*
 * Plugin Name: VTC Thuiswedstrijden overzicht
 */

require_once(dirname(__FILE__) . '/thuiswedstrijden-overzicht.php');

function agenda_styles() {
	// When css compression is enabled this will not update
	wp_enqueue_style('matches_overview', plugin_dir_url(__FILE__) . '/thuiswedstrijden-overzicht-style.css');
}

add_action('wp_enqueue_scripts', 'agenda_styles');