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
use \Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

$canEdit = Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_ts');

if (!$canEdit && Factory::getApplication()->getIdentity()->authorise('core.edit.own', 'com_ts'))
{
	$canEdit = Factory::getApplication()->getIdentity()->id == $this->item->created_by;
}
$model = $this->getModel();
$tournament_id =Factory::getApplication()->input->get('tournament_id'); 
$html = $model->GetScheduleInfo($tournament_id);
echo $html;
?>


