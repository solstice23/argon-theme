<?php
/**
 * 说说的 meta
 *
 */
?>

<span>
    <div class="post-meta-detail">
        <i class="fa fa-calendar-o" aria-hidden="true"></i> 
        <span class="shuoshuo-date-month"><?php echo get_the_time('n')?></span> <?php _e('月', 'argon');?> 
        <span class="shuoshuo-date-date"><?php echo get_the_time('d')?></span> <?php _e('日', 'argon');?> , 
        <span class="shuoshuo-date-year"><?php echo get_the_time('Y')?></span>
    </div>
    <div class="post-meta-devide">|</div>
    <div class="post-meta-detail">
        <i class="fa fa-clock fa-clock-o" aria-hidden="true"></i> 
        <span class="shuoshuo-date-time"><?php echo get_the_time('G:i')?></span>
    </div>
</span>
<?php if ( is_sticky() ) : ?>
    <div class="post-meta-devide">|</div>
    <div class="post-meta-detail post-meta-detail-words">
        <i class="fa fa-thumb-tack" aria-hidden="true"></i>
        <?php _ex('置顶', 'pinned', 'argon');?>
    </div>
<?php endif; ?>
