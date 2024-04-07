<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
use Joomla\CMS\Filter\InputFilter;
//namespace YourNamespace\Models;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\AdminModel;

jimport( 'joomla.application.component.model' );

class TSAdminModelRegistration extends JModelLegacy
{

    public static function setAllRegToolbar()
    {
        JToolBarHelper::title('Registration Manager', 'generic.png');
        JToolBarHelper::deleteList();
		JToolBarHelper::apply();
		JToolBarHelper::custom('export', 'generic.png', 'generic.png', 'Export', true);
    }

    public static function setRegToolbarEdit()
    {
        JToolBarHelper::title('Registration Manager', 'generic.png');
        //JToolBarHelper::cancel();
    }

    public static function DeleteRegistration($registration_id)
    {
        
        $registration_id = filter_var(trim($registration_id), FILTER_SANITIZE_STRING);
                
        $query = "UPDATE jos_ts_register 
		SET reg_status = 'Deleted' ";
        $query .= "WHERE registration_id = ".($registration_id);

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
	
	public static function UpdateRegistrationStatus($registration_id, $reg_status)
    {
        
        $registration_id = filter_var(trim($registration_id), FILTER_SANITIZE_STRING);
		$reg_status = filter_var(trim($reg_status), FILTER_SANITIZE_STRING);
                
        $query = "UPDATE jos_ts_register 
		SET reg_status = '".$reg_status."' ";
        $query .= "WHERE registration_id = ".($registration_id);

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

	public static function dropdown_regstatus($regstatus_selected, $regststatus_id = '0', $add_all = false)
    {

		$arr_reg_status = array("New", "Complete", "Waiting List");
		$arr_reg_status_count = count($arr_reg_status);
		$html = '<select name="reg_status_'.$regststatus_id.'" id="reg_status_'.$regststatus_id.'">';
		
		//if all selection is needed all it
        if($add_all)
        {
            $html = $html. '  <option value="-1">ALL</option>';
        }
		
        for($x = 0; $x < $arr_reg_status_count; $x++)
        {
            if ($arr_reg_status[$x] == $regstatus_selected)
            {
                $html = $html. '  <option value="'.$arr_reg_status[$x].'" SELECTED>'.$arr_reg_status[$x].'</option>';
            }
            else
            {
                $html = $html. '  <option value="'.$arr_reg_status[$x].'">'.$arr_reg_status[$x].'</option>';
            }
        }

        $html = $html .'</select>';
        return $html;

    }
	
    public static function getRegistrationData($reg_status = '', $tournament_id = '', $age_id = '')
    {
		$inputFilter = new InputFilter;
        $reg_status = $inputFilter->clean(trim($reg_status), 'string');
		//$reg_status = filter_var(trim($reg_status), FILTER_SANITIZE_STRING);
        $tournament_id = $inputFilter->clean(trim($tournament_id), 'string');
		//$tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_STRING);
        $age_id = $inputFilter->clean(trim($age_id), 'string');
		//$age_id = filter_var(trim($age_id), FILTER_SANITIZE_STRING);

		$query = "select distinct s.season_name, reg.team_name, reg.date_submitted, IFNULL(reg.reg_status, 'New') as reg_status, reg.team_manager_1, reg.cell_phone_1, reg.email_1, reg.comments, reg.registration_id 
		from jos_ts_register reg 
		inner join  jos_ts_register_tourn rt on rt.register_id=reg.registration_id 
        inner join jos_ts_age a on a.age_id=rt.age_id
        inner join jos_ts_tournament t on t.tournament_id=rt.tournament_id
        inner join jos_ts_tournament_age_cost ac on ac.age_id=rt.age_id AND ac.tournament_id=rt.tournament_id
        INNER JOIN jos_ts_season s on s.season_id=t.season_id
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) 
        AND s.season_current = 1 
		AND reg.reg_status <> 'Deleted' ";
				 
		if(trim($reg_status) != '')
		{
			$query .= " AND reg.reg_status = '".$reg_status."' ";
		}
		
		if(trim($tournament_id) != '')
		{
			$query .= " AND t.tournament_id = ".$tournament_id." ";
		}
		
		if(trim($age_id) != '')
		{
			$query .= " AND rt.age_id = ".$age_id." ";
		}
		
		$query .= " ORDER BY date_submitted DESC ";
		
		//echo $query;
		
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        return $rows;

    }

    public static function GetTournamentRegInfo($registration_id)
    {
        $inputFilter = new InputFilter;
        $registration_id = $inputFilter->clean(trim($registration_id),'string');
        //$registration_id = filter_var(trim($registration_id), FILTER_SANITIZE_STRING);
		
        $query = "select t.tournament_name, t.tournament_name, a.age, ac.tournament_cost,rt.* from jos_ts_register_tourn rt
        inner join jos_ts_age a on a.age_id=rt.age_id
        inner join jos_ts_tournament t on t.tournament_id=rt.tournament_id
        inner join jos_ts_tournament_age_cost ac on ac.age_id=rt.age_id AND ac.tournament_id=rt.tournament_id
        INNER JOIN jos_ts_season s on s.season_id=t.season_id
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) AND 
        register_id = ".($registration_id). "
        AND s.season_current = 1 ";
			
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $html = '<ul>';
        $total = 0;
        $tourn_text = '';

        foreach ($rows as $row)
        {
            $html = $html . '<li>' . $row->tournament_name .'- '. $row->age .'&nbsp;&nbsp;$'. $row->tournament_cost . '</li>';
            $total = $total + $row->tournament_cost;
            $tourn_text .=  $row->tournament_name .'- '. $row->age .' $'. $row->tournament_cost . ', ';

        }

        $html = $html . '</ul>';

        $arr_tourn = array($html, $total, $tourn_text);


        return $arr_tourn;

    }

