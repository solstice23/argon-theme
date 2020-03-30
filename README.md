![Argon](https://img.solstice23.top/2020/03/05/bee9b7b3ddceb.jpg)
# Argon-Theme
Argon - 一个轻盈、简洁、美观的 WordPress 主题

[![GitHub release](https://img.shields.io/github/v/release/solstice23/argon-theme?color=%235e72e4&style=for-the-badge)](https://github.com/solstice23/argon-theme/releases) [![GitHub All Releases](https://img.shields.io/github/downloads/solstice23/argon-theme/total?style=for-the-badge)](https://github.com/solstice23/argon-theme/releases) [![GitHub](https://img.shields.io/github/license/solstice23/argon-theme?color=blue&style=for-the-badge)](https://github.com/solstice23/argon-theme/blob/master/LICENSE) [![Author]( https://img.shields.io/badge/author-solstice23-yellow?style=for-the-badge)](https://github.com/solstice23) [![GitHub stars](https://img.shields.io/github/stars/solstice23/argon-theme?color=ff69b4&style=for-the-badge)](https://github.com/solstice23/argon-theme/stargazers)

[![GitHub last commit](https://img.shields.io/github/last-commit/solstice23/argon-theme?style=flat-square)](https://github.com/solstice23/argon-theme/commits/master) [![GitHub Release Date](https://img.shields.io/github/release-date/solstice23/argon-theme?style=flat-square)](https://github.com/solstice23/argon-theme/releases) ![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/solstice23/argon-theme?style=flat-square) 

# 特性

- 使用 Argon Design System 前端框架，轻盈美观
- 丰富的自定义选项 （顶栏，侧栏，头图等）
- 顶栏、侧栏完全自定义 （自定义链接，图标，博客名，二级菜单等）
- 丰富的可自定义侧栏内容 （作者名称，格言，作者信息，作者链接，友情链接，分类目录，所有标签等）
- 可设置主题色
- 内置 "说说" 功能，随时发表想法
- 评论支持再次编辑、悄悄话模式、回复时邮件通知（可选）
- 支持在侧栏添加小工具
- 良好的阅读体验
- 侧栏浮动文章目录
- 自动计算字数和阅读时间
- Pjax 无刷新加载
- Ajax 评论
- 内置多种小工具（进度条，TODO 复选框，标签等）
- 内置 Mathjax、平滑滚动等
- 支持自定义 CSS 和 JS
- 适配小屏幕设备
- 夜间模式支持

# 安装

在 [Release](https://github.com/solstice23/argon-theme/releases) 页面下载 .zip 文件，在 WordPress 后台 "主题" 页面上传并安装。

# 文档

[Argon-Theme 文档 : https://argon-docs.solstice23.top](https://argon-docs.solstice23.top/)

# Demo

主题效果预览

[solstice23.top](https://solstice23.top)

[argon-demo.solstice23.top](http://argon-demo.solstice23.top)

# 注意

Argon 使用 [GPL V3.0](https://github.com/solstice23/argon-theme/blob/master/LICENSE) 协议开源，请遵守此协议进行二次开发等。

您**必须在页脚保留 Argon 主题的名称及其链接**，否则请不要使用 Argon 主题。

您**可以删除**页脚的作者信息，但是**不能删除** Argon 主题的名称和链接。

# 渲染

![render1](https://cdn.jsdelivr.net/gh/solstice23/cdn@master/argon-render-small-1.jpg)

![render2](https://cdn.jsdelivr.net/gh/solstice23/cdn@master/argon-render-small-2.jpg)

![render3](https://cdn.jsdelivr.net/gh/solstice23/cdn@master/argon-render-small-3.jpg)

![render4](https://cdn.jsdelivr.net/gh/solstice23/cdn@master/argon-render-small-4.jpg)

![render5](https://cdn.jsdelivr.net/gh/solstice23/cdn@master/argon-render-small-5.jpg)

# 更新日志

## 20200330 v0.921
+ 修复评论编辑历史记录的 BUG
+ 优化搜索逻辑

## 20200330 v0.920
+ 增加查看评论编辑历史记录功能
+ 增加 "谁可以查看评论编辑记录" 选项
+ 赞赏二维码弹框移到赞赏按钮上方
+ 修复分类中文章总数统计错误的 BUG

## 20200326 v0.914
+ 修复评论相关的一些小 BUG

## 20200325 v0.913
+ 夜间模式时间调整 (21:00 改为 22:00)
+ 修复小问题

## 20200324 v0.912
+ 增加 Pangu.js 文本格式化选项
+ 需要密码的文章支持 Ajax 加载

## 20200323 v0.911
+ 增加单栏模式

## 20200322 v0.910
+ 评论区支持分页
+ 新增 "无限加载" 和 "页码" 两种评论分页方式
+ 重写评论模块代码
+ 评论发送后改为局部刷新评论区
+ 优化评论/编辑体验
+ 优化其他一堆细节

## 20200321 v0.902
+ 新增新的友情链接短代码
+ 友情链接改为从 Wordpress 链接管理器中读取
+ 启用 Wordpress 链接管理器
+ 旧的友情链接短代码改名为 `sfriendlinks`
+ 评论会自动填充上一次的姓名、邮箱、网站输入框的内容
+ 增加 "评论时默认勾选 '启用邮件通知'' 复选框" 选项
+ 文章设置新增 "隐藏文章发布时间和分类" 选项
+ 更改说说文章页面 URL

⚠ 在该版本中，友情链接改为从 Wordpress 链接管理器中读取。请将友情链接迁移至 Wordpress 链接管理器中，或将原先的友链短代码改为 `sfriendlinks`。

## 20200319 v0.901
+ 评论通知邮件支持退订
+ 优化评论通知发送邮件逻辑
+ 评论 Markdown 增加对标题、有序列表和无序列表的支持
+ 手机端 UI 微调
+ 优化手机端交互体验微调
+ 修了评论的一堆 BUG

## 20200318 v0.900
+ 评论允许发送者再次编辑（可选）
+ 评论增加悄悄话模式（可选）
+ 评论增加回复时邮件通知模式（可选）
+ 优化文章访问量统计逻辑
+ 其他的一些优化和调整

## 20200315 v0.891
+ 修 BUG

## 20200315 v0.890
+ Argon 设置增加 导入/导出 功能
+ 新增日间/夜间模式不同背景选项
+ 新增 Banner 标题打字动画选项
+ 增加 jsdelivr 更新源
+ 修复一个重大 BUG

## 20200314 v0.885
+ 新增文章过时信息提示选项
+ 增加在浮动按钮栏显示跳到评论区按钮选项
+ 增加 Banner 遮罩和 Banner 标题阴影选项
+ 修复手机上的一系列小问题
+ 略微优化后台设置界面

## 20200310 v0.884
+ 增加夜间模式的另一种配色: 暗黑 (AMOLED Black)
+ 修复夜间模式相关的 BUG

## 20200309 v0.883
+ 修复过渡动画的一个问题

## 20200309 v0.882
+ 修复首页显示说说选项开启后，置顶文章不能正常显示的 BUG

## 20200309 v0.881
+ 修 BUG

## 20200309 v0.880
+ 增加夜间模式切换方案 (默认日间/默认夜间/跟随系统自动切换/根据时间自动切换)
+ 优化性能
+ 修 BUG

## 20200308 v0.873
+ 优化侧栏的搜索体验
+ 修复 Safari 上的渲染问题

## 20200306 v0.872
+ 修复 Safari 上的一系列显示问题
+ 修复点击导航栏时高度跳动的 BUG
+ 略微优化性能

## 20200306 v0.871
+ BUG 修复

## 20200306 v0.870
+ 优化顶栏搜索体验，将搜索框嵌入导航栏中，同时搜索支持 Pjax
+ 增加首页文章和说说同时显示的选项
+ 修复 Safari 上的一个性能问题
+ 增加评论禁用 Markdown 选项
+ 优化手机端阅读体验
+ 手机端浮动按钮增加透明度
+ 修复偶现的 Tooltip 乱码问题
+ 修复手机点击导航栏链接菜单不会自动关闭的问题
+ 修复其他小问题

## 20200303 v0.860
+ 编辑文章界面侧栏增加 "隐藏字数及阅读时间提示 Meta 信息" 选项
+ 优化夜间模式相关逻辑
+ 修复赞赏二维码的显示和过渡动画问题
+ 增加禁用 Pjax 选项
+ 修复 BUG

## 20200229 v0.852
+ 友情链接短代码增加随机顺序可选参数

## 20200228 v0.851
+ 修复手机端侧栏的一系列问题
+ 优化开启公告时手机端的显示效果
+ 优化手机端评论区的阅读体验
+ 增加 `noshortcode` 短代码
+ 优化浮动操作按钮菜单中恢复默认圆角大小按钮的提示

## 20200225 v0.850
+ Argon 选项中增加自定义默认卡片圆角大小设置
+ 浮动操作按钮菜单中增加了自定义圆角大小滑块
+ 优化评论区图片打开的动画曲线
+ 微调 UI 细节
+ 修复代码块和某些插件样式冲突的问题

## 20200223 v0.845
+ 修复以前手滑遗留的在新标签页打开问题

## 20200222 v0.844
+ 默认显示页脚作者信息，在 Argon 设置中增加了隐藏页脚作者信息的选项

## 20200222 v0.843
+ 修复顶栏二级菜单点击时菜单项高度跳动的 BUG
+ 细节修复
+ 删除页脚作者信息，只保留主题名称和链接

## 20200219 v0.842
+ 添加 Mathjax 2，现在有 Mathjax 3 和 2 两个版本可以选择

## 20200217 v0.841
+ 增加 "留言板" 页面模板
+ 修复浮动操作按钮与 Font Awesome 5 的类名冲突兼容问题
+ 修复夜间模式的一个小 BUG
+ 进一步完善 Pjax 逻辑

## 20200215 v0.840
+ 修复开启 "评论作者必须填入姓名和电子邮件地址" 选项后未填写名称无法发送评论的错误
+ 增加隐藏发送评论区中 "作者名称"、"邮件"、"网站" 输入框的选项
+ 增加禁用评论验证码的选项
+ 修复 Pjax 的几个 BUG
+ 完善 Pjax 逻辑，实现了近乎完美的 Pjax 体验
+ 增加 "博客 Banner 副标题" 设置选项，显示在 Banner 标题下方
+ 优化手机端有头图的博文的显示效果
+ 修复暗色滤镜与背景冲突的 BUG
+ 完善了手机端夜间模式的适配
+ 加入 "暂停更新" 选项，位于 "检测更新源" 选项中
+ 加入了 "博文发布时间"、"博文最后修改时间" 短代码
+ 一系列微调和优化

## 20200210 v0.830
+ 增加评论区 Markdown 支持
+ 优化夜间模式在页面刚载入时的体验

## 20200206 v0.820
+ 增加博客背景图片设置选项
+ 增加 沉浸式 Banner (透明) 和 毛玻璃 Banner 选项 来增强背景图片的显示效果

## 20200205 v0.810
+ BUG 修复

## 20200128 v0.800
+ 大幅提升前端加载速度
+ SEO 优化
+ 增加 SEO Description Meta 标签和 Keywords Meta 标签设置选项
+ 增强页面可访问性，优化无障碍体验
+ 修复一些问题
+ 针对打印进行优化

## 20200125 v0.703
+ Github 用户名更换适配 (abc2237512422 -> solstice23)

## 20200125 v0.702
+ 修复图片全屏预览选项关闭后无效的 BUG

## 20200123 v0.701
+ 修复不显示自定义主题色选择器时 js 的执行错误

## 20200123 v0.700
+ 增加前端自定义主题色功能（用户在浮动操作按钮博客设置菜单中可自定义主题色）
+ 问题修复

## 20200121 v0.610
+ 重构切换主题功能
+ 修复 CSS 的一堆问题
+ 修复 Pjax 带 `target="blank"` 属性的 `a` 标签在本页打开的问题
+ 一些小改进

## 20200116 v0.601
+ 进一步适配主题色 (如滚动条颜色，`a` 标签下划线颜色等)

## 20200116 v0.600
+ 增加博客主题色选项，可自定义主题色
+ 增加 SEO Meta 标签
+ 修复 Pjax 的一个 BUG

## 20200105 v0.597
+ 修复之前没发现的一个无关紧要的小问题

## 20200104
+ 更改协议为 GPL V3.0

## 20191231 v0.596
+ 修复设置界面的小问题

## 20191221 v0.595
+ 平滑滚动增加脉冲式滚动的选项 (Edge 式滚动)

## 20191216 v0.594
+ Argon 后台设置增加浮动目录
+ 增加文章目录显示序号选项
+ 修复左侧栏 Tab 的显示问题
+ 修复左侧栏浮动时在特定屏幕尺寸下的显示问题

## 20191214 v0.593
+ 博客设置增加阴影选项
+ 修复界面的一些问题
+ 修复其他的一些小问题
+ 升级 Argon 框架到 1.1.0 版本

## 20191214 v0.592
+ 加入博客设置功能
	+ 位于浮动操作按钮栏
	+ 设置选项：夜间模式、字体（衬线/无衬线）、页面滤镜
	+ 默认关闭浮动操作按钮栏的夜间模式切换按钮（与设置菜单中重复），可以在 Argon 设置中手动开启
+ 微调 CSS
+ 其他小改动

## 20191204 v0.591
+ 增加进入文章过渡动画选项（测试）

## 20191111 v0.590
+ 增加博客公告功能

## 20191107 v0.582
+ 修复未开启 Mathjax 选项时 Pjax 错误的问题

## 20191104 v0.581
+ 支持切换主题更新源
+ 修复 CSS 一个小问题

## 20191104 v0.58
+ 优化设置页面
+ 修复评论框高度错误问题

## 20191029 v0.57
+ 增加 题图(特色图片) 的支持

## 20191026 v0.56
+ 提升 Mathjax 版本到 3.0
+ 更换默认 Mathjax CDN
+ 允许自定义 Mathjax CDN
+ 修复由于 Mathjax 文件未加载成功导致 Pjax 错误的问题

## 20191023 v0.55
+ 修复手机端侧栏的小问题
+ 提升后台管理中"Argon 主题选项"菜单层级
+ 采用新的检测更新库，修复更新问题
+ 其他细节调整

## 20191017 v0.54
+ 修改手机端侧栏效果
+ 合并 CSS 文件
+ 细节微调
+ 修改加密博客阅读量统计逻辑

## 20191014 v0.53
+ 增加赞赏二维码选项
+ 增加视频短代码
+ 修改 Pjax 逻辑
+ 增加首页文章浏览不显示短代码选项
+ 修复夜间模式的一个小问题

## 20191013 v0.52
+ 增加安装统计
+ 增加时区修复

## 20191012 v0.51
+ "说说"增加点赞功能
+ 微调弹出提示的样式

## 20191010 v0.5
+ 增加 "说说" 功能
+ 增加 Github Repo 信息短代码
+ 细节修改

## 20190923 v0.4
+ 如果某个菜单没配置，会默认隐藏，不再会影响观感
+ 修复了检测更新的一个问题
+ 增加"隐藏文字"短代码，在鼠标移上时才会显示
+ 修复图片放大模糊的问题
+ Banner 支持必应每日一图
+ 适配 Android Chrome Toolbar 颜色
+ 待审核评论会打上标签提示发送者
+ 修复 Pjax 加载后评论框大小不随内容调整的 BUG
+ 夜间模式全屏放大图片图片颜色不会变暗了
+ 修复了 CSS 的一些问题
+ 修复其他一些小问题

## 20190907 v0.31
+ 修复调试时遗留下来的一个 BUG

## 20190904 v0.3
+ Pjax 加载时替换 WordPress Adminbar
+ 修复后台评论提示验证码错误问题
+ 手机减小文章页面 margin
+ Pjax 加载逻辑修改
+ 博主评论免验证码

## 20190829 v0.2
+ 修复一些 BUG
+ checkbox 增加可选的 `inline` 属性
+ 针对 Wordpress 管理条进行处理
+ 修复夜间模式的一些问题
+ 修改一些细节

## 捐赠
如果你觉得 Argon 主题不错，可以请我一杯咖啡来支持我的开发。

![微信捐赠码](https://img.solstice23.top/2020/03/07/fc4b804bf938b.png)