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
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_HOME_TEAM'); ?></th>
			<td><?php echo $this->item->home_team; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_VISITOR_TEAM'); ?></th>
			<td><?php echo $this->item->visitor_team; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_HOME_POOL'); ?></th>
			<td>
			<?php

			if (!empty($this->item->home_pool) || $this->item->home_pool === 0)
			{
				echo Text::_('COM_TS_GAMES_HOME_POOL_OPTION_' . strtoupper(str_replace(' ', '_',$this->item->home_pool)));
			}
			?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_GAME_DATE'); ?></th>
			<td><?php echo $this->item->game_date; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_GAME_TYPE'); ?></th>
			<td>
			<?php

			if (!empty($this->item->game_type) || $this->item->game_type === 0)
			{
				echo Text::_('COM_TS_GAMES_GAME_TYPE_OPTION_' . strtoupper(str_replace(' ', '_',$this->item->game_type)));
			}
			?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_FIELD_LOCATION'); ?></th>
			<td><?php echo $this->item->field_location; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_HOME_SCORE'); ?></th>
			<td><?php echo $this->item->home_score; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_VISITOR_SCORE'); ?></th>
			<td><?php echo $this->item->visitor_score; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_NOTES'); ?></th>
			<td><?php echo nl2br($this->item->notes); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_AGE_ID'); ?></th>
			<td><?php echo $this->item->age_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_TOURNAMENT_ID'); ?></th>
			<td><?php echo $this->item->tournament_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_VISITOR_POOL'); ?></th>
			<td>
			<?php

			if (!empty($this->item->visitor_pool) || $this->item->visitor_pool === 0)
			{
				echo Text::_('COM_TS_GAMES_VISITOR_POOL_OPTION_' . strtoupper(str_replace(' ', '_',$this->item->visitor_pool)));
			}
			?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_GAME_TIME'); ?></th>
			<td><?php echo $this->item->game_time; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_GAME_ORDER'); ?></th>
			<td><?php echo $this->item->game_order; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_TS_FORM_LBL_GAME_GAME_ACTIVE'); ?></th>
			<td><?php echo $this->item->game_active; ?></td>
		</tr>

	</table>

</div>

<?php $canCheckin = Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_ts.' . $this->item->id) || $this->item->checked_out == Factory::getApplication()->getIdentity()->id; ?>
	<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_ts&task=game.edit&id='.$this->item->id); ?>"><?php echo Text::_("COM_TS_EDIT_ITEM"); ?></a>
	<?php elseif($canCheckin && $this->item->checked_out > 0) : ?>
	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_ts&task=game.checkin&id=' . $this->item->id .'&'. Session::getFormToken() .'=1'); ?>"><?php echo Text::_("JLIB_HTML_CHECKIN"); ?></a>

<?php endif; ?>

<?php if (Factory::getApplication()->getIdentity()->authorise('core.delete','com_ts.game.'.$this->item->id)) : ?>

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
                                        'footer' => '<button class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button><a href="' . Route::_('index.php?option=com_ts&task=game.remove&id=' . $this->item->id, false, 2) .'" class="btn btn-danger">' . Text::_('COM_TS_DELETE_ITEM') .'</a>'
                                    ),
                                    Text::sprintf('COM_TS_DELETE_CONFIRM', $this->item->id)
                                ); ?>

<?php endif; ?>