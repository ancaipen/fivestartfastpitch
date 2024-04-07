<?php


defined('_JEXEC') or die('Restricted access');
require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

class mod_ts_shirts
{

    //------------------------------------------------------
    // EMAIL
    //------------------------------------------------------
    
    public static function sendemail_shirt($email_from, $email_from_name, $email_to)
    {
        
        $success = 0;
        
        //check for post
        if($_SERVER["REQUEST_METHOD"] == 'POST')
        {
        
            //get post values
            $post = JRequest::get('post');
        
            //declare all local variables
            $shirt_id = -1;
            $tournament_id = -1;
            $shirt_img = "";
            $shirt_href = "";
            
            if(isset($post))
            {
            
                //set all variables to post values
                if(isset($post['shirt_id']))
                { $shirt_id = filter_var(trim($post['shirt_id']), FILTER_VALIDATE_INT); }
                if(isset($post['tournament_id']))
                { $tournament_id = filter_var(trim($post['tournament_id']), FILTER_VALIDATE_INT); }
                if(isset($post['shirt_img']))
                { $shirt_img = filter_var(trim($post['shirt_img']), FILTER_SANITIZE_STRING); }
                if(isset($post['shirt_href']))
                { $shirt_href = filter_var(trim($post['shirt_href']), FILTER_SANITIZE_STRING); }
                if(isset($post['shirt_desc']))
                { $shirt_desc = filter_var(trim($post['shirt_desc']), FILTER_SANITIZE_STRING); }
                
                //build email message
                $e_html = "<div style='font-family: arial;'>";
                $e_html .= "<h1>Ohio Baseball TShirts - ".date("m.d.y")."</h1>";
                $e_html .= "<table cellpadding='5' cellspacing='0'>";
                if($tournament_id >= 0)
                { $e_html .= "<tr><td>Tournament Id:</td><td>" . $tournament_id . "</td></tr>"; }
                if(isset($shirt_img))
                { $e_html .= "<tr><td>Shirt Img:</td><td>" . $shirt_img . "</td></tr>"; }
                if(isset($shirt_href))
                { $e_html .= "<tr><td>Shirt Href:</td><td>" . $shirt_href . "</td></tr>"; }
                $e_html .= "</table></div>";
                
                $subject = "Ohio Baseball TShirts - " . date("m-d-y");
                $success = JUtility::sendMail($email_from, $email_from_name, $email_to, $subject, $e_html, true);
                
            }
        
        }
        
        return $success;
        
    }
    
    //------------------------------------------------------
    // DATA ACCESS
    //------------------------------------------------------
    
    public static function get_shirt_html($tournament_id)
    {
        $html = "";
        $rows = mod_ts_shirts::select_shirt('',$tournament_id);
        $rows_count = count($rows);
        
        if($rows_count > 0)
        {
            $html = '<div class="tournament_shirts_container">';
            foreach($rows as $row)
            {
                if(trim($row->shirt_href) != "")
                {
                    $html .= '<a href="'.$row->shirt_href.'" class="tournament_shirt_link">';
                    $html .= '<img src="modules/mod_ts_shirts/images/'.$row->shirt_img.'" />';
                    $html .= '</a>';
                }
                else
                {
                    $html .= '<img src="modules/mod_ts_shirts/images/'.$row->shirt_img.'" />';
                }
            }
            $html .= '</div>';
        }
        
        return $html;
    }
    
    //controller save call
    public static function save_shirt()
    {
    
        $post = JRequest::get('post');
        $error_msg = "";
        
        //declare all local variables
        $shirt_id = -1;
        $tournament_id = -1;
        $shirt_img = "";
        $shirt_href = "";
        $shirt_desc = "";
        
        if(isset($post))
        {
            
            //set all variables to post values
            if(isset($post['shirt_id']))
            { $shirt_id = filter_var(trim($post['shirt_id']), FILTER_VALIDATE_INT); }
            if(isset($post['tournament_id']))
            { $tournament_id = filter_var(trim($post['tournament_id']), FILTER_VALIDATE_INT); }
            if(isset($post['shirt_img']))
            { $shirt_img = filter_var(trim($post['shirt_img']), FILTER_SANITIZE_STRING); }
            if(isset($post['shirt_href']))
            { $shirt_href = filter_var(trim($post['shirt_href']), FILTER_SANITIZE_STRING); }
            if(isset($post['shirt_desc']))
            { $shirt_desc = filter_var(trim($post['shirt_desc']), FILTER_SANITIZE_STRING); }
            
            //validate all variables server side
            //tournament id required
            if($tournament_id <= 0)
            { $error_msg .= "<div class='message_error'>Tournament Id is Required.</div>"; }
            //shirt img required
            if(trim($shirt_img) == "")
            { $error_msg .= "<div class='message_error'>Shirt Img is Required.</div>"; }
            //shirt href required
            if(trim($shirt_href) == "")
            { $error_msg .= "<div class='message_error'>Shirt Href is Required.</div>"; }
        }
        else
        {
            $error_msg .= "<div class='message_error'>An error has occurred, please try submission again.</div>";
        }
        
        //insert or update record  
        if($error_msg == "")
        {
            if(trim($shirt_id) != -1)
            {
                //update mode
                $success = mod_ts_shirts::update_shirt($shirt_id,
                	$tournament_id,
	$shirt_img,
	$shirt_href,
	$shirt_desc);
                
                //redirect back to landing page
                if($success != true)
                {
                    $error_msg .= "<div class='message_error'>An error has occurred while updating the record, please try submission again.</div>";
                }    
            }
            else
            {
                //insert mode
                $rows = mod_ts_shirts::insert_shirt(	$tournament_id,
	$shirt_img,
	$shirt_href,
	$shirt_desc);
                
                $row_count = 0;
                $row_count = count($rows);
                
                if($row_count == 0)
                {
                    $error_msg .= "<div class='message_error'>An error has occurred while adding record, please try submission again.</div>";
                }
            }
        }
        
        //set error session
        if($error_msg != "")
        {
            $_SESSION['error_msg'] = $error_msg;
        }
        
        return $error_msg;
        
    }
    
