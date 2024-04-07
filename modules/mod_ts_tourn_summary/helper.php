<?php
/**
 * Helper class for Maxiem Front Page module
 * 
 */
 
 // no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route; 
class mod_ts_tourn_summary
{

    public static function GetCurrentSeasonName()
    {

        $query = "SELECT * FROM #__ts_season WHERE season_current = 1 ";
        $s_year = '';
        $db = JFactory::getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        foreach ($rows as $row)
        {
            $s_year = $row->season_name;
        }

        return $s_year;

    }

    //create html to display current tournaments
    public static function GetCurrentTournSummary()
    {
			
        $db = JFactory::getDbo();
        $query = "SELECT * FROM #__ts_tournament t ";
        $query .= "WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) AND
        t.season_id in (SELECT season_id FROM #__ts_season WHERE season_current = 1) ";
        $query .= " ORDER BY tournament_start_date";
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $html = '';
        $rowCount = 0;
		
                foreach ($rows as $row)
                {
                    $css = "odd_row";
                        if ($rowCount%2 == 0)
                            {
                            $css = "even_row";

                            }
                                    //format times from mysql
                        $start_date = strtotime($row->tournament_start_date);
                        $start_date = date("m/d",$start_date);

                        $end_date = strtotime($row->tournament_end_date);
                        $end_date = date("m/d",$end_date);
                        $linkinfo = Route::_('index.php?option=com_ts&view=info&tournament_id='.$row->id,false);        
               
						
						
						   $html = $html. '<div class="row '.$css.'"><div class="col-xs-7 col-sm-6"><a href="'.$linkinfo.'">'. $row->tournament_name .'</a> </div>';
                        $html = $html. '<div class="col-xs-3 col-sm-3">'. $start_date .'-'. $end_date .'</div>';
                        $html = $html. '<div class="col-xs-2 col-sm-3"><a href="index.php?option=com_ts&view=results&tournament_id='.$row->id.'"><img src="modules/mod_ts_tourn_summary/images/schedule.png" style="padding-left: 35px;"/></a></div></div>';
						
                        $rowCount++;
                }
		
		
		
        return $html;
    }
}

?>