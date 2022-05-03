<?php
/**
 * 默认的页面主体内容部分
 * Template part for displaying post content 
 *
 */
?>
<article class="post post-full card bg-white shadow-sm border-0" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php 	
		do_action( 'argon_entry_header' ); 
		do_action( 'argon_entry_content', $args); 
		do_action( 'argon_after_entry_content' );
		do_action( 'argon_entry_footer' );  
	?>
</article>
