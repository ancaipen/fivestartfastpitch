<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

defined ('_JEXEC') or die ('restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\HTML\HTMLHelper as JHtml;

$app =Factory::getApplication();
$task = $app->input->getCmd('task');
//$task = JRequest::getCmd('task');
$cid = $app->input->get('cid', [], 'ARRAY');
//$cid = JRequest::getVar('cid', array(), 'request', 'array');
$post_base_url = "https://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];

$post = $app->input->getArray($_POST);
//$post = JRequest::get('post');
//JHTML::_('behavior.calendar');
$rows = null;
$row_count = 0;

//declare all local variables
$tournament_name = '';
$tournament_cost_id = -1;
$tournament_id = -1;
$age_id = -1;
$age = '';
$tournament_cost = "";
$tournament_results = "";
$field_location_description = "";
$tourn_capacity = "";

if($task=="" && !isset($cid[0]))
{

    //set the toolbar here
    tsAdminModeltournament_cost::setAllTournamentcostToolbar();

    $dd_tournament_id = '';
    $dd_age_id = '';

    //set filter sessions if found
    if($_SERVER["REQUEST_METHOD"] == 'POST')
    {

        if(isset($_POST['tournament_id']))
        {   if($_POST['tournament_id'] != "-1")
            {$dd_tournament_id = filter_var(trim($_POST['tournament_id']), FILTER_VALIDATE_INT);}
            else
            {$dd_tournament_id = '';}
        }

        if(isset($_POST['age_id']))
        {   if($_POST['age_id'] != "-1")
            {$dd_age_id = filter_var(trim($_POST['age_id']), FILTER_VALIDATE_INT);}
            else
            {$dd_age_id = '';}
        }
    }

    //get dropdowns html
    $agedd = tsAdminModeltournament_cost::dropdown_age($dd_age_id, true);
    $tourndd = tsAdminModeltournament_cost::dropdown_tournament($dd_tournament_id,true);

    //get data for form here
    $rows = tsAdminModeltournament_cost::select_tournament_cost('',$dd_tournament_id,$dd_age_id);
    $row_count = count($rows);

}
else
{
    if(isset($cid[0]))
    {
        $rows = tsAdminModeltournament_cost::select_tournament_cost($cid[0],'');
        $row_count = count($rows);
    }

    //set all local variables
    if($row_count > 0)
    {
        foreach ($rows as $row)
        {
            //set tournament_cost_id numeric value
            if(isset($row->tournament_cost_id))
            { $tournament_cost_id = $row->tournament_cost_id; }
            if(isset($row->tournament_name))
            { $tournament_name = $row->tournament_name; }
            //set tournament_id numeric value
            if(isset($row->tournament_id))
            { $tournament_id = $row->tournament_id; }
            //set age_id numeric value
            if(isset($row->age_id))
            { $age_id = $row->age_id; }
            if(isset($row->age))
            { $age = $row->age; }
            //set tournament_cost text value
            if(isset($row->tournament_cost))
            { $tournament_cost = $row->tournament_cost; }
            //set tournament_results text value
            if(isset($row->tournament_results))
            { $tournament_results = $row->tournament_results; }
            //set field_location_description text value
            if(isset($row->field_location_description))
            { $field_location_description = $row->field_location_description; }
			if(isset($row->tourn_capacity))
            { $tourn_capacity = $row->tourn_capacity; }
        }
    }
}

?>

<?php if($task=="" && !isset($cid[0])) { ?>
<!-- VIEW MODE START -->
<form action="/administrator/index.php?option=com_ts&view=tournament_cost" method="post" name="adminForm" id="adminForm">
    <table cellpadding="3" cellspacing="0">
        <tr>
            <td><?php echo $tourndd; ?></td>
            <td><?php echo $agedd; ?></td>
            <td><input type="submit" value="filter" /></td>
        </tr>
    </table>
    <table class="table table-striped">
                <thead>
                        <tr>
                            <th width="20">
							<?php echo JHtml::_('grid.checkall'); ?>
							</th>
                            <th nowrap="nowrap" class="title">Tournament Name</th>
                            <th nowrap="nowrap" class="title">Age</th>
                        </tr>
                </thead>
                <tbody>
                        <?php

                        $k = 0;
                        for ($i=0, $n=count($rows); $i < $n; $i++) {

                                $row = &$rows[$i];

                                $checked = JHTML::_('grid.id', $i, $row->tournament_cost_id);
                                $link = 'index.php?option=com_ts&view=tournament_cost&task=edit&cid[]='. $row->tournament_cost_id;

                                ?>
                                <tr class="<?php echo "row$k"; ?>">
                                        <td align="center">
                                                <?php echo $checked; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo $link; ?>" title="Edit Tournament Id">
                                                <?php echo $row->tournament_name; ?>
                                            </a>
                                        </td>
                                        <td><?php echo $row->age; ?></td>
                                <?php

                                $k = 1 - $k;
                        }

                        ?>
                </tbody>
        </table>

        <input type="hidden" name="option" value="com_ts" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />

</form>
<!-- VIEW MODE END -->
<?php } else { ?>
<?php

$c_id = null;
if(isset($cid[0]))
{$c_id=$cid[0];}

//set toolbar
tsAdminModeltournament_cost::setTournamentcostToolbar($c_id);

 //get dropdowns html
$agedd = tsAdminModeltournament_cost::dropdown_age($age_id, false);
$tourndd = tsAdminModeltournament_cost::dropdown_tournament($tournament_id,false);
$file_html = tsAdminModeltournament_cost::display_files($age_id,$tournament_id);

?>
<!-- EDIT FORM START -->
<form action="<?php echo $post_base_url; ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
<table border="0" cellpadding="5" cellspacing="0" class="tblprimary">
<tr>
  <td valign="top" class="left_col">Tournament:</td>
  <td valign="top" class="right_col"><?php echo $tourndd; ?></td>
</tr>
<tr>
  <td valign="top" class="left_col">Age:</td>
  <td valign="top" class="right_col"><?php echo $agedd; ?></td>
</tr>
<tr>
  <td valign="top" class="left_col">Tournament Cost:</td>
  <td valign="top" class="right_col"><input type="text" name="tournament_cost" id="tournament_cost" value="<?php echo $tournament_cost; ?>" /></td>
</tr>
<tr>
  <td valign="top" class="left_col">Tournament Capacity:</td>
  <td valign="top" class="right_col"><input type="text" name="tourn_capacity" id="tourn_capacity" value="<?php echo $tourn_capacity; ?>" /></td>
</tr>
<tr>
  <td valign="top" class="left_col">Tournament Files:</td>
  <td valign="top" class="right_col">

  <a href="javascript:void(0);" OnClick="add_upload();">Add File</a>
  <div id="filediv_0" style="margin-bottom: 4px;">
  <input name="filedesc_0" id="filedesc_0" type="text" style="width:200px;" />&nbsp;&nbsp;
  <input name="file_0" id="file_0" type="file" />
  </div>
  <div id="file_container"></div>
  <input name="filecount" id="filecount" type="hidden" value="1" />
  <?php echo $file_html; ?>
  <input type="hidden" id="file_delete" name="file_delete" value="0" />
  </td>
</tr>
<tr>
  <td valign="top" class="left_col">Tournament Notes:</td>
  <td valign="top" class="right_col">
  <?php

   $editor = Editor::getInstance(Factory::getConfig()->get('editor'));
    //$editor = JFactory::getEditor();
    $params = array( 'smilies'=> '0' ,
                     'style'  => '1' ,  
                     'layer'  => '0' , 
                     'table'  => '0' ,
                     'clear_entities'=>'0'
                     );
    echo $editor->display( 'tournament_results', $tournament_results, '600', '400', '20', '20', false, null, null, null, $params );               

    
  ?>
  </td>
</tr>
<tr>
  <td valign="top" class="left_col">Field Location Description:</td>
  <td valign="top" class="right_col">
  <?php
    
  
    $params = array( 'smilies'=> '0' ,
                     'style'  => '1' ,  
                     'layer'  => '0' , 
                     'table'  => '0' ,
                     'clear_entities'=>'0'
                     );
    echo $editor->display( 'field_location_description', $field_location_description, '600', '400', '20', '20', false, null, null, null, $params );               

  ?>
  </td>
</tr>
</table>

<input type="hidden" name="tournament_cost_id" value="<?php echo $tournament_cost_id; ?>" />
<input type="hidden" name="option" value="com_ts" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="cid[]" value="<?php echo $tournament_cost_id; ?>" />
<input type="hidden" name="controller" value="" />

</form>
<!-- EDIT FORM START -->

<!-- VALIDATION START -->
<style type="text/css">
    .invalid {border: solid 1px #ff0000;}
    .message_error
    {
        padding: 5px;
        border: solid 1px #ff0000;
        background-color:#FFCCCC;
        font-weight:bold;
        font-size: 12px;
    }
    .message_success
    {
        padding: 5px;
        border: solid 1px #00CC33;
        background-color:#CCFFCC;
        font-weight:bold;
        font-size: 12px;
    }
    .file_spacer
    {
    clear: both;
    padding: 5px;
    border-bottom: 1px solid #ccc;
    }
    .btn_delete
    {
    background-image:url('images/publish_x.png');
    background-repeat: no-repeat;
    background-position: 5px center;
    padding: 5px 5px 5px 25px;
    }
</style>
<!-- VALIDATION END -->
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
<?php } ?>