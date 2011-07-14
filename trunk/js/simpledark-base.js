/* <![CDATA[ */
if(!window.ajaxParams) {
	var ajaxParams = false;
}

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

	if($('#comment_edit_ID').val() != 0 && !scriptParams['threadcmnts']) {
		cancelCommentEdit();
	}

	var field = $('#' + commentBoxID);

	// set the comment textarea as the target if it exists
	if(field.length == 0) {
		return false;
	}

	if(scriptParams['atreply']) {
		// make '@commenter' link
		var atReplyCode = '<a href="#' + commentID + '">@' + author.replace(/\t|\n/g, "") + ' </a> \n';

		// append reply to the comment textarea
		if(scriptParams['threadcmnts']) {
			field.val('');
		}

		if (field.val().indexOf(atReplyCode) == -1) {
		
			// set the content of the textarea as the string to append if the textarea is empty (ignoring white spaces and tabs)
			if (field.val().replace(/\s|\t|\n/g, '') == '') {
				field.val(atReplyCode);
			// or clear all unnecessary new lines and append the string to the textarea
			} else {
				field.val(field.val().replace(/[\n]*$/g, '') + '\n\n' + atReplyCode);
			}
		}
		textAreaFixCursorPosition();
	}

	// focus the textarea
	if(!scriptParams['threadcmnts']) {
		$('#respond').ScrollTo(500);
		field.focus();
	}
}

function editComment(comment)  {
	var field = $('#comment'), editIdField = $('#comment_edit_ID'), commentID = comment.attr('id').split('comment-')[1];
	if(editIdField.val() == commentID) {
		field.focus();
		return;
	} else if(editIdField.val() != 0) {
		$('#comment-' + editIdField.val()).fadeTo(200, 1);
		comment.fadeTo(200, .2);
		editIdField.val(commentID);
		field.val(comment.children().children('.comment-text').text());
		return;
	}
	editIdField.val(commentID);
	field.val(comment.children().children('.comment-text').text());
	var respondElement = $('#respond'), cancelLinkElement = $("#cancel-comment-reply-link");
	if(!ajaxParams || !ajaxParams['cmntpost']) {
		$('<input type="hidden" name="action" id="action" value="comment_edit" />').insertAfter($('#comment_edit_ID'));
		var commentForm = $('#commentform'); defaultAction = commentForm.attr('action');
		commentForm.attr('action', scriptParams['tmpldir'] + '/comment-edit-post.php').data('action', defaultAction);
	}
	cancelLinkElement.fadeIn(200, function() {
		$(this).removeAttr('style');
	});
	comment.fadeTo(200, .2);
	textAreaFixCursorPosition();
	respondElement.find('#reply-title').animate({opacity:0}, 200, function() {
		shiftText();
		$(this).animate({opacity:1}, 200, function() {
			$(this).removeAttr('style');
		});
		respondElement.ScrollTo(500);
		field.focus();
	})
}

function cancelCommentEdit() {
	var field = $('#comment'), respondElement = $('#respond'), cancelLinkElement = $("#cancel-comment-reply-link"), comment = $('#comment-' + $('#comment_edit_ID').val());
	$('#comment_edit_ID').val(0);
	field.val('');
	if(!ajaxParams || !ajaxParams['cmntpost']) {
		$('#action').remove();
		var commentForm = $('#commentform');
		commentForm.attr('action', commentForm.data('action'));
	}
	cancelLinkElement.fadeOut(200).unbind('click');
	comment.fadeTo(200, 1, function() {
		$(this).removeAttr('style');
	});
	respondElement.find('#reply-title').animate({opacity:0}, 200, function() {
		shiftText();
		$(this).animate({opacity:1}, 200, function() {
			$(this).removeAttr('style');
		});
	});
	return false;
}

