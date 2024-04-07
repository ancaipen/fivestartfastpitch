<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Request\Request;
use Joomla\CMS\Filter\InputFilter;


jimport( 'joomla.application.component.model' );

class tsAdminModeltournament_cost extends JModelLegacy
{

    public static function setAllTournamentcostToolbar()
    {
        JToolBarHelper::title('Tournament Detail By Age', 'generic.png');
        JToolBarHelper::deleteList();
        JToolBarHelper::editList();
        JToolBarHelper::addNew();
    }

    public static function setTournamentcostToolbar($id)
    {
        if ($id) {
                $newEdit = 'Edit';
        } else {
                $newEdit = 'New';
        }

        JToolBarHelper::title($newEdit . ' Tournament Detail By Age', 'generic.png');
        JToolBarHelper::save();
        JToolBarHelper::apply();
        JToolBarHelper::cancel();

    }

    //--------------------------------------
    // send email
    //--------------------------------------

    public static function sendemail_tournament_cost($email_from, $email_from_name, $email_to)
    {

        $success = 0;

        //check for post
        if($_SERVER["REQUEST_METHOD"] == 'POST')
        {

            //get post values
            $input = Factory::getApplication()->input;            
            $post = $input->getArray($_POST);
            //$post = JRequest::get('post');

            //declare all local variables
            $tournament_cost_id = -1;
            $tournament_id = -1;
            $age_id = -1;
            $tournament_cost = "";
            $tournament_results = "";

            if(isset($post))
            {

                //set all variables to post values
                if(isset($post['tournament_cost_id']))
                { $tournament_cost_id = filter_var(trim($post['tournament_cost_id'])); }
                if(isset($post['tournament_id']))
                { $tournament_id = filter_var(trim($post['tournament_id'])); }
                if(isset($post['age_id']))
                { $age_id = filter_var(trim($post['age_id'])); }
                if(isset($post['tournament_cost']))
                { $tournament_cost = filter_var(trim($post['tournament_cost'])); }
                if(isset($post['tournament_results']))
                { $tournament_results = filter_var(trim($post['tournament_results'])); }
                if(isset($post['field_location_description']))
                { $field_location_description = filter_var(trim($post['field_location_description'])); }

                //build email message
                $e_html = "<div style='font-family: arial;'>";
                $e_html .= "<h1>Tournament - ".date("m.d.y")."</h1>";
                $e_html .= "<table cellpadding='5' cellspacing='0'>";
                if($tournament_id >= 0)
                { $e_html .= "<tr><td>Tournament Id:</td><td>" . $tournament_id . "</td></tr>"; }
                if($age_id >= 0)
                { $e_html .= "<tr><td>Age Id:</td><td>" . $age_id . "</td></tr>"; }
                if(isset($tournament_cost))
                { $e_html .= "<tr><td>Tournament Cost:</td><td>" . $tournament_cost . "</td></tr>"; }
                if(isset($tournament_results))
                { $e_html .= "<tr><td>Tournament Results:</td><td>" . $tournament_results . "</td></tr>"; }
                $e_html .= "</table></div>";

                $subject = "Tournament - " . date("m-d-y");
                $success = JUtility::sendMail($email_from, $email_from_name, $email_to, $subject, $e_html, true);

            }

        }

        return $success;

    }

    //--------------------------------------
    // data access
    //--------------------------------------

