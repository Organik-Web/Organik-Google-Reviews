<?php
/**
 * Main Organik_Google_Reviews_ACF_Fields class
 */
class Organik_Google_Reviews_ACF_Fields {

	/**
     * Constructor function
     */
	public function __construct() {

		// Hook into the 'init' action to add the ACF fields
		add_filter( 'init', array( $this, 'orgnk_greviews_acf_fields' ) );
	}

	/**
	 * orgnk_greviews_acf_fields()
	 * Manually insert ACF fields
	 */
	public function orgnk_greviews_acf_fields() {

		// Return early if ACF isn't active
		if ( ! class_exists( 'ACF' ) || ! function_exists( 'acf_add_local_field_group' ) || ! is_admin() ) return;

		// Field Group - Google Review Settings
		acf_add_local_field_group( array(
			'key'				=> 'group_6009103c913a3',
			'title'				=> 'Google Reviews Settings',
			'fields'			=> array(

				// Field - API Settings - Accordion
				array(
					'key' 				=> 'field_600927d6af8e1',
					'label' 			=> 'API Settings',
					'name' 				=> '',
					'type' 				=> 'accordion',
					'instructions' 		=> '',
					'required' 			=> 0,
					'conditional_logic'	=> 0,
					'wrapper'			=> array(
						'width'				=> '',
						'class'				=> '',
						'id'				=> '',
					),
					'open'				=> 0,
					'multi_expand'		=> 0,
					'endpoint'			=> 0,
				),

				// Field - Google API Key - Text
				array(
					'key'				=> 'field_60092806af8e4',
					'label'				=> 'Google API Key',
					'name'				=> 'orgnk_greviews_api_key',
					'type'				=> 'text',
					'instructions'		=> '',
					'required'			=> 0,
					'conditional_logic'	=> 0,
					'wrapper'			=> array(
						'width'				=> '',
						'class'				=> '',
						'id'				=> '',
					),
					'default_value'		=> '',
					'placeholder'		=> '',
					'prepend'			=> '',
					'append'			=> '',
					'maxlength'			=> '',
				),

				// Field - Google Place ID - Text
				array(
					'key'				=> 'field_600928cc77b71',
					'label'				=> 'Google Place ID',
					'name'				=> 'orgnk_greviews_place_id',
					'type'				=> 'text',
					'instructions'		=> '',
					'required'			=> 0,
					'conditional_logic'	=> 0,
					'wrapper'			=> array(
						'width'				=> '',
						'class'				=> '',
						'id'				=> '',
					),
					'default_value'		=> '',
					'placeholder'		=> '',
					'prepend'			=> '',
					'append'			=> '',
					'maxlength'			=> '',
				),

				// Field - Display Settings - Accordion
				array(
					'key'				=> 'field_600927edaf8e2',
					'label'				=> 'Display Settings',
					'name'				=> '',
					'type'				=> 'accordion',
					'instructions'		=> '',
					'required'			=> 0,
					'conditional_logic' => 0,
					'wrapper'			=> array(
						'width'				=> '',
						'class'				=> '',
						'id'				=> '',
					),
					'open'				=> 0,
					'multi_expand'		=> 0,
					'endpoint'			=> 0,
				),

				// Field - Reviews To Show - Number
				array(
					'key'				=> 'field_60091101ec043',
					'label'				=> 'Reviews To Show',
					'name'				=> 'orgnk_greviews_count',
					'type'				=> 'number',
					'instructions' 		=> 'The Google Reviews API delivers 5 \'most relevant\' reviews by default. This option allows you to further limit how many are displayed.<br><br><strong>Note: </strong>this setting may be ignored depending on the \'Minimum Rating\' setting set below.',
					'required' 			=> 0,
					'conditional_logic' => 0,
					'wrapper' 			=> array(
						'width'				=> '',
						'class'				=> '',
						'id'				=> '',
					),
					'default_value'		=> 5,
					'placeholder'		=> '',
					'prepend'			=> '',
					'append'			=> '',
					'min'				=> 1,
					'max'				=> 5,
					'step'				=> '',
				),

				// Field - Minimum Rating - Number
				array(
					'key'				=> 'field_600911bbec044',
					'label'				=> 'Minimum Rating',
					'name'				=> 'orgnk_greviews_min_rating',
					'type'				=> 'number',
					'instructions'		=> 'The minimum star rating to show.<br><br><strong>Note: </strong>if there are less than 5 reviews with this rating or higher, Google will only deliver those reviews. For example: if there are only 2 5-star reviews, then only 2 reviews will display in total.',
					'required'			=> 0,
					'conditional_logic'	=> 0,
					'wrapper'			=> array(
						'width'				=> '',
						'class'				=> '',
						'id'				=> '',
					),
					'default_value'		=> 1,
					'placeholder'		=> '',
					'prepend'			=> '',
					'append'			=> '',
					'min'				=> 1,
					'max'				=> 5,
					'step'				=> '',
				),

				// Field - Word Limit - Number
				array(
					'key'				=> 'field_60096f537f0da',
					'label'				=> 'Word Limit',
					'name'				=> 'orgnk_greviews_word_limit',
					'type'				=> 'number',
					'instructions'		=> 'Limit the length of the review text. Long reviews will be truncated. Leave blank for no word limit.',
					'required'			=> 0,
					'conditional_logic'	=> 0,
					'wrapper'			=> array(
						'width'				=> '',
						'class'				=> '',
						'id'				=> '',
					),
					'default_value'		=> '',
					'placeholder'		=> '',
					'prepend'			=> '',
					'append'			=> '',
					'min'				=> 20,
					'max'				=> 2000,
					'step'				=> '',
				),

				// Field - Sort Reviews - Select
				array(
					'key'				=> 'field_6009105aec042',
					'label'				=> 'Sort Reviews',
					'name'				=> 'orgnk_greviews_sort',
					'type'				=> 'select',
					'instructions'		=> '',
					'required'			=> 0,
					'conditional_logic'	=> 0,
					'wrapper'			=> array(
						'width'				=> '',
						'class'				=> '',
						'id'				=> '',
					),
					'choices'			=> array(
						'none'				=> 'None',
						'rating'			=> 'Rating',
						'date'				=> 'Date',
					),
					'default_value'		=> 'none',
					'allow_null'		=> 0,
					'multiple'			=> 0,
					'ui'				=> 0,
					'return_format'		=> 'value',
					'ajax'				=> 0,
					'placeholder'		=> '',
				),

				// Field - Links - Accordion
				array(
					'key'				=> 'field_600927fdaf8e3',
					'label'				=> 'Links',
					'name'				=> '',
					'type'				=> 'accordion',
					'instructions'		=> '',
					'required'			=> 0,
					'conditional_logic' => 0,
					'wrapper'			=> array(
						'width'				=> '',
						'class'				=> '',
						'id'				=> '',
					),
					'open'				=> 0,
					'multi_expand'		=> 0,
					'endpoint'			=> 0,
				),

				// Field - 'Leave a Review' Link - Link
				array(
					'key'				=> 'field_60091238ec045',
					'label'				=> '\'Leave A Review\' Link',
					'name'				=> 'orgnk_greviews_write_review_link',
					'type'				=> 'link',
					'instructions'		=> '',
					'required'			=> 0,
					'conditional_logic' => 0,
					'wrapper'			=> array(
						'width'				=> '',
						'class'				=> '',
						'id'				=> '',
					),
					'return_format'		=> 'array',
				),

				// Field - 'View All Reviews' Link - Link
				array(
					'key'				=> 'field_60091267ec046',
					'label'				=> '\'View All Reviews\' Link',
					'name'				=> 'orgnk_greviews_view_reviews_link',
					'type'				=> 'link',
					'instructions'		=> '',
					'required'			=> 0,
					'conditional_logic' => 0,
					'wrapper'			=> array(
						'width'				=> '',
						'class'				=> '',
						'id'				=> '',
					),
					'return_format'		=> 'array',
				),
			),

			// Location Rules - Google Reviews Settings Page
			'location' => array(
				array(
					array(
						'param'					=> 'options_page',
						'operator'				=> '==',
						'value'					=> 'google-reviews-settings',
					),
				),
			),

			// Field Group Settings
			'menu_order'				=> 0,
			'position'					=> 'normal',
			'style'						=> 'seamless',
			'label_placement'			=> 'left',
			'instruction_placement'		=> 'label',
			'hide_on_screen'			=> '',
			'active'					=> true,
			'description'				=> '',
		));
	}
}
