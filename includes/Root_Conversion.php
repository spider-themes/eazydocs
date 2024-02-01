<?php
if ( ! eaz_fs()->is_plan( 'promax' ) ) {
    return;
}

class ezd_remove_docs_base{

	var $ezd_post_selected, $ezd_post_selected_keys;
    
	function __construct(){
	 
		// load user settgins from database
		$this->ezd_post_selected = ['docs' => 1];

		$this->ezd_post_selected_keys = array_keys( $this->ezd_post_selected );
		
		 
		// remove CPT base slug from URLs
		add_filter( 'post_type_link', array( $this, 'remove_slug' ), 10, 3 );
		
		// auto redirect old URLs to non-base versions
		add_action( 'template_redirect', function(){
			global $post;
			if( ! is_preview() && is_single() && is_object( $post ) && isset( $this->ezd_post_selected[ $post->post_type ] ) ){
				$new_url = get_permalink();
				$real_url = $this->get_current_url();
				if( substr_count( $new_url, '/' ) != substr_count( $real_url, '/' ) && strstr( $real_url, $new_url ) == false ){
					remove_filter( 'post_type_link', array( $this, 'remove_slug' ), 10 );
					$old_url = get_permalink();
					add_filter( 'post_type_link', array( $this, 'remove_slug' ), 10, 3 );
					$fixed_url = str_replace( $old_url, $new_url, $real_url );
					wp_redirect( $fixed_url, 301 );
				}
			}
		}, 1 );

		// here the magic was born
		add_filter( 'request', function( $query_vars ){
			// echo '<pre>' . print_r( $query_vars, 1 ) . '</pre>';
			if( ! is_admin() && ! isset( $query_vars['post_type'] ) && ( ( isset( $query_vars['error'] ) && $query_vars['error'] == 404 ) || isset( $query_vars['pagename'] ) || isset( $query_vars['attachment'] ) || isset( $query_vars['name'] ) || isset( $query_vars['category_name'] ) ) ){
				$web_roots = array();
				$web_roots[] = site_url();
				
                if( site_url() != home_url() ){
					$web_roots[] = home_url();
				}

				// polylang fix
				if( function_exists('pll_home_url') ){
					if( site_url() != pll_home_url() ){
						$web_roots[] = pll_home_url();
					}
				}

				foreach( $web_roots as $web_root ){
					// get clean current URL path
					$path = $this->get_current_url();
					$path = str_replace( $web_root, '', $path );
					$path = trim( $path, '/' );

					// clean custom rewrite endpoints
					$path = explode( '/', $path );
					foreach( $path as $i => $path_part ){
						if( isset( $query_vars[ $path_part ] ) ){
							$path = array_slice( $path, 0, $i );
							break;
						}
					}
                    
					$path = implode( '/', $path );

					// test for posts
					$post_data = get_page_by_path( $path, OBJECT, 'post' );
					if( ! ( $post_data instanceof WP_Post ) ){
						// echo '#1<br>';
						// test for pages
						$post_data = get_page_by_path( $path );
						if( ! is_object( $post_data ) ){
							// echo '#2<br>';
							// test for selected CPTs
							$post_data = get_page_by_path( $path, OBJECT, $this->ezd_post_selected_keys );
							if( is_object( $post_data ) ){
								// echo '#3<br>';
								// maybe name with ancestors is needed
								$post_name = $post_data->post_name;
								if( $this->ezd_post_selected[ $post_data->post_type ] == 1 ){
									// echo '#4<br>';
									$ancestors = get_post_ancestors( $post_data->ID );
									foreach( $ancestors as $ancestor ){
										$post_name = get_post_field( 'post_name', $ancestor ) . '/' . $post_name;
									}
								}
								unset( $query_vars['error'] );
								unset( $query_vars['pagename'] );
								unset( $query_vars['attachment'] );
								unset( $query_vars['category_name'] );
								$query_vars['page'] = '';
								$query_vars['name'] = $path;
								$query_vars['post_type'] = $post_data->post_type;
								$query_vars[ $post_data->post_type ] = $path;
								break;
							}else{
								// echo '#5<br>';
								// deeper matching
								global $wp_rewrite;
								// test all selected CPTs
								foreach( $this->ezd_post_selected_keys as $post_type ){
									// get CPT slug and its length
									$query_var = get_post_type_object( $post_type )->query_var;
									// test all rewrite rules
									foreach( $wp_rewrite->rules as $pattern => $rewrite ){
										// test only rules for this CPT
										if( strpos( $pattern, $query_var ) !== false ){
											// echo '#6<br>';
											if( strpos( $pattern, '(' . $query_var . ')' ) === false ){
												// echo '#7<br>';
												preg_match_all( '#' . $pattern . '#', '/' . $query_var . '/' . $path, $matches, PREG_SET_ORDER );
											}else{
												// echo '#8<br>';
												preg_match_all( '#' . $pattern . '#', $query_var . '/' . $path, $matches, PREG_SET_ORDER );
											}

											if( count( $matches ) !== 0 && isset( $matches[0] ) ){
												// echo '#9<br>';
												// build URL query array
												$rewrite = str_replace( 'index.php?', '', $rewrite );
												parse_str( $rewrite, $url_query );
												foreach( $url_query as $key => $value ){
													$value = (int)str_replace( array( '$matches[', ']' ), '', $value );
													if( isset( $matches[0][ $value ] ) ){
														$value = $matches[0][ $value ];
														$url_query[ $key ] = $value;
													}
												}

												// test new path for selected CPTs
												if( isset( $url_query[ $query_var ] ) ){
													// echo '#10<br>';
													$post_data = get_page_by_path( '/' . $url_query[ $query_var ], OBJECT, $this->ezd_post_selected_keys );
													if( is_object( $post_data ) ){
														// echo '#11<br>';
														unset( $query_vars['error'] );
														unset( $query_vars['pagename'] );
														unset( $query_vars['attachment'] );
														unset( $query_vars['category_name'] );
														$query_vars['page'] = '';
														$query_vars['name'] = $path;
														$query_vars['post_type'] = $post_data->post_type;
														$query_vars[ $post_data->post_type ] = $path;
														// solve custom rewrites, pagination, etc.
														foreach( $url_query as $key => $value ){
															if( $key != 'post_type' && substr( $value, 0, 8 ) != '$matches' ){
																$query_vars[ $key ] = $value;
															}
														}
														break 3;
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
				// echo '<pre>' . print_r( $query_vars, 1 ) . '</pre>';
				// exit();
			}
			return $query_vars;
		});
	}
    
	function remove_slug( $permalink, $post, $leavename ){
		global $wp_post_types;
		foreach( $wp_post_types as $type => $custom_post ){
			if( $custom_post->_builtin == false && $type == $post->post_type && isset( $this->ezd_post_selected[ $custom_post->name ] ) ){
				$custom_post->rewrite['slug'] = trim( $custom_post->rewrite['slug'], '/' );
				$permalink = str_replace( '/' . $custom_post->rewrite['slug'] . '/', '/', $permalink );
			}
		}
		return $permalink;
	}

	function get_current_url(){
		$REQUEST_URI = strtok( $_SERVER['REQUEST_URI'], '?' );
		$real_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? 'https://' : 'http://';
		$real_url .= $_SERVER['SERVER_NAME'] . $REQUEST_URI;
		return $real_url;
	}
}

new ezd_remove_docs_base();