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
 * Methods supporting a list of Tournamentagecosts records.
 *
 * @since  1.0.0
 */
class TournamentagecostsModel extends ListModel
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
				'tournament_cost', 'a.tournament_cost',
				'tourn_capacity', 'a.tourn_capacity',
				'field_location_description', 'a.field_location_description',
				'tournament_results', 'a.tournament_results',
				'age_id', 'a.age_id',
				'tournament_id', 'a.tournament_id',
				'tournament_name','t.tournament_name'
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
		parent::populateState('tournament_name', 'ASC');

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
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__ts_tournament_age_cost` AS a');

		$query->select("t.tournament_name");
		$query->join('INNER', $db->quoteName('#__ts_tournament', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('a.tournament_id'));
		
		$query->select("ag.age,ag.age_num");
		$query->join('INNER', $db->quoteName('#__ts_age', 'ag') . ' ON ' . $db->quoteName('ag.age_id') . ' = ' . $db->quoteName('a.age_id'));
		
		$query->select("s.season_name");
		$query->join('INNER', $db->quoteName('#__ts_season', 's') . ' ON ' . $db->quoteName('s.season_id') . ' = ' . $db->quoteName('t.season_id'));
        
        $query->where('(t.is_deleted = 0 OR t.is_deleted IS NULL)');
		$query->where('(s.season_current = 1)');
        


		
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

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif (empty($published))
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
		

		// Filtering age_id
		$filter_age_id = $this->state->get("filter.age_id");

		if ($filter_age_id !== null && (is_numeric($filter_age_id) || !empty($filter_age_id)))
		{
			$query->where("a.`age_id` = '".$db->escape($filter_age_id)."'");
		}

		// Filtering tournament_id
		$filter_tournament_id = $this->state->get("filter.tournament_id");

		if ($filter_tournament_id !== null && (is_numeric($filter_tournament_id) || !empty($filter_tournament_id)))
		{
			$query->where("a.`tournament_id` = '".$db->escape($filter_tournament_id)."'");
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $oneItem)
		{

			if (isset($oneItem->age_id))
			{
				$values    = explode(',', $oneItem->age_id);
				$textValue = array();

				foreach ($values as $value)
				{
					if (!empty($value))
					{
						$db = $this->getDbo();
						$query = "SELECT * FROM `#__ts_age` WHERE age_num = '$value' AND 1";
						$db->setQuery($query);
						$results = $db->loadObject();

						if ($results)
						{
							$textValue[] = $results->age;
						}
					}
				}

				$oneItem->age_id = !empty($textValue) ? implode(', ', $textValue) : $oneItem->age_id;
			}

			if (isset($oneItem->tournament_id))
			{
				$values    = explode(',', $oneItem->tournament_id);
				$textValue = array();

				foreach ($values as $value)
				{
					if (!empty($value))
					{
						$db = $this->getDbo();
						$query = "SELECT * FROM `#__ts_tournament` WHERE id = '$value' AND 1";
						$db->setQuery($query);
						$results = $db->loadObject();

						if ($results)
						{
							$textValue[] = $results->tournament_name;
						}
					}
				}

				$oneItem->tournament_id = !empty($textValue) ? implode(', ', $textValue) : $oneItem->tournament_id;
			}
		}

		return $items;
	}
}
