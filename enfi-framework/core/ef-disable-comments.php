<?php

################################################################################################################################################## 
### disable comments
### thanks to https://gist.github.com/mattclements/eab5ef656b2f946c4bfb
##################################################################################################################################################

add_action('admin_init', 'df_disable_comments_post_types_support');
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);
add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);
add_action('admin_menu', 'df_disable_comments_admin_menu');
add_action('admin_init', 'df_disable_comments_dashboard');
add_action('admin_init', 'df_disable_comments_admin_menu_redirect');
add_action('init', 'df_disable_comments_admin_bar');

# remove post type support
function df_disable_comments_post_types_support() {
	$post_types = get_post_types();
	foreach ($post_types as $post_type) {
		if(post_type_supports($post_type, 'comments')) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}
}

function df_disable_comments_status() {
	return false;
}

# Hide existing comments
function df_disable_comments_hide_existing_comments($comments) {
	$comments = array();
	return $comments;
}

# Remove comments page in menu
function df_disable_comments_admin_menu() {
	remove_menu_page('edit-comments.php');
}

# Redirect any user trying to access comments page
function df_disable_comments_admin_menu_redirect() {
	global $pagenow;
	if ($pagenow === 'edit-comments.php') {
		wp_redirect(admin_url()); exit;
	}
}

# Remove comments metabox from dashboard
function df_disable_comments_dashboard() {
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}

# Remove comments links from admin bar
function df_disable_comments_admin_bar() {
	if (is_admin_bar_showing()) {
		remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
	}
}


?>