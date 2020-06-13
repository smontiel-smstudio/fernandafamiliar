<?php get_header(); ?>
<div class="container pt-5 pb-5">
    <?php
    query_posts(array('post_type' => 'post','orderby' => 'date'));
    if(have_posts()) : while(have_posts()) : the_post();
    $external_link = get_post_custom_values('external_link')[0];
    ?>
<div class="card mt-5 mb-5 p-2">
    <?php $image_id = get_post_thumbnail_id(); ?>
    <?php $image_attributes = wp_get_attachment_image_src( $image_id, 'full');  ?>
    <a href="<?php echo $external_link; ?>" target="_blank"><img style="object-fit: cover; object-position: top;" class="mb-3" src="<?php echo $image_attributes[0]; ?>" width="100%" height="400"></a>
        <h3><?php the_title(); ?></h3>
    <div>Fecha y hora de publicación: <?php the_date(); ?> <?php the_time(); ?></div>
    <div>Fecha y hora de modificación <?php the_modified_date(); ?> <?php the_modified_time(); ?></div>
    <p><?php the_excerpt(); ?></p>
</div>
    <?php endwhile; ?>
    <?php else : ?>

        <p>sorry no results</p>

    <?php endif; wp_reset_query(); ?>
</div>
<?php get_footer(); ?>
