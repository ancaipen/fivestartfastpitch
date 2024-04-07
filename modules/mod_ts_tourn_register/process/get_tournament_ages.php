<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

require('../../../configuration.php');

$tournament_id = "";
$json = "";
   
//find any tournament id
if(isset($_GET['tournament_id']))
{
   $tournament_id = $_GET['tournament_id'];
}

//get results from db
if($tournament_id != "")
{
    
    $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_STRING);  
            
    //get config settings
    $c = new JConfig();

    //make connection to db
	$con = new mysqli($c->host, $c->user, $c->password, $c->db);

	if ($con->connect_errno) {
		echo "Error: Failed to make a MySQL connection, here is why: \n";
		echo "Errno: " . $mysqli->connect_errno . "\n";
		echo "Error: " . $mysqli->connect_error . "\n";
		exit;
	}
	
    $sql = "select * from jos_ts_tournament_age_cost tac
    INNER JOIN jos_ts_age a on a.age_id=tac.age_id
    WHERE tournament_id = ".($tournament_id). "
    ORDER BY a.age_num ";

    $result = $con->query($sql);
    
    $json = "[";
    $rowCount = 0;

    $json .= '{ "optionValue" : "-1",';
    $json .= '"optionDisplay" : "Select One"},';

    while ($row = $result->fetch_assoc())
    {
		$over_capacity = CheckTournamentCapacity($tournament_id, $row['age_id']);
		$tourn_desc = trim($row['age']).' $'.trim($row['tournament_cost']);
		$tourn_value = trim($row['age_id']);
		
		if($over_capacity)
		{
			$tourn_desc .= ' *Tournament is over capacity, you will be added to waitlist.*';
			$tourn_value .= '_WAITLIST'; 
		}
		
        $json .= '{ "optionValue" : "'.$tourn_value.'",';
        $json .= '"optionDisplay" : "'.$tourn_desc.'" },';
        $rowCount++;
    }

    $json = substr($json, 0, -1) . "]";
	
	//write out json response
	echo $json;

}

function CheckTournamentCapacity($tournament_id, $age_id, $tourn_capacity = -1)
{

    //get config settings
    $c = new JConfig();
	
	//make connection to db
	$con = new mysqli($c->host, $c->user, $c->password, $c->db);

	if ($con->connect_errno) {
		echo "Error: Failed to make a MySQL connection, here is why: \n";
		echo "Errno: " . $mysqli->connect_errno . "\n";
		echo "Error: " . $mysqli->connect_error . "\n";
		exit;
	}
			
	$tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_STRING);
	$age_id = filter_var(trim($age_id), FILTER_SANITIZE_STRING);
	$over_capacity = false;
	$tourn_total = 0;
	
	//lookup capacity as it has not been specified
	if($tourn_capacity == -1)
	{
		
		$tourn_capacity = 0;
		
		//get tournament capacity
		$query = "SELECT ta.tourn_capacity FROM jos_ts_tournament t 
		INNER JOIN jos_ts_tournament_age_cost ta on ta.tournament_id=t.tournament_id
		INNER JOIN jos_ts_season s on s.season_id=t.season_id 
		WHERE s.season_current = 1 
		AND (t.is_deleted = 0 OR t.is_deleted IS NULL) 
		AND ta.tourn_capacity IS NOT NULL ";
		
		$query .= "AND ta.tournament_id = ".$tournament_id. " ";
		$query .= "AND ta.age_id = ".$age_id. " ";       
		
		$result = $con->query($query);
		
		while ($row = $result->fetch_assoc())
		{
			$tourn_capacity  = filter_var(trim($row['tourn_capacity']), FILTER_SANITIZE_NUMBER_INT);
		}
		
	}
			
	//if greater than 0, calculate current registration
	if($tourn_capacity > 0)
	{

		$query = "SELECT count(*) as reg_count FROM jos_ts_register_tourn rt 
		INNER JOIN jos_ts_register r on r.registration_id=rt.register_id 
		INNER JOIN  jos_ts_tournament t on t.tournament_id = rt.tournament_id
		INNER JOIN jos_ts_season s on s.season_id=t.season_id 
		WHERE s.season_current = 1 
		AND (t.is_deleted = 0 OR t.is_deleted IS NULL) 
		AND (rt.waitlist IS NULL OR rt.waitlist = 0)
		AND r.reg_status <> 'Deleted' ";
		
		$query .= "AND rt.tournament_id = ".$tournament_id. " ";
		$query .= "AND rt.age_id = ".$age_id. " ";     
		
		$result = $con->query($query);
		
		while ($row = $result->fetch_assoc())
		{
			$tourn_total  = filter_var(trim($row['reg_count']), FILTER_SANITIZE_NUMBER_INT);
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

?>