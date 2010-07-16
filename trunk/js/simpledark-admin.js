/* <![CDATA[ */
jQuery(document).ready(function() {

	/* Auto Resize for Comment Form */
	jQuery('.appearance_page_SimpleDarkAdmin textarea').autoResize({
		extraSpace : 8
	});

	jQuery('.appearance_page_SimpleDarkAdmin textarea').focus(function() {
		jQuery(this).keydown();
	});

});
/* ]]> */