function shiftText() {
	var titleElement = $('#reply-title'), cancelElement = $('#cancel-comment-reply-link'), titleAltElement = $('#edit-or-reply-title'), cancelAltElement = $('#edit-or-reply-cancel'), s = titleElement.children('small');
	s.detach();
	title = titleElement.html(), cancel = cancelElement.html();
	titleElement.html(titleAltElement.html());
	cancelElement.html(cancelAltElement.html());
	titleAltElement.html(title);
	cancelAltElement.html(cancel);
	s.appendTo(titleElement);
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
		var checkSize = function(image) {
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
		};
		$('.entry img').load(function() {
			var image = $(this);
			checkSize(image);
		});
		$('.entry embed, .entry object, .entry iframe').each(function() {
			var image = $(this);
			checkSize(image);
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
		var w, h;
		var embed = $(this).find('embed');
		if(embed.length > 0) {
			w = embed.width();
			h = embed.height();
		}
		if(!w || !h) {
			w = $(this).width();
			h = $(this).height();
		}
		var r = w / h;
		if(w > contentWidth) {
			resizeToFit($(this), contentWidth, r);
			if(embed.length > 0) {
				resizeToFit(embed, contentWidth, r);
			}
		} else if($.browser.msie && $.browser.version == 8) {
			$(this).width(w).height(h);
		}
	});

	if($.browser.msie && $.browser.version <= 7) {
		/* Table Row Colorization */
		$('.entry table').not('.wp_syntax table').each(function() {
			$(this).find('tr:not(thead tr, tfoot tr):odd').addClass('even');
		});

		/* Fix Cursor Position */
		$('#comment').focus(function() {
			$(this).val($(this).val());
		});
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
		list.width(stops[stops.length - 1]).animate({'margin-left': menuWindow.width() - stops[stops.length - 1] - 5}, 500);
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
	$('body').delegate('a[href*=#]:not(.comment-reply-link, #cancel-comment-reply-link, .comment-edit-link, .commentnavi a)', 'click', function() {
		// comment page navigation on clicking @reply link
		if($(this).is('li:not(.tooltip)>.comment-body a[href^=#comment-]')) {
			var m, id, map, current;
			if((m = $(this).attr('href').match(/#comment-(\d+)/)) != null) {
				id = m[1];
			}
			current = $('.commentnavi .page-numbers.current').text();
			map = $.parseJSON($('#comment-page-number').val());
			if(map && (current != map[id])) {
				var url = document.URL.split(/#.*$/)[0].split(/\/comment-page-[\d]+\//)[0];
				var postId = $('#cp_post_id').text();
				url += /\?/i.test(url) ? '&' : '?';
				if(ajaxParams && ajaxParams['cmntpagenav']) { // AJAX pagination is on
					url += 'action=cpage_ajax&post=' + postId + '&page=' + map[id];
					ajaxPaginateComments(url, function() {
						$('#comment-' + id).ScrollTo(500);
					});
				} else {
					url += 'cpage=' + map[id];
					window.location.href = url + '#comment-' + id;
				}
			} else {
				$('#comment-' + id).ScrollTo(300);
			}
		}

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
	$('#content').delegate('.wp_syntax_wrapper', 'hover', function(event) {
		if (event.type == 'mouseenter') {
			$(this).children('.wp_syntax_lang').stop().fadeTo(200, .2);
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
	$('#reaction').delegate('.comment', 'mouseenter', function(e) {
		$(this).addClass('hover').parents('.comment').removeClass('hover');
		e.stopPropagation();
	}).delegate('.comment', 'mouseleave', function() {
		$(this).removeClass('hover').parent().closest('.comment').addClass('hover');
	});

	/* Toggle Commenter Info */
	$('#toggle-info').click(function() {
		var info = $('#comment-author-info');
		if(info.css('display') == 'none') {
			info.slideDown(500);
		}
		$('#commentform .comment-notes').animate({
			opacity: 'hide',
			height: 'hide',
			marginBottom: 'hide'
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

	/* Smiley Select Button Behaviour */
	if($('#comment').length > 0) {
		var commentInput = $('#comment').get(0);
		$('.smiley-select').click(function() {
			var tag = $(this).attr('alt');
			if (document.selection) {
				commentInput.focus();
				sel = document.selection.createRange();
				sel.text = tag;
				commentInput.focus();
			}
			else if (commentInput.selectionStart || commentInput.selectionStart == '0') {
				var startPos = commentInput.selectionStart;
				var endPos = commentInput.selectionEnd;
				var cursorPos = endPos;
				commentInput.value = commentInput.value.substring(0, startPos) + tag + commentInput.value.substring(endPos, commentInput.value.length);
				cursorPos += tag.length;
				commentInput.focus();
				commentInput.selectionStart = cursorPos;
				commentInput.selectionEnd = cursorPos;
			}
			else {
				commentInput.value += tag;
				commentInput.focus();
			}
		});
	}

	/* Comment Reply Link Behaviour */
	$('.comment-reply-link').live('click', function() {
		var comment = $(this).parent().parent().parent();
		reply(comment.children().children('span.author').text(), comment.attr('id'), 'comment');
		return false;
	});

	/* Comment Edit Link Behaviour */
	if(scriptParams['commentquickedit'] && !scriptParams['threadcmnts']) {
		$('.comment-edit-link').live('click', function(e) {
			var comment = $(this).parent().parent().parent();
			editComment(comment, 'comment');
			return false;
		});
	}
	
	/* Cancel Comment Edit or Reply Link Behaviour */
	var cancelLinkElement = $("#cancel-comment-reply-link");
	if(cancelLinkElement.length > 0) {
		if(scriptParams['threadcmnts']) {
			cancelLinkElement.click(cancelCommentReplyOrEdit);
		} else if(scriptParams['commentquickedit']) {
			cancelLinkElement.click(cancelCommentEdit);
		}
	}

	/* Show Tooltip Comment When Hover the '@somebody' Link */
	$('#comments').delegate('li:not(.tooltip)>.comment-body a[href^=#comment-]', 'mouseenter', function() {
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
	}).delegate('li:not(.tooltip)>.comment-body a[href^=#comment-]', 'mouseleave', function() {
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
			var ev = window.event || e;
			if(ev != null && ev.ctrlKey && ev.keyCode == 13) {
				// Comment form in WordPress has an input[name="submit"] which overwrites the object's submit() function, use the function in its prototype to invoke
				HTMLFormElement.prototype.submit.apply($('#commentform').get(0));
			}
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