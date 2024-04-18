<?php
/**
 * Helper class for Maxiem Front Page module
 * 
 */
 
 // no direct access



defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;

//require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
        
class mod_ts_tourn_register
{

    public static function SetRegisterMode($mode)
    {

        //check to see if session has been created, if not create it
        if (session_id() == '')
        {
          session_start();
        }

        //updates current mode
        if(!isset($_SESSION['ts_mode']))
        {
            //set default settings
            $_SESSION['ts_mode'] = 'register';
        }
        else
        {
            $_SESSION['ts_mode'] = $mode;
        }

     }

    public static function GetRegisterMode()
    {

        $mode = 'register';
        //check to see if session has been created, if not create it
        if (session_id() == '')
        {
          session_start();
        }

        //gets current mode
        if(isset($_SESSION['ts_mode']))
        {
            //set default settings
            $mode = $_SESSION['ts_mode'];
        }
        else
        {
            $_SESSION['ts_mode'] = $mode;
        }

        return $mode;

     }

    public static function GetTournamentRegInfo($registration_id)
    {
        
        $registration_id = filter_var(trim($registration_id), FILTER_SANITIZE_STRING);
        
        $query = "select t.tournament_name, a.age, ac.tournament_cost,rt.* from j3_ts_register_tourn rt
        inner join j3_ts_age a on a.age_id=rt.age_id
        inner join j3_ts_tournament t on t.id=rt.tournament_id
        inner join j3_ts_tournament_age_cost ac on ac.age_id=rt.age_id AND ac.tournament_id=rt.tournament_id
        INNER JOIN j3_ts_season s on s.season_id=t.season_id
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) AND 
        register_id = ".($registration_id). " AND t.season_id in (SELECT season_id FROM j3_ts_season WHERE season_current = 1) ";

        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $html = '<ul>';
        $total = 0;
        $tourn_text = '';
        
        foreach ($rows as $row)
        {
			$waitlist = filter_var(trim($row->waitlist), FILTER_SANITIZE_NUMBER_INT);
			if($waitlist == 1)
			{
				$html = $html . '<li>' . $row->tournament_name .'- '. $row->age .'&nbsp;&nbsp;$'. $row->tournament_cost . ' <span class="waitlist-highlight">*Added to Wait List*</span></li>';
				$tourn_text .=  $row->tournament_name .'- '. $row->age .' $'. $row->tournament_cost . ', ';
			}
			else
			{
				$html = $html . '<li>' . $row->tournament_name .'- '. $row->age .'&nbsp;&nbsp;$'. $row->tournament_cost . '</li>';
				$total = $total + $row->tournament_cost;
				$tourn_text .=  $row->tournament_name .'- '. $row->age .' $'. $row->tournament_cost . ', ';
			}
        }

        $html = $html . '</ul>';

        $arr_tourn = array($html, $total, $tourn_text);


