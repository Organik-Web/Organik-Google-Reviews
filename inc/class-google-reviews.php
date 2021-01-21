<?php
/**
 * Define constant variables
 */
// define( 'ORGNK_GREVIEWS_API_KEY', get_option( 'options_google_api_key' ) );
define( 'ORGNK_GREVIEWS_API_KEY', 111 );

// define( 'ORGNK_GREVIEWS_PLACE_ID', get_option( 'options_google_place_id' ) );
define( 'ORGNK_GREVIEWS_PLACE_ID', 222 );

/**
 * Main Organik_Services class
 */
class Organik_Services {

	/**
     * The single instance of Organik_Services
     */
	private static $instance = null;

	/**
     * Main class instance
     * Ensures only one instance of this class is loaded or can be loaded
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self;
        }
        return self::$instance;
	}
	
	/**
     * Constructor function
     */
	public function __construct() {

	}

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
	
	

	/** 
	 * orgnk_greviews_get_reviews()
	 * 
	 */
	public function orgnk_greviews_get_reviews() {

		$reviews = array();
		$reviews = $this->orgnk_greviews_get_google_reviews_data();
		$reviews = $this->orgnk_greviews_parse_reviews( $reviews );

		if ( is_array( $reviews ) && ! empty( $reviews ) ) {

			if ( 'rating' == $this->settings->reviews_filter_by ) {
				usort( $reviews, array( $this, 'sort_by_rating' ) );
			} elseif ( 'date' == $this->settings->reviews_filter_by ) {
				usort( $reviews, array( $this, 'sort_by_time' ) );
			}

			$max_reviews        = 5;
			$reviews_to_show 	= get_option( 'options_orgnk_greviews_count' );

			$reviews_to_show = ( '' !== $reviews_to_show ) ? $reviews_to_show : $max_reviews;

			if ( $max_reviews !== $reviews_to_show ) {
				$display_number = (int) $reviews_to_show;
				$reviews        = array_slice( $reviews, 0, $display_number );
			}
		}

		return $reviews;
	}

	





	/**
	 * orgnk_greviews_get_google_reviews_data()
	 * Get reviews from Google Place API and store it in a transient
	 */
	public function orgnk_greviews_get_google_reviews_data() {

		$response = array(
			'data'	=> array(),
			'error' => false,
		);

		$transient_name = 'orgnk_greviews_' . ORGNK_GREVIEWS_PLACE_ID;

		$response['data'] = get_transient( $transient_name );
		
		if ( empty( $response['data'] ) ) {

			$api_data = $this->orgnk_greviews_get_api_data();

			if ( is_wp_error( $api_data ) ) {
				
				$response['error'] = $api_data;

			} else {

				if ( wp_remote_retrieve_response_code( $api_data ) === 200 ) {

					$data = json_decode( wp_remote_retrieve_body( $api_data ) );
					
					if ( 'OK' !== $data->status ) {

						$response['error'] = isset( $data->error_message ) ? $data->error_message : 'No reviews found.';

					} else {

						if ( isset( $data->result ) && isset( $data->result->reviews ) ) {

							$response['data'] = array(
								'reviews' => $data->result->reviews,
								'location' => array(),
							);

							if ( isset( $data->result->geometry->location ) ) {
								$response['data']['location'] = $data->result->geometry->location;
							}

							set_transient( $transient_name, $response['data'], 24 * MINUTE_IN_SECONDS );

							$response['error'] = false;

						} else {
							$response['error'] = 'This place doesn\'t have any reviews.';
						}
					}
				}
			}	
		}

		return $response;
	}





/**
	 * orgnk_greviews_parse_reviews()
	 * Change the number of 'posts per page' for the CPT archive
	 */
	public function orgnk_greviews_parse_reviews( $reviews ) {

		if ( is_wp_error( $reviews['error'] ) ) {
			return $reviews['error'];
		}

		if ( empty( $reviews['data'] ) ) {
			return;
		}

		$parsed_reviews = array();
		$filter_by_min_rating = false;

		$data = $reviews['data']['reviews'];

		if ( '' !== $this->settings->reviews_min_rating ) {
			$filter_by_min_rating = true;
		}

		foreach ( $data as $review ) {

			$_review = array();
			$review_url = explode( '/reviews', $review->author_url );
			$review_url = $review_url[0] . '/place/' . ORGNK_GREVIEWS_PLACE_ID;

			if ( isset( $reviews['data']['location'] ) && ! empty( $reviews['data']['location'] ) ) {
				$location = $reviews['data']['location'];
				$review_url = $review_url . '/' . $location->lat . ',' . $location->lng;
			}

			$_review['source']                    = 'google';
			$_review['author_name']               = $review->author_name;
			$_review['author_url']                = $review->author_url;
			$_review['profile_photo_url']         = $review->profile_photo_url;
			$_review['rating']                    = $review->rating;
			$_review['relative_time_description'] = $review->relative_time_description;
			$_review['text']                      = $review->text;
			$_review['time']                      = $review->time;
			$_review['title']              		  = $review->relative_time_description;
			$_review['review_url']                = $review_url;

			if ( $filter_by_min_rating ) {
				if ( $review->rating >= $this->settings->reviews_min_rating ) {
					$parsed_reviews[] = $_review;
				}
			} else {
				$parsed_reviews[] = $_review;
			}
		}

		return $parsed_reviews;
	}







	public function orgnk_greviews_get_api_data() {

		$api_args = array(
			'method'		=> 'POST',
			'timeout'		=> 60,
			'httpversion'	=> '1.0',
			'sslverify'		=> false,
		);

		$api_key = ORGNK_GREVIEWS_API_KEY;
		$place_id = ORGNK_GREVIEWS_PLACE_ID;

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

		$response = wp_remote_post(
			esc_url_raw( $url ),
			$api_args
		);

		if ( ! is_wp_error( $response ) ) {

			$body = json_decode( wp_remote_retrieve_body( $response ) );

			if ( isset( $body->error_message ) && ! empty( $body->error_message ) ) {
				$status = isset( $body->status ) ? $body->status : $source . '_api_error';
				return new WP_Error( $status, $body->error_message );
			}
		}

		return $response;
	}






	/**
	 * sort_by_rating()
	 * Sort reviews by rating
	 */
	private function sort_by_rating( $review_1, $review_2 ) {
		return strcmp( $review_2['rating'], $review_1['rating'] );
	}

	/**
	 * sort_by_time()
	 * Sort reviews by time
	 */
	private function sort_by_time( $review_1, $review_2 ) {
		return strcmp( $review_2['time'], $review_1['time'] );
	}
}
