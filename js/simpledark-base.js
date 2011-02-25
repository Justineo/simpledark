/* <![CDATA[ */

function extractParams() {
	var scripts = document.getElementsByTagName('script');
	var script = scripts[ scripts.length - 1 ];
	var queryString = script.src.replace(/^[^\?]+\??/,'');
	return parseQuery(queryString);
}

function parseQuery(query) {
	var params = new Object();
	if(!query) return params;
	var pairs = query.split('&');
	for(var i = 0; i < pairs.length; i++) {
		var pair = pairs[i].split('=');
		if (!pair || pair.length != 2 ) continue;
		var key = unescape(pair[0]);
		var val = unescape(pair[1]);
		val = decodeURIComponent(val);
		params[key] = val;
	}
	return params;
}

function textAreaFixCursorPosition() {
	/* fix cursor position when focus textareas */
	var ta;
	if(ta = document.getElementById('comment')) {
		if (document.selection) {
			var rng = ta.createTextRange();
			rng.text = ta.value;
			rng.collapse(false);
		}
		else if (typeof ta.selectionStart == 'number' && typeof ta.selectionEnd == 'number') {
			ta.selectionStart = ta.selectionEnd = ta.value.length;
		}
	}
}

function showMessage(msg, callback) {
	$('#commentform .fade').remove();
	$('#submit').after('<span class="fade ajax-comment-msg">' + msg + '</span>');
	$('#commentform .fade').delay(2000).fadeOut(500, function() {
		$(this).remove();
		if(callback && typeof(callback) == 'function')
			callback();
	});
}

function showError(msg, callback) {
	$('#commentform .fade').remove();
	$('#submit').after('<span class="fade ajax-comment-error">' + msg + '</span>');
	$('#commentform .fade').delay(2000).fadeOut(500, function() {
		$(this).remove();
		if(callback && typeof(callback) == 'function')
			callback();
	});
}

/*
 * Reply comment using '@'
 * refined from mg12's code
 */
function reply(author, commentID, commentBoxID) {

	var field;
	// set the comment textarea as the target if it exists
	if(document.getElementById(commentBoxID) && document.getElementById(commentBoxID).type == 'textarea') {
		field = document.getElementById(commentBoxID);
	// or make an alert and return
	} else {
		showError('Cannot find the comment text box!');
		return false;
	}

	if(scriptParams['atreply']) {
		// make '@commenter' link
		var atReplyCode = '<a href="#' + commentID + '">@' + author.replace(/\t|\n/g, "") + ' </a> \n';

		// append reply to the comment textarea
		appendReply(field, atReplyCode, commentBoxID);
	}

	// focus the textarea
	if(!scriptParams['threadcmnts']) {
		$('#respond').ScrollTo(500);
		field.focus();
	}
}

function appendReply(field, atReplyCode, commentBoxID) {

	if(scriptParams['threadcmnts']) {
		field.value = '';
	}

	// make an alert and return when to reply the same comment more than once
	if (field.value.indexOf(atReplyCode) > -1) {
		showMessage('You\'ve already replied this comment!');
	} else {
		// set the content of the textarea as the string to append if the textarea is empty (ignoring white spaces and tabs)
		if (field.value.replace(/\s|\t|\n/g, '') == '') {
			field.value = atReplyCode;
		// or clear all unnecessary new lines and append the string to the textarea
		} else {
			field.value = field.value.replace(/[\n]*$/g, '') + '\n\n' + atReplyCode;
		}
	}
}

var imageCache = [];
function loadImage() {
	// Arguments are image paths relative to the current page.
	var args_len = arguments.length;
	for (var i = args_len; i--;) {
	  var cacheImage = document.createElement('img');
	  cacheImage.src = arguments[i];
	  imageCache.push(cacheImage);
	}
}

