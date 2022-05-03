<?php
/**
 * 展示页面的内容部分，包括输入密码表单（外部模板）、正文、引文
 * Template part for displaying post content 
 *
 */
?>
<div class="post-content" id="post_content">
    <?php if ( post_password_required() ){ 
            do_action( 'argon_entry_content_passwd_required' );
        }else{

            global $post_references, $post_reference_keys_first_index, $post_reference_contents_first_index;
            $post_references = array();
            $post_reference_keys_first_index = array();
            $post_reference_contents_first_index = array();

            do_action( 'argon_entry_content_no_passwd_required', $args );

        }
    ?>
</div>

<?php
    $referenceList = get_reference_list();
    if ($referenceList != ""){
        echo $referenceList;
    }
?>