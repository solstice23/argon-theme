					<footer id="footer" class="site-footer card shadow-sm border-0">
						<?php
							echo get_option('argon_footer_html');
						?>
						<div>Theme Argon By abc2237512422</div>
					</footer>
				</main>
			</div>
		</div>
		<script src="<?php bloginfo('template_url'); ?>/js/argontheme.js"></script>
		<?php if (get_option('argon_mathjax_enable') == 'true') { /*Mathjax*/?>
			<script type="text/x-mathjax-config" id="mathjax_script">
				MathJax.Hub.Config({
					messageStyle: "<?php echo (get_option('argon_mathjax_loading_msg_type') == '' ? 'none' : get_option('argon_mathjax_loading_msg_type'));?>",
					tex2jax: {
						inlineMath: [["$", "$"], ["\\\\(", "\\\\)"]],
						displayMath: [['$$','$$']],
						processEscapes: true,
						skipTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code']
					},
					menuSettings: {
						zoom: "<?php echo (get_option('argon_mathjax_zoom_cond') == '' ? 'Hover' : get_option('argon_mathjax_zoom_cond'));?>",
						zscale: "<?php echo (get_option('argon_mathjax_zoom_scale') == '' ? '200' : get_option('argon_mathjax_zoom_scale')); ?>%"
					},
					"HTML-CSS": {
						showMathMenu: <?php echo (get_option('argon_mathjax_show_menu') == 'true' ? 'true' : 'false');?>
					}
				});
			</script>
			<script src="//cdn.bootcss.com/mathjax/2.6.0/MathJax.js?config=TeX-AMS_HTML"></script>
		<?php }?>
	</div>
</div>
<?php wp_footer(); ?>
</body>

<?php echo get_option('argon_custom_html_foot'); ?>

</html>
