<?php get_header(); ?>
<?php
if(have_posts()) {
	the_post();
?>
		<div id="post-<?php the_ID(); ?>" <?php post_class();?>>
<?php
	// give some extra metadata (aka is-sticky, is-protected, is-private, etc.)
	simpledark_extra_post_meta();
	$metadata = wp_get_attachment_metadata();
?>
			<h2 class="post-title"><a rel="bookmark permalink" title="<?php printf(__('Permanent Link to %s', THEME_NAME), the_title_attribute('echo=0')); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<div class="post-meta"><?php the_time(simpledark_time_format('date')); ?> / <?php printf(__('Uploaded by %s', THEME_NAME), simpledark_get_the_author_posts_link()); ?> / <?php if(function_exists('the_views')) { the_views(); echo ' / '; } if(post_password_required()) { _e('X Comments', THEME_NAME); } else { comments_popup_link(__('No Comments', THEME_NAME), __('1 Comment', THEME_NAME), __('% Comments', THEME_NAME), 'comment-link', __('Comments Off', THEME_NAME)); } edit_post_link(__('Edit', THEME_NAME), ' / '); ?></div>
			<div class="entry attachment-entry">
<?php
	if ( wp_attachment_is_image() ) {
		$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
		foreach ( $attachments as $k => $attachment ) {
			if ( $attachment->ID == $post->ID )
				break;
		}
		$k++;
		// If there is more than 1 image attachment in a gallery
		if ( count( $attachments ) > 1 ) {
			if ( isset( $attachments[ $k ] ) )
				// get the URL of the next image attachment
				$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
			else
				// or get the URL of the first image attachment
				$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
		} else {
			// or, if there's only 1 image attachment, get the URL of the image
			$next_attachment_url = wp_get_attachment_url();
		}
?>
					<a href="<?php echo $next_attachment_url; ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment">
						<?php echo wp_get_attachment_image( $post->ID, 'full' ); ?>
					</a>
<?php
	} else {
		printf(__('Permalink: %s', THEME_NAME), sprintf('<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>', wp_get_attachment_url(), esc_attr( get_the_title() ), get_permalink() ));
	}
?>
			</div>
			<div class="post-info">
<?php
	if(wp_attachment_is_image()) {
		printf( __( 'Full size: %s', THEME_NAME), sprintf( '<a href="%1$s" title="%2$s">%3$s &times; %4$s</a>',
				wp_get_attachment_url(),
				esc_attr( __('Link to full-size image', THEME_NAME) ),
				$metadata['width'],
				$metadata['height']
			)
		);
		echo '<br />';
	}
	printf(__('MIME Type: %s', THEME_NAME), get_post_mime_type());
	if(wp_attachment_is_image()) {
		if(!empty($post->post_excerpt)) {
			echo '<br />';
			printf(__('Caption: %s', THEME_NAME), get_the_excerpt());
		}
	}
?>
			</div>
			<div class="entry">
				<?php the_content(); ?>
			</div>
		</div>
		<div class="pagenavi">
<?php if(wp_attachment_is_image()) { ?>
			<span class="previous-page"><?php simpledark_previous_image_link(); ?></span><span class="next-page"><?php simpledark_next_image_link(); ?></span>
<?php } ?>
		</div>
		<div id="reaction">
<?php comments_template('', true); ?>
		</div>
<?php
}
?>
<?php get_footer(); ?>