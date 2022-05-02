<div class="post-content" id="post_content">
    <?php if (post_password_required()){ 
            do_action( 'argon_passwd_required' );
        }else{
            echo argon_get_post_outdated_info();

            global $post_references, $post_reference_keys_first_index, $post_reference_contents_first_index;
            $post_references = array();
            $post_reference_keys_first_index = array();
            $post_reference_contents_first_index = array();

            the_content();
        }
    ?>
</div>

<?php
    $referenceList = get_reference_list();
    if ($referenceList != ""){
        echo $referenceList;
    }
?>