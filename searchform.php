<form id="argon_serach_form" class="argon-search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>" role="search">
	<div class="form-group mb-3">
		<div class="input-group input-group-alternative">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
			</div>
			<input name="s" class="form-control" placeholder="<?php esc_attr_e('搜索什么...', 'argon');?>" type="search" autocomplete="off" aria-label="<?php esc_attr_e('搜索', 'argon');?>" value="<?php echo esc_attr(get_search_query()); ?>">
		</div>
	</div>
	<div class="text-center">
		<button type="submit" class="btn btn-primary"><?php esc_html_e('搜索', 'argon');?></button>
	</div>
</form>
