var $ = window.$;

if (typeof(window.window.argonConfig) == "undefined"){
	window.window.argonConfig = {};
}
if (typeof(window.window.argonConfig.wp_path) == "undefined"){
	window.window.argonConfig.wp_path = "/";
}

//Lazyload
require('./lazyload');

//顶栏
require('./toolbar');

//左侧栏
require('./sidebar');

//浮动按钮栏
require('./float-action-btns');

//搜索框
require('./search');
//搜索过滤器
require('./search-filter');

//Headroom
require('./headroom');

//瀑布流布局
require('./waterflow');

//Highlight.js
require('./code-highlight');


//评论相关
require('./comments');

//需要密码的文章加载
require('./article-password');


// Hash 定位
require('./utils/go-to-hash');


//显示文章过时信息 Toast
require('./post-outdated-toast');


//Zoomify
require('./zoomify');

//Fancybox
require('./fancybox');


//Highlight.js
require('./code-highlight');

//Clamp.js
require('./utils/clamp');

//Tippy.js
require('./tippy');

//Banner 全屏封面相关
require('./banner-cover');

//Pjax
require('./pjax');

//Reference 跳转
require('./article-reference');

//侧栏 & 顶栏菜单手机适配
require('./responsible');


//短代码
//折叠区块
require('./shortcodes/collapse-block');
//Github 卡片
require('./shortcodes/github-card');


//说说点赞
require('./shuoshuo-vote');

//折叠长说说
require('./shuoshuo-fold');


//Banner 打字效果
require('./banner-typing-effect.js');

//一言
require('./hitokoto');

//分享
require('./share');

//横向滚动
require('./horizontal-scroll');

//Console Info
require('./console-info');