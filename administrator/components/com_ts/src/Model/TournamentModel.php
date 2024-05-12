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

use \Joomla\CMS\Table\Table;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Plugin\PluginHelper;
use \Joomla\CMS\MVC\Model\AdminModel;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Filter\OutputFilter;

/**
 * Tournament model.
 *
 * @since  1.0.0
 */
class TournamentModel extends AdminModel
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 *
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_TS';

	/**
	 * @var    string  Alias to manage history control
	 *
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_ts.tournament';

	/**
	 * @var    null  Item data
	 *
	 * @since  1.0.0
	 */
	protected $item = null;

	
	

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table    A database object
	 *
	 * @since   1.0.0
	 */
	public function getTable($type = 'Tournament', $prefix = 'Administrator', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  \JForm|boolean  A \JForm object on success, false on failure
	 *
	 * @since   1.0.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = Factory::getApplication();

		// Get the form.
		$form = $this->loadForm(
								'com_ts.tournament', 
								'tournament',
								array(
									'control' => 'jform',
									'load_data' => $loadData 
								)
							);

		

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.0.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_ts.edit.tournament.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$data = $this->item;
			
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function getItem($pk = null)
	{
		
			if ($item = parent::getItem($pk))
			{
				if (isset($item->params))
				{
					$item->params = json_encode($item->params);
				}
				
				// Do any procesing on fields here if needed
			}

			return $item;
		
	}

	/**
	 * Method to duplicate an Tournament
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$app = Factory::getApplication();
		$user = $app->getIdentity();

		// Access checks.
		if (!$user->authorise('core.create', 'com_ts'))
		{
			throw new \Exception(Text::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$context    = $this->option . '.' . $this->name;

		// Include the plugins for the save events.
		PluginHelper::importPlugin($this->events_map['save']);

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			
				if ($table->load($pk, true))
				{
					// Reset the id to create a new record.
					$table->id = 0;

					if (!$table->check())
					{
						throw new \Exception($table->getError());
					}
					

					// Trigger the before save event.
					$result = $app->triggerEvent($this->event_before_save, array($context, &$table, true, $table));

					if (in_array(false, $result, true) || !$table->store())
					{
						throw new \Exception($table->getError());
					}

					// Trigger the after save event.
					$app->triggerEvent($this->event_after_save, array($context, &$table, true));
				}
				else
				{
					throw new \Exception($table->getError());
				}
			
		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   Table  $table  Table Object
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = $this->getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__ts_tournament');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
		
		//echo var_dump($table);
		
	}
	
	public function BuildTournamentAgeCostLists($tournament_id)
    {
        $tournament_id = trim($tournament_id);
        
		if(!isset($tournament_id))
		{
			$html = '<h2>Save Tournament first in order to assign ages and cost</h2>';
			return $html;
		}
		
		if($tournament_id == "")
		{
			$html = '<h2>Save Tournament first in order to assign ages and cost</h2>';
			return $html;
		}
		$db = $this->getDbo();
		
        //build available list
        $query = "select a.*, ac.tournament_cost, ac.id FROM #__ts_age a
        LEFT JOIN #__ts_tournament_age_cost ac on ac.tournament_id=".$tournament_id." AND a.age_id=ac.age_id
        ORDER BY age_num";       
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $rowcount = 0;
        $html .= '<table cellpadding="8" cellspacing="0">';

        foreach ($rows as $row)
        {
            $found = $this->getSelectedAgeCost($tournament_id, $row->age_id);

            $css = 'tr_0';
            if($rowcount % 2 == 0)
            {
                $css = 'tr_1';
            }
            
            $chk_id = "'ageid_".$row->age_id."'";
            $chk_lbl = "'agelbl_".$row->age_id."'";
            $txt_id = "'tourncost_".$row->age_id."'";
            $filedesc_id = "'tournfiledesc_".$row->age_id."'";
            $edit_id = "tournedit_".$row->age_id;
            $link_id = "'tournlink_".$row->age_id."'";
            $linkremove_id = "'tournlinkremove_".$row->age_id."'";
            $age_id = "'".$row->age_id."'";
            $div_id = "'".'appendfiles_'.$row->age_id."'";
            $default_div_id = "'".'defaultfiles_'.$row->age_id."'";

            $html .= '<tr class="'.$css.'"><td valign="top" colspan="2">';

            //add check box
            if($found==true)
            {
                //age id
                $html .= '<input type="checkbox" name='.$chk_id.' id='.$chk_id.' OnClick="enableField('.$chk_id.','.$txt_id.','.$default_div_id.','.$div_id.');" CHECKED /><label name='.$chk_lbl.' for='.$chk_id.'>'.$row->age.'</label>';
            }
            else
            {
                $html .= '<input type="checkbox" name='.$chk_id.' id='.$chk_id.' OnClick="enableField('.$chk_id.','.$txt_id.','.$default_div_id.','.$div_id.');" UNCHECKED /><label name='.$chk_lbl.' for='.$chk_id.'>'.$row->age.'</label>';
            }

            //tournament cost
            $html .= '&nbsp;&nbsp;$<input type="text" id='.$txt_id.' name='.$txt_id.' value="'.$row->tournament_cost.'"/>';
            if(isset($row->tournament_cost_id))
            {
               $html .= '&nbsp;<a href="index.php?option=com_ts&view=tournament_cost&task=edit&cid[]='.$row->tournament_cost_id.' id="'.$edit_id.'">edit</a>';
            }
            $html .= '</td></tr>';

            $html .= '<div id='.$div_id.'></div>';
            $html .= '<input type="hidden" name="clickcount_'.$row->age_id.'" value="1" id="clickcount_'.$row->age_id.'" />';

            //used to disable or enable texboxes onload
            //$html .= '<img src="/templates/OhioBaseball/images/icon-48-generic.png" height="0" width="0" OnLoad="enableField('.$chk_id.','.$txt_id.','.$default_div_id.','.$div_id.');" />';
            $html .= '</td>';

            $html .= '</tr>';

            $rowcount++;
        }

        $html .= '</table>';

        return $html;

    }

	public function getSelectedAgeCost($tournament_id, $age_id)
    {		
		$db = $this->getDbo();
        $tournament_id = trim($tournament_id);
        $age_id = trim($age_id);
        
        $query = "SELECT * FROM #__ts_tournament t INNER JOIN #__ts_tournament_age_cost ac on ac.tournament_id = t.id
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) ";
		
		if(isset($tournament_id))
		{
			if($tournament_id != "")
			{
				$query .= "AND t.id =".($tournament_id) . " ";
			}
		}
		
		if(isset($tournament_id))
		{
			if($tournament_id != "")
			{
				$query .= "AND ac.age_id = ".($age_id);
			}
		}
		
        $found = false;        
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        foreach ($rows as $row)
        {
            $found = true;
        }

        return $found;
    }

	function getCurrentseason_id(){
		$db = $this->getDbo();
		$q="SELECT season_id FROM #__ts_season WHERE season_current = 1 limit 1";
		$db->setQuery($q);
		$season_id = $db->loadResult();
		return $season_id;
	}
}
