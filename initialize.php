<?php
/* initialize.php
 * Description: Takes care of the CRUD operations for the plugin's database
 * Created:July 18, 2011
 */


    global $wpdb;
    $table_name = $wpdb->prefix.'multi_tracker';
    
    if($wpdb->get_var("SHOW TABLES LIKE '%table_name%'") != $table_name){
        
        $sql = "CREATE TABLE ".$table_name." (
                `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
                `track_title` VARCHAR( 120 ) NOT NULL ,
                `track_code` TEXT NOT NULL ,
                `pages` VARCHAR( 500 ) NOT NULL ,
                UNIQUE KEY id (id)
                )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
    }