    public static function getRegistrationForm($register_id)
    {
        $inputFilter = new InputFilter;
        $register_id = $inputFilter->clean(trim($register_id), 'string');
        //$register_id = filter_var(trim($register_id), FILTER_SANITIZE_STRING);
                
        $query = "SELECT * FROM jos_ts_register 
        WHERE registration_id =" .($register_id);		
	
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        return $rows;

    }

    public static function getTournamentReg($register_id)
    {
        $inputFilter = new InputFilter;
        $register_id = $inputFilter->clean(trim($register_id), 'string');
        //$register_id = filter_var(trim($register_id), FILTER_SANITIZE_STRING);
                
        $html = '';
        $query = "SELECT register_id, tournament_name, age, waitlist FROM jos_ts_register
        INNER JOIN jos_ts_register_tourn ON jos_ts_register.registration_id = jos_ts_register_tourn.register_id
        INNER JOIN jos_ts_tournament ON jos_ts_register_tourn.tournament_id = jos_ts_tournament.tournament_id
        INNER JOIN jos_ts_age ON jos_ts_register_tourn.age_id = jos_ts_age.age_id
        WHERE (jos_ts_tournament.is_deleted = 0 OR jos_ts_tournament.is_deleted IS NULL) AND
        jos_ts_tournament.season_id in (SELECT season_id FROM jos_ts_season WHERE season_current = 1) AND
        register_id =" .($register_id);
        
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        foreach ($rows as $row)
        {
			$html .= $row->tournament_name . ' - ' . $row->age;
			$waitlist  = filter_var(trim($row->waitlist), FILTER_SANITIZE_NUMBER_INT);
			if($waitlist == 1)
			{
				$html .= '<span style="background-color:#f7f779;padding: 2px;">*Added to Waitlist*</span>';
			}
			$html .= '<hr style="padding:3px;margin:0;" />';
        }
        return $html;

    }

      //create html to display current tournaments
    public static function getRegFormData($registration_id)
    {
        
        $registration_id = filter_var(trim($registration_id), FILTER_SANITIZE_STRING);
		
        $query = "SELECT * FROM jos_ts_tournament
        INNER JOIN jos_ts_tournament_age_cost on jos_ts_tournament_age_cost.tournament_id = jos_ts_tournament.tournament_id
        WHERE jos_ts_tournament.tournament_id =".($tournament_id). " AND jos_ts_tournament_age_cost.age_id =" .($age_id);
	
		
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        return $rows;
    }


}



?>
