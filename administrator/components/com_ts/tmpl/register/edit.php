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

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
?>

<form
	action="<?php echo Route::_('index.php?option=com_ts&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="register-form" class="form-validate form-horizontal">

	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'tournament')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'tournament', Text::_('COM_TS_TAB_TOURNAMENT', true)); ?>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo Text::_('COM_TS_FIELDSET_TOURNAMENT'); ?></legend>
				<?php echo $this->form->renderField('comments'); ?>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo Text::_('COM_TS_FIELDSET_TOURNAMENT'); ?></legend>
				<?php echo $this->form->renderField('cell_phone_1'); ?>
			</fieldset>
		</div>
	</div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>
	<input type="hidden" name="jform[team_manager_1]" value="<?php echo $this->item->team_manager_1; ?>" />
	<input type="hidden" name="jform[team_address]" value="<?php echo $this->item->team_address; ?>" />
	<input type="hidden" name="jform[level_play]" value="<?php echo $this->item->level_play; ?>" />
	<input type="hidden" name="jform[registration_number]" value="<?php echo $this->item->registration_number; ?>" />
	<input type="hidden" name="jform[team_name]" value="<?php echo $this->item->team_name; ?>" />
	<input type="hidden" name="jform[team_manager_2]" value="<?php echo $this->item->team_manager_2; ?>" />
	<input type="hidden" name="jform[team_city]" value="<?php echo $this->item->team_city; ?>" />
	<input type="hidden" name="jform[team_state]" value="<?php echo $this->item->team_state; ?>" />
	<input type="hidden" name="jform[team_zip]" value="<?php echo $this->item->team_zip; ?>" />
	<input type="hidden" name="jform[home_phone]" value="<?php echo $this->item->home_phone; ?>" />
	<input type="hidden" name="jform[cell_phone_2]" value="<?php echo $this->item->cell_phone_2; ?>" />
	<input type="hidden" name="jform[email_1]" value="<?php echo $this->item->email_1; ?>" />
	<input type="hidden" name="jform[season_id]" value="<?php echo $this->item->season_id; ?>" />
	<input type="hidden" name="jform[reg_status]" value="<?php echo $this->item->reg_status; ?>" />
	<?php echo $this->form->renderField('date_submitted'); ?>
	<input type="hidden" name="jform[league_affiliation]" value="<?php echo $this->item->league_affiliation; ?>" />
	<input type="hidden" name="jform[email_2]" value="<?php echo $this->item->email_2; ?>" />

	
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
