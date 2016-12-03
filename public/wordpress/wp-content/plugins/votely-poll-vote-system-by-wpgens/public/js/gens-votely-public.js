// Copyright (c) 2012 Florian H., https://github.com/js-coder https://github.com/js-coder/cookie.js
!function(e,t){var n=function(){return n.get.apply(n,arguments)},r=n.utils={isArray:Array.isArray||function(e){return Object.prototype.toString.call(e)==="[object Array]"},isPlainObject:function(e){return!!e&&Object.prototype.toString.call(e)==="[object Object]"},toArray:function(e){return Array.prototype.slice.call(e)},getKeys:Object.keys||function(e){var t=[],n="";for(n in e)e.hasOwnProperty(n)&&t.push(n);return t},escape:function(e){return String(e).replace(/[,;"\\=\s%]/g,function(e){return encodeURIComponent(e)})},retrieve:function(e,t){return e==null?t:e}};n.defaults={},n.expiresMultiplier=86400,n.set=function(n,i,s){if(r.isPlainObject(n))for(var o in n)n.hasOwnProperty(o)&&this.set(o,n[o],i);else{s=r.isPlainObject(s)?s:{expires:s};var u=s.expires!==t?s.expires:this.defaults.expires||"",a=typeof u;a==="string"&&u!==""?u=new Date(u):a==="number"&&(u=new Date(+(new Date)+1e3*this.expiresMultiplier*u)),u!==""&&"toGMTString"in u&&(u=";expires="+u.toGMTString());var f=s.path||this.defaults.path;f=f?";path="+f:"";var l=s.domain||this.defaults.domain;l=l?";domain="+l:"";var c=s.secure||this.defaults.secure?";secure":"";e.cookie=r.escape(n)+"="+r.escape(i)+u+f+l+c}return this},n.remove=function(e){e=r.isArray(e)?e:r.toArray(arguments);for(var t=0,n=e.length;t<n;t++)this.set(e[t],"",-1);return this},n.empty=function(){return this.remove(r.getKeys(this.all()))},n.get=function(e,n){n=n||t;var i=this.all();if(r.isArray(e)){var s={};for(var o=0,u=e.length;o<u;o++){var a=e[o];s[a]=r.retrieve(i[a],n)}return s}return r.retrieve(i[e],n)},n.all=function(){if(e.cookie==="")return{};var t=e.cookie.split("; "),n={};for(var r=0,i=t.length;r<i;r++){var s=t[r].split("=");n[decodeURIComponent(s[0])]=decodeURIComponent(s[1])}return n},n.enabled=function(){if(navigator.cookieEnabled)return!0;var e=n.set("_","_").get("_")==="_";return n.remove("_"),e},typeof define=="function"&&define.amd?define(function(){return n}):typeof exports!="undefined"?exports.cookie=n:window.cookie=n}(document);

(function($) {
    'use strict';

    // Share Shortcode
    $.fn.votelySocShare = function(opts) {
        var $this = this;
        var $win = $(window);

        opts = $.extend({
            attr: 'href',
            facebook: false,
            google_plus: false,
            twitter: false,
            linked_in: false,
            pinterest: false,
            whatsapp: false
        }, opts);

        for (var opt in opts) {

            if (opts[opt] === false) {
                continue;
            }

            switch (opt) {
                case 'facebook':
                    var url = 'https://www.facebook.com/sharer/sharer.php?u=';
                    var name = 'Facebook';
                    _popup(url, name, opts[opt], 400, 640);
                    break;

                case 'twitter':
                    var posttitle = $(".gens_votely_tw").data("title");
                   //  var via = $(".gens_votely_tw").data("via");
                    var url = 'https://twitter.com/intent/tweet?text=' + posttitle + '&url=';
                    var name = 'Twitter';
                    _popup(url, name, opts[opt], 440, 600);
                    break;

                case 'google_plus':
                    var url = 'https://plus.google.com/share?url=';
                    var name = 'Google+';
                    _popup(url, name, opts[opt], 600, 600);
                    break;
                default:
                    break;
            }
        }

        function _popup(url, name, opt, height, width) {
            if (opt !== false && $this.find(opt).length) {
                $this.on('click', opt, function(e) {
                    e.preventDefault();

                    var top = (screen.height / 2) - height / 2;
                    var left = (screen.width / 2) - width / 2;
                    var share_link = $(this).attr(opts.attr);


                    if (name != "whatsapp") {
                        window.open(
                            url + encodeURIComponent(share_link),
                            name,
                            'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=' + height + ',width=' + width + ',top=' + top + ',left=' + left
                        );
                    } else {
                        return false;
                    }

                    return false;
                });
            }
        }
        return;
    };

    $(document).ready(function() {

        //Update text
        $('.gens_answers').on('mouseenter', 'a', function() {
            if (!$(".gens_thanks").is(":visible")) {
                var vote_text = $(".gens_answers").data("vote");
                $(this).find('.gens_answer').text(vote_text);
            }
        });
        $('.gens_answers').on('mouseleave', 'a', function() {
            var prev_text = $(this).find(".gens_answer").data("text");
            $(this).find('.gens_answer').text(prev_text);
        });

        //Ajax Add Votes
        $(".gens_answers").on("click", "a", function(e) {
            e.preventDefault();
            var $this = $(this);
            var choice = $this.data("choice");

            // Ajax
            $.ajax({
                url: admin_urls.admin_ajax,
                type: 'post',
                data: {
                    'postNonce': admin_urls.postNonce,
                    'action': 'update_votes',
                    'post_id': admin_urls.post_id,
                    'choice': choice
                },
                success: function(data) {
                    // var string = JSON.stringify(data);
                    if (data.update) {
                        $(".gens_thanks").show();
                        var $prev_val = parseInt($this.find(".gens_votes").text());
                        $this.find(".gens_votes").text($prev_val + 1);
                        cookie.set("gens_votely_" + admin_urls.post_id, "voted", {
                            expires: 365,
                            path: '/'
                        });
                    } else {
                        $(".gens_thanks").text(data.msg).show();
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });

        //Share
        $('.gens_votely_share_icon').votelySocShare({
            facebook: '.gens_votely_fb',
            twitter: '.gens_votely_tw',
        });

    });

})(jQuery);
