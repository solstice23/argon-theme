<?php
    // get_template_part( 'template-parts/preview/content-preview', get_option('argon_article_list_layout', '1') );
?>

<?php $argon_article_list_layout = get_option('argon_article_list_layout', '1'); ?>

<article class="post card bg-white shadow-sm border-0 <?php if (get_option('argon_enable_into_article_animation') == 'true'){echo 'post-preview';} ?> post-preview-layout-<?php echo $argon_article_list_layout; ?>" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php 
    switch ( $argon_article_list_layout ) {
        case '2':
            get_template_part( 'template-parts/preview/content-preview', '2' );
            break;
        
        default:
            get_template_part( 'template-parts/entry/header', $argon_article_list_layout );
            do_action( 'argon_entry_excerpt' );
            do_action( 'argon_entry_tags' );
            break;
    }
?>	
</article>