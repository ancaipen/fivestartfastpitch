<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Ts
 * @author     Percept <perceptinfotech2@gmail.com>
 * @copyright  2023 Percept
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Session\Session;
use \Joomla\CMS\User\UserFactoryInterface;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
$app = Factory::getApplication();
$user       = Factory::getApplication()->getIdentity();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_ts') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'tournamentform.xml');
$canEdit    = $user->authorise('core.edit', 'com_ts') && file_exists(JPATH_COMPONENT .  DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'tournamentform.xml');
$canCheckin = $user->authorise('core.manage', 'com_ts');
$canChange  = $user->authorise('core.edit.state', 'com_ts');
$canDelete  = $user->authorise('core.delete', 'com_ts');

// Import CSS
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_ts.list');
$model = $this->getModel();
$tournament_id = "";
$tournament_id = $app->input->get('tournament_id');
$age_id = "";
$age_id = $app->input->get('age_id');
if ($tournament_id != '' && $age_id == '')
{
//print out ages here
  $html = $model->GetTournamentResults($tournament_id);

}
else if ($tournament_id != '' && $age_id != '')
{
//print out schedule/results here

  $html = $model->GetScheduleResults($tournament_id, $age_id);

}
echo $html;
?>

