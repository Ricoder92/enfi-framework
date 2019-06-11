<?php

################################################################################################################################################## 
### debug settings page
##################################################################################################################################################

$debug_page = new EF_Settings_Page('ef-debug', __('UPDATE_MAINTENANCE_DEBUG', 'ef'), __('UPDATE_MAINTENANCE_DEBUG', 'ef'), __('UPDATE_MAINTENANCE_DEBUG_DESCRIPTION', 'ef'), 'settings', 'fa-bug', 8);

$debug_page->addSection('debug-settings', __('DEBUG', 'ef'));
$debug_page->addField('debug-settings', 'debug-mode-enable', __('DEBUG_ENABLE', 'ef'), __('DEBUG_ENABLE_DESCRIPTION', 'ef'), 'checkbox', null, array('checkboxText' => __('DEBUG_ENABLE_CHECKBOXTEXT', 'ef')));

$debug_page->addSection('maintenance', __('MAINTENANCE', 'ef'));
$debug_page->addField('maintenance', 'maintenance-enable', __('MAINTENANCE_ENABLE', 'ef'), __('MAINTENANCE_ENABLE_DESCRIPTION', 'ef'), 'checkbox', null, array('checkboxText' => __('MAINTENANCE_ENABLE_CHECKBOXTEXT', 'ef')));
$debug_page->addField('maintenance', 'maintenance-page', __('MAINTENANCE_SITE', 'ef'), __('MAINTENANCE_SITE_DESCRIPTION', 'ef'), 'selection', null, array( 'posts' => 'page'));

$debug_page->addSection('404', __('404_ERROR', 'ef'));
$debug_page->addField('404', '404-page', __('404_ERROR_PAGE', 'ef'), __('404_ERROR_PAGE_DESCRIPTION', 'ef'), 'selection', null, array( 'posts' => 'page'));

$debug_page->addSection('settings', __('UPDATE_CHECK', 'ef'));
$debug_page->addContent('check_for_update_render');

function check_for_update_render() {

    # get current theme version
    $my_theme = wp_get_theme();
    $current_version = $my_theme->get( 'Version' );

    # get aviable theme version
    $json = file_get_contents('http://ricoder.de/ziptest/version.php');
    $data = json_decode($json,true);
    $aviable_version = $data['version'];

    echo '<div class="ef-update-version-check">';

        echo '<div class="version-container"><h2>'.$current_version.'</h2><p>'.__('UPDATE_CURRENT_THEME_VERSION', 'ef').'</p></div>';
        echo '<div class="version-container"><h2>'.$aviable_version.'</h2><p>'.__('UPDATE_AVIABLE_THEME_VERSION', 'ef').'</p></div>';

    echo '</div>';

    echo '<div class="ef-update-version-check">';

        echo '<div class="update-process-container">';

        echo $_POST['update_post'];

        if($current_version < $aviable_version) {
            #echo '<form action="'.admin_url('admin-post.php').'" method="post">';
            #    echo '<input type="hidden" name="action" value="ef_update_form">';
            #    echo '<input type="hidden" name="data" value="true" />';
            #    echo '<input class="btn" type="submit" value="'.__('UPDATE_NOW', 'ef').'"/>';
            #echo '</form>';
        }
        else  {
            echo '<span class="no-update-aviable">'.__('NO_UPDATE_AVIABLE', 'ef').'</span>';
        }

        echo '</div>';

    echo '</div>';

    $is_update = false;

    if($is_update) {
        
        $temp_path = get_template_directory().'/update/update.zip';
        $update_destination = get_template_directory().'/update-test';

        download_safe_update($temp_path);
        unzip_new_update($temp_path, $update_destination);
        remove_orphan_failes(get_files_to_remove());
    }




}



function download_safe_update($temp_path) {
  
    # source
    $source = "http://ricoder.de/ziptest/update/Enfi-Framework-WIP-master.zip"; 

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $source);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec ($ch);
    curl_close ($ch);

    # save file
    $destination = $temp_path; // NEW FILE LOCATION
    $file = fopen($destination, "w+");
    fputs($file, $data);
    fclose($file);
}

function unzip_new_update($temp_path) {
    WP_Filesystem();
    $unzipfile = unzip_file( $temp_path, $update_destination);

    if ( is_wp_error( $unzipfile ) ) {
        echo 'There was an error unzipping the file.'; 
    } else {
        echo 'Successfully unzipped the file!';       
    }
}

function get_current_files($dir = null, &$results = array()){
    
    if($dir == null) 
        $dir = get_template_directory();

    $files = scandir($dir);
    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = str_replace(get_template_directory(), '', $path);
        } else if($value != "." && $value != "..") {
            get_current_files($path, $results, $dir);
            $results[] = str_replace(get_template_directory(), '', $path.'/');
        }
    }
    return $results;
}

function get_files_to_remove($update_files, $current_files) {
    $files_update = $update_files;
    $files_current = $current_files;
    $result = array_diff($files_current, $files_update);
    return $result;
}

function remove_orphan_failes($files_to_remove) {
    foreach($files_to_remove as $file) {
        unlink($file);
    }
}

function get_files_from_update($temp_path) {
    $zip = zip_open($temp_path);
    if (is_resource($zip)) {  
        $files = array();
        while ($zip_entry = zip_read($zip)) {
            array_push($files, zip_entry_name($zip_entry));
        }
    zip_close($zip);
    }

    return $files;
}


################################################################################################################################################## 
### set states for pages in dashboard
##################################################################################################################################################

function enfi_filter_post_state_404( $post_states, $post ) {
    
    $option = ef_get_option('ef-debug');
    
    $option = $option['404-page'];
    
	if( $post->ID == $option) {
        $post_states[] = __('404 Error page', 'ef');
    }
    
	return $post_states;
}
add_filter( 'display_post_states', 'enfi_filter_post_state_404', 10, 2 );

################################################################################################################################################## 
### set states for pages in dashboard
##################################################################################################################################################

function enfi_filter_post_state_maintenance( $post_states, $post ) {
    
    $option = ef_get_option('ef-debug');
    
    $option = $option['maintenance-page'];
    
	if( $post->ID == $option) {
        $post_states[] = __('Maintenance', 'ef');
    }
    
	return $post_states;
}
add_filter( 'display_post_states', 'enfi_filter_post_state_maintenance', 10, 2 );

################################################################################################################################################## 
### maintenance loop
##################################################################################################################################################

function enfi_maintenance_maintenance_mode() {

    $option = ef_get_option('ef-debug');

    if(isset($option['maintenance-enable']))
        $enable = true;
    else   
        $enable = false;

    #if maintenance is enable
    if($enable) {

        global $pagenow;

        if ( $pagenow !== 'wp-login.php' && ! current_user_can( 'manage_options' ) && ! is_admin() ) {
       
            header( $_SERVER["SERVER_PROTOCOL"] . ' 503 Service Temporarily Unavailable', true, 503 );
            header( 'Content-Type: text/html; charset=utf-8' );

            get_template_part('maintenance');

            die();
        }
    }
}
add_action( 'parse_query', 'enfi_maintenance_maintenance_mode' );

################################################################################################################################################## 
### if is debug mode
##################################################################################################################################################

function ef_is_debug_mode() {

    #$option = ef_get_option('ef-debug');

    return true;

}


?>