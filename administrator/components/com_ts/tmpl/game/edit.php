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
	method="post" enctype="multipart/form-data" name="adminForm" id="game-form" class="form-validate form-horizontal">

	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'game')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'game', Text::_('Game', true)); ?>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">
			<?php echo $this->form->renderField('game_active'); ?>
				<?php echo $this->form->renderField('tournament_id'); ?>
				<?php echo $this->form->renderField('age_id'); ?>
				<?php echo $this->form->renderField('game_date'); ?>
				<?php echo $this->form->renderField('game_time'); ?>
				<?php echo $this->form->renderField('game_type'); ?>
				<?php echo $this->form->renderField('game_order'); ?>
				<?php echo $this->form->renderField('field_location'); ?>

				<?php echo $this->form->renderField('home_team'); ?>
				<?php echo $this->form->renderField('home_pool'); ?>
				<?php echo $this->form->renderField('home_score'); ?>

				<?php echo $this->form->renderField('visitor_team'); ?>
				<?php echo $this->form->renderField('visitor_pool'); ?>				
				<?php echo $this->form->renderField('visitor_score'); ?>

				<?php echo $this->form->renderField('notes'); ?>	
				
				<?php echo $this->form->renderField('test_game_time'); ?>
				
			</fieldset>
		</div>
	</div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<!-- <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" /> -->
	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>
	<!-- <input type="hidden" name="jform[home_seed]" value="<?php echo $this->item->home_seed; ?>" />
	<input type="hidden" name="jform[visitor_seed]" value="<?php echo $this->item->visitor_seed; ?>" /> -->

	
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
