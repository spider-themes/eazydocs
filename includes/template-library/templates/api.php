<?php
namespace EazyDocs\Templates;

use \Elementor\TemplateLibrary\Source_Base;
use \Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class Api extends Source_Base {
	
	const LIBRARY_OPTION_KEY = 'eazydocs_library_info';

	const LIBRARY_TIMESTAMP_CACHE_KEY = 'eazydocs_remote_update_timestamp';
	const API_INFO_URL = 'https://wordpress-theme.spider-themes.net/elementor-template-library/eazydocs/';

	public function get_id() {
		return 'template_library';
	}

	public function get_title() {
		return esc_html__( 'Eazydocs Library', 'eazydocs' );
	}

	public function register_data() {}

	public function get_items( $args = [] ) {
		$library_data = self::get_library_data();
		$templates = [];
		if ( ! empty( $library_data['templates'] ) ) {
			foreach ( $library_data['templates'] as $template_data ) {
				$templates[] = $this->prepare_template( $template_data );
			}
		}
		return $templates;
	}

	public function get_tags() {
		$library_data = self::get_library_data();
		return ( ! empty( $library_data['tags'] ) ? $library_data['tags'] : [] );
	}

	public function get_type_tags() {
		$library_data = self::get_library_data();
		return ( ! empty( $library_data['type_tags'] ) ? $library_data['type_tags'] : [] );
	}

	
	private function prepare_template( array $template_data ) {
		return [
			'template_id' => $template_data['template_id'],
			'title'       => $template_data['title'],
			'type'        => $template_data['type'],
			'thumbnail'   => $template_data['thumbnail'],
			'date'        => $template_data['modified'],
			'tags'        => $template_data['tags'],
			'url'         => $template_data['preview'],
			'liveurl'     => $template_data['liveurl'],
			'favorite' 	  => ! empty( $template_data['template_id'] ),
		];
	}

	
	private static function request_library_data( $force_update = false ) {
		$data = get_option( self::LIBRARY_OPTION_KEY );
		
		$elementor_update_timestamp = get_option( '_transient_timeout_elementor_remote_info_api_data_' . ELEMENTOR_VERSION );
		$update_timestamp = get_transient( self::LIBRARY_TIMESTAMP_CACHE_KEY );

		if ( $force_update || false === $data || ! $update_timestamp || $update_timestamp != $elementor_update_timestamp ) {
			$timeout = ( $force_update ) ? 25 : 8;

			$apiUrl = self::API_INFO_URL .'?' . http_build_query([
				'action' => 'get_layouts',
				'tab' => ''
			]);
			$response = wp_remote_get( $apiUrl, [
				'timeout' => $timeout,
			] );

			if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
					update_option( self::LIBRARY_OPTION_KEY, [] );
					return false;
			}

			$data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( empty( $data ) || ! is_array( $data ) ) {
				update_option( self::LIBRARY_OPTION_KEY, [] );
				set_transient( self::LIBRARY_TIMESTAMP_CACHE_KEY, [], 2 * HOUR_IN_SECONDS );
				return false;
			}
			
			//Update when reload
			update_option( self::LIBRARY_OPTION_KEY, $data, 'yes' );
		}
		return $data;
	}

	public static function get_library_data( $force_update = false ) {
		self::request_library_data( $force_update );
		$library_data = get_option( self::LIBRARY_OPTION_KEY );
		if ( empty( $library_data ) ) {
			return [];
		}
		return $library_data;
	}

	public function get_item( $template_id ) {
		$templates = $this->get_items();

		return $templates[ $template_id ];
	}
	
	public function save_item( $template_data ) {
		return new \WP_Error( 'invalid_request', 'Cannot save template to a droit elementor addons library' );
	}
	
	public function update_item( $new_data ) {
		return new \WP_Error( 'invalid_request', 'Cannot update template to a droit elementor addons library' );
	}

	
	public function delete_template( $template_id ) {
		return new \WP_Error( 'invalid_request', 'Cannot delete template from a droit elementor addons library' );
	}

	public function export_template( $template_id ) {
		return new \WP_Error( 'invalid_request', 'Cannot export template from a droit elementor addons library' );
	}
	
	public static function request_template_data( $template_id ) {
		if ( empty( $template_id ) ) {
			return $template_id;
		}

		$body = [
			'site_lang' => get_bloginfo( 'language' ),
			'home_url' => trailingslashit( home_url() ),
			'template_version' => '1.0.0',
		];

		$body_args = apply_filters( 'elementor/api/get_templates/body_args', $body );

		$apiUrl = self::API_INFO_URL .'?' . http_build_query([
			'action' => 'get_layout_data',
			'id' => $template_id,
		]);

		$response = wp_remote_get(
			$apiUrl,
			[
				'body' => $body_args,
				'timeout' => 10
			]
		);

		return wp_remote_retrieve_body( $response );
	}

	public function get_data( array $args, $context = 'display' ) {
		$data = self::request_template_data( $args['template_id'] );
		
		$data = json_decode( $data, true );
		if ( empty( $data ) || empty( $data['content'] ) ) {
			throw new \Exception( __( 'Template does not have any content', 'eazydocs' ) );
		}

		$data['content'] = $this->replace_elements_ids( $data['content'] );
		$data['content'] = $this->process_export_import_content( $data['content'], 'on_import' );

		$post_id = $args['editor_post_id'];
		$document = Plugin::$instance->documents->get( $post_id );
		if ( $document ) {
			$data['content'] = $document->get_elements_raw_data( $data['content'], true );
		}
		return $data;
	}

}
