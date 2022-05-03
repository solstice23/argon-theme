<?php
if ( is_singular( 'post' ) ) {
    if (get_previous_post() || get_next_post()){
        echo '<div class="post-navigation card shadow-sm">';
        if (get_previous_post()){ 
            previous_post_link('<div class="post-navigation-item post-navigation-pre"><span class="page-navigation-extra-text"><i class="fa fa-circle-arrow-left fa-arrow-circle-o-left" aria-hidden="true"></i>' . __("上一篇", 'argon') . '</span>%link</div>' , '%title');
        }else{
            echo '<div class="post-navigation-item post-navigation-pre"></div>';
        }
        if (get_next_post()){
            next_post_link('<div class="post-navigation-item post-navigation-next"><span class="page-navigation-extra-text">' . __("下一篇", 'argon') . ' <i class="fa fa-circle-arrow-right fa-arrow-circle-o-right" aria-hidden="true"></i></span>%link</div>' , '%title');
        }else{
            echo '<div class="post-navigation-item post-navigation-next"></div>';
        }
        echo '</div>';
    }
}
