<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Factory;



jimport( 'joomla.application.component.model' );

class TSAdminModelTournament extends JModelLegacy
{

    public static function setAllTournToolbar()
    {
        JToolBarHelper::title('Tournament Manager', 'generic.png');
        JToolBarHelper::deleteList();
        JToolBarHelper::editList();
        JToolBarHelper::addNew();
    }

    public static function setTournToolbar($id)
    {
        if ($id) {
                $newEdit = 'Edit';
        } else {
                $newEdit = 'New';
        }

        JToolBarHelper::title($newEdit . ' Tournament', 'generic.png');
        JToolBarHelper::save();
        JToolBarHelper::apply();
        JToolBarHelper::cancel();
        
    }    

    public static function delete_tournament($tournament_id)
    {

        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_STRING);
        
        $query = "UPDATE jos_ts_tournament SET
        is_deleted = 1
        WHERE tournament_id = ".($tournament_id);

         //update into tournament table
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $result = $db->execute();

        // if ($db->getErrorMsg() != ""){
        //     $message = "DB Error: ";
        //     $message .= $db->getErrorMsg();
        //     $message .= 'Whole query: ' . $query;
        //     $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
        //     echo $message;
        // }
    }
    
    public static function save_tournament_age_cost($post, $tournament_id)
    {     
        
        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_STRING);
        
        //remove existing records
        if($tournament_id != "-1")
        {
           TSAdminModelTournament::delete_tournament_age_cost($post['tournament_id']);
        }

        $age_id = -1;
        $tournament_cost = -1;
        
        //get all age ids and find them in posted values
        $query = "SELECT * FROM jos_ts_age
        ORDER BY age_num";

        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        foreach ($rows as $row)
        {

            $age_id = $post['ageid_'.$row->age_id];
            $tournament_cost = $post['tourncost_'.$row->age_id];

            //save data
            if($age_id =='on' && $tournament_cost > 0)
            {

                //add tournament association
                $query = "INSERT INTO jos_ts_tournament_age_cost (tournament_id, age_id, tournament_cost, tournament_results, field_location_description)
                VALUES (".($tournament_id).",
                ".($row->age_id).",
                ".($tournament_cost).",
                '".($post['tournament_results'])."',
                '".($post['field_location_description'])."') ";

                //insert to tournament_age_cost table
                $db = JFactory::getDBO();
                $db->setQuery($query);
                $result = $db->execute();
                /*
                if ($db->getErrorMsg() != ""){
                    $message = "DB Error: ";
                    $message .= $db->getErrorMsg();
                    $message .= 'Whole query: ' . $query2;
                    $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
                    echo $message;
                }
                */
            }
        }
    }

    public static function delete_tournament_age_cost($tournament_id)
    {
        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_STRING);
        
        $query = "DELETE FROM jos_ts_tournament_age_cost WHERE tournament_id=".($tournament_id);

        $db = JFactory::getDBO();
        $db->setQuery($query);
        $result = $db->execute();
        /*
        if ($db->getErrorMsg() != ""){
            $message = "DB Error: ";
            $message .= $db->getErrorMsg();
            $message .= 'Whole query: ' . $query2;
            $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
            echo $message;
        }
        */
    }

    public static function update_tournament($post)
    {

        $message = '';

        //add insert code here
        $tournament_complete = 0;
        if(isset($post['tournament_complete']))
        {
            $tournament_complete = 1;
        }

        //make sure all fields are present
        if(trim($post['tournament_name']) == '')
        {
            $message .= $error_div.'Tournament name is required.</div>';
        }

        if(trim($post['tournament_start_date']) == '')
        {
            $message .= $error_div.'Tournament start date is required.</div>';
        }

        if(trim($post['tournament_end_date']) == '')
        {
            $message .= $error_div.'Tournament end date is required.</div>';
        }

        if($message=='')
        {
            //editor fields, needs to allow html
            $request = Factory::getApplication()->input;
            $tournamentDescription = $request->get('tournament_description');
           
            $teamsRegistered = $request->get('teams_registered');
         
            $tournamentNotes = $request->get('tournament_notes');
           
            
            //replace/remove single quotes
            $post['tournament_description'] = preg_replace("/'/", "\&#39;", $post['tournament_description']);
            $post['teams_registered'] = preg_replace("/'/", "\&#39;", $post['teams_registered']);
            $post['tournament_notes'] = preg_replace("/'/", "\&#39;", $post['tournament_notes']);
            
            $query = "UPDATE jos_ts_tournament SET
            tournament_name ='".($post['tournament_name'])."',
            tournament_start_date = '".($post['tournament_start_date'])."',
            tournament_end_date = '".($post['tournament_end_date'])."',
            tournament_description = '".($post['tournament_description'])."',
            teams_registered = '".($post['teams_registered'])."',
            tournament_notes = '".($post['tournament_notes'])."',
            tournament_complete = ".($tournament_complete)."
            WHERE tournament_id = ".($post['tournament_id']);

            //update var to allow html
            $input = Factory::getApplication()->input;
            $tournamentResults = $input->getString('tournament_results', '', 'post', 'html');
            //$post['tournament_results']=JRequest::getVar( 'tournament_results', '', 'post', 'string', JREQUEST_ALLOWHTML );
            $fieldLocationDescription = $input->getString('field_location_description', '', 'post', 'html');
            //$post['field_location_description']=JRequest::getVar( 'field_location_description', '', 'post', 'string', JREQUEST_ALLOWHTML );

            //update into tournament table
            $db = Factory::getDBO();
            $db->setQuery($query);
            $result = $db->execute();

            
            //Replacement code for joomla 4
            // $db = Factory::getDBO();
            // $query = $db->getQuery(true);
            // $query->select($db->quoteName('columnname'))->from($db->quoteName('tablename'));
            // $db->setQuery($query);
            // $result = $db->loadResult();
            // $db->execute();
			
			/*

            if ($db->getErrorMsg() != ""){
                $message = "DB Error: ";
                $message .= $db->getErrorMsg();
                $message .= 'Whole query: ' . $query;
                $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
                echo $message;
            }
			*/
            //save tournament age association
            TSAdminModelTournament::save_tournament_age_cost($post, $post['tournament_id']);
        }

        return $message;
        
    }

    public static function insert_tournament($post)
    {
        $app = Factory::getApplication();
        $message = '';
        $error_div = '<div style="padding:7px;background-color:#FFB3B3;border:solid 1px #FF8080;margin-bottom:2px;">';

        //add insert code here
        $tournament_complete = 0;
        if(isset($post['tournament_complete']))
        {
            $tournament_complete = 1;
        }

        //make sure all fields are present
        if(trim($post['tournament_name']) == '')
        {
            $message .= $error_div.'Tournament name is required.</div>';
        }

        if(trim($post['tournament_start_date']) == '')
        {
            $message .= $error_div.'Tournament start date is required.</div>';
        }

        if(trim($post['tournament_end_date']) == '')
        {
            $message .= $error_div.'Tournament end date is required.</div>';
        }

        if($message=='')
        {
            
            //editor fields, needs to allow html
            $post['tournament_description']=$app->input->getVar( 'tournament_description');
            $post['teams_registered']=$app->input->getVar( 'teams_registered');
            $post['tournament_notes']=$app->input->getVar( 'tournament_notes');
            
            //replace/remove single quotes
            $post['tournament_description'] = preg_replace("/'/", "\&#39;", $post['tournament_description']);
            $post['teams_registered'] = preg_replace("/'/", "\&#39;", $post['teams_registered']);
            $post['tournament_notes'] = preg_replace("/'/", "\&#39;", $post['tournament_notes']);
            
            $query = "INSERT INTO jos_ts_tournament (season_id, tournament_name, tournament_start_date, tournament_end_date, tournament_description, teams_registered, tournament_notes, is_deleted, tournament_complete)
            VALUES ((SELECT season_id FROM jos_ts_season WHERE season_current = 1 limit 1),
            '".($post['tournament_name'])."',
            '".($post['tournament_start_date'])."','".($post['tournament_end_date'])."',
            '".($post['tournament_description'])."',
            '".($post['teams_registered'])."',
            '".($post['tournament_notes'])."',
            0,
            ".($tournament_complete).") ";

            //insert into tournament table
            $db = JFactory::getDBO();
            $db->setQuery($query);
            $result = $db->execute();

            // if ($db->getErrorMsg() != ""){
            //     $message = "DB Error: ";
            //     $message .= $db->getErrorMsg();
            //     $message .= 'Whole query: ' . $query;
            //     $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
            //     echo $message;
            // }

            //save tournament age association
            TSAdminModelTournament::save_tournament_age_cost($post, "-1");

        }

        return $message;

    }

    public static function getTournamentData()
    {

        $query = "SELECT * FROM jos_ts_tournament t ";
        $query .= "INNER JOIN jos_ts_season s on s.season_id=t.season_id ";
        $query .= "WHERE s.season_current = 1 AND (t.is_deleted = 0 OR t.is_deleted IS NULL) ";
        $query .= "ORDER BY tournament_start_date";

        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        return $rows;

    }

    public static function getTournamentAgeData($tournament_id)
    {
        $tournament_id = InputFilter::getInstance()->clean(trim($tournament_id), 'string');
        //$tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_STRING);
        
        $html = '';

        $query = "SELECT * FROM jos_ts_tournament_age_cost ac
        INNER JOIN jos_ts_age a on a.age_id=ac.age_id
        WHERE tournament_id=".($tournament_id);

        try 
        {
            $db = JFactory::getDBO();
            $db->setQuery($query);
            $a_rows = $db->loadObjectList();

            foreach ($a_rows as $a_row)
            {
                $html .= $a_row->age.', ';
            }

            if(strlen($html) > 0)
            {
                $html = substr($html, 0, -2);
            }

        } catch (Exception $e) {}

        return $html;

    }

    public static function BuildTournamentAgeCostLists($tournament_id)
    {
        //$tournament_id = InputFilter::getInstance()->clean($tournament_id, 'STRING');
        $tournament_id = filter_var(trim($tournament_id));
       
		if(!isset($tournament_id))
		{
			$html = '<h2>Save Tournament first in order to assign ages and cost</h2>';
			return $html;
		}
		
		if($tournament_id == "")
		{
			$html = '<h2>Save Tournament first in order to assign ages and cost</h2>';
			return $html;
		}
		
        //build available list
        $query = "select a.*, ac.tournament_cost, ac.tournament_cost_id FROM jos_ts_age a
        LEFT JOIN jos_ts_tournament_age_cost ac on ac.tournament_id=".$tournament_id." AND a.age_id=ac.age_id
        ORDER BY age_num";

        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
   
        $rowcount = 0;
        $html = '';
        $html .= '<table cellpadding="8" cellspacing="0">';

        foreach ($rows as $row)
        {
            $found = TSAdminModelTournament::getSelectedAgeCost($tournament_id, $row->age_id);
    
            $css = 'tr_0';
            if($rowcount % 2 == 0)
            {
                $css = 'tr_1';
            }
            
            $chk_id = "'ageid_".$row->age_id."'";
            $chk_lbl = "'agelbl_".$row->age_id."'";
            $txt_id = "'tourncost_".$row->age_id."'";
            $filedesc_id = "'tournfiledesc_".$row->age_id."'";
            $edit_id = "tournedit_".$row->age_id;
            $link_id = "'tournlink_".$row->age_id."'";
            $linkremove_id = "'tournlinkremove_".$row->age_id."'";
            $age_id = "'".$row->age_id."'";
            $div_id = "'".'appendfiles_'.$row->age_id."'";
            $default_div_id = "'".'defaultfiles_'.$row->age_id."'";

            $html .= '<tr class="'.$css.'"><td valign="top" colspan="2">';

            //add check box
            if($found==true)
            {
                //age id
                $html .= '<input type="checkbox" name='.$chk_id.' id='.$chk_id.' OnClick="enableField('.$chk_id.','.$txt_id.','.$default_div_id.','.$div_id.');" CHECKED /><label name='.$chk_lbl.' for='.$chk_id.'>'.$row->age.'</label>';
            }
            else
            {
                $html .= '<input type="checkbox" name='.$chk_id.' id='.$chk_id.' OnClick="enableField('.$chk_id.','.$txt_id.','.$default_div_id.','.$div_id.');" UNCHECKED /><label name='.$chk_lbl.' for='.$chk_id.'>'.$row->age.'</label>';
            }

            //tournament cost
            $html .= '&nbsp;&nbsp;$<input type="text" id='.$txt_id.' name='.$txt_id.' value="'.$row->tournament_cost.'"/>';
            if(isset($row->tournament_cost_id))
            {
               $html .= '&nbsp;<a href="index.php?option=com_ts&view=tournament_cost&task=edit&cid[]='.$row->tournament_cost_id.' id="'.$edit_id.'">edit</a>';
            }
            $html .= '</td></tr>';

            $html .= '<div id='.$div_id.'></div>';
            $html .= '<input type="hidden" name="clickcount_'.$row->age_id.'" value="1" id="clickcount_'.$row->age_id.'" />';

            //used to disable or enable texboxes onload
            $html .= '<img src="/templates/OhioBaseball/images/icon-48-generic.png" height="0" width="0" OnLoad="enableField('.$chk_id.','.$txt_id.','.$default_div_id.','.$div_id.');" />';
            $html .= '</td>';

            $html .= '</tr>';

            $rowcount++;
        }

        $html .= '</table>';
     
        return $html;

    }

      //create html to display current tournaments
    public static function getSelectedAgeCost($tournament_id, $age_id)
    {
        $tournament_id = InputFilter::getInstance()->clean($tournament_id, 'STRING');
        //$tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_STRING);
        $age_id = InputFilter::getInstance()->clean(trim($age_id), 'STRING');
        //$age_id = filter_var(trim($age_id), FILTER_SANITIZE_STRING);
        
        $query = "SELECT * FROM jos_ts_tournament t
        INNER JOIN jos_ts_tournament_age_cost ac on ac.tournament_id = t.tournament_id
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) ";
		
		if(isset($tournament_id))
		{
			if($tournament_id != "")
			{
				$query .= "AND t.tournament_id =".($tournament_id) . " ";
			}
		}
		
		if(isset($tournament_id))
		{
			if($tournament_id != "")
			{
				$query .= "AND ac.age_id = ".($age_id);
			}
		}
		
        $found = false;
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        foreach ($rows as $row)
        {
            $found = true;
        }

        return $found;
    }

      //create html to display current tournaments
    public static function getTournamentFormData($tournament_id)
    {
        $tournament_id = InputFilter::getInstance()->clean($tournament_id, 'STRING');
        //$tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_STRING);
        $rows = null;
		
		if(isset($tournament_id))
		{
			if($tournament_id != "")
			{
				$query = "SELECT *, (SELECT field_location_description FROM jos_ts_tournament_age_cost WHERE tournament_id=t.tournament_id LIMIT 1,1) as field_location_description
				FROM jos_ts_tournament t
				WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) "; 
				$query .= "AND t.tournament_id =".($tournament_id);
				
				$db = JFactory::getDBO();
				$db->setQuery($query);
				$rows = $db->loadObjectList();
				
			}
		}

        return $rows;
    }

    //create html to display current tournaments
    public static function PopulateAge($age_id)
    {

        $query = "SELECT * FROM jos_ts_age
        ORDER BY age_num";

        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $html = '';
        $rowCount = 0;

        $html = $html .'<option value="-1">Select One</option>';
        foreach ($rows as $row)
        {
            $option_value = $row->age_id;
            $option_text = $row->age;
            
            if($age_id == $row->age_id)
            {
                $html = $html . '<option value="'.$option_value .'" SELECTED>'. $option_text .'</option>\n';
            }
            else
            {
               $html = $html . '<option value="'.$option_value .'">'. $option_text .'</option>\n';
            }
            
            $rowCount++;
        }

        return $html;
    }

}

?>
