<?php
/*
Plugin Name: Multi Analytic Tracker
Plugin URI: http://www.danramosd.com
Description: Gives the ablity to add multiple analytics codes to the site.  The codes can be added by page.
Author: Dan Ramos-Dominko
Version: 1.0
Author URI: http://www.danramosd.com
*/
    /* function multi_admin_actions()
     * Description: Creates the admin menu in the WP backend
     * return void
     */
    function multi_admin_actions() {  
        add_options_page("Multi Analytic Tracker Options", "Multi Analytic Tracker", "manage_options", "multi-analytic-tracker", 'admin_menu_output');  
    }  
     
    /* function admin_menu_output()
     * Description: Loads the actual menu page when the user selects it from the settings menu
     * return void
     */
    function admin_menu_output()
    {
        require_once 'admin_options.php';
    }
    
    /* function initialize()
     * Description: Creates the database structure
     * return void
     */
    function initialize()
    {
        require_once 'initialize.php';
    }
    
    /* function addHeaderContent()
     * Description: Loads the necessary files in header
     * return void
     */
    function addHeaderContent()
    {
        $pluginDir = plugins_url()."/";
        $pluginDir .= substr(strrchr(dirname(__FILE__),DIRECTORY_SEPARATOR),1)."/";
        echo "<script src='//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js' type='text/javascript'></script>
              <script src='".$pluginDir."admin.js' type='text/javascript'></script>
              <link media='screen' href='".$pluginDir."multi.css' type='text/css' rel='stylesheet' > ";
        
    }
    
    /* function: processAJAX()
     * Description: Handles the ajax request from admin.js.  It uses this to populate the textarea with code
     */
    function getCodeContent()
    {
        require_once 'database.php';
        $mat = new Database();
        $result = $mat->getCodeById($_POST['id']);
        echo $result;
        //Needed to return a proper result and removes the 0 from end of string.
        die();
    }
    
    /* function insertCodeOnPage()
     * Description: Injects the proper tracking code on the specified pages
     * return void
     */
    function insertCodeOnPage()
    {
        require_once 'database.php';
        $mat = new Database();
        global $post;
        $codeString = $mat->getCodeForPage($post->ID);
        echo $codeString;
       
    }
    
    /* function getAssignedPagesByCode()
     * Description: Gets the ID's of all the pages that have the selected code assigned to them.  Accessed via AJAX
     * @return void
     */
    function getAssignedPagesByCode()
    {
        require_once 'database.php';
        $mat = new Database();
        $ids = $mat->getAssignedPagesByCode($_POST['id']);
        echo json_encode($ids);
        exit();
        
    }
    
    add_action('wp_ajax_getAssignedPagesByCode', 'getAssignedPagesByCode');
    add_action('wp_ajax_getCodeContent', 'getCodeContent');
    add_action('admin_head', 'addHeaderContent');
    add_action('admin_menu', 'multi_admin_actions');
    add_action('wp_footer', 'insertCodeOnPage');
    //Creates the database table needed when the plugin is activated
    register_activation_hook(__FILE__, 'initialize');
?>