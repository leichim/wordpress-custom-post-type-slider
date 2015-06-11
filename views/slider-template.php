<div class="sldcontainer">
    <div class="slides">
        <div class="slides-container">
            <?php  // Grabs all feature slider posts, with a maximum of 10 slides

            $args = array( 'post_type' => 'slider_feature', 'posts_per_page' => '10');
            $loop = new WP_Query( $args );

            while( $loop->have_posts() ) : $loop->the_post(); ?>
                <div class="slide">
                    <?php if( get_post_meta( $post->ID, "slider_image_value", true ) ) : ?>
                        <a href="<?php echo get_post_meta( $post->ID, "slider_link_value", true ) ?>" rel="bookmark">
                            <img src="<?php echo get_post_meta( $post->ID, "slider_image_value", true ); ?>" alt="<?php the_title(); ?>" />
                        </a>
                    <?php endif; ?>
                    <div class="caption">
                        <h2>
                            <a href="<?php echo get_post_meta( $post->ID, "slider_link_value", true )?>" rel="bookmark" title="<?php the_title(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        <?php the_content() ?>
                        <p>
                            <a href="<?php echo get_post_meta( $post->ID, "slider_link_value", true ) ?>" title="Continue reading <?php the_title(); ?>" class="read-more">
                                <?php _e('More information', 'language_domain'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            <?php endwhile; 
            // Reset the query, so we do not interfere with other queries
            wp_reset_postdata(); ?>
        </div> <!-- .slides-container -->
        <a href="#" class="prev" title="Previous Slide">Previous Slide</a> <a href="#" class="next" title="Next Slide">Next Slide</a>
    </div> <!-- .slides -->
</div><!-- .sldcontainer -->