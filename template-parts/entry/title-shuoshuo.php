<?php
/**
 * 说说的 title
 *
 */
?>

<?php if ( get_the_title() != '' ) : ?>
    <a class="shuoshuo-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
<?php endif; ?>
