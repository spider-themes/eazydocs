<?php
/**
 * Plugin Name: Landpagy Elementor Template Library
 * Plugin URI: https://themeforest.net/user/spider-themes/portfolio
 * Description: This plugin adds the core features template library to the Landpagy WordPress theme. You must have to install this plugin to get all the features included with the landpagy theme.
 * Version: 1.0.0
 * Author: Arif Rahman
 * Author URI: https://themeforest.net/user/droitthemes/portfolio
 * Text domain: landpagy-elementor-template-library
 */


// templates
include( __DIR__ . '/templates/import.php');
include( __DIR__ . '/templates/init.php');
include( __DIR__ . '/templates/load.php');
//include( __DIR__ . '/templates/api.php');

\eazyDocs\Templates\Import::instance()->load();
\eazyDocs\Templates\Load::instance()->load();
\eazyDocs\Templates\Templates::instance()->init();


if (!defined('TEMPLATE_LOGO_SRC')){
	define('TEMPLATE_LOGO_SRC', plugin_dir_url( __FILE__ ) . 'templates/assets/img/template_logo.ico');
}