function fadingSlideDown(performer, callback) {
	var h = performer.css({
		height: 'auto',
		position: 'absolute',
		visibility: 'hidden'
	}).height();
	performer.css({
		opacity: 0,
		height: 0,
		position: 'static',
		visibility: 'visible'
	});
	performer.animate({
		opacity: 1,
		height: h
	}, 500, function() {
		performer.removeAttr('style');
		if(callback && typeof(callback) == 'function')
			callback();
	});
}

var contentWidth = 580;
function processContent() {
	/* Image Width Restriction */
	$('.entry img').load(function() {
		var w = $(this).width();
		var h = $(this).height();
		var r = w / h;
		var imageWidth = contentWidth;
		var caption = $(this).parents('.wp-caption');
		if(caption.length > 0) {
			caption.width(w + 14);
			if(caption.width() > contentWidth) {
				imageWidth = contentWidth - 14;
				resizeToFit($(this), imageWidth, r);
				caption.width(contentWidth);
			}
		} else if(w > contentWidth) {
			resizeToFit($(this), contentWidth, r);
		}
	});

	/* Clearing Borders for Small Images */
	if(scriptParams['hidesmallimgbdr']) {
		$('.entry img').load(function() {
			var image = $(this);
			var w = image.width();
			var h = image.height();
			if(scriptParams['smallimgwidth'] > 0 && scriptParams['smallimgheight'] > 0) {
				if(scriptParams['smallimglogic'] == 'and') {
					if(w <= scriptParams['smallimgwidth'] && h <= scriptParams['smallimgheight'] > 0) {
						image.addClass('no-border');
					}
				} else {
					if(w <= scriptParams['smallimgwidth'] || h <= scriptParams['smallimgheight'] > 0) {
						image.addClass('no-border');
					}
				}
			} else if(scriptParams['smallimgwidth'] > 0) {
				if(w < scriptParams['smallimgwidth']) {
					image.addClass('no-border');
				}
			} else if(scriptParams['smallimgheight'] > 0) {
				if(h < scriptParams['smallimgheight']) {
					image.addClass('no-border');
				}
			} else {
				image.addClass('no-border');
			}
		});
		$('img.no-border').load(function() {
			var cap = $(this).parents('.wp-caption');
			cap.width(cap.width() - 4);
		});
	}
	
	$('.entry img').each(function() {
		if(this.complete || ($.browser.msie && parseInt($.browser.version) == 6))
			$(this).trigger('load');
	});

	/* Video Width Restriction */
	$('.entry object').each(function() {
		var embed = $(this).find('embed');
		var w = embed.width();
		var h = embed.height();
		if(!w || !h) {
			w = $(this).width();
			h = $(this).height();
		}
		var r = w / h;
		if(w > contentWidth) {
			resizeToFit($(this), contentWidth, r);
			resizeToFit(embed, contentWidth, r);
		}
	});

	/* Table Row Colorization */
	if($.browser.msie) {
		if($.browser.version <= 7) {
			$('.entry table').not('.wp_syntax table').each(function() {
				$(this).find('tr:not(thead tr, tfoot tr):odd').addClass('even');
			});
		}
	}
}

var commentWidth = [378, 516, 490, 456, 422, 388];
function processComments() {
	$('.comment-body img').load(function() {
		var w = $(this).width();
		var h = $(this).height();
		var r = w / h;
		var depth = $(this).parents('.comment-body').parent().attr('class').match(/depth-(\d)/);
		if(!depth) {
			depth = 1;
		} else {
			depth = depth[1];
		}
		if(w > commentWidth[depth]) {
			resizeToFit($(this), commentWidth[depth], r);
		}
	});

	/* Clearing Borders for Small Images */
	if(scriptParams['hidesmallimgbdr']) {
		$('.comment-body img').load(function() {
			var image = $(this);
			var w = image.width();
			var h = image.height();
			if(scriptParams['smallimgwidth'] > 0 && scriptParams['smallimgheight'] > 0) {
				if(scriptParams['smallimglogic'] == 'and') {
					if(w <= scriptParams['smallimgwidth'] && h <= scriptParams['smallimgheight'] > 0) {
						image.addClass('no-border');
					}
				} else {
					if(w <= scriptParams['smallimgwidth'] || h <= scriptParams['smallimgheight'] > 0) {
						image.addClass('no-border');
					}
				}
			} else if(scriptParams['smallimgwidth'] > 0) {
				if(w < scriptParams['smallimgwidth']) {
					image.addClass('no-border');
				}
			} else if(scriptParams['smallimgheight'] > 0) {
				if(h < scriptParams['smallimgheight']) {
					image.addClass('no-border');
				}
			} else {
				image.addClass('no-border');
			}
		});
		$('img.no-border').load(function() {
			var cap = $(this).parents('.wp-caption');
			cap.width(cap.width() - 4);
		});
	}

	$('.comment-body img').each(function() {
		if(this.complete || ($.browser.msie && parseInt($.browser.version) == 6))
			$(this).trigger('load');
	});
}

