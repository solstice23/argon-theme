(function(){
	tinymce.create('tinymce.plugins.codeblock', {
		init : function(ed, url){
			ed.addButton('codeblock', {
				title : '代码块',
				image : url+'/codeblock.svg',
				onclick : function(){
					ed.selection.setContent('<pre class="code">' + ed.selection.getContent() + '</pre>');
				}
			});
		},
		createControl:function(n, cm){
			return null;
		},
	});
	tinymce.PluginManager.add('codeblock', tinymce.plugins.codeblock);

	tinymce.create('tinymce.plugins.label', {
		init : function(ed, url){
			ed.addButton('label', {
				title : '标签',
				image : url+'/label.svg',
				onclick : function(){
					ed.selection.setContent('[label]' + ed.selection.getContent() + '[/label]');
				}
			});
		},
		createControl:function(n, cm){
			return null;
		},
	});
	tinymce.PluginManager.add('label', tinymce.plugins.label);

	tinymce.create('tinymce.plugins.checkbox', {
		init : function(ed, url){
			ed.addButton('checkbox', {
				title : 'TODO 复选框',
				image : url+'/checkbox.svg',
				onclick : function(){
					ed.selection.setContent('[checkbox checked="true/false"]' + ed.selection.getContent() + '[/checkbox]');
				}
			});
		},
		createControl:function(n, cm){
			return null;
		},
	});
	tinymce.PluginManager.add('checkbox', tinymce.plugins.checkbox);

	tinymce.create('tinymce.plugins.progressbar', {
		init : function(ed, url){
			ed.addButton('progressbar', {
				title : '进度条',
				image : url+'/progressbar.svg',
				onclick : function(){
					ed.selection.setContent('[progressbar progress="100"]' + ed.selection.getContent() + '[/progressbar]');
				}
			});
		},
		createControl:function(n, cm){
			return null;
		},
	});
	tinymce.PluginManager.add('progressbar', tinymce.plugins.progressbar);

	tinymce.create('tinymce.plugins.alert', {
		init : function(ed, url){
			ed.addButton('alert', {
				title : '提示',
				image : url+'/alert.svg',
				onclick : function(){
					ed.selection.setContent('[alert]' + ed.selection.getContent() + '[/alert]');
				}
			});
		},
		createControl:function(n, cm){
			return null;
		},
	});
	tinymce.PluginManager.add('alert', tinymce.plugins.alert);

	tinymce.create('tinymce.plugins.admonition', {
		init : function(ed, url){
			ed.addButton('admonition', {
				title : '警告',
				image : url+'/admonition.svg',
				onclick : function(){
					ed.selection.setContent('[admonition]' + ed.selection.getContent() + '[/admonition]');
				}
			});
		},
		createControl:function(n, cm){
			return null;
		},
	});
	tinymce.PluginManager.add('admonition', tinymce.plugins.admonition);

	tinymce.create('tinymce.plugins.collapse', {
		init : function(ed, url){
			ed.addButton('collapse', {
				title : '折叠区块',
				image : url+'/collapse.svg',
				onclick : function(){
					ed.selection.setContent('[collapse title="折叠区块标题"]' + ed.selection.getContent() + '[/collapse]');
				}
			});
		},
		createControl:function(n, cm){
			return null;
		},
	});
	tinymce.PluginManager.add('collapse', tinymce.plugins.collapse);

	tinymce.create('tinymce.plugins.timeline', {
		init : function(ed, url){
			ed.addButton('timeline', {
				title : '时间线',
				image : url+'/timeline.svg',
				onclick : function(){
					ed.selection.setContent('[timeline]' + ed.selection.getContent() + '[/timeline]');
				}
			});
		},
		createControl:function(n, cm){
			return null;
		},
	});
	tinymce.PluginManager.add('timeline', tinymce.plugins.timeline);

	tinymce.create('tinymce.plugins.github', {
		init : function(ed, url){
			ed.addButton('github', {
				title : 'Github 信息卡',
				image : url+'/github.svg',
				onclick : function(){
					ed.selection.setContent('[github author="" project="" /]');
				}
			});
		},
		createControl:function(n, cm){
			return null;
		},
	});
	tinymce.PluginManager.add('github', tinymce.plugins.github);

	tinymce.create('tinymce.plugins.video', {
		init : function(ed, url){
			ed.addButton('video', {
				title : '视频',
				image : url+'/video.svg',
				onclick : function(){
					ed.selection.setContent('[video url="" /]');
				}
			});
		},
		createControl:function(n, cm){
			return null;
		},
	});
	tinymce.PluginManager.add('video', tinymce.plugins.video);

	tinymce.create('tinymce.plugins.hiddentext', {
		init : function(ed, url){
			ed.addButton('hiddentext', {
				title : '隐藏文本',
				image : url+'/hiddentext.svg',
				onclick : function(){
					ed.selection.setContent('[spoiler]' + ed.selection.getContent() + '[/spoiler]');
				}
			});
		},
		createControl:function(n, cm){
			return null;
		},
	});
	tinymce.PluginManager.add('hiddentext', tinymce.plugins.hiddentext);
})();