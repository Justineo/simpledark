addComment={
	moveForm : function(commentElementId,commentId,respondElementId,postId,actionType) {
		var m = this;
		var actionType = actionType || 'reply';
		commentElement = $('#' + commentElementId);
		if(actionType == m.type && m.commentId == commentId) {
			$('#comment').focus();
			return false;
		}
		m.respondId=respondElementId;
		respondElement = $('#' + respondElementId);
		cancelLinkElement = $("#cancel-comment-reply-link");
		parentInput = $('#comment_parent');
		editInput = $('#comment_edit_ID');
		postIdInput = $('#comment_post_ID');
		if($('#wp-temp-form-div').length == 0) {
			var tempForm = $('<div></div>');
			tempForm.attr('id', 'wp-temp-form-div').css('display', 'none');
			respondElement.before(tempForm);
		}
		if(postIdInput.length != 0 && postId) {
			postIdInput.val(postId);
		}
		m.commentId = commentId;
		if(actionType == 'reply') {
			parentInput.val(commentId);
			editInput.val('0');
		} else {
			parentInput.val('0');
			editInput.val(commentId);
			if(!ajaxParams || !ajaxParams['cmntpost']) {
				$('<input type="hidden" name="action" id="action" value="comment_edit" />').insertAfter($('#comment_edit_ID'));
				var commentForm = $('#commentform'); defaultAction = commentForm.attr('action');
				commentForm.attr('action', scriptParams['tmpldir'] + '/comment-edit-post.php').data('action', defaultAction);
			}
		}
		if(!m.type) {
			cancelLinkElement.fadeIn(200, function() {
				$(this).removeAttr('style');
			});
		}
		respondElement.animate({opacity:'hide', height:'hide'}, 200, function() {
			if((m.type && actionType != m.type) || (!m.type && actionType == 'edit') ) {
				shiftText();
			}
			if(m.type == 'edit') {
				respondElement.prev('.comment-body').animate({opacity:'show', height:'show'}, 500, function() {
					$(this).removeAttr('style');
				});
			}
			commentBody = commentElement.children('.comment-body');
			commentBody.after(respondElement);
			if(actionType == 'reply') {
				$(this).animate({opacity:'show', height:'show'}, 500, function() {
					$(this).removeAttr('style');
					commentElement.ScrollTo(300);
					$('#comment').focus();
				});
			} else {
				commentBody.animate({opacity:'hide', height:'hide'}, 200);
				var commentText = commentElement.children().children('.comment-text').text();
				$(this).animate({opacity:'show', height:'show'}, 500, function() {
					$(this).removeAttr('style');
					commentElement.ScrollTo(300);
					$('#comment').val(commentText).focus();
					textAreaFixCursorPosition();
				});
			}
			m.type = actionType;
		});
		return false;
	}
};

function cancelCommentReplyOrEdit() {
	var n = addComment, tempFormDiv = $('#wp-temp-form-div'), respondElementB = $('#' + n.respondId);
	if(tempFormDiv.length == 0 || respondElementB.length ==0 || !n.type) {
		return;
	}
	if(n.type == 'edit') {
		respondElementB.prev('.comment-body').animate({opacity:'show', height:'show'}, 500);
		if(!ajaxParams || !ajaxParams['cmntpost']) {
			$('#action').remove();
			var commentForm = $('#commentform');
			commentForm.attr('action', commentForm.data('action'));
		}
	}
	respondElementB.animate({opacity:'hide', height:'hide'}, 200, function() {
		if(n.type == 'edit') {
			shiftText();
		}
		tempFormDiv.before(respondElementB);
		$('#comment_parent, #comment_edit_ID').val('0');
		n.commentId = 0;
		$(this).unbind('click');
		$('#comment').val('');
		n.type = '';
		$(this).animate({opacity:'show', height:'show'}, 500);
	});
	cancelLinkElement.fadeOut(200);
	return false;
}