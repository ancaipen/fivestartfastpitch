<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Factory;
use Joomla\Input\Input;

jimport( 'joomla.application.component.model' );

class TSAdminModelGame extends JModelLegacy
{

    public static function setAllGameToolbar()
    {
        JToolBarHelper::title('Game Manager', 'generic.png');
        JToolBarHelper::addNew();
        JToolBarHelper::deleteList();
		JToolBarHelper::publish();
		JToolBarHelper::unpublish();
    }

     public static function setGameToolbar($id)
    {
        if ($id) {
                $newEdit = 'Edit';
        } else {
                $newEdit = 'New';
        }

        JToolBarHelper::title($newEdit . ' Game', 'generic.png');
        JToolBarHelper::save();
        JToolBarHelper::apply();
        JToolBarHelper::cancel();

    }

    public static function getGameData($tournament_id = "-1", $age_id = "-1")
    {

        $tournament_id = InputFilter::getInstance()->clean($tournament_id, 'STRING');
        //$tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_STRING);
        $age_id = InputFilter::getInstance()->clean(trim($age_id), 'STRING');
        //$age_id = filter_var(trim($age_id), FILTER_SANITIZE_STRING);
        
        $query = "SELECT *, DATE_FORMAT(game_time, '%h:%i %p') AS game_time_f FROM jos_ts_games c
        INNER JOIN jos_ts_tournament t on t.tournament_id=c.tournament_id
        INNER JOIN jos_ts_age a on a.age_id=c.age_id
        INNER JOIN jos_ts_season s on s.season_id=t.season_id
        WHERE s.season_current = 1 ";

        //filter results if selected, tournament_id
        if($tournament_id != -1)
        {
            $query .= "AND t.tournament_id = ".($tournament_id)." ";
        }
        //age_id
        if($age_id != -1)
        {
            $query .= "AND a.age_id = ".($age_id)." ";
        }
        
        $query .= "ORDER BY tournament_name,age,game_date, game_time";

        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        return $rows;

    }

     public static function getGameDataForm($game_id)
    {
        $input = Factory::getApplication()->input;
        if(is_null($game_id) || $game_id == ''){
            $game_id = $input->get('game_id');    
        }
        
      
        //$game_id = filter_var(trim($game_id), FILTER_SANITIZE_STRING);
        $rows = null;
		
		if($game_id != null)
		{
			$query = "SELECT *, DATE_FORMAT(game_time, '%h:%i %p') AS game_time_f FROM jos_ts_games c
			INNER JOIN jos_ts_tournament t on t.tournament_id=c.tournament_id
			INNER JOIN jos_ts_age a on a.age_id=c.age_id
			INNER JOIN jos_ts_season s on s.season_id=t.season_id
			WHERE s.season_current = 1 AND game_id = ".($game_id);
			$query .= " ORDER BY t.tournament_name, a.age, c.game_date, c.game_time";

			$db = JFactory::getDBO();
			$db->setQuery($query);
			$rows = $db->loadObjectList();
		}

        return $rows;

    }
    