    //save record (insert or update)
    public static function save_tournament_cost($cid,$post)
    {

        $error_msg = "";

        //declare all local variables
        $tournament_cost_id = -1;
        $tournament_id = -1;
        $age_id = -1;
        $tournament_cost = "";
        $tournament_results = "";
        $field_location_description = "";
		$tourn_capacity = 0;
		
        if(isset($post))
        {
            //set all variables to post values
            if(isset($post['tournament_cost_id']))
            { $tournament_cost_id = filter_var(trim($post['tournament_cost_id'])); }
            if(isset($post['tournament_id']))
            { $tournament_id = filter_var(trim($post['tournament_id'])); }
            if(isset($post['age_id']))
            { $age_id = filter_var(trim($post['age_id'])); }
			if(isset($post['tourn_capacity']))
            { $tourn_capacity = filter_var(trim($post['tourn_capacity'])); }
            if(isset($post['tournament_cost']))
            { 
                $filter = new InputFilter;
                $tournament_cost = $filter->clean($post['tournament_cost'], 'string');
                //$tournament_cost = filter_var(trim($post['tournament_cost']), FILTER_SANITIZE_STRING); 
            }
            if(isset($post['tournament_results']))
            { 
                $input = Factory::getApplication()->input;
                $tournament_results = $input->get('tournament_results');
                //$post['tournament_results']=JRequest::getVar( 'tournament_results', '', 'post', 'string', JREQUEST_ALLOWHTML );
                $post['tournament_results'] = preg_replace("/'/", "\&#39;", $post['tournament_results']);
                $tournament_results = $post['tournament_results'];
            }
            if(isset($post['field_location_description']))
            { 
                $input = Factory::getApplication()->input;
                $fieldLocationDescription = $input->get('field_location_description');
                //$post['field_location_description']=JRequest::getVar( 'field_location_description', '', 'post', 'string', JREQUEST_ALLOWHTML );
                $post['field_location_description'] = preg_replace("/'/", "\&#39;", $post['field_location_description']);
                $field_location_description = $post['field_location_description'];
            }

            //validate all variables server side
            //tournament id required
            if($tournament_id <= 0)
            { $error_msg .= "<div class='message_error'>Tournament Id is Required.</div>"; }
            //age id required
            if($age_id <= 0)
            { $error_msg .= "<div class='message_error'>Age Id is Required.</div>"; }
        }
        else
        {
            $error_msg .= "<div class='message_error'>An error has occurred, please try submission again.</div>";
        }

        //insert or update record
        if($error_msg == "")
        {
            if(isset($cid[0]) && trim($tournament_cost_id) != -1)
            {
                //update mode
                $success = tsAdminModeltournament_cost::update_tournament_cost($tournament_cost_id,
                $tournament_id,
                $age_id,
                $tournament_cost,
                $tournament_results,
                $field_location_description,
				$tourn_capacity);

                //add error message if update was not a success
                if($success != true)
                {
                    $error_msg .= "<div class='message_error'>An error has occurred while updating the record, please try submission again.</div>";
                }
            }
            else
            {
                //insert mode
                $rows = tsAdminModeltournament_cost::insert_tournament_cost($tournament_id,
                $age_id,
                $tournament_cost,
                $tournament_results,
                $field_location_description,
				$tourn_capacity);

                $row_count = 0;
                $row_count = count($rows);

                //add error message if no data is returned
                if($row_count == 0)
                {
                    $error_msg .= "<div class='message_error'>An error has occurred while adding record, please try submission again.</div>";
                }
            }

            //save file uploads
            if($error_msg == "")
            {
                //delete removed file
                if(isset($post['file_delete']))
                {
                    if($post['file_delete'] != "0")
                    {
                        tsAdminModeltournament_cost::delete_file($post['file_delete']);
                    }
                }
                //save new files
                tsAdminModeltournament_cost::save_tournament_files($post,$age_id,$tournament_id);
            }
        }

        return $error_msg;

    }

    //insert record
    public static function insert_tournament_cost($tournament_id,
    $age_id,
    $tournament_cost = '',
    $tournament_results = '',
    $field_location_description = '',
	$tourn_capacity = '')
    {
        
        $tournament_id = filter_var(trim($tournament_id));
        $age_id = filter_var(trim($age_id));
        
        //create sql query
        $query = 'INSERT INTO jos_ts_tournament_age_cost (';
		$query .= 'tournament_id,';
		$query .= 'age_id,';
		if(trim($tournament_cost) != '') {
			$query .= 'tournament_cost,';
		}
		if(trim($tournament_results) != '') {
			$query .= 'tournament_results,';
		}
		if(trim($field_location_description) != '') {
			$query .= 'field_location_description,';
		}
		if(trim($tourn_capacity) != '') {
			$query .= 'tourn_capacity,';
		}
		if(strlen($query) > 0)
		{ $query = substr($query, 0, (strlen($query) - 1)); }
		$query .= ') ';

		$query .= ' VALUES (';
		$query .= "'".($tournament_id)."',";
		$query .= "'".($age_id)."',";
		if(trim($tournament_cost) != '') {
			$query .= "'".($tournament_cost)."',";
		}
		if(trim($tournament_results) != '') {
			$query .= "'".($tournament_results)."',";
		}
		if(trim($field_location_description) != '') {
			$query .= "'".($field_location_description)."',";
		}
		if(trim($tourn_capacity) != '') {
			$query .= "'".($tourn_capacity)."',";
		}
		if(strlen($query) > 0)
		{ $query = substr($query, 0, (strlen($query) - 1)); }
		$query .= ') ';

        $db = Factory::getDBO();
        $db->setQuery($query);
        $result = $db->execute();

        // if ($db->getErrorMsg() != ""){
        //     $message = "DB Error: ";
        //     $message .= $db->getErrorMsg();
        //     $message .= 'Whole query: ' . $query;
        //     $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
        //     echo $message;
        // }

        $query = "SELECT * FROM jos_ts_tournament_age_cost
        WHERE tournament_cost_id=LAST_INSERT_ID()";
        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        return $rows;

    }

