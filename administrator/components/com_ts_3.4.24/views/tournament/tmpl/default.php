<style type="text/css">
    
.tourn_admin_container input, .tourn_admin_container textarea{
	padding: 3px;
}

</style>

<?php

defined ('_JEXEC') or die ('restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Editor\Editor;

//hide notices


$document = Factory::getDocument();

//$document->addScript('../includes/js/joomla.javascript.js');
$app = Factory::getApplication();
$task = $app->input->getCmd('task');
//$task = JRequest::getCmd('task');

$cid = $app->input->get('cid', [], 'ARRAY');

//$cid = JRequest::getVar('cid', array(), 'request', 'array');

$post = $app->input->getArray($_POST);

//$post = JRequest::get('post');
//JHtml::_('behavior.calendar');

if($task=="" && !isset($cid[0]))
{
//gets all rows for admin section
$rows = TSAdminModelTournament::getTournamentData();
//set the toolbar here
TSAdminModelTournament::setAllTournToolbar();

?>
<!-- VIEW MODE START -->
<form action="index.php?option=com_ts" method="post" name="adminForm" id="adminForm">
        <table class="table table-striped">
                <thead>
                        <tr>
                                <th width="20">
								<?php echo JHtml::_('grid.checkall'); ?>
								</th>
                                <th nowrap="nowrap" class="title">Season</th>
                                <th nowrap="nowrap">Tournament Name</th>
                                <th nowrap="nowrap">Dates</th>
                        </tr>
                </thead>
                <tbody>
                        <?php

                        $k = 0;
                        for ($i=0, $n=count($rows); $i < $n; $i++) {

                                $row = &$rows[$i];

                                $checked = JHtml::_('grid.id', $i, $row->tournament_id );
                                $link = 'index.php?option=com_ts&task=edit&cid[]='. $row->tournament_id;

                                ?>

                                <tr class="<?php echo "row$k"; ?>">
                                        <td align="center">
                                                <?php echo $checked; ?>
                                        </td>
                                        <td>
                                                <?php echo $row->season_name; ?>
                                        </td>
                                        <td>
                                                <a href="<?php echo $link; ?>" title="Edit Tournament">
                                                        <?php 
                                                            $ages_list_html = TSAdminModelTournament::getTournamentAgeData($row->tournament_id);
                                                            echo $row->tournament_name . ' - '. $ages_list_html;
                                                        ?>
                                                </a>
                                        </td>
                                        <td>
                                                <?php echo JHTML::date($row->tournament_start_date).'-'.JHtml::date($row->tournament_end_date); ?>
                                        </td>
  
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
<?php
}else{

//set the toolbar here
//TSAdminModelTournament::setTournToolbar($cid);// replacement code for joomla 4
if(!isset($cid[0])){$cid[0]=0;}
TSAdminModelTournament::setTournToolbar($cid[0]);
$arr_ids = explode('_',$cid[0]);
//$arr_ids = explode(',',$cid['arr_ids'] ?? ''); // replacement code for joomla 4


//echo '<h1>ID: '.$arr_ids[0].'</h1>';

//get tournament information
$rows = array();

//declare all local variables
$tournament_name = '';
$tournament_start_date = -1;
$tournament_end_date = -1;
$tournament_description = "";
$teams_registered = "";
$tournament_notes = "";
// $tournament_results = "";
// $field_location_description = "";
// $tourn_capacity = "";
if(empty($rows)){

}

if(isset($arr_ids[0]))
{
	$rows = TSAdminModelTournament::getTournamentFormData($arr_ids[0]);
}

//creates checkbox list
$age_html = TSAdminModelTournament::BuildTournamentAgeCostLists($arr_ids[0]);

$tourn_complete = '';
if (!empty($rows) && isset($rows[0])) {
    $tourn_complete = ($rows[0]->tournament_complete == 1) ? 'CHECKED' : '';
}


// $tourn_complete = '';

// if($rows[0]->tournament_complete == 1)
// {
//     $tourn_complete = 'CHECKED';
// }

//$document = JFactory::getDocument();
//$document->addCustomTag( '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>' );
//$document->addCustomTag( '<script type="text/javascript" language="javascript">jQuery.noConflict();</script>' );

?>

<!-- EDIT MODE START -->
<form action="index.php?option=com_ts" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
    <style type="text/css">
        .hdr_title
        {
            font-weight: bold;
            text-decoration: underline;
        }
        .tr_0
        {
            background-color:#f2f2f2;
        }
        .tr_1
        {
            background-color:#E6E6E6;
        }
    </style>
    <div class ="tourn_admin_container">
    <table cellpadding="3" cellspacing="0" border="0" class="adminlist">
        <tr><td class="hdr_title">Tournament Complete:</td><td><input name="tournament_complete" type="checkbox" <?php echo @$tourn_complete; ?> /></td></tr>
        <tr><td class="hdr_title">Tournament Name:</td><td><input name="tournament_name" type="text" style="width:250px" value="<?php echo @$rows[0]->tournament_name; ?>"></td></tr>
        <tr><td class="hdr_title">Tournament Start Date:</td><td>
        <?php echo JHTML::_('calendar', @$rows[0]->tournament_start_date, "tournament_start_date" , "tournament_start_date", '%Y-%m-%d');?>
        
        </td></tr>
        <tr><td class="hdr_title">
        Tournament End Date:</td><td>
        <?php echo JHTML::_('calendar', @$rows[0]->tournament_end_date, "tournament_end_date" , "tournament_end_date", '%Y-%m-%d');?>        
        </td></tr>
        <tr><td valign="top" colspan="2" class="hdr_title">Tournament Age(s) and Cost:</td></tr>
        <tr><td valign="top" colspan="2"><?php //echo @$age_html; ?></td></tr>
        <tr><td valign="top" colspan="2" class="hdr_title">Tournament Description:</td></tr><tr><td colspan="2">
                <?php
                
                $editor = Editor::getInstance(Factory::getConfig()->get('editor'));
                
                $params = array( 'smilies'=> '0' ,
                                 'style'  => '1' ,  
                                 'layer'  => '0' , 
                                 'table'  => '0' ,
                                 'clear_entities'=>'0'
                                 );               

                echo $editor->display( 'tournament_description', @$rows[0]->tournament_description, '600', '400', '250', '250', false, null, null, null, $params );               
                
                ?></td></tr>
        <tr><td colspan="2" class="hdr_title">Teams Registered:</td></tr><tr><td colspan="2"><?php

             
                $params = array( 'smilies'=> '0' ,
                                 'style'  => '1' ,  
                                 'layer'  => '0' , 
                                 'table'  => '0' ,
                                 'clear_entities'=>'0'
                                 );
                echo $editor->display( 'teams_registered', @$rows[0]->teams_registered, '600', '400', '250', '250', false, null, null, null, $params );
                
                ?>
            </td></tr>
            <tr><td colspan="2" class="hdr_title">Field Locations</td></tr><tr><td colspan="2"><?php

                
                $params = array( 'smilies'=> '0' ,
                                 'style'  => '1' ,  
                                 'layer'  => '0' , 
                                 'table'  => '0' ,
                                 'clear_entities'=>'0'
                                 );
                echo $editor->display( 'tournament_notes', @$rows[0]->tournament_notes, '600', '400', '250', '250', false, null, null, null, $params );
                
                ?></td></tr>
        
    </table>
    </div>
    
<input type="hidden" name="option" value="com_ts" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="cid[]" value="<?php if(@$rows[0]->tournament_id!=""){ echo @$rows[0]->tournament_id.'_'.@$rows[0]->age_id;} ?>" />
<input type="hidden" name="controller" value="" />
<input type="hidden" name="tournament_cost_id" value="<?php echo @$rows[0]->tournament_cost_id; ?>" />
<input type="hidden" name="tournament_id" value="<?php echo @$rows[0]->tournament_id; ?>" />

<script language="javascript" type="text/javascript">

function enableField(chk_id, cost_id, default_div_id, div_id)
{
    var chk = document.getElementById(chk_id);

    if(chk.checked==false)
    {
       jQuery('input.#'+cost_id).attr('disabled', true);
       jQuery('input.#'+cost_id).attr('value', '');
       jQuery('#'+default_div_id+" :input").attr('disabled', true);
       jQuery('#'+default_div_id+" :input").attr('value', '');
       jQuery('#'+div_id+" :input").attr('disabled', true);
       jQuery('#'+div_id+" :input").attr('value', '');
       jQuery('#'+default_div_id+" :a.add_link").html("");
       jQuery('#'+default_div_id+" :a.remove_link").html("");
    }
    else
    {
        jQuery('input.#'+cost_id).removeAttr('disabled');
        jQuery('#'+default_div_id+" :input").removeAttr('disabled');
        jQuery('#'+div_id+" :input").removeAttr('disabled');
        jQuery('#'+default_div_id+" :a.add_link").html("Add File");
        jQuery('#'+default_div_id+" :a.remove_link").html("Remove");
    }

}


</script>

</form>
<!-- EDIT MODE END -->

<?php } ?>
