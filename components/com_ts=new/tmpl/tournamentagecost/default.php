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
?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_TOURNAMENTAGECOST_TOURNAMENT_COST'); ?></th>
			<td><?php echo $this->item->tournament_cost; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_TOURNAMENTAGECOST_TOURN_CAPACITY'); ?></th>
			<td><?php echo $this->item->tourn_capacity; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_TOURNAMENTAGECOST_FIELD_LOCATION_DESCRIPTION'); ?></th>
			<td><?php echo nl2br($this->item->field_location_description); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_TOURNAMENTAGECOST_TOURNAMENT_RESULTS'); ?></th>
			<td><?php echo nl2br($this->item->tournament_results); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_TOURNAMENTAGECOST_AGE_ID'); ?></th>
			<td><?php echo $this->item->age_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_TOURNAMENTAGECOST_TOURNAMENT_ID'); ?></th>
			<td><?php echo $this->item->tournament_id; ?></td>
		</tr>

	</table>

</div>

<?php $canCheckin = Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_ts.' . $this->item->id) || $this->item->checked_out == Factory::getApplication()->getIdentity()->id; ?>
	<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_ts&task=tournamentagecost.edit&id='.$this->item->id); ?>"><?php echo Text::_("COM_TS_EDIT_ITEM"); ?></a>
	<?php elseif($canCheckin && $this->item->checked_out > 0) : ?>
	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_ts&task=tournamentagecost.checkin&id=' . $this->item->id .'&'. Session::getFormToken() .'=1'); ?>"><?php echo Text::_("JLIB_HTML_CHECKIN"); ?></a>

<?php endif; ?>

<?php if (Factory::getApplication()->getIdentity()->authorise('core.delete','com_ts.tournamentagecost.'.$this->item->id)) : ?>

	<a class="btn btn-danger" rel="noopener noreferrer" href="#deleteModal" role="button" data-bs-toggle="modal">
		<?php echo Text::_("COM_TS_DELETE_ITEM"); ?>
	</a>

	<?php echo HTMLHelper::_(
                                    'bootstrap.renderModal',
                                    'deleteModal',
                                    array(
                                        'title'  => Text::_('COM_TS_DELETE_ITEM'),
                                        'height' => '50%',
                                        'width'  => '20%',
                                        
                                        'modalWidth'  => '50',
                                        'bodyHeight'  => '100',
                                        'footer' => '<button class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button><a href="' . Route::_('index.php?option=com_ts&task=tournamentagecost.remove&id=' . $this->item->id, false, 2) .'" class="btn btn-danger">' . Text::_('COM_TS_DELETE_ITEM') .'</a>'
                                    ),
                                    Text::sprintf('COM_TS_DELETE_CONFIRM', $this->item->id)
                                ); ?>

<?php endif; ?>