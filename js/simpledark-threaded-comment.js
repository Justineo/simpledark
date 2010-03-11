addComment={
	moveForm : function(parentCommentElementId,parentCommentId,respondElementId,postId) {
		var m = this;
		parentCommentElement = $('#' + parentCommentElementId);
		m.respondId=respondElementId;
		respondElement = $('#' + respondElementId);
		cancelLinkDiv = $('#cancel-comment-reply');
		cancelLinkElement = $("#cancel-comment-reply-link");
		parentInput = $("#comment_parent");
		postIdInput = $("#comment_post_ID");
		if(parentCommentElement.next().attr('id') == respondElement.attr('id')) {
			$('#comment').focus();
			return false;
		}

//		alert('Check point #1');
//		alert(parentCommentElement.length + ', ' + respondElement.length + ', ' + cancelLinkElement.length + ', ' + parentInput.length);
		if(parentCommentElement.length == 0 || respondElement.length == 0 || cancelLinkElement.length ==0 || parentInput.length == 0) {
			return;
		}
//		alert('Check point #2');
		postId=postId||false;
		if($('#wp-temp-form-div').length == 0) {
			var tempForm = $('<div></div>');
			tempForm.attr('id', 'wp-temp-form-div');
			tempForm.css('display', 'none');
			respondElement.before(tempForm);
		}
//		alert('Check point #3');
//		alert(parentCommentElement.next().html());
		if(postIdInput.length != 0 && postId) {
			postIdInput.val(postId);
		}
		parentInput.val(parentCommentId);
		fadingSlideDown(cancelLinkDiv);
		cancelLinkElement.click(cancelCommentReply);
		respondElement.animate({opacity:0, height:0}, 200, function() {
			parentCommentElement.after(respondElement);
			fadingSlideDown($(this), function() {
				parentCommentElement.parent().ScrollTo(300);
				$('#comment').focus();
			});
		});
		return false;
	}
};

function cancelCommentReply(callback) {
	var n = addComment, tempFormDiv = $('#wp-temp-form-div'), respondElementB = $('#' + n.respondId);
	if(tempFormDiv.length == 0 || respondElementB.length ==0) {
		return;
	}
	$('#comment_parent').val('0');
	respondElementB.animate({opacity:0, height:0}, 200, function() {
		tempFormDiv.before(respondElementB);
		tempFormDiv.remove();
		fadingSlideDown($(this));
	});
	cancelLinkDiv.animate({opacity:0, height:0}, 200, function() {
		if(callback && typeof(callback) == 'function')
			callback();
	});
	$(this).unbind('click');
	return false;
}