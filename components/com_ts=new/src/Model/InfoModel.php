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
use \Joomla\Utilities\ArrayHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Table\Table;
use \Joomla\CMS\MVC\Model\ItemModel;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Object\CMSObject;
use \Joomla\CMS\User\UserFactoryInterface;
use \Teamtournaments\Component\Ts\Site\Helper\TsHelper;
use Joomla\CMS\Router\Route;

/**
 * Ts model.
 *
 * @since  1.0.0
 */
class InfoModel extends ItemModel
{
	public $_item;

	

	

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 *
	 * @throws Exception
	 */
	protected function populateState()
	{
		$app  = Factory::getApplication('com_ts');
		$user = $app->getIdentity();

		// Check published state
		if ((!$user->authorise('core.edit.state', 'com_ts')) && (!$user->authorise('core.edit', 'com_ts')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

		// Load state from the request userState on edit or from the passed variable on default
		if (Factory::getApplication()->input->get('layout') == 'edit')
		{
			$id = Factory::getApplication()->getUserState('com_ts.edit.tournament.id');
		}
		else
		{
			$id = Factory::getApplication()->input->get('id');
			Factory::getApplication()->setUserState('com_ts.edit.tournament.id', $id);
		}

		$this->setState('tournament.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('tournament.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}

	/**
	 * Method to get an object.
	 *
	 * @param   integer $id The id of the object to get.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @throws Exception
	 */
	public function getItem($id = null)
	{
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id))
			{
				$id = $this->getState('tournament.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table && $table->load($id))
			{
				

				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if (isset($table->state) && $table->state != $published)
					{
						throw new \Exception(Text::_('COM_TS_ITEM_NOT_LOADED'), 403);
					}
				}

				// Convert the Table to a clean CMSObject.
				$properties  = $table->getProperties(1);
				$this->_item = ArrayHelper::toObject($properties, CMSObject::class);

				
			}

			if (empty($this->_item))
			{
				throw new \Exception(Text::_('COM_TS_ITEM_NOT_LOADED'), 404);
			}
		}

		

		 $container = \Joomla\CMS\Factory::getContainer();

		 $userFactory = $container->get(UserFactoryInterface::class);

		if (isset($this->_item->created_by))
		{
			$user = $userFactory->loadUserById($this->_item->created_by);
			$this->_item->created_by_name = $user->name;
		}

		 $container = \Joomla\CMS\Factory::getContainer();

		 $userFactory = $container->get(UserFactoryInterface::class);

		if (isset($this->_item->modified_by))
		{
			$user = $userFactory->loadUserById($this->_item->modified_by);
			$this->_item->modified_by_name = $user->name;
		}

		return $this->_item;
	}
	
	public function GetScheduleInfo($tournament_id='')
    {
		
		$tournament_id = $tournament_id;
        $html = "";

        if($tournament_id!='')
        {
            
            $html = $this->GetTournamentHtml($tournament_id, false);
            $html .= $this->GetMenuHtml($tournament_id);
        }
        else if($tournament_id=='')
        {
          
            $html = $this->GetAllTournaments();
        }

        return $html;

    }
	public function GetMenuHtml($tournament_id='')
    {
		
		$tournament_id = $tournament_id;
		$reg_link = Route::_('index.php?option=com_content&view=article&id=4&Itemid=11');
        $html = '<div class="tourn_menu">';
        $html .= '<ul>';
        $html .= '<li><a href="'.$reg_link.'" style="padding: 0 0 0 20px">Register Now</a></li>';
        $html .= '<li><a href="index.php?option=com_ts&view=results&tournament_id='.$tournament_id.'" style="padding: 0 0 0 10px">Schedule/Results</a></li>';
        $html .= '</ul></div>';

        return $html;
    }
	public function GetAllTournaments()
    {
        $html = "";
		$db  = $this->getDbo();

        $query = "SELECT * FROM #__ts_tournament as t ";
        $query .= "WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL)
        t.season_id in (SELECT season_id FROM #__ts_season WHERE season_current = 1)
        ORDER BY t.tournament_start_date";       
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		
        foreach ($rows as $row)
        {
            $html .= $this->GetTournamentHtml($row->tournament_id, true);

        }

        return $html;
    }
	public function GetTournamentHtml($tournament_id, $display_link)
    {

        $html = "";
        $html_age = "";
        
        $tournament_id = $tournament_id;
        
        //Get Age info
        $query = "SELECT * FROM #__ts_tournament_age_cost
        INNER JOIN #__ts_age on #__ts_age.age_id = #__ts_tournament_age_cost.age_id
        INNER JOIN #__ts_tournament t on t.id = #__ts_tournament_age_cost.tournament_id
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) AND
        t.season_id in (SELECT season_id FROM #__ts_season WHERE season_current = 1) AND
        #__ts_tournament_age_cost.tournament_id = ".$tournament_id;

		$db  = $this->getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $html_age = $html_age .'<div style="font-family: arial; font-size: 12px;">';
        $html_age = $html_age . '<span style="font-weight: bold;">Age Group(s) and Cost:&nbsp;</span>';

        foreach ($rows as $row)
        {
            $html_age = $html_age . ' ' . $row->age . ' ($' . $row->tournament_cost . ') , ';

        }

        $html_age = $html_age . '</div>';

        //get data back from database and create html to display tournament details
        $query = "SELECT * FROM #__ts_tournament WHERE id = ".($tournament_id);               
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        foreach ($rows as $row)
        {
            $html = $html .'<div class="tourn_header">';

            if($display_link == true)
                {
                 $html = $html .'<a href="index.php?option=com_ts&view=info&tournament_id='.$row->tournament_id.'">' . $row->tournament_name .'</a>';
                }
             else{
               $html = $html  . $row->tournament_name;
                 }
           
            $html = $html .'</div>';

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
            $html = $html . '<span style="font-weight:bold">Teams Registered: </span><div style="padding: 10px 0 0 0;">' . $row->teams_registered .'</div>';
            }
            }
             if($display_link == true)
                {
                 $html = $html .'<div style="border-bottom: solid 1px #cbcfd4">&nbsp;</div>';
                }
     
        return $html;
    }

	/**
	 * Get an instance of Table class
	 *
	 * @param   string $type   Name of the Table class to get an instance of.
	 * @param   string $prefix Prefix for the table class name. Optional.
	 * @param   array  $config Array of configuration values for the Table object. Optional.
	 *
	 * @return  Table|bool Table if success, false on failure.
	 */
	public function getTable($type = 'Tournament', $prefix = 'Administrator', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Get the id of an item by alias
	 * @param   string $alias Item alias
	 *
	 * @return  mixed
	 * 
	 * @deprecated  No replacement
	 */
	public function getItemIdByAlias($alias)
	{
		$table      = $this->getTable();
		$properties = $table->getProperties();
		$result     = null;
		$aliasKey   = null;
		if (method_exists($this, 'getAliasFieldNameByView'))
		{
			$aliasKey   = $this->getAliasFieldNameByView('tournament');
		}
		

		if (key_exists('alias', $properties))
		{
			$table->load(array('alias' => $alias));
			$result = $table->id;
		}
		elseif (isset($aliasKey) && key_exists($aliasKey, $properties))
		{
			$table->load(array($aliasKey => $alias));
			$result = $table->id;
		}
		
			return $result;
		
	}

	/**
	 * Method to check in an item.
	 *
	 * @param   integer $id The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState('tournament.id');
				
		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in.
			if (method_exists($table, 'checkin'))
			{
				if (!$table->checkin($id))
				{
					return false;
				}
			}
		}

		return true;
		
	}

	/**
	 * Method to check out an item for editing.
	 *
	 * @param   integer $id The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState('tournament.id');

				
		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object.
			$user = Factory::getApplication()->getIdentity();

			// Attempt to check the row out.
			if (method_exists($table, 'checkout'))
			{
				if (!$table->checkout($user->get('id'), $id))
				{
					return false;
				}
			}
		}

		return true;
				
	}

	/**
	 * Publish the element
	 *
	 * @param   int $id    Item id
	 * @param   int $state Publish state
	 *
	 * @return  boolean
	 */
	public function publish($id, $state)
	{
		$table = $this->getTable();
				
		$table->load($id);
		$table->state = $state;

		return $table->store();
				
	}

	/**
	 * Method to delete an item
	 *
	 * @param   int $id Element id
	 *
	 * @return  bool
	 */
	public function delete($id)
	{
		$table = $this->getTable();

		
			return $table->delete($id);
		
	}

	
}
