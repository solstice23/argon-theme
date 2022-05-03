<h3 class="text-black"><?php _e('说说', 'argon');?></h3>
<?php if (the_archive_description() != ''){ ?>
    <p class="text-black mt-3">
        <?php the_archive_description(); ?>
    </p>
<?php } ?>
<p class="text-black mt-3 mb-0 opacity-8">
    <i class="fa fa-quote-left mr-1"></i>
    <?php echo wp_count_posts('shuoshuo','') -> publish; ?> <?php _e('条说说', 'argon');?>
</p>