/* <![CDATA[ */

/* use AJAX to submit comment */
function ajaxSubmitComment() {
	var form = jQuery('#comment-form');
	var params = {};
	
	params['author'] = form.find('#author').val();
	params['email'] = form.find('#email').val();
	params['url'] = form.find('#url').val();
	params['comment'] = form.find('#comment').val();
	params['comment_post_ID'] = form.find('#comment_post_ID').val();
	params['comment_parent'] = form.find('#comment_parent').val();
	jQuery.ajax({
		url: scriptParams['tmpldir'] + '/ajax-comments-post.php',
		type: 'POST',
		data: params,
		dataType: 'html',
		beforeSend: commentSubmitting,
		complete: commentSubmitComplete,
		success: commentSubmitSuccess,
		error: commentSubmitError
	});

	function commentSubmitting() {
		jQuery('.fade').stop().fadeOut(500, function() {
			jQuery(this).remove();
		});
		jQuery('#author, #email, #url, #comment, .entry .textbox, #submit-button').attr('disabled', 'disabled').animate({ opacity: 0.5 }, 500);
		jQuery('#submit-button').after('<span class="processing"></span>');
	}

	function commentSubmitError(request) {
		showError(request.responseText, function() {
			jQuery('#author, #email, #url, #comment, .entry .textbox, #submit-button').animate({ opacity: 1 }, 500).removeAttr('style').removeAttr('disabled');
		});
	}

	function commentSubmitSuccess(data) {
		var content = data.split('<!-- AJAX Comment Data Separator -->');
		if(!jQuery('ol.comment-list').length > 0) {
			jQuery('#comments').append('<ol class="comment-list"></ol>').find('p.message').animate({ opacity: 0, height: 0, marginBottom: 0 }, 500, function() {
				jQuery(this).remove();
				appendCommentToList(content[0]);
			});
		} else {
			appendCommentToList(content[0]);
		}
		jQuery('#comment').val('');
		var countspan = jQuery('.comment-count');
		countspan.animate({opacity:0}, 500, function() {
			jQuery(this).text(countspan.text() - 0 + 1).animate({opacity:1}, 500).removeAttr('style');
		});
		if(window.RCJS) { RCJS.page(scriptParams['blogurl'],scriptParams['rcparams'],0,'Loading'); }
		showMessage(content[1], function() {
			jQuery('#author, #email, #url, #comment, .entry .textbox, #submit-button').animate({ opacity: 1 }, 500).removeAttr('style').removeAttr('disabled');
		});
	}

	function appendCommentToList(data) {
		var list;
		if(!scriptParams['threadcmnts']) {
			list = jQuery('ol.comment-list').append(data);
		} else {
			var respond = jQuery('#respond');
			list = respond.next();
			if(list.length == 0) {
				list = jQuery('<ul></ul>');
				list.attr('class', 'children');
				respond.after(list);
			} else if(list.attr('id') == 'pings') {
				list = jQuery('ol.comment-list');
			}
			list.append(data + '</li>');
		}
		var current = list.children('li:last');
		fadingSlideDown(current);
	/*	cancelCommentReply(function() {
			current.ScrollTo(500);
		});*/
		current.ScrollTo(500);
	}

	function commentSubmitComplete() {
	//	jQuery('#author, #email, #url, #comment, .entry .textbox, #submit-button').animate({ opacity: 1 }, 500).removeAttr('style').removeAttr('disabled');
		jQuery('#comment-form span.processing').remove();
	}

}

/* use AJAX to get comment */
function ajaxGetComment(id, tooltip, callback) {
	jQuery.ajax({
		url: scriptParams['blogurl'] + '?action=cget_ajax&id=' + id,
		type: 'get',
		dataType: 'html',
		beforeSend: commentGetting,
		complete: commentGetComplete,
		success: commentGetSuccess,
		error: commentGetError
	});

	function commentGetting() {
		tooltip.append('<span class="processing"></span>').css({textAlign : 'center', paddingTop : 10, paddingBottom : 10, backgroundColor : '#222', width : 240}, 200);
	}

	function commentGetComplete() {
		tooltip.find('.processing').remove();
	}

	function commentGetError(request) {
		tooltip.css('line-height', '16px').append('<p class="ajax-comment-error">' + request.responseText + '</p>');
	}

	function commentGetSuccess(data) {
		var commentItem = jQuery(data);
		tooltip.fadeOut(200, function() {
			tooltip.children().remove();
			tooltip.append(commentItem.children().clone());
			tooltip.addClass(commentItem.attr('class')).css({textAlign : '', padding : '', backgroundColor : '', width : ''}).find('.actions').remove();
			tooltip.fadeIn(200, function() {
				if(callback && typeof(callback) == 'function')
					callback();
			});
		});
	}
	
}

/* use AJAX to navigate through comment pages */
function ajaxPaginateComments(url) {
	jQuery.ajax({
		url: url,
		type: 'get',
		dataType: 'html',
		beforeSend: commentPaginating,
		complete: commentPaginateComplete,
		success: commentPaginateSuccess,
		error: commentPaginateError
	});
}

var commentNavs;
function commentPaginating() {
	jQuery('.fade').stop().fadeOut(500, function() {
		jQuery(this).remove();
	});
	commentNavs = jQuery('.commentnavi a, .commentnavi span').detach();
	jQuery('.commentnavi').append('<span class="processing"></span>');
}

function commentPaginateComplete() {
	//Do nothing now
}

function commentPaginateSuccess(data) {
	var content = data.split('<!-- AJAX Comment Paginate Data Separator -->');
	jQuery('.comment-list, .commentnavi').animate({
		opacity: 0,
		height: 0
	}, 500, function() {
		jQuery(this).empty();
		jQuery('.comment-list').html(content[0]);
		jQuery('.commentnavi').html(content[1]);
		jQuery(this).each(function() {
			fadingSlideDown(jQuery(this), function() {
				jQuery('#comments').ScrollTo(500);
			});
		});
	});
}

function commentPaginateError() {
	jQuery('.commentnavi span.processing').remove();
	commentNavs.appendTo('.commentnavi');
	commentNavs = null;
}

/* use AJAX to navigate through post pages */
function ajaxPaginatePosts(url) {
	jQuery.ajax({
		url: url,
		type: 'get',
		dataType: 'html',
		beforeSend: postPaginating,
		complete: postPaginateComplete,
		success: postPaginateSuccess,
		error: postPaginateError
	});

	var postNavs;
	function postPaginating() {
		postnavs = jQuery('.pagenavi').children().detach();
		jQuery('.pagenavi').append('<span class="processing"></span>');
	}

	function postPaginateComplete() {
		jQuery('.pagenavi span.processing').remove();
	}

	function postPaginateSuccess(data) {
		var content = data;
		contentSection = jQuery('#content');
		loadContent(content, contentSection, true);
	}

	function postPaginateError() {
		postnavs.appendTo(jQuery('pagenavi'));
		postnavs = null;
	}

}

function ajaxPaginateSingular(url) {
	jQuery.ajax({
		url: url,
		type: 'get',
		dataType: 'html',
		beforeSend: singluarPaginating,
		complete: singluarPaginateComplete,
		success: singluarPaginateSuccess,
		error: singluarPaginateError
	});

	var postNavs;
	function singluarPaginating() {
		var pages = jQuery('.post-pages'); 
		postnavs = pages.html();
		pages.text('').children().remove();
		pages.append('<span class="processing"></span>');
	}

	function singluarPaginateComplete() {
		jQuery('.post-pages span.processing').remove();
	}

	function singluarPaginateSuccess(data) {
		var content = data;
		contentSection = jQuery('.entry');
		loadContent(content, contentSection, false);
	}

	function singluarPaginateError() {
		jQuery('post-pages').html(postnavs);
		postnavs = null;
	}

}

function ajaxSearch(url) {
	jQuery.ajax({
		url: url,
		type: 'get',
		dataType: 'html',
		beforeSend: searchSubmitting,
		complete: searchComplete,
		success: searchSuccess,
		error: searchError
	});

	function searchSubmitting() {
		jQuery('#search-form div').append('<span class="processing"></span>');
	}

	function searchComplete() {
		jQuery('#search-form span.processing').remove();
	}

	function searchSuccess(data) {
		var content = data;
		contentSection = jQuery('#content');
		if(searched)
			loadContent(content, contentSection, true);
	}

	function searchError() {
		jQuery('#search-form span.processing').remove();
		loadContent(contentCache, jQuery('#content'), true);
		searched = false;
	}

}


/* utilities */
function loadContent(content, contentSection, fixPaginatorPlugin, callback) {
	contentSection.animate({
		opacity: 0,
		height: 0
	}, 500, function() {
		jQuery('#header').ScrollTo(500);
		contentSection.empty();
		contentSection.html(content);
		
		//fix for Paginator after AJAX
		if(fixPaginatorPlugin && jQuery('#paginator').length > 0)
			fixPaginator(content);

		processContent();
		fadingSlideDown(contentSection, callback);
	});
}

function fixPaginator(content) {
	if(jQuery.browser.msie) {
		var paginatorScript = content.match(/id="paginator">[\s\S]+<script[^>]+>([\s\S]+?)<\/script>/i);
		if(paginatorScript != null) {
			var src = paginatorScript[1];
			eval(src.match(/(pag = new.+\);)/i)[1]);
		}
	}
}

function showMessage(msg, callback) {
	var fade;
	jQuery('#comment-form .fade').remove();
	jQuery('#submit-button').after('<span class="fade ajax-comment-msg">' + msg + '</span>');
	jQuery('#comment-form .fade').delay(2000).fadeOut(500, function() {
		jQuery(this).remove();
		if(callback && typeof(callback) == 'function')
			callback();
	});
}

function showError(msg, callback) {
	var fade;
	jQuery('#comment-form .fade').remove();
	jQuery('#submit-button').after('<span class="fade ajax-comment-error">' + msg + '</span>');
	jQuery('#comment-form .fade').delay(2000).fadeOut(500, function() {
		jQuery(this).remove();
		if(callback && typeof(callback) == 'function')
			callback();
	});
}

var contentCache, searched = false;
jQuery(document).ready(function() {

	jQuery('#comment-form').submit(function() {
		ajaxSubmitComment();
		return false;
	});

	jQuery('.commentnavi a').live('click', function() {
		var wpurl = jQuery(this).attr('href').split(/(\?|&)action=cpage_ajax.*$/)[0];
		var commentPage = 1;
		if (/comment-page-/i.test(wpurl)) {
			commentPage = wpurl.split(/comment-page-/i)[1].split(/(\/|#|&).*$/)[0];
		} else if (/cpage=/i.test(wpurl)) {
			commentPage = wpurl.split(/cpage=/i)[1].split(/(\/|#|&).*$/)[0];
		}
		var postId = jQuery('#cp_post_id').text();
		var url = wpurl.split(/#.*$/)[0];
		url += /\?/i.test(wpurl) ? '&' : '?';
		url += 'action=cpage_ajax&post=' + postId + '&page=' + commentPage;
		ajaxPaginateComments(url);
		return false;
	});

	jQuery('.post-pages a').live('click', function() {
		var wpurl = jQuery(this).attr('href').split(/(\?|&)action=spage_ajax.*$/)[0];
		var singluarPage = 1;
		if(/page=/i.test(wpurl)) {
			singluarPage = wpurl.split(/page=/i)[1].split(/(\/|#|&).*jQuery/)[0];
		}
		var url = wpurl.split(/#.*$/)[0];
		url += /\?/i.test(wpurl) ? '&' : '?';
		url += 'action=spage_ajax';
		if(!/page=/i.test(url))
			url += '&page=' + singluarPage;
		ajaxPaginateSingular(url);
		return false;
	});

	jQuery('body.home .pagenavi a, body.archive .pagenavi a, body.search .pagenavi a').live('click', function() {
		var url = jQuery(this).attr('href');
		if(/action=/i.test(url)) {
			url = url.replace(/action=[^#&]+/ig, 'action=ppage_ajax');
		}
		else {
			url += /\?/i.test(url) ? '&' : '?';
			url += 'action=ppage_ajax';
		}
		ajaxPaginatePosts(url);
		return false;
	});

	jQuery('#search-form').submit(function() {
		var s = jQuery(this).find('#s').val();
		var url = jQuery(this).attr('action') + '?s=' + encodeURIComponent(s) + '&action=search_ajax'
		ajaxSearch(url);
		return false;
	});

	var timer, currentKey;
	jQuery('#s').keyup(function() {
		clearTimeout(timer);
		timer = setTimeout(function() {
			var sInput = jQuery('#s');
			var s = sInput.val();
			if(s.length == 0) {
				if(searched) {
					loadContent(contentCache, jQuery('#content'), true, function() {
						sInput.focus();
					});
					jQuery('#search-form span.processing').remove();
					searched = false;
				}
				currentKey = s;
			} else {
				if(s != currentKey) {
					if(!searched) {
						contentCache = jQuery('#content')[0].innerHTML;
						searched = true;
					}
					currentKey = s;
					jQuery('#search-form').submit();
				}
			}
		}, 800);
	});

});

/* ]]> */