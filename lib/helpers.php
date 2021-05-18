<?php
/**
 * orgnk_greviews_get_api_url()
 * Returns the Google API URL if an API key is set
 */
function orgnk_greviews_get_api_url() {

    $key = ORGNK_GREVIEWS_API_KEY;

    if ( ! empty( $key ) ) {
        return "https://maps.googleapis.com/maps/api/js?key={$key}";
    }

    return false;
}

//=======================================================================================================================================================

/**
 * orgnk_greviews_get_average_rating()
 */
function orgnk_greviews_get_average_rating( $decimal = true ) {

    $average_rating = null;
    $reviews = orgnk_greviews_get_google_reviews_data();

    if ( isset( $reviews['data']['rating'] ) ) {
        $average_rating = $reviews['data']['rating'];

        if ( $decimal ) {
            $average_rating = number_format( $average_rating, 1 );
        }
    }

    return $average_rating;
}

//=======================================================================================================================================================

/**
 * orgnk_greviews_get_reviews()
 */
function orgnk_greviews_get_reviews() {

    $reviews = orgnk_greviews_get_google_reviews_data();
    $reviews = orgnk_greviews_parse_reviews( $reviews );
    $sort_by = esc_html( get_option( 'options_orgnk_greviews_sort' ) );

    if ( is_array( $reviews ) && ! empty( $reviews ) ) {

        if ( 'rating' == $sort_by ) {
            usort( $reviews, 'orgnk_greviews_sort_by_rating' );
        } elseif ( 'date' == $sort_by ) {
            usort( $reviews, 'orgnk_greviews_sort_by_time' );
        }

        $max_reviews        = 5;
        $reviews_to_show 	= esc_html( get_option( 'options_orgnk_greviews_count' ) );

        $reviews_to_show = ( $reviews_to_show ) ? $reviews_to_show : $max_reviews;

        if ( $max_reviews !== $reviews_to_show ) {
            $display_number = (int) $reviews_to_show;
            $reviews        = array_slice( $reviews, 0, $display_number );
        }
    }

    return $reviews;
}

//=======================================================================================================================================================

/**
 * orgnk_greviews_get_google_reviews_data()
 * Get reviews from Google Place API and store it in a transient
 */
function orgnk_greviews_get_google_reviews_data() {

    $response = array(
        'data'	=> array(),
    );

    $transient_name = 'orgnk_greviews_' . ORGNK_GREVIEWS_PLACE_ID;

    $response['data'] = get_transient( $transient_name );

    if ( empty( $response['data'] ) ) {

        $api_data = orgnk_greviews_get_api_data();

        if ( wp_remote_retrieve_response_code( $api_data ) === 200 ) {

            $data = json_decode( wp_remote_retrieve_body( $api_data ) );

            if ( isset( $data->result ) && isset( $data->result->reviews ) ) {

                $response['data'] = array(
                    'reviews'   => $data->result->reviews,
                    'location'  => array(),
                );

                if ( isset( $data->result->geometry->location ) ) {
                    $response['data']['location'] = $data->result->geometry->location;
                }

                if ( isset( $data->result->rating ) ) {
                    $response['data']['rating'] = $data->result->rating;
                }

                set_transient( $transient_name, $response['data'], 1 * MINUTE_IN_SECONDS );
            }
        }
    }

    return $response;
}

//=======================================================================================================================================================

/**
 * orgnk_greviews_parse_reviews()
 */
function orgnk_greviews_parse_reviews( $reviews ) {

    if ( empty( $reviews['data'] ) ) {
        return;
    }

    $parsed_reviews         = array();
    $data                   = $reviews['data']['reviews'];
    $min_rating             = esc_html( get_option( 'options_orgnk_greviews_min_rating' ) );
    $word_limit             = esc_html( get_option( 'options_orgnk_greviews_word_limit' ) );
    $filter_by_min_rating   = ( $min_rating ) ? true : false;

    foreach ( $data as $review ) {

        $review_meta = array();
        $review_url = explode( '/reviews', $review->author_url );
        $review_url = $review_url[0] . '/place/' . ORGNK_GREVIEWS_PLACE_ID;

        if ( isset( $reviews['data']['location'] ) && ! empty( $reviews['data']['location'] ) ) {
            $location = $reviews['data']['location'];
            $review_url = $review_url . '/' . $location->lat . ',' . $location->lng;
        }

        $review_meta['source']                    = 'google';
        $review_meta['author_name']               = $review->author_name;
        $review_meta['author_url']                = $review->author_url;
        $review_meta['profile_photo_url']         = $review->profile_photo_url;
        $review_meta['rating']                    = $review->rating;
        $review_meta['text']                      = $review->text;
        $review_meta['time']                      = $review->time;
        $review_meta['relative_time']      		  = $review->relative_time_description;
        $review_meta['review_url']                = $review_url;

        if ( $word_limit ) {
            $review_meta['text'] = wp_trim_words( $review_meta['text'], $word_limit, '...' );
        }

        if ( $filter_by_min_rating ) {
            if ( $review->rating >= $min_rating ) {
                $parsed_reviews[] = $review_meta;
            }
        } else {
            $parsed_reviews[] = $review_meta;
        }
    }

    return $parsed_reviews;
}