    public static function display_files($age_id,$tournament_id)
    {
        $html = '';

        $mime_types = array(
        'image/png',
        'image/jpeg',
        'image/jpeg',
        'image/jpeg',
        'image/gif',
        'image/tiff',
        'image/tiff'
        );

        $query = "select * FROM jos_ts_files
        WHERE tournament_id=".($tournament_id)." AND age_id=".($age_id)."
        ORDER BY date_created DESC";

        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $rowcount = count($rows);

        $upload_dir = '/images/stories/tournaments/'.$tournament_id.'/';

        if($rowcount > 0)
        {

            $html .= '<div class="files_container">';
            $html .= '<table cellpadding="3" cellspacing="0">';

            foreach ($rows as $row)
            {

                $file_text = $row->file_name;
                if(trim($row->file_desc) != '')
                {
                    $file_text = $row->file_desc;
                }

                $filedelete_id = "'".$row->files_id."'";
                if (in_array($row->file_mime, $mime_types))
                {
                    $html .= '<tr><td><img src="'.$upload_dir.$row->file_name.'" width="50" />&nbsp;'.$file_text.'</td><td><a href="javascript:void(0);" class="btn_delete" OnClick="delete_file('.$filedelete_id.');">Delete</a></td></tr>';
                }
                else
                {
                    $html .= '<tr><td><a href="'.$upload_dir.$row->file_name.'" target="_blank">'.$file_text.'</a></td><td><a href="javascript:void(0);" class="btn_delete" OnClick="delete_file('.$filedelete_id.');">Delete</a></td></tr>';
                }
            }
            $html .= '</table>';
            $html .= '</div>';
        }

        return $html;
    }

    public static function save_tournament_files($post,$age_id,$tournament_id)
    {
        if(isset($_FILES))
        {
            foreach ($_FILES as $_key => $_value)
            {
                if(trim($_value['name']) != '')
                {

                    $upload_dir = addslashes(JPATH_SITE.'/images/stories/tournaments/'.$tournament_id.'/');

                    //create directory if needed
                    mkdir($upload_dir, 0755);

                    //check file type
                    $mime_types = array(
                    'text/plain',

                    // images
                    'image/png',
                    'image/jpeg',
                    'image/jpeg',
                    'image/jpeg',
                    'image/gif',
                    'image/tiff',
                    'image/tiff',

                    // archives
                    'application/zip',
                    'application/x-rar-compressed',

                    // adobe
                    'application/pdf',

                    // ms office
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/rtf',
                    'application/vnd.ms-excel',
                    'application/vnd.ms-powerpoint'

                    );

                    //save files and insert record into database
                    if (in_array($_FILES[$_key]["type"], $mime_types) &&
                    ($_FILES[$_key]["size"] < 25000000))
                    {
                        $ext = end(explode('.', $_FILES[$_key]["name"]));
                        $file_name = $today = date("Hisu").substr((string)microtime(), 1, 8).'_'.$tournament_id.'_'.$age_id.'.'.$ext;
                        $result = move_uploaded_file($_FILES[$_key]["tmp_name"], $upload_dir.$file_name);
                        if (!$result)
                        {
                            //error here
                        }
                        else
                        {
                            //save file information to database
                            $file_md5 = md5_file($upload_dir.$file_name);

                            //attempt to find file desc
                            $file_desc = '';
                            $sub = str_replace("file_", "", $_key);
                            if(isset($post["filedesc_".$sub]))
                            {
                                if($post["filedesc_".$sub] != '')
                                {
                                    $file_desc = $post["filedesc_".$sub];
                                }
                            }

                            $query = "INSERT INTO jos_ts_files (file_name,file_path,file_desc,file_md5,file_mime,tournament_id,age_id,date_created)
                            VALUES ('".($file_name)."',
                            '".($upload_dir)."',
                            '".($file_desc)."',
                            '".($file_md5)."',
                            '".($_FILES[$_key]["type"])."',
                            '".($tournament_id)."',
                            '".($age_id)."',
                            current_timestamp) ";

                            //insert to tournament_age_cost table
                            $db = Factory::getDBO();
                            $db->setQuery($query);
                            $result = $db->execute();

                            if ($db->getErrorMsg() != ""){
                                $message = "DB Error: ";
                                $message .= $db->getErrorMsg();
                                $message .= 'Whole query: ' . $query2;
                                $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
                                echo $message;
                            }
                        }
                    }

                }
            }
        }

    }

