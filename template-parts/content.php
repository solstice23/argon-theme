<?php
/**
 * 默认的页面主体内容部分
 * Template part for displaying post content 
 *
 */
?>
<article class="post post-full card bg-white shadow-sm border-0" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php 
		if (argon_has_post_thumbnail() && get_option('argon_show_thumbnail_in_banner_in_content_page') == 'true'){
			get_template_part( 'template-parts/entry/header', 'thumbnail-in-banner' );
		}
		if (argon_has_post_thumbnail() && get_option('argon_show_thumbnail_in_banner_in_content_page') != 'true'){
			switch ( get_option('argon_article_list_layout', '1') ) {
				case '2':
					get_template_part( 'template-parts/entry/header', '1' );
					break;
				
				default:
					get_template_part( 'template-parts/entry/header', get_option('argon_article_list_layout', '1') );
					break;
			}
		}
	?>
	<?php 	
		// do_action( 'argon_entry_header' ); 
		do_action( 'argon_entry_content', $args); 
		do_action( 'argon_after_entry_content' );
		do_action( 'argon_entry_footer' );  
	?>
</article>
