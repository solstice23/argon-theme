<article class="no-results post card bg-white shadow-sm border-0">
	<header class="post-header text-center">
		<i style="font-size:80px;opacity:.6;margin-top:30px;margin-bottom:15px;" aria-hidden="true" class="fa fa-folder-o"></i>
		<h1 class="post-title"><?php _e('没有搜索结果', 'argon');?></h1>
		<?php if (($_GET['post_type'] ?? '') == 'none'){ ?>
			<span><?php _e('似乎没有勾选任何分类', 'argon');?></span>
		<?php } else { ?>
			<span><?php _e('换个关键词试试 ?', 'argon');?></span>
		<?php }?>
		<br>
		<a href="javascript:window.history.back(-1);" ondragstart="return false;" class="btn btn-outline-primary" style="margin-top:45px;">
			<span class="btn-inner--icon"><i class="fa fa-chevron-left"></i></span>
			<span class="btn-inner--text"><?php _e('返回上一页', 'argon');?></span>
		</a>
	</header>
</article>