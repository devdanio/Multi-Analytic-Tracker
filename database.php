<?php
/* File: database.php
 * Description: Gets the submitted form data from admin_options.php and updates the database.
 * Created:  July 18, 2011
 */
?>
<?php
   
    class Database{
       
        private $tableName;
        function __construct(){
            global $wpdb;
            $this->tableName = $wpdb->prefix.'multi_tracker';
              
        }
        
        /* function addTrackingCode()
         * Description: Creates a new tracking code in the database
         * @param string $trackTitle - The name of the tracking code
         * @param string $trackCode - The actual code being stored
         * @return void
         */
        public function addTrackingCode($trackTitle, $trackCode){
            global $wpdb;
            $insertData = array('track_title' => $trackTitle,
                                'track_code'  => $trackCode
                                );
                        
            $wpdb->insert($this->tableName, $insertData);
           
        }
        
        /* function getTrackingCodes
         * Description: Gets all of the tracking codes in the database
         * return database Objects
         */
        public function getTrackingCodes(){
            global $wpdb;
            $trackingCodes = $wpdb->get_results("SELECT * from ".$this->tableName);
            return $trackingCodes;
        }
        
        /* function getCodeById
         * Description: Gets the tracking code (invoked by AJAX), based on the db ID
         * @param int codeID
         * return string
         */
        function getCodeById($codeID){
            global $wpdb;
            $result = $wpdb->get_row("SELECT * FROM ".$this->tableName." WHERE id = ".$codeID);
            return htmlentities(stripcslashes($result->track_code))  ;
            
        }
        
        /* function updateTrackingCode
         * Description: Updates the content of a tracking code.
         * @param int $codeID - the ID of the code in the database
         * @param string $trackCode - the code that will be updated
         * return void
         */
        public function updateTrackingCode($codeID, $trackCode){
            global $wpdb;
            $wpdb->escape($codeID);
            $wpdb->escape($trackCode);
            $result = $wpdb->update($this->tableName, array('track_code' => $trackCode), array("ID" =>$codeID));   
            
        }
        
        /* function assignCodeToPages
         * Description: Assigns a tracking code to a list of pages and stores it in the DB as a serialized array
         * @param array $assignment
         * @return void
         */
        public function assignCodeToPages($assignment){
            global $wpdb;
            $trackID = filter_var($assignment['trackingCode'], FILTER_VALIDATE_INT);
            $assign = serialize($assignment['page']);
            $assign = htmlentities($assign, ENT_QUOTES);
            $wpdb->update($this->tableName,array('pages' => $assign), array('ID' => $trackID));
        }
        
        
        /* function parseSerial()
         * Description: Takes a serialized string, decodes it, and returns it as an array
         * @param string $serial - A serialized string
         * @return array - An array representation of the string
         */
        private static function parseSerial($serial){
            $arrayString = html_entity_decode($serial,ENT_QUOTES);
            $array=unserialize($arrayString);
            return $array;
        }
        
        
        /* function getCodeForPage()
         * Description: Looks for all of the codes that belong on the page based on its ID.
         * @param int $pageID - The id of the page that corresponds to the one in the database
         * @return string - All of the tracking codes for the page with out the slashes
         */
        public function getCodeForPage($pageID){
            global $wpdb;
            $rows = $wpdb->get_results("SELECT pages, track_code FROM ".$this->tableName);
            $codeString = '';
           
            /* Loops through all of the rows in the table
             * Checks to make sure the pages column has some data in it
             * If it has data we decode it and unserialize it to get its true array form
             * Search for the current $pageID in the array and push its track_code content if we find it
             */
            foreach($rows as $row)
            {
               if(!empty($row->pages)){
                    $array = self::parseSerial($row->pages);
                    if(in_array($pageID, $array)){
                        $codeString .= $row->track_code;
                    }
                }
            }
            return stripcslashes($codeString);
        }
        
        /* function getAssignedPagesByCode()
         * Description: Looks up all the page IDs associated with the given code
         * @param int $ID
         * return array
         */
        public function getAssignedPagesByCode($ID){
            global $wpdb;
            filter_var($ID, FILTER_VALIDATE_INT);
            $sql = 'SELECT pages FROM '.$this->tableName.' WHERE id = '.$ID;
            $pageIDs = $wpdb->get_row($sql);
            $result = self::parseSerial($pageIDs->pages);
            return $result;
        }
    }
?>