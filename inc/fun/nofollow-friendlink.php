<?php
function argon_blogroll_nofollow() {
    add_action('add_meta_boxes', 'argon_blogroll_add_meta_box', 1, 1);
    add_filter('pre_link_rel', 'argon_blogroll_save_meta_box', 10, 1);
}

function argon_blogroll_add_meta_box() {
    add_meta_box('argon_blogroll_nofollow_div', __('nofollow'), 'argon_blogroll_inner_meta_box', 'link', 'side');
}

function argon_blogroll_inner_meta_box($post) {
    $bookmark = get_bookmark($post->ID, 'ARRAY_A');
    if (strpos($bookmark['link_rel'], 'nofollow') !== FALSE)
        $checked = ' checked="checked"';
    else
        $checked = '';
?>
    <input value="1" id="argon_blogroll_nofollow_checkbox" name="argon_blogroll_nofollow_checkbox"
           type="checkbox"<?php echo $checked; ?>> <label
    for="argon_blogroll_nofollow_checkbox"><?php echo __('添加 <code>nofollow</code> 属性', 'argon'); ?></label>
<?php
}

function argon_blogroll_save_meta_box($link_rel) {
    $rel = trim(str_replace('nofollow', '', $link_rel));
    if ($_POST['argon_blogroll_nofollow_checkbox'])
        $rel .= ' nofollow';
    return trim($rel);
}
add_action('load-link.php', 'argon_blogroll_nofollow');
add_action('load-link-add.php', 'argon_blogroll_nofollow');