//=======================================================================================================================================================

function orgnk_greviews_get_api_data() {

    $api_args   = array(
        'method'		=> 'POST',
        'timeout'		=> 60,
        'httpversion'	=> '1.0',
        'sslverify'		=> false,
    );

    $api_key    = ORGNK_GREVIEWS_API_KEY;
    $place_id   = ORGNK_GREVIEWS_PLACE_ID;

    if ( empty( $api_key ) ) {
        return new WP_Error( 'missing_api_key', 'To display Google Reviews, you need to setup an API key.' );
    }

    if ( empty( $place_id ) ) {
        return new WP_Error( 'missing_place_id', 'To display Google Reviews, you need to provide a valid Place ID.' );
    }

    $url = add_query_arg(
        array(
            'key'		=> $api_key,
            'placeid'	=> $place_id,
        ),
        'https://maps.googleapis.com/maps/api/place/details/json'
    );

    $response = wp_remote_post( esc_url_raw( $url ), $api_args );

    return $response;
}

//=======================================================================================================================================================

/**
 * orgnk_greviews_sort_by_rating()
 * Sort reviews by rating
 */
function orgnk_greviews_sort_by_rating( $review_1, $review_2 ) {
    return strcmp( $review_2['rating'], $review_1['rating'] );
}

/**
 * orgnk_greviews_sort_by_time()
 * Sort reviews by time
 */
function orgnk_greviews_sort_by_time( $review_1, $review_2 ) {
    return strcmp( $review_2['time'], $review_1['time'] );
}

//=======================================================================================================================================================

function orgnk_greviews_do_rating_stars( $review = null, $html_icons = true ) {

    $output = null;

    if ( $review ) {
        $rating         = (int) $review['rating'];
        $full_icon      = ( $html_icons ) ? '&#9733;' : '';
        $empty_icon     = ( $html_icons ) ? '&#9734;' : '';

        for ( $stars = 1; $stars <= 5; $stars++ ) {

            if ( $stars <= $rating ) {
                $output .= '<i class="star star-full">' . $full_icon . '</i>';
            } else {
                $output .= '<i class="star star-empty">' . $empty_icon . '</i>';
            }
        }
    }

	return $output;
}

//=======================================================================================================================================================

/**
 * orgnk_greviews_has_reviews()
*/
function orgnk_greviews_has_reviews() {

    $reviews = orgnk_greviews_get_reviews();

    if ( $reviews ) {
        return true;
    } else {
        return false;
    }
}

//=======================================================================================================================================================

/**
 * orgnk_greviews_do_reviews_list()
*/
function orgnk_greviews_do_reviews_list( $display_type = 'list', $slider_navigation = 'dots' ) {
    ob_start();
    include plugin_dir_path( __FILE__ ) . '../public/reviews-list.php';
    return ob_get_clean();
}

//=======================================================================================================================================================

/**
 * orgnk_greviews_do_write_review_button()
 *
 */
function orgnk_greviews_do_write_review_button( $button_type = 'primary-button', $invert = false ) {

	$output = NULL;
	$button = maybe_unserialize( get_option( 'options_orgnk_greviews_write_review_link' ) );
	$color_class = '';

	if ( $invert === true ) {
		$color_class .= ' white';
	}

  	// Check a button exists before proceeding
	if ( $button ) {

		$output .= '<a href="' . esc_url( $button['url'] ) . '" class="' . $button_type . $color_class . '"';

		if ( $button['target'] ) {
			$output .= ' target="_blank" rel="noopener"';
		}

		$output .= '>' . esc_html( $button['title'] ) . '</a>';
	}

	return $output;
}

//=======================================================================================================================================================

/**
 * orgnk_greviews_do_view_reviews_button()
 *
 */
function orgnk_greviews_do_view_reviews_button( $button_type = 'primary-button', $invert = false ) {

	$output = NULL;
	$button = maybe_unserialize( get_option( 'options_orgnk_greviews_view_reviews_link' ) );
	$color_class = '';

	if ( $invert === true ) {
		$color_class .= ' white';
	}

  	// Check a button exists before proceeding
	if ( $button ) {

		$output .= '<a href="' . esc_url( $button['url'] ) . '" class="' . $button_type . $color_class . '"';

		if ( $button['target'] ) {
			$output .= ' target="_blank" rel="noopener"';
		}

		$output .= '>' . esc_html( $button['title'] ) . '</a>';
	}

	return $output;
}
