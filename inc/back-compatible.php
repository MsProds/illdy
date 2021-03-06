<?php

$theme = wp_get_theme();
if( version_compare( $theme->version, '1.0.17', '>' ) ) {

	$current_logo = get_theme_mod( 'illdy_img_logo', '' );
	$logo = get_custom_logo();
	if ( $current_logo != '' && !$logo ) {
		$logoID = attachment_url_to_postid($current_logo);
		if ( $logoID ) {
			set_theme_mod( 'custom_logo', $logoID );
			remove_theme_mod( 'illdy_img_logo' );
		}
	}

}

// Backward compatibility for sections ordering
if( version_compare( $theme->version, '1.0.36', '>=' ) ) {

	$defaults = array(
				'illdy_panel_about',
				'illdy_panel_projects',
				'illdy_testimonials_general',
				'illdy_panel_services',
				'illdy_latest_news_general',
				'illdy_counter_general',
				'illdy_panel_team',
				'illdy_contact_us',
				'illdy_full_width'
			);

	$old_order = array();
	$new_order = array();

	$old_order[] = get_theme_mod( 'illdy_general_sections_order_first_section');
	$old_order[] = get_theme_mod( 'illdy_general_sections_order_second_section');
	$old_order[] = get_theme_mod( 'illdy_general_sections_order_third_section');
	$old_order[] = get_theme_mod( 'illdy_general_sections_order_fourth_section');
	$old_order[] = get_theme_mod( 'illdy_general_sections_order_fifth_section');
	$old_order[] = get_theme_mod( 'illdy_general_sections_order_sixth_section');
	$old_order[] = get_theme_mod( 'illdy_general_sections_order_seventh_section');
	$old_order[] = get_theme_mod( 'illdy_general_sections_order_eighth_section');

	foreach ($old_order as $key) {
		if ( $key ) {
			$index = $key-1;
			$new_order[$index] = $defaults[$index];
			unset($defaults[$index]);
		}
	}

	if ( !empty($new_order) ) {
		$new_order = array_merge( $new_order, $defaults );
		set_theme_mod( 'illdy_frontpage_sections', $new_order );

		remove_theme_mod( 'illdy_general_sections_order_first_section');
		remove_theme_mod( 'illdy_general_sections_order_second_section');
		remove_theme_mod( 'illdy_general_sections_order_third_section');
		remove_theme_mod( 'illdy_general_sections_order_fourth_section');
		remove_theme_mod( 'illdy_general_sections_order_fifth_section');
		remove_theme_mod( 'illdy_general_sections_order_sixth_section');
		remove_theme_mod( 'illdy_general_sections_order_seventh_section');
		remove_theme_mod( 'illdy_general_sections_order_eighth_section');

	}

	// Backward compatibility for testimonials section
	$illdy_testimonials_update = get_theme_mod( 'illdy_testimonials_update');
	if ( class_exists('Illdy_Widget_Testimonial') && ! $illdy_testimonials_update ) {

		$jetpack_testimonial_query_args = array (
			'post_type'			=> array( 'jetpack-testimonial' ),
			'post_status'		=> 'publish',
			'posts_per_page'	=> -1,
		);

		$jetpack_testimonial_query = new WP_Query( $jetpack_testimonial_query_args );

		if ( $jetpack_testimonial_query->have_posts() ) {
			$sidebars_widgets = get_option( 'sidebars_widgets' );
			$widgets = get_option( 'widget_illdy_testimonial' );

			if ( !empty($widgets) ) {
				$aux_widgets = $widgets;
				if ( isset($aux_widgets['_multiwidget']) ) {
					unset($aux_widgets['_multiwidget']);
				}
				$last_key = key( array_slice( $aux_widgets, -1, 1, TRUE ) );
			}else{
				$last_key = 1;
			}
			$key = intval($last_key) + 1;

			if ( !isset($sidebars_widgets['front-page-testimonials-sidebar']) ) {
				$sidebars_widgets['front-page-testimonials-sidebar'] = array();
			}

			foreach ($jetpack_testimonial_query->posts as $index => $post) {
				
				$url = get_the_post_thumbnail_url( $post->ID, 'illdy-front-page-testimonials' );
				$name = $post->post_title;
				$testimonial = $post->post_content;
				$widgets[$key] = array(
						'name' => $name,
						'image'  => $url,
						'testimonial' => $testimonial
					);
				array_push( $sidebars_widgets['front-page-testimonials-sidebar'], 'illdy_testimonial-'.$key );

				$key = $key+1;
			}

			update_option( 'widget_illdy_testimonial', $widgets );
			update_option( 'sidebars_widgets', $sidebars_widgets );
			set_theme_mod( 'illdy_testimonials_update', true );
			
		}

	}

}