    public static function delete_file($files_id)
    {
        
        $files_id = filter_var(trim($files_id));
                
        $query = "DELETE FROM jos_ts_files
        WHERE files_id = ".($files_id);

        $db = Factory::getDBO();
        $db->setQuery($query);
        $result = $db->execute();

        if ($db->getErrorMsg() != ""){
            $message = "DB Error: ";
            $message .= $db->getErrorMsg();
            $message .= 'Whole query: ' . $query2;
            $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
            echo $message;
        }
    }

    //update record
    public static function update_tournament_cost($tournament_cost_id,
    $tournament_id,
    $age_id,
    $tournament_cost = '',
    $tournament_results = '',
    $field_location_description = '',
	$tourn_capacity = '')
    {
        
        $tournament_id = filter_var(trim($tournament_id));
        $age_id = filter_var(trim($age_id));
        
        //create sql query
        $query = 'UPDATE jos_ts_tournament_age_cost SET ';
		$query .= "tournament_id='".($tournament_id)."',";
		$query .= "age_id='".($age_id)."',";
		if(trim($tournament_cost) != '') {
			$query .= "tournament_cost='".($tournament_cost)."',";
		}
		if(trim($tournament_results) != '') {
			$query .= "tournament_results='".($tournament_results)."',";
		}
		if(trim($field_location_description) != '') {
			$query .= "field_location_description='".($field_location_description)."',";
		}
		if(trim($tourn_capacity) != '') {
			$query .= "tourn_capacity=".$tourn_capacity.",";
		}
		if(strlen($query) > 0)
		{ $query = substr($query, 0, (strlen($query) - 1)); }
		$query .= " WHERE tournament_cost_id='".($tournament_cost_id)."'";

        $db = Factory::getDBO();
        $db->setQuery($query);
        $result = $db->execute();

        $success = false;

        // if ($db->getErrorMsg() != ""){
        //     $message = "DB Error: ";
        //     $message .= $db->getErrorMsg();
        //     $message .= 'Whole query: ' . $query;
        //     $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
        //     echo $message;
        // }
        // else
        // {
        //     $success = true;
        // }
        // return $success;

    }

    //delete record
    public static function delete_tournament_cost($tournament_cost_id)
    {
        $tournament_cost_id = filter_var(trim($tournament_cost_id));
        
        $query = "DELETE FROM jos_ts_tournament_age_cost
        WHERE tournament_cost_id = ".($tournament_cost_id);

        $success = false;

        $db = Factory::getDBO();
        $db->setQuery($query);
        $result = $db->execute();

        // if ($db->getErrorMsg() != ""){
        //     $message = "DB Error: ";
        //     $message .= $db->getErrorMsg();
        //     $message .= 'Whole query: ' . $query;
        //     $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
        //     echo $message;
        // }
        // else
        // {
        //     $success = true;
        // }

        // return $success;

    }

