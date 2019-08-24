;(function ($, window) {
    var headIndex = (function () {
        function headIndex(element, options) {
            this.settings = $.extend(true, $.fn.headIndex.default, options || {});
            this.element = element;
            this.init();
        }

        headIndex.prototype = {
            init: function () {
                this.articleWrap = $(this.settings.articleWrapSelector);
                this.headerList = this.articleWrap.find(':header');
                this.indexBox = $(this.settings.indexBoxSelector);
                this.scrollBody = $(this.settings.scrollSelector);
                this.scrollWrap = $(this.settings.scrollWrap);
                this.manual = false;

                if (this.indexBox.length === 0 || this.headerList.length === 0) {
                    return null;
                }

                this.initHeader();
                this.event();
            },

            initHeader: function () {
                for (var i = 0; i < this.headerList.length; i++, this.autoId++) {
                    //文章header添加id和计算高度
                    this.headerList[i].id = this.headerList[i].id || "header-id-" + this.autoId;
                    this.headerList[i].topHeight = this.offsetTop(this.headerList[i]);
                    this.headerList[i].h = Number(this.headerList[i].tagName.charAt(1));
                }

                this.tempHtml = [];
                this.buildHtml(this.buildTree());

                var res = '<ul>' + this.tempHtml.join('') + '</ul>';
                this.indexBox.html(res);
            },

            event: function () {
                var that = this;
                var manualValTimer = null;
                this.indexBox.on('click.headindex', function (event) {
                    var target = $(event.target);
                    if (target.hasClass(that.settings.linkClass)) {
                        event.preventDefault();
                        var indexItem = target.parent('.' + that.settings.itemClass);

                        //手动点击的时候，屏蔽滑动响应
                        that.manual = true;
                        if (manualValTimer) {
                            clearTimeout(manualValTimer);
                            manualValTimer = null;
                        }
                        manualValTimer = setTimeout(function () {
                            that.manual = false;
                        }, 300);
                        that.current(indexItem);

                        //滚动到当前的标题
                        that.scrollTo(event.target.getAttribute('href'))
                    }
                });

                //滑动监听
                $(this.scrollWrap).scroll(function () {
                    if (that.manual) return;
                    that.updateCurrent();
                });

                //默认选中当前滑动的位置
                that.updateCurrent();
            },

            updateTopHeight: function () {
                var length = this.headerList.length;
                var i;
                if (length === 0) return;

                //第一个和最后一个标题的高度都没有发生变化，默认整体高度没变
                if (this.headerList[0].topHeight === this.offsetTop(this.headerList[0])
                    && this.headerList[length - 1].topHeight === this.offsetTop(this.headerList[length - 1])) {
                    return;
                }

                //第一个和最后一个标题的高度变化的差值相同，默认整体标题的变化差值相同
                if ((this.headerList[0].topHeight - this.offsetTop(this.headerList[0]))
                    === (this.headerList[length - 1].topHeight - this.offsetTop(this.headerList[length - 1]))) {

                    var hx = this.offsetTop(this.headerList[0]) - this.headerList[0].topHeight;
                    for (i = 0; i < this.headerList.length; i++, this.autoId++) {
                        this.headerList[i].topHeight += hx;
                    }
                    return;
                }

                //其他变化，整体进行重新计算和赋值
                for (i = 0; i < this.headerList.length; i++, this.autoId++) {
                    this.headerList[i].topHeight = this.offsetTop(this.headerList[i]);
                }
            },

            current: function (indexItem) {
                var subBox,
                    currentClass = 'current';

                if (indexItem.length === 0 || indexItem.hasClass(currentClass)) {
                    return;
                }
                //移除其他位置的current类
                var otherCurrent = this.indexBox.find('li.' + currentClass);
                if (otherCurrent.length > 0) {
                    otherCurrent.removeClass(currentClass);
                }
                //先清除全部的open标记
                this.indexBox.find('ul.open').removeClass('open');

                //打开当前下级别的subItemBox
                subBox = indexItem.children('.' + this.settings.subItemBoxClass);
                if (subBox.length > 0) {
                    subBox.addClass('open').slideDown();
                }
                //为了应对非常快速滑动的时候，scroll函数略过父级的box
                var parentsBox = indexItem.parents('ul.' + this.settings.subItemBoxClass);
                if (parentsBox.length > 0) {
                    parentsBox.addClass('open').slideDown()
                }
                //关闭其他位置打开的subItemBox 排除当前父级上的subItemBox
                subBox = this.indexBox.find('ul.' + this.settings.subItemBoxClass).not('.open');
                if (subBox.length > 0) {
                    subBox.slideUp()
                }
                //为当前添加current类
                indexItem.addClass(currentClass);
            },

            buildHtml: function (tree) {
                if (tree === undefined || tree.length === 0) return;

                for (var i = 0; i < tree.length; i++) {
                    this.tempHtml.push("<li class='" + this.settings.itemClass + "'>"
                        + "<a no-pjax class='" + this.settings.linkClass + "' href='#" + tree[i].item.id + "'>"
                        + tree[i].item.innerText + "</a>");

                    if (tree[i].children.length !== 0) {
                        this.tempHtml.push("<ul class='" + this.settings.subItemBoxClass + "'>");
                        this.buildHtml(tree[i].children);
                        this.tempHtml.push("</ul>");
                    }
                    this.tempHtml.push("</li>")
                }
            },

            buildTree: function () {
                var current = null,
                    tempCur,
                    indexTree = [];

                for (var i = 0; i < this.headerList.length; i++) {
                    if (current == null) {
                        current = {item: this.headerList[i], parent: null, children: [],};
                        indexTree.push(current);
                        continue;
                    }
                    if (current.item.h < this.headerList[i].h) {
                        tempCur = {item: this.headerList[i], parent: current, children: [],};
                        current.children.push(tempCur);
                        current = tempCur;
                        continue;
                    }
                    if (current.item.h === this.headerList[i].h) {
                        tempCur = {item: this.headerList[i], parent: current.parent, children: [],};
                        ((current.parent && current.parent.children) || indexTree).push(tempCur);
                        current = tempCur;
                        continue;
                    }
                    while (current != null && current.item.h > this.headerList[i].h) {
                        current = current.parent;
                    }
                    if (current == null) {
                        current = {item: this.headerList[i], parent: null, children: [],};
                        indexTree.push(current);
                        continue;
                    }
                    i--;
                }

                return indexTree;
            },
            search: function (start, end, findValue) {
                if (this.headerList.length === 0) return null;

                if (end - start <= 1) {
                    if (this.headerList[end].topHeight < findValue) {
                        return this.headerList[end];
                    }
                    return this.headerList[start];
                }

                if (start < end) {
                    var middleIndex = parseInt((start + end) / 2);
                    var middleValue = this.headerList[middleIndex].topHeight;
                    if (findValue < middleValue) {
                        end = middleIndex;
                    } else if (findValue > middleValue) {
                        start = middleIndex
                    } else {
                        return this.headerList[middleIndex];
                    }
                    return this.search(start, end, findValue)
                }
            },

            /**
             * 计算每个标题距离文档顶部的垂直高度，
             * 为了应对不同需求，如果计算不准确，可以修改这个方法
             * @param elem
             * @returns {number}
             */
             /*已重写该方法*/
            offsetTop: function (elem) {
                return $(elem).offset().top - this.settings.offset;
                /*var wrapTop = this.articleWrap[0].getBoundingClientRect().top
                var eTop = elem.getBoundingClientRect().top

                return parseInt(eTop - wrapTop - this.settings.offset)*/

                // var rect, win;
                // if (!elem) {
                //     return;
                // }
                // if (!elem.getClientRects().length) {
                //     return {top: 0, left: 0};
                // }
                //
                // rect = elem.getBoundingClientRect();
                // win = elem.ownerDocument.defaultView;
                // return parseInt(rect.top + win.pageYOffset);
            },
            /**
             * 滑动到指定id选择器的标题
             * @param eid 标题的id值
             */
            scrollTo: function (eid) {
                this.scrollBody.stop().animate({
                    scrollTop: this.offsetTop(document.querySelector(eid))
                }, 'fast');
            },
            /**
             * 更新当前位置
             */
            updateCurrent: function () {
                var scrollTop = this.scrollBody.scrollTop();
                this.updateTopHeight();

                var find = this.search(0, this.headerList.length - 1, scrollTop);//
                if (!find) {
                    return;
                }

                var indexItem = this.indexBox
                    .find('a[href="#' + find.id + '"]')
                    .parent('li.' + this.settings.itemClass);

                this.current(indexItem);
            },
            /**
             * 清除缓存对象
             */
            clean: function () {
                if (this.element) {
                    this.element.data("headIndex", null)
                }
            },
            /**
             * 临时忽略滚动监听
             */
            ignoreScrollEvent: function (ignore) {
                if (ignore) {
                    this.manual = true;
                } else {
                    this.manual = false;
                    this.updateCurrent();
                }
            }
        };

        headIndex.prototype.autoId = 1;

        return headIndex;
    })();

    $.fn.headIndex = function (options) {
        return this.each(function () {
            var $this = $(this),
                instance = $this.data("headIndex");
                /*此处为适配 pjax 注释了下面的 if*/
            /*if (!instance) {*/
                instance = new headIndex($this, options);
                $this.data("headIndex", instance);
            /*}*/
            if ($.type(options) === "string") return instance[options]();
        });
    };

    //-----------------------------------
    // 默认参数
    //-----------------------------------
    $.fn.headIndex.default = {
        articleWrapSelector: ".article-wrap",/*包裹文章的选择器*/
        indexBoxSelector: ".index-box",/*包裹目录索引的选择器*/
        scrollSelector: 'body,html',/*滑动元素的选择器*/
        scrollWrap: window,/*能够监听到scrollSelector滑动的选择器*/
        subItemBoxClass: "index-subItem-box",
        itemClass: "index-item",
        linkClass: "index-link",
        offset: 0,/*滑动偏移量 按需求进行偏移*/
    }
})(jQuery, window);
