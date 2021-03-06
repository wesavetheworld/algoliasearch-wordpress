<?php

class Algolia_Compatibility {

    private $current_language;

	public function __construct() {
		add_action( 'algolia_before_handle_task', array( $this, 'register_vc_shortcodes' ) );
		add_action( 'algolia_before_handle_task', array( $this, 'enable_yoast_frontend' ) );
		add_action( 'algolia_before_get_records', array( $this, 'wpml_switch_language' ) );
		add_action( 'algolia_after_get_records', array( $this, 'wpml_switch_back_language' ) );
	}

	/**
	 * @param Algolia_Task $task
	 */
	public function enable_yoast_frontend( Algolia_Task $task ) {
		if ( class_exists( 'WPSEO_Frontend' ) && method_exists( 'WPSEO_Frontend', 'get_instance' ) ) {
			WPSEO_Frontend::get_instance();
		}
	}

	/**
	 * @param Algolia_Task $task
	 */
	public function register_vc_shortcodes( Algolia_Task $task ) {
		if ( class_exists( 'WPBMap' ) && method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
			WPBMap::addAllMappedShortcodes();
		}
	}

	public function wpml_switch_language( $post ) {
	    if ( ! $post instanceof WP_Post || ! $this->is_wpml_enabled() ) {
            return;
        }

        global $sitepress;
        $langInfo = wpml_get_language_information( null, $post->ID );
        $this->current_language = $sitepress->get_current_language();
        $sitepress->switch_lang( $langInfo['language_code'] );
    }

    public function wpml_switch_back_language( $post ) {
        if ( ! $post instanceof WP_Post || ! $this->is_wpml_enabled() ) {
            return;
        }

        global $sitepress;

        $sitepress->switch_lang($this->current_language);
    }

    /**
     * @return bool
     */
    private function is_wpml_enabled()
    {
        return function_exists( 'icl_object_id' );
    }
}