    //insert record
    public static function insert_shirt($tournament_id,
    $shirt_img,
    $shirt_href,
    $shirt_desc = '')
    {
        
        //create sql query
        	$query = 'INSERT INTO jos_ts_shirts (';
	$query .= 'tournament_id,';
	$query .= 'shirt_img,';
	$query .= 'shirt_href,';
	if(trim($shirt_desc) != '') {
		$query .= 'shirt_desc,';
	}
	if(strlen($query) > 0)
 	{ $query = substr($query, 0, (strlen($query) - 1)); }
	$query .= ') ';

	$query .= ' VALUES (';
	$query .= "'".($tournament_id)."',";
	$query .= "'".($shirt_img)."',";
	$query .= "'".($shirt_href)."',";
	if(trim($shirt_desc) != '') {
		$query .= "'".($shirt_desc)."',";
	}
	if(strlen($query) > 0)
 	{ $query = substr($query, 0, (strlen($query) - 1)); }
	$query .= ') ';


        
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $result = $db->query();
        
        if ($db->getErrorMsg() != ""){
            $message = "DB Error: ";
            $message .= $db->getErrorMsg();
            $message .= 'Whole query: ' . $query;
            $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
            echo $message;
        }
        
        $query = "SELECT * FROM jos_ts_shirts 
        WHERE shirt_id=LAST_INSERT_ID()";
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        return $rows;
    
    }
    
    //update record
    public static function update_shirt($shirt_id,
    $tournament_id,
    $shirt_img,
    $shirt_href,
    $shirt_desc = '')
    {
        
        //create sql query
        	$query = 'UPDATE jos_ts_shirts SET ';
	$query .= "tournament_id='".($tournament_id)."',";
	$query .= "shirt_img='".($shirt_img)."',";
	$query .= "shirt_href='".($shirt_href)."',";
	if(trim($shirt_desc) != '') {
		$query .= "shirt_desc='".($shirt_desc)."',";
	}
	if(strlen($query) > 0)
 	{ $query = substr($query, 0, (strlen($query) - 1)); }	$query .= " WHERE shirt_id='".($shirt_id)."'";
        
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $result = $db->query();
        
        $success = false;
        
        if ($db->getErrorMsg() != ""){
            $message = "DB Error: ";
            $message .= $db->getErrorMsg();
            $message .= 'Whole query: ' . $query;
            $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
            echo $message;
        }
        else
        {
            $success = true;
        }
        return $success;
    
    }
    
    //delete record
    public static function delete_shirt($shirt_id)
    {
    
        $query = "DELETE FROM jos_ts_shirts
        WHERE shirt_id = ".($shirt_id);
        
        $success = false;
        
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $result = $db->query();
        
        if ($db->getErrorMsg() != ""){
            $message = "DB Error: ";
            $message .= $db->getErrorMsg();
            $message .= 'Whole query: ' . $query;
            $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
            echo $message;
        }
        else
        {
            $success = true;
        }
        
        return $success;
        
    }
    
    //select record
    public static function select_shirt($shirt_id = '',$tournament_id = '')
    {
    
        $query = "SELECT * FROM jos_ts_shirts t ";
        $query .= "WHERE t.shirt_id IS NOT NULL ";
        
        if(trim($shirt_id) != "")
        {
            $query .= "AND t.shirt_id = '".($shirt_id)."' ";
        }
        
        if(trim($tournament_id) != "")
        {
            $query .= "AND t.tournament_id = '".($tournament_id)."' ";
        }
    
        $query .= "ORDER BY t.tournament_id";
        
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
    
        return $rows;
    }
    
    //select name record
    public static function selectname_shirt($shirt_id)
    {
        $name = "";
        $query = "SELECT * FROM jos_ts_shirts t ";
        $query .= "WHERE t.shirt_id IS NOT NULL ";
        $query .= "AND t.shirt_id = '".($shirt_id)."' ";  
        $query .= "ORDER BY t.tournament_id";
        
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        foreach ($rows as $row)
        {
            $name = $row->tournament_id;
        }
        
        return $name;
    }
    
    //populate dropdown
    public static function dropdown_shirt($shirt_id_selected, $add_all = false)
    {
    
        $html = '<select name="shirt_id" id="shirt_id">';
        $query = "SELECT * FROM jos_ts_shirts t ";
        $query .= "WHERE t.shirt_id IS NOT NULL ";
        $query .= "ORDER BY tournament_id";
    
        //if all selection is needed all it
        if($add_all)
        {
            $html = $html. '  <option value="-1">ALL</option>';
        }
    
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        foreach ($rows as $row)
        {
            if ($shirt_id_selected == $row->shirt_id)
            {
                $html = $html. '  <option value="'.$row->shirt_id.'" SELECTED>'.$row->tournament_id.'</option>';
            }
            else
            {
                $html = $html. '  <option value="'.$row->shirt_id.'">'.$row->tournament_id.'</option>';
            }
        }
        
        $html = $html .'</select>';
        return $html;
    
    }
    
}

?>
