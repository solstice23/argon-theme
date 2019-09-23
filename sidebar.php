<div id="sidebar_mask"></div>
<aside id="leftbar" class="leftbar widget-area" role="complementary">
		<div id="leftbar_part1" class="widget widget_search card bg-white shadow-sm border-0">
			<div class="leftbar-banner card-body">
				<span class="leftbar-banner-title text-white"><?php echo get_option('argon_sidebar_banner_title') == '' ? bloginfo('name') : get_option('argon_sidebar_banner_title'); ?></span>

				<?php if (get_option('argon_sidebar_banner_subtitle') != '') { /*左侧栏子标题/格言(如果选项中开启)*/?>
					<span class="leftbar-banner-subtitle text-white"><?php echo get_option('argon_sidebar_banner_subtitle'); ?></span>
				</a>
				<?php } /*顶栏标题*/?>

			</div>

			<?php
				/*侧栏上部菜单*/
				class leftbarMenuWalker extends Walker_Nav_Menu{
					public function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
						if ($depth == 0){
							$output .= "\n
							<li class='leftbar-menu-item" . ( $object -> current == 1 ? " current" : "" ) . "'>
								<a href='" . $object -> url . "'>". $object -> title . "</a>";
						}
					}
					public function end_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
						if ($depth == 0){
							$output .= "</li>";
						}
					}
				}
				echo "<ul id='leftbar_part1_menu' class='leftbar-menu'>";
				if ( has_nav_menu('leftbar_menu') ){
					wp_nav_menu( array(
						'container'  => '',
						'theme_location'  => 'leftbar_menu',
						'items_wrap'  => '%3$s',
						'depth' => 0,
						'walker' => new leftbarMenuWalker()
					) );
				}
				echo "</ul>";
			?>
			<div class="card-body text-center leftbar-search-button">
				<button class="btn btn-secondary btn-lg active btn-sm btn-block" role="button" aria-pressed="true" data-toggle="modal" data-target="#argon_search_modal"><i class="menu-item-icon fa fa-search"></i> 搜索</button>
			</div>
		</div>
		<div id="leftbar_part2" class="widget widget_search card bg-white shadow-sm border-0">
			<div id="leftbar_part2_inner" class="card-body">
				<?php
					$nowActiveTab = 1;/*默认激活的标签*/
					if (have_catalog()){
						$nowActiveTab = 0;
					}
				?>
				<div class="nav-wrapper" style="padding-top: 5px;<?php if (!have_catalog() && !is_active_sidebar('leftbar-tools')) { echo ' display:none;'; }?>">
	                <ul class="nav nav-pills nav-fill" role="tablist">
						<?php if (have_catalog()) { ?>
							<li class="nav-item sidebar-tab-switcher">
								<a class="<?php if ($nowActiveTab == 0) { echo 'active show'; }?>" id="leftbar_tab_catalog_btn" data-toggle="tab" href="#leftbar_tab_catalog" role="tab" aria-controls="leftbar_tab_catalog" no-pjax>文章目录</a>
							</li>
						<?php } ?>
						<li class="nav-item sidebar-tab-switcher">
							<a class="<?php if ($nowActiveTab == 1) { echo 'active show'; }?>" id="leftbar_tab_overview_btn" data-toggle="tab" href="#leftbar_tab_overview" role="tab" aria-controls="leftbar_tab_overview" no-pjax>站点概览</a>
						</li>
						<?php if ( is_active_sidebar( 'leftbar-tools' ) ){?>
							<li class="nav-item sidebar-tab-switcher">
								<a class="<?php if ($nowActiveTab == 2) { echo 'active show'; }?>" id="leftbar_tab_tools_btn" data-toggle="tab" href="#leftbar_tab_tools" role="tab" aria-controls="leftbar_tab_tools" no-pjax>功能</a>
							</li>
						<?php }?>
	                </ul>
				</div>
				<div>
					<div class="tab-content" style="padding: 10px 10px 0 10px;">
						<?php if (have_catalog()) { ?>
							<div class="tab-pane fade<?php if ($nowActiveTab == 0) { echo ' active show'; }?>" id="leftbar_tab_catalog" role="tabpanel" aria-labelledby="leftbar_tab_catalog_btn">
								<div id="leftbar_catalog"></div>
								<script type="text/javascript">
									$(function () {
									    $(document).headIndex({
											articleWrapSelector: '#post_content',
											indexBoxSelector: '#leftbar_catalog',
											subItemBoxClass: "index-subItem-box",
											itemClass: "index-item",
											linkClass: "index-link",
											offset: 80,
										});
									})
								</script>
							</div>
						<?php } ?>
						<div class="tab-pane fade text-center<?php if ($nowActiveTab == 1) { echo ' active show'; }?>" id="leftbar_tab_overview" role="tabpanel" aria-labelledby="leftbar_tab_overview_btn">
							<img id="leftbar_overview_author_image" src="<?php echo get_option('argon_sidebar_auther_image') == '' ? 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/PjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+PHN2ZyB0PSIxNTYwNDQyNTc2NjMxIiBjbGFzcz0iaWNvbiIgc3R5bGU9IiIgdmlld0JveD0iMCAwIDEwMjQgMTAyNCIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHAtaWQ9IjIyMjEiIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iODAwIiBoZWlnaHQ9IjgwMCI+PGRlZnM+PHN0eWxlIHR5cGU9InRleHQvY3NzIj48L3N0eWxlPjwvZGVmcz48cGF0aCBkPSJNMC43MjUgNTEyYTUxMS41MjkgNTExLjUyOSAwIDEgMCAxMDIzLjA1OSAwQTUxMS41MjkgNTExLjUyOSAwIDEgMCAwLjcyNiA1MTJ6IiBmaWxsPSIjZjJjMzQ1IiBwLWlkPSIyMjIyIj48L3BhdGg+PHBhdGggZD0iTTc0OS42NTQgODQ3LjI0M2MtMjMuOTI1LTkuMzI2LTUyLjA1Mi0xNy4yMi04My4yNTctMjMuMzM1aC0wLjAwMWMtMTcuNDY5LTMuNDI0LTM1Ljg4Ni02LjMtNTUuMTE2LTguNTMtMjguMTQ5IDcuNTY3LTYyLjI1NyAxMi4wMDEtOTkuMDI0IDEyLjAwMS0zNi44MDkgMC03MC45NTItNC40NDQtOTkuMTItMTIuMDI2LTIwLjcxOSAyLjM5Ny00MC40OSA1LjU0NC01OS4xNSA5LjMyIDEuNjU3IDAuNTY4IDMuMzUzIDEuMDQyIDUuMDM1IDEuNTUxLTEuNjgyLTAuNTA5LTMuMzc4LTAuOTgyLTUuMDM0LTEuNTUtMjkuNDQzIDUuOTU4LTU2LjA3IDEzLjUwOC03OC44OTMgMjIuMzYtMC40NDMtMS4yOS01NS4zOCAyOS4zMjMtODYuOTUxIDYwLjgwNCA4OC4yMTggNzIuMjgzIDIwMS4wMSAxMTUuNjkgMzIzLjk1NiAxMTUuNjkgMTIzLjA5IDAgMjM2LjAwNy00My41IDMyNC4yNy0xMTUuOTM2LTMxLjU1Mi0zMS4yOTUtODYuMjUxLTYxLjY5Mi04Ni43MTUtNjAuMzV6IiBmaWxsPSIjMTI5NkRCIiBwLWlkPSIyMjIzIj48L3BhdGg+PHBhdGggZD0iTTczNi42OTYgNDQ0LjEyN2MtNi40OTgtMi40NTUtMTMuNzA3LTEuMjc3LTIwLjgyNSAyLjY3NiAwLjYzLTQyLjEzLTUuNjA3LTgwLjI1OC0xOC4wMzktMTEwLjUxNGgtMzcxLjQ3Yy0xMi45MjMgMzEuNDU1LTE5LjE5NSA3MS4zOS0xNy45NjkgMTE1LjUxOC03LjE0LTMuOTg0LTE0LjM3NC01LjE3Ni0yMC44OTQtMi43MTMtMTguNDgxIDYuOTgxLTI0LjE5MiA0MC4xODYtMTIuNzU0IDc0LjE2NiAxMS40MzYgMzMuOTcxIDM1LjY4MiA1NS44NTQgNTQuMTYgNDguODg4IDE2LjYzMyA1My45NTkgNDEuMzIgMTAwLjQ2MyA2OS42NyAxMzUuMjQ0IDQuOTggNi4xMDkgMTAuMDY5IDExLjg2NCAxNS4yNSAxNy4yMjV2OTAuNjU0Yy0wLjIzIDAuMDI2LTAuNDU5IDAuMDU2LTAuNjkgMC4wODMgMjguMTY3IDcuNTgyIDYyLjMxIDEyLjAyNiA5OS4xMTkgMTIuMDI2IDM2Ljc2NyAwIDcwLjg3NS00LjQzNCA5OS4wMjQtMTJsLTAuOTEtMC4xMXYtOTAuNjEyYzUuMjE3LTUuMzk0IDEwLjM0Mi0xMS4xODUgMTUuMzU0LTE3LjMzNSAyOC4yOS0zNC43MTggNTIuOTI5LTgxLjExMiA2OS41NDMtMTM0LjkzMiAwLjQ5NC0xLjU5NyA0My45ODQgMCA1NC4xODMtNTQuMSAxMS40NC0zMy45NzkgNS43My02Ny4xODUtMTIuNzUyLTc0LjE2NHoiIGZpbGw9IiNFRUQ1QTEiIHAtaWQ9IjIyMjQiPjwvcGF0aD48cGF0aCBkPSJNNjk3LjgzMyAzMzguMDRzLTIuMjQzIDQ3LjI3IDE4LjAzOSAxMTUuMjc2YzAgMCA4OC41OTctMjU3LjQ1OS03Ny45Mi0yNTcuNDU5IDAgMC00MzEuMjM4LTE0OS45MzktMzI5LjU1OCAyNjIuNjc4IDAgMCAyLjg1NCAxMS40OTMgNy45MDkgMjAuODcgNjguMzEzLTEwMS4wNDkgMjk0LjYwNi02OC4zMTQgMzgxLjUzLTE0MS4zNjR6IiBmaWxsPSIjNUM0OTQ4IiBwLWlkPSIyMjI1Ij48L3BhdGg+PHBhdGggZD0iTTMyMS4zNCA5MzMuNzA3aDkyLjQ2N3YxNS44MTRIMzIxLjM0di0xNS44MTR6TTUxMS43NzIgODI3LjM2OHMyMS4xODIgMzUuNjY5IDczLjg0NCA0Ni4xMDdjMCAwIDI3LjExMy0zNi4wMzYgMjUuMTgtNTguMTA4bC05OS4wMjQgMTJ6TTUxMS43MjQgODI3LjM4M3MtMjEuMTgzIDM1LjY3LTczLjg0NSA0Ni4xMDdjMCAwLTI3LjExMy0zNi4wMzYtMjUuMTgtNTguMTA4bDk5LjAyNSAxMi4wMDF6IiBmaWxsPSIjQjlDOUMyIiBwLWlkPSIyMjI2Ij48L3BhdGg+PHBhdGggZD0iTTUwMi40NTggODUzLjc1YTkuMzE0IDkuMzE0IDAgMSAwIDE4LjYyOSAwIDkuMzE0IDkuMzE0IDAgMSAwLTE4LjYyOSAwek01MDIuNDA5IDg4OS40OTFhOS4zMTQgOS4zMTQgMCAxIDAgMTguNjI5IDAgOS4zMTQgOS4zMTQgMCAxIDAtMTguNjI5IDB6TTUwMi40MDkgOTI1Ljg2NGE5LjMxNCA5LjMxNCAwIDEgMCAxOC42MjkgMCA5LjMxNCA5LjMxNCAwIDEgMC0xOC42MjkgMHoiIGZpbGw9IiM1NkE1N0UiIHAtaWQ9IjIyMjciPjwvcGF0aD48L3N2Zz4' : get_option('argon_sidebar_auther_image'); ?>" class="img-fluid rounded-circle shadow-sm" style="width: 100px;">
							<h6 id="leftbar_overview_author_name"><?php echo get_option('argon_sidebar_auther_name') == '' ? bloginfo('name') : get_option('argon_sidebar_auther_name'); ?></h6>
							<nav class="site-state">
								<div class="site-state-item site-state-posts">
									<a style="cursor: default;">
										<span class="site-state-item-count"><?php echo wp_count_posts() -> publish; ?></span>
										<span class="site-state-item-name">文章</span>
									</a>
								</div>
								<div class="site-state-item site-state-categories">
									<a data-toggle="modal" data-target="#blog_categories">
										<span class="site-state-item-count"><?php echo wp_count_terms('category'); ?></span>
										<span class="site-state-item-name">分类</span>
									</a>
								</div>      
								<div class="site-state-item site-state-tags">
									<a data-toggle="modal" data-target="#blog_tags">
										<span class="site-state-item-count"><?php echo wp_count_terms('post_tag'); ?></span>
										<span class="site-state-item-name">标签</span>
									</a>
								</div>
							</nav>
							<?php
								/*侧栏作者链接*/
								class leftbarAuthorLinksWalker extends Walker_Nav_Menu{
									public function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
										if ($depth == 0){
											$output .= "\n
											<div class='site-author-links-item'>
												<a href='" . $object -> url . "' rel='noopener' target='_blank'>". $object -> title . "</a>											";
										}
									}
									public function end_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
										if ($depth == 0){
											$output .= "</div>";
										}
									}
								}

								if ( has_nav_menu('leftbar_author_links') ){
									echo "<div class='site-author-links'>";
									wp_nav_menu( array(
										'container'  => '',
										'theme_location'  => 'leftbar_author_links',
										'items_wrap'  => '%3$s',
										'depth' => 0,
										'walker' => new leftbarAuthorLinksWalker()
									) );
									echo "</div>";
								}
							?>
							<?php
								/*侧栏友情链接*/
								class leftbarFriendLinksWalker extends Walker_Nav_Menu{
									public function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
										if ($depth == 0){
											$output .= "\n
											<li class='site-friend-links-item'>
												<a href='" . $object -> url . "' rel='noopener' target='_blank'>". $object -> title . "</a>";
										}
									}
									public function end_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
										if ($depth == 0){
											$output .= "</li>";
										}
									}
								}

								if ( has_nav_menu('leftbar_friend_links') ){
									echo "<div class='site-friend-links'>
											<div class='site-friend-links-title'><i class='fa fa-fw fa-link'></i> Links</div>
											<ul class='site-friend-links-ul'>";
									wp_nav_menu( array(
										'container'  => '',
									    'theme_location'  => 'leftbar_friend_links',
										'items_wrap'  => '%3$s',
									    'depth' => 0,
										'walker' => new leftbarFriendLinksWalker()
									) );
									echo "</ul></div>";
								}else{
									echo "<div style='height: 20px;'></div>";
								}
							?>
						</div>
						<?php if ( is_active_sidebar( 'leftbar-tools' ) ){?>
							<div class="tab-pane fade<?php if ($nowActiveTab == 2) { echo ' active show'; }?>" id="leftbar_tab_tools" role="tabpanel" aria-labelledby="leftbar_tab_tools_btn">
								<?php dynamic_sidebar( 'leftbar-tools' ); ?>
							</div>
						<?php }?>
					</div>
				</div>
			</div>
		</div>
</aside>
<!--显示所有分类和标签的 Dialog-->
<div class="modal fade" id="blog_categories" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">分类</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?php
					$categories = get_categories(array(
						'child_of' => 0,
						'orderby' => 'name',
						'order' => 'ASC',
						'hide_empty' => 1,
						'hierarchical' => 0,
						'taxonomy' => 'category',
						'pad_counts' => false
					));
					foreach($categories as $category) {
						echo "<a href=" . get_category_link( $category -> term_id ) . " class='badge badge-secondary tag'>" . $category->name . " <span class='tag-num'>" . $category -> count . "</span></a>";
					}
				?>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="blog_tags" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">标签</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?php
					$categories = get_categories(array(
						'child_of' => 0,
						'orderby' => 'name',
						'order' => 'ASC',
						'hide_empty' => 1,
						'hierarchical' => 0,
						'taxonomy' => 'post_tag',
						'pad_counts' => false
					));
					foreach($categories as $category) {
						echo "<a href=" . get_category_link( $category -> term_id ) . " class='badge badge-secondary tag'>" . $category->name . " <span class='tag-num'>" . $category -> count . "</span></a>";
					}
				?>
			</div>
		</div>
	</div>
</div>