<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Ts
 * @author     Percept <perceptinfotech2@gmail.com>
 * @copyright  2023 Percept
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Teamtournaments\Component\Ts\Administrator\Table;
// No direct access
defined('_JEXEC') or die;

use \Joomla\Utilities\ArrayHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Access\Access;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Table\Table as Table;
use \Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\CMS\Tag\TaggableTableInterface;
use Joomla\CMS\Tag\TaggableTableTrait;
use \Joomla\Database\DatabaseDriver;
use \Joomla\CMS\Filter\OutputFilter;
use \Joomla\CMS\Filesystem\File;
use \Joomla\Registry\Registry;
use \Teamtournaments\Component\Ts\Administrator\Helper\TsHelper;
use \Joomla\CMS\Helper\ContentHelper;


/**
 * Game table
 *
 * @since 1.0.0
 */
class GameTable extends Table implements VersionableTableInterface, TaggableTableInterface
{
	use TaggableTableTrait;
	
	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_ts.game';
		parent::__construct('#__ts_games', 'id', $db);
		$this->setColumnAlias('published', 'state');
		
	}

	/**
	 * Get the type alias for the history table
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   1.0.0
	 */
	public function getTypeAlias()
	{
		return $this->typeAlias;
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  Optional array or list of parameters to ignore
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     Table:bind
	 * @since   1.0.0
	 * @throws  \InvalidArgumentException
	 */
	public function bind($array, $ignore = '')
	{
		$date = Factory::getDate();
		$task = Factory::getApplication()->input->get('task');
		$user = Factory::getApplication()->getIdentity();
		
		$input = Factory::getApplication()->input;
		$task = $input->getString('task', '');

		if ($array['id'] == 0 && empty($array['created_by']))
		{
			$array['created_by'] = Factory::getUser()->id;
		}

		if ($array['id'] == 0 && empty($array['modified_by']))
		{
			$array['modified_by'] = Factory::getUser()->id;
		}

		if ($task == 'apply' || $task == 'save')
		{
			$array['modified_by'] = Factory::getUser()->id;
		}

		// Support for multiple field: home_pool
		if (isset($array['home_pool']))
		{
			if (is_array($array['home_pool']))
			{
				$array['home_pool'] = implode(',',$array['home_pool']);
			}
			elseif (strpos($array['home_pool'], ',') != false)
			{
				$array['home_pool'] = explode(',',$array['home_pool']);
			}
			elseif (strlen($array['home_pool']) == 0)
			{
				$array['home_pool'] = '';
			}
		}
		else
		{
			$array['home_pool'] = '';
		}

		// Support for multiple field: game_type
		if (isset($array['game_type']))
		{
			if (is_array($array['game_type']))
			{
				$array['game_type'] = implode(',',$array['game_type']);
			}
			elseif (strpos($array['game_type'], ',') != false)
			{
				$array['game_type'] = explode(',',$array['game_type']);
			}
			elseif (strlen($array['game_type']) == 0)
			{
				$array['game_type'] = '';
			}
		}
		else
		{
			$array['game_type'] = '';
		}

		// Support for multiple field: age_id
		if (isset($array['age_id']))
		{
			if (is_array($array['age_id']))
			{
				$array['age_id'] = implode(',',$array['age_id']);
			}
			elseif (strpos($array['age_id'], ',') != false)
			{
				$array['age_id'] = explode(',',$array['age_id']);
			}
			elseif (strlen($array['age_id']) == 0)
			{
				$array['age_id'] = '';
			}
		}
		else
		{
			$array['age_id'] = '';
		}

		// Support for multiple field: tournament_id
		if (isset($array['tournament_id']))
		{
			if (is_array($array['tournament_id']))
			{
				$array['tournament_id'] = implode(',',$array['tournament_id']);
			}
			elseif (strpos($array['tournament_id'], ',') != false)
			{
				$array['tournament_id'] = explode(',',$array['tournament_id']);
			}
			elseif (strlen($array['tournament_id']) == 0)
			{
				$array['tournament_id'] = '';
			}
		}
		else
		{
			$array['tournament_id'] = '';
		}

		// Support for multiple field: visitor_pool
		if (isset($array['visitor_pool']))
		{
			if (is_array($array['visitor_pool']))
			{
				$array['visitor_pool'] = implode(',',$array['visitor_pool']);
			}
			elseif (strpos($array['visitor_pool'], ',') != false)
			{
				$array['visitor_pool'] = explode(',',$array['visitor_pool']);
			}
			elseif (strlen($array['visitor_pool']) == 0)
			{
				$array['visitor_pool'] = '';
			}
		}
		else
		{
			$array['visitor_pool'] = '';
		}

		// Support for checkbox field: game_active
		if (!isset($array['game_active']))
		{
			$array['game_active'] = 0;
		}

		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new Registry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new Registry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		if (!$user->authorise('core.admin', 'com_ts.game.' . $array['id']))
		{
			$actions         = Access::getActionsFromFile(
				JPATH_ADMINISTRATOR . '/components/com_ts/access.xml',
				"/access/section[@name='game']/"
			);
			$default_actions = Access::getAssetRules('com_ts.game.' . $array['id'])->getData();
			$array_jaccess   = array();

			foreach ($actions as $action)
			{
				if (key_exists($action->name, $default_actions))
				{
					$array_jaccess[$action->name] = $default_actions[$action->name];
				}
			}

			$array['rules'] = $this->JAccessRulestoArray($array_jaccess);
		}

		// Bind the rules for ACL where supported.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$this->setRules($array['rules']);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Method to store a row in the database from the Table instance properties.
	 *
	 * If a primary key value is set the row with that primary key value will be updated with the instance property values.
	 * If no primary key value is set a new row will be inserted into the database with the properties from the Table instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0.0
	 */
	public function store($updateNulls = true)
	{
		return parent::store($updateNulls);
	}

	/**
	 * This function convert an array of Access objects into an rules array.
	 *
	 * @param   array  $jaccessrules  An array of Access objects.
	 *
	 * @return  array
	 */
	private function JAccessRulestoArray($jaccessrules)
	{
		$rules = array();

		foreach ($jaccessrules as $action => $jaccess)
		{
			$actions = array();

			if ($jaccess)
			{
				foreach ($jaccess->getData() as $group => $allow)
				{
					$actions[$group] = ((bool)$allow);
				}
			}

			$rules[$action] = $actions;
		}

		return $rules;
	}

	/**
	 * Overloaded check function
	 *
	 * @return bool
	 */
	public function check()
	{
		// If there is an ordering column and this is a new row then get the next ordering value
		if (property_exists($this, 'ordering') && $this->id == 0)
		{
			$this->ordering = self::getNextOrder();
		}
		
		

		return parent::check();
	}

	/**
	 * Define a namespaced asset name for inclusion in the #__assets table
	 *
	 * @return string The asset name
	 *
	 * @see Table::_getAssetName
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return $this->typeAlias . '.' . (int) $this->$k;
	}

	/**
	 * Returns the parent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
	 *
	 * @param   Table   $table  Table name
	 * @param   integer  $id     Id
	 *
	 * @see Table::_getAssetParentId
	 *
	 * @return mixed The id on success, false on failure.
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = Table::getInstance('Asset');

		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();

		// The item has the component as asset-parent
		$assetParent->loadByName('com_ts');

		// Return the found asset-parent-id
		if ($assetParent->id)
		{
			$assetParentId = $assetParent->id;
		}

		return $assetParentId;
	}

	//XXX_CUSTOM_TABLE_FUNCTION

	
    /**
     * Delete a record by id
     *
     * @param   mixed  $pk  Primary key value to delete. Optional
     *
     * @return bool
     */
    public function delete($pk = null)
    {
        $this->load($pk);
        $result = parent::delete($pk);
        
        return $result;
    }
}
