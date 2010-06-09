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

/*
 * Reply comment using '@'
 * refined from mg12's code
 */
function reply(author, commentID, commentBoxID) {
	// make '@commenter' link
	var atReplyCode = '<a href="#' + commentID + '">@' + author.replace(/\t|\n/g, "") + ' </a> \n';

	// append reply to the comment textarea
	appendReply(atReplyCode, commentBoxID);
}

function appendReply(atReplyCode, commentBoxID) {

	var field;

	// set the comment textarea as the target if it exists
	if(document.getElementById(commentBoxID) && document.getElementById(commentBoxID).type == 'textarea') {
		field = document.getElementById(commentBoxID);
	// or make an alert and return
	} else {
		showError('Cannot find the comment text box!');
		return false;
	}

	// make an alert and return when to reply the same comment more than once
	if (field.value.indexOf(atReplyCode) > -1) {
		showMessage('You\'ve already replied this comment!');
		jQuery('#comment').ScrollTo(500);
		field.focus();
		return false;
	}

	// set the content of the textarea as the string to append if the textarea is empty (ignoring white spaces and tabs)
	if (field.value.replace(/\s|\t|\n/g, '') == '') {
		field.value = atReplyCode;
	// or clear all unnecessary new lines and append the string to the textarea
	} else {
		field.value = field.value.replace(/[\n]*$/g, '') + '\n\n' + atReplyCode;
	}

	// focus the textarea
	jQuery('#respond').ScrollTo(500);
	field.focus();
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

var contentWidth = 596;
var commentWidth = 520;
function processContent() {
	/* Image Width Restriction */
	jQuery('.entry img').each(function() {
		var w = jQuery(this).width();
		var h = jQuery(this).height();
		var r = w / h;
		if(w > contentWidth) {
			jQuery(this).width(contentWidth).height(contentWidth / r);
		}
	});
	jQuery('.comment-body img').each(function() {
		var w = jQuery(this).width();
		var h = jQuery(this).height();
		var r = w / h;
		if(w > commentWidth) {
			jQuery(this).width(commentWidth).height(commentWidth / r);
		}
	});
	jQuery('.entry .wp-caption').removeAttr('style'); // Fix the width of images with wp-caption wrapper
	
	/* Video Width Restriction */
	jQuery('object').each(function() {
		var embed = jQuery(this).find('embed');
		var w = embed.width();
		var h = embed.height();
		var r = w / h;
		if(w > contentWidth) {
			jQuery(this).width(contentWidth).height(contentWidth / r);
			embed.width(contentWidth).height(contentWidth / r);
		}
	});

	/* Table Row Colorization */
	if(jQuery.browser.msie) {
		if(jQuery.browser.version <= 7) {
			jQuery('.entry table').not('.wp_syntax table').each(function() {
				jQuery(this).find('tr:not(thead tr, tfoot tr):odd').addClass('even');
			});
		}
	}
}

var commentCache = new Array();

jQuery(document).ready(function() {

	/******************
	 * Preload Images *
	 ******************/
	loadImage(scriptParams['tmpldir'] + '/images/loading.gif');

	/**************************
	 * Internal Anchor Easing *
	 **************************/
	jQuery('a[href*=#]:not(.reply-button)').live('click', function() {
		if(location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname && location.search == this.search) {
			var target = jQuery(this).attr('href');
			target = target.substring(target.indexOf('#'));
			if(target != '#')
				jQuery(target).ScrollTo(300);
			return false;
		}
		return true;
	});

	/*******************
	 * Fixed Navigator *
	 *******************/
	if(!!window.XMLHttpRequest) { // not IE6
		var nav = jQuery('#fixed-nav');
		nav.fadeTo(0, .25);

		/* Navigator Opacity Adjustment */
		var page = jQuery('#page');
		jQuery('body').mousemove(function(e) {
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

	/*****************
	 * Comments Area *
	 *****************/
	/* Comment Hover Effect */
	jQuery('.comment').live('mouseenter', function() {
		jQuery(this).addClass('hover').parents('.comment').removeClass('hover');
	}).live('mouseleave', function() {
		jQuery(this).removeClass('hover');
	});
	
	/* Toggle Commenter Info */
	jQuery('#toggle-info').click(function() {
		var info = jQuery('#comment-author-info');
		if(info.css('display') == 'none') {
			info.slideDown(500);
		}
		jQuery('#comment-form .message').animate({
			opacity: '0',
			height: '0',
			marginBottom: '0'
		}, 500, function() {
			jQuery(this).remove();
		});
		return false;
	});

	/* Allowed Tags Display */
	jQuery('.allowed-tags').hide();
	jQuery('#comment').focus(function() {
		jQuery('.allowed-tags').slideDown(500);
	}).focus(textAreaFixCursorPosition);

	/* Reply Button Behaviour */
	jQuery('.reply-button').live('click', function() {
		var comment = jQuery(this).parents('li.comment');
		reply(comment.find('span.author').text(), comment.attr('id'), 'comment');
		return false;
	});

	/* Show Tooltip Comment When Hover the '@somebody' Link */
	jQuery('li:not(.tooltip)>.comment-body a[href^=#comment-]').live('mouseover', function() {
		var id, m, tooltip;
		if((m = jQuery(this).attr('href').match(/#comment-(\d+)/)) != null) {
			id = m[1];
			if(commentCache[id]) {
				tooltip = commentCache[id];
				var list = jQuery(this).parents('.comment-list');
				tooltip.appendTo(list).stop().fadeTo(200, 1);
			}
			else {
				tooltip = jQuery('.tooltip');
				if(tooltip.length > 0) {
					tooltip.stop().children().remove();
				} else {
					tooltip = jQuery('<li class="tooltip"></li>');
				}
				var commentItem = jQuery('#comment-' + id);
				if(commentItem.length > 0) { // found in current page
					tooltip.append(commentItem.children().clone());
					tooltip.addClass(commentItem.attr('class')).find('.actions').remove();
					tooltip.appendTo(commentItem.parent()).stop().fadeTo(200, 1);
					commentCache[id] = tooltip;
				} else { //did not find in current page
					if(ajaxGetComment == null) { return; }
					tooltip.appendTo(jQuery(this).parents('.comment-list')).stop().fadeTo(200, 1);
					ajaxGetComment(id, tooltip, function() {
						commentCache[id] = tooltip;
					});
				}
			}
			var itemOffset = jQuery(this).offset();
			var listOffset = jQuery('#page').offset();
			var x = itemOffset.left - listOffset.left, y = itemOffset.top - listOffset.top + jQuery(this).height();
			tooltip.css({ left : x + 30, top : y + 10 });
		}
	}).live('mouseout', function() {
		jQuery('.tooltip').stop().fadeTo(200, 0, function() {
			jQuery(this).detach();
			
		});
	});

//	jQuery('#cancel-comment-reply').fadeTo(0, 0).find('#cancel-comment-reply-link').removeAttr('style');

	/* Auto Resize for Comment Form */
	jQuery('#comment').autoResize({
		extraSpace : 0
	});

	/* Use Ctrl+Enter to Submit Comment */
	jQuery('#comment.quick-submit').keydown(function(e) {
		var ev;
		if(window.event) {
			ev = window.event;
		} else {
			ev = e;
		}
		if(ev != null && ev.ctrlKey && ev.keyCode == 13)
			jQuery('#comment-form').submit();
	});

	/**************
	 * Search Box *
	 **************/
	var searchBox = jQuery('#s');
	var msgBox = jQuery('#s-msg');
/*	var defaultMsg = searchBox.val();
	searchBox.focus(function() {
		if(searchBox.val() == defaultMsg)
			searchBox.val('');
	});
	searchBox.blur(function() {
		if(searchBox.val() == '')
			searchBox.val(defaultMsg);
	});*/
	if(searchBox.val() != '')
		msgBox.fadeTo(0, 0);
	searchBox.focus(function() {
		if(searchBox.val() == '')
			msgBox.stop().fadeTo(200, .25);
		else
			msgBox.stop().fadeTo(200, 0, function() {
				jQuery(this).hide();
			});
	}).blur(function() {
		if(searchBox.val() == '')
			msgBox.stop().fadeTo(200, 1);
	}).keyup(function() {
		if(searchBox.val() == '')
			msgBox.stop().fadeTo(200, .25);
		else
			msgBox.stop().fadeTo(200, 0, function() {
				jQuery(this).hide();
			});
	});
});

/* ]]> */