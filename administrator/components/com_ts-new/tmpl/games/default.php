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
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

// Import CSS
$wa =  $this->document->getWebAssetManager();
$wa->useStyle('com_ts.admin')
    ->useScript('com_ts.admin');

$user      = Factory::getApplication()->getIdentity();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_ts');

$saveOrder = $listOrder == 'a.ordering';

if (!empty($saveOrder))
{
	$saveOrderingUrl = 'index.php?option=com_ts&task=games.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
}
$model = $this->getModel();
?>

<form action="<?php echo Route::_('index.php?option=com_ts&view=games'); ?>" method="post"
	  name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
			<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

				<div class="clearfix"></div>
				<table class="table table-striped" id="gameList">
					<thead>
					<tr>
						<th class="w-1 text-center">
							<input type="checkbox" autocomplete="off" class="form-check-input" name="checkall-toggle" value=""
								   title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
						</th>
						
					<?php if (isset($this->items[0]->ordering)): ?>
					<th scope="col" class="w-1 text-center d-none d-md-table-cell">

					<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>

					</th>
					<?php endif; ?>

						
					<th  scope="col" class="w-1 text-center">
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
						<th class='left'>Season</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TS_GAMES_TOURNAMENT_ID', 't.tournament_name', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TS_GAMES_AGE_ID', 'a.age_id', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TS_GAMES_GAME_DATE', 'a.game_date', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TS_GAMES_GAME_TIME', 'a.game_time', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TS_GAMES_GAME_TYPE', 'a.game_type', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TS_GAMES_HOME_TEAM', 'a.home_team', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TS_GAMES_HOME_SCORE', 'a.home_score', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TS_GAMES_VISITOR_TEAM', 'a.visitor_team', $listDirn, $listOrder); ?>
						</th>
						
						
						
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TS_GAMES_VISITOR_SCORE', 'a.visitor_score', $listDirn, $listOrder); ?>
						</th>
						
						
						
						
					<th scope="col" class="w-3 d-none d-lg-table-cell" >

						<?php echo HTMLHelper::_('searchtools.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>					</th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
					<tbody <?php if (!empty($saveOrder)) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" <?php endif; ?>>
					<?php 
						
					foreach ($this->items as $i => $item) :
						$ordering   = ($listOrder == 'a.ordering');
						$canCreate  = $user->authorise('core.create', 'com_ts');
						$canEdit    = $user->authorise('core.edit', 'com_ts');
						$canCheckin = $user->authorise('core.manage', 'com_ts');
						$canChange  = $user->authorise('core.edit.state', 'com_ts');
						$game_link ='index.php?option=com_ts&view=game&layout=edit&id='.$item->id;
						 //show inactive games
						 $tr_style = "";
						 $game_active = false;
						 $game_active = $item->game_active;

						 if($game_active == false)
						 {
							 $tr_style = ' style="background-color:#FFCCCC;"';
						 }
						
						?>
						<tr class="row<?php echo $i % 2; ?>" <?php echo $tr_style; ?> data-draggable-group='1' data-transition>
							<td class="text-center" <?php echo $tr_style; ?>>
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>
							
							<?php if (isset($this->items[0]->ordering)) : ?>

							<td class="text-center d-none d-md-table-cell" <?php echo $tr_style; ?>>

							<?php

							$iconClass = '';

							if (!$canChange)

							{
								$iconClass = ' inactive';

							}
							elseif (!$saveOrder)

							{
								$iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');

							}							?>							<span class="sortable-handler<?php echo $iconClass ?>">
							<span class="icon-ellipsis-v" aria-hidden="true"></span>
							</span>
							<?php if ($canChange && $saveOrder) : ?>
							<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order hidden">
								<?php endif; ?>
							</td>
							<?php endif; ?>

							
							<td class="text-center" <?php echo $tr_style; ?>>
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'games.', $canChange, 'cb'); ?>
							</td>
							<td <?php echo $tr_style; ?>><?php echo $item->season_name; ?></td>
							<td <?php echo $tr_style; ?>>
								<?php echo $item->tournament_name; ?>
							</td>
							<td <?php echo $tr_style; ?>>
								<?php echo $item->age; ?>
							</td>
							<td <?php echo $tr_style; ?>>
								<a href="<?php echo $game_link ?>">
									<?php echo HTMLHelper::date($item->game_date); ?>
								</a>
							</td>
							<td <?php echo $tr_style; ?>>
								<?php 
								echo date('h:i a',strtotime($item->game_time));
								 ?>
							</td>
							<td <?php echo $tr_style; ?>>
								<?php echo $item->game_type; ?>
							</td>

							<td <?php echo $tr_style; ?>>
								<?php echo $item->home_team; ?>
							</td>
							<td <?php echo $tr_style; ?>>
								<?php echo $item->home_score; ?>
							</td>
							<td <?php echo $tr_style; ?>>
								<?php echo $item->visitor_team; ?>
							</td>
							
							
							
							<td <?php echo $tr_style; ?>>
								<?php echo $item->visitor_score; ?>
							</td>
							
							
							
							
							<td class="d-none d-lg-table-cell" <?php echo $tr_style; ?>>
							<?php echo $item->id; ?>

							</td>


						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="boxchecked" value="0"/>
				<input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>