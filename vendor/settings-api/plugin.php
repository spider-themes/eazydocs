<?php
/**
 * Plugin Name: WordPress Settings API
 * Plugin URI: #
 * Description: WordPress Settings API testing
 * Author: SpiderDevs
 * Author URI: spider-themes.net
 * Version: 1.0
 */

require_once dirname( __FILE__ ) . '/src/class.settings-api.php';
require_once dirname( __FILE__ ) . '/fields/settings-fields.php';

new SpiderDevs_Settings_API_Test();