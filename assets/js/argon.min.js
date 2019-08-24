/*!

=========================================================
* Argon Design System - v1.0.1
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-design-system
* Copyright 2018 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://github.com/creativetimofficial/argon-design-system/blob/master/LICENSE.md)

* Coded by www.creative-tim.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

*/
"use strict";$(document).ready(function(){($(".navbar-main .collapse").on("hide.bs.collapse",function(){$(this).addClass("collapsing-out")}),$(".navbar-main .collapse").on("hidden.bs.collapse",function(){$(this).removeClass("collapsing-out")}),$(".navbar-main .dropdown").on("hide.bs.dropdown",function(){var e=$(this).find(".dropdown-menu");e.addClass("close"),setTimeout(function(){e.removeClass("close")},200)}),$(".headroom")[0])&&new Headroom(document.querySelector("#navbar-main"),{offset:300,tolerance:{up:30,down:30}}).init();if($(".datepicker")[0]&&$(".datepicker").each(function(){$(".datepicker").datepicker({disableTouchKeyboard:!0,autoclose:!1})}),$('[data-toggle="tooltip"]').tooltip(),$('[data-toggle="popover"]').each(function(){var e="";$(this).data("color")&&(e="popover-"+$(this).data("color")),$(this).popover({trigger:"focus",template:'<div class="popover '+e+'" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>'})}),$(".form-control").on("focus blur",function(e){$(this).parents(".form-group").toggleClass("focused","focus"===e.type||0<this.value.length)}).trigger("blur"),$(".input-slider-container")[0]&&$(".input-slider-container").each(function(){var e=$(this).find(".input-slider"),t=e.attr("id"),a=e.data("range-value-min"),n=e.data("range-value-max"),o=$(this).find(".range-slider-value"),r=o.attr("id"),i=o.data("range-value-low"),l=document.getElementById(t),d=document.getElementById(r);noUiSlider.create(l,{start:[parseInt(i)],connect:[!0,!1],range:{min:[parseInt(a)],max:[parseInt(n)]}}),l.noUiSlider.on("update",function(e,t){d.textContent=e[t]})}),$("#input-slider-range")[0]){var e=document.getElementById("input-slider-range"),t=document.getElementById("input-slider-range-value-low"),a=document.getElementById("input-slider-range-value-high"),n=[t,a];noUiSlider.create(e,{start:[parseInt(t.getAttribute("data-range-value-low")),parseInt(a.getAttribute("data-range-value-high"))],connect:!0,range:{min:parseInt(e.getAttribute("data-range-value-min")),max:parseInt(e.getAttribute("data-range-value-max"))}}),e.noUiSlider.on("update",function(e,t){n[t].textContent=e[t]})}$('[data-toggle="on-screen"]')[0]&&$('[data-toggle="on-screen"]').onScreen({container:window,direction:"vertical",doIn:function(){},doOut:function(){},tolerance:200,throttle:50,toggleClass:"on-screen",debug:!1}),$('[data-toggle="scroll"]').on("click",function(e){var t=$(this).attr("href"),a=$(this).data("offset")?$(this).data("offset"):0;$("html, body").stop(!0,!0).animate({scrollTop:$(t).offset().top-a},600),e.preventDefault()})});