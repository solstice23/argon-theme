<?php
//编辑文章界面新增 Meta 编辑模块
function argon_meta_box_1(){
	wp_nonce_field("argon_meta_box_nonce_action", "argon_meta_box_nonce");
	global $post;
	?>
		<h4><?php _e("显示字数和预计阅读时间", 'argon');?></h4>
		<?php $argon_meta_hide_readingtime = get_post_meta($post->ID, "argon_hide_readingtime", true);?>
		<select name="argon_meta_hide_readingtime" id="argon_meta_hide_readingtime">
			<option value="false" <?php if ($argon_meta_hide_readingtime=='false'){echo 'selected';} ?>><?php _e("跟随全局设置", 'argon');?></option>
			<option value="true" <?php if ($argon_meta_hide_readingtime=='true'){echo 'selected';} ?>><?php _e("不显示", 'argon');?></option>
		</select>
		<p style="margin-top: 15px;"><?php _e("是否显示字数和预计阅读时间 Meta 信息", 'argon');?></p>
		<h4><?php _e("Meta 中隐藏发布时间和分类", 'argon');?></h4>
		<?php $argon_meta_simple = get_post_meta($post->ID, "argon_meta_simple", true);?>
		<select name="argon_meta_simple" id="argon_meta_simple">
			<option value="false" <?php if ($argon_meta_simple=='false'){echo 'selected';} ?>><?php _e("不隐藏", 'argon');?></option>
			<option value="true" <?php if ($argon_meta_simple=='true'){echo 'selected';} ?>><?php _e("隐藏", 'argon');?></option>
		</select>
		<p style="margin-top: 15px;"><?php _e("适合特定的页面，例如友链页面。开启后文章 Meta 的第一行只显示阅读数和评论数。", 'argon');?></p>
		<h4><?php _e("使用文章中第一张图作为头图", 'argon');?></h4>
		<?php $argon_first_image_as_thumbnail = get_post_meta($post->ID, "argon_first_image_as_thumbnail", true);?>
		<select name="argon_first_image_as_thumbnail" id="argon_first_image_as_thumbnail">
			<option value="default" <?php if ($argon_first_image_as_thumbnail=='default'){echo 'selected';} ?>><?php _e("跟随全局设置", 'argon');?></option>
			<option value="true" <?php if ($argon_first_image_as_thumbnail=='true'){echo 'selected';} ?>><?php _e("使用", 'argon');?></option>
			<option value="false" <?php if ($argon_first_image_as_thumbnail=='false'){echo 'selected';} ?>><?php _e("不使用", 'argon');?></option>
		</select>
		<h4><?php _e("显示文章过时信息", 'argon');?></h4>
		<?php $argon_show_post_outdated_info = get_post_meta($post->ID, "argon_show_post_outdated_info", true);?>
		<div style="display: flex;">
			<select name="argon_show_post_outdated_info" id="argon_show_post_outdated_info">
				<option value="default" <?php if ($argon_show_post_outdated_info=='default'){echo 'selected';} ?>><?php _e("跟随全局设置", 'argon');?></option>
				<option value="always" <?php if ($argon_show_post_outdated_info=='always'){echo 'selected';} ?>><?php _e("一直显示", 'argon');?></option>
				<option value="never" <?php if ($argon_show_post_outdated_info=='never'){echo 'selected';} ?>><?php _e("永不显示", 'argon');?></option>
			</select>
			<button id="apply_show_post_outdated_info" type="button" class="components-button is-primary" style="height: 22px; display: none;"><?php _e("应用", 'argon');?></button>
		</div>
		<p style="margin-top: 15px;"><?php _e("单独控制该文章的过时信息显示。", 'argon');?></p>
		<h4><?php _e("文末附加内容", 'argon');?></h4>
		<?php $argon_after_post = get_post_meta($post->ID, "argon_after_post", true);?>
		<textarea name="argon_after_post" id="argon_after_post" rows="3" cols="30" style="width:100%;"><?php if (!empty($argon_after_post)){echo $argon_after_post;} ?></textarea>
		<p style="margin-top: 15px;"><?php _e("给该文章设置单独的文末附加内容，留空则跟随全局，设为 <code>--none--</code> 则不显示。", 'argon');?></p>
		<h4><?php _e("自定义 CSS", 'argon');?></h4>
		<?php $argon_custom_css = get_post_meta($post->ID, "argon_custom_css", true);?>
		<textarea name="argon_custom_css" id="argon_custom_css" rows="5" cols="30" style="width:100%;"><?php if (!empty($argon_custom_css)){echo $argon_custom_css;} ?></textarea>
		<p style="margin-top: 15px;"><?php _e("给该文章添加单独的 CSS", 'argon');?></p>

        <?php if(get_option('argon_ai_post_summary', false) == 'true'){ ?>
            <h4><?php _e("启用 AI 文章摘要", 'argon');?></h4>
	        <?php $argon_ai_post_summary = get_post_meta($post->ID, "argon_ai_post_summary", true);?>
            <select name="argon_ai_post_summary" id="argon_ai_post_summary">
                <option value="true" <?php if ($argon_ai_post_summary=='true'){echo 'selected';} ?>><?php _e("启用", 'argon');?></option>
                <option value="false" <?php if ($argon_ai_post_summary=='false'){echo 'selected';} ?>><?php _e("不启用", 'argon');?></option>
            </select>
            <p style="margin-top: 15px;"><?php _e("当且仅当该选项和全局选项同时启用时才会有效。使用 ChatGPT 自动生成文章摘要。这将替换您主页的文章摘要，并在文章页面头部显示一个摘要卡片。", 'argon');?></p>
            <h4><?php _e("更新文章时不重复生成摘要", 'argon');?></h4>
	        <?php $argon_ai_no_update_post_summary = get_post_meta($post->ID, "argon_ai_no_update_post_summary", true);?>
            <div style="display: flex;">
                <select name="argon_ai_no_update_post_summary" id="argon_ai_no_update_post_summary">
                    <option value="default" <?php if ($argon_ai_no_update_post_summary=='default'){echo 'selected';} ?>><?php _e("跟随全局设置", 'argon');?></option>
                    <option value="true" <?php if ($argon_ai_no_update_post_summary=='true'){echo 'selected';} ?>><?php _e("不更新", 'argon');?></option>
                    <option value="false" <?php if ($argon_ai_no_update_post_summary=='false'){echo 'selected';} ?>><?php _e("更新", 'argon');?></option>
                </select>
                <button id="apply_ai_no_update_post_summary" type="button" class="components-button is-primary" style="height: 22px; display: none;"><?php _e("应用", 'argon');?></button>
            </div>
            <p style="margin-top: 15px;"><?php _e('设置本项为"不更新"以阻止摘要在更新文章时重新生成，避免产生高额的 API 调用开销。', 'argon');;?></p>
            <h4><?php _e("额外 Prompt", 'argon');?></h4>
	        <?php $argon_ai_extra_prompt_mode = get_post_meta($post->ID, "argon_ai_extra_prompt_mode", true);?>
            <div style="display: flex;">
                <select style="margin-bottom: 1px" name="argon_ai_extra_prompt_mode" id="argon_ai_extra_prompt_mode">
                    <option value="default" <?php if ($argon_ai_extra_prompt_mode=='default'){echo 'selected';} ?>><?php _e("跟随全局设置", 'argon');?></option>
                    <option value="replace" <?php if ($argon_ai_extra_prompt_mode=='replace'){echo 'selected';} ?>><?php _e("替换全局设置", 'argon');?></option>
                    <option value="append" <?php if ($argon_ai_extra_prompt_mode=='append'){echo 'selected';} ?>><?php _e("附加在全局设置后", 'argon');?></option>
                    <option value="none" <?php if ($argon_ai_extra_prompt_mode=='none'){echo 'selected';} ?>><?php _e("不使用", 'argon');?></option>
                </select>
                <button id="apply_ai_extra_prompt_mode" type="button" class="components-button is-primary" style="height: 22px; display: none;"><?php _e("应用", 'argon');?></button>
            </div>
	        <?php $argon_ai_extra_prompt = get_post_meta($post->ID, "argon_ai_extra_prompt", true);?>
            <textarea name="argon_ai_extra_prompt" id="argon_ai_extra_prompt" rows="5" cols="30" style="width:100%;"><?php if (!empty($argon_ai_extra_prompt)){echo $argon_ai_extra_prompt;} ?></textarea>
            <p style="margin-top: 15px;"><?php _e('发送给 ChatGPT 的额外 Prompt，将被以"system"的角色插入在文章信息后。', 'argon');?></p>
            <script>
                let mode = document.getElementById('argon_ai_extra_prompt_mode');
                let prompts = document.getElementById('argon_ai_extra_prompt');
                mode.addEventListener('change', () => {
                    if (mode.value === 'none' || mode.value === 'default') {
                        prompts.style.display = 'none'
                    } else {
                        prompts.style.display = ''
                    }
                })
                mode.dispatchEvent(new Event('change'))
            </script>
	    <?php } ?>

		<script>$ = window.jQuery;</script>
		<script>
			function showAlert(type, message){
				if (!wp.data){
					alert(message);
					return;
				}
				wp.data.dispatch('core/notices').createNotice(
					type,
					message,
					{ type: "snackbar", isDismissible: true, }
				);
			}
            function registerListener(elementName, buttonID){
                const element = $(`#${elementName}`);
                const button = $(`#${buttonID}`);
                element.change(function(){
                    button.css("display", "");
                });
                button.click(function(){
                    button.addClass("is-busy").attr("disabled", "disabled").css("opacity", "0.5");
                    element.attr("disabled", "disabled");
                    const data = {
                        action: 'update_post_meta_ajax',
                        argon_meta_box_nonce: $("#argon_meta_box_nonce").val(),
                        post_id: <?php echo $post->ID; ?>,
                        meta_key: elementName,
                        meta_value: element.val()
                    };
                    $.ajax({
                        url: ajaxurl,
                        type: 'post',
                        data: data,
                        success: function(response) {
                            button.removeClass("is-busy").removeAttr("disabled").css("opacity", "1");
                            element.removeAttr("disabled");
                            if (response.status == "failed"){
                                showAlert("failed", "<?php _e("应用失败", 'argon');?>");
                                return;
                            }
                            button.css("display", "none");
                            showAlert("success", "<?php _e("应用成功", 'argon');?>");
                        },
                        error: function(response) {
                            button.removeClass("is-busy").removeAttr("disabled").css("opacity", "1");
                            element.removeAttr("disabled");
                            showAlert("failed", "<?php _e("应用失败", 'argon');?>");
                        }
                    });
                });
            }

            registerListener('argon_show_post_outdated_info', 'apply_show_post_outdated_info')
            registerListener('argon_ai_no_update_post_summary', 'apply_ai_no_update_post_summary')
            registerListener('argon_ai_extra_prompt_mode', 'apply_ai_extra_prompt_mode')
            registerListener('argon_ai_extra_prompt', 'apply_ai_extra_prompt_mode')
		</script>
	<?php
}
function argon_add_meta_boxes(){
	add_meta_box('argon_meta_box_1', __("文章设置", 'argon'), 'argon_meta_box_1', array('post', 'page'), 'side', 'low');
}
add_action('admin_menu', 'argon_add_meta_boxes');
function argon_save_meta_data($post_id){
	if (!isset($_POST['argon_meta_box_nonce'])){
		return $post_id;
	}
	$nonce = $_POST['argon_meta_box_nonce'];
	if (!wp_verify_nonce($nonce, 'argon_meta_box_nonce_action')){
		return $post_id;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
		return $post_id;
	}
	if ($_POST['post_type'] == 'post'){
		if (!current_user_can('edit_post', $post_id)){
			return $post_id;
		}
	}
	if ($_POST['post_type'] == 'page'){
		if (!current_user_can('edit_page', $post_id)){
			return $post_id;
		}
	}
	update_post_meta($post_id, 'argon_hide_readingtime', $_POST['argon_meta_hide_readingtime']);
	update_post_meta($post_id, 'argon_meta_simple', $_POST['argon_meta_simple']);
	update_post_meta($post_id, 'argon_first_image_as_thumbnail', $_POST['argon_first_image_as_thumbnail']);
	update_post_meta($post_id, 'argon_show_post_outdated_info', $_POST['argon_show_post_outdated_info']);
	update_post_meta($post_id, 'argon_after_post', $_POST['argon_after_post']);
	update_post_meta($post_id, 'argon_custom_css', $_POST['argon_custom_css']);
	update_post_meta($post_id, 'argon_ai_post_summary', $_POST['argon_ai_post_summary'] );
    update_post_meta($post_id,'argon_ai_no_update_post_summary', $_POST['argon_ai_no_update_post_summary']);
	update_post_meta($post_id,'argon_ai_extra_prompt_mode', $_POST['argon_ai_extra_prompt_mode']);
	update_post_meta($post_id,'argon_ai_extra_prompt', $_POST['argon_ai_extra_prompt']);
}
add_action('save_post', 'argon_save_meta_data');
function update_post_meta_ajax(){
	if (!isset($_POST['argon_meta_box_nonce'])){
		return;
	}
	$nonce = $_POST['argon_meta_box_nonce'];
	if (!wp_verify_nonce($nonce, 'argon_meta_box_nonce_action')){
		return;
	}
	header('Content-Type:application/json; charset=utf-8');
	$post_id = intval($_POST["post_id"]);
	$meta_key = $_POST["meta_key"];
	$meta_value = $_POST["meta_value"];

	if (get_post_meta($post_id, $meta_key, true) == $meta_value){
		exit(json_encode(array(
			'status' => 'success'
		)));
		return;
	}

	$result = update_post_meta($post_id, $meta_key, $meta_value);

	if ($result){
		exit(json_encode(array(
			'status' => 'success'
		)));
	}else{
		exit(json_encode(array(
			'status' => 'failed'
		)));
	}
}
add_action('wp_ajax_update_post_meta_ajax' , 'update_post_meta_ajax');
add_action('wp_ajax_nopriv_update_post_meta_ajax' , 'update_post_meta_ajax');