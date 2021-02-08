<?php
add_filter('plugins_api', 'misha_plugin_info', 20, 3);
/*
* $res empty at this step
* $action 'plugin_information'
* $args stdClass Object ( [slug] => woocommerce [is_ssl] => [fields] => Array ( [banners] => 1 [reviews] => 1 [downloaded] => [active_installs] => 1 ) [per_page] => 24 [locale] => en_US )
*/

function misha_plugin_info( $res, $action, $args ){

// do nothing if this is not about getting plugin information
if( 'plugin_information' !== $action ) {
return false;
}

$plugin_slug = 'webfixers-tinymce-buttons'; // we are going to use it in many places in this function

// do nothing if it is not our plugin
if( $plugin_slug !== $args->slug ) {
return false;
}

// trying to get from cache first
if( false == $remote = get_transient( 'misha_update_' . $plugin_slug ) ) {

// info.json is the file with the actual plugin information on your server
$remote = wp_remote_get( plugin_dir_url( __FILE__ ) . 'info.json', array(
'timeout' => 10,
'headers' => array(
'Accept' => 'application/json'
) )
);

if ( ! is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && ! empty( $remote['body'] ) ) {
set_transient( 'misha_update_' . $plugin_slug, $remote, 43200 ); // 12 hours cache
}

}

if( ! is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && ! empty( $remote['body'] ) ) {

$remote = json_decode( $remote['body'] );
$res = new stdClass();

$res->name = $remote->name;
$res->slug = $plugin_slug;
$res->version = $remote->version;
$res->download_link = $remote->download_url;
$res->trunk = $remote->download_url;


return $res;

}

return false;

}

add_filter('site_transient_update_plugins', 'misha_push_update' );

function misha_push_update( $transient ){

    if ( empty($transient->checked ) ) {
        return $transient;
    }

    $plugin_slug = 'webfixers-tinymce-buttons'; // we are going to use it in many places in this function

    // trying to get from cache first, to disable cache comment 10,20,21,22,24
    if( false == $remote = get_transient( 'misha_upgrade' . $plugin_slug ) ) {

        // info.json is the file with the actual plugin information on your server
        $remote = wp_remote_get( plugin_dir_url( __FILE__ ) . 'info.json', array(
                'timeout' => 10,
                'headers' => array(
                    'Accept' => 'application/json'
                ) )
        );

        if ( !is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) ) {
            set_transient( 'misha_upgrade' . $plugin_slug, $remote, 43200 ); // 12 hours cache
        }

    }

    if( $remote ) {

        $remote = json_decode( $remote['body'] );

        // your installed plugin version should be on the line below! You can obtain it dynamically of course
        if( $remote && version_compare( '1.0', $remote->version, '<' ) && version_compare($remote->requires, get_bloginfo('version'), '<' ) ) {
            $res = new stdClass();
            $res->slug = $plugin_slug;
            $res->plugin = 'wf-tinymce-buttons/wf-tinymce-buttons.php'; // it could be just YOUR_PLUGIN_SLUG.php if your plugin doesn't have its own directory
            $res->new_version = $remote->version;
            $res->package = $remote->download_url;
            $transient->response[$res->plugin] = $res;
            //$transient->checked[$res->plugin] = $remote->version;
        }

    }
    return $transient;
}

add_action( 'upgrader_process_complete', 'misha_after_update', 10, 2 );

function misha_after_update( $upgrader_object, $options ) {
    if ( $options['action'] == 'update' && $options['type'] === 'plugin' )  {
        // just clean the cache when new plugin version is installed
        delete_transient( 'misha_upgrade_YOUR_PLUGIN_SLUG' );
    }
}