        return $arr_tourn;

    }

    public static function PaypalURL($paypal_email, $tourn_text, $total, $return_url){

        $redirect = "https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=" . $paypal_email;
        $redirect .= "&item_name=" . $tourn_text;
        $redirect .= "&amount=" . $total;
        $redirect .= "&item_number=5 Star Fastpitch" ;
        $redirect .= "&cy=USD";
        $redirect .= "&return=" . $return_url;
        $redirect .= "&cancel_return=" . $return_url;

        return $redirect;


    }

    //create html to display current tournaments
    public static function PopulateTournamentDropdown()
    {

        //-------------------------------
        //TOURNAMENT DROPDOWN
        //-------------------------------

        $query = "select id as tournament_id, tournament_name,
        DATE_FORMAT(tournament_start_date, '%M %D') as tournament_start_name,
        DATE_FORMAT(tournament_end_date, '%M %D') as tournament_end_name
        from j3_ts_tournament t
        INNER JOIN j3_ts_season s on s.season_id=t.season_id
        WHERE (is_deleted = 0 OR is_deleted IS NULL) AND s.season_current = 1
        ORDER BY tournament_start_date ASC ";

        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $html = '';
        $rowCount = 0;

        $html = $html .'<option value="-1">Select One</option>';
        foreach ($rows as $row)
        {
            $option_value = $row->tournament_id;
            $option_text = $row->tournament_start_name . '-' . $row->tournament_end_name .'&nbsp;'.$row->tournament_name;
            $html = $html . '<option value="'.$option_value .'">'. $option_text .'</option>\n';
            $rowCount++;
        }

        $html = $html . ' &nbsp;';

        return $html;
    }

    public static function CheckActiveTournaments()
    {

        $query = "SELECT tournament_name FROM j3_ts_tournament t
        INNER JOIN j3_ts_season s on s.season_id=t.season_id
        WHERE s.season_current = 1 AND (t.is_deleted = 0 OR t.is_deleted IS NULL) ";

        $found = false;
        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $row_count = count($rows);

        if($row_count > 0)
        {
            $found = true;
        }

        return $found;

    }
	
	public static function CheckTournamentCapacity($tournament_id, $age_id, $tourn_capacity = -1)
    {
		
		$tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_STRING);
		$age_id = filter_var(trim($age_id), FILTER_SANITIZE_STRING);
		$over_capacity = false;
		$tourn_total = 0;
		
		//lookup capacity as it has not been specified
		if($tourn_capacity == -1)
		{
			
			$tourn_capacity = 0;
			
			//get tournament capacity
			$query = "SELECT ta.tourn_capacity FROM j3_ts_tournament t 
			INNER JOIN j3_ts_tournament_age_cost ta on ta.tournament_id=t.id
			INNER JOIN j3_ts_season s on s.season_id=t.season_id 
			WHERE s.season_current = 1 
			AND (t.is_deleted = 0 OR t.is_deleted IS NULL) 
			AND ta.tourn_capacity IS NOT NULL ";
			
			$query .= "AND ta.tournament_id = ".$tournament_id. " ";
			$query .= "AND ta.age_id = ".$age_id. " ";       
			
			$db = Factory::getDBO();
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			$row_count = count($rows);

			if($row_count > 0)
			{			
				$tourn_capacity  = filter_var(trim($rows[0]->tourn_capacity), FILTER_SANITIZE_NUMBER_INT);
			}
		}
				
		//if greater than 0, calculate current registration
		if($tourn_capacity > 0)
		{

			$query = "SELECT count(*) as reg_count FROM j3_ts_register_tourn rt 
			INNER JOIN j3_ts_register r on r.registration_id=rt.register_id 
			INNER JOIN  j3_ts_tournament t on t.id = rt.tournament_id
			INNER JOIN j3_ts_season s on s.season_id=t.season_id 
			WHERE s.season_current = 1 
			AND (t.is_deleted = 0 OR t.is_deleted IS NULL) 
			AND (rt.waitlist IS NULL OR rt.waitlist = 0)
			AND r.reg_status <> 'Deleted' ";
			
			$query .= "AND rt.tournament_id = ".$tournament_id. " ";
			$query .= "AND rt.age_id = ".$age_id. " ";     
			
			$db = Factory::getDBO();
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			$row_count = count($rows);
			
			if($row_count > 0)
			{			
				$tourn_total  = filter_var(trim($rows[0]->reg_count), FILTER_SANITIZE_NUMBER_INT);
			}
		}
		
		if($tourn_capacity > 0)
		{
			if($tourn_total >= $tourn_capacity)
			{
				$over_capacity = true;
			}
		}
				

        return $over_capacity;

    }

    public static function PopulateAgeDropdown()
    {

        //-------------------------------
        //AGE DROPDOWN
        //-------------------------------

        $query = "SELECT * FROM j3_ts_age a
        INNER JOIN j3_ts_tournament_age_cost tac on tac.age_id = a.age_id
        INNER JOIN j3_ts_tournament t on t.id = tac.tournament_id
        INNER JOIN j3_ts_season s on s.season_id=t.season_id 
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) AND s.season_current = 1
        ORDER BY age_num";

        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $html = '';
        $rowCount = 0;

        $html = $html .'<option value="-1">Select One</option>';
        foreach ($rows as $row)
        {
            $option_value = $row->age_id . $row->tournament_cost ;
            $option_text = $row->age . '&nbsp;$' . $row->tournament_cost;
            $html = $html . '<option value="'.$option_value .'">'. $option_text .'</option>\n';
            $rowCount++;
        }

        $html = $html . ' &nbsp;';

        return $html;
    }

    //create html to display current tournaments
    public static function PopulateTournament()
    {
			
        $query = "SELECT * FROM j3_ts_tournament_age_cost ta
        INNER JOIN j3_ts_tournament t on t.id=ta.tournament_id
        INNER JOIN j3_ts_age a on a.age_id = ta.age_id
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) AND 
        ORDER BY tournament_name asc, age desc ";

        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $html = '';
        $rowCount = 0;

        $html = $html .'<option value="-1">Select One</option>';
        foreach ($rows as $row)
        {
            $option_value = $row->tournament_id .'_'.$row->age_id;
            $option_text = $row->tournament_name.'&nbsp;-  '.$row->age .'  &nbsp;$'.$row->tournament_cost;
            $html = $html . '<option value="'.$option_value .'">'. $option_text .'</option>\n';
            $rowCount++;
        }
		
        return $html;
    }

    public static function SaveRegistration()
    {
        if($_SERVER["REQUEST_METHOD"] == 'POST')
        {

            $team_name = filter_var(trim($_POST["team_name"]), FILTER_SANITIZE_STRING);
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
            $level_play = filter_var(trim($_POST["level_play"]), FILTER_SANITIZE_STRING);
            $team_manager_1 = filter_var(trim($_POST["team_manager_1"]), FILTER_SANITIZE_STRING);
            $team_manager_2 = filter_var(trim($_POST["team_manager_2"]), FILTER_SANITIZE_STRING);
            $team_address = filter_var(trim($_POST["team_address"]), FILTER_SANITIZE_STRING);
            $team_city = filter_var(trim($_POST["team_city"]), FILTER_SANITIZE_STRING);
            $team_state = filter_var(trim($_POST["team_state"]), FILTER_SANITIZE_STRING);
            $team_zip = filter_var(trim($_POST["team_zip"]), FILTER_SANITIZE_STRING);
            $home_phone = filter_var(trim($_POST["home_phone"]), FILTER_SANITIZE_STRING);
            $cell_phone_1 = filter_var(trim($_POST["cell_phone_1"]), FILTER_SANITIZE_STRING);
            $cell_phone_2 = filter_var(trim($_POST["cell_phone_2"]), FILTER_SANITIZE_STRING);
            $email_1 = filter_var(trim($_POST["email_1"]), FILTER_SANITIZE_STRING);
            $email_2 = filter_var(trim($_POST["email_2"]), FILTER_SANITIZE_STRING);
            $comments = filter_var(trim($_POST["comments"]), FILTER_SANITIZE_STRING);
			
			//check for waitlist, flag any selections with wait list
			$age_id_1_waitlist = 0;
			$age_id_2_waitlist = 0;
			$age_id_3_waitlist = 0;
			$age_id_4_waitlist = 0;
			$age_id_5_waitlist = 0;
			
			if (strpos($age_id_1, '_WAITLIST') !== false) {
				$age_id_1_waitlist = 1;
				$age_id_1 = str_replace("_WAITLIST","",$age_id_1);
			}
			
			if (strpos($age_id_2, '_WAITLIST') !== false) {
				$age_id_2_waitlist = 1;
				$age_id_2 = str_replace("_WAITLIST","",$age_id_2);
			}
			
			if (strpos($age_id_3, '_WAITLIST') !== false) {
				$age_id_3_waitlist = 1;
				$age_id_3 = str_replace("_WAITLIST","",$age_id_3);
			}
			
			if (strpos($age_id_4, '_WAITLIST') !== false) {
				$age_id_4_waitlist = 1;
				$age_id_4 = str_replace("_WAITLIST","",$age_id_4);
			}
			
			if (strpos($age_id_5, '_WAITLIST') !== false) {
				$age_id_5_waitlist = 1;
				$age_id_5 = str_replace("_WAITLIST","",$age_id_5);
			}
			
			$reg_status = 'New';
			
			if($age_id_1_waitlist == 1
			|| $age_id_2_waitlist == 1
			|| $age_id_3_waitlist == 1
			|| $age_id_4_waitlist == 1
			|| $age_id_5_waitlist == 1)
			{
				$reg_status = 'Waiting List';
			}
			
            $query = "INSERT INTO j3_ts_register (team_name, level_play, team_manager_1, team_manager_2, team_address, team_city, team_state, team_zip, home_phone, cell_phone_1, cell_phone_2, email_1, email_2, comments, reg_status, season_id) ";
            $query = $query . "VALUES ('".  ($team_name) . "',";
            $query = $query . "'".  ($level_play) . "',";
            $query = $query . "'".  ($team_manager_1) . "',";
            $query = $query . "'".  ($team_manager_2) . "',";
            $query = $query . "'".  ($team_address) . "',";
            $query = $query . "'".  ($team_city) . "',";
            $query = $query . "'".  ($team_state) . "',";
            $query = $query . "'".  ($team_zip) . "',";
            $query = $query . "'".  ($home_phone) . "',";
            $query = $query . "'".  ($cell_phone_1) . "',";
            $query = $query . "'".  ($cell_phone_2) . "',";
            $query = $query . "'".  ($email_1) . "',";
            $query = $query . "'".  ($email_2) . "',";
            $query = $query . "'".  ($comments) . "',";
			$query = $query . "'".$reg_status."',";
            $query = $query . "(SELECT season_id FROM j3_ts_season WHERE season_current = 1 limit 1));";

            //joomla database call
            $db = Factory::getDBO();
            $db->setQuery($query);
            $result = $db->execute();

            $err_count = 0;
            $err_count = $db->getErrorNum;

            if ($err_count > 0){
                $message = "DB Error: ";
                $message .= $db->getErrorMsg();
                $message .= 'Whole query: ' . $query;
                $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
                die($message);
            }
            else
            {
                
                $first_name = "";
                $last_name = "";
                
                $names = explode(' ', $team_manager_1);
                
                if(isset($names[0]))
                { $first_name = $names[0]; }
                
                if(isset($names[1]))
                { $last_name = $names[1]; }
                
                //save registration details to mail chimp
                require_once('modules/mod_ts_tourn_register/inc/MailChimp.class.php');
                $MailChimp = new MailChimp('aa691150fa1c6fd5730d50617852a4ff-us7');
                $result = $MailChimp->call('lists/subscribe', array(
                                'id'                => '583932f24e',
                                'email'             => array('email'=>$email_1),
                                'merge_vars'        => array('FNAME'=>$first_name, 
                                                        'LNAME'=>$last_name,
                                                        'TEAMNAME'=>$team_name,
                                                        'TEAMADD'=>$team_address,
                                                        'TEAMCITY'=>$team_city,
                                                        'TEAMSTATE'=>$team_state,
                                                        'TEAMZIP'=>$team_zip,
                                                        'LEVELPLAY'=>$level_play,
                                                        'PHONE'=>$home_phone,
                                                        'CELL1'=>$cell_phone_1,
                                                        'CELL2'=>$cell_phone_2,
                                                        'EMAIL2'=>$email_2,
                                                        'TEAMMAN1'=>$team_manager_1,
                                                        'TEAMMAN2'=>$team_manager_2,
                                                        'COMMENTS'=>$comments),
                                'double_optin'      => false,
                                'update_existing'   => true,
                                'replace_interests' => false,
                                'send_welcome'      => false,
                            ));
                //print_r($result);
            }

            //now retrieve last inserted id
            //$query = 'SELECT MAX(registration_id) as registration_id FROM j3_ts_register;';
            $query = 'SELECT LAST_INSERT_ID() as registration_id;';

            $db = Factory::getDBO();
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
          else
          {

               $registration_id = 0;
               foreach ($rows as $row)
               {
                   $registration_id=$row->registration_id;
               }

                //Grabbing tournament id and age id from dropdown value
                //Save tournament and age registration seperately
                if($registration_id !=0)
                {
                    //create a session variable to hold value of most recent registration value
                    $_SESSION['registration_id'] = $registration_id;
                    $arr_tourn = array($tournament_id_1, $tournament_id_2, $tournament_id_3, $tournament_id_4, $tournament_id_5);
                    $arr_age = array($age_id_1, $age_id_2, $age_id_3, $age_id_4, $age_id_5);
					$arr_age_waitlist = array($age_id_1_waitlist, $age_id_2_waitlist, $age_id_3_waitlist, $age_id_4_waitlist, $age_id_5_waitlist);
					
                    foreach ($arr_tourn as $i => $value) {
                        $tourn = "";
                        $age="";

                        if($arr_tourn[$i]!='-1')
                        {
							
                            $tourn = $arr_tourn[$i];
                            $age = $arr_age[$i];
							$age_waitlist = $arr_age_waitlist[$i];

                            $tourn = filter_var(trim($tourn), FILTER_SANITIZE_STRING);
                            $age = filter_var(trim($age), FILTER_SANITIZE_STRING);
                            $registration_id = filter_var(trim($registration_id), FILTER_SANITIZE_STRING);
                            
                            $query = 'INSERT INTO j3_ts_register_tourn (tournament_id, age_id, waitlist, register_id) ';
                            $query = $query . "VALUES (".  ($tourn) . ",";
                            $query = $query .   ($age) . ",";
							$query = $query .   ($age_waitlist) . ",";
                            $query = $query .  ($registration_id) . ");";

                            $db = Factory::getDBO();
                            $db->setQuery($query);
                            $result = $db->execute();

                            if ($db->getErrorNum > 0){
                                $message = "DB Error: ";
                                $message .= $db->getErrorMsg();
                                $message .= 'Whole query: ' . $query;
                                $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
                                die($message);
                          }
                        }
                    }
                        
                    //create email to send
                    mod_ts_tourn_register::SendTournEmail($registration_id);
                        
                }

            }

            //update mode to payment for next step
            mod_ts_tourn_register::SetRegisterMode('payment');

        }
    }
    
    public static function SendTournEmail($registration_id)
    {
        
        $registration_id = filter_var(trim($registration_id), FILTER_SANITIZE_STRING);  
        
        //get tournament registration data
        $query = "select * from j3_ts_register where id=".$registration_id;
        
        $e_html = "<div style='font-family: arial;'>"."\r\n";
        $e_html .= "<h1>Tournament Registration - ".date("m.d.y")."</h1>"."\r\n";
        
        $email_from = "info@5starfastpitch.com";
        $email_from_name = "Tournament Registration";
        $team_name = "";
        
        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        //construct email text
        foreach($rows as $row)
        {
            
            //$email_from = $row->email_1;
            //$email_from_name = $row->team_manager_1;
            $email_from_name = "Tournament Registration - " . $row->team_manager_1;
            $team_name = $row->team_name;
            
            $e_html .= "<h2>Registration Details</h2>"."\r\n";
            $e_html .= '<table cellpadding="5" cellspacing="0">'."\r\n";
            $e_html .= "<tr><td>Team Name:</td><td>" . $row->team_name . "</td></tr>"."\r\n";
            $e_html .= "<tr><td>Level of Play:</td><td>" . $row->level_play . "</td></tr>";
            $e_html .= "<tr><td>Team Manager 1:</td><td>" . $row->team_manager_1 . "</td></tr>"."\r\n";
            $e_html .= "<tr><td>Team Manager 2:</td><td>" . $row->team_manager_2 . "</td></tr>"."\r\n";
            $e_html .= "<tr><td>Team Address:</td><td>" . $row->team_address . "</td></tr>"."\r\n";
            $e_html .= "<tr><td>Team City:</td><td>" . $row->team_city . "</td></tr>"."\r\n";
            $e_html .= "<tr><td>Team State:</td><td>" . $row->team_state . "</td></tr>"."\r\n";
            $e_html .= "<tr><td>Team Zip:</td><td>" . $row->team_zip . "</td></tr>"."\r\n";
            $e_html .= "<tr><td>Home Phone:</td><td>" . $row->home_phone . "</td></tr>"."\r\n";
            $e_html .= "<tr><td>Cell Phone 1:</td><td>" . $row->cell_phone_1 . "</td></tr>"."\r\n";
            $e_html .= "<tr><td>Cell Phone 2:</td><td>" . $row->cell_phone_2 . "</td></tr>"."\r\n";
            $e_html .= "<tr><td>Email 1:</td><td>" . $row->email_1 . "</td></tr>"."\r\n";
            $e_html .= "<tr><td>Email 2:</td><td>" . $row->email_2 . "</td></tr>"."\r\n";
            $e_html .= "<tr><td>Comments:</td><td>" . $row->comments . "</td></tr>"."\r\n";
            $e_html .= "<tr><td>Date Submitted:</td><td>" . $row->date_submitted . "</td></tr>"."\r\n";
            $e_html .= '</table>'."\r\n";
            
        }
        
        //get selected tournaments
        $query = 'SELECT t.id as tournament_id, t.tournament_name, t.tournament_start_date, t.tournament_end_date, t.tournament_description, a.age, ac.tournament_cost 
        FROM j3_ts_register_tourn tr
        inner join j3_ts_register r on r.id=register_id
        inner join j3_ts_tournament t on t.id=tr.tournament_id 
        inner join j3_ts_age a on a.age_id=tr.age_id 
        inner join j3_ts_tournament_age_cost ac on ac.tournament_id=t.id and ac.age_id=a.age_id         
        WHERE r.id = '.$registration_id;
        
        $db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        //construct email text
        $e_html .= "<h2>Tournament(s) Registered</h2>"."\r\n";
        $e_html .= '<table cellpadding="5" cellspacing="0">'."\r\n";
        $e_html .= '<tr style="font-weight: bold;"><td>Tournament</td><td>Start Date</td><td>End Date</td><td>Age</td><td>Cost</td></tr>'."\r\n";
        $total_cost = 0;
        
        foreach($rows as $row)
        {
            $e_html .= "<tr><td>" . $row->tournament_name . "</td>";
            $e_html .= "<td>" . $row->tournament_start_date . "</td>";
            $e_html .= "<td>" . $row->tournament_end_date . "</td>";
            $e_html .= "<td>" . $row->age . "</td>";
            $e_html .= "<td>" . $row->tournament_cost . "</td></tr>"."\r\n";
            
            //total cost
            $total_cost = $total_cost + floatval($row->tournament_cost);
            
        }
        
        $e_html .= '<tr style="font-weight: bold;"><td></td><td></td><td></td><td></td><td>Total $'.$total_cost.'</td></tr>'."\r\n";
        $e_html .= "</table>"."\r\n";
        
        $e_html .= "</div>";
        
        //Send Email
        $email_to = "mhoisington@ohiobaseball.com";
        $cc_to = "support@myteaminn.com";
        $subject = "5starfastpitch.com registration - ". $team_name; 
        
		$mail = Factory::getMailer();
		
        //to
		$success = $mail->sendMail($email_from, $email_from_name, $email_to, $subject, $e_html, 1);
				
        //cc
		$success = $mail->sendMail($email_from, $email_from_name, $cc_to, $subject, $e_html, 1);
        
    }

}

?>