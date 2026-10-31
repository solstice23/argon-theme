![Argon](https://cdn.jsdelivr.net/gh/solstice23/cdn@master/argon_new_animate.svg)

**简体中文** | [繁體中文](README_tw.md) | [English](README_en.md) | [Russian](README_ru.md)

# Argon-Theme

Argon - 一个轻盈、简洁、美观的 WordPress 主题

Hexo 版本 : [github.com/solstice23/hexo-theme-argon](https://github.com/solstice23/hexo-theme-argon)

[![GitHub release](https://img.shields.io/github/v/release/solstice23/argon-theme?color=%235e72e4&style=for-the-badge)](https://github.com/solstice23/argon-theme/releases) [![GitHub All Releases](https://img.shields.io/github/downloads/solstice23/argon-theme/total?style=for-the-badge)](https://github.com/solstice23/argon-theme/releases) [![GitHub](https://img.shields.io/github/license/solstice23/argon-theme?color=blue&style=for-the-badge)](https://github.com/solstice23/argon-theme/blob/master/LICENSE) [![Author]( https://img.shields.io/badge/author-solstice23-yellow?style=for-the-badge)](https://github.com/solstice23) [![GitHub stars](https://img.shields.io/github/stars/solstice23/argon-theme?color=ff69b4&style=for-the-badge)](https://github.com/solstice23/argon-theme/stargazers)

[![GitHub last commit](https://img.shields.io/github/last-commit/solstice23/argon-theme?style=flat-square)](https://github.com/solstice23/argon-theme/commits/master) [![GitHub Release Date](https://img.shields.io/github/release-date/solstice23/argon-theme?style=flat-square)](https://github.com/solstice23/argon-theme/releases) ![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/solstice23/argon-theme?style=flat-square)

# 状态

> 前端工程化重构初步完成，1.x 版本将冻结开发，如有前端相关的 PR 请提交到 [dev](https://github.com/solstice23/argon-theme/tree/dev) 分支。后续将逐渐重构前端代码。

# 特性

+ **轻盈美观** - 使用 Argon Design System 前端框架，细节精致，轻盈美观
+ **高度可定制化** - 可自定义主题色、布局(双栏/单栏/三栏)、顶栏、侧栏、Banner、背景图、日夜间模式不同背景、背景沉浸、浮动操作按钮等，提供了丰富的自定义选项
+ **夜间模式** - 支持日间、夜间、纯黑三种模式，并可以根据时间自动切换或跟随系统夜间模式
+ **功能繁多** - Tag 和分类统计、作者链接、额外链接、文章字数和预计阅读时间、文章过时信息显示
+ **Pjax** - 支持 Pjax 无刷新加载，提高浏览体验
+ **友情链接** - 支持使用 Wordpress 自带的链接管理器进行友链管理，支持多种友链样式
+ **"说说" 功能** - 随时发表想法，并在专门的 "说说" 页面展示，也支持说说和首页文章穿插
+ **评论功能扩展** - Ajax 评论，评论支持 Markdown、验证码、再次编辑、显示 UA、悄悄话模式、回复时邮件通知、查看编辑记录、无限加载等功能
+ **诸多功能** - 文章目录、阅读进度、Mathjax 或 Katex 公式解析、图片放大预览、Pangu.js 文本格式化、平滑滚动等
+ **丰富的短代码** - 支持通过短代码在文章中插入 TODO、标签、警告、提示、折叠区块、Github 信息卡、时间线、隐藏文本、视频等模块
+ **适配 Gutenberg 编辑器** - 支持使用 Gutenberg 编辑器可视化插入区块
+ **多语言** - 支持中文、英文、俄文等语言
+ **其他** - 自适应、精心优化的文章阅读界面 CSS、可切换衬线/非衬线字体、可自定义 CSS 和 JS、支持使用 CDN 加速静态文件访问、SEO 友好、Banner 打字动画、留言板页面、文章脚注等

# 现代化改造与 WordPress 官方核心依赖接入

针对现代 WordPress（6.x+）运行环境及 PHP 8.1~8.3 运行时，本分支完成了以下架构规范化与安全性加固改造：

### 1. 全面接入 WordPress 官方核心依赖
- **核心脚本绑定**：显式依赖 WordPress 官方维护的 `jquery`（3.7+）、`clipboard` 与 `wp-i18n` 运行时依赖。
- **国际化（i18n）桥接**：注册 `wp_set_script_translations`，前端脚本优先通过 `window.wp.i18n` 执行多语言翻译并优雅回退，契合官方国际化生态。
- **无缝兼容多插件环境**：在前端打包脚本中增加运行态环境守卫，当 WordPress 官方库存在时自动复用核心实例，杜绝因内置旧版本（jQuery 3.2.1 / 旧版 ClipboardJS）覆盖全局对象引发的第三方插件冲突。

### 2. 生命周期与资源入列规范化
- **标准动作挂钩**：移除模板文件中散落的脚本注入与硬编码引用，统一通过 `wp_enqueue_scripts` 钩子按依赖树规范加载静态资源。
- **后台与登录页隔离**：登录页样式规范挂载至 `login_enqueue_scripts`；Gutenberg 区块资源改用主题标准 API（`get_theme_file_uri` / `get_theme_file_path`）进行加载与国际化绑定。

### 3. 安全防护与鉴权加固
- **AJAX 权限与白名单控制**：
  - 置顶评论（`pin_comment`）：加入管理员鉴权与参数校验，移除未登录暴露接口。
  - 文章 Meta 异步更新（`update_post_meta_ajax`）：接入 `current_user_can('edit_post', $post_id)` 权限检验，并采用严格 Meta 字段白名单过滤，阻止任意字段修改。
  - 主题设置更新：补充 `current_user_can('edit_theme_options')` 鉴权与 Nonce 有效性验证。
- **输出转义与 XSS 防护**：对评论列表、文章 Banner、属性 URL 及表单字段应用 `esc_attr()` 与 `esc_url()` 转义。
- **重构语义化搜索表单**：符合 HTML5 规范与 Web 可访问性（a11y）标准，采用原生提交动作与安全 URL 转义。

### 4. PHP 8.x 兼容与性能提升
- **消除运行时弃用警告**：解决空 Cookie / 空参数传递至 `strlen()` 等字符串函数触发的 PHP 8.1+ 弃用警告；改用 WordPress 原生 `is_email()` 校验邮箱。
- **Y2038 时间戳溢出防护**：点赞持久化过期时间改用官方标准常量 `YEAR_IN_SECONDS`，规避 32 位整型溢出风险。
- **消除前台磁盘 I/O 与网络阻塞**：
  - 版权校验限制在后台管理面板（`is_admin()`）按需执行，降低前台并发访问时的磁盘读取压力。
  - 外链与分析请求改用 `wp_remote_get()` 并在后台分发，设置 2 秒硬超时，杜绝网络异常阻塞前台渲染。
- **邮件退订解耦**：改用 WordPress 标准 `query_vars` 与 `template_redirect` 重写机制，消除直接访问主题内部 PHP 文件的架构弊端。

# 安装

在 [Release](https://github.com/solstice23/argon-theme/releases) 页面下载 .zip 文件，在 WordPress 后台 "主题" 页面上传并安装。

如果你从 GitHub 仓库源码安装，需要先运行 `composer install` 安装依赖。详细安装说明请查看 [INSTALL.md](./INSTALL.md)。

# 文档

[Argon-Theme 文档 : https://argon-docs.solstice23.top](https://argon-docs.solstice23.top/)

# Demo / 用户墙

[solstice23.top](https://solstice23.top)

[argon-demo.solstice23.top](http://argon-demo.solstice23.top)

前往 [Argon 用户墙](https://argon-showcase.solstice23.top/) 查看更多博客的主题效果。

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

# Telegram 频道
[t.me/argontheme](https://t.me/argontheme)

自动推送更新消息以及其他关于 Argon 的消息

# 更新日志

[查看更新日志](./CHANGLOG.md)

# 贡献指南

**Bugs**

我们使用 GitHub [Issues](https://github.com/solstice23/argon-theme/issues/new/choose) 来做 bug 追踪。

**注意：** 在你报告一个问题之前，请先确保已经搜索过已有的 [issue](https://github.com/solstice23/argon-theme/issues) 和阅读了我们的 [文档](https://argon-docs.solstice23.top/) 尤其是 [常见问题](https://argon-docs.solstice23.top/#/faq) 。

**新功能**

如果你有改进或者新增功能的想法，我们推荐你在 [Discussions](https://github.com/solstice23/argon-theme/discussions) 发起讨论。

欢迎以 Pull Request 的形式对 Argon 作出贡献，详情请查阅 [贡献指南](https://argon-docs.solstice23.top/#/contribute-guide) 。

# 捐赠

如果你觉得 Argon 主题不错，可以请我一杯咖啡来支持我的开发。

![微信捐赠码](https://img.solstice23.top/2020/03/07/fc4b804bf938b.png)