    public static function buildPool($pool, $id)
    {
        $html = '<select name="'.$id.'" id="'.$id.'">';
        $poolvals=array('','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
        foreach ($poolvals as &$value) {

            if ($pool == $value)
            {
            $html = $html. '  <option value="'.$value.'" SELECTED>'.$value.'</option>';

            }
            else
            {
            $html = $html. '  <option value="'.$value.'">'.$value.'</option>';
            }
        }
        $html = $html .'</select>';
        return $html;


    }

    public static function buildGameType($game_type)
    {
        $html = '<select name="game_type" id="game_type">';
        $game_typevals=array('Pool','Silver Bracket' ,'Silver Bracket Game 1' ,'Silver Bracket Game 2' ,'Silver Bracket Game 3' ,'Silver Bracket Game 4' ,'Silver Bracket Game 5' ,'Silver Bracket Game 6','Silver Bracket Game 7' ,'Silver Bracket Game 8','Silver Bracket Game 9','Silver Bracket Game 10','Silver Bracket Game 11','Silver Bracket Game 12','Silver Bracket Game 13','Silver Bracket Game 14','Silver Bracket Game 15','Silver Bracket Game 16','Consolation', 'Consolation Game 1', 'Consolation Game 2', 'Consolation Game 3', 'Consolation Game 4', 'Consolation Game 5', 'Consolation Game 6', 'Consolation Game 7','Consolation Game 8','Consolation Game 9','Consolation Game 10','Consolation Game 11','Consolation Game 12','Consolation Game 13','Consolation Game 14','Consolation Game 15','Consolation Game 16','Consolation Game 17','Consolation Game 18','Consolation Game 19','Consolation Game 20','Championship','Bronze Bracket','Bronze Bracket 1','Bronze Bracket 2','Bronze Bracket 3','Bronze Bracket 4','Bronze Bracket 5','Bronze Bracket 6','Bronze Bracket 7','Bronze Bracket 8','Bronze Bracket 9','Bronze Bracket 10','Bronze Bracket 11','Bronze Bracket 12','Bronze Bracket 13','Bronze Bracket 14','Bronze Bracket 15','Bronze Bracket 16', 'Championship Game 1','Championship Game 2','Championship Game 3','Championship Game 4','Championship Game 5','Championship Game 6', 'Championship Game 7', 'Championship Game 8', 'Championship Game 9', 'Championship Game 10', 'Championship Game 11', 'Championship Game 12', 'Championship Game 13', 'Championship Game 14', 'Championship Game 15', 'Championship Game 16', 'Championship Game 17', 'Championship Game 18', 'Championship Game 19', 'Championship Game 20', 'Championship Game 21', 'Championship Game 22', 'Championship Game 23', 'Championship Game 24', 'Championship Game 25');
        foreach ($game_typevals as &$value) {

            if ($game_type == $value)
            {
            $html = $html. '  <option value="'.$value.'" SELECTED>'.$value.'</option>';

            }
            else
            {
            $html = $html. '  <option value="'.$value.'">'.$value.'</option>';
            }
        }
        $html = $html .'</select>';
        return $html;


    }

    public static function CheckActiveTournaments()
    {

        $query = "SELECT tournament_name FROM jos_ts_tournament t
        INNER JOIN jos_ts_season s on s.season_id=t.season_id
        WHERE s.season_current = 1 AND (t.is_deleted = 0 OR t.is_deleted IS NULL) ";

        $found = false;
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $row_count = count($rows);

        if($row_count > 0)
        {
            $found = true;
        }

        return $found;

    }

public static function buildTournamentName($tournament_id, $add_all = false)
{
    $tournament_id = InputFilter::getInstance()->clean($tournament_id, 'STRING');
    //$tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_STRING);
    
    $html = '<select name="tournament_id" id="tournament_id">';
     $query = "SELECT * FROM jos_ts_tournament t
    INNER JOIN jos_ts_season s on s.season_id=t.season_id
    WHERE s.season_current = 1";

    //if all selection is needed all it
    if($add_all){
        $html = $html. '  <option value="-1">ALL</option>';
    }

    $db = JFactory::getDBO();
    $db->setQuery($query);
    $rows = $db->loadObjectList();
     foreach ($rows as $row)
        {
        if ($tournament_id == $row->tournament_id)
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

public static function buildTournamentAge($age_id,$add_all = false)
{

    $html = '<select name="age_id" id="age_id">';
    $query = "SELECT * FROM jos_ts_age
    ORDER BY age_num";

    //if all selection is needed all it
    if($add_all){
        $html = $html. '  <option value="-1">ALL</option>';
    }

    $db = JFactory::getDBO();
    $db->setQuery($query);
    $rows = $db->loadObjectList();

    foreach ($rows as $row)
    {
    if ($age_id == $row->age_id)
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

public static function InsertGame($game_id,$game_active,$home_team,$visitor_team,$home_pool,$game_date,$game_type,
$field_location,$home_score,$visitor_score,$notes,$age_id,$tournament_id,$visitor_pool,$game_time,$game_order='')
{

    $game_time_new =  date('Y-m-d G:i:s', strtotime("01/01/1970".$game_time));

    $query = "INSERT INTO jos_ts_games (
    home_team,
    visitor_team,
    home_pool,
    game_date,
    game_type,
    field_location,";

    if($home_score != ""){
        $query .= "home_score,";
    }
    if($visitor_score != ""){
        $query .= "visitor_score,";
    }
    if($notes != ""){
        $query .= "notes,";
    }
    if($game_order != ""){
        $query .= "game_order,";
    }

     $query .= "age_id,
    tournament_id,
    visitor_pool,
    game_time,
    game_active
    )
    VALUES
    (
    '".($home_team)."',
    '".($visitor_team)."',
    '".($home_pool)."',
    '".($game_date)."',
    '".($game_type)."',
    '".($field_location)."',";

    if($home_score != ""){
        $query .= ($home_score).",";
    }
    if($visitor_score != ""){
        $query .= ($visitor_score).",";
    }
    if($notes != ""){
        $query .= "'".($notes)."',";
    }
    if($game_order != ""){
        $query .= "'".($game_order)."',";
    }

    $query .= ($age_id).",
    ".($tournament_id).",
    '".($visitor_pool)."',
    '".($game_time_new)."',
    ".($game_active)."
    )";

    $db = JFactory::getDBO();
    $db->setQuery($query);
    $result = $db->execute();

    /* if ($db->getErrorMsg() != ""){
         $message = "DB Error: ";
         $message .= $db->getErrorMsg();
         $message .= 'Whole query: ' . $query;
         $message .= '<div style="clear:both;border:solid 3px #ccc;"></div>';
         echo $message;
     }*/

    $query = "SELECT * FROM jos_ts_games
    WHERE game_id=LAST_INSERT_ID()";
    $db = JFactory::getDBO();
    $db->setQuery($query);
    $rows = $db->loadObjectList();

    return $rows;

}

public static function DeleteGame($game_id)
{

    $game_id = filter_var(trim($game_id), FILTER_SANITIZE_STRING);

    $query = "DELETE FROM jos_ts_games
    WHERE game_id = ".($game_id);

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

public static function PublishGame($game_id, $game_active = '1')
{

    $game_id = filter_var(trim($game_id), FILTER_SANITIZE_STRING);

    $query = "UPDATE jos_ts_games ";
    $query .= "SET game_active = ". $game_active . " ";
    $query .= "WHERE game_id = ".($game_id);

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

public static function UpdateGame($game_id,$game_active,$home_team,$visitor_team,$home_pool,$game_date,$game_type,
$field_location,$home_score,$visitor_score,$notes,$age_id,$tournament_id,$visitor_pool,$game_time,$game_order='')
{

    //convert date time
    $game_time_new =  date('Y-m-d G:i:s', strtotime("01/01/1970".$game_time));

    //allow for notes html
     $notes=Factory::getApplication()->input->get( 'notes', '', 'RAW');

    $query = "UPDATE jos_ts_games SET
    home_team='".($home_team)."',
    visitor_team='".($visitor_team)."',
    home_pool='".($home_pool)."',
    game_date='".($game_date)."',
    game_type='".($game_type)."',
    field_location='".($field_location)."',";

    if($home_score != ""){
        $query .= "home_score=".($home_score).",";
    }
    else
    {
        $query .= "home_score=NULL,";
    }

    if($visitor_score != ""){
        $query .= "visitor_score=".($visitor_score).",";
    }
    else
    {
        $query .= "visitor_score=NULL,";
    }

    if($notes != ""){
        $query .= "notes='".($notes)."',";
    }
    if($game_order != ""){
        $query .= "game_order='".($game_order)."',";
    }
    
    $query .= "age_id=".($age_id).",
    tournament_id=".($tournament_id).",
    visitor_pool='".($visitor_pool)."',
    game_time='".($game_time_new)."',
    game_active=".($game_active)."
    WHERE game_id=".($game_id);

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

}



?>
