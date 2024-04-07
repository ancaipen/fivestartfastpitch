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
use \Teamtournaments\Component\Ts\Site\Helper\TsHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_ts', JPATH_SITE);

$user    = Factory::getApplication()->getIdentity();
$canEdit = TsHelper::canUserEdit($this->item, $user);


?>

<div class="register-edit front-end-edit">
	<?php if (!$canEdit) : ?>
		<h3>
		<?php throw new \Exception(Text::_('COM_TS_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo Text::sprintf('COM_TS_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
		<?php else: ?>
			<h1><?php echo Text::_('COM_TS_ADD_ITEM_TITLE'); ?></h1>
		<?php endif; ?>

		<form id="form-register"
			  action="<?php echo Route::_('index.php?option=com_ts&task=registerform.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
	<input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo isset($this->item->state) ? $this->item->state : ''; ?>" />

	<input type="hidden" name="jform[ordering]" value="<?php echo isset($this->item->ordering) ? $this->item->ordering : ''; ?>" />

	<input type="hidden" name="jform[checked_out]" value="<?php echo isset($this->item->checked_out) ? $this->item->checked_out : ''; ?>" />

	<input type="hidden" name="jform[checked_out_time]" value="<?php echo isset($this->item->checked_out_time) ? $this->item->checked_out_time : ''; ?>" />

				<?php echo $this->form->getInput('created_by'); ?>
				<?php echo $this->form->getInput('modified_by'); ?>
	<input type="hidden" name="jform[team_manager_1]" value="<?php echo isset($this->item->team_manager_1) ? $this->item->team_manager_1 : ''; ?>" />

	<input type="hidden" name="jform[team_address]" value="<?php echo isset($this->item->team_address) ? $this->item->team_address : ''; ?>" />

	<input type="hidden" name="jform[level_play]" value="<?php echo isset($this->item->level_play) ? $this->item->level_play : ''; ?>" />

	<input type="hidden" name="jform[registration_number]" value="<?php echo isset($this->item->registration_number) ? $this->item->registration_number : ''; ?>" />

	<input type="hidden" name="jform[team_name]" value="<?php echo isset($this->item->team_name) ? $this->item->team_name : ''; ?>" />

	<input type="hidden" name="jform[team_manager_2]" value="<?php echo isset($this->item->team_manager_2) ? $this->item->team_manager_2 : ''; ?>" />

	<input type="hidden" name="jform[team_city]" value="<?php echo isset($this->item->team_city) ? $this->item->team_city : ''; ?>" />

	<input type="hidden" name="jform[team_state]" value="<?php echo isset($this->item->team_state) ? $this->item->team_state : ''; ?>" />

	<input type="hidden" name="jform[team_zip]" value="<?php echo isset($this->item->team_zip) ? $this->item->team_zip : ''; ?>" />

	<input type="hidden" name="jform[home_phone]" value="<?php echo isset($this->item->home_phone) ? $this->item->home_phone : ''; ?>" />

	<input type="hidden" name="jform[cell_phone_2]" value="<?php echo isset($this->item->cell_phone_2) ? $this->item->cell_phone_2 : ''; ?>" />

	<input type="hidden" name="jform[email_1]" value="<?php echo isset($this->item->email_1) ? $this->item->email_1 : ''; ?>" />

	<input type="hidden" name="jform[season_id]" value="<?php echo isset($this->item->season_id) ? $this->item->season_id : ''; ?>" />

	<input type="hidden" name="jform[reg_status]" value="<?php echo isset($this->item->reg_status) ? $this->item->reg_status : ''; ?>" />

				<?php echo $this->form->getInput('date_submitted'); ?>
	<input type="hidden" name="jform[league_affiliation]" value="<?php echo isset($this->item->league_affiliation) ? $this->item->league_affiliation : ''; ?>" />

	<input type="hidden" name="jform[email_2]" value="<?php echo isset($this->item->email_2) ? $this->item->email_2 : ''; ?>" />

	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'tournament')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'tournament', Text::_('COM_TS_TAB_TOURNAMENT', true)); ?>
	<?php echo $this->form->renderField('comments'); ?>

	<?php echo $this->form->renderField('cell_phone_1'); ?>

	<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<span class="fas fa-check" aria-hidden="true"></span>
							<?php echo Text::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn btn-danger"
					   href="<?php echo Route::_('index.php?option=com_ts&task=registerform.cancel'); ?>"
					   title="<?php echo Text::_('JCANCEL'); ?>">
					   <span class="fas fa-times" aria-hidden="true"></span>
						<?php echo Text::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_ts"/>
			<input type="hidden" name="task"
				   value="registerform.save"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
