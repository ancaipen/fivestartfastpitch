<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Ts
 * @author     Percept <perceptinfotech2@gmail.com>
 * @copyright  2023 Percept
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Teamtournaments\Component\Ts\Site\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Layout\FileLayout;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;
use \Teamtournaments\Component\Ts\Site\Helper\TsHelper;


/**
 * Methods supporting a list of Ts records.
 *
 * @since  1.0.0
 */
class ResultsModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see    JController
	 * @since  1.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by',
				'tournament_name', 'a.tournament_name',
				'tournament_start_date', 'a.tournament_start_date',
				'tournament_end_date', 'a.tournament_end_date',
				'tournament_description', 'a.tournament_description',
				'teams_registered', 'a.teams_registered',
				'season_id', 'a.season_id',
				'tournament_notes', 'a.tournament_notes',
				'is_deleted', 'a.is_deleted',
				'tournament_complete', 'a.tournament_complete',
			);
		}

		parent::__construct($config);
	}

	

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState("a.id", "ASC");

		$app = Factory::getApplication();
		$list = $app->getUserState($this->context . '.list');

		$value = $app->getUserState($this->context . '.list.limit', $app->get('list_limit', 25));
		$list['limit'] = $value;
		
		$this->setState('list.limit', $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

		$ordering  = $this->getUserStateFromRequest($this->context .'.filter_order', 'filter_order', "a.id");
		$direction = strtoupper($this->getUserStateFromRequest($this->context .'.filter_order_Dir', 'filter_order_Dir', "ASC"));
		
		if(!empty($ordering) || !empty($direction))
		{
			$list['fullordering'] = $ordering . ' ' . $direction;
		}

		$app->setUserState($this->context . '.list', $list);

		

		$context = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $context);

		// Split context into component and optional section
		if (!empty($context))
		{
			$parts = FieldsHelper::extract($context);

			if ($parts)
			{
				$this->setState('filter.component', $parts[0]);
				$this->setState('filter.section', $parts[1]);
			}
		}
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery()
	{
			// Create a new query object.
			$db    = $this->getDbo();
			$query = $db->getQuery(true);

			// Select the required fields from the table.
			$query->select(
						$this->getState(
								'list.select', 'DISTINCT a.*'
						)
				);

			$query->from('`#__ts_tournament` AS a');
			
		// Join over the users for the checked out user.
		$query->select('uc.name AS uEditor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		// Join over the created by field 'modified_by'
		$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');
			
		if (!Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_ts'))
		{
			$query->where('a.state = 1');
		}
		else
		{
			$query->where('(a.state IN (0, 1))');
		}

			// Filter by search in title
			$search = $this->getState('filter.search');

			if (!empty($search))
			{
				if (stripos($search, 'id:') === 0)
				{
					$query->where('a.id = ' . (int) substr($search, 3));
				}
				else
				{
					$search = $db->Quote('%' . $db->escape($search, true) . '%');
				}
			}
			

			
			
			// Add the list ordering clause.
			$orderCol  = $this->state->get('list.ordering', "a.id");
			$orderDirn = $this->state->get('list.direction', "ASC");

			if ($orderCol && $orderDirn)
			{
				$query->order($db->escape($orderCol . ' ' . $orderDirn));
			}

			return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = Factory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(Text::_("COM_TS_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	public function GetTournamentResults($tournament_id)
    {
        $tournament_id = (int) strip_tags(trim($tournament_id));
        $html = "";

        //get data back from database and create html to display tournament details
        $query = "SELECT * FROM #__ts_tournament_age_cost
        INNER JOIN #__ts_age on #__ts_tournament_age_cost.age_id = #__ts_age.age_id
        INNER JOIN #__ts_tournament t on t.id = #__ts_tournament_age_cost.tournament_id
        INNER JOIN #__ts_season s on s.season_id=t.season_id
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) AND
        #__ts_tournament_age_cost.tournament_id =" .($tournament_id). " AND t.season_id in (SELECT season_id FROM #__ts_season WHERE season_current = 1) ";
        $query .= " ORDER BY age_num, age";
       
        //$query = "SELECT * FROM #__ts_tournament
        //WHERE tournament_id =" .($tournament_id) . "and tournament_name =" .($tournament_name) ;

		$db    = $this->getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		//$db->freeResult();
		
        $rowCount = 0;
        $html = $html . "<div class = 'age_landing_header'>Select a Division to view your team's Schedule and Results</div>";

        $html = $html . '<table width="500px" cellspacing="0" cellpadding="0" class="age_selection_table">';
		 
        foreach ($rows as $row)       
        {
            $css = "age_row_1";
            if ($rowCount%2 == 0)
            {
                $css = "age_row_2";
            }

            $html = $html . '<tr><td class="'.$css.'">';
            $html = $html .'<div class="age_links">
			<a href="index.php?option=com_ts&view=results&tournament_id='.$row->tournament_id.'&age_id='.$row->age_id.'">'.$row->tournament_name  . '&nbsp;-&nbsp;' .$row->age .' Division</a></div>';
            $html = $html . '</td></tr>';

            $rowCount++;
        }

        $html = $html . '</table>';

        return $html;
    }
	public function GetScheduleResults($tournament_id, $age_id)
    {
        
        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
        $age_id = filter_var(trim($age_id), FILTER_SANITIZE_NUMBER_INT);
        
        $html = "";
        $html .= $this->GetTournamentText($tournament_id, $age_id);
        $html .= $this->GetPoolText($tournament_id, $age_id);
        $html .= $this->GetFieldText($tournament_id, $age_id);
        $html .= $this->GetTournFiles($tournament_id, $age_id);
        
        //get data back from database and create html to display tournament details
        $query = "SELECT *, DATE_FORMAT(g.game_time, '%h:%i %p') as game_time_new FROM #__ts_games g
        INNER JOIN #__ts_tournament t on g.tournament_id=t.id
        INNER JOIN #__ts_season s on s.season_id=t.season_id
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) AND g.game_active = 1 AND 
        g.tournament_id = ".($tournament_id)." and age_id = ".($age_id);
        $query .= " AND t.season_id in (SELECT season_id FROM #__ts_season WHERE season_current = 1) ";
        $query .= " ORDER BY g.game_date asc, g.game_order, g.game_type desc, g.game_time asc ";

        $db    = $this->getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		
        $curr_date = '';
        $row_count = 0;
        $rowCount = 0;
		
        foreach ($rows as $row)
        {

            $game_date = strtotime($row->game_date);
            $game_date = date("F j, Y",$game_date);

            $game_time = strtotime($row->game_time_new);
            $game_time = date("g:i a",$game_time);

            $home_score = '';
            $visitor_score = '';
            if($row->home_score != -1)
            {
                $home_score = $row->home_score;
            }

            if($row->visitor_score != -1)
            {
                $visitor_score = $row->visitor_score;
            }
            $css = "row_style1";
            if ($rowCount%2 == 0)
            {
                 $css = "row_style";
            }
            if($curr_date!=$row->game_date)
            {
                $html = $html . '<div class="results_title">'. $game_date.'</div>';
                $html = $html . '<table width="100%" border="0" cellpadding="2" cellspacing="0" class="schedule_table2">';
                $html = $html . '<tr><td class="schedule_style" style="width: 55px;">Type</td><td class="schedule_style">Team 1</td><td class="schedule_style2">&nbsp;</td><td class="schedule_style">Team 2</td><td class="schedule_style2">&nbsp;</td><td class="schedule_style">Time</td><td class="schedule_style">Field</td></tr>';
                
                $curr_date=$row->game_date;
            }

            $html = $html . '<tr class="'.$css.'"><td class="row_style2">'. $row->game_type .'</td><td class="row_style2">' . $row->home_team . '</td><td class="schedule_style4" style="font-weight: bold; padding-right: 10px; text-align: center; width=15px;">'. $home_score.'</td><td class="row_style2">' . $row->visitor_team . '</td><td class="schedule_style4" style="font-weight: bold; padding-right: 10px; text-align: center; width=15px;">'.$visitor_score.'</td><td class="schedule_style2" style="width: 55px;">' . $game_time . '</td><td class="schedule_style2">' . $row->field_location . '</td></tr>';
            $rowCount++;
            if($rows[$row_count+1]->game_date!=$curr_date)
            {
                $html = $html . '</table>';
            }
            $row_count++;
        }

        return $html;
    }
	public function GetTournamentText($tournament_id, $age_id)
    {

        $html = "";
        $html_age = "";

        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
        $age_id = filter_var(trim($age_id), FILTER_SANITIZE_NUMBER_INT);
               
        //Get Age info
        $query = "SELECT * FROM #__ts_tournament_age_cost ac
        INNER JOIN #__ts_age a on a.age_id = ac.age_id 
        INNER JOIN #__ts_tournament t on ac.tournament_id=t.id
        INNER JOIN #__ts_season s on s.season_id=t.season_id
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) AND
        t.season_id in (SELECT season_id FROM #__ts_season WHERE season_current = 1) AND ac.tournament_id = ".($tournament_id). " and ac.age_id = " .($age_id);

		$db    = $this->getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		
		//$db->freeResult();
		
        $html_age = $html_age .'<div style="font-family: arial; font-size: 12px;">';
   
        foreach ($rows as $row)
        {
            $html_age = $html_age . '<div class="schedule_age"> ' . $row->age . ' Division</div>';

        }

        $html_age = $html_age . '</div>';

        //get data back from database and create html to display tournament details
        $query = "SELECT * FROM #__ts_tournament t
        INNER JOIN #__ts_season s on s.season_id=t.season_id 
        WHERE t.season_id in (SELECT season_id FROM #__ts_season WHERE season_current = 1) AND t.id = ".($tournament_id);

        
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		//$db->freeResult();
		
        foreach ($rows as $row)
        {
            $html = $html .'<div class="tourn_header_wrapper">';
            $html = $html .'<div class="tourn_header">';
            $html = $html .'<div class="full_schedule_header">' . $row->tournament_name .'</div>';
            $html = $html .'</div>';

            //format times from mysql
            $start_date = strtotime($row->tournament_start_date);
            $start_date = date("m/d/y",$start_date);

            $end_date = strtotime($row->tournament_end_date);
            $end_date = date("m/d/y",$end_date);

            $html = $html .'<div class="schedule_dates"> ' . $start_date .' - ' . $end_date .'</div> ';
            $html = $html . $html_age;
    
        }
        $html = $html . '</div>';
        return $html;
    }
	public function GetFieldText($tournament_id, $age_id)
    {

        $html = "";

        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
        $age_id = filter_var(trim($age_id), FILTER_SANITIZE_NUMBER_INT);
               
        //Get Field Location Info
        $query = "SELECT * FROM #__ts_tournament_age_cost ac
        INNER JOIN #__ts_tournament t on ac.tournament_id=t.id
        INNER JOIN #__ts_season s on s.season_id=t.season_id 
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) AND ac.tournament_id =" .($tournament_id). " and ac.age_id =" .($age_id);
        $query .= " AND t.season_id in (SELECT season_id FROM #__ts_season WHERE season_current = 1) ";

        $db    = $this->getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		//$db->freeResult();
		
        foreach ($rows as $row)
		{
			$html = $html .'<div class="tourn_title">Tournament Notes:</div> ';
			$html = $html . '<div style="padding: 5px 0 5px 0;">'. $row->tournament_results . '</div>';
			$html = $html .'<div class="tourn_title">Field Locations:</div> ';
			$html = $html . '<div style="padding: 5px 0 5px 0;">' . $row->field_location_description. '</div>';
		}
        
         return $html;
    }

	public  function GetPoolText($tournament_id, $age_id)
     {

        $html = "";
        $rowCount = 0;
		
        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
        $age_id = filter_var(trim($age_id), FILTER_SANITIZE_NUMBER_INT);
               
        //Get Pool info
        $query ="SELECT distinct pool, team_name FROM (
        SELECT TRIM(home_team) as team_name, TRIM(home_pool) as pool FROM #__ts_games g
        INNER JOIN #__ts_tournament t on g.tournament_id=t.id
        INNER JOIN #__ts_season s on s.season_id=t.season_id
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) AND (g.state <> -2) AND 
        t.season_id in (SELECT season_id FROM #__ts_season WHERE season_current = 1) AND g.tournament_id = ".($tournament_id)." and g.age_id = ".($age_id)."
        UNION ALL
        SELECT TRIM(visitor_team) as team_name, TRIM(visitor_pool) as pool FROM #__ts_games g 
        INNER JOIN #__ts_tournament t on g.tournament_id=t.id 
        INNER JOIN #__ts_season s on s.season_id=t.season_id 
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) AND (g.state <> -2) AND 
        t.season_id in (SELECT season_id FROM #__ts_season WHERE season_current = 1) AND g.tournament_id = ".($tournament_id)." and g.age_id = ".($age_id)."
        ) as pools
        WHERE pool <> ''
        ORDER BY pool, team_name";

        $db    = $this->getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		//$db->freeResult();
		
        $html = $html .'<div style="font-family: arial; font-size: 12px;">';
        $row_count = 0;
        $title_count = 1;
        $pool = '';

        foreach ($rows as $row)
        {

            $css = "teams_row_1";
            if ($rowCount%2 == 0)
            {
				$css = "teams_row_2";
            }

            //check for new pool
            if ($pool != $row->pool)
            {
                $html = $html .'<div class="pool_header"> Pool '. $row->pool . '</div>';
                $html = $html . '<div class="pool_teams"><table width = "100%" cellpadding ="0" cellspacing="0">';
                $html = $html . '<tr><td with="40%;">&nbsp;</td>';
				$html = $html . '<td style="text-align: center; font-weight:bold; color: #002964;" width="30px;">Wins</td>';
				$html = $html . '<td style="text-align: center; font-weight: bold; color: #002964;" width="30px;">Losses</td>';
				$html = $html . '<td style="text-align: center; font-weight: bold; color: #002964;" width="30px;">Ties</td>';
				$html = $html . '<td style="text-align: center; font-weight: bold; color: #002964;" width="30px;">Runs Scored</td>';
				$html = $html . '<td style="text-align: center; font-weight: bold; color: #002964;" width="30px;">Runs Allowed</td>';
				$html = $html . '<td style="text-align: center; font-weight: bold; color: #002964;" width="30px;">Run Diff</td></tr>';
                $pool = $row->pool;
            }

            //get team calculated results
            $wins = $this->GetTeamWins($row->team_name,$tournament_id,$age_id);
            $losses = $this->GetTeamLosses($row->team_name,$tournament_id,$age_id);
            $ties = $this->GetTeamTies($row->team_name,$tournament_id,$age_id);
            
            $runs = 0;
            $runs_scored = 0;
            $runs_diff = 0;
			
            $runs = $this->GetTeamRunsAllowed($row->team_name,$tournament_id,$age_id);
            $runs_scored = $this->GetTeamRunsScored($row->team_name,$tournament_id,$age_id);
            $runs_diff = $this->GetTeamRunDiff($row->team_name,$tournament_id,$age_id);
            
            $html = $html . '<tr><td width="40%;" class="'.$css.'">' . $title_count. '. ' . $row->team_name . '</td>'; 
			$html = $html . '<td style="text-align: center; font-weight: bold;" width="30px;" class="'.$css.'"> ' . $wins .'</td>';
			$html = $html . '<td style="text-align: center; font-weight: bold;" width="30px;" class="'.$css.'">'.$losses.'</td>';
			$html = $html . '<td style="text-align: center; font-weight: bold;" width="30px;" class="'.$css.'">'.$ties .'</td>';
			$html = $html . '<td style="text-align: center; font-weight: bold;" width="30px;" class="'.$css.'">'.$runs_scored .'</td>';
			$html = $html . '<td style="text-align: center; font-weight: bold;" width="30px;" class="'.$css.'">'.$runs .'</td>';
			$html = $html . '<td style="text-align: center; font-weight: bold;" width="30px;" class="'.$css.'">'.$runs_diff .'</td></tr>';
			
            if(@$rows[$row_count + 1]->pool != @$pool)
            {
              $html = $html . '</table></div>';
              $html = $html . '<div style="border-top: solid 1px #cbcfd4;">&nbsp;</div>';
            }

            $title_count++;
            $row_count++;
        }
        $html = $html . '</div>';
        return $html;
    }
	public function GetTournFiles($tournament_id, $age_id)
    {
        $html = '';
        
        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
        $age_id = filter_var(trim($age_id), FILTER_SANITIZE_NUMBER_INT);
        
        $mime_types = array(
        'image/png',
        'image/jpeg',
        'image/jpeg',
        'image/jpeg',
        'image/gif',
        'image/tiff',
        'image/tiff'
        );

        $query = "select * FROM #__ts_files
        WHERE tournament_id=".($tournament_id)." AND age_id=".($age_id)."
        ORDER BY date_created DESC";

        $db    = $this->getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $rowcount = count($rows);

        $upload_dir = '/images/stories/tournaments/'.trim($tournament_id).'/';

        if($rowcount > 0)
        {
            $html .= '<div class="tourn_title">Tournament Files:</div>';
            $html .= '<div class="tourn_files_container">';
            foreach ($rows as $row)
            {

                $filedelete_id = "'".$row->files_id."'";
                if (in_array($row->file_mime, $mime_types))
                {

                    $i_width = 555;
                    list($width, $height, $type, $attr) = getimagesize($upload_dir.$row->file_name);

                    if($width < $i_width)
                    {
                        $i_width = $width;
                    }

                    $html .= '<div class="tourn_file">';
                    $html .= '<a href="'.$upload_dir.$row->file_name.'" rel="lightbox" title="'.$row->file_desc.'">';
                    $html .= '<img src="'.$upload_dir.$row->file_name.'" width="'.$i_width.'" border="0" />';
                    $html .= '</a>';

                    if(trim($row->file_desc) != '')
                    {
                        $html .= '<div class="tourn_desc">';
                        $html .= $row->file_desc;
                        $html .= '</div>';
                    }

                    $html .= '</div>';

                }
                else
                {
                    $html .= '<div class="tourn_file">';

                    $file_text = $row->file_name;
                    if(trim($row->file_desc) != '')
                    {
                        $file_text = $row->file_desc;
                    }
                    $html .= '<a href="'.$upload_dir.$row->file_name.'" class="btn_file" target="_blank">'.$file_text.'</a>';
                    $html .= '</div>';
                }
            }
            $html .= '</div>';
        }

        return $html;
    }
	public function GetTeamWins($team_name,$tournament_id,$age_id)
    {

        $total = 0;

        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
        $age_id = filter_var(trim($age_id), FILTER_SANITIZE_NUMBER_INT);
        $team_name = strip_tags(trim($team_name));
		
        //select team wins for a given tournament, age group
        $query ="SELECT COUNT(*) as Wins FROM #__ts_games
        WHERE UPPER(TRIM(home_team)) = UPPER(TRIM('".(strtoupper(trim($team_name)))."')) and home_score > visitor_score
        AND tournament_id = ".($tournament_id)." AND age_id = ".($age_id)." 
        AND home_score <> -1 AND visitor_score <> -1
        UNION ALL
        SELECT COUNT(*) as Wins FROM #__ts_games
        WHERE UPPER(TRIM(visitor_team)) = UPPER(TRIM('".(strtoupper(trim($team_name)))."')) and visitor_score > home_score
        AND tournament_id = ".($tournament_id)." AND age_id = ".($age_id)."
        AND home_score <> -1 AND visitor_score <> -1 ";

        $db    = $this->getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		//$db->freeResult();
		
        foreach ($rows as $row)
        {
            $total = $total + $row->Wins;
        }

        return $total;
        
     }
	 public function GetTeamLosses($team_name,$tournament_id,$age_id)
     {

        $total = 0;
        
        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
        $age_id = filter_var(trim($age_id), FILTER_SANITIZE_NUMBER_INT);
        $team_name = strip_tags(trim($team_name));
		
        //select team wins for a given tournament, age group
        $query ="SELECT COUNT(*) as Losses FROM #__ts_games
        WHERE UPPER(TRIM(home_team)) = UPPER(TRIM('".(strtoupper(trim($team_name)))."')) and home_score < visitor_score
        AND tournament_id = ".($tournament_id)." AND age_id = ".($age_id)."
        AND home_score <> -1 AND visitor_score <> -1
        UNION ALL
        SELECT COUNT(*) as Losses FROM #__ts_games
        WHERE UPPER(TRIM(visitor_team)) = UPPER(TRIM('".(strtoupper(trim($team_name)))."')) and visitor_score < home_score
        AND tournament_id = ".($tournament_id)." AND age_id = ".($age_id)."
        AND home_score <> -1 AND visitor_score <> -1 ";

        $db    = $this->getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		//$db->freeResult();
		
        foreach ($rows as $row)
        {
            $total = $total + $row->Losses;
        }

        return $total;

     }

	 public function GetTeamTies($team_name,$tournament_id,$age_id)
     {

        $total = 0;

        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
        $age_id = filter_var(trim($age_id), FILTER_SANITIZE_NUMBER_INT);
        $team_name = strip_tags(trim($team_name));
		
        //select team losses for a given tournament, age group
        $query ="SELECT COUNT(*) as Tie FROM #__ts_games
        WHERE UPPER(TRIM(home_team)) = UPPER(TRIM('".(strtoupper(trim($team_name)))."')) and home_score = visitor_score
        AND tournament_id = ".($tournament_id)." AND age_id = ".($age_id)."
        AND home_score <> -1 AND visitor_score <> -1 AND home_score = visitor_score  AND home_score != 0 AND visitor_score != 0  
        UNION ALL
        SELECT COUNT(*) as Tie FROM #__ts_games
        WHERE UPPER(TRIM(visitor_team)) = UPPER(TRIM('".(strtoupper(trim($team_name)))."')) and visitor_score = home_score
        AND tournament_id = ".($tournament_id)." AND age_id = ".($age_id)."
        AND home_score <> -1 AND visitor_score <> -1 AND home_score = visitor_score AND home_score != 0 AND visitor_score != 0  ";

        $db    = $this->getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		//$db->freeResult();
		
        foreach ($rows as $row)
        {
            $total = $total + $row->Tie;
        }

        return $total;

     }
	 public function GetTeamRunsAllowed($team_name,$tournament_id,$age_id)
     {

        $total = 0;

        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
        $age_id = filter_var(trim($age_id), FILTER_SANITIZE_NUMBER_INT);
        $team_name = strip_tags(trim($team_name));
		
        //select team losses for a given tournament, age group
        $query ="SELECT SUM(visitor_score) as Runs FROM #__ts_games
        WHERE UPPER(TRIM(home_team)) = UPPER(TRIM('".(strtoupper(trim($team_name)))."')) 
        AND tournament_id = ".($tournament_id)." AND age_id = ".($age_id)."
        UNION ALL
        SELECT SUM(home_score) as Runs FROM #__ts_games
        WHERE UPPER(TRIM(visitor_team)) = UPPER(TRIM('".(strtoupper(trim($team_name)))."')) 
        AND tournament_id = ".($tournament_id)." AND age_id = ".($age_id);

        $db    = $this->getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		//$db->freeResult();
		
        foreach ($rows as $row)
        {
            $total = $total + $row->Runs;
        }

        return $total;

     }
 
     public function GetTeamRunsScored($team_name,$tournament_id,$age_id)
     {

        $total = 0;

        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
        $age_id = filter_var(trim($age_id), FILTER_SANITIZE_NUMBER_INT);
        $team_name = strip_tags(trim($team_name));
		
        //select team losses for a given tournament, age group
        $query ="SELECT SUM(home_score) as Runs FROM #__ts_games
        WHERE UPPER(TRIM(home_team)) = UPPER(TRIM('".(strtoupper(trim($team_name)))."')) 
        AND tournament_id = ".($tournament_id)." AND age_id = ".($age_id)."
        UNION ALL
        SELECT SUM(visitor_score) as Runs FROM #__ts_games
        WHERE UPPER(TRIM(visitor_team)) = UPPER(TRIM('".(strtoupper(trim($team_name)))."')) 
        AND tournament_id = ".($tournament_id)." AND age_id = ".($age_id);

        $db    = $this->getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		//$db->freeResult();
		
        foreach ($rows as $row)
        {
            $total = $total + $row->Runs;
        }

        return $total;

     }
	 
	 public function GetTeamRunDiff($team_name,$tournament_id,$age_id)
     {

        $total = 0;

        $tournament_id = filter_var(trim($tournament_id), FILTER_SANITIZE_NUMBER_INT);
        $age_id = filter_var(trim($age_id), FILTER_SANITIZE_NUMBER_INT);
		$team_name = strip_tags(trim($team_name));
		
        $team_name = strtoupper(trim($team_name));
		
        //select team runs and figure out run diff
        $query ="select 
		home_score,
		visitor_score,
		UPPER(TRIM(home_team)) as home_team,
		UPPER(TRIM(visitor_team)) as visitor_team
		from #__ts_games
		where (UPPER(TRIM(home_team)) = UPPER(TRIM('".$team_name."')) OR UPPER(TRIM(visitor_team))=UPPER(TRIM('".$team_name."'))) 
        AND tournament_id = ".($tournament_id)." AND age_id = ".($age_id);

        $db    = $this->getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		//$db->freeResult();
		
        foreach ($rows as $row)
        {
			if($row->home_team == $team_name)
			{
				$home_run_diff = ($row->home_score - $row->visitor_score);
				
				if($home_run_diff > 8)
				{
					$home_run_diff = 8;
				}
				
				if($home_run_diff < -8)
				{
					$home_run_diff = -8;
				}
				
				$total = $total + $home_run_diff;
			}
			else if($row->visitor_team == $team_name)
			{
			
				$visitor_run_diff = ($row->visitor_score - $row->home_score);
				
				if($visitor_run_diff > 8)
				{
					$visitor_run_diff = 8;
				}
				
				if($visitor_run_diff < -8)
				{
					$visitor_run_diff = -8;
				}
			
				$total = $total + $visitor_run_diff;
			}
        }

        return $total;

     }

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);
		return (date_create($date)) ? Factory::getDate($date)->format("Y-m-d") : null;
	}
}
