$(document).ready(function() {
    /* The location the AJAX calls should be POSTed to */
    var postTo = $('input[name=pluginDir]').val();
    
    /* Uses and AJAX call to get the content of the code when the user tries to edit an existing code */
    $('select[name=existingTrackingCode]').change(function()
    {
       $.post(postTo,
              {
                action: 'getCodeContent',
                'id' : $(this).val()
              }, function(data) {
          $('#trackingCode').html(data);
        });
                                                    
    });
    
   /* This is a hack to modify the wp_list_pages way of displaying items
    * Loops through all of the pages (via their <a> tag), gets its content and removes the surrounding <a>
    * It also gets the ID of each item from their class which is page-item-#
    * Adds check boxes before all of the pages with their corresponding ID
    */
    $('#pages a').each(function(){
        var classId = $(this).parent().attr('class');
        var id = classId.split('page-item-');
        $(this).children('input[type=checkbox]').attr('value', id[1]);
        var s = $(this).clone().html();
        $(this).replaceWith(s);
    });
    
    /* Updates the checkboxes for the pages everytime a new tracking code is selected.
     * Posts the data via AJAX sending the tracking code id and gets all it's pages in a JSON format
     * Loops through each JSON result and sets its page value to checked if found
     */
    $('input[name=trackingCode]').change(function(){
            //Removes any previously checked checkboxes
            $('#pages input:checkbox').removeAttr('checked');
            $.post(postTo,
              {
                action: 'getAssignedPagesByCode',
                'id' : $(this).val()
              }, function(data) {
              var ids = $.parseJSON(data);
              $(ids).each(function(k, v){
                  $('#pages input:checkbox[value='+v+']').attr('checked','checked');
                  console.log(v);                                      
                })
              
            });     

        });

});