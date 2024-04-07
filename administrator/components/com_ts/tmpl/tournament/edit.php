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
$model = $this->getModel();
$age_html = $model->BuildTournamentAgeCostLists($this->item->id);
?>
<style>
.tr_1 {background-color: #E6E6E6;}
.tr_0 {background-color: #f2f2f2;}
</style>
<form
	action="<?php echo Route::_('index.php?option=com_ts&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="tournament-form" class="form-validate form-horizontal">

	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'tournament')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'tournament', Text::_('COM_TS_TAB_TOURNAMENT', true)); ?>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">
				
				<?php echo $this->form->renderField('tournament_complete'); ?>
				<?php echo $this->form->renderField('tournament_name'); ?>
				<?php echo $this->form->renderField('tournament_start_date'); ?>
				<?php echo $this->form->renderField('tournament_end_date'); ?>
				<div class="control-group">
					<div class="control-label">
						<label id="jform_tournament_end_date-lbl" for="jform_tournament_end_date">
					Tournament End Date</label>
					</div>
					<div class="controls"><?php echo $age_html; ?>
					</div>
				</div>

				<?php echo $this->form->renderField('tournament_description'); ?>
				<?php echo $this->form->renderField('teams_registered'); ?>
				<?php //echo $this->form->renderField('season_id'); ?>
				<?php echo $this->form->renderField('tournament_notes'); ?>
				<?php echo $this->form->renderField('is_deleted'); ?>
				
			</fieldset>
		</div>
	</div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	
	
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[season_id]" value="<?php echo $model->getCurrentseason_id(); ?>" />
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>

	
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
