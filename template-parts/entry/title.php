<?php
/**
 * 页面标题
 * Template part for displaying a post's title
 *
 */

do_action( 'argon_single_before_entry_title' );
the_title(
    sprintf( '<a class="post-title" href="%s" rel="bookmark">', esc_attr( esc_url( get_permalink() ) ) ),
    '</a>'
  );
do_action( 'argon_single_after_entry_title' );
