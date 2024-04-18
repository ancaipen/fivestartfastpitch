<?php

//error_reporting(E_ALL);
ini_set('display_errors', '0');

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

//TEMP REDIRECT TO HOMEPAGE
//header('Location: ' . "https://www.ohiobaseball.com");

// Include the syndicate functions only once
require_once( dirname(__FILE__).DS.'helper.php' );

//gets the current form mode (ie register, payment, thank_you)
$mode = mod_ts_tourn_register::GetRegisterMode();

//gets options for tournament/age dropdowns
$tourn_vals = mod_ts_tourn_register::PopulateTournamentDropdown();
$age_vals = mod_ts_tourn_register::PopulateAgeDropdown();
//sets current url we will use to POST values to
$post_base_url = "https://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
$err_msg = '';

//check for page post
if($_SERVER["REQUEST_METHOD"] == 'POST')
{
    
    //get current mode from posted form
    if($mode == "register")
    {
        
        //save register values and refresh page
        //salutation used as honeypot should always be blank
        $err_msg = '';
        $salutation = $_POST["salutation"];
		$team_state = $_POST["team_state"];
        $tournament_id_1 = filter_var(trim($_POST["tournament_id_1"]), FILTER_SANITIZE_STRING);
        $tournament_id_2 = filter_var(trim($_POST["tournament_id_2"]), FILTER_SANITIZE_STRING);
        $tournament_id_3 = filter_var(trim($_POST["tournament_id_3"]), FILTER_SANITIZE_STRING);
        $tournament_id_4 = filter_var(trim($_POST["tournament_id_4"]), FILTER_SANITIZE_STRING);
        $tournament_id_5 = filter_var(trim($_POST["tournament_id_5"]), FILTER_SANITIZE_STRING);
        $age_id_1 = filter_var(trim($_POST["age_id_1"]), FILTER_SANITIZE_STRING);
        $age_id_2 = filter_var(trim($_POST["age_id_2"]), FILTER_SANITIZE_STRING);
        $age_id_3 = filter_var(trim($_POST["age_id_3"]), FILTER_SANITIZE_STRING);
        $age_id_4 = filter_var(trim($_POST["age_id_4"]), FILTER_SANITIZE_STRING);
        $age_id_5 = filter_var(trim($_POST["age_id_5"]), FILTER_SANITIZE_STRING);

        $team_selected = false;
        $age_selected = false;
		$the_date = 0;
        
        if(isset($_POST["the_date"]))
        {
            $the_date = $_POST["the_date"];
        }
        
        //make sure team has been selected
        if($tournament_id_1 != "-1")
        {
            $team_selected = true;
        }
        
        if($tournament_id_2 != "-1")
        {
            $team_selected = true;
        }
        
        if($tournament_id_3 != "-1")
        {
            $team_selected = true;
        }
        
        if($tournament_id_4 != "-1")
        {
            $team_selected = true;
        }
        
        if($tournament_id_5 != "-1")
        {
            $team_selected = true;
        }
        
        //make sure age has been selected
        if($age_id_1 != "-1")
        {
            $age_selected = true;
        }
        
        if($age_id_2 != "-1")
        {
            $age_selected = true;
        }
        
        if($age_id_3 != "-1")
        {
            $age_selected = true;
        }
        
        if($age_id_4 != "-1")
        {
            $age_selected = true;
        }
        
        if($age_id_5 != "-1")
        {
            $age_selected = true;
        }
        
        //validate request
        if($team_selected == false)
        {
            $err_msg .= '<div class="message_error">Please select a tournament to continue registration.</div>';            
        }
        
        if($age_selected == false)
        {
            $err_msg .= '<div class="message_error">Please select an age for each selected tournament to continue registration.</div>';            
        }
        
        //check for honeypot(s)
        if($salutation != "")
        {
            $err_msg .= '<div class="message_error">Please correct any errors to continue registration.</div>';   
        }
        
        if($the_date < 10)
        {
            $err_msg .= '<div class="message_error">Please correct any errors to continue registration.</div>'; 
        }
        
        if ($err_msg == "")
        {
            mod_ts_tourn_register::SaveRegistration();
            
			$guid = uniqid();
			header('Location: '.$post_base_url . '?registration='.$guid);
            //JApplication::redirect($post_base_url);
        }
        
    }

}
else
{
    if($mode == "payment")
    {
        $registration_id = $_SESSION['registration_id'];
        if($registration_id != 0)
        {
			
			$registration_id = filter_var(trim($registration_id), FILTER_SANITIZE_STRING);
			
            $tournament_html = "";
            $team_name = "";
            $level_play = "";
            $team_manager_1 = "";
            $team_manager_2 = "";
            $team_address = "";
            $team_city = "";
            $team_state = "";
            $team_zip = "";
            $home_phone = "";
            $cell_phone_1 = "";
            $cell_phone_2 = "";
            $email_1 = "";
            $email_2 = "";
            $comments = "";

            //get variables back from the database based on saved last id
            $query = 'SELECT * FROM j3_ts_register ';
            $query = $query . 'WHERE id='.($registration_id);

            $db =& JFactory::getDBO();
            $db->setQuery($query);
            $rows = $db->loadObjectList();

            $err_count = 0;
            $err_count = $db->getErrorNum;
            
            if ($err_count > 0){
                $message = "DB Error: ";
                $message .= $db->getErrorMsg();
                $message .= 'Whole query: ' . $query;
                $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
                die($message);
            }

            //set registration variables
            foreach ($rows as $row)
            {
                $team_name = $row->team_name;
                $level_play = $row->level_play;
                $team_manager_1 = $row->team_manager_1;
                $team_manager_2 = $row->team_manager_2;
                $team_address = $row->team_address;
                $team_city = $row->team_city;
                $team_state = $row->team_state;
                $team_zip = $row->team_zip;
                $home_phone = $row->home_phone;
                $cell_phone_1 = $row->cell_phone_1;
                $cell_phone_2 = $row->cell_phone_2;
                $email_1 = $row->email_1;
                $email_2 = $row->email_2;
                $comments = $row->comments;
            }
        
            $arr_tourn = mod_ts_tourn_register::GetTournamentRegInfo($registration_id);
            $paypal_url = mod_ts_tourn_register::PaypalURL('mhoisington@ohiobaseball.com',$arr_tourn[2], $arr_tourn[1], $post_base_url );
            mod_ts_tourn_register::SetRegisterMode('thank_you');

        }

    }
    else if($mode =='thank_you')
    {
      
        mod_ts_tourn_register::SetRegisterMode('register');

    }

}

//loads forms from tmpl\default.php
require( JModuleHelper::getLayoutPath( 'mod_ts_tourn_register' ) );

?>