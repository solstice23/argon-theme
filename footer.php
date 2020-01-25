					<footer id="footer" class="site-footer card shadow-sm border-0">
						<?php
							echo get_option('argon_footer_html');
						?>
						<div>Theme <a href="https://github.com/solstice23/argon-theme"><strong>Argon</strong></a> By solstice23</div>
					</footer>
				</main>
			</div>
		</div>
		<script src="<?php bloginfo('template_url'); ?>/argontheme.js?v<?php echo wp_get_theme('argon')-> Version; ?>"></script>
		<?php if (get_option('argon_mathjax_enable') == 'true') { /*Mathjax*/?>
			<script>
				window.MathJax = {
					tex: {
						inlineMath: [["$", "$"], ["\\\\(", "\\\\)"]],
						displayMath: [['$$','$$']],
						processEscapes: true,
						packages: {'[+]': ['noerrors']}
					},
					options: {
						skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code'],
						ignoreHtmlClass: 'tex2jax_ignore',
						processHtmlClass: 'tex2jax_process'
					},
					loader: {
						load: ['[tex]/noerrors']
					}
				};
			</script>
			<script src="<?php echo get_option('argon_mathjax_cdn_url') == '' ? '//cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml-full.js' : get_option('argon_mathjax_cdn_url'); ?>" id="MathJax-script" async></script>
		<?php }?>
	</div>
</div>
<?php wp_footer(); ?>
</body>

<?php echo get_option('argon_custom_html_foot'); ?>

</html>
