/**
 * Zoomify
 * A jQuery plugin for simple lightboxes with zoom effect.
 * http://indrimuska.github.io/zoomify
 *
 * (c) 2015 Indri Muska - MIT
 */
;(function($){
	
	// initialization
	Zoomify = function (element, options) {
		var that = this;
		
		this._zooming = false;
		this._zoomed  = false;
		this._timeout = null;
		this.$shadow  = null;
		this.$image   = $(element).addClass('zoomify');
		this.options  = $.extend({}, Zoomify.DEFAULTS, this.$image.data(), options);
		
		this.$image.on('click', function () { that.zoom(); });
		$(window).on('resize', function () { that.reposition(); });
		$(document).on('scroll', function () { that.reposition(); });
		$(window).on('keyup', function (e) { 
			if (that._zoomed && e.keyCode == 27)
				that.zoomOut();
		 });
	};
	Zoomify.DEFAULTS = {
		duration: 200,
		easing:   'linear',
		scale:    0.9
	};
	
	// css utilities
	Zoomify.prototype.transition = function ($element, value) {
		$element.css({
			'-webkit-transition': value,
			'-moz-transition': value,
			'-ms-transition': value,
			'-o-transition': value,
			'transition': value
		});
	};
	Zoomify.prototype.addTransition = function ($element) {
		this.transition($element, 'all ' + this.options.duration + 'ms ' + this.options.easing);
	};
	Zoomify.prototype.removeTransition = function ($element, callback) {
		var that = this;
		
		clearTimeout(this._timeout);
		this._timeout = setTimeout(function () {
			that.transition($element, '');
			if ($.isFunction(callback)) callback.call(that);
		}, this.options.duration);
	};
	Zoomify.prototype.transform = function (value) {
		this.$image.css({
			'-webkit-transform': value,
			'-moz-transform': value,
			'-ms-transform': value,
			'-o-transform': value,
			'transform': value
		});
	};
	Zoomify.prototype.transformScaleAndTranslate = function (scale, translateX, translateY, callback) {
		this.addTransition(this.$image);
		this.transform('scale(' + scale + ') translate(' + translateX + 'px, ' + translateY + 'px)');
		this.removeTransition(this.$image, callback);
	};
	
	// zooming functions
	Zoomify.prototype.zoom = function () {
		if (this._zooming) return;
		
		if (this._zoomed) this.zoomOut();
		else this.zoomIn();
	};
	Zoomify.prototype.zoomIn = function () {
		var that      = this,
			transform = this.$image.css('transform');
		
		this.transition(this.$image, 'none');
		this.transform('none');
		
		var offset     = this.$image.offset(),
			width      = this.$image.outerWidth(),
			height     = this.$image.outerHeight(),
			nWidth     = this.$image[0].naturalWidth || +Infinity,
			nHeight    = this.$image[0].naturalHeight || +Infinity,
			wWidth     = $(window).width(),
			wHeight    = $(window).height(),
			scaleX     = Math.min(nWidth, wWidth * this.options.scale) / width,
			scaleY     = Math.min(nHeight, wHeight * this.options.scale) / height,
			scale      = Math.min(scaleX, scaleY),
			translateX = (-offset.left + (wWidth - width) / 2) / scale,
			translateY = (-offset.top + (wHeight - height) / 2 + $(document).scrollTop()) / scale;
		
		this.transform(transform);
		
		this._zooming = true;
		$("body").addClass("noscroll");
		this.$image.addClass('zoomed').trigger('zoom-in.zoomify');
		setTimeout(function () {
			that.addShadow();
			that.transformScaleAndTranslate(scale, translateX, translateY, function () {
				that._zooming = false;
				that.$image.trigger('zoom-in-complete.zoomify');
			});
			that._zoomed = true;
		});
	};
	Zoomify.prototype.zoomOut = function () {
		var that = this;
		
		this._zooming = true;
		this.$image.trigger('zoom-out.zoomify');
		this.transformScaleAndTranslate(1, 0, 0, function () {
			that._zooming = false;
			$("body").removeClass("noscroll");
			that.$image.removeClass('zoomed').trigger('zoom-out-complete.zoomify');
		});
		this.removeShadow();
		this._zoomed = false;
	};
	
	// page listener callbacks
	Zoomify.prototype.reposition = function () {
		if (this._zoomed) {
			this.transition(this.$image, 'none');
			this.zoomIn();
		}
	};
	
	// shadow background
	Zoomify.prototype.addShadow = function () {
		var that = this;
		if (this._zoomed) return;
		
		if (that.$shadow) that.$shadow.remove();
		this.$shadow = $('<div class="zoomify-shadow"></div>');
		$('body').append(this.$shadow);
		this.addTransition(this.$shadow);
		this.$shadow.on('click', function () { that.zoomOut(); })
		
		setTimeout(function () { that.$shadow.addClass('zoomed'); }, 10);
	};
	Zoomify.prototype.removeShadow = function () {
		var that = this;
		if (!this.$shadow) return;
		
		this.addTransition(this.$shadow);
		this.$shadow.removeClass('zoomed');
		this.$image.one('zoom-out-complete.zoomify', function () {
			if (that.$shadow) that.$shadow.remove();
			that.$shadow = null;
		});
	};
	
	// plugin definition
	$.fn.zoomify = function (option) {
		return this.each(function () {
			var $this   = $(this),
				zoomify = $this.data('zoomify');
			
			if (!zoomify) $this.data('zoomify', (zoomify = new Zoomify(this, typeof option == 'object' && option)));
			if (typeof option == 'string' && ['zoom', 'zoomIn', 'zoomOut', 'reposition'].indexOf(option) >= 0) zoomify[option]();
		});
	};
	
})(jQuery);