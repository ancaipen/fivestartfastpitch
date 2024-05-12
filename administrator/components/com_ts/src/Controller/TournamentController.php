<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Ts
 * @author     Percept <perceptinfotech2@gmail.com>
 * @copyright  2023 Percept
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Teamtournaments\Component\Ts\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use \Joomla\CMS\Factory;
use \Joomla\Database\DatabaseDriver;
use \Joomla\CMS\Table\Table;

/**
 * Tournament controller class.
 *
 * @since  1.0.0
 */
class TournamentController extends FormController
{
	protected $view_list = 'tournaments';
	
	public function save($key = null, $urlVar = null)
    {
		
		$date = Factory::getDate();
		$task = Factory::getApplication()->input->get('task');
		$user = Factory::getApplication()->getIdentity();
		
		$input = Factory::getApplication()->input;
		$task = $input->getString('task', '');
		$tournament_id = $input->get('id');
		
		if($tournament_id != 0)
		{
			
			$db = Factory::getDbo();
			$query = "SELECT * FROM #__ts_age ORDER BY age_num";			
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row)
			{
								
				$age_id_check = $input->get('ageid_'.$row->age_id);
				$age_id = intval($row->age_id);
				$tournament_cost = $input->get('tourncost_'.$row->age_id);
				
				//echo 'ageid checkbox: '.$age_id_check.'-'.$age_id.'<br>';
							
				//save data
				if($age_id_check =='on' && $tournament_cost > 0)
				{
					$qq= "SELECT `id` FROM `#__ts_tournament_age_cost` WHERE `age_id`='".$age_id."' AND `tournament_id`='".$tournament_id."'";
					$db->setQuery($qq);
					$ttac = $db->loadResult();
					
					if($ttac){
						$upd = "UPDATE `#__ts_tournament_age_cost` SET `tournament_cost`='".$tournament_cost."',`age_id`='".$age_id."' WHERE `tournament_id`='".$tournament_id."' AND `age_id`='".$age_id."'";
						$db->setQuery($upd);						
						$db->execute();
					}
					else
					{
						$query = "INSERT INTO #__ts_tournament_age_cost (tournament_id, age_id, tournament_cost, tournament_results, field_location_description)
						VALUES (".$tournament_id.",
						".$age_id.",
						".$tournament_cost.",
						'',
						'')";
						$db->setQuery($query);						
						$result = $db->execute();
					}
				}
				else if($age_id_check == '')
				{
					//delete row
					$query_delete = "DELETE FROM #__ts_tournament_age_cost WHERE tournament_id = ".$tournament_id.' AND age_id = '.$age_id;
					$db->setQuery($query_delete);						
					$result = $db->execute();
					//echo $tournament_cost.'-'.$age_id_check.'-'.$query_delete.'<br>';
				}
				
			}
		}
		
        // Call parent save function
        $result = parent::save($key, $urlVar);


        return $result;
    }
	
}
