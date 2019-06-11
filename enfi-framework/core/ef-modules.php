<?php

################################################################################################################################################## 
### module handler
##################################################################################################################################################

$modules_page = new EF_Settings_Page('ef-modules', __('MODULES', 'ef'), __('MODULES', 'ef'), __('MODULES_DESCRIPTION', 'ef'), 'modules', 'fa-puzzle-piece', 9);
$modules_page->addContent('modules_render');

function modules_render() {

    $modules_path_child =  get_stylesheet_directory().'/modules';
    $modules_path =  get_template_directory().'/modules';

    $option = ef_get_option('ef-modules');
    $name = ef_get_child_name('ef-modules');

    echo '<table class="ef-admin-table">';

        echo '<thead><tr>';
            echo '<th class="center">'.__('ENABLE', 'ef').'</th>';
            echo '<th class="left">'.__('NAME', 'ef').'</th>';
            echo '<th class="left">'.__('DESCRIPTION', 'ef').'</th>';
        echo '</tr></thead>';

        
    
    foreach(glob($modules_path.'/*/init.php') as $file){
           
        $filedata = get_file_data($file, array(
            'name' => 'Name',
            'slug' => 'Slug',
            'description' => 'Description',
            'version' => 'Version',
            'author' => 'Author'
        ));   

        if(is_array($option) && $option != null) {
            if(array_key_exists($filedata['slug'], $option)) 
                $checked = checked('true', $option[$filedata['slug']] , false );
            else 
                $checked = null;
        } else {
            $checked = null;
        }     

        echo '<tbody><tr>';
            echo '<td class="center"><input type="checkbox" id="'.$filedata['slug'].'" value="true" name="'.$name.'['.$filedata['slug'].']" '.$checked.'></td>';
            echo '<td>'.$filedata['name'].'</td>';
            echo '<td>'.$filedata['description'].' - '.$filedata['author'].' - '.$filedata['version'].'</td>';
        echo '</tr></tbody>';

    }

    if(is_child_theme()) 
        foreach(glob($modules_path_child.'/*/init.php') as $file){
           
            $filedata = get_file_data($file, array(
                'name' => 'Name',
                'slug' => 'Slug',
                'description' => 'Description',
                'version' => 'Version',
                'author' => 'Author'
            ));   

            if(is_array($option) && $option != null) {
                if(array_key_exists($filedata['slug'], $option)) 
                    $checked = checked('true', $option[$filedata['slug']] , false );
                else 
                    $checked = null;
            } else {
                $checked = null;
            }     

            echo '<tr>';
                echo '<td>'.$filedata['name'].'</td>';
                echo '<td>'.$filedata['description'].'</td>';
                echo '<td><input type="checkbox" id="'.$filedata['slug'].'" value="true" name="ef-pluugs['.$filedata['slug'].']" '.$checked.'><label for="'.$filedata['slug'].'">Aktivieren</label></td>';
            echo '</tr>';

        }

    echo '</table>';
    
}

################################################################################################################################################## 
### load active moduls
##################################################################################################################################################

$active_modules = ef_get_option('ef-modules');

if(is_array($active_modules)) {
    foreach($active_modules as $key => $module) {
        if(file_exists(get_template_directory().'/modules/'.$key.'/init.php'))
            require_once get_template_directory().'/modules/'.$key.'/init.php';
    }
}





?>