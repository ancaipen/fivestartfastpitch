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
use Joomla\CMS\Uri\Uri;
/**
 * Tournamentagecost model.
 *
 * @since  1.0.0
 */
class TournamentagecostModel extends AdminModel
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
	public $typeAlias = 'com_ts.tournamentagecost';

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
	public function getTable($type = 'Tournamentagecost', $prefix = 'Administrator', $config = array())
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
								'com_ts.tournamentagecost', 
								'tournamentagecost',
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
		$data = Factory::getApplication()->getUserState('com_ts.edit.tournamentagecost.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$data = $this->item;
			

			// Support for multiple or not foreign key field: age_id
			$array = array();

			foreach ((array) $data->age_id as $value)
			{
				if (!is_array($value))
				{
					$array[] = $value;
				}
			}
			if(!empty($array)){

			$data->age_id = $array;
			}

			// Support for multiple or not foreign key field: tournament_id
			$array = array();

			foreach ((array) $data->tournament_id as $value)
			{
				if (!is_array($value))
				{
					$array[] = $value;
				}
			}
			if(!empty($array)){

			$data->tournament_id = $array;
			}
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
	 * Method to duplicate an Tournamentagecost
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

	public function display_files($age_id,$tournament_id)
    {
		$db = $this->getDbo();
        $html = '';
        $mime_types = array(
        'image/png',
        'image/jpeg',
        'image/jpeg',
        'image/jpeg',
        'image/gif',
        'image/tiff',
        'image/tiff'
        );

        $query = "SELECT * FROM `#__ts_files` WHERE tournament_id=".($tournament_id)." AND age_id=".($age_id)."
        ORDER BY date_created DESC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $rowcount = count($rows);
        $upload_dir = Uri::root().'images/stories/tournaments/'.$tournament_id.'/';

        if($rowcount > 0)
        {
            $html .= '<div class="files_container">';
            $html .= '<table cellpadding="3" cellspacing="0">';

            foreach ($rows as $row)
            {
                $file_text = $row->file_name;
                if(trim($row->file_desc) != '')
                {
                    $file_text = $row->file_desc;
                }

                $filedelete_id = "'".$row->files_id."'";
                if (in_array($row->file_mime, $mime_types))
                {
                    $html .= '<tr><td><img src="'.$upload_dir.$row->file_name.'" width="50" />&nbsp;'.$file_text.'</td><td><a href="javascript:void(0);" class="btn_delete" OnClick="delete_file('.$filedelete_id.');">Delete</a></td></tr>';
                }
                else
                {
                    $html .= '<tr><td><a href="'.$upload_dir.$row->file_name.'" target="_blank">'.$file_text.'</a></td><td><a href="javascript:void(0);" class="btn_delete" OnClick="delete_file('.$filedelete_id.');">Delete</a></td></tr>';
                }
            }
            $html .= '</table>';
            $html .= '</div>';
        }

        return $html;
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
				$db->setQuery('SELECT MAX(ordering) FROM #__ts_tournament_age_cost');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}
}
