<?php
/*
 * Plugin name: Webfixers TinyMCE Buttons
 */

require_once plugin_dir_path( __FILE__ ) . 'classes/WebfixersTinyMCEButtons.php';
require_once plugin_dir_path( __FILE__ ) . 'auto_update/auto_update.php';

$addButton = new WebfixersTinyMCEButtons;
$addButton->init();