<?php

class WebfixersTinyMCEButtons 
{
    public function init()
    {
        add_filter( 'mce_buttons', array($this, 'add_button') );
        add_filter( 'mce_external_plugins', array($this, 'register_script') );
        add_action( 'admin_init', array($this, 'add_plugin_style') );
    }

    public function add_button( $buttons )
    {
        $buttons[] ='wf_highlight';
        $buttons[] = 'wf_btn';
        
        return $buttons;
    }

    public function register_script( $plugins ) 
    {
        $plugins['wf_buttons'] = plugins_url( 'wf-tinymce-buttons' ) . '/assets/js/wf_buttons.js';
    
        return $plugins;
    }

    public function add_plugin_style()
    {
        add_editor_style( plugins_url( 'wf-tinymce-buttons' ) . '/assets/css/wf_buttons.css' );
    }
}