<?php
// Get reviews data
$reviews            = orgnk_greviews_get_reviews();
$list_class         = 'reviews-list';
$list_class         .= ( $display_type === 'slider' ) ? ' splide' : null;
$list_class         .= ( $display_type === 'slider' && $slider_navigation === 'dots' ) ? ' splide-dot-pagination' : ' splide-button-pagination';
$review_class       = 'review';
$review_class       .= ( $display_type === 'slider' ) ? ' splide__slide' : '';

if ( $reviews ) : ?>

    <div class="orgnk-greviews type-<?php echo $display_type ?>">
        <div class="<?php echo $list_class ?>">

            <?php
            // Add wrappers for Splide slider
            if ( $display_type === 'slider' ) : ?>
                <div class="splide__track">
                <div class="splide__list">
            <?php endif ?>

            <?php foreach ( $reviews as $key => $review ) : ?>

                    <div class="<?php echo $review_class ?>">
                        <div class="review-wrap">

                            <div class="review-rating">
                                <?php echo orgnk_greviews_do_rating_stars( $review, false ) ?>
                            </div>

                            <div class="review-content">
                                <div class="content"><?php echo $review['text']; ?></div>
                            </div>

                            <div class="review-meta">

                                <?php if ( $review['profile_photo_url'] ) : ?>
                                    <div class="avatar">
                                        <img src="<?php echo $review['profile_photo_url'] ?>" alt="<?php echo $review['author_name'] ?>" />
                                    </div>
                                <?php endif ?>

                                <div class="attribution">
                                    <span class="name"><?php echo ucwords( $review['author_name'] ) ?></span>
                                    <span class="relative-time"><?php echo $review['relative_time'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
            endforeach ?>

            <?php
            // Close Splide slider wrappers
            if ( $display_type === 'slider' ) : ?>
                </div>
                </div>
            <?php endif ?>

        </div>
    </div>

<?php endif;