    //select record
    public static function select_tournament_cost($tournament_cost_id = '',$tournament_id = '',$age_id='')
    {

        $tournament_cost_id = InputFilter::getInstance()->clean(trim($tournament_cost_id), 'STRING');
        //$tournament_cost_id = filter_var(trim($tournament_cost_id), FILTER_SANITIZE_STRING);
        $tournament_id = InputFilter::getInstance()->clean($tournament_id, 'STRING');
        //$tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_STRING);
        $age_id = InputFilter::getInstance()->clean(trim($age_id), 'STRING');
        //$age_id = filter_var(trim($age_id), FILTER_SANITIZE_STRING);
        
        $query = "SELECT * FROM jos_ts_tournament_age_cost ac ";
        $query .= "INNER JOIN jos_ts_tournament t on t.tournament_id=ac.tournament_id ";
        $query .= "INNER JOIN jos_ts_age a on a.age_id=ac.age_id ";
        $query .= "INNER JOIN jos_ts_season s on s.season_id=t.season_id ";
        $query .= "WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) ";
        $query .= "AND s.season_current = 1 ";

        if(trim($tournament_cost_id) != "")
        {
            $query .= "AND ac.tournament_cost_id = '".($tournament_cost_id)."' ";
        }

        if(trim($tournament_id) != "")
        {
            $query .= "AND ac.tournament_id = '".($tournament_id)."' ";
        }

        if(trim($age_id) != "")
        {
            $query .= "AND a.age_id = '".($age_id)."' ";
        }

        $query .= "ORDER BY t.tournament_name, a.age_num ";

        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        return $rows;
    }

    //select name record
    public static function selectname_tournament_cost($tournament_cost_id)
    {
        $tournament_cost_id = filter_var(trim($tournament_cost_id));
                
        $name = "";
        $query = "SELECT * FROM jos_ts_tournament_age_cost j ";
        $query .= "WHERE j.tournament_cost_id IS NOT NULL ";
        $query .= "AND j.tournament_cost_id = '".($tournament_cost_id)."' ";
        $query .= "ORDER BY j.tournament_id";

        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        foreach ($rows as $row)
        {
            $name = $row->tournament_id;
        }

        return $name;
    }

    //populate dropdown
    public static function dropdown_tournament($tournament_id_selected, $add_all = false)
    {

        $html = '<select name="tournament_id" id="tournament_id">';
        $query = "SELECT * FROM jos_ts_tournament j ";
        $query .= "INNER JOIN jos_ts_season s ON s.season_id = j.season_id ";
        $query .= "WHERE j.tournament_id IS NOT NULL ";
        $query .= "AND s.season_current = 1 ";
        $query .= "AND j.is_deleted = 0 ";
        $query .= "ORDER BY tournament_name";

        //if all selection is needed all it
        if($add_all)
        {
            $html = $html. '  <option value="-1">ALL</option>';
        }

        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        foreach ($rows as $row)
        {
            if ($tournament_id_selected == $row->tournament_id)
            {
                $html = $html. '  <option value="'.$row->tournament_id.'" SELECTED>'.$row->tournament_name.'</option>';
            }
            else
            {
                $html = $html. '  <option value="'.$row->tournament_id.'">'.$row->tournament_name.'</option>';
            }
        }

        $html = $html .'</select>';
        return $html;

    }

    public static function dropdown_age($age_id_selected, $add_all = false)
    {

        $html = '<select name="age_id" id="age_id">';
        $query = "SELECT * FROM jos_ts_age j ";
        $query .= "WHERE j.age_id IS NOT NULL ";
        $query .= "ORDER BY age_num";

        //if all selection is needed all it
        if($add_all)
        {
            $html = $html. '  <option value="-1">ALL</option>';
        }

        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        foreach ($rows as $row)
        {
            if ($age_id_selected == $row->age_id)
            {
                $html = $html. '  <option value="'.$row->age_id.'" SELECTED>'.$row->age.'</option>';
            }
            else
            {
                $html = $html. '  <option value="'.$row->age_id.'">'.$row->age.'</option>';
            }
        }

        $html = $html .'</select>';
        return $html;

    }

    //populate dropdown
    public static function dropdown_tournament_cost($tournament_cost_id_selected, $add_all = false)
    {

        $html = '<select name="tournament_cost_id" id="tournament_cost_id">';
        $query = "SELECT * FROM jos_ts_tournament_age_cost j ";
        $query .= "WHERE j.tournament_cost_id IS NOT NULL ";
        $query .= "ORDER BY tournament_id";

        //if all selection is needed all it
        if($add_all)
        {
            $html = $html. '  <option value="-1">ALL</option>';
        }

        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        foreach ($rows as $row)
        {
            if ($tournament_cost_id_selected == $row->tournament_cost_id)
            {
                $html = $html. '  <option value="'.$row->tournament_cost_id.'" SELECTED>'.$row->tournament_id.'</option>';
            }
            else
            {
                $html = $html. '  <option value="'.$row->tournament_cost_id.'">'.$row->tournament_id.'</option>';
            }
        }

        $html = $html .'</select>';
        return $html;

    }

}

?>