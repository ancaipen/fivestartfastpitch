<?php

defined ('_JEXEC') or die ('restricted access');
jimport('joomla.application.component.model');

class ModelTSInfo extends JModelLegacy
{

    var $_html = null;

    public function __construct()
    {
      parent::__construct();
    }

    public static function GetScheduleInfo($tournament_id='')
    {
		
		$tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
        $html = "";

        if($tournament_id!='')
        {
            
            $html = ModelTSInfo::GetTournamentHtml($tournament_id, false);
            $html .= ModelTSInfo::GetMenuHtml($tournament_id);
        }
        else if($tournament_id=='')
        {
          
            $html = ModelTSInfo::GetAllTournaments();
        }

        return $html;

    }

    public static function GetMenuHtml($tournament_id='')
    {
		
		$tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
		
        $html = '<div class="tourn_menu">';
        $html .= '<ul>';
        $html .= '<li><a href="/register.html"  class="tp-button home-slider-button-blue">Register Now</a></li>';
        $html .= '<li><a href="index.php?option=com_ts&view=results&tournament_id='.$tournament_id.'"  class="tp-button home-slider-button">Schedule/Results</a></li>';
        $html .= '</ul></div>';

        return $html;
    }

    public static function GetAllTournaments()
    {
        $html = "";
        $query = "SELECT * FROM jos_ts_tournament t ";
        $query .= "WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL)
        t.season_id in (SELECT season_id FROM jos_ts_season WHERE season_current = 1)
        ORDER BY tournament_start_date";

        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        foreach ($rows as $row)
        {
            $html .= ModelTSInfo::GetTournamentHtml($row->tournament_id, true);

        }

        return $html;
    }

    public static function GetTournamentHtml($tournament_id, $display_link)
    {

        $html = "";
        $html_age = "";
        
        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
        
        //Get Age info
        $query = "SELECT * FROM jos_ts_tournament_age_cost
        INNER JOIN jos_ts_age on jos_ts_age.age_id = jos_ts_tournament_age_cost.age_id
        INNER JOIN jos_ts_tournament t on t.tournament_id = jos_ts_tournament_age_cost.tournament_id
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) AND
        t.season_id in (SELECT season_id FROM jos_ts_season WHERE season_current = 1) AND
        jos_ts_tournament_age_cost.tournament_id = ".$tournament_id;

        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $html_age = $html_age .'<div style="padding-top: 5px">';
        $html_age = $html_age . '<span style="font-weight: bold;">Age Group(s) and Cost:&nbsp;</span>';

        foreach ($rows as $row)
        {
            $html_age = $html_age . ' ' . $row->age . ' ($' . $row->tournament_cost . ') , ';

        }

        $html_age = $html_age . '</div>';

        //get data back from database and create html to display tournament details
        $query = "SELECT * FROM jos_ts_tournament WHERE tournament_id = ".($tournament_id);
        

        $db = JFactory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        foreach ($rows as $row)
        {
            $html = $html .'<div class="padding"><h3 class="tourn_header">';

            if($display_link == true)
                {
                 $html = $html .'<a href="index.php?option=com_ts&view=info&tournament_id='.$row->tournament_id.'">' . $row->tournament_name .'</a>';
                }
             else{
               $html = $html  . $row->tournament_name;
                 }
           
            $html = $html .'</h3>';

            //format times from mysql
            $start_date = strtotime($row->tournament_start_date);
            $start_date = date("m/d/y",$start_date);

            $end_date = strtotime($row->tournament_end_date);
            $end_date = date("m/d/y",$end_date);

            $html = $html .'<span style="font-weight:bold">Tournament Dates: </span> ' . $start_date .' - ' . $end_date .' ';
            $html = $html . $html_age;

            //Displays read more on info summary page
            if($display_link == true)
            {
                $html = $html .'<div style="padding: 10px 0 10px 0;">';
                $short_desc = explode ('.' ,$row->tournament_description);
                $html = $html . $short_desc[0].'. ' . $short_desc[0] . '. &nbsp;<a href="index.php?option=com_ts&view=info&tournament_id='.$row->tournament_id.'" style="font-weight: bold;">read more</a> ...';
                $html = $html . '</div>';
            }
            else
            {
            $html = $html .'<div style="padding: 10px 0 10px 0;">' . $row->tournament_description .'</div>';
            }

            //Displays Team Register on info details page.  Displays bottom border on info summary page.
            if($display_link == false)
            {
            $html = $html . '<span style="font-weight:bold">Teams Registered: </span><div style="padding: 10px 0 10px 0;">' . $row->teams_registered .'</div>';
            }
            }
             if($display_link == true)
                {
                 $html = $html .'<div style="border-bottom: solid 1px #cbcfd4">&nbsp;</div></div>';
                }
     
        return $html;
    }

}

?>
