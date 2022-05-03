# 贡献指南

## Bugs

我们使用 GitHub [Issues](https://github.com/solstice23/argon-theme/issues/new/choose) 来做 bug 追踪。 如果你想要你发现的 bug 被快速解决，请使用这个 模板 来报告。

在你报告一个 bug 之前，请先确保已经搜索过已有的 [issue](https://github.com/solstice23/argon-theme/issues) 和阅读了我们的 [文档](https://argon-docs.solstice23.top/) 及 [常见问题](https://argon-docs.solstice23.top/#/faq) 。

## 新增功能

如果你有改进或者新增功能的想法，我们推荐你在 [Discussions](https://github.com/solstice23/argon-theme/discussions) 发起讨论。

## 翻译

> WIP

## 页面结构

```php single.php
    get_header();  //header.php
    get_side_bar();  //sidebar.php

        do_action('argon_content'); //template-parts/content.php
            do_action('argon_entry_header'); //template-parts/entry/header.php
                do_action('argon_entry_title'); //template-parts/entry/title.php
                do_action('argon_entry_meta'); //template-parts/entry/meta.php

            do_action('argon_before_entry_content'); 
            do_action('argon_entry_content'); //template-parts/entry/content.php
                do_action('argon_entry_passwd_required'); //template-parts/entry/passwd_required.php
                the_content(); //template-parts/entry/meta.php
            do_action('argon_after_entry_content'); 
            
            do_action('argon_entry_footer'); //template-parts/entry/footer.php
                do_action('argon_entry_donate'); //template-parts/entry/donate.php
                do_action('argon_entry_tag'); //template-parts/entry/tag.php

        do_action( 'argon_show_sharebtn' );  //template-parts/share.php
        do_action( 'argon_show_comment' );  //comment.php
        do_action( 'argon_post_navigation' );  //template-parts/post-navigation.php
        do_action( 'argon_related_post' );  //template-parts/related.php

    get_footer();  //footer.php

```