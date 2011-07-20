<?php
/* Plugin: Multi Analytic Tracker
 * admin_options.php
 * Displays the list of options for the admin panel of WP
 * Users add tracking code to the database and then will be allowed to assign them to specific pages
 * Created July 18, 2011
 */

?>
<?php
    require_once 'database.php';
    $mat = new Database();
    $curURL = get_bloginfo('wpurl').'/wp-admin/options-general.php?page=multi-analytic-tracker';
   
    //Creates the new tracking code if there is a codeTitle and trackingCode POSTed
   if(!empty($_POST['codeTitle']) && !empty($_POST['trackingCode'])){
        $mat->addTrackingCode($_POST['codeTitle'], $_POST['trackingCode']);
    }
    
    //Updates the tracking code
    if(!empty($_POST['existingTrackingCode']) && !empty($_POST['trackingCode']) && !empty($_POST['update']))
       {
            $mat->updateTrackingCode($_POST['existingTrackingCode'], $_POST['trackingCode']);
       }
    //Assigns the tracking code to the pages
    if(!empty($_POST['page']) && !empty($_POST['trackingCode']))
       {
            $mat->assignCodeToPages($_POST);
       }
    
?>


    <div class='wrap'>
        <input type='hidden' name='pluginDir' value="<?php echo get_bloginfo('wpurl').'/wp-admin/admin-ajax.php' ?>" />
         <h2><?php _e('Multi Analytic Tracker Settings'); ?></h2>
        <a href="<?php echo $curURL;?>&action=new"><?php _e("Create new tracking code")?></a><br />
        <a href="<?php echo $curURL;?>&action=edit"><?php _e("Edit existing tracking code")?></a><br />
        <a href="<?php echo $curURL;?>&action=assign"><?php _e('Assign tracking code to pages')?></a>
        
        <?php if($_GET['action']=='edit' || $_GET['action']=='new') :?>
       
       <form name='multi_analytic_form' method='post' action="<?php echo $curURL ?>">
        <p>
            <?php if($_GET['action'] == 'new') : ?>
            <label for='codeTitle'><?php _e('New tracking code tile')?>:</label><br />
            <input type='text' maxlength='60' style='width:220px;' name='codeTitle' id='codeTitle' />
            <?php else: ?>
            <label for='editCodeTitle'><?php _e('Edit existing tracking code title')?>:</label><br />
            <input type='hidden' name='update' value='true' />
            <select name='existingTrackingCode'>
                <option value='null' disabled='disabled' selected='selected'><?php _e('Select a code');?></option>
              <?php foreach($mat->getTrackingCodes() as $trackingCode) : ?>
                    <option value='<?php echo $trackingCode->id?>'> <?php _e($trackingCode->track_title)?></option>
                <?php  endforeach; ?>
                
            </select>
            <?php endif; ?>
        </p>
        <p>
            <label for='trackingCode'><?php _e('Tracking Code')?>:</label><br />
            <textarea cols='80' rows='8' name='trackingCode' id='trackingCode'> </textarea>
        </p>
        
        <input type='submit' value='<?php $_GET['action'] == 'new' ? _e('Create Tracking Code') : _e('Update Tracking Code')?>' />
        <?php endif; ?>
        </form>
        
        <?php
                /* This section displays the pages and tracking codes the user will assign to them */
                if($_GET['action']=='assign') :
        ?>
        <form name='assignCodes' method='post' action="<?php echo $curURL ?>" >
            <div class='contentBox'>
                <strong><?php _e("Select a tracking code you would like to apply to the pages listed below:"); ?></strong><br />
               <?php foreach ($mat->getTrackingCodes() as $trackingCode) : ?>
                    <input type='radio' name='trackingCode' value='<?php echo $trackingCode->id?>' />
                    <?php _e($trackingCode->track_title); ?><br />
               <?php endforeach; ?>
            </div>
            <div class='contentBox'>
                <ul id='pages'>
                <?php
                _e("<strong>Pages</strong><br />");
                $args = array('title_li' => '',
                             'link_before' => "<input type='checkbox' name='page[]' />"
                              );
                wp_list_pages($args);
          ?>
        
                     
                </ul>
                <input type='submit' value='Assign codes' />
            </div>
        
        </form>
        <?php endif; ?>
        
        
        
    </div>