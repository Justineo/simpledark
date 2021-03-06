/* <![CDATA[ */

/* use AJAX to submit comment */
function ajaxSubmitComment() {
	var form = $('#commentform');
	var respond = $('#respond');
	var params = {};

	params['author']			= $('#author').val();
	params['email']				= $('#email').val();
	params['url']				= $('#url').val();
	params['comment']			= $('#comment').val();
	params['comment_post_ID']	= $('#comment_post_ID').val();
	params['comment_parent']	= $('#comment_parent').val();
	params['comment_edit_ID']	= $('#comment_edit_ID').val();
	if(scriptParams['threadcmnts']) {
		var prev;
		if(respond.parent('li.comment').length > 0) {
			prev = respond.next().children('li:last');
		} else {
			prev = $('ol.comment-list li:last');
		}
		if(prev.length > 0) {
			if(prev.hasClass('even') || prev.hasClass('thread-even')) {
				params['comment_alt'] = '1';
			}
		}
	}

	$.ajax({
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
		$('.fade').stop().fadeOut(500, function() {
			$(this).remove();
		});
		$('#author, #email, #url, #comment, .entry .textbox, #submit').attr('disabled', 'disabled').animate({ opacity: 0.5 }, 500);
		$('#cancel-comment-reply-link').fadeOut(500);
		$('#submit').after('<span class="processing"></span>');
	}

	function commentSubmitError(request) {
		showError(request.responseText, function() {
			$('#author, #email, #url, #comment, .entry .textbox, #submit').removeAttr('disabled').animate({ opacity: 1 }, 500, function() {
				$(this).removeAttr('style');
			});
			if($('#respond').parent('li').length > 0) {
				$('#cancel-comment-reply-link').fadeIn(500, function() {
					$(this).removeAttr('style');
				});
			};
		});
	}

	function commentSubmitSuccess(data) {
		var content = data.split('<!-- AJAX Comment Data Separator -->');
		meta = eval('(' + content[1] + ')');
		if(meta['type'] == 'new') {
			if(!$('ol.comment-list').length > 0) {
				$('#comments').append('<ol class="comment-list"></ol>').find('p.message').animate({ opacity:'hide', height:'hide', marginBottom:'hide' }, 500, function() {
					$(this).remove();
					appendCommentToList(content[0]);
				});
			} else {
				appendCommentToList(content[0]);
			}
			$('#comment').val('');
			var countSpan = $('.comment-count'), commentCount = countSpan.text() - 0;
			countSpan.animate({opacity:'hide'}, 500, function() {
				countSpan.text(commentCount + 1).animate({opacity:'show'}, 500, function() {
					$(this).removeAttr('style');
				});
			});
			var countLink = $('.comment-link');
			countLink.animate({opacity:'hide'}, 500, function() {
				var newText, currentText = countLink.text();
				if(currentText == ajaxParams['cmntinfotxt']['zero']) {
					newText = ajaxParams['cmntinfotxt']['one'];
				} else {
					newText = ajaxParams['cmntinfotxt']['more'].replace('%', commentCount + 1);
				}
				countLink.text(newText).animate({opacity:'show'}, 500, function() {
					$(this).removeAttr('style');
				});
			});
		} else if(meta['type'] == 'edit') {
			var updatedComment = $(content[0]), comment = $('#comment-' + $('#comment_edit_ID').val());
			comment.children('.avatar').attr('src', updatedComment.children('.avatar').attr('src'));
			comment.children('.comment-meta').html(updatedComment.children('.comment-meta').html());
			comment.children().children('.comment-text').val(updatedComment.children().children('.comment-text').val());
			comment.children('.comment-body').html(updatedComment.children('.comment-body').html());
			$('.new-comment').removeClass('new-comment');
			comment.addClass('new-comment').click(function() {
				$(this).removeClass('new-comment').unbind('click');
			});
			processComments();

			comment.ScrollTo(500);
		}
		if(scriptParams['threadcmnts']) {
			cancelCommentReplyOrEdit();
		} else if(meta['type'] == 'edit') {
			cancelCommentEdit();
		}
		showMessage(meta['message'], function() {
			$('#author, #email, #url, #comment, .entry .textbox, #submit').removeAttr('disabled').animate({ opacity: 1 }, 500, function() {
				$(this).removeAttr('style');
			});
		});
		if(window.RCJS) { // for older versions of WP-RecentComments
			RCJS.page(scriptParams['blogurl'],scriptParams['rcparams'],0,'Loading');
		} else if(window.RecentComment) { // version 2.0 and above
			// refreshment is now unavailable to be invoked from external, discussed with mg12 and now waiting for updated versions.
		}
	}

	function appendCommentToList(data) {
		var list, current = $(data + '</li>');
		if(!scriptParams['threadcmnts'] || $('.comment #respond').length == 0) {
			list = $('ol.comment-list').append(current);
		} else {
			var respond = $('#respond');
			list = respond.next('ul.children');
			if(list.length == 0) {
				list = $('<ul></ul>');
				list.attr('class', 'children');
				respond.after(list);
			}
			list.append(current);
		}
		$('.new-comment').removeClass('new-comment');
		current.animate({opacity:'hide', height:'hide'}, 0).addClass('new-comment').click(function() {
			$(this).removeClass('new-comment').unbind('click');
		});
		processComments();
		current.animate({opacity:'show', height:'show'}, 500, function() {
			$(this).removeAttr('style').ScrollTo(500);
		});
	}

	function commentSubmitComplete() {
		$('#commentform span.processing').remove();
	}

}

/* use AJAX to get comment */
function ajaxGetComment(id, tooltip, callback) {
	$.ajax({
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
		var commentItem = $(data);
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
var paginateCommentsCallback;
function ajaxPaginateComments(url, cb) {

	paginateCommentsCallback = cb;
	
	$.ajax({
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
	$('.fade').stop().fadeOut(500, function() {
		$(this).remove();
	});
	commentNavs = $('.commentnavi a, .commentnavi span').detach();
	$('.commentnavi').append('<span class="processing"></span>');
}

function commentPaginateComplete() {
	//Do nothing now
}

function commentPaginateSuccess(data) {
	var content = data.split('<!-- AJAX Comment Paginate Data Separator -->');
	$('.comment-list, .commentnavi').animate({
		opacity: 'hide',
		height: 'hide'
	}, 500, function() {
		$(this).empty();
		$('.comment-list').html(content[0]);
		$('.commentnavi').html(content[1]);
		processComments();
		$(this).each(function() {
			$(this).animate({opacity:'show', height:'show'}, 500, function() {
				$(this).removeAttr('style');
				if(paginateCommentsCallback && typeof(paginateCommentsCallback) == 'function') {
					paginateCommentsCallback();
				} else {
					$('#comments').ScrollTo(500);
				}
			});
		});
	});
}

function commentPaginateError() {
	$('.commentnavi span.processing').remove();
	commentNavs.appendTo('.commentnavi');
	commentNavs = null;
}

/* use AJAX to navigate through post pages */
function ajaxPaginatePosts(url) {
	$.ajax({
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
		postnavs = $('.pagenavi').children().detach();
		$('.pagenavi').append('<span class="processing"></span>');
	}

	function postPaginateComplete() {
		$('.pagenavi span.processing').remove();
	}

	function postPaginateSuccess(data) {
		var content = data;
		contentSection = $('#content');
		loadContent(content, contentSection, true);
	}

	function postPaginateError() {
		postnavs.appendTo($('pagenavi'));
		postnavs = null;
	}

}

function ajaxPaginateSingular(url) {
	$.ajax({
		url: url,
		type: 'get',
		dataType: 'html',
		beforeSend: singularPaginating,
		complete: singularPaginateComplete,
		success: singularPaginateSuccess,
		error: singularPaginateError
	});

	var postNavs;
	function singularPaginating() {
		var pages = $('.post-pages');
		postnavs = pages.html();
		pages.text('').children().remove();
		pages.append('<span class="processing"></span>');
	}

	function singularPaginateComplete() {
		$('.post-pages span.processing').remove();
	}

	function singularPaginateSuccess(data) {
		var content = data;
		contentSection = $('body.single .entry, body.page .entry');
		loadContent(content, contentSection, false);
	}

	function singularPaginateError() {
		$('post-pages').html(postnavs);
		postnavs = null;
	}

}

function ajaxSearch(url) {
	$.ajax({
		url: url,
		type: 'get',
		dataType: 'html',
		beforeSend: searchSubmitting,
		complete: searchComplete,
		success: searchSuccess,
		error: searchError
	});

	function searchSubmitting() {
		$('#search-form').append('<span class="processing"></span>');
	}

	function searchComplete() {
		$('#search-form span.processing').remove();
	}

	function searchSuccess(data) {
		var content = data;
		contentSection = $('#content');
		if(searched)
			loadContent(content, contentSection, true);
	}

	function searchError() {
		$('#search-form span.processing').remove();
		loadContent(contentCache, $('#content'), true);
		searched = false;
	}

}


/* utilities */
function loadContent(content, contentSection, fixPaginatorPlugin, callback) {
	contentSection.animate({
		opacity: 'hide',
		height: 'hide'
	}, 500, function() {
		$('#header').ScrollTo(500);
		contentSection.empty();
		contentSection.html(content);

		//fix for Paginator after AJAX
		if(fixPaginatorPlugin && $('#paginator').length > 0)
			fixPaginator(content);

		contentSection.animate({opacity:'show', height:'show'}, 500, function() {
			if(callback && typeof(callback) == 'function')
				callback();
			processContent();
		});
	});
}

function fixPaginator(content) {
	if($.browser.msie) {
		var paginatorScript = content.match(/id="paginator">[\s\S]+<script[^>]+>([\s\S]+?)<\/script>/i);
		if(paginatorScript != null) {
			var src = paginatorScript[1];
			eval(src.match(/(pag = new.+\);)/i)[1]);
		}
	}
}

var contentCache, searched = false;
$(document).ready(function() {

	if(ajaxParams['cmntpost']) {
		$('#commentform').submit(function() {
			ajaxSubmitComment();
			return false;
		});
	}

	if(ajaxParams['cmntpagenav']) {
		$('.commentnavi').delegate('a', 'click', function() {
			var wpurl = $(this).attr('href').split(/(\?|&)action=cpage_ajax.*$/)[0];
			var commentPage = 1;
			if (/comment-page-/i.test(wpurl)) {
				commentPage = wpurl.split(/comment-page-/i)[1].split(/(\/|#|&).*$/)[0];
			} else if (/cpage=/i.test(wpurl)) {
				commentPage = wpurl.split(/cpage=/i)[1].split(/(\/|#|&).*$/)[0];
			}
			var postId = $('#cp_post_id').text();
			var url = wpurl.split(/#.*$/)[0];
			url += /\?/i.test(wpurl) ? '&' : '?';
			url += 'action=cpage_ajax&post=' + postId + '&page=' + commentPage;
			if(scriptParams['threadcmnts']) {
				cancelCommentReplyOrEdit();
			}
			ajaxPaginateComments(url);
			return false;
		});
	}

	if(ajaxParams['postcntntpagnav']) {
		$('body.single .entry, body.page .entry').delegate('.post-pages a', 'click', function() {
			if(searched)
				return true;
			var wpurl = $(this).attr('href').split(/(\?|&)action=spage_ajax.*$/)[0];
			var singularPage = 1;
			if(/page=/i.test(wpurl)) {
				singularPage = wpurl.split(/page=/i)[1].split(/(\/|#|&).*$/)[0];
			} else if (/\/[\d+]\/?$/.test(wpurl)) {
				var result = wpurl.match(/\/([\d+])\/?$/);
				if(result != null) {
					singularPage = result[1];
				}
			}
			var url = wpurl.split(/#.*$/)[0];
			url += /\?/i.test(wpurl) ? '&' : '?';
			url += 'action=spage_ajax';
			if(!/page=/i.test(url))
				url += '&page=' + singularPage;
			ajaxPaginateSingular(url);
			return false;
		});
	}

	if(ajaxParams['postpagenav']) {
		$('body.home #content, body.archive #content, body.search #content, #content.search').delegate('.pagenavi a', 'click', function() {
			var url = $(this).attr('href');
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
	}

	if(ajaxParams['search']) {
		var cmntNav = $('#fixed-nav .cmnts');
		var timer, currentKey;
		$('#search-form').submit(function() {
			if(timer) {
				clearTimeout(timer);
			}
			var s = $(this).find('#s').val();
			var url = $(this).attr('action') + '?s=' + encodeURIComponent(s) + '&action=search_ajax';
			ajaxSearch(url);
			$('#content').addClass('search');
			cmntNav.detach();
			$('.top-menu').find('.current_page_item, .current-cat, .current_menu_item, .current-menu-item, .current_page_ancestor, .current-cat-parent, .current-menu-ancestor').each(function() {
				$(this).attr('class', $(this).attr('class').replace(/current/g, 'kurrent'));
			});
			return false;
		});

		$('#s').bind('keyup', function() {
			if(timer) {
				clearTimeout(timer);
			}
			timer = setTimeout(function() {
				var sInput = $('#s');
				var s = sInput.val();
				if(s.length == 0) {
					if(searched) {
						loadContent(contentCache, $('#content'), true, function() {
							sInput.focus();
							$('.top-menu').find('.kurrent_page_item, .kurrent-cat, .kurrent_menu_item, .kurrent-menu-item, .kurrent_page_ancestor, .kurrent-cat-parent, .kurrent-menu-ancestor').each(function() {
								$(this).attr('class', $(this).attr('class').replace(/kurrent/g, 'current'));
							});
							cmntNav.insertAfter($('#fixed-nav .top'));
							$('#content').removeClass('search');
						});
						$('#search-form span.processing').remove();
						searched = false;
					}
					currentKey = s;
				} else {
					if(s != currentKey) {
						if(!searched) {
							contentCache = $('#content')[0].innerHTML;
							searched = true;
						}
						currentKey = s;
						$('#search-form').submit();
					}
				}
			}, 800);
		});
	}

});

/* ]]> */