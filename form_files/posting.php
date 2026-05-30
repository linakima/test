<!DOCTYPE html>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title>Новый релиз</title>

<link rel="stylesheet" href="//static.pornolab.net/templates/default/css/main.css?v=47" type="text/css">
<link rel="stylesheet" href="//static.pornolab.net/templates/default/css/theme.css?v=47" type="text/css">
<link rel="shortcut icon" href="//static.pornolab.net/favicon.ico" type="image/x-icon">

<link rel="search" type="application/opensearchdescription+xml" title="Поиск на Pornolab.net" href="//static.pornolab.net/opensearch.xml">

<link rel="stylesheet" href="//static.pornolab.net/templates/default/css/fontawesome.css" type="text/css">
<link rel="stylesheet" href="//static.pornolab.net/templates/default/css/font-awesome-animation.min.css" type="text/css">

<script type="text/javascript" src="//static.pornolab.net/js/jquery.pack.js?v=28"></script>

<script type="text/javascript" src="//static.pornolab.net/js/main.js?v=28"></script>

<script type="text/javascript" src="//static.pornolab.net/js/bbcode.js?v=28"></script>

<script type="text/javascript">
var postImg_MaxWidth = screen.width - 220;
var postImgAligned_MaxWidth = Math.round(screen.width/3);
var hidePostImg =	false;

jQuery.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ? 
                        matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
}

function initPost(context)
{
	$('span.post-hr', context).html('<hr align="left" />');
	initQuotes(context);
	initExternalLinks(context);
	initPostImages(context);
	initPostVideos(context);
	initSpoilers(context);
}
function initQuotes(context)
{
	$('div.q', context).each(function(){
		var $q = $(this);
		var name = $(this).attr('head');
		$q.before('<div class="q-head"><span>' + (name ? '<b>'+name+'</b> писал(а):' : '<b>Цитата:</b>') +'</span></div>');
		
		var quoted_pid;
		if (quoted_pid = $q.children('u.q-post:first').text()) {
			var href = 'viewtopic.php?p=' + quoted_pid + '#' + quoted_pid;
			$q.siblings('div.q-head').find('span')
				.prepend('<img src="//static.pornolab.net/templates/default/images/icon_latest_reply.gif" class="icon2" alt="->">')
				.wrap('<a href="' + href + '" title="Перейти к цитируемому сообщению"></a>');
		}
	});
}
function initPostImages(context)
{
	if (hidePostImg) return;
	var $in_spoilers = $('div.sp-body var.postImg', context);
	$('var.postImg', context).not($in_spoilers).each(function(){
		var $v = $(this);
		var src = $v.attr('title');
		var $img = $('<img src="'+ src +'" class="'+ $v.attr('class') +'" alt="pic" />');
		$img = fixPostImage($img);
		var maxW = ($v.hasClass('postImgAligned')) ? postImgAligned_MaxWidth : postImg_MaxWidth;
		$img.bind('click', function(){ imgFit(this, maxW); });
		if (user.opt_js.i_aft_l) {
			$('#preload').append($img);
			var loading_icon = '<a href="'+ src +'" target="_blank"><img src="//static.pornolab.net/templates/default/images/loading_3.gif" alt="" /></a>';
			$v.html(loading_icon);
			if ($.browser.msie) {
				$v.after('<wbr>');
			}
			$img.one('load', function(){
				imgFit(this, maxW);
				$v.empty().append(this);
			});
		}
		else {
			$img.one('load', function(){ imgFit(this, maxW) });
			$v.empty().append($img);
			if ($.browser.msie) {
				$v.after('<wbr>');
			}
		}
	});
}
function initPostVideos(context)
{
	if (hidePostImg) return;
	var $in_spoilers = $('div.sp-body var.postVideo', context);
	$('var.postVideo', context).not($in_spoilers).each(function(){
		var $v = $(this);
		var src = $v.attr('title');
		var $vid = $('<video class="'+ $v.attr('class') +'" controls alt="video"><source src="'+ src +'">Ваш браузер не поддерживает просмотр видео</video>');
		$v.empty().append($vid);
	});
}
function initSpoilers(context)
{
	$('div.sp-body', context).each(function(){
		var $sp_body = $(this);
		var name = $.trim(this.title) || 'скрытый текст';

		var picUrlRegexStr = "((http)?s?&#58;(\/\/[^\"'].*?\.(?:png|jpg|jpeg|gif|png|svg)))";
		var picUrlRegex    = new RegExp(picUrlRegexStr, "gi");
		if (name.match(picUrlRegex)) {
			name = name.replaceAll(picUrlRegex, "<var class='postImg sp-pic-var' title='$1'><img src='$1' class='sp-pic' alt='pic'></var>");
		}
		this.title = '';
		var $sp_head = $('<div class="sp-head folded clickable">'+ name +'</div>');
		$sp_head.insertBefore($sp_body).click(function(e){
			if (!$sp_body.hasClass('inited')) {
				initPostImages($sp_body);
				initPostVideos($sp_body);
				var $sp_fold_btn = $('<div class="sp-fold clickable">[свернуть]</div>').click(function(){
					$.scrollTo($sp_head, { duration:200, axis:'y', offset:-200 });
					$sp_head.click().animate({opacity: 0.1}, 500).animate({opacity: 1}, 700);
				});
				$sp_body.prepend('<div class="clear"></div>').append('<div class="clear"></div>').append($sp_fold_btn).addClass('inited');
			}
			if (e.shiftKey) {
				e.stopPropagation();
				e.shiftKey = false;
				var fold = $(this).hasClass('unfolded');
				$('div.sp-head', $($sp_body.parents('td')[0])).filter( function(){ return $(this).hasClass('unfolded') ? fold : !fold } ).click();
			} else {
				$(this).toggleClass('unfolded');
				//$sp_body.slideToggle('fast');
				$sp_body.toggle(50);
			}
		});
	});
}
function initExternalLinks(context)
{
	$('a.postLink:regex(href, pornolab.(lib|biz|cc|net))', context).each(function(){
		$(this).attr({ href: this.href.replace(/pornolab.(lib|biz|cc|net)/g, window.location.hostname)});
	});
	if (document.location.protocol == 'https:') {
	    $("a.postLink[href*='http://pornolab']:not([href*='.lib'])", context).each(function(){ $(this).attr({ href: this.href.replace(/http\:\/\//g, 'https://')}); });
	    $("img[src*='http://static.pornolab']", context).each( function(){ $(this).attr({ src: this.src.replace(/http\:\/\//g, 'https://')}); });
		$("var.postImg[title*='http://static.pornolab']").each( function(){ $(this).attr({ title: this.title.replace(/http\:\/\//g, 'https://')}); });
	}
	if (document.location.protocol == 'http:') {
		$("a.postLink[href*='https://pornolab']:not([href*='.lib'])", context).each(function(){ $(this).attr({ href: this.href.replace(/https\:\/\//g, 'http://')}); });
	}
	if (document.location.hostname == 'pornolab.lib') {
	    $("a.postLink[href*='https://pornolab']", context).each( function(){ $(this).attr({ href: this.href.replace(/https\:\/\//g, 'http://')}); });
	}
	$("a.postLink:not([href*='"+ window.location.hostname +"/'])", context).attr({ target: '_blank' });
	$('img.smile,img.postImg', context)
	    .filter(function() {
			return this.src.match(/http(s|)\:\/\/static.pornolab.(lib|biz|cc|net)/);
		}).each(function() {
			$(this).attr({ src: this.src.replace(/static.pornolab.(lib|biz|cc|net)/g, 'static.' + window.location.hostname) });
	    });
	$('var.postImg', context)
		.filter(function() {
				return this.title.match(/http(s|)\:\/\/static.pornolab.(lib|biz|cc|net)/);
		}).each(function() {
				$(this).attr({ title: this.title.replace(/static.pornolab.(lib|biz|cc|net)/g, 'static.' + window.location.hostname)
		});
	});
}
function fixPostImage ($img)
{
 var banned_image_hosts = /imagebanana|hidebehind|imageshack/i;
	var src = $img[0].src;
	if (src.match(banned_image_hosts)) {
		$img.wrap('<a href="'+ this.src +'" target="_blank"></a>').attr({ src: "//static.pornolab.net/smiles/tr_oops.gif", title: "Прочтите правила выкладывания скриншотов!" });
	}
	return $img;
}
$(function(){
	$('div.post_body, div.signature').each(function(){ initPost( $(this) ) });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initPostVideos();
    });

    function initPostVideos(root) {
        // root — контейнер, если посты подгружаются аяксом
        var container = document;
        var nodes = container.querySelectorAll('var.postVideo');

        nodes.forEach(function (node) {
            var url = node.getAttribute('title');
            if (!url) return;

            var isHls = /\.m3u8(\?|$)/i.test(url);

            var video = document.createElement('video');
            // сохраняем старые классы + добавляем embded-video
            video.className = node.className + ' embded-video';
            video.controls = true;
            video.preload = 'metadata';

            if (isHls) {
                // HLS
                if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    // Safari / iOS
                    // video.src = url;
                    var hls = new Hls();
                    hls.loadSource(url);
                    hls.attachMedia(video)
                } else if (window.Hls && Hls.isSupported()) {
                    // Chrome / Firefox / Edge
                    var hls = new Hls();
                    hls.loadSource(url);
                    hls.attachMedia(video);
                } else {
                    // Совсем старый браузер — хотя бы ссылка
                    var link = document.createElement('a');
                    link.href = url;
                    link.textContent = 'Скачать видео';
                    node.parentNode.replaceChild(link, node);
                    return;
                }
            } else {
                // Обычное видео (mp4/webm)
                video.src = url;
            }

            node.parentNode.replaceChild(video, node);
        });
    }
</script>

<script>
	function is_touch_device() {
		return (('ontouchstart' in window) ||
				(navigator.maxTouchPoints > 0) ||
				(navigator.msMaxTouchPoints > 0));
	}
</script>
<script type="text/javascript">
var BB            = {};
var BB_ROOT       = "./";
var cookieDomain  = ".pornolab.net";
var cookiePath    = "/forum/";
var cookieSecure  = 0;
var cookiePrefix  = "bb_";
var LOGGED_IN     = 1;
var InfoWinParams = 'width=780,height=510,resizable=yes';

var user = {
	opt_js: {"only_new":0,"h_flag":0,"h_av":0,"h_rnk_i":0,"h_post_i":0,"i_aft_l":0,"h_smile":0,"h_sig":0,"h_priv":0,"h_colors":0,"sp_op":0,"tr_tm":0,"tr_rp":0,"tr_sd":0,"h_cat":"","h_tsp":0,"h_acomp":0,"h_highl":0,"s_f_f":0,"s_f_o":0},

	set: function(opt, val, days, reload) {
		this.opt_js[opt] = val;
		setCookie('opt_js', $.toJSON(this.opt_js), days);
		if (reload) {
			window.location.reload();
		}
	}
}

$(function(){
	$('a.dl-stub').each(function(){
		var $a = $(this);
		var href = $a.attr('href');
		var t_id = href.slice( href.lastIndexOf('=')+1 );
		var event = ($.browser.opera) ? 'mouseover' : 'mousedown';
		$a.bind(event, function(){
			setCookie('bb_dl', t_id, 'SESSION')
		});
		/*$a.click(function(){
			$('#dl-form').attr('action', href);
			$('#dl-form').submit();
			return false;
		});*/
	});
		$('form.tokenized').append('<input type="hidden" name="form_token" value="011aaf0d89012c0cedb28c5f94a9ba20" />');
	ajax.form_token = '011aaf0d89012c0cedb28c5f94a9ba20';
		
	$('.sp-open-all').click(function () {
		var fold = $(this).hasClass('sp-open-uncollapsed');
		$('div.sp-head').filter( function(){ return $(this).hasClass('unfolded') ? fold : !fold } ).click();
		$(this).toggleClass('sp-open-uncollapsed');

		if (!$(this).hasClass('sp-open-uncollapsed')) {
			var $_this = $(this);
			setTimeout(function() {
				const y = $_this.offset().top - 100;
				window.scrollTo({top: y, behavior: 'smooth'});
			}, 200);
		}
		return false;
	});
});

var ajax = new Ajax('ajax.php', 'POST', 'json');

function getElText (e)
{
	var t = '';
	if (e.textContent !== undefined) {
		t = e.textContent;
	}
	else if (e.innerText !== undefined) {
		t = e.innerText;
	}
	else {
		t = jQuery(e).text();
	}
	return t;
}
function escHTML (txt) {
	return txt.replace(/</g, '&lt;');
}
function cfm (txt)
{
	return window.confirm(txt);
}
function post2url (url, params) {
	params = params || {};
	var f = document.createElement('form');
	f.setAttribute('method', 'post');
	f.setAttribute('action', url);
	params['form_token'] = '011aaf0d89012c0cedb28c5f94a9ba20';
	for (var k in params) {
		var h = document.createElement('input');
		h.setAttribute('type', 'hidden');
		h.setAttribute('name', k);
		h.setAttribute('value', params[k]);
		f.appendChild(h);
	}
	document.body.appendChild(f);
	f.submit();
	return false;
}
</script>

<!--[if lte IE 6]><script type="text/javascript">
$(ie6_make_clickable_labels);

$(function(){
	$('div.menu-sub').prepend('<iframe class="ie-fix-select-overlap"></iframe>'); // iframe for IE select box z-index issue
	Menu.iframeFix = true;
});
</script><![endif]-->


<!--[if gte IE 7]><style type="text/css">
input[type="checkbox"] { margin-bottom: -1px; }
</style><![endif]-->

<!--[if lte IE 6]><style type="text/css">
.forumline th { height: 24px; padding: 2px 4px; }
.menu-sub iframe.ie-fix-select-overlap { display: none; display/**/: block; position: absolute; z-index: -1; filter: mask(); }
</style><![endif]-->

<!--[if IE]><style type="text/css">
.post-hr { margin: 2px auto; }
.fieldsets div > p { margin-bottom: 0; }
</style><![endif]-->

<style type="text/css">
img.t-spacer { width: 142px; margin-top: -1px; }
.hidden, .menu-sub, #ajax-loading, #ajax-error, var.ajax-params, .sp-title, .q-post { display: none; }
#adriver-240x120 { width: 240px; height: 120px; padding-bottom: 2px; margin-right: -2px; }
#adriver-468x60, #pr-468x60  { width: 468px; height: 60px; }
#dd-900x90 { width: 980px; }
@media screen and (max-width: 1100px) {
	#dd-900x90, #dd-900x90 > object { width: 620px; }
}
#latest_news table { width: 100%; }

/* temp */


/* temp end */
</style>

</head>

<body>

<script type="text/javascript" src="//static.pornolab.net/js/bottom_message.js?v=28"></script>



<!--cse-->
<script type="text/javascript">
$(function(){
	// $('#cse-search-btn, #cse-search-btn-top').click(function(){
	// 	var text_match_input_id = $(this).attr('href');
	// 	var text_match = $('#'+text_match_input_id).val();
	// 	if (text_match == '') {
	// 		$('#'+text_match_input_id).addClass('hl-err-input').focus();
	// 		return false;
	// 	}
	// 	$('#cse-text-match').val( text_match );
	// 	$('#cse-submit-btn').click();
	// 	return false;
	// });

	$('#quick-search').submit(function(){
		var action = $('#search-action').val();
		var txt = $('#search-text').val();
		if (txt=='поиск...' || txt == '') {
			$('#search-text').val('').addClass('hl-err-input').focus();
			return false;
		}
		// if (action == 'cse') {
		// 	$('#cse-search-btn-top').click();
		// 	return false;
		// }
		// else {
		// 	$(this).attr('action', action);
		// }
        $(this).attr('action', action);
	});


    // Function to get search text based on the clicked button's href
    function getSearchText(button) {
        var text_match_input_id = button.attr('href');
        return $('#' + text_match_input_id).val();
    }

    // Google Search
    $('#cse-search-btn').click(function(e) {
        e.preventDefault();
        var searchText = getSearchText($(this));
        var googleSearchUrl = 'https://www.google.com/search?q=site:pornolab.net+' + encodeURIComponent(searchText);
        window.open(googleSearchUrl, '_blank');
    });

    // DuckDuckGo Search
    $('#ddg-search-btn').click(function(e) {
        e.preventDefault();
        var searchText = getSearchText($(this));
        var duckDuckGoSearchUrl = 'https://duckduckgo.com/?q=site:pornolab.net+' + encodeURIComponent(searchText);
        window.open(duckDuckGoSearchUrl, '_blank');
    });
});
</script>
<div id="cse-form-holder" style="display: none;">
<form action="search_cse.php" id="cse-search-box" accept-charset="utf-8">
	<input type="hidden" name="cx" value="9187e436392724367" />
	<input type="hidden" name="cof" value="FORID:9" />
	<input type="hidden" name="ie" value="utf-8" />
	<input type="text" name="q" size="60" value="" id="cse-text-match" />
	<input type="submit" name="sa" value="Поиск в Google" id="cse-submit-btn" />
</form>

</div>
<!--/cse-->

<!-- <form method="post" action="" id="dl-form" style="display: none;"></form> -->

<script>
	ajax.dark_theme = function(mode) {
		ajax.exec({
			action   : 'dark_theme',
			mode     : mode
		});
	};
	ajax.callback.dark_theme = function(data) {
		if (data.info) {
			window.location.reload();
		}
	};
</script>



<div id="preload" style="position: absolute; overflow: hidden; top: 0; left: 0; height: 1px; width: 1px;"></div>

<div id="body_container">

<!--
    <div class="bypass-alert">
        <div class="dCenter nowrap">
        <a href="//pornolab.net/forum/viewtopic.php?t=2324898" style="color: red !important;">Просьба о помощи с переоформлением раздач</a>
        </div>
    </div>
-->
    <div class="bypass-alert" style="display: none;">
        <div class="dCenter">
			<a href="//pornolab.net/forum/viewtopic.php?t=3114884" style="color: darkred;">Почтовые сервисы mail.ru добавлены в черный список на форуме</a>
            <a href="javascript: void(0)" onclick="store.set('bypass-alert-hide', 1); $('.bypass-alert').hide();" style="font-size: 10px; position: relative; top: -5px; margin-left: 10px; text-decoration: none;">[ Cкрыть ]</a>
        </div>
        <script>
            $(function(){
                if (!store.get('bypass-alert-hide')) {
                    $('.bypass-alert').show();
                }
            });
        </script>
    </div>


<!--************************************************************************-->
<!--=COMMON_HEADER==========================================================-->

<script type="text/javascript">
if (top != self) {
	allowed_top = /translate/i;
	if (top.location.hostname != self.location.hostname && !top.location.hostname.match(allowed_top)) {
		alert('in frame!'+'\n\n'+top.location.hostname);
		top.location.href = self.document.location;
	}
}
</script>

<!--page_container-->
<div id="page_container">
<a name="top"></a>

<!--page_header-->
<div id="page_header">

<!--main_nav-->
<div id="main-nav" style="height: 17px;">
	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="nowrap">
			<a href="./index.php"><b>Главная</b></a>&#0183;
			<a href="tracker.php?f[]=-1"><b>Трекер</b></a>&#0183;
			<a href="search.php"><b>Поиск</b></a>&#0183;
			<a href="viewtopic.php?t=980423"><b>Правила</b></a>&#0183;
			<a href="viewforum.php?f=566"><b style="color: #993300;">FAQ</b></a>&#0183;
			<a href="privmsg.php?folder=inbox"><b>ЛС</b></a>&#0183;
			<a href="groupcp.php"><b>Группы</b></a>&#0183;
			<a href="viewtopic.php?t=2947333"><i class="fas fa-heart" style="color: red;"></i>&nbsp;<b style="color: #993300;">Donate</b></a>&#0183;
												<script>
				var endTime = 13833,
					timer = 0;
				function fixLeadingZero(num) {
					return num < 10 ? "0" + num : num;
				}
				function calcCountDown() {
					timer++;

					var distance = (endTime - timer)*1000; // multiply by 1000 because below formulas for microseconds

					var days = Math.floor(distance / (1000 * 60 * 60 * 24)); var daysPhrase = '';
					var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
					var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
					var seconds = Math.floor((distance % (1000 * 60)) / 1000);

					//days = fixLeadingZero(days);
					hours = fixLeadingZero(hours);
					minutes = fixLeadingZero(minutes);
					seconds = fixLeadingZero(seconds);
					if (days > 0) {
						daysPhrase = days;
						if (days == 1) {
							daysPhrase = daysPhrase + " день";
						} else if (days > 1 && days < 5) {
							daysPhrase = daysPhrase + " дня";
						} else {
							daysPhrase = daysPhrase + " дней";
						}
						daysPhrase = daysPhrase + " ";
					}


					$('.freelech-countdown').text(daysPhrase + hours + ":" + minutes + ":" + seconds);

					// If the count down is finished, write some text
					if (distance < 0) {
						clearInterval(x);
						$('.freelech-countdown').text("00:00:00");
					}

					$('.freeleech-icon-link').attr('title', "Сегодня фрилич! Кончится через " + $('.freelech-countdown').text());
				}
				var x = setInterval(calcCountDown, 1000);
				$(function (){
					calcCountDown();
				});
			</script>
			&#0183;&nbsp;&nbsp;<a href="/forum/viewtopic.php?p=12417428#12417428">Окончание фрилича через <span class="freelech-countdown"></span></a>
					</td>
	</tr>
	</table>
</div>
<!--/main-nav-->


<div style="position: absolute; top: 2px; right: 9px;">
		&#0183;
	<form id="quick-search" method="post" action="">
	<input type="hidden" name="max" value="1" />
	<input type="hidden" name="to" value="1" />
	поиск&nbsp;<input id="search-text" type="search" name="nm" accesskey="ы" value="" class="hint" style="width: 150px;" />
	<select id="search-action" autocomplete="off">
		<script>
		$(function () {
			if ($('#search-mode').length) {
				$('#search-action').val($('#search-mode').val());
			}
            $('#quick-search').on('submit', function(event) {
                var selectedValue = $('#search-action').val();
                var searchText = $('#search-text').val();
                var searchUrl;

                if (selectedValue === 'cse') {
                    searchUrl = 'https://www.google.com/search?q=site:pornolab.net+' + encodeURIComponent(searchText);
                } else if (selectedValue === 'ddg') {
                    searchUrl = 'https://duckduckgo.com/?q=site:pornolab.net+' + encodeURIComponent(searchText);
                }

                if (searchUrl) {
                    window.open(searchUrl, '_blank');
                    event.preventDefault();
                }
            });
		});
		</script>
		<option value="tracker.php" selected="selected"> раздачи </option>
		<option value="search.php"  > все темы </option>
        		<option value="search.php?po=1&dm=1"  > в сообщениях </option>
        				<option value="cse"> в Google </option>
        <option value="ddg">&nbsp;в DuckDuckGo&nbsp;</option>
	</select>
	<input type="submit" class="med bold" value="&raquo;" style="width: 30px;" />
	</form>
<a style="display: none;" id="cse-search-btn-top" href="search-text">&nbsp;</a>
</div>

<!--logo-->
<div id="logo">
	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td id="logo-td">
						<a href="/forum/viewtopic.php?p=12417428#12417428" title="Сегодня Фрилич!" class="freeleech-icon-link">
<!--                <i class="fas fa-box-open faa-tada animated animate-3-tm freeleech-icon"></i>-->
                                <img src="//static.pornolab.net/images/logo/freeleech/freeleech_white.gif" class="freeleech-icon" title="Сегодня Фрилич!"  />
                            </a>
			<script>
				$(function (){
					$('.freeleech-icon').hover(
							function (){
								$(this).removeClass('faa-tada animate-3-tm').addClass('faa-pulse faa-fast');
							},
							function (){
								$(this).removeClass('faa-pulse faa-fast').addClass('faa-tada animate-3-tm');
							}
					);
				});
			</script>
									<a href="./index.php">
								<img src="//static.pornolab.net/images/logo/spring/logo_spring-9.gif" class="site-logo" alt="" />
							</a>
			<!-- <a href="./index.php"><img src="//static.pornolab.net/images/logo/logo.gif" width="353" height="131" alt="" /></a> -->
		</td>
		<td width="100%" class="tCenter" style="padding: 0 6px 0 6px;">

			
					</td>
	</tr>
	</table>
</div>
<!--/logo-->

<!--topmenu-->
<div class="topmenu">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
		<td width="50%">
		Вы зашли как: &nbsp;<a href="./profile.php?mode=viewprofile&amp;u=36015205"><b class="med">iqdhk8t0hxww</b></a>&nbsp; [ <a href="#" onclick="return post2url('login.php', {logout: 1});">Выход</a> ]
	</td>
	
		<td>
				<i class="fas fa-envelope" style="color: #345da4;"></i>
				<a href="privmsg.php?folder=inbox">
		Личные сообщения: новых нет</a>
	</td>
	
		<td width="50%" class="tRight">
		<a href="javascript:void(0)" onclick="ajax.dark_theme(1)">

			<i class="fas fa-moon"></i>
		</a>
		&#0183;
		<a href="profile.php?mode=editprofile">Настройки</a>
		&#0183;
		<a href="./profile.php?mode=viewprofile&amp;u=36015205">Рейтинг/Закачки</a> <a href="#dls-menu" class="menu-root without-caret menu-alt1"><i class="fas fa-caret-down btn-caret"></i></a>
		&#0183;
		<a href="search.php?uid=36015205">Мои сообщения</a>
	</td>
	</tr>
</table>
</div><!--/topmenu-->
    
<noscript>
	<style type="text/css">
	.sp-body, .sp-title { display: block; }
	</style>
</noscript>

<div class="menu-sub" id="dls-menu">
	<div class="menu-a bold nowrap">
		<a class="med" href="tracker.php?rid=36015205">Текущие раздачи</a>
		<a class="med" href="./profile.php?mode=viewprofile&amp;u=36015205">Текущие закачки</a>
		<a class="med" href="search.php?dlu=36015205&amp;dlc=1">Прошлые закачки</a>
		<a class="med" href="search.php?dlu=36015205&amp;dlw=1">Будущие закачки</a>
	</div>
</div>

<!--breadcrumb-->
<!--<div id="breadcrumb"></div>-->
<!--/breadcrumb-->


</div>
<!--/page_header-->

<script>
    $(function () {
        const alertKey = 'bypass-top-alert-hide-2024';
        $('div.top-alert span').on('click', function () {
            store.set(alertKey, 1); $('div.top-alert').hide();
        });
        if (!store.get(alertKey)) {
            $('div.top-alert').show();
        }
    });
</script>
<style>
    .ta-inf2 {
        background: #f5f1e8;
        border-color: #b19b68;
    }
    .top-alert {
        position: relative;
        max-width: 80%;
        padding: 13px 25px 12px;
        line-height: 27px;
        border: 1px solid #800000;
        border-radius: 6px;
        box-shadow: 3px 3px 3px rgba(0,0,0,0.1);
        font-size: 14px;
        text-align: center;
        margin: 16px auto 2px;
        display: none;
    }
    .ta-hide-btn {
        position: absolute;
        top: -2px;
        right: 6px;
        font-size: 18px;
        color: #000000;
    }
    .top-alert a {
        color: #690a1d;
        text-decoration: none;
    }
    .top-alert a:hover {
        color: #690a1d;
    }
</style>
<div class="top-alert ta-inf2 hide-for-print">
    <span><a target="_blank" href="/forum/viewtopic.php?t=2947333">Нам нужна помощь на оплату серверов! We're need <i class="fas fa-heart" style="color: red;"></i> donate to pay the servers!</a></span>
    <span class="ta-hide-btn bold nowrap clickable">&#x2715;</span>
</div>

<!--page_content-->
<div id="page_content">
<table cellspacing="0" cellpadding="0" border="0" style="width: 100%;"><tr>


<!--main_content-->
<td id="main_content">
<div id="main_content_wrap">


<!--========================================================================-->
<!--************************************************************************-->



<!-- page_header.tpl END -->
<!-- module_xx.tpl START -->

<style type="text/css">
td.rel-inputs { padding-left: 6px; }
.rel-el      { margin: 2px 6px 2px 0; }
.rel-title   { font-weight: bold; }
.rel-input   { }
.rel-free-el { font-size: 11px; line-height: 12px; }

textarea.rel-input { width: 98%; }
#rel-create textarea, #tpl-row-src { font-size: 13px; font-family: "Lucida Console","Courier New",Courier,monospace; }

.tpl-err-hl-input { border: 1px solid #8C0000; background: #FFF9F2; }
.tpl-err-hl-tr    { background: #FFEAD5; }
.tpl-err-hint     { color: #8C0000; margin-right: 6px; }
.tpl-adm-block    { padding: 6px 20px; margin: 8px 0; border: 4px ridge #808080; }
.tpl-adm-block div.label { padding: 2px 0; }
.attr-list    { color: #0000BB; }
.tpl-help-msg { padding: 8px; border: 2px solid #A5AFB4; background: #DEE3E7; font-size: 11px; }
.hlp-1        { color: blue; }
.hlp-2        { color: red; }

#rel-preview .rel-input { background: #DEE3E7; border: 1px solid #9BA8AE; }
#rel-preview td { padding: 5px 10px 6px; }
#tpl-row-src    { border: 1px dashed #CFD6D8; background: #F8F8F8; color: #0000D5; font-size: 14px; }

#row-preview-win { height: 45px; overflow: auto; }
#tpl-row-preview textarea.rel-input { height: 20px; border: 2px inset; }
.hid-el > * { color: red !important; border-color: red !important; }
</style>

<script type="text/javascript">
var custom_tpl_script;

$(function(){
	var src_form = $('#tpl-src-form-val').val();
	var script_re = /^<script>\n([^]+)\n<\/script>/m;
	var match = src_form.match(script_re);
	if (match) {
		custom_tpl_script = match[1];
		src_form = src_form.replace(script_re, '');
	}

	// инициализация значений #tpl-src
	$('#tpl-src-form').val( src_form );
	$('#tpl-src-title').val( $('#tpl-src-title-val').val() );
	$('#tpl-src-msg').val( $('#tpl-src-msg-val').val() );
	$('#tpl-src-sel').val( $('#tpl-src-sel-val').val() );

});

var TPL = {
	
	match_rows: function(s) {
		return $.trim(s).split(/\->/g) || [];
	},
	match_cols: function(s) {
		return $.trim(s).match(/<\-\s*(\w+|\{.+?\})([\s\S]+)/) || [];
	},
	match_els: function(s) {
		var re = /`(\S[^`]+)\[/g;
		while (s.match(re))
		{
			s = s.replace(re, '`$1&#91;');
		}
		s = s.replace(/(\w+\[.+?\])/g, '`$1`');
		return $.trim(s).match(/`([\s\S]+?)`/g) || [];
	},
	match_el_attrs: function(s) {
		return s.match(/(\w+|\{.+\})\[(.+)\]/) || [];
	},
	trim_brackets: function(s) {
		return $.trim( s.substring(1, s.length-1) ) || '';
	},

	rows: {},
	el_titles: {},

	build_tpl_form: function(str, res_id)
	{
		$('#'+res_id+' tr').remove();
		TPL.rows = {};
		TPL.el_titles = {};
		TPL.parse_sel_from_src( $('#tpl-src-sel').val() );

		$.each(TPL.match_rows(str), function(i,row){
			if (row == null || row == '') return true; // continue
			TPL.rows[i] = $.trim(row);
		});
		$.each(TPL.rows, function(i,row){
			var mr = TPL.match_cols(row);
			if (mr[2] == null) return true; // continue
			var title_id = mr[1];    // id элемента для подстановки его названия или {произвольное название}
			var input_els = mr[2];
			var row_title = (TPL.el_attr[title_id] != null) ? TPL.el_attr[title_id][1] : TPL.trim_brackets(title_id);
			var $tr = $('<tr><td class="rel-title">'+ row_title +':</td><td class="rel-inputs"></td></tr>');
			var $td = $('td.rel-inputs', $tr);

			$.each(TPL.match_els(input_els), function(j,el){
				if (!(el = TPL.trim_brackets(el))) return true; // continue
				var el_html = '';
				var me = TPL.match_el_attrs(el);
				// вставка шаблонного элемента типа TYPE[attr]
				if (me[2] != null) {
					var at = me[2].split(',');
					var nm = at[0];

					switch (me[1])
					{
					case 'E':
						if ( $('#'+ nm +'-hid').length ) {
							if (res_id == 'tpl-row-preview') {
								el_html = '<span class="rel-el hid-el">'+ $('#'+ nm +'-hid').html() +'</span>'; // скрытый элемент
							}
						}
						else {
							el_html = '<span class="rel-el">'+ $('#'+ nm).html() +'</span>';
						}
						break;
					case 'T':
						el_html = '<span class="rel-el rel-title">'+ TPL.el_attr[nm][1] +'</span>';
						break;
					case 'INP':
						var id = TPL.build_el_id_title(nm);
						var def = (TPL.el_attr[id] != null) ? TPL.el_attr[id][2].split(',') : [200,80];
						var mlem = at[1] || def[0];
						var size = at[2] || def[1];
						el_html = '<input class="rel-el rel-input" type="text" id="'+ id +'" maxlength="'+ mlem +'" size="'+ size +'" />';
						break;
					case 'TXT':
						var id = TPL.build_el_id_title(nm);
						var def = (TPL.el_attr[id] != null) ? TPL.el_attr[id][2].split(',') : [3];
						var rows = at[1] || def[0];
						var cols = 100;
						el_html = '<textarea class="rel-el rel-input" id="'+ id +'" rows="'+ rows +'" cols="'+ cols +'" />';
						break;
					case 'SEL':
						var id = TPL.build_el_id_title(nm);
						el_html = TPL.build_select_el(nm);
						break;
					}
				}
				// вставка нешаблонного элемента
				else {
					if (el == 'BR') {
						el_html = '<br />';
					}
					else {
						el_html = '<span class="rel-el rel-free-el">'+ escHTML(el) +'</span>';
					}
				}
				// добавление элемента в td.rel-inputs
				if (el_html != '') {
					$td.append(el_html);
				}
			});
			// добавление tr в форму
			$('#'+res_id).append($tr);
		});

		TPL.build_msg_src();
		$('#rel-form').show();

		$('select.rel-input').bind('change', function(){
			var $sel = $(this);
			if ( $sel.val().toLowerCase().match(/^друг(ой|ая|ое|ие)$/) ) {
				var $input = $('<input class="rel-el rel-input" type="text" id="'+ $sel.attr('id') +'" style="width: '+ $sel.width() +'px;" />');
				$sel.after($input);
				$sel.remove();
				$input.focus();
			}
		});
	},

	build_el_id_title: function(nm) {
		if (TPL.el_attr[nm] != null) {
			var id = nm;
			TPL.el_titles[id] = TPL.el_attr[id][1];
		}
		else {
			var id = $P.md5(nm);
			TPL.el_titles[id] = TPL.trim_brackets(nm);
		}
		return id;
	},
	get_el_id: function(el) {
		return (TPL.el_attr[el] != null || TPL.el_id[el] != null) ? el : $P.md5(el);
	},

	build_select_el: function(name) {
		var sel_id = TPL.get_el_id(name);
		if (TPL.selects[sel_id] == null) return '';
		var s = '<select class="rel-el rel-input" id="'+sel_id+'">';
		var q = /"/g;  //"
		$.each(TPL.selects[sel_id], function(i,v){
			s += '<option value="'+(i==0 ? '' : v.replace(q, '&quot;'))+'">'+(v=='' ? '&raquo; Выбрать' : v)+'</option>';
		});
		s += '</select>';
		return s;
	},

	// возвращает все элементы формата el[atr1,atr2] в виде
	// { el: 'el[attr]' }  для parse_attr == false
	// { el: [atr1,atr2] } для parse_attr == true
	get_msg_els: function(str, parse_attr) {
		var res = {};
		$.each(str.split('\n'), function(i,v){
			if (!(v = $.trim(v))) return true; // continue
			var m = v.match(/^(\w+|\{.+\})\[(.*)\]$/);
			if (m == null) return true; // continue
			if (parse_attr) {
				res[ m[1] ] = m[2].split(',');
			}
			else {
				res[ m[1] ] = m[0];
			}
		});
		return res;
	},

	sel_ids: [],
	parse_sel_from_src: function(str) {
		$.each(TPL.sel_ids, function(i,id){
			TPL.selects[id] = null;   // удаление ранее созданых селектов
			TPL.el_titles[id] = null;
		});
		TPL.sel_ids = [];

		$.each(str.split('\n'), function(i,v){
			if (!(v = $.trim(v))) return true; // continue
			var m = v.match(/^(\{.+\})\s*\[(.+)\]$/);
			if (m == null) return true; // continue
			var id   = $P.md5(m[1]);
			var name = TPL.trim_brackets(m[1]);
			var vals = m[2].split(',');
			TPL.sel_ids.push(id);
			TPL.selects[id] = ['&raquo; '+ name].concat(vals);
			TPL.el_titles[id] = name;
		});
	},

	// создает-обновляет скрипт создания сообщения (#tpl-src-msg)
	build_msg_src: function() {
		var r_old = TPL.get_msg_els( $('#tpl-src-msg').val(), false );   // старые правила для создания сообщения
		var r_gen = [];                                                  // новые, сгенерированные из всех доступных в форме
		var r_new = [];                                                  // новые, с учетом изменений в форме
		// получение всех инпутов из формы
		var m;
		var t = $('#tpl-src-form').val();
		var r = /(?:INP|TXT|SEL)(?:\[)(\w+|\{.+?\})/g;
		while((m = r.exec(t)) != null) {
			r_gen.push(m[1]);
		}
		// создание нового (старые значения сохраняются без именений, новые добавляются, отсутствующие в форме удаляются)
		$.each(r_gen, function(i,v){
			if (r_old[v] != null) {
				r_new.push( r_old[v] );
			}
			else {
				var def_attr = (TPL.el_attr[v] != null) ? TPL.el_attr[v][3] : '';
				r_new.push( v +'['+ def_attr +']' );
			}
		});
		var new_txt = r_new.join('\n');
		$('#tpl-src-msg').val(new_txt);
	},

	// количество найденных ошибок при заполнении формы
	f_errors_cnt: 0,
	// удаление подсветки ошибок, сброс счетчика
	reset_f_errors: function() {
		TPL.f_errors_cnt = 0;
		$('tr.tpl-err-hl-tr').removeClass('tpl-err-hl-tr');
		$('.tpl-err-hl-input').removeClass('tpl-err-hl-input');
		$('div.tpl-err-hint').remove();
	},
	// подсветка ошибок
	hl_form_err: function(el, hint_id) {
		if (TPL.el_attr[el] != null) {
			var el_id = el;
			var el_title = TPL.el_attr[el][1];
		}
		else {
			var el_id = $P.md5(el);
			var el_title = TPL.el_titles[el_id];
		}
		var hint = TPL.err_msg[hint_id].replace(/%s/, el_title);
		$('#'+el_id)
			.addClass('tpl-err-hl-input')
			.parent('td').append('<div class="tpl-err-hint">'+hint+'</asd>')
			.parent('tr').addClass('tpl-err-hl-tr')
		;
		if (TPL.f_errors_cnt == 0) $('#'+el_id).focus();
		TPL.f_errors_cnt++;
	},
	// сообщения об ошибках при валидации заполнения формы
	err_msg: {
		empty_INP : 'Вы должны заполнить поле <b>%s</b>',
		empty_TXT : 'Вы должны заполнить поле <b>%s</b>',
		empty_SEL : 'Вы должны выбрать <b>%s</b>',
		not_num   : '<b>%s</b> - должно быть число',
		not_url   : '<b>%s</b> - должна быть http:// ссылка',
		not_img   : '<b>%s</b> - должна быть http:// ссылка на картинку'
	},

	msg_attr: {
		HEAD     : 'поместить в заголовок',
		POSTER   : 'постер',
		req      : 'требует заполнения',
		spoiler  : 'спойлер',
		BR       : 'новая строка',
		br2      : 'новая строка после названия',
		num      : 'число',
		URL      : 'ссылка',
		img      : 'картинка',
		pre      : 'pre',
		inline   : 'на той же строке',
		headonly : 'только в заголовке'
	},
	reg: {
		num     : /^\d+$/,
		URL     : /^https?:\/\/[\w\#$%&~/.\-;:=?@\[\]+]+$/i,
		img     : /^https?:\/\/[^\s\?&;:=\#\"<>]+\.(jpg|jpeg|gif|png|webp|avif)$/i,
		img_tag : /(https?:\/\/[^\s\?&;:=\#\"<>]+\.(jpg|jpeg|gif|png|webp|avif)(?!\[|\.))/ig
	},

	// построение сообщения на основе данных из формы
	build_msg_all: function(msg_res_id, title_res_id) {
		$.each(TPL.submit_fn, function(el,fn){
			fn();
		});
		$('#tpl-row-preview tr').remove();

		TPL.reset_f_errors();
		var msg_header = [];
		var msg_poster = '';
		var msg_body = []
		var msg_els = TPL.get_msg_els( $('#tpl-src-msg').val(), true );

		$.each(msg_els, function(el,at){
			var el_id = TPL.get_el_id(el);
			var el_val = $('#'+el_id).val() || '';

			// требуемые поля
			if (el_val == '') {
				if ($.inArray('req', at) != -1) {
					var el_type = (TPL.el_attr[el] != null) ? TPL.el_attr[el][0] : 'INP';
					TPL.hl_form_err(el, 'empty_'+ el_type);
				}
				return true; // continue
			}

			// валидация значений
			if ($.inArray('num', at) != -1 && !TPL.reg['num'].test(el_val)) {
				TPL.hl_form_err(el, 'not_num');
				return true; // continue
			}
			if ($.inArray('URL', at) != -1 && !TPL.reg['URL'].test(el_val)) {
				TPL.hl_form_err(el, 'not_url');
				return true; // continue
			}
			if ($.inArray('img', at) != -1 && !TPL.reg['img'].test(el_val)) {
				TPL.hl_form_err(el, 'not_img');
				return true; // continue
			}

			// post-submit обработка значений
			el_val = TPL.normalize_val(el, el_val);

			// заголовок
			if ($.inArray('HEAD', at) != -1) {
				msg_header.push( el_val );
				return true; // continue
			}
			// постер
			if ($.inArray('POSTER', at) != -1) {
				msg_poster = el_val;
				return true; // continue
			}

			// новая строка после названия
			if ($.inArray('br2', at) != -1) {
				el_val = '\n'+ el_val;
			}
			// спойлер
			if ($.inArray('spoiler', at) != -1) {
				msg_body.push( TPL.build_spoiler(el_id, el_val) );
				return true; // continue
			}
			// pre
			if ($.inArray('pre', at) != -1) {
				msg_body.push( TPL.build_pre(el_id, el_val) );
				return true; // continue
			}
			// inline
			if ($.inArray('inline', at) != -1) {
				msg_body.push( TPL.build_inline(el_id, el_val) );
				return true; // continue
			}
			// только в заголовке
			if ($.inArray('headonly', at) != -1) {
				return true; // continue
			}
			// обычный элемент
			msg_body.push( TPL.build_msg_el(el_id, el_val) );

			// новая строка после элемента
			if ($.inArray('BR', at) != -1) {
				msg_body.push('\n');
			}
		});
		if (TPL.f_errors_cnt) {
			return false;
		}
		msg_header = TPL.build_msg_header(msg_header);
		msg_poster = TPL.build_msg_poster(msg_poster);
		msg_body = msg_body.join('');
		// теги для картинок
		//msg_body = msg_body.replace(TPL.reg['img_tag'], '[img]$1[/img]');
		$('#'+msg_res_id).val( msg_header + msg_poster + msg_body );

		TPL.build_title(title_res_id);

		return true;
	},

	normalize_val: function(el, val) {
		switch (el) {
			// 2000 г.
			case 'year':
				val += ' г.';
				break;

			// "Имя / Name /" -> "Имя / Name"
			case 'director':
			case 'studio':
				val = val.replace(/[\s\/]+$/, '');
				break;
		}
		return val;
	},

	build_msg_header: function(a) {
		return a.length ? '[size=24]'+ a.join(' / ') +'[/size]\n' : '';
	},
	build_msg_poster: function(s) {
		return TPL.reg['img'].test(s) ? '\n[img=right]'+ s +'[/img]\n' : s;
	},
	build_spoiler: function(el_id, el_val) {
		return '\n[spoiler="'+ TPL.el_titles[el_id] +'"]\n'+ el_val +'\n[/spoiler]\n';
	},
	build_pre: function(el_id, el_val) {
		return '\n[spoiler="'+ TPL.el_titles[el_id] +'"][pre]\n'+ el_val +'\n[/pre][/spoiler]\n';
	},
	build_inline: function(el_id, el_val) {
		return ' '+ TPL.el_titles[el_id] +' '+ el_val;
	},
	build_msg_el: function(el_id, el_val) {
		return '\n[b]'+ TPL.el_titles[el_id] +'[/b]: '+ el_val;
	},

	build_title: function(res_id) {
		var title = [];
		var trim_after_chars = {};
		var trim_before_chars = {};
		var g;                                                   // группа элементов <-el1 el2->[,]
		var t = $('#tpl-src-title').val().replace(/\n/g, ' ');   // формат
		var r = /<-([^>]+)->(\S*)/g;
		while((g = r.exec(t)) != null) {
			var g_els = g[1].match(/(\w+|\{.+?\})/g);
			if (g_els == null) return true; // continue

			var g_start_char = ' ';
			var g_delim_char = ' ';
			var g_end_char   = ' ';

			if (g[2].length == 1) {
				g_delim_char = ' '+ g[2];
			}
			else if (g[2].length == 3) {
				g_start_char = g[2].charAt(0);
				trim_after_chars[ g_start_char ] = true;

				g_delim_char = g[2].charAt(1);

				g_end_char = g[2].charAt(2);
				trim_before_chars[ g_end_char ] = true;
			}

			var g_vals = [];
			$.each(g_els, function(i,el){
				var el_id = TPL.get_el_id(el);
				var v = $('#'+el_id).val();
				if (v == undefined || $.trim(v) == '') return true; // continue
				v = TPL.normalize_val(el_id, v);
				g_vals.push(' '+ v +' ');
			});
			if (g_vals.length != 0) {
				title.push(' '+ g_start_char +' ');
				title.push( g_vals.join(' '+g_delim_char+' ') );
				title.push(' '+ g_end_char);
			}
		}
		var t = $.trim( title.join('').replace(/\s+,/g, ',').replace(/\s+/g, ' ') );
		$.each(trim_before_chars, function(ch,v){
			var r = new RegExp( '\\s*'+ preg_quote(ch), 'g' );
			t = t.replace(r, ch);
		});
		$.each(trim_after_chars, function(ch,v){
			var r = new RegExp( preg_quote(ch) +'\\s*', 'g' );
			t = t.replace(r, ch);
		});

		$('#'+res_id).val( t );
	},

	submit_fn : {}
};


/*
  -------------------------------------------------------------------------------------------------
  -- el_attr --------------------------------------------------------------------------------------
  -------------------------------------------------------------------------------------------------
*/
TPL.el_attr = {
	/*
		код_элемента = ID элемента в форме
		все элементы имеют class "rel-input"
		формат el_attr
			код_элемента: [
				[0] - тип
				[1] - название
				[2] - атрибуты элемента типа size,rows.. по умолчанию (в том же порядке как и опциональные для элемента)
				[3] - атрибуты типа HEAD,req.. по умолчанию для формата сообщения
			]
		формат элементов в #tpl-src-form (включая все опциональные атрибуты типа maxlength..)
			INP - input[name,maxlength,size]
			TXT - textarea[name,rows]
			SEL - select[name]               -- значения для селектов находятся в TPL.selects
	*/

	aud_cdc_rip     : [ 'SEL',  'Аудио кодек',                 '',        ''           ],
    tablet          : [ 'SEL',  'Лекарство',                    '',        ''           ],
	bi_trans        : [ 'SEL',  'Доминирующий жанр',           '',        ''           ],
	gay_r_type      : [ 'SEL',  'Тип раздачи',                 '',        ''           ], 
	aud_cdc_dvd     : [ 'SEL',  'Аудио кодек',                 '',        ''           ],
	cens            : [ 'SEL',  'Цензура',                     '',        ''           ],
	cens_hart_hmanga            : [ 'SEL',  'Цензура',                     '',        ''           ],
	cens_long       : [ 'SEL',  'Цензура',                     '',        ''           ],
	cens_jap_vid    : [ 'SEL',  'Цензура',                     '',        ''           ],
	cens_game       : [ 'SEL',  'Цензура',                     '',        ''           ],	
	cens_film       : [ 'SEL',  'Цензура',                     '',        ''           ],
	untr_genr       : [ 'SEL',  'Доминирующий жанр',           '',        ''           ],
	mag_cntry       : [ 'SEL',  'Страна',                      '',        ''           ],
	site            : [ 'INP',  'Подсайт и сайт',              ',60',     ''           ],
	vid_num         : [ 'INP',  'Количество роликов',          ',40',     ''           ],
	hd_type         : [ 'SEL',  'Тип HD видео',                '',        ''           ],
	hd_type_3D_VR   : [ 'SEL',  'Тип HD видео',                '',        ''           ],
	hd_type_device_VR	: [ 'SEL',  'Тип устройства (для видео VR)',                '',        ''           ],
	hd_type_3D   : [ 'SEL',  ' Тип 3D (для видео в 3D)',                '',        ''           ],
	hd_t_no         : [ 'SEL',  'Тип HD видео',                '',        ''           ],
	ethno_type      : [ 'SEL',  'Этнический состав',           '',        ''           ],
	eurogirls_type  : [ 'SEL',  'Европейские девушки в фильме',           '',        ''           ],
	menu            : [ 'SEL',  'Меню',                        '',        ''           ],
	split_scene		: [ 'SEL',  'Разбит на эпизоды',           '',        ''           ],
	mag_format      : [ 'SEL',  'Формат',                      '',        ''           ],
	art_format      : [ 'SEL',  'Формат',                      '',        ''           ],
	comix_type      : [ 'SEL',  'Тип раздачи',                 '',        ''           ],
	hentaiart_type  : [ 'SEL',  'Тип раздачи',                 '',        ''           ],
	ero_studio      : [ 'SEL',  'Название студии',             '',        ''           ],
	pack_type       : [ 'SEL',  'Тип ПАКа',                    '',        ''           ],
	time_range      : [ 'INP',  'Охваченный временной промежуток',   ',30',   ''       ],
	language_g      : [ 'SEL',  'Язык озвучки',                '',        ''           ],
	language_h      : [ 'SEL',  'Язык',                        '',        ''           ],
	language1       : [ 'SEL',  'Язык',                        '',        ''           ],
	language2       : [ 'SEL',  'Язык',                        '',        ''           ],
	language3       : [ 'SEL',  'Язык',                        '',        ''           ],
	transtype1      : [ 'SEL',  'Тип перевода',                '',        ''           ],
	transtype2      : [ 'SEL',  'Тип перевода',                '',        ''           ],
	transtype3      : [ 'SEL',  'Тип перевода',                '',        ''           ],
	lang_g			: [ 'SEL',  'Язык игры (сюжет)',					'',        ''           ],
    menulang_g		: [ 'SEL',  'Язык интерфейса',             '',        ''           ],
	menulang1       : [ 'SEL',  'Язык интерфейса',             '',        ''           ],
	menulang2       : [ 'SEL',  'Язык интерфейса',             '',        ''           ],
	menulang3       : [ 'SEL',  'Язык интерфейса',             '',        ''           ],
	platform        : [ 'SEL',  'Платформа',                   '',        ''           ],
    release_type    : [ 'SEL',  'Тип издания',                 '',        ''           ],	
	disktype        : [ 'SEL',  'Тип диска',                   '',        ''           ],	
	subtit1         : [ 'SEL',  'Субтитры',                    '',        ''           ],
	subtit2         : [ 'SEL',  'Субтитры',                    '',        ''           ],
	subtit3         : [ 'SEL',  'Субтитры',                    '',        ''           ],
	sub_dvd1        : [ 'SEL',  'Субтитры',                    '',        ''           ],
	sub_dvd2        : [ 'SEL',  'Субтитры',                    '',        ''           ],
	sub_dvd3        : [ 'SEL',  'Субтитры',                    '',        ''           ],
	language4       : [ 'SEL',  'Язык',                        '',        ''           ],
	lang_det4       : [ 'INP',  'Доп. языки',                 ',70',  ''           ],
	audio           : [ 'INP',  'Аудио',                       '200,80',  ''           ],
	pseudonym       : [ 'INP',  'Псевдонимы актрисы',      '200,80',  ''           ],
	casting         : [ 'TXT',  'В ролях',                     '3',       'BR'         ],
	vid_cdc_rip     : [ 'SEL',  'Видео кодек',                 '',        ''           ],
	region          : [ 'SEL',  'Регион',                      '',        ''           ],
	vid_cdc_hd      : [ 'SEL',  'Видео кодек',                 '',        ''           ],
	video           : [ 'INP',  'Видео',                       '200,80',  ''           ],
	year            : [ 'INP',  'Год производства',                 '4,5',     'num'        ],
	g_year          : [ 'INP',  'Год выпуска',                 '4,5',     'num'        ],
	g_data          : [ 'INP',  'Дата релиза',                 '10,10',        ''           ],
	g_data_p        : [ 'INP',  'Дата производства',                 '10,10',        ''           ],
	stepinstall     : [ 'TXT',  'Порядок установки',             '3',       'spoiler'         ],
	moreinfo        : [ 'TXT',  'Доп. информация',             '3',       'BR'         ],
	moreinfo1       : [ 'TXT',  'Доп. материалы',             '3',       'BR'         ],
	genre           : [ 'INP',  'Жанр',                        '200,50',  ''           ],
	genre_g         : [ 'TXT',  'Теги',                        '3',  ''           ],
	icons_g         : [ 'TXT',  '',                        '3',  ''           ],
	vid_type_h      : [ 'SEL',  'Тип видео',              '',        ''           ],
	vid_p_rip       : [ 'SEL',  'Разрешение видео',            '',        ''           ],
	vid_q_rip_mod   : [ 'SEL',  'Изменение в разрешении видео',            '',        ''           ],
	vid_q_rip       : [ 'SEL',  'Качество видео',              '',        ''           ],
	vid_q_rip2D     : [ 'SEL',  'Качество видео',              '',        ''           ],
	vid_q_dvd       : [ 'SEL',  'Качество видео',              '',        ''           ],
	vid_q_hd        : [ 'SEL',  'Качество видео',              '',        ''           ],
	vid_q_clip      : [ 'SEL',  'Качество видео',              '',        ''           ],
	vid_q_unif      : [ 'SEL',  'Качество видео',              '',        ''           ],
	title_rus       : [ 'INP',  'Название',                    '90,80',   'HEAD,req'   ],
	description     : [ 'TXT',  'Описание',                    '6',       'BR'         ],
	title_eng       : [ 'INP',  'Оригинальное название',       '90,80',   'HEAD'       ],
	translat1       : [ 'SEL',  'Озвучка',                     '',        ''           ],
	translat2       : [ 'SEL',  'Озвучка',                     '',        ''           ],
	translat3       : [ 'SEL',  'Озвучка',                     '',        ''           ],
	poster          : [ 'INP',  'Постер',                      '200,60',  'img,POSTER' ],
	playtime        : [ 'INP',  'Продолжительность',           '200,30',  ''           ],
	director        : [ 'INP',  'Режиссер',                    '200,50',  ''           ],
	autor_original  : [ 'INP',  'Автор оригинала',             '200,50',  ''           ],
	rus_sub         : [ 'SEL',  'Русские субтитры',            '',        ''           ],
	rus_sub2        : [ 'SEL',  'Субтитры на русском языке',            '',        ''           ],
	eng_sub         : [ 'SEL',  'Английские субтитры',         '',        ''           ],
	setlist         : [ 'TXT',  'Список сетов',                '3',       'spoiler'    ],
	screenshots     : [ 'TXT',  'Скриншоты и скринлисты',                   '3',       'spoiler'    ],
	screenshots_g   : [ 'TXT',  'Скриншоты/Примеры',                   '3',       'spoiler'    ],
	screenshots_p   : [ 'TXT',  'Скриншоты',                   '3',       'spoiler'    ],
	scene_screen    : [ 'TXT',  'Скриншоты и тех. данные ко всем роликам',                   '40',       'spoiler'    ],
	country         : [ 'INP',  'Страна',                      '200,40',  ''           ],
	studio          : [ 'INP',  'Студия',                      '200,50',  ''           ],
	vid_frmt_rip    : [ 'SEL',  'Формат видео',                '',        ''           ],
	vid_frmt_hd     : [ 'SEL',  'Формат видео',                '',        ''           ],
	vid_frmt_unif   : [ 'SEL',  'Формат видео',                '',        ''           ],

	// dummy
	dummy : ['','']
};

/*
  -------------------------------------------------------------------------------------------------
  -- el_id ----------------------------------------------------------------------------------------
  -------------------------------------------------------------------------------------------------
*/
TPL.el_id = {
	// ID контейнеров содержащих html элементы

	load_pic_btn    : 'Кнопка "Загрузить картинку"',
	lang_faq_btn    : 'Кнопка "FAQ по озвучке и субтитрам"',
	genre_btn       : 'Кнопка "Справочник по порно жанрам"',
	tech_btn        : 'Кнопка "FAQ по тех. данным"',
	h_genre_btn     : 'Кнопка "Жанры хентая"',
	g_genre_btn     : 'Кнопка "Жанры игр"',
	np_genre_btn    : 'Кнопка "Жанры нетрадиционного порно"',
	mk_scrn_btn     : 'Кнопка "Как изготовить скриншоты?"',
	ad_scrn_btn     : 'Кнопка "Как разместить скриншоты?"',
	auto_pack_btn   : 'Кнопка "Авто-составление списка видео"',
	translit_url	: 'Ссылка "Ссылка на переводчик"',
	translit2_url	: 'Ссылка "Ссылка на переводчик"',
	uploadpic_url	: 'Ссылка "Как залить картинку на бесплатный хост"',

        // ID элементов, для которых нужно создать скрытые элементы, содержащие аббревиатуры для подстановки в название 
        // Каждый элемент el_abr должен точно соответствовать el (translation_abr -> translation) 
	  cens_abr          : '[ABR] Тег цензур для хентая',
	  cens_hart_hmanga_abr          : '[ABR] Тег цензур для хентай арт, манга',
	  cens_jap_vid_abr	: '[ABR] Тег цензур для японских фильмов',
	  cens_long_abr     : '[ABR] Тег цензур для японских фильмов',
	  cens_game_abr     : '[ABR] Тег цензур игр',	  
	  cens_film_abr     : '[ABR] Тег цензур японских фильмов',
	  ethno_type_abr    : '[ABR] Тег этники',
	  pack_type_abr     : '[ABR] Тег типа ПАКа',
	  mag_format_abr    : '[ABR] Тег формат журнала',
	  art_format_abr    : '[ABR] Тег формат арта',
	  lang_g_abr        : '[ABR] Тег языка игр',
	  release_type_abr  : '[ABR] Тег версия игр',
	  vid_p_rip_abr     : '[ABR] Тег разрешения видео хентая',
	  vid_q_rip_mod_abr : '[ABR] Тег модификация видео хентая',
	  rus_sub2_abr 		: '[ABR] Тег субтитров классики',
	  language_h_abr    : '[ABR] Тег языка озвучки/субтитров хентая',
	  language1_abr     : '[ABR] Тег языка озвучки/субтитров 1',
	  language2_abr     : '[ABR] Тег языка озвучки/субтитров 2',
	  language3_abr     : '[ABR] Тег языка озвучки/субтитров 3',
	  language4_abr     : '[ABR] Тег языка фильмов',
	  mag_cntry_abr     : '[ABR] Тег страны журнала',
	  untr_genr_abr     : '[ABR] Тег главноего нетрадиц жанра',
	  gay_r_type_abr    : '[ABR] Тег типа гей-разного',
	  platform_abr      : '[ABR] Тег платформы программы',
	  region_abr        : '[ABR] Тег региона DVD диска программы',
	  bi_trans_abr      : '[ABR] Тег би/трассекса',
	  eurogirls_type_abr      : '[ABR] Тег европейские девушки',
	  split_scene_abr      : '[ABR] Тег разбит на эпизоды',
	  
	// dummy
	dummy : ''
};

/*
  -------------------------------------------------------------------------------------------------
  -- selects --------------------------------------------------------------------------------------
  -------------------------------------------------------------------------------------------------
*/
TPL.selects = {
	// [0] всегда имеет value='' и если задан как '' (пустая строка) заменяется на "&raquo; Выбрать"

	translat1 : [
		'&raquo; Отсутствует/Выбрать',
		'Оригинальная',
		'Любительская (одноголосая)',
		'Любительская (двухголосая)',
		'Профессиональная (одноголосая)',
		'Профессиональная (двухголосая)',
		'Профессиональная (многоголосая, закадровая)',
		'Профессиональная (полное дублирование)'
	],

	translat2 : [
		'&raquo; Отсутствует/Выбрать',
		'Оригинальная',
		'Любительская (одноголосая)',
		'Любительская (двухголосая)',
		'Профессиональная (одноголосая)',
		'Профессиональная (двухголосая)',
		'Профессиональная (многоголосая, закадровая)',
		'Профессиональная (полное дублирование)'
	],

	translat3 : [
		'&raquo; Отсутствует/Выбрать',
		'Оригинальная',
		'Любительская (одноголосая)',
		'Любительская (двухголосая)',
		'Профессиональная (одноголосая)',
		'Профессиональная (двухголосая)',
		'Профессиональная (многоголосая, закадровая)',
		'Профессиональная (полное дублирование)'
	],

	transtype1 : [
		'&raquo; Отсутствует/Выбрать',
		'Текст и звук на этом языке (оригинальные)',
		'Текст и звук на этом языке (перевод)',		
		'Только текст на этом языке (оригинальный текст)',
		'Только текст на этом языке (перевод)',		
		'Только звук на этом языке (оригинальный звук)',
		'Только звук на этом языке (перевод)'	
	],

	transtype2 : [
		'&raquo; Отсутствует/Выбрать',
		'Текст и звук на этом языке (оригинальные)',
		'Текст и звук на этом языке (перевод)',		
		'Только текст на этом языке (оригинальный текст)',
		'Только текст на этом языке (перевод)',		
		'Только звук на этом языке (оригинальный звук)',
		'Только звук на этом языке (перевод)'	
	],
	
	transtype3 : [
		'&raquo; Отсутствует/Выбрать',
		'Текст и звук на этом языке (оригинальные)',
		'Текст и звук на этом языке (перевод)',		
		'Только текст на этом языке (оригинальный текст)',
		'Только текст на этом языке (перевод)',		
		'Только звук на этом языке (оригинальный звук)',
		'Только звук на этом языке (перевод)'	
	],	
	
	platform : [
		'&raquo; Выбрать',
		'Windows',
		'Linux',
		'Windows+APK',
		'Linux+APK',
		'MacOS',
		'MacOS+APK',
		'Windows+Linux',
		'Windows+Linux+APK',
		'Windows+MacOS',
		'Windows+MacOS+APK',
		'Windows+Linux+MacOS',
		'Windows+Linux+MacOS+APK',
		'APK',		
		'PSP',		
		'PS1',
		'PS2',
		'PS3',
		'Xbox',
		'Xbox для Xbox 360',
		'Xbox 360',
		'NDS',
		'Wii',
		'Dreamcast',
		'Other'
	],
	
	platform_abr : [
		'',	
		'Win',	
		'Lin',
		'Win, APK',
		'Lin, APK',
		'Mac',
		'Mac, APK',
		'Win, Lin',
		'Win, Lin, APK',
		'Win, Mac',
		'Win, Mac, APK',
		'Win, Lin, Mac',
		'Win, Lin, Mac, APK',
		'APK',		
        'PSP',		
		'PS1',
		'PS2',
		'PS3',
		'Xbox',
		'Xbox360E',
		'Xbox360',
		'NDS',
		'Wii',
		'DC',
		''
	],	

	release_type : [
		'&raquo; Выбрать, если Демо/В раработке',
		'Демо-версия',
		'В разработке'
	],
	
	release_type_abr : [
		'',		
		'DEMO',
		'InProgress'
	],

	region : [
		'&raquo; Не требуется',
		'RegionFree',
		'PAL',
		'NTSC'
	],

	region_abr : [
		'',	
		'RegionFree',
		'PAL',
		'NTSC'
	],	
	
	tablet : [
		'&raquo; Не требуется',	
		'Нет в раздаче'
	],		
	
	rus_sub : ['', 'есть', 'нет'],

	rus_sub_abr : ['', 'есть', 'нет'],
	
	rus_sub2 : ['&raquo; Нет', 'Есть'],

	rus_sub2_abr : ['', 'Rus Sub'],

	eng_sub : ['', 'есть', 'нет'],

	eng_sub_abr : ['', 'есть', 'нет'],

	sub_dvd1 : [
		'&raquo; Отсутствуют/Выбрать',
		'Есть'
	],

	sub_dvd2 : [
		'&raquo; Отсутствуют/Выбрать',
		'Есть'
	],

	sub_dvd3 : [
		'&raquo; Отсутствуют/Выбрать',
		'Есть'
	],

	subtit1 : [
		'&raquo; Отсутствуют/Выбрать',
		'Встроенные неотключаемые (хардсаб)',
		'Встроенные отключаемые SRT',
		'Встроенные отключаемые ASS/SSA',
		'Встроенные отключаемые SUB',
		'Внешние SRT',
		'Внешние ASS/SSA',
		'Внешние SUB',
		'Неизвестно/ Другие'
	],

	subtit2 : [
		'&raquo; Отсутствуют/Выбрать',
		'Встроенные неотключаемые (хардсаб)',
		'Встроенные отключаемые SRT',
		'Встроенные отключаемые ASS/SSA',
		'Встроенные отключаемые SUB',
		'Внешние SRT',
		'Внешние ASS/SSA',
		'Внешние SUB',
		'Неизвестно/ Другие'
	],

	subtit3 : [
		'&raquo; Отсутствуют/Выбрать',
		'Встроенные неотключаемые (хардсаб)',
		'Встроенные отключаемые SRT',
		'Встроенные отключаемые ASS/SSA',
		'Встроенные отключаемые SUB',
		'Внешние SRT',
		'Внешние ASS/SSA',
		'Внешние SUB',
		'Неизвестно/ Другие'
	],
	
	vid_type_h  : [
		'&raquo; Тип видео',
		'GameRip',
		'Doujin',
		'AMV',
		'Motion Manga'
	], 

	vid_p_rip  : [
		'&raquo; Разрешение видео',
		'480p',
		'576p',
		'720p',
		'1080p'
	], 
	
	vid_p_rip_abr  : [
		'',
		'',
		'576p',
		'720p',
		'1080p'
	],
	
	vid_q_rip : [
		'&raquo; Качество видео',
		'BDRip',
		'HDRip',
		'DVDRip',
		'SiteRip',
		'VOD',
		'VHSRip',
		'SATRip',
		'TVRip',
		'CamRip',
		'GameRip',
		'WebRip',
		'WEB-DL',
		'WEB-DLRip',
		'Upscale'
	],
	
	vid_q_rip_mod : [
		'&raquo; Изменение в разрешении видео',
		'разрешение увеличено (upscale)',
		'разрешение уменьшено (downscale)'
	],
	
	vid_q_rip_mod_abr : [
		'',
		'upscale',
		'downscale'
	],
	
	vid_q_rip2D : [
		'&raquo; Качество видео',
		'BDRip',
		'HDRip',
		'DVDRip',
		'SiteRip',
		'VOD',
		'VHSRip',
		'SATRip',
		'TVRip',
		'CamRip',
		'WEB-DL',
		'WEB-DLRip',
		'Upscale'
	],
	
	vid_q_clip : [
		'&raquo; Качество видео',
		'Blu-Ray',
		'BDRip',
		'HDRip',
		'DVDRip',
		'DVDRemux',
		'SiteRip',
		'VOD',
		'VHSRip',
		'SATRip',
		'TVRip',
		'HDTVRip', 
		'CamRip',
		'WebCam',
		'WEB-DL',
		'Upscale'
	],
	
	vid_q_unif : [
		'&raquo; Качество видео',
		'Blu-Ray',
		'BDRip',
		'HDRip',
		'DVD5',
		'2x DVD5',
		'DVD9',
		'2x DVD9',
		'DVDRip',
		'SiteRip',
		'VOD',
		'VHSRip',
		'SATRip',
		'TVRip',
		'HDTVRip', 
		'CamRip',
		'WEBRip',
		'WebCam',
		'WEB-DL',
		'Upscale',
		'BDRemux'
	],
	
	vid_q_hd : [
		'&raquo; Качество видео',
		'Blu-Ray',
		'BDRip',
		'WEB-DL',
		'HDRip',
		'HDTV',
		'HDV',
		'Upscale'
	],


	vid_q_dvd : [
		'&raquo; Качество видео',
		'DVD5',
		'2x DVD5',
		'DVD9',
		'2x DVD9',
		'Upscale'
	],
	
	disktype : [
		'&raquo; Не диск/выбрать',
		'CD',
		'2x CD',
		'3x CD',		
		'DVD5',		
		'2x DVD5',
		'DVD9',
		'2x DVD9',
		'Blu-Ray'
	],	

	cens : [
		'',
		'Есть во всех файлах',
		'Отсутствует',
		'Убрана программой/нейросетью',
		'Есть в некоторых файлах',
		'Этти - легкая эротика',
		'Софткор - эротика с сексом'
	],

	cens_abr : [
		'',
		'cen',
		'uncen',
		'decen',
		'ptcen',
		'ecchi',
		'softcore'
	],
	
	cens_hart_hmanga : [
		'',
		'Есть',
		'Нет',
		'Есть в некоторых файлах',
		'Этти - легкая эротика',
		'Софткор - эротика с сексом'
	],

	cens_hart_hmanga_abr : [
		'',
		'cen',
		'uncen',
		'ptcen',
		'ecchi, uncen',
		'softcore, uncen'
	],
	
	cens_jap_vid : [
		'',
		'Есть во всех файлах',
		'Отсутствует',
		'Убрана программно',
		'Есть в некоторых файлах',
		'Этти - легкая эротика',
		'Софткор - эротика с сексом'
	],
	
	cens_jap_vid_abr : [
		'',
		'cen',
		'uncen',
		'decen',
		'ptcen',
		'ecchi',
		'softcore'
	],
	
	cens_long : [
		'',
		'Присутствует',
		'Отсутствует'
	],
	
	
	cens_long_abr : [
		'',
		'CENSORED',
		'UNCENSORED'
	],

	cens_game : [
		'',
		'Есть',
		'Нет',
		'Частично'
	],

	cens_game_abr : [
		'',
		'cen',
		'uncen',
		'ptcen'
	],	
	
	cens_film : [
		'',
		'Есть',
		'Отсутствует'
	],

	cens_film_abr : [
		'',
		'CENSORED',
		''
	],
	
	untr_genr : [
		'',
		'Бисексуалы',
		'БДСМ',
		'Дилдо',
		'Море спермы',
		'Фильм от John Thompson',
		'Фистинг',
		'Фетиш',
		'Писанье',
		'Беременные',
		'Страпон',
		'Транссексуалы',
		'Другое'
	],


	bi_trans : [
		'',
		'Бисексуалы',
		'Транссексуалы'
	],

	bi_trans_abr : [
		'',
		'bisex',
		'transsex'
	],

	untr_genr_abr : [
		'',
		'Bisex',
		'BDSM',
		'Dildo',
		'Bukkake',
		'JTPron',
		'Fisting',
		'Fetish',
		'Peeing',
		'Pregnant',
		'Strapon',
		'Transsex',
		'Rest'
	],

	hd_type : [
		'&raquo; Тип HD видео',
		'720p',
		'1080p',
		'2160p'
	],
	
	hd_type_3D_VR : [
		'&raquo; Тип HD видео',
		'SD',
		'720p',
		'960p',
		'1080p',
		'1440p',
		'1600p',
		'1700p',
		'1920p',
		'2048р',
		'2160p',
		'2700р',
		'2880p',
		'3072р'
	],
	
	hd_type_device_VR : [
		'&raquo; Тип устройства',
		'Oculus Rift / Vive',
		'Samsung Gear VR',
		'PlayStation VR',
		'Smartphone / Mobile'
	],

	hd_type_3D : [
		'&raquo; Тип 3D',
		'SideBySide',
		'OverUnder',
		'Anaglyph'
	],	
	
	hd_t_no : [
		'&raquo; Не HD видео',
		'720p',
		'1080p',
		'2160p'
	],

	ethno_type : [
		'',
		'Все актёры чёрные, все актрисы белые',
		'Все актёры чёрные, все актрисы азиатки',
		'Все актёры чёрные, все актрисы латинки',
		'Все актёры белые, все актрисы чёрные',
		'Все актёры белые, все актрисы индуски',
		'Все актёры белые, все актрисы азиатки',
		'Все актёры белые, все актрисы латинки',
		'Все актёры белые, все актрисы индуски',
		'Все актёры и актрисы чёрные',
		'Фильм производства Бразилии',
		'Фильм производства Израиля',
		'Другое'
	],

	ethno_type_abr : [
		'',
		'BDWC',
		'BDAC',
		'BDLC',
		'WDBC',
		'BDIC',
		'WDAC',
		'WDLC',
		'WDIC',
		'Black',
		'Brasil',
		'Israel',
		''
	],

	eurogirls_type : [
		'&raquo; Нет',
		'Есть'	
	],
	
	eurogirls_type_abr : [	
		'',
		'EuroGirls'	
	
	],
	
	menu : [
		'&raquo; Нет',
		'Есть'
	],
	
	split_scene : [
		'&raquo; Нет',
		'Да',
	],
	
	split_scene_abr : [
		'',
		'Split Scenes'
	],

	pack_type : [
		'',
		'В ПАКе меньше 15 роликов (MiniPack)',
		'В ПАКе от 15 до 50 роликов (Pack)',
		'В ПАКе больше 50 роликов (MegaPack)'
	],

	pack_type_abr : [
		'',
		'MiniPack',
		'Pack',
		'MegaPack',
		''
	],

	mag_format : [
		'&raquo; Формат раздачи',
		'JPG',
		'GIF',
		'PNG',
		'PDF',
		'MP3',
		'Другой'		
	],

	mag_format_abr : [
		'',
		'JPG',
		'GIF',
		'PNG',
		'PDF',
		'MP3',
		''
	],
	
	art_format : [
		'&raquo; Формат раздачи',
		'JPG',
		'PNG',
		'GIF',
		'Другой'
	],

	art_format_abr : [
		'',
		'JPG',
		'PNG',
		'GIF',
		''
	],

	comix_type : [
		'',
		'ART',
		'Audio',
		'Comix'
	],

	hentaiart_type : [
		'',
		'ART',
		'HCG'
	],
	
	gay_r_type : [
		'&raquo; Тип раздачи',
		'Фотографии',
		'Журналы',
		'Комиксы/ Манга',
		'Рисунки',
		'Другое'
	],

	gay_r_type_abr : [
		'',
		'GayPhoto',
		'GayMagazine',
		'GayComix',
		'GayCartoon',
		'Misc'
	],

	ero_studio : [
		'&raquo; Студия',
		'EvasGarden',
		'Hegre-Art',
		'Hegre-Archives',
		'Met-Art',
		'MetModels',
		'Watch4Beauty /W4B'
	],

	language_g : [
		'&raquo; Отсутствует/Выбрать',
		'Русский',
		'Русский(авто)',
		'Английский',
		'Английский(авто)',
		'Русский+Английский',
		'Японский',
		'Китайский',
		'Японский+Английский',
		'Японский+Английский(авто)',
		'Японский+Русский',
		'Японский+Русский(авто)',
		'Китайский+Английский',
		'Китайский+Английский(авто)',
		'Китайский+Русский',
		'Китайский+Русский(авто)',
		'Яп.+Англ.+Рус.',
		'Яп.+Англ.+Рус.(авто)',
		'Кит.+Англ.+Рус.',
		'Кит.+Англ.+Рус.(авто)',
		'Яп.+Кит.+Англ.+Рус.',
		'Яп.+Кит.+Англ.+Рус.(авто)',
		'Яп.+Кит.+Англ.+Рус.и др.',
		'Яп.+Кит.+Англ.+Рус.(авто)и др.',
		'Неизвестен'
	],
	
	language1 : [
		'&raquo; Отсутствует/Выбрать',
		'Русский',
		'Английский',
		'Испанский',
		'Немецкий',
		'Французский',
		'Итальянский',
		'Японский',
		'Неизвестен/ Другой'
	],

	language1_abr : [
		'',
		'rus',
		'eng',
		'spa',
		'ger',
		'fra',
		'ita',
		'jap',
		''
	],
	
	language_h : [
		'&raquo; Отсутствует/Выбрать',
		'Японский',
		'Русский',
		'Английский',
		'Испанский',
		'Немецкий',
		'Французский',
		'Итальянский',
		'Неизвестен/ Другой',
		'Без диалогов',
		'Без звука'
	],

	language_h_abr : [
		'',
		'jap',
		'rus',
		'eng',
		'spa',
		'ger',
		'fra',
		'ita',
		'',
		'no dialogue',
		'no sound'
	],

	language2 : [
		'&raquo; Отсутствует/Выбрать',
		'Русский',
		'Английский',
		'Испанский',
		'Немецкий',
		'Французский',
		'Итальянский',
		'Японский',
		'Неизвестен/ Другой'
	],

	language2_abr : [
		'',
		'rus',
		'eng',
		'spa',
		'ger',
		'fra',
		'ita',
		'jap',
		''
	],

	language3 : [
		'&raquo; Отсутствует/Выбрать',
		'Русский',
		'Английский',
		'Испанский',
		'Немецкий',
		'Французский',
		'Итальянский',
		'Японский',
		'Неизвестен/ Другой'
	],
	

	language3_abr : [
		'',
		'rus',
		'eng',
		'spa',
		'ger',
		'fra',
		'ita',
		'jap',
		''
	],

	language4 : [
		'',
		'Русский',
		'Английский',
		'Испанский',
		'Немецкий',
		'Французский',
		'Итальянский',
		'Португальский',
		'Японский',
		'Неизвестен/ Другой'
	],

	language4_abr : [
		'',
		'rus',
		'',
		'',
		'',
		'',
		'',
		'',
		''
	],
	
	lang_g : [
		'&raquo; Выбрать',
		'Русский',
		'Русский(авто)',
		'Английский',
		'Английский(авто)',
		'Русский+Английский',
		'Русский+Английский и др.',
		'Русский+Английский(авто) и др.',
		'Японский',
		'Китайский',
		'Японский+Английский',
		'Японский+Английский(авто)',
		'Японский+Русский',
		'Японский+Русский(авто)',
		'Китайский+Английский',
		'Китайский+Английский(авто)',
		'Китайский+Русский',
		'Китайский+Русский(авто)',
		'Яп.+Англ.+Рус.',
		'Яп.+Англ.+Рус.(авто)',
		'Кит.+Англ.+Рус.',
		'Кит.+Англ.+Рус.(авто)',
		'Яп.+Кит.+Англ.+Рус.',
		'Яп.+Кит.+Англ.+Рус.(авто)',
		'Яп.+Кит.+Англ.+Рус.и др.',
		'Яп.+Кит.+Англ.+Рус.(авто)и др.',
		'Неизвестен'
	],
	
	menulang_g : [
		'&raquo; Выбрать',
		'Русский',
		'Русский(авто)',
		'Английский',
		'Английский(авто)',
		'Русский+Английский',
		'Русский+Английский и др.',
		'Русский+Английский(авто) и др.',
		'Японский',
		'Китайский',
		'Японский+Английский',
		'Японский+Английский(авто)',
		'Японский+Русский',
		'Японский+Русский(авто)',
		'Китайский+Английский',
		'Китайский+Английский(авто)',
		'Китайский+Русский',
		'Китайский+Русский(авто)',
		'Яп.+Англ.+Рус.',
		'Яп.+Англ.+Рус.(авто)',
		'Кит.+Англ.+Рус.',
		'Кит.+Англ.+Рус.(авто)',
		'Яп.+Кит.+Англ.+Рус.',
		'Яп.+Кит.+Англ.+Рус.(авто)',
		'Яп.+Кит.+Англ.+Рус.и др.',
		'Яп.+Кит.+Англ.+Рус.(авто)и др.',
		'Неизвестен'
	],
	
	lang_g_abr : [
		'',
		'rus',
		'rus(auto)',
		'eng',
		'eng(auto)',
		'rus+eng',
		'rus+eng+multi',
		'rus+eng(auto)+multi',
		'jap',
		'chi',
		'jap+eng',
		'jap+eng(auto)',
		'jap+rus',
		'jap+rus(auto)',
		'chi+eng',
		'chi+eng(auto)',
		'chi+rus',
		'chi+rus(auto)',
		'jap+eng+rus',
		'jap+eng+rus(auto)',
		'chi+eng+rus',
		'chi+eng+rus(auto)',
		'jap+chi+eng+rus',
		'jap+chi+eng+rus(auto)',
		'jap+chi+eng+rus+multi',
		'jap+chi+eng+rus(auto)+multi',
		''
	],

	menulang1 : [
		'&raquo; Отсутствует/Выбрать',
		'Русский',
		'Английский',
		'Испанский',
		'Немецкий',
		'Французский',
		'Итальянский',
		'Японский',
		'Неизвестен/ Другой'
	],	

	menulang2 : [
		'&raquo; Отсутствует/Выбрать',
		'Русский',
		'Английский',
		'Испанский',
		'Немецкий',
		'Французский',
		'Итальянский',
		'Японский',
		'Неизвестен/ Другой'
	],	

	menulang3 : [
		'&raquo; Отсутствует/Выбрать',
		'Русский',
		'Английский',
		'Испанский',
		'Немецкий',
		'Французский',
		'Итальянский',
		'Японский',
		'Неизвестен/ Другой'
	],		
		
	mag_cntry : [
		'&raquo; Выберите страну',
		'Россия',
		'США',
		'Германия',
		'Франция',
		'Италия',
		'Украина'
	],

	mag_cntry_abr : [
		'&raquo; выберите страну',
		'RUS',
		'USA',
		'DE',
		'FRA',
		'ITA',
		'UA'
	],
	vid_frmt_rip : [
		'&raquo; Формат видео',
		'AVI',
		'MKV',
		'MPEG',
		'WMV',
		'OGM',
		'MP4',
		'ASF',
		'FLV',
		'MOV',
		'RM/RAM/RMVB',
		'3GP'
	],

	vid_frmt_unif : [
		'&raquo; Формат видео',
		'Blu-Ray Video',
		'DVD Video',
		'AVI',
		'MPEG',
		'WMV',
		'ASF',
		'FLV',
		'MOV',
		'OGM',
		'MKV',
		'RM/RAM/RMVB',
		'3GP',
		'MP4'
	],

	vid_frmt_hd : [
		'&raquo; Формат видео',
		'Blu-Ray Video',
		'AVI',
		'MOV',
		'MKV',
		'WMV',
		'MPEG',
		'MP4',
		'FLV'
	],

	vid_cdc_rip : [
		'&raquo; Видео кодек',
		'DivX',
		'XviD',
		'MPEG1',
		'MPEG2',
		'Windows Media',
		'QuickTime',
		'RealVideo',
		'H.264/AVC',
		'Flash'
	],

	vid_cdc_hd : [
		'&raquo; Видео кодек',
		'DivX',
		'MPEG2',
		'Windows Media',
		'QuickTime',
		'H.264/AVC'
	],

	aud_cdc_rip : [
		'&raquo; Аудио кодек',
		'MP3',
		'WMA',
		'OGG Vorbis',
		'Real Cooker',
		'DTS',
		'AAC',
		'PCM',
		'AC3'
	],

	aud_cdc_dvd : [
		'&raquo; Аудио кодек',
		'MPEG',
		'PCM',
		'DTS',
		'AC3'
	],

	// dummy
	dummy : ['']
};


function preg_quote (str) {
	return (str+'').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");  // http://kevin.vanzonneveld.net
}


function tpl_submit ()
{
	if ( TPL.build_msg_all('tpl-post-message', 'tpl-post-subject') ) {
	 $('#tpl-post-form').submit();
	}
}
</script>

<h1 class="maintitle"><a href="./viewforum.php?f=1756">Игры: визуальные новеллы (профессиональная студия) / Games: Visual Novels (Pro)</a></h1>

<div class="nav">
	<p class="floatL"><a href="./index.php">Список форумов pornolab.net</a></p>
	<p class="floatR">
				<a href="posting.php?mode=newtopic&amp;f=1756">Создать обычную тему</a>
	</p>
	<div class="clear"></div>
</div>

<div style="display: none;">
	<textarea id="tpl-src-form" rows="10" cols="10"></textarea>
	<textarea id="tpl-src-title" rows="10" cols="10"></textarea>
	<textarea id="tpl-src-msg" rows="10" cols="10"></textarea>
	<textarea id="tpl-src-sel" rows="10" cols="10"></textarea>
</div>
<script type="text/javascript">
$(function(){
	TPL.build_tpl_form( $('#tpl-src-form').val(), 'rel-tpl' );
	initPost('#tpl-rules-html');
	if (custom_tpl_script) $('<script>').text(custom_tpl_script).appendTo(document.body);
});
</script>
<style type="text/css">
<!--
li {
	margin-top: 4px;
}
-->
</style>
<table class="forumline">
<tr>
	<th>Общие правила и положения</th>
</tr>
<tr>
	<td class="row1">

<div class="w95 bCenter">

<h1 class="tCenter mrg_14">Правила оформления раздач на трекере <a href="index.php">PornoLab.net</a></h1>

<div>
	<ul>
		<li>
		Все создающие раздачу (выкладывающие файл для общедоступного скачивания) обязаны контролировать содержимое этой раздачи и качество (соответствие содержимого названию и Правилам, отсутствие звука в фильмах, несинхронность изображения и звука, качество изображения).
		</li>
		<li>
		Данная конференция ведется на русском языке. Для не имеющих возможности писать кириллицей в форум встроена <a href="viewtopic.php?t=980542#vkey">виртуальная русская клавиатура</a> и <a href="viewtopic.php?t=980542#translit">транслит</a>. Модераторы и Координаторы оставляют за собой право редактировать или удалять сообщения, написанные латиницей, без предупреждения.
		</li>
		<li>
		Перед тем, как создавать раздачу, воспользуйтесь <a href="viewtopic.php?t=101236">поиском</a> - возможно такая раздача уже существует.
		</li>
	</ul>
</div>

<h2 class="tCenter mrg_8">Всем участникам раздач на данном трекере запрещается:</h2>

<div>
 <ul>
 	<li>Создавать раздачу, дублирующую то, что уже существует на трекере. Дублирующей считается не отличающаяся ни по содержанию, ни по качеству информация.</li>
 	<li>Создавать раздачу, содержащую видео и/или фото педофилии, некрофилии и зоофилии, а также содержащие сцены насилия и членовредительства.</li>
 	</ul>
</div>

<h6 class="tCenter mrg_8 bCenter w85">Решение по соответствию раздачи данным требованиям принимает <a href="groupcp.php?g=104787"><b>Модератор</b></a> или <b><a href="groupcp.php?g=104792">Координатор</a></b>. В их полномочиях редактировать, перемещать, закрывать или удалять раздачу.</h6>

</div>

	</td>
</tr>
</table>

<br /><table class="forumline">
<tr>
	<th>Правила оформления</th>
</tr>
<tr>
	<td class="row1">
	<div class="w95 bCenter" style="padding: 12px;" id="tpl-rules-html"><div class="post-align" style="text-align: center;" data-placement="center"><div class="q-wrap"><div class="q"><span style="font-family: Arial;"><span class="post-b">Перед тем, как начинать оформление раздачи, вы должны обязательно ознакомиться со следующими темами:<br /><a href="http://pornolab.net/forum/viewtopic.php?t=101236" class="postLink">Как пользоваться поиском</a><br /><a href="http://pornolab.net/forum/viewtopic.php?t=1028368#screen" class="postLink">Как сделать скриншот с игры</a><br /><a href="http://pornolab.net/forum/viewtopic.php?t=980456" class="postLink">Как залить картинку на бесплатный хост</a><br /><a href="http://pornolab.net/forum/viewtopic.php?t=980542" class="postLink">Как правильно писать на форуме</a><br /><a href="http://pornolab.net/forum/viewtopic.php?t=1028368" class="postLink">&gt;&gt;&gt; [ ПРАВИЛА ПОДРАЗДЕЛА ИГР ] &lt;&lt;&lt;</a></span></div></div><span class="post-hr">-</span><table class="post-table-user"><tr class="post-tr-user"><td class="post-td-user"><span style="font-size: 20px; line-height: normal;">&#128161;<span class="post-b"><span class="post-color-text" style="color: #009F00;">И</span><span class="post-color-text" style="color: #00A200;">н</span><span class="post-color-text" style="color: #00A500;">с</span><span class="post-color-text" style="color: #00A800;">т</span><span class="post-color-text" style="color: #00AB00;">р</span><span class="post-color-text" style="color: #00AE00;">у</span><span class="post-color-text" style="color: #00B100;">к</span><span class="post-color-text" style="color: #00B400;">ц</span><span class="post-color-text" style="color: #00B700;">и</span><span class="post-color-text" style="color: #00BA00;">я</span> <span class="post-color-text" style="color: #00BD00;">п</span><span class="post-color-text" style="color: #00C000;">о</span> <span class="post-color-text" style="color: #00C300;">с</span><span class="post-color-text" style="color: #00C600;">о</span><span class="post-color-text" style="color: #00C900;">з</span><span class="post-color-text" style="color: #00CC00;">д</span><span class="post-color-text" style="color: #00CF00;">а</span><span class="post-color-text" style="color: #00D200;">н</span><span class="post-color-text" style="color: #00D500;">и</span><span class="post-color-text" style="color: #00D800;">ю</span> <span class="post-color-text" style="color: #00DB00;">р</span><span class="post-color-text" style="color: #00DE00;">а</span><span class="post-color-text" style="color: #00E100;">з</span><span class="post-color-text" style="color: #00E400;">д</span><span class="post-color-text" style="color: #00E100;">а</span><span class="post-color-text" style="color: #00DE00;">ч</span><span class="post-color-text" style="color: #00DB00;">и</span><span class="post-color-text" style="color: #00D800;">:</span></span></span></td></tr></table></div></span><span class="post-br"><br /></span><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#128204;1. Убедитесь, что содержимое вашей будущей раздачи соответствует требованиям:</span></span><div class="sp-wrap"><div class="sp-body" title="Внимание! Запрещается&amp;#58;"><h3 class="sp-title">Внимание! Запрещается&amp;#58;</h3>&#9940;<span style="font-family: Arial;"><span class="post-b"><span class="post-color-text" style="color: red;">Запрещается</span> раздача информации, которая повторяет или дублирует уже выложенную на трекере!</span>  Повтором считается не отличающаяся ни по качеству ни по содержанию информация. Дублированием считается раздача незначительно уступающая по качеству изображения и/или звука.<br />  &#9940;<span class="post-b"><span class="post-color-text" style="color: red;">Запрещается</span> раздача в архивах! </span> Исключением могут быть: сборники модов, сейвов, патчей и т.п, а также случаи технических проблем с раздачами. Уточняйте у модераторов.<br />  &#9940;<span class="post-b"><span class="post-color-text" style="color: red;">Запрещается</span> создавать раздачу сборника игр несвязанных между собой!</span> Только одноименная серия игр или сборник по разработчику, где игры имеют небольшой размер. Например, мелкие флешки или по весу от 0 ~ 200 MB.<br />  &#9940;<span class="post-b"><span class="post-color-text" style="color: red;">Запрещается</span> намеренная упаковка игр в свои самораспаковывающиеся архивы или сетапы (.exe)!</span> Мы приветствует оригинальный вид и состав файлов. Исключением могут быть случаи технических проблем с раздачами. Уточняйте у модераторов.<br />  &#9940;<span class="post-b"><span class="post-color-text" style="color: red;">Запрещается</span> создавать раздачу объёмом 10 MB и менее!</span> Исключением могут быть сборники модов, сейвов, патчей и т.п. Уточняйте у модераторов.<br />  &#9940;<span class="post-b"><span class="post-color-text" style="color: red;">Запрещается</span> создавать раздачи игр:</span><br /><span style="font-family: Arial;">1) Которые можно скачать с официального сайта, где требуется обязательная онлайн регистрация, создание логина и пароля, при этом присутствует встроенный магазин продаж за реальные деньги.<br />2) Где присутствует минимальный игровой контент, а далее идет блокировка процесса игры с принудительным требованием оплаты продукта.<br />3) В которые встроена навязчивая реклама сторонних сайтов или трекеров.<br /></span><span class="post-br"><br /></span><ol style="list-style: disc;"><li>&#9940;<span style="font-size: 14px; line-height: normal;"><span class="post-color-text" style="color: red;"><span class="post-b"><span style="font-size: 16px; line-height: normal;">Запрещенные теги на трекере</span>: Animal, Zoo, Beasteality, Zoophiles, Zoophilia, Beast, Lolicon/Loli, Shotacon/Shota, Toddlercon.<br /><li>&#9940;<span style="font-size: 14px; line-height: normal;"><span class="post-color-text" style="color: red;"><span class="post-b"><span style="font-size: 16px; line-height: normal;">Запрещено раздавать</span> материал с реалистичным изображением несовершеннолетних (на усмотрение администрации).</span></span></span><br /><li>&#9940;<span style="font-size: 14px; line-height: normal;"><span class="post-color-text" style="color: red;"><span class="post-b"><span style="font-size: 16px; line-height: normal;">Запрещено раздавать</span> Toddlercon (дет.сад и младше).</span></span></span></ol></span></div></div><span class="post-hr">-</span><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#128204; 2. Поиск на повтор: ищем на трекере игру, которую хотите раздать по английскому/ромадзи/оригинальному названию, а также по имени разработчика.</span></span><br />- Если активная раздача не обнаружена, то переходим к следующему шагу.<br />- Если игра уже есть на теркере, то увы, придется подыскать что-то ещё, чем можно поделиться.<div class="sp-wrap"><div class="sp-body" title="Подробнее"><h3 class="sp-title">Подробнее</h3><var class="postImg postImgAligned img-right" title="https://static.pornolab.net/pic/template_games_1.jpg">&#10;</var><var class="postImg postImgAligned img-right" title="https://static.pornolab.net/pic/template_games_2.jpg">&#10;</var><var class="postImg postImgAligned img-right" title="https://static.pornolab.net/pic/template_games_3.jpg">&#10;</var><span class="post-b"><span class="post-color-text" style="color: #0017C4;">П</span><span class="post-color-text" style="color: #002EC6;">о</span><span class="post-color-text" style="color: #0045C8;">и</span><span class="post-color-text" style="color: #005CCA;">с</span><span class="post-color-text" style="color: #0073CC;">к</span><span class="post-color-text" style="color: #008ACE;">:</span></span><ol style="list-style: disc;"><span style="font-family: Arial;">Ищите не только по названию игры, но и по разработчику.<br />Если игра азиатская, поиск по оригинальному названию (иероглифами) делайте через меню "в google".<span class="post-br"><br /></span>Если вам известна дата релиза, то можно глянуть темы:</span><ol style="list-style: disc;"><li><a href="http://pornolab.net/forum/viewtopic.php?t=1298937" class="postLink"><span class="post-b"><span style="font-family: Arial;">НОВИНКИ игр - новостная лента / NEW games - News Feed (Общий раздел игр)</span></span></a><br /><li><a href="http://pornolab.net/forum/viewtopic.php?t=1070156" class="postLink"><span class="post-b"><span style="font-family: Arial;">Новинки японских Eroge</span></span> (Визуальные новеллы)</span></a><br /><li><a href="http://pornolab.net/forum/viewtopic.php?t=1668949" class="postLink"><span class="post-b"><span style="font-family: Arial;">НОВИНКИ игр - новостная лента / NEW games - News Feed (РПГ игры на движках RPG Maker и WOLF RPG Editor)</span></span></a><br /><li><a href="http://pornolab.net/forum/viewtopic.php?t=1701742" class="postLink"><span class="post-b"><span style="font-family: Arial;">НОВИНКИ анимации - новостная лента / NEW animation - News Feed</span></span></a></ol></ol></div></div><span class="post-hr">-</span><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#128204;3. Создание темы с заполнением всех пунктов шаблона.</span></span><div class="sp-wrap"><div class="sp-body" title="Подробнее"><h3 class="sp-title">Подробнее</h3><a name="0"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#tag">&#9196;<span class="post-color-text" style="color: indigo;">Категории</span></a></div><div class="post-align" style="text-align: center;" data-placement="center"><table class="post-table-user"><tr class="post-tr-user"><td class="post-td-user"><a class="postLink-name" href="#1"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#10060;1.Заголовок темы</span></span></a></td>  <td class="post-td-user"><a class="postLink-name" href="#7"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#11093;7.Цензура</span></span></a></td> <td class="post-td-user"><a class="postLink-name" href="#13"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#10060;13.Описание</span></span></a></td></tr><tr class="post-tr-user"><td class="post-td-user"><a class="postLink-name" href="#2"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#11093;2.Постер</span></span></a></td> <td class="post-td-user"><a class="postLink-name" href="#8"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#10060;8.Разработчик/Издатель</span></span></a></td> <td class="post-td-user"><a class="postLink-name" href="#14"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#11093;14.Доп. информация</span></span></a></td></tr><tr class="post-tr-user"><td class="post-td-user"><a class="postLink-name" href="#3"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#10060;3.Название</span></span></a></td> <td class="post-td-user"><a class="postLink-name" href="#9"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#11093;9.Платформа / Тип издания / Лекарство</span></span></a></td> <td class="post-td-user"><a class="postLink-name" href="#15"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#10060;15.Порядок установки</span></span></a></td></tr><td class="post-td-user"><a class="postLink-name" href="#4"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#11093;4.Год выпуска</span></span></a></td> <td class="post-td-user"><a class="postLink-name" href="#10"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#10060;10.Версия</span></span></a></td> <td class="post-td-user"><a class="postLink-name" href="#16"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#11093;16.Скриншоты</span></span></a></td></tr><td class="post-td-user"><a class="postLink-name" href="#5"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#10060;5.Дата релиза </span></span></a></td> <td class="post-td-user"><a class="postLink-name" href="#11"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#11093;11.Язык игры</span></span></a></td> <td class="post-td-user"><a class="postLink-name" href="#17"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#10060;17.Папка раздачи</span></span></a></td></tr><span class="post-br"><br /></span><td class="post-td-user"><a class="postLink-name" href="#6"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#11093;6.Теги</span></span></a></td> <td class="post-td-user"><a class="postLink-name" href="#12"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#10060;12.Системные требования</span></span></a></td> <td class="post-td-user"><a class="postLink-name" href="#18"><span style="font-family: Arial;"><span style="font-size: 14px; line-height: normal;">&#11093;18.Торрент-файл</span></span></a></td></table></div><span class="post-br"><br /></span><span class="post-hr">-</span><a name="1"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Заголовок темы</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Заголовок темы</span></span><ol style="list-style: disc;"><span class="post-color-text" style="color: green;">(В редакторе-шаблоне создаётся автоматически)</span><br />Формат:<div class="c-wrap"><div class="c-head"><b>Код:</b></div><div class="c-body">Название &#91;Версия&#93; (Разработчик&#41; &#91;Цензура&#93; &#91;Год, Теги жанров&#93; &#91;Язык&#93;</div></div><div class="sp-wrap"><div class="sp-body" title="Подробнее"><h3 class="sp-title">Подробнее</h3><li><span class="post-b">Порядок названий в заголовке</span>:<div class="q-wrap"><div class="q" head="В последовательности&amp;#58;">- Для <span class="post-b">азиатских</span> игр:<br /><span class="post-b"><span class="post-color-text" style="color: darkgreen;">Оригинальное название иероглифами / Английское название / Русское название (необязательно)</span></span><span class="post-br"><br /></span>- Для <span class="post-b">остальных</span> игр:<br /><span class="post-b"><span class="post-color-text" style="color: darkgreen;">Английское название / Русское название</span></span><span class="post-br"><br /></span>Часть этого списка может отсутствовать (например, у данного материала может не быть альтернативных названий, либо может не быть английского названия), тогда заполнение названий происходит в том-же порядке, а отсутствующие элементы следует опустить.<br />Перевод можно через <a href="http://translate.google.com/" class="postLink">Translate Google</a>.<br />Если название игры на японском, а в интернете нету английского перевода? - используйте <a href="http://nihongo.j-talk.com/" class="postLink">Kanji Converter</a> (для перевода японских названий).<span class="post-br"><br /></span><span class="post-color-text" style="color: red;">*<span class="post-b">примечание</span>: Русским названием в заголовке можно пренебречь, когда название очень длинное и основные теги не помещаются в заголовок.<br />Укажите его в теле сообщения, если хочется.</span></div></div><ol style="list-style: disc;"><li><span class="post-b">Наличие цензуры указывается в заголовке тэгом</span> <span class="post-b">[cen] / [uncen] / [ptcen]:</span><br /><span class="post-b">[cen]</span> - Есть<br /><span class="post-b">[uncen]</span> - Нет<br /><span class="post-b">[ptcen]</span> - Частично</ol><ol style="list-style: disc;"><li><span class="post-b">В конце заголовка каждой темы должен стоять тег обозначающий язык сюжета игры</span><br /><span class="post-b">[eng]</span> - английский<br /><span class="post-b">[jap]</span> - японский<br /><span class="post-b">[rus]</span> - русский</ol><span class="post-b">Пример полностью сформированного заголовка</span>:<div class="c-wrap"><div class="c-head"><b>Код:</b></div><div class="c-body">&#12524;&#12452;&#12497;&#12540;&#12474;&#12503;&#12522;&#12540;&#12474; / Rapers Please / Rei Pazu Purizu &#91;Final&#93; (&#12363;&#12425;&#12354;&#12370;&#12459;&#12531;&#12497;&#12491;&#12540; / Karaage Kompany&#41; &#91;cen&#93; &#91;2022, SLG, Animation, Oral, Anal, Vaginal, Titsjob, Group, Rape, Female Protagonist&#93; &#91;rus+eng&#93;</div></div></div></div></ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="2"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Постер</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Постер</span></span><ol style="list-style: disc;"><span class="post-color-text" style="color: red;">Обязательный пункт!</span><br />Объем - менее 300 KB. Разрешение - не более 1200 px. Формат - jpg. Заливать картинку только на рекомендуемые хостинги. <a href="http://pornolab.net/forum/viewtopic.php?t=980456" class="postLink"><span class="post-b">Как залить картинку на бесплатный хост</span></a>. См. настройки в спойлере "Настройки загрузки" ниже.<div class="sp-wrap"><div class="sp-body" title="Подробнее"><h3 class="sp-title">Подробнее</h3>- Постером является изображение, используемое в качестве обложки для раздачи.<br />- Количество постеров не должно превосходить 2-х. Большее количество постеров допустимо по усмотрению модератора.<br />- В случае нескольких изображений требования действуют на каждое из них по отдельности.</div></div><div class="sp-wrap"><div class="sp-body" title="Настройки загрузки"><h3 class="sp-title">Настройки загрузки</h3><div class="post-align" style="text-align: center;" data-placement="center"><a href="https://new.fastpic.org/" class="postLink">https://new.fastpic.org/</a><br /><table class="post-table-user"><tr class="post-tr-user"><td class="post-td-user"><span class="post-b"><span class="post-u">Настройки:</span></span><span class="post-br"><br /></span><a href="https://static.pornolab.net/pic/pos1.gif" class="postLink"><var class="postImg" title="https://static.pornolab.net/pic/pos1-1.gif">&#10;</var></a></td> <td class="post-td-user"><span class="post-b"><span class="post-u">Вставка кода:</span></span><span class="post-br"><br /></span><a href="https://static.pornolab.net/pic/pos2.gif" class="postLink"><var class="postImg" title="https://static.pornolab.net/pic/pos2-2.gif">&#10;</var></a></td></tr></table></div></div></div></ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="3"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Название</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Название</span></span><ol style="list-style: disc;"><span class="post-b"><span class="post-color-text" style="color: #1824c5;">Оригинальное название (иероглифы):</span></span><br /><span class="post-color-text" style="color: red;">Обязательный пункт для азиатских игр!</span><br />Японские и др. иероглифы.<span class="post-br"><br /></span><span class="post-b"><span class="post-color-text" style="color: #1824c5;">Оригинальное название:</span></span><br /><span class="post-color-text" style="color: red;">Обязательный пункт!</span><br />Английское название.<span class="post-br"><br /></span><span class="post-b"><span class="post-color-text" style="color: #1824c5;">Название на русском:</span></span><br />Необязательный пункт. По желанию. Перевод можно через <a href="http://translate.google.ru/" class="postLink"><span class="post-b">Translate Google</span></a></ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="4"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Год выпуска</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Год выпуска</span></span><ol style="list-style: disc;"><span class="post-color-text" style="color: red;">Обязательный пункт!</span><br /><span style="font-family: Arial;">Пример: 2026<br />2026-2027 (для сборников разного года)</span></ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="5"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Дата релиза</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Дата релиза</span></span><ol style="list-style: disc;">Официальная дата релиза игры (см. в магазинах или оф. сайт).<br />Формат: "год/мес/число".<br />Пример: 2026/01/01</ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="6"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Теги</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Теги</span></span><ol style="list-style: disc;"><span class="post-color-text" style="color: red;">Обязательный пункт!</span><br />Используйте редактор тегов.<br /><table class="post-table-user"><td class="post-td-user"><a href="https://static.pornolab.net/tag_editor/?conf=game" class="postLink"><span style="font-size: 16px; line-height: normal;"><span class="post-b">&#128681;Редактор - Теги и Иконки</span></span></a></td></tr></table><div class="sp-wrap"><div class="sp-body" title="Подробнее"><h3 class="sp-title">Подробнее</h3><ol style="list-style: disc;"><li>&#10071; <span class="post-color-text" style="color: red;">Обязательно! </span>Теги должны быть через запятую ',' и разделены пробелом!<br /><li>&#10071;<span class="post-color-text" style="color: red;">Важно:</span> Приоритет по указанию тегов: <span class="post-b">Игровой &gt; Графический &gt; Секс &gt; Движок игры</span><br /><li><span class="post-b">Первый тег - основной игровой жанр игры.</span> Указывайте внимательней. После него можно указать вторичные игровые жанры. По ним будет определятся содержимое игры и в каком подразделе будет находится раздача.</span><br /><li>Обязательные теги - <span class="post-b">Игровые, OS.</span> Важно указать графические: 3D/3DCG, если есть. 2D графику не указываем. У нас нет такого тега. По умолчанию для всех игр.<br /><li>Теги необходимы для упрощения поиска и повышения популярности вашего релиза.&lt;/li&gt;&lt;/p&gt;&lt;p&gt;&lt;li&gt; Достаточно указать пару-тройку основных секс жанров. При желании укажите более подробно - повысит эффективность.<br /><li>Достаточно указать пару-тройку основных секс тегов. При желании укажите более подробно - повысит эффективность.<br /><li><span class="post-b">Количество символов в заголовке ограничено.</span> Примерно ~ 500 символов на весь заголовок. Не увлекайтесь. При превышении лимита сайт форума выдаст ошибку.<br /><li>Проверяйте правильность написания тега. Одна неточность, лишняя или неверная буква и поиск не сработает.<br /><li>Когда берёте теги с других ресурсов, меняйте их под наши теги через редактор. Если у нас такого тега нет, то уточните его актуальность у модератора.<br /><li>Указывайте все виды операционных систем для игры в раздаче. Одна или несколько.<br /><li>Укажите движок игры, если вы понимаете на каком сделана игра. Популярные - UE3/UE4; Unity; GAMEMAKER; REN’PY; VN MAKER; RPG MAKER; WOLF Engine и др..</ol></div></div></ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="7"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Цензура</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Цензура</span></span><ol style="list-style: disc;"><span class="post-color-text" style="color: red;">Обязательный пункт!</span><br />Выбрать необходимое из списка. Цензурой считается любой вид скрытия гениталий - от полосок до размытия.<ol style="list-style: disc;"><li><span class="post-b">Есть</span>  [cen] - по умолчанию для всех японских игр (законодательство).<br /><li><span class="post-b">Нет</span>  [uncen] - полностью отсутствует или есть патч для удаления.<br /><li><span class="post-b">Частично</span>  [ptcen] - у части материала в игре остаётся цензура.</ol></ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="8"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Разработчик/Издатель</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Разработчик/Издатель</span></span><ol style="list-style: disc;"><span class="post-color-text" style="color: red;">Обязательный пункт!</span><br />Без дублей, если разработчик и издатель совпадают.<br />Для азиатских игр - имя иероглифами / на английском</ol><span class="post-hr">-</span><a name="9"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Платформа / Тип издания / Лекарство</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Платформа / Тип издания / Лекарство</span></span><ol style="list-style: disc;"><span class="post-color-text" style="color: red;">Обязательный пункт!</span><br />Выбрать необходимое из списка.<div class="sp-wrap"><div class="sp-body" title="Тип издания"><h3 class="sp-title">Тип издания</h3>Данный пункт только для игр "Демо-версии" или "В разработке".<ol style="list-style: disc;"><li><span class="post-b">Демо-версия</span> - ознакомительная версия игры.<br /><li><span class="post-b">В разработке</span> - ранняя-средняя стадия готовности игры.</ol></div></div><div class="sp-wrap"><div class="sp-body" title="Лекарство"><h3 class="sp-title">Лекарство</h3><ol style="list-style: disc;"><li>Если для запуска игры необходим серийник, кряк или кейген, он должен быть включен в раздачу.<br /><li>Если для вашей игры нужен кряк, об этом следует указать в оформлении.</ol></div></div></ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="10"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Версия</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Версия</span></span><ol style="list-style: disc;">Пример: v.1.1<br />Указывайте тег для:<br />- демо-версий: DEMO<br />- в разработке: InProgress<div class="sp-wrap"><div class="sp-body" title="Подробнее"><h3 class="sp-title">Подробнее</h3><span style="font-family: Arial;"><span class="post-u">[DEMO] - "Демо-Версией" считается:</span></span><ol style="list-style: disc;">Небольшая часть игры для ознакомления с продуктом.<br />Никаких обновлений и новых версий для них не предусматривается.</ol><span class="post-hr">-</span><span class="post-u">[InProgress] - "В разработке" считается:</span><ol style="list-style: disc;">Игра в процессе <span class="post-b">начальной</span> разработки. Альфа, Бета и др. версии.<br />Обновления и новые версии выходят с периодичностью.<br /><span class="post-b">При выходе первой стабильной версии этот тег необходимо убрать.</span></ol></div></div></ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="11"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Язык игры</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Язык игры</span></span><ol style="list-style: disc;"><span class="post-color-text" style="color: red;">Обязательный пункт!</span><br />Выбрать необходимое из списка.</ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="12"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Системные требования</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Системные требования</span></span><ol style="list-style: disc;"><span class="post-color-text" style="color: red;">Обязательный пункт!</span><br />Необходимо указать минимальные системные требования к игре: OS (система) | CPU (процессор) | GPU (графическая карта) | RAM (оперативная память) | VRAM (видео память) | HDD (объём на диске).<br />Если данных по игре в сети нет, то указать минимум данных - OS (система) и HDD (объём на диске).<br />Пример: OS: Windows7 | CPU: Pentium4 2.0GHz | RAM: 512MB | VRAM: 128MB | HDD: 3GB</ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="13"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Описание</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Описание</span></span><ol style="list-style: disc;"><span class="post-color-text" style="color: red;">Обязательный пункт!</span><br />Описание игры на русском или английском языке. Дополнительное описание на других языках скрыть спойлером<br />Должно быть читаемым и понятным. Без мата и жаргона.<br />Перевод можно через <a href="http://translate.google.ru/" class="postLink"><span class="post-b">Translate Google</span></a></ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="14"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Доп. информация</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Доп. информация</span></span><ol style="list-style: disc;">Особенности игры, отличия от аналогичных раздач, ссылки на базы данных и официальные странички и т.п..<br />В помощь <a href="http://pornolab.net/forum/viewtopic.php?t=1028368#0" class="postLink"><span class="post-b">Вспомогательные информационные ссылки</span></a><div class="sp-wrap"><div class="sp-body" title="Подробнее"><h3 class="sp-title">Подробнее</h3>Пример оформления ссылок:<div class="c-wrap"><div class="c-head"><b>Код:</b></div><div class="c-body">&#91;url=ссылка&#93;название&#91;/url&#93;</div></div><div class="c-wrap"><div class="c-head"><b>Код:</b></div><div class="c-body">&#91;url=http&#58;//www.getchu.com/soft.phtml?id=725092&amp;gc=gc&#93;Getchu&#91;/url&#93;</div></div><span class="post-hr">-</span>Содержание раздачи:<ol style="list-style: disc;">Для сборников игр требуется указать список содержимого. Используйте для этого спойлер:<div class="c-wrap"><div class="c-head"><b>Код:</b></div><div class="c-body">&#91;spoiler="Содержание&#58;"&#93;- Здесь укажите ваш список (в столбик&#41; -&#91;/spoiler&#93;</div></div></ol></div></div></ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="15"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Порядок установки</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Порядок установки</span></span><ol style="list-style: disc;"><span class="post-color-text" style="color: red;">Внимание!</span> Используйте этот пункт, если к игре действительно требуется подробное описание процесса установки или есть технические нюансы по запуску.<br />Простейшие действия (например, нажмите .exe) указывать не нужно!</ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="16"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Скриншоты/Примеры</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Скриншоты/Примеры</span></span><ol style="list-style: disc;"><span class="post-color-text" style="color: red;">Обязательный пункт!</span><br />Минимальное кол-во 3 шт. в виде "превью, увеличение по клику". Размер превью 350рх (выбрать на фотохостинге). См. настройки в спойлере "Настройки загрузки" ниже.<div class="sp-wrap"><div class="sp-body" title="Подробнее"><h3 class="sp-title">Подробнее</h3><ol style="list-style: disc;"><li>Не менее трех скриншотов (или примеров с магазина/оф. сайта) одного размера, оформленных в виде "Превью - увеличением по клику до оригинального размера"<br /><li>Размер скриншотов должен соответствовать разрешению поддерживаемому игрой<br /><li>Размер превью картинки - 350 px<br /><li>Располагать ссылки в линию - есть меню на фотохостинге / ссылки картинок через пробел или убрать пространство между ними<br /><li>Формат скриншотов - <span class="post-b">jpg</span><br /><li>Наличие тематики - эротика, порно, хентай.<br /><li>Перевод языка в игре: нужны отдельные примеры показывающие качество перевода и его наличие.<br /><li>Использовать рекомендуемые хостинги<br /><a href="http://pornolab.net/forum/viewtopic.php?t=980456" class="postLink"><span class="post-b">Как залить картинку на бесплатный хост</span></a></ol>Вы можете использовать готовые скриншоты и примеры с интернет магазинов.<br />Учтите при этом, что на них не должно быть сторонних логотипов сайтов. Исключение - лого разработчика.<br />Заливать эти примеры нужно так же в виде превью - по клику на один из разрешённых хостингов.</span><span class="post-hr">-</span>Пример ссылок:<div class="c-wrap"><div class="c-head"><b>Код:</b></div><div class="c-body">&#91;url=ссылка на полноразмерную картинку&#93;&#91;img&#93;ссылка на превью&#91;/img&#93;&#91;/url&#93;</div></div><div class="c-wrap"><div class="c-head"><b>Код:</b></div><div class="c-body">&#91;URL=http&#58;//fastpic.org/view/94/2017/0617/cade7aeda99ebffaaf4c5ccfaabd5872.jpg.html&#93;&#91;IMG&#93;http&#58;//i94.fastpic.org/thumb/2017/0617/72/cade7aeda99ebffaaf4c5ccfaabd5872.jpeg&#91;/IMG&#93;&#91;/URL&#93; </div></div><span class="post-hr">-</span>Как сделать скриншоты:<br />В windows нажать Win+shift+S и выбрать окно игры, сохранив его после "Сохранить как".</div></div><span class="post-br"><br /></span><span class="post-hr">-</span><a name="17"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Папка раздачи</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Папка раздачи</span></span><ol style="list-style: disc;"><span class="post-color-text" style="color: red;">Внимание!</span> Имя корневой папки в раздаче на английском языке (само название игры или перевод). Иероглифы в имени разрешены, но как дополнение (для коротких имён). Не вставляйте лишь японское имя или лишь номер с DLsite, например.</ol><span class="post-br"><br /></span><span class="post-hr">-</span><a name="18"></a><div class="post-align" style="text-align: right;" data-placement="right"><a class="postLink-name" href="#0"><span class="post-color-text" style="color: indigo;">#Торрент-файл</span>  <span style="font-size: 26px; line-height: normal;">&#128285;</span></a></div><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#9745;&#65039;Торрент-файл</span></span><ol style="list-style: disc;">Размер торрент-файла ограничен.<br />Если файл большой, то через модераторов можно закрепить до 2 MB торрент.<br />Пишите в ЛС за подробностями.</ol><span class="post-hr">-</span></div></div><span class="post-hr">-</span><span style="font-family: Arial;"><span style="font-size: 20px; line-height: normal;">&#128204;4. Создание торрент-файла, заливка его в тему и начало раздачи:</span></span><div class="sp-wrap"><div class="sp-body" title="Подробнее"><h3 class="sp-title">Подробнее</h3><div class="post-align" style="text-align: center;" data-placement="center"><span class="post-b"><span class="post-color-text" style="color: darkred;"><span style="font-size: 24px; line-height: normal;">Как создать и оформить раздачу</span></span></span><br />Эта статья поможет Вам правильно оформить тему для раздачи собственного файла.</div><span class="post-hr">-</span><div class="post-align" style="text-align: center;" data-placement="center">Первое, что нужно сделать перед созданием раздачи - это проверить не является ли она повтором. Помните, что все повторы удаляются с нашего трекера, поэтому, что бы не делать напрасный труд - воспользуйтесь <span class="post-b"><a href="http://pornolab.net/forum/search.php" class="postLink">поиском</a></span> по трекеру и убедитесь в уникальности своей раздачи.<br /><span class="post-b"><span style="font-size: 24px; line-height: normal;"><a href="http://pornolab.net/forum/viewtopic.php?t=101236" class="postLink">Как пользоваться Поиском (инструкция)</a></span></span><span class="post-br"><br /></span><span class="post-b">Внимание:</span> создавайте свои раздачи только в предназначенных для этого разделах сайта.</div><span class="post-hr">-</span>При создании новой раздачи, вы пройдете через 3 этапа:<br /><span class="post-i">1. Создание торрента<br />2. Оформление раздачи на трекере<br />3. Постановка раздачи в клиенте</span><span class="post-br"><br /></span>Рассмотрим каждый из них подробнее:<span class="post-hr">-</span><a name="sozdan"></a><span class="post-color-text" style="color: brown;"><span style="font-size: 22px; line-height: normal;"><span class="post-b">1. Создание торрента</span></span></span><br />В зависимости от того, с какой программой вы работаете этот этап будет проделываться разными путями:<div class="sp-wrap"><div class="sp-body" title="Создание торрента при помощи uTorrent"><h3 class="sp-title">Создание торрента при помощи uTorrent</h3>1. Для создания торрента открываете клиент и в меню <span class="post-b">Файл</span> выбираете опцию <span class="post-b">Создать новый торрент...</span><span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior01.png">&#10;</var><span class="post-br"><br /></span>2. Появится диалоговое окно "Создать новый торрент". Если вы раздаете один файл, то нажимайте на кнопку <span class="post-b">Выбрать файл</span>, если папку - <span class="post-b">Выбрать папку</span>. В появившемся окне выбираем то что будем раздавать, а затем нажимаем кнопку <span class="post-b">Создать и сохранить в...</span><span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior02.png">&#10;</var><span class="post-br"><br /></span>3. В появившемся окне нажимаете <span class="post-b">Да</span><span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior03.png">&#10;</var><span class="post-br"><br /></span>4. Ждете пока клиент сделает торрент и сохраняете (кнопка <span class="post-b">Сохранить</span>) его у себя на диске.<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior04.png">&#10;</var></div></div><div class="sp-wrap"><div class="sp-body" title="Создание торрента при помощи Vuze (бывш. Azureus&amp;#41;"><h3 class="sp-title">Создание торрента при помощи Vuze (бывш. Azureus&amp;#41;</h3>1. Для создания торрента открываете клиент и в меню <span class="post-b">Файл</span> выбираете опцию <span class="post-b">Создать торрент</span><span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior05.png">&#10;</var><span class="post-br"><br /></span>2. Дальше появляется окно "Создать торрент", где вам надо выбрать <span class="post-b">URL раздачи</span> и определится будете вы раздавать <span class="post-b">папку</span> или <span class="post-b">один файл</span><br />В <span class="post-b">URL раздачи:</span> надо вписать <span class="post-b"><a href="http://bt.pornolab.net" class="postLink">http://bt.pornolab.net</a></span>. Определившись будете вы раздавать один файл или папку, можно воспользоваться советом и перетащить содержимое в окно:<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior06.png">&#10;</var><span class="post-br"><br /></span>3. Если вы воспользовались советом из пункта (2), то вы сразу нажимаете на <span class="post-b">Далее</span>.<br />Если нет, то вам надо выбрать файл/папку которую вы будете раздавать (кнопка <span class="post-b">Обзор</span>), а потом нажать <span class="post-b">Далее</span><span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior07.png">&#10;</var><span class="post-br"><br /></span>4. Теперь осталось только выбрать <span class="post-b">Размер Кусочков</span> и дать имя создаваемому файлу.<br />Имя и место сохранения торрент-файла можно задать при помощи кнопки <span class="post-b">Обзор</span>. Нажимаете кнопку <span class="post-b">Готово</span>.<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior08.png">&#10;</var><span class="post-br"><br /></span>5. Когда торрент будет создан, то в появившемся окне появится надпись <span class="post-i">Файл сохранён</span>. Нажмите кнопку <span class="post-b">Закрыть</span>.<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior09.png">&#10;</var></div></div><div class="sp-wrap"><div class="sp-body" title="Создание торрента при помощи BitComet 0.89"><h3 class="sp-title">Создание торрента при помощи BitComet 0.89</h3>1. Для создания торрента открываете клиент и в меню <span class="post-b">Файл</span> выбираете опцию <span class="post-b">Создать торрент...</span><span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior11.png">&#10;</var><span class="post-br"><br /></span>2. Появится диалоговое окно "Создатель торрентов". Если вы раздаете один файл, то выбираете пункт <span class="post-b">Одиночный файл</span>, если папку - <span class="post-b">Папка (несколько файлов)</span>. При помощи <span class="post-i">верхней</span> кнопки <span class="post-b">Выбрать</span> указываете путь к файлу или папке, при помощи <span class="post-i">нижней</span> - задаёте имя и место сохранения создаваемого торрент-файла. Нажимаете кнопку <span class="post-b">ОК</span>.<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior12.png">&#10;</var><span class="post-br"><br /></span>3. Когда торрент создался, удаляете задание. ВНИМАНИЕ: удаляем ТОЛЬКО ЗАДАНИЕ! Для этого правой кнопкой "мыши" нажимаете на задание, в появившемся меню выбираете <span class="post-b">Удалить-&gt; Удалить только задание</span>, в появившемся окне нажимаете <span class="post-b">ОК</span>.<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior13.png">&#10;</var> <var class="postImg" title="http://static.pornolab.net/pic/ksior14.png">&#10;</var></div></div><span class="post-hr">-</span><a name="oform"></a><span class="post-color-text" style="color: brown;"><span style="font-size: 22px; line-height: normal;"><span class="post-b">2. Оформление раздачи на трекере</span></span></span><br />При создании раздачи в разделах нашего трекера руководствуйтесь   <img class="smile" src="//static.pornolab.net/smiles/icon_arrow.gif?v=1" alt=":arrow:" align="absmiddle" border="0" /> <a href="http://pornolab.net/forum/viewtopic.php?t=982810" class="postLink">правилами оформления раздач на трекере PornoLab.net</a>, которые дополняются правилами разделов. Пост, содержащий правила оформления раздач в каждом из разделов является одним из первых в списке:<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior15.png">&#10;</var><span class="post-br"><br /></span>Оформленные не по правилам раздачи будут закрываться, а затем удаляться с трекера. За систематическое игнорирование правил, действующих на нашем ресурсе пользователю будет вынесено предупреждение. 3 предупреждения приравниваются к бану.<span class="post-br"><br /></span>Итак, прочитав правила, приступаем к созданию новой темы:<br />Зайдя в каждый раздел, доступный для создания раздач, можно увидеть кнопку <var class="postImg" title="http://static.pornolab.net/templates/default/images/post.gif">&#10;</var> Нажатие на неё вызывает переход к шаблону оформления темы, в поля которого следует ввести данные о релизе. После заполнения шаблона следует нажать кнопку <span class="post-b">Продолжить</span>.<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior16.png">&#10;</var><span class="post-br"><br /></span>Теперь можно видеть как будет смотреться наш пост (верхняя часть страницы):<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior17.png">&#10;</var><span class="post-br"><br /></span>Изменить его (метка <span class="post-b">1.</span> - заголовок топика, метка <span class="post-b">2.</span> - содержание поста) и прикрепить торрент-файл (нижняя часть страницы). Чтобы прикрепить торрент следует нажать на надпись Загрузить файл (метка <span class="post-b">3.</span>)<br /><var class="postImg" title="http://static.pornolab.net/pic/ksior18.png">&#10;</var><span class="post-br"><br /></span>На месте надписи появится кнопка <span class="post-b">Выберите файл</span> (<span class="post-b">Обзор</span> в некоторых браузерах):<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior19.png">&#10;</var><span class="post-br"><br /></span>Нажатие на эту кнопку вызовет новое окошко, где надо найти и выбрать торрент-файл, после чего нажать кнопку <span class="post-b">Открыть</span>:<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior20.png">&#10;</var><span class="post-br"><br /></span>Теперь следует нажать кнопку <span class="post-b">Отправить</span>:<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior21.png">&#10;</var><span class="post-br"><br /></span>Тема создана. Теперь нужно скачать торрент-файл и встать на раздачу.<span class="post-hr">-</span><span class="post-color-text" style="color: brown;"><span style="font-size: 22px; line-height: normal;"><span class="post-b">3. Постановка раздачи в клиенте</span></span></span><br />(на примере uTorrent 1.8.1)<br />В появившемся окне следует нажать на надпись <span class="post-b">Нужно его скачать</span>:<br /><var class="postImg" title="http://static.pornolab.net/pic/ksior22.png">&#10;</var><br /><span class="post-color-text" style="color: red;"><span class="post-b">ВНИМАНИЕ!</span></span> <span class="post-b">торрент-файл скачивается браузером (таким как Internet Explorer, Opera, Mozila firefox и др.), а не менеджерами закачки (такими как FlashGet, ReGet, Download Master и пр.).</span><br />Теперь запускаем клиент и добавляем, скачанный ранее, торрент-файл, нажав на пиктограмму <span class="post-i">Добавить торрент</span>, или через пункт меню <span class="post-b">Файл</span> <span class="post-b">Открыть торрент</span>.<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior23.png">&#10;</var> <var class="postImg" title="http://static.pornolab.net/pic/ksior24.png">&#10;</var><span class="post-br"><br /></span>В появившемся окне нужно осуществить поиск и выбор скачанного торрент-файла, после чего нажать кнопку <span class="post-b">Открыть</span>.<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior25.png">&#10;</var><span class="post-br"><br /></span>В диалоговом окне сохранения закачиваемого файла требуется указать путь к раздаваемому файлу (кнопка <span class="post-b">...</span>) и нажать кнопку <span class="post-b">ОК</span>:<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior26.png">&#10;</var><span class="post-br"><br /></span>Запускается процесс проверки файла:<span class="post-br"><br /></span><var class="postImg" title="http://static.pornolab.net/pic/ksior27.png">&#10;</var><span class="post-br"><br /></span>После завершения проверки файла вы становитесь сидом своей раздачи.<span class="post-br"><br /></span>Автор - <a href="http://pornolab.net/forum/profile.php?mode=viewprofile&amp;u=163604" class="postLink"><span class="post-b">Grezz</span></a><br />Отредактировал - <a href="http://pornolab.net/forum/profile.php?mode=viewprofile&amp;u=856321" class="postLink"><span class="post-b">поросячий визг</span></a><br />Отредактировал - <a href="http://pornolab.net/forum/profile.php?mode=viewprofile&amp;u=4102744" class="postLink"><span class="post-b">Leobret</span></a></div></div><span class="post-hr">-</span><span style="font-size: 11px; line-height: normal;"><span style="font-family: Arial;">При проблемах со скоростью отдачи настоятельно рекомендуется посетить следующие разделы:<br /><a href="http://pornolab.net/forum/viewforum.php?f=1743" class="postLink"><span class="post-b"><span class="post-color-text" style="color: darkblue;">Вопросы по форуму и трекеру</span></span></a> - здесь можно спросить<br /><a href="http://pornolab.net/forum/viewforum.php?f=566" class="postLink"><span class="post-b"><span class="post-color-text" style="color: darkblue;">Правила, основные инструкции, FAQ</span></span></a>-и - здесь узнать ответ</span></span><span class="post-br"><br /></span><span class="post-hr">-</span><span class="post-color-text" style="color: red;"><span class="post-b"><span style="font-size: 20px; line-height: normal;">(*)</span></span> - </span><span style="font-size: 16px; line-height: normal;"><span style="font-family: Arial;"><span class="post-color-text" style="color: red;"><span class="post-b">Знак обязательного пункта.</span></span> Обращайте внимание на него. См. ниже.</span></span></div>
	<div class="clear"></div>
	</td>
</tr>
</table>
<div class="spacer_12"></div>


<div style="display: none;">
<form id="tpl-post-form" method="post" action="posting.php?mode=newtopic&amp;f=1756" name="post" class="tokenized">
	<input type="hidden" name="tor_required" value="1">
	<input type="hidden" name="preview" value="1">
	<input id="tpl-post-subject" type="text" name="subject" size="90" value="" />
	<textarea id="tpl-post-message" name="message" rows="1" cols="1"></textarea>
</form>
</div>

<div id="rel-form" style="display: none;">
<table class="forumline">
<col class="row1" width="20%">
<col class="row2" width="80%">
<thead>
<tr>
	<th colspan="2">Заполните форму для релиза</th>
</tr>
</thead>
<tbody id="rel-tpl">
</tbody>
<tfoot>
<tr>
	<td colspan="2" class="pad_8 tCenter bold">На следующей странице проверьте оформление и загрузите torrent файл</td>
</tr>
<tr>
	<td class="catBottom" colspan="2">
				<input type="button" value="Продолжить" class="bold" style="width: 150px;" onclick="tpl_submit(true);" />
			</td>
</tr>
</tfoot>
</table>
</div>


<div style="display: none;">
	<!-- TPL.el_id элементы, для E[el] в форму подставляется $(el).html() -->
	
<div id="tpl-abr-box"></div>
<script type="text/javascript">
$(document).ready(function(){
	$.each(TPL.el_id, function(el,desc){
		var m = el.match(/^(.*)(_abr)$/);
		if (m == null) {
			return true; // continue
		}
		var el_abr = m[0];
		var el_ref = m[1];
		$('#tpl-abr-box').append('<div id="'+el_abr+'-hid">'+ TPL.build_select_el(el_abr) +'</div>');
		TPL.submit_fn[el_abr] = function(){
			if ( $('#'+el_ref).length ) {
				$('#'+el_abr)[0].selectedIndex = $('#'+el_ref)[0].selectedIndex;
			}
		}
	});
});
</script>

<!--Ниже описаны кнопки-->

<!--load_pic_btn-->
<div id="load_pic_btn"><input type="button" title="Загрузить картинку на фотохостинг" value="Загрузить картинку" onclick="window.open('https://new.fastpic.org/', '_blank'); return false;" /></div>
<!--/load_pic_btn-->

<!--lang_faq_btn-->
<div id="lang_faq_btn"><input type="button" title="Вопросы о субтитрах и озвучке" value="FAQ по озвучке и субтитрам" onclick="window.open('http://pornolab.net/forum/viewtopic.php?t=1033780', '_blank'); return false;" /></div>
<!--/lang_faq_btn-->

<!--genre_btn-->
<div id="genre_btn"><input type="button" title="Справочник по порнотерминологии" value="Справочник по жанрам" onclick="window.open('http://pornolab.net/forum/viewtopic.php?t=985049', '_blank'); return false;" /></div>
<!--/genre_btn-->

<!--tech_btn-->
<div id="tech_btn"><input type="button" title="Как получить информацию о видео файле?" value="FAQ по тех. данным" onclick="window.open('http://pornolab.net/forum/viewtopic.php?t=984608', '_blank'); return false;" /></div>
<!--/tech_btn-->

<!--tech_quality_btn-->
<div id="tech_quality_btn"><input type="button" title="Расшифровки аббревиатур качества видео" value="Качество видео" onclick="window.open('https://pornolab.net/forum/viewtopic.php?t=1833345', '_blank'); return false;" /></div>
<!--/tech_quality_btn-->

<!--g_genre_btn-->
<div id="g_genre_btn"><input type="button" title="Таблица тегов" value="Таблица тегов" onclick="window.open('https://pornolab.net/forum/viewtopic.php?t=2914348', '_blank'); return false;" /></div>
<!--/g_genre_btn-->

<!--h_genre_btn-->
<div id="h_genre_btn"><input type="button" title="Таблица тегов" value="Таблица тегов" onclick="window.open('http://pornolab.net/forum/viewtopic.php?t=1205872', '_blank'); return false;" /></div>
<!--/h_genre_btn-->

<!--g_tag_editor_btn-->
<div id="g_tag_editor_btn"><input type="button" title="Редактор тегов" value="Редактор - Теги и Иконки" onclick="window.open('https://static.pornolab.net/tag_editor/?conf=game', '_blank'); return false;" /></div>
<!--/g_tag_editor_btn-->

<!--h_tag_editor_btn-->
<div id="h_tag_editor_btn"><input type="button" title="Редактор тегов" value="Редактор - Теги и Иконки" onclick="window.open('https://static.pornolab.net/tag_editor/?conf=hentai', '_blank'); return false;" /></div>
<!--/h_tag_editor_btn-->

<!--h_icon_genre_btn-->
<div id="h_icon_genre_btn"><input type="button" title="Иконки" value="Иконки" onclick="window.open('https://pornolab.net/forum/viewtopic.php?t=3011437', '_blank'); return false;" /></div>
<!--/h_icon_genre_btn-->

<!--g_icon_genre_btn-->
<div id="g_icon_genre_btn"><input type="button" title="Иконки" value="Иконки" onclick="window.open('https://pornolab.net/forum/viewtopic.php?t=2504323', '_blank'); return false;" /></div>
<!--/g_icon_genre_btn-->

<!--np_genre_btn-->
<div id="np_genre_btn"><input type="button" title="Жанры нетрадиционного порно" value="Жанры нетрадиционного порно" onclick="window.open('http://pornolab.net/forum/viewtopic.php?t=1378857', '_blank'); return false;" /></div>
<!--/np_genre_btn-->

<!--mk_scrn_btn-->
<div id="mk_scrn_btn"><input type="button" title="Как изготовить скриншоты" value="Как изготовить скриншоты" onclick="window.open('http://pornolab.net/forum/viewtopic.php?t=980749', '_blank'); return false;" /></div>
<!--/mk_scrn_btn-->

<!--ad_scrn_btn-->
<div id="ad_scrn_btn"><input type="button" title="Как разместить скриншоты" value="Как разместить скриншоты" onclick="window.open('http://pornolab.net/forum/viewtopic.php?t=980456', '_blank'); return false;" /></div>
<!--/ad_scrn_btn-->

<!--auto_pack_btn-->
<div id="auto_pack_btn"><input type="button" title="Автоматизация составления списков видеофайлов" value="Авто-составление списка видеофайлов" onclick="window.open('http://pornolab.net/forum/viewtopic.php?t=1063076', '_blank'); return false;" /></div>
<!--/auto_pack_btn-->

<!--Ниже описаны ссылки-->

<!--translit_url-->
<div id="translit_url"> <a href="http://romaji.org/" target="_blank"><b>Romaji.org</b></a> </div>
<!--/translit_url-->

<!--translit2_url-->
<div id="translit2_url"> <a href="http://nihongo.j-talk.com/" target="_blank"><b>Kanji Converter</b></a> </div>
<!--/translit_url-->

<!--uploadpic_url-->
<div id="uploadpic_url"> <a href="http://pornolab.net/forum/viewtopic.php?t=980456" target="_blank"><b>Как залить картинку на бесплатный хост</b></a> </div>
<!--/uploadpic_url--></div>
<div style="display: none;">
	<!-- исходные значения всех #tpl-src -->
	<textarea id="tpl-src-form-val" rows="10" cols="10">&lt;-poster                  `(*)` `URL` E[uploadpic_url] `BR` INP[poster]`BR` E[load_pic_btn] -&gt;
&lt;-{Оригинальное название (иероглифы)} `(*) для азиатских игр` `BR` INP[{Оригинальное название (иероглифы)},90,80] -&gt;
&lt;-{Оригинальное название}   `(*) на английском` `BR` INP[title_eng,90,80] -&gt;
&lt;-{Название на русском}      INP[{Название на русском},90,80] -&gt;
&lt;-g_year                  INP[g_year]`(*)`-&gt;
&lt;-g_data  INP[g_data] `формат даты: &quot;гггг/мм/дд&quot;` -&gt;
&lt;-{Теги}                   `Теги: (*) выбрать и скопировать из редактора` TXT[genre_g] `BR` E[g_tag_editor_btn] `BR` `Иконки: ссылки скопировать из редактора` TXT[icons_g] `BR` `(!) После завершения создания темы, символы &quot;[b][/b]:&quot; перед иконками можно удалить вручную` -&gt;
&lt;-cens_game               SEL[cens_game]`(*)` -&gt;
&lt;-{Разработчик/Издатель}  `(*) на английском | для азиатских игр - имя иероглифами / на английском` `BR` INP[{Разработчик/Издатель},200,50]-&gt;
&lt;-platform                SEL[platform]`(*)`-&gt; 
&lt;-release_type `(*) Если демка или игра в процессе разработки, то обязательно выбрать соответствующий пункт` `BR` SEL[release_type] -&gt;
&lt;-tablet SEL[tablet] -&gt;м
&lt;-{Версия}                INP[{Версия},200,10]-&gt;
&lt;-{Язык игры}             `Сюжет` SEL[lang_g] `| Интерфейс` SEL[menulang_g] `| Озвучка` SEL[language_g]`(*)` -&gt;
&lt;-{Системные требования}  `(*)` `OS (система) | CPU (процессор) | GPU (графическая карта) | RAM (оперативная память) | VRAM (видео память) | HDD (объём на диске)` TXT[{Системные требования}] `BR` `Пример: OS: Windows7 | CPU: Pentium4 2.0GHz | RAM: 512MB | VRAM: 128MB | HDD: 3GB` -&gt;
&lt;-description             `(*)``Описание игры на русском или английском языке. Дополнительное описание скрыть спойлером.` TXT[description]-&gt;
&lt;-moreinfo                `Особенности игры, отличия от аналогичных раздач, ссылки на базы данных и официальные странички и т.п..` TXT[moreinfo] -&gt;
&lt;-stepinstall     `Внимание! Порядок установки - только для сложного, поэтапного процесса или технических нюансов при запуске игры. Не для банальщины.` TXT[stepinstall] -&gt;
&lt;-screenshots_g           `(*)` `URLs` E[load_pic_btn] `Мин.кол-во 3 шт. в виде превью. Размер превью 350рх (выбрать на фотохостинге).` `BR` E[uploadpic_url] TXT[screenshots_g]-&gt;

&lt;script&gt;


// ==UserScript==
// @name         Pornolab title length counter (Universal)
// @namespace    https://pornolab.net/
// @version      1.2.0
// @description  Live counter for forum topic title length (New releases, Edit posts &amp; inline post editor)
// @match        https://pornolab.net/forum/posting.php?mode=new_rel*
// @match        https://www.pornolab.net/forum/posting.php?mode=new_rel*
// @match        https://pornolab.net/forum/posting.php?mode=editpost*
// @match        https://www.pornolab.net/forum/posting.php?mode=editpost*
// @match        https://pornolab.net/forum/posting.php?mode=newtopic&amp;f*
// @match        https://www.pornolab.net/forum/posting.php?mode=newtopic&amp;f*
// @match        https://pornolab.net/forum/posting.php*
// @match        https://www.pornolab.net/forum/posting.php*
// @match        https://pornolab.net/forum/viewtopic.php*
// @match        https://www.pornolab.net/forum/viewtopic.php*
// @grant        none
// @run-at       document-end
// @license      MIT
// @downloadURL https://update.greasyfork.org/scripts/578631/Pornolab%20title%20length%20counter%20%28Universal%29.user.js
// @updateURL https://update.greasyfork.org/scripts/578631/Pornolab%20title%20length%20counter%20%28Universal%29.meta.js
// ==/UserScript==
/* global $, jQuery */
(function () {
    &#039;use strict&#039;;

    // Максимально допустимая длина заголовка.
    const MAX_LEN = 500;

    // ID элементов интерфейса виджета
    const BOX_ID = &#039;tm-title-len-box&#039;;
    const OUT_ID = &#039;tm-title-len-out&#039;;
    const PREVIEW_ID = &#039;tm-title-len-preview&#039;;
    const TOGGLE_ID = &#039;tm-title-len-toggle&#039;;
    const TMP_ID = &#039;tm-title-len-tmp&#039;;

    // Считает длину строки с учетом особенностей кодировки cp1251 на форуме
    function cpLen(str) {
        if (!str) return 0;
        let length = 0;

        const extraCp1251 = new Set([
            0x0402, 0x0403, 0x201A, 0x0453, 0x201E, 0x2026, 0x2020, 0x2021,
            0x20AC, 0x2030, 0x0409, 0x2039, 0x040A, 0x040C, 0x040B, 0x040F,
            0x0452, 0x2018, 0x2019, 0x201C, 0x201D, 0x2022, 0x2013, 0x2014,
            0x2122, 0x0459, 0x203A, 0x045A, 0x045C, 0x045B, 0x045F, 0x00A0,
            0x040E, 0x045E, 0x0408, 0x00A4, 0x0490, 0x00A6, 0x00A7, 0x0401,
            0x00A9, 0x0404, 0x00AB, 0x00AC, 0x00AD, 0x00AE, 0x0407, 0x00B0,
            0x00B1, 0x0406, 0x0456, 0x0491, 0x00B5, 0x00B6, 0x00B7, 0x0451,
            0x2116, 0x0454, 0x00BB, 0x0458, 0x0405, 0x0455, 0x0457
        ]);

        function isCp1251(codePoint) {
            if (codePoint &lt;= 127) return true;
            if (codePoint &gt;= 0x0410 &amp;&amp; codePoint &lt;= 0x044F) return true;
            return extraCp1251.has(codePoint);
        }

        for (const char of str) {
            const codePoint = char.codePointAt(0);

            switch (char) {
                case &quot;&amp;&quot;:
                    length += 5;
                    break;
                case &quot;&lt;&quot;:
                case &quot;&gt;&quot;:
                    length += 4;
                    break;
                case &quot;\&quot;&quot;:
                case &quot;&#039;&quot;:
                    length += 6;
                    break;
                default:
                    if (isCp1251(codePoint)) {
                        length += 1;
                    } else {
                        length += 3 + codePoint.toString().length;
                    }
            }
        }

        return length;
    }

    // Возвращает стандартное поле ввода темы (для страниц редактирования постов)
    function getSubjectField() {
        return document.querySelector(&#039;input[name=&quot;subject&quot;], #tpl-post-subject&#039;);
    }

    // Проверяет, есть ли вообще на странице поддерживаемые формы заголовков
    function hasTitleElements() {
        return !!(document.getElementById(&#039;custom-tpl&#039;) || document.getElementById(&#039;tpl-src-title&#039;) || getSubjectField());
    }

    // Создает скрытое поле для интеграции со штатным генератором TPL сайта
    function ensureTempInput() {
        let el = document.getElementById(TMP_ID);
        if (el) return el;

        el = document.createElement(&#039;input&#039;);
        el.type = &#039;text&#039;;
        el.id = TMP_ID;
        el.style.cssText = &#039;position:absolute;left:-9999px;top:-9999px;width:1px;height:1px;opacity:0;&#039;;
        document.body.appendChild(el);
        return el;
    }

    // Синхронизация дублирующих селектов сайта (*_abr)
    function syncAbrSelects() {
        document.querySelectorAll(&#039;select[id$=&quot;_abr&quot;]&#039;).forEach((abr) =&gt; {
            const baseId = abr.id.slice(0, -4);
            const base = document.getElementById(baseId);
            if (!base || base.tagName !== &#039;SELECT&#039;) return;

            let idx = base.selectedIndex;
            if (idx &lt; 0) idx = 0;
            if (idx &gt;= abr.options.length) idx = abr.options.length - 1;

            abr.selectedIndex = idx;
            abr.value = abr.options[idx] ? abr.options[idx].value : &#039;&#039;;
        });
    }

    // Сборка заголовка через внутренний генератор шаблонов сайта
    function buildStandardTitle() {
        try {
            const titleBox = document.getElementById(&#039;tpl-src-title&#039;);
            const template = titleBox ? (titleBox.value || &#039;&#039;).trim() : &#039;&#039;;
            if (!template || template === &#039;...&#039;) return &#039;&#039;;

            if (window.TPL &amp;&amp; typeof window.TPL.build_title === &#039;function&#039;) {
                syncAbrSelects();
                ensureTempInput();
                window.TPL.build_title(TMP_ID);

                const tmp = document.getElementById(TMP_ID);
                return tmp ? (tmp.value || &#039;&#039;) : &#039;&#039;;
            }
        } catch (e) { }
        return &#039;&#039;;
    }

    // Извлечение шаблона из структуры данных кастомной формы
    function extractCustomSubjectTemplate() {
        const srcEl = document.getElementById(&#039;tpl-src-form-val&#039;);
        const src = srcEl ? (srcEl.value || &#039;&#039;) : &#039;&#039;;
        if (!src) return &#039;&#039;;

        const m = src.match(/subject\s*:\s*([&quot;&#039;`])([\s\S]*?)\1\s*,/);
        return m ? m[2] : &#039;&#039;;
    }

    // Рендеринг кастомного шаблона по маске %поле%
    function renderCustom(template, data) {
        return template.replace(/%([^%]+)%(\n?)/g, (m0, m1, m2) =&gt; {
            const parts = m1.split(&#039;:&#039;);
            let prefix, id, index, suffix;

            if (parts.length === 3) {
                [prefix, id, suffix] = parts;
                index = null;
            } else {
                [prefix, id, index, suffix] = parts;
            }

            let named = false;
            if (id &amp;&amp; id[0] === &#039;+&#039;) {
                named = true;
                id = id.slice(1);
            }

            const entry = data[id];
            if (!entry) return m0;

            let value = entry.value;
            if (index !== null) {
                value = String(value || &#039;&#039;).split(&#039;;&#039;)[index];
            }

            if (value) {
                value = prefix + value + String(suffix || &#039;&#039;).replace(/\[br\]/g, &#039;\n&#039;);
                if (named) value = &#039;[b]&#039; + entry.title + &#039;[/b]: &#039; + value;
            } else {
                value = &#039;&#039;;
            }

            return value ? value + m2 : value;
        });
    }

    // Сборка заголовка из заполненных полей кастомного конструктора раздач
    function buildCustomTitle() {
        try {
            const custom = document.getElementById(&#039;custom-tpl&#039;);
            if (!custom) return &#039;&#039;;

            const template = extractCustomSubjectTemplate();
            if (!template) return &#039;&#039;;

            const data = {};
            custom.querySelectorAll(&#039;tr&#039;).forEach((tr) =&gt; {
                const item = $(tr).data(&#039;item&#039;);
                if (!item || !item.name) return;

                let value = &#039;&#039;;
                try {
                    if (typeof item.getter === &#039;function&#039;) {
                        value = item.getter($(tr));
                    } else if (item.options) {
                        const checked = tr.querySelectorAll(&#039;input:checked&#039;);
                        if (checked.length) {
                            value = Array.from(checked).map((el) =&gt; el.value).join(&#039;, &#039;);
                        } else {
                            const select = tr.querySelector(&#039;select&#039;);
                            if (select) value = select.value || &#039;&#039;;
                        }
                    } else {
                        const field = tr.querySelector(&#039;input,textarea&#039;);
                        if (field) value = (field.value || &#039;&#039;).trim();
                    }
                } catch (e) {
                    value = &#039;&#039;;
                }

                data[item.name] = {
                    title: item.title || item.name,
                    value: value || &#039;&#039;
                };
            });

            return renderCustom(template, data);
        } catch (e) {
            return &#039;&#039;;
        }
    }

    // Универсальный метод получения текущего заголовка
    function buildCurrentTitle() {
        if (document.getElementById(&#039;custom-tpl&#039;)) {
            return buildCustomTitle();
        }

        const standard = buildStandardTitle();
        if (standard) return standard;

        const subject = getSubjectField();
        return subject ? (subject.value || &#039;&#039;) : &#039;&#039;;
    }

    // Гарантирует наличие UI панели на экране
    function ensureBox() {
        let box = document.getElementById(BOX_ID);
        if (box) return box;

        box = document.createElement(&#039;div&#039;);
        box.id = BOX_ID;
        box.style.cssText = [
            &#039;position:fixed&#039;,
            &#039;top:40px&#039;,
            &#039;right:12px&#039;,
            &#039;z-index:2147483647&#039;,
            &#039;min-width:260px&#039;,
            &#039;max-width:360px&#039;,
            &#039;padding:10px 12px&#039;,
            &#039;border:1px solid #cfd6df&#039;,
            &#039;border-radius:10px&#039;,
            &#039;background:rgba(246,247,249,0.85)&#039;,
            &#039;color:#223&#039;,
            &#039;font:13px/1.4 Arial, sans-serif&#039;,
            &#039;box-shadow:0 6px 20px rgba(0,0,0,.12)&#039;
        ].join(&#039;;&#039;);

        box.innerHTML = `
      &lt;div style=&quot;font-weight:700;margin-bottom:6px;&quot;&gt;Лимит заголовка&lt;/div&gt;
      &lt;div id=&quot;${OUT_ID}&quot; style=&quot;margin-bottom:8px;&quot;&gt;Загрузка…&lt;/div&gt;
      &lt;button id=&quot;${TOGGLE_ID}&quot; type=&quot;button&quot; style=&quot;
        display:inline-block;
        padding:3px 8px;
        border:1px solid #b9c2cc;
        border-radius:8px;
        background:#fff;
        cursor:pointer;
        font:inherit;
        margin-bottom:8px;
      &quot;&gt;Показать заголовок&lt;/button&gt;
      &lt;div id=&quot;${PREVIEW_ID}&quot; style=&quot;
        display:none;
        padding:8px 10px;
        border:1px solid #dde3ea;
        border-radius:8px;
        background:#fff;
        white-space:pre-wrap;
        word-break:break-word;
      &quot;&gt;&lt;/div&gt;
    `;

        document.body.appendChild(box);

        const btn = document.getElementById(TOGGLE_ID);
        const preview = document.getElementById(PREVIEW_ID);

        if (btn &amp;&amp; preview) {
            btn.addEventListener(&#039;click&#039;, () =&gt; {
                const visible = preview.style.display !== &#039;none&#039;;
                preview.style.display = visible ? &#039;none&#039; : &#039;block&#039;;
                btn.textContent = visible ? &#039;Показать заголовок&#039; : &#039;Скрыть заголовок&#039;;
                if (!visible) updateCounter();
            });
        }

        return box;
    }

    // Обновление состояния и текста счетчика.
    // На viewtopic.php: показывает панель когда форма открыта, скрывает когда закрыта.
    function updateCounter() {
        if (!hasTitleElements()) {
            const box = document.getElementById(BOX_ID);
            if (box) box.remove();
            return;
        }

        ensureBox();

        const out = document.getElementById(OUT_ID);
        const preview = document.getElementById(PREVIEW_ID);
        if (!out) return;

        const title = buildCurrentTitle();
        const len = cpLen(title);
        const diff = MAX_LEN - len;

        if (diff &gt;= 0) {
            out.textContent = `Заголовок: ${len}/${MAX_LEN}. Осталось ${diff} символов.`;
            out.style.color = &#039;#223&#039;;
        } else {
            out.textContent = `Заголовок: ${len}/${MAX_LEN}. Нужно удалить ${Math.abs(diff)} символов.`;
            out.style.color = &#039;#b00020&#039;;
        }

        if (preview &amp;&amp; preview.style.display !== &#039;none&#039;) {
            const newText = title || &#039;Заголовок пустой&#039;;
            if (preview.textContent !== newText) {
                preview.textContent = newText;
            }
        }
    }

    let scheduled = false;
    function scheduleUpdate() {
        if (scheduled) return;
        scheduled = true;
        setTimeout(() =&gt; {
            scheduled = false;
            updateCounter();
        }, 30);
    }

    // Подписка на события и запуск периодического polling.
    // polling нужен для viewtopic.php: форма появляется/исчезает динамически,
    // поэтому мы не можем полагаться только на события ввода.
    function attachListeners() {
        document.addEventListener(&#039;input&#039;, scheduleUpdate, true);
        document.addEventListener(&#039;change&#039;, scheduleUpdate, true);
        document.addEventListener(&#039;keyup&#039;, scheduleUpdate, true);
        document.addEventListener(&#039;click&#039;, scheduleUpdate, true);
        document.addEventListener(&#039;mouseup&#039;, scheduleUpdate, true);
        setInterval(updateCounter, 300);
    }

    // Инициализация: всегда запускаем listeners и polling.
    // На posting.php — поле есть сразу, бокс появится немедленно.
    // На viewtopic.php — поля нет, бокс появится как только скрипт
    // первого юзерскрипта (Post Editor) откроет форму редактирования.
    function init() {
        attachListeners();
        updateCounter();
    }

    if (document.body) {
        init();
    } else {
        document.addEventListener(&#039;DOMContentLoaded&#039;, init);
    }
})();

&lt;/script&gt;</textarea>
	<textarea id="tpl-src-title-val" rows="10" cols="10">&lt;-{Оригинальное название (иероглифы)} [title_eng]-&gt;/ &lt;-release_type_abr {Версия}-&gt;[,] &lt;-{Разработчик/Издатель}-&gt;(,) &lt;-cens_game_abr-&gt;[,] &lt;-g_year genre_g-&gt;[,] &lt;-lang_g_abr-&gt;[,]</textarea>
	<textarea id="tpl-src-msg-val" rows="10" cols="10">poster[img,POSTER]
{Оригинальное название (иероглифы)}[HEAD]
title_eng[HEAD,req]
{Название на русском}[HEAD]
g_year[req]
g_data[]
genre_g[]
icons_g[]
cens_game[req]
{Разработчик/Издатель}[req]
platform[req]
release_type[]
tablet[]
{Версия}[]
lang_g[req]
menulang_g[req]
language_g[]
{Системные требования}[br2,BR,req]
description[BR,req]
moreinfo[BR]
stepinstall[spoiler]
screenshots_g[spoiler,req]</textarea>
	<textarea id="tpl-src-sel-val" rows="10" cols="10"></textarea>
</div>

<noscript><div class="warningBox2 bold tCenter">Для показа необходимo включить JavaScript</div></noscript>


	</div><!--/main_content_wrap-->
	</td><!--/main_content-->

	
	</tr></table>
	</div>
	<!--/page_content-->

	<!--page_footer-->
	<div id="page_footer">

		
		<div class="clear"></div>

		<br />

				<div class="med bold tCenter pad_4">
			<a href="info.php?show=user_agreement" onclick="window.open(this.href, '', InfoWinParams); return false;">Пользовательское Соглашение</a>
									<span class="normal">&nbsp;|&nbsp;</span>
			<a href="info.php?show=reg_problems" onclick="window.open(this.href, '', InfoWinParams); return false;">При проблемах с регистрацией</a>
									<span class="normal">&nbsp;|&nbsp;</span>
			<a href="viewtopic.php?t=2947333" target="_blank">Помощь ресурсу</a>
									<span class="normal">&nbsp;|&nbsp;</span>
			<a href="info.php?show=advert" onclick="window.open(this.href, '', InfoWinParams); return false;">Реклама на сайте</a>
					</div>
		<br />
		
		
		<table width="99%" cellpadding="0" cellspacing="0" class="bCenter">
		<tr>

			<td width="50%" class="vBottom">
				<div class="copyright">
					<p><a href="groupcp.php?g=104792">Координаторы</a> &middot; <a href="groupcp.php?g=104787">Модераторы</a> &middot; <a href="groupcp.php?g=104841">Техническая помощь</a></p>
				</div>
			</td>
			<td class="vBottom">

						<table class="bCenter" cellspacing="0" cellpadding="2" border="0">
			<tr>
				<td class="vBottom nowrap" style="padding: 0 4px;">

					<!--LiveInternet counter-->
					<script type="text/javascript">
						document.write('<a href="//www.liveinternet.ru/stat/pornolab.net/" '+ 'target=_blank><img src="//counter.yadro.ru/hit?t14.6;r'+escape(document.referrer)+((typeof(screen)=='undefined')?'':';s'+screen.width+'*'+screen.height+'*'+(screen.colorDepth?screen.colorDepth:screen.pixelDepth))+';'+Math.random()+'" alt="liveinternet.ru" '+'border=0 width=88 height=31></a>');
					</script>
					<!--/LiveInternet-->
					

				</td>
			</tr>
			</table>

			
			</td>
			 <td width="50%" class="vBottom" style="text-align: right !important;">
					<div style="font-size: 10px; color: #444444; letter-spacing: -1px;">
					    &nbsp;
					    <b><a href="https://5porno.pro/categories/">HD порно ролики 5porno.pro</a></b>
					    &nbsp;
					    Новое порно на <b><a href="http://porno365.plus/categories">Порно 365</a></b>
					    &nbsp;
					    <b><a href="http://www.mega-xxx.tv/categories/">mega-xxx.tv</a></b>
					    &nbsp;
					    <b><a href="https://toy69.ru/dlya-nego/masturbatory/iskusstvennoe-vlagalishche/?sort=p.price?der=ASC?utm_source=pornolab&utm_medium=textdown&utm_campaign=masturbators&utm_content=text1
">Тут продаются японочки</a></b>
					    &nbsp;
					    <b><a href="https://www.topfapgirls.com/">TopFapGirls</a></b> is the best OnlyFans leaks
					    &nbsp;
					    <b><a href="https://thepornplus.com">Порно видео</a></b>
					</div>
			</td>
		</tr>
		</table>

	</div>
	<!--/page_footer-->

	</div>
	<!--/page_container-->






<div id="ajax-loading"><b>Loading...</b></div>
<div id="ajax-error"><b>Error</b></div>
<style>
#bb-alert-box {
	width: auto;
	max-width: 800px;
	line-height: 18px;
	display: none;
}
#bb-alert-msg {
	min-width: 400px;
	max-height: 400px;
	margin: 50px 20px;
	padding: 10px;
	overflow: auto;
	text-align: center;
}
.bb-alert-err {
	color: #7E0000;
	background: #FFEEEE;
	box-shadow: 0 0 20px #B85353;
	font-weight: bold;
}
</style>
<div id="bb-alert-box">
	<div id="bb-alert-msg"></div>
</div>
<div id="modal-blocker"></div>

	</div><!--/body_container-->

</body>
</html>