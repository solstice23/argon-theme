<?php
/**
 * 展示文章的参考文献列表
 * 
 *
 */
?>

<?php
	global $post_references;
	if ( count($post_references) !== 0 ):
?>
    <div class='reference-list-container'>
        <h3> <?php echo (get_option('argon_reference_list_title') == "" ? __('参考', 'argon') : get_option('argon_reference_list_title')); ?> </h3>
        <ol class='reference-list'>
            <?php
            foreach ($post_references as $index => $ref) {
                echo "<li id='ref_" . ($index + 1)  . "'><div>";
                if ($ref['count'] == 1){
                    echo "<a class='reference-list-backlink' href='#ref_" . ($index + 1) . "_1' aria-label='back'>^</a>";
                }else{
                    echo "<span class='reference-list-backlink'>^</span>";
                    for ($i = 1, $j = 'a'; $i <= $ref['count']; $i++, $j++){
                        echo "<sup><a class='reference-list-backlink' href='#ref_" . ($index + 1) . "_" . $i . "' aria-label='back'>" . $j . "</a></sup>";
                    }
                }
                echo "<span>" . $ref['content'] . "</span>";
                echo "<div class='space' tabindex='-1'></div>";
                echo "</div></li>";
            }?>
        </ol>
    </div>

<?php endif?>