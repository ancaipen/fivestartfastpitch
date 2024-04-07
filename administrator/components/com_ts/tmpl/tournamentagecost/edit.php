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
$file_html='';
if($this->item->age_id){
	$file_html = $model->display_files($this->item->age_id,$this->item->tournament_id);
}


?>

<form
	action="<?php echo Route::_('index.php?option=com_ts&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="tournamentagecost-form" class="form-validate form-horizontal">

	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'tournament')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'tournament', Text::_('Tournament Detail By Age', true)); ?>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">
				
				<?php echo $this->form->renderField('tournament_id'); ?>
				<?php echo $this->form->renderField('age_id'); ?>
				<?php echo $this->form->renderField('tournament_cost'); ?>
				<?php echo $this->form->renderField('tourn_capacity'); ?>
				<div class="control-group">
					<div class="control-label"><label id="jform_file_0-lbl" for="jform_file_0">
					Tournament Files:</label>
					</div>
					<div class="controls has-success">
						<a href="javascript:void(0);" OnClick="add_upload();">Add File</a>
						<div id="filediv_0" style="margin-bottom: 4px;">
						<input name="filedesc_0" id="filedesc_0" type="text" style="width:200px;" />&nbsp;&nbsp;
						<input name="file_0" id="file_0" type="file" />
						</div>
						<div id="file_container"></div>
						<input name="filecount" id="filecount" type="hidden" value="1" />
						<?php echo $file_html; ?>
						<input type="hidden" id="file_delete" name="file_delete" value="0" />
					</div>
				</div>
				<?php echo $this->form->renderField('tournament_results'); ?>
				<?php echo $this->form->renderField('field_location_description'); ?>
				
				
				
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

	
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
<script language="javascript" type="text/javascript">

function add_upload()
{

    var _filecount = document.getElementById("filecount");
    var count = 0;
    count = parseInt(_filecount.value);

    var _appendfiles = document.getElementById("file_container");
    if(_appendfiles != null)
    {

        var html = "";

        html += '<div id="filediv_'+count+'" style="margin-bottom: 4px;">';
        html += '<input name="filedesc_'+count+'" id="filedesc_'+count+'" type="text" style="width:200px;" />&nbsp;&nbsp;';
        html += '<input name="file_'+count+'" id="file_'+count+'[]" type="file" />';
        html += '&nbsp;<a href="javascript:void(0);" class="remove_link_'+count+'" onclick="javascript:remove_upload('+count+');">Remove</a>';
        html += '</div>';

        _appendfiles.innerHTML += html;

        count = (count + 1);
        _filecount.value = count;
    }

}

function remove_upload(count)
{

    var _divid = document.getElementById('filediv_'+count);
    var _appendfiles = document.getElementById("file_container");
    var _filecount = document.getElementById("filecount");

    var count = 0;
    count = parseInt(_filecount.value);

    if(_divid != null && _appendfiles != null)
    {
        _appendfiles.removeChild(_divid);
        count = (count - 1);
        _filecount.value = count;
    }

}

function delete_file(files_id)
{
    //set file id to delete and submit form
    if(confirm('Are you sure you want to DELETE this item?'))
    {
        var _filedelete = document.getElementById("file_delete");
        _filedelete.value = files_id;
        submitbutton('apply');
    }
}

function clear_upload(default_div_id)
{
    jQuery('#'+default_div_id+" :input").attr('value', '');
}

</script>