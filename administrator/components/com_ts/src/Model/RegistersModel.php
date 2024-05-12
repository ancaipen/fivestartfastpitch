<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Ts
 * @author     Percept <perceptinfotech2@gmail.com>
 * @copyright  2023 Percept
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Teamtournaments\Component\Ts\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;
use Teamtournaments\Component\Ts\Administrator\Helper\TsHelper;

/**
 * Methods supporting a list of Registers records.
 *
 * @since  1.0.0
 */
class RegistersModel extends ListModel
{
	/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
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
				'team_manager_1', 'a.team_manager_1',
				'team_address', 'a.team_address',
				'level_play', 'a.level_play',
				'registration_number', 'a.registration_number',
				'team_name', 'a.team_name',
				'team_manager_2', 'a.team_manager_2',
				'team_city', 'a.team_city',
				'team_state', 'a.team_state',
				'team_zip', 'a.team_zip',
				'home_phone', 'a.home_phone',
				'cell_phone_2', 'a.cell_phone_2',
				'email_1', 'a.email_1',
				'season_id', 'a.season_id',
				'reg_status', 'a.reg_status',
				'date_submitted', 'a.date_submitted',
				'league_affiliation', 'a.league_affiliation',
				'email_2', 'a.email_2',
				'comments', 'a.comments',
				'cell_phone_1', 'a.cell_phone_1',
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
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('id', 'ASC');

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
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string A store id.
	 *
	 * @since   1.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		
		return parent::getStoreId($id);
		
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
				'list.select', 'DISTINCT a.*,IFNULL(a.reg_status, "New") as reg_status'
			)
		);
		$query->from('`#__ts_register` AS a');

		$query->select("rt.*");
		$query->join('INNER', $db->quoteName('#__ts_register_tourn', 'rt') . ' ON ' . $db->quoteName('rt.register_id') . ' = ' . $db->quoteName('a.id'));

		$query->select("ag.age,ag.age_num");
		$query->join('INNER', $db->quoteName('#__ts_age', 'ag') . ' ON ' . $db->quoteName('ag.age_id') . ' = ' . $db->quoteName('rt.age_id'));

		$query->select("t.tournament_name");
		$query->join('INNER', $db->quoteName('#__ts_tournament', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('rt.tournament_id'));

		$query->select("ac.tournament_cost,ac.tourn_capacity,ac.tournament_results,ac.age_id,ac.tournament_id");
		$query->join('INNER', $db->quoteName('#__ts_tournament_age_cost', 'ac') . ' ON ' . ($db->quoteName('ac.age_id') . ' = ' . $db->quoteName('rt.age_id')) .' AND '. ($db->quoteName('ac.tournament_id') . ' = ' . $db->quoteName('rt.tournament_id')));
		
		$query->select("s.season_name");
		$query->join('INNER', $db->quoteName('#__ts_season', 's') . ' ON ' . $db->quoteName('s.season_id') . ' = ' . $db->quoteName('t.season_id'));
		
		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
		// Filter by published state
		$published = $this->getState('filter.state');

		$query->where('(t.is_deleted = 0 OR t.is_deleted IS NULL)');
		$query->where('(s.season_current = 1)');
		$query->where('(a.reg_status <> "Deleted")');
		
		$age_id = $this->getState('filter.age_id');
		$tournament_id = $this->getState('filter.tournament_id');
		$reg_status = $this->getState('filter.reg_status');
		
		/*
		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif (empty($published))
		{
			$query->where('(a.state IN (0, 1))');
		}
		*/
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		
		if (is_numeric($age_id))
		{
			$query->where('ag.age_num = "' .   $age_id.'"');
		}
		
		if (is_numeric($tournament_id))
		{
			$query->where('ac.tournament_id = ' . (int) $tournament_id);
		}
		
		if (!empty($reg_status))
		{
			$reg_status = trim($reg_status);
			$query->where("a.reg_status = '" . $reg_status . "'");
		}
		
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
		

		// Filtering registration_number

		// Filtering team_name
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}
		
		//echo $query; 
		
		return $query;
	}



	public function dropdown_regstatus($regstatus_selected, $regststatus_id = '0', $add_all = false)
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
	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		

		return $items;
	}
}
