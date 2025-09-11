<?php
/*
Plugin Name: Slider Revolution Particles Effect
Plugin URI: http://www.themepunch.com/
Description: Add interactive particle animations to your sliders
Author: ThemePunch
Version: 2.1.0.1
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:

	'rs-particles-admin'
	'rs-particles-front'

*/

// $this->_frameworkHelper->delete_option('revslider_addon_particles_templates');

// If this file is called directly, abort.
if ( ! defined('\Nwdthemes\Revslider\Helper\Framework::WPINC')) die();

define('RS_PARTICLES_PLUGIN_PATH', $this->plugin_dir_path(__FILE__));
define('RS_PARTICLES_PLUGIN_URL', str_replace('index.php', '', $this->plugins_url( 'index.php', __FILE__)));

require_once(RS_PARTICLES_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_particles_init(\Nwdthemes\Revslider\Helper\Framework $frameworkHelper){

	new RsParticlesBase($frameworkHelper);

}

/**
* call all needed functions on plugins loaded *
**/
$this->add_action('plugins_loaded', 'rs_particles_init');
$this->register_activation_hook( __FILE__, 'rs_particles_init');

//build js global var for activation
$this->add_filter( 'revslider_activate_addon', array('RsAddOnParticlesBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
$this->add_filter( 'revslider_help_directory', array('RsAddOnParticlesBase','get_help'),10,1);