function processTooltip(tooltip) {
	tooltip.find('.comment-body img').each(function() {
		var w = $(this).width();
		var h = $(this).height();
		var r = w / h;
		if(w > commentWidth[0]) {
			resizeToFit($(this), commentWidth[0], r);
		}
	});
}

function resizeToFit(obj, width, ratio) {
	obj.width(width).height(width / ratio);
}

var commentCache = new Array();

$(document).ready(function() {

	/*************************
	 * Top Menu Manipulation *
	 *************************/
	var menuWindow = $('.top-menu-window');
	var list = menuWindow.children('ul');
	var listWidth = 0;
	var stops = new Array();
	stops.unshift(0);
	list.children('li').each(function() {
		listWidth += $(this).width();
		stops.push(listWidth);
	});
	/* Check if scrolling is needed */
	if(stops[stops.length - 1] > menuWindow.width()) {
		list.width(listWidth);
		var scrollerLeft = $('<div class="scroller scroller-left" style="display:none;"></div>');
		var scrollerRight = $('<div class="scroller scroller-right"></div>');
		scrollerLeft.insertBefore(menuWindow);
		scrollerRight.insertAfter(menuWindow);
		var SCROLLERWIDTH = $('.scroller-left').width() - 10;
		var windowWidth = menuWindow.width() - SCROLLERWIDTH;
		var menuMarginLeft = SCROLLERWIDTH;
		var tmr;
		var current = list.children('.current-cat, .current_page_item, .current_page_ancestor, .current_menu_item, .current-menu-item, .current-menu-ancestor');
		function next(speed, callback) {
			scrollerLeft.fadeIn(100);
			if(!speed)
				speed = 200;
			var rightOffset = windowWidth - menuMarginLeft;
			var next;
			if(rightOffset < listWidth) {
				for(var i = 0; i < stops.length; i ++) {
					if(stops[i] > rightOffset) {
						next = stops[i];
						break;
					}
				}
				menuMarginLeft = windowWidth - next;
				list.animate({ 'margin-left' : menuMarginLeft }, speed, callback);
				if(windowWidth - menuMarginLeft >= listWidth) {
					scrollerRight.fadeOut(100);
				}
			}
		}
		function previous(speed, callback) {
			scrollerRight.fadeIn(100);
			if(!speed)
				speed = 200;
			var leftOffset = 0 - menuMarginLeft;
			var next;
			if(leftOffset > 0) {
				for(var i = 0; i < stops.length + 1; i ++) {
					if(stops[i] >= leftOffset) {
						next = stops[i - 1];
						break;
					}
				}
				menuMarginLeft = SCROLLERWIDTH - next;
				list.animate({ 'margin-left' : menuMarginLeft }, speed, callback);
				if(0 - menuMarginLeft <= 0) {
					scrollerLeft.fadeOut(100);
				}
			}
		}
		var stopped = false;
		function forward() {
			if(!stopped) {
				next(100, forward);
			}
		}
		function rewind() {
			if(!stopped) {
				previous(100, rewind);
			}
		}
		if(listWidth > windowWidth) {
			$('.scroller-right').mousedown(function() {
				stopped = false;
				next();
				tmr = setTimeout(function() {
					forward();
				}, 500);
			}).bind('mouseup mouseout', function() {
				stopped = true;
				clearTimeout(tmr);
			});
			$('.scroller-left').mousedown(function() {
				stopped = false;
				previous();
				tmr = setTimeout(function() {
					rewind();
				}, 500);
			}).bind('mouseup mouseout', function() {
				stopped = true;
				clearTimeout(tmr);
			});
			list.children('li').children('a').mouseover(function() {
				var item = $(this).parent();
				if(item.position().left < 0) {
					previous();
				} else if(item.position().left + item.width() > windowWidth) {
					next();
				}
			});
			if(current.length > 0) {
				function showCurrent() {
					if(current.position().left > windowWidth) {
						next(20, showCurrent);
					} else {
						if(current.width() > windowWidth) {
							next(20);
						} else if(current.position().left + current.width() > windowWidth) {
							next(20, showCurrent);
						}
					}
				}
				showCurrent();
			}
		}
	} else {
		list.width(stops[stops.length - 1]).css({'float': 'right', 'margin-right': 5});
	}
	list.children('li').mouseenter(function() {
		menuWindow.stop().height($(document).height() - menuWindow.offset().top - 45);
		var overflow = $(this).position().left + 152 > windowWidth || $(this).offset().left + 200 > menuWindow.offset().left + menuWindow.width();
		$(this).children('ul:not(:animated)').removeAttr('style').animate(overflow? {'opacity': 'show', 'height': 'show', 'right': '0'} : {'opacity': 'show', 'height': 'show', 'left': '0'}, 100);
		$(this).children('a').addClass('hover');
	}).mouseleave(function() {
		$(this).children('ul').animate({'opacity': 'hide', 'height': 'hide'}, 100);
		menuWindow.animate({'height':45}, 100);
		$(this).children('a').removeClass('hover');
	});
	
	/******************
	 * Preload Images *
	 ******************/
	loadImage(scriptParams['tmpldir'] + '/images/loading.gif');
	
	/**************************
	 * Internal Anchor Easing *
	 **************************/
	$('a[href*=#]:not(.comment-reply-link, .cancel-comment-reply-link)').live('click', function() {
		if(location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname && location.search == this.search) {
			var target = $(this).attr('href');
			target = target.substring(target.indexOf('#'));
			if(target != '#') {
				$('[name="' + target.substring(1) + '"], ' + target).ScrollTo(300);
			}
			return false;
		}
		return true;
	});

	/*******************
	 * Fixed Navigator *
	 *******************/
	if(!!window.XMLHttpRequest) { // not IE6
		var nav = $('#fixed-nav');
		nav.fadeTo(0, .25);

		/* Navigator Opacity Adjustment */
		var page = $('#page');
		$('body').mousemove(function(e) {
			if(e.pageX > (nav.offset().left + nav.width()) && e.pageX > page.offset().left) {
				nav.stop().fadeTo(100, .25);
			} else {
				nav.stop().fadeTo(100, 1);
			}
		});
	}

	/*********************
	 * Post Content Area *
	 *********************/
	processContent();
	
	/* WP-Syntax Support */
	$('.wp_syntax_wrapper').live('hover', function(event) {
		if (event.type == 'mouseover') {
			$(this).children('.wp_syntax_lang').stop().fadeTo(200, 0);
		} else {
			$(this).children('.wp_syntax_lang').stop().fadeTo(200, 1, function() {
				$(this).removeAttr('style');
			});
		}
	});

	/*****************
	 * Comments Area *
	 *****************/
	processComments();

	/* Comment Hover Effect */
	$('.comment').live('mouseenter', function() {
		$(this).addClass('hover').parents('.comment').removeClass('hover');
	}).live('mouseleave', function() {
		$(this).removeClass('hover');
	});

	/* Toggle Commenter Info */
	$('#toggle-info').click(function() {
		var info = $('#comment-author-info');
		if(info.css('display') == 'none') {
			info.slideDown(500);
		}
		$('#commentform .comment-notes').animate({
			opacity: '0',
			height: '0',
			marginBottom: '0'
		}, 500, function() {
			$(this).remove();
		});
		return false;
	});

	/* Allowed Tags Display *-/
	$('.allowed-tags').hide();
	$('#comment').focus(function() {
		$('.allowed-tags').slideDown(500);
	}).focus(textAreaFixCursorPosition);
	/* Allowed Tags Display End */

	/* Move Insertion Point to the End of the Textarea */
	$('#comment').focus(textAreaFixCursorPosition);
	
	/* Reply Button Behaviour */
	$('.comment-reply-link').live('click', function() {
		var comment = $(this).parent().parent().parent();
		reply(comment.children().children('span.author').text(), comment.attr('id'), 'comment');
		return false;
	});

	/* Show Tooltip Comment When Hover the '@somebody' Link */
	$('li:not(.tooltip)>.comment-body a[href^=#comment-]').live('mouseover', function() {
		var id, m, tooltip;
		if((m = $(this).attr('href').match(/#comment-(\d+)/)) != null) {
			id = m[1];
			if(commentCache[id]) {
				tooltip = commentCache[id];
				var list = $(this).parents('.comment-list');
				tooltip.appendTo(list).stop().fadeTo(200, 1);
			}
			else {
				tooltip = $('.tooltip');
				if(tooltip.length > 0) {
					tooltip.stop().children().remove();
				} else {
					tooltip = $('<li class="tooltip"></li>');
				}
				var commentItem = $('#comment-' + id);
				if(commentItem.length > 0) { // found in current page
					tooltip.append(commentItem.children().clone());
					tooltip.addClass(commentItem.attr('class')).find('.actions').remove();
					tooltip.children('ul').remove();
					tooltip.appendTo(commentItem.parent()).stop().fadeTo(200, 1);
					commentCache[id] = tooltip;
				} else { //did not find in current page
					if(!ajaxGetComment) { return; }
					tooltip.appendTo($(this).parents('.comment-list')).stop().fadeTo(200, 1);
					ajaxGetComment(id, tooltip, function() {
						commentCache[id] = tooltip;
					});
				}
			}
			var itemOffset = $(this).offset();
			var listOffset = $('#page').offset();
			var x = itemOffset.left - listOffset.left, y = itemOffset.top - listOffset.top + $(this).height();
			tooltip.css({ left : x + 30, top : y + 10 });
			processTooltip(tooltip);
		}
	}).live('mouseout', function() {
		$('.tooltip').stop().fadeTo(200, 0, function() {
			$(this).detach();
		});
	});

	/* Auto Resize for Comment Form */
	$('#comment').autoResize({
		extraSpace : 0
	});

	/* Use Ctrl+Enter to Submit Comment */
	if(scriptParams['quicksubmit']) {
		$('#comment').keydown(function(e) {
			var ev;
			if(window.event) {
				ev = window.event;
			} else {
				ev = e;
			}
			if(ev != null && ev.ctrlKey && ev.keyCode == 13)
				$('#commentform').submit();
		});
	}

	/**************
	 * Search Box *
	 **************/
	var searchBox = $('#s');
	var msgBox = $('#s-msg');

	if(searchBox.val() != '')
		msgBox.fadeTo(0, 0);
	searchBox.focus(function() {
		if(searchBox.val() == '')
			msgBox.stop().fadeTo(200, .25);
		else
			msgBox.stop().fadeTo(200, 0, function() {
				$(this).hide();
			});
	}).blur(function() {
		if(searchBox.val() == '')
			msgBox.stop().fadeTo(200, 1, function() {
				$(this).removeAttr('style');
			});
	}).keyup(function() {
		if(searchBox.val() == '')
			msgBox.stop().fadeTo(200, .25);
		else
			msgBox.stop().fadeTo(200, 0, function() {
				$(this).hide();
			});
	});
});

/* ]]> */