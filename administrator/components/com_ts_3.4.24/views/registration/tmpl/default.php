<style type="text/css">
    
.tourn_admin_container input, .tourn_admin_container textarea{
	padding: 3px;
}

</style>

<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

defined ('_JEXEC') or die ('restricted access');
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\MVC\Models\RegistrationModel;


jimport('joomla.application.component.model');

require_once JPATH_ADMINISTRATOR . '/components/com_ts/models/tournament_cost.php';

//hide notices
error_reporting (E_ALL ^ E_NOTICE);

$task = Factory::getApplication()->input->getCmd('task');
//$task = JRequest::getCmd('task');

$cid = Factory::getApplication()->input->get('cid', [], 'ARRAY');
//$cid = JRequest::getVar('cid', array(), 'request', 'array');
$dd_tourn_status = 'Some value';
$dd_tournament_id = '123';
$dd_age_id = 'test';
if (isset($cid[0])== true & $task != '')
{
    	
    $register_id = $cid[0];
    $model = new TSAdminModelRegistration;
    $rows = $model->getRegistrationForm($register_id, $dd_tourn_status, $dd_tournament_id, $dd_age_id);
	  //$rows =  TSAdminModelRegistration::getRegistrationForm($register_id, $dd_tourn_status, $dd_tournament_id, $dd_age_id);
    $arr_tourn =  TSAdminModelRegistration::GetTournamentRegInfo($register_id);
    
	TSAdminModelRegistration::setRegToolbarEdit();
    
 ?>


 <!-- START PAYMENT FORM -->

<form action="../ts_register/print.php" method="post" id="payment_form" target="_blank">
<div id="print_results">
<table width="525px" border="0" cellpadding="5" cellspacing="0" style="padding-top:10px; font-size: 12px;" class="contact_form">

<tr class="left_col">
    <td width="45%;" colspan="2" style="font-size: 18px; font-weight: bold;"><?php echo $rows[0]->team_name; ?></td>
</tr>
<tr class="left_col">
<td valign="top" colspan="2" style="padding-top: 15px;"><label for="tournaments_desired"><strong>Tournaments Desired:</strong></label></td>
</tr>

<tr><td colspan ="2">
    <?php
    echo $arr_tourn[0];
    ?>

    <div style="font-weight: bold; font-size: 14px;">Total Due: $ <?php echo $arr_tourn [1]; ?></div>

    </td></tr>


<tr class="left_col">
  <td valign="top"><p><strong>Please qualify your team's
  </strong><strong> level of play:</strong></p></td>
  <td><?php
    echo $rows[0]->level_play;
    ?></td>
</tr>
<tr class="left_col">
  <td valign="top"><p><strong>Current League Affiliation:</strong></p></td>
  <td><?php
    echo $rows[0]->league_affiliation;
    ?></td>
</tr>
<tr class="left_col">
  <td  style="padding-top: 15px;"><strong>Team Information:</strong></td>
  <td>&nbsp;</td>
</tr>
<tr class="left_col">
  <td  style="padding-top: 10px;"><strong>Manager/Team Contact 1:</strong></td>
  <td><?php
    echo $rows[0]->team_manager_1;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong>Manager/Team Contact 2:</strong></td>
  <td><?php
    echo $rows[0]->team_manager_2;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong>Address:</strong></td>
  <td><?php
    echo $rows[0]->team_address;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong>City</strong></td>
  <td><?php
    echo $rows[0]->team_city;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong>State:</strong></td>
  <td><?php
    echo $rows[0]->team_state;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong>Zip Code:</strong></td>
  <td><?php
    echo $rows[0]->team_zip;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong> Home Phone:</strong></td>
  <td><?php
    echo $rows[0]->home_phone;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong> Cell Phone 1:</strong></td>
  <td><?php

    echo $rows[0]->cell_phone_1;
    ?></td>
</tr>


<tr class="left_col">
  <td><strong> Cell Phone 2:</strong></td>
  <td><?php
    echo $rows[0]->cell_phone_2;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong> Email 1:</strong></td>
  <td><?php
    echo $rows[0]->email_1;
    ?></td>
</tr>

<tr class="left_col">
  <td><strong> Email 2:</strong></td>
  <td><?php
    echo $rows[0]->email_2;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong> Comments:</strong></td>
  <td><?php
    echo $rows[0]->comments;
    ?></td>
</tr>

 </table>
</div>
<input type="submit" name="Print" id="print_reg" value="Print" onClick="load_print_text();"/>
<input type="hidden" name="print_text" id="print_text"/>
</form>
<!-- END PAYMENT FORM -->
<script type="text/javascript" language="javascript">

//on page load
function load_print_text()
{
    var html = document.getElementById('print_results').innerHTML;
    document.getElementById('print_text').value = html;
}

</script>
<?php
}
else
{
    TSAdminModelRegistration::setAllRegToolbar();
	
		
	$dd_reg_status = '';
	$dd_tournament_id = '';
    $dd_age_id = '';

    //set filter sessions if found
    if($_SERVER["REQUEST_METHOD"] == 'POST')
    {
		
		if(isset($_POST['reg_status_0']))
        {   if($_POST['reg_status_0'] != "-1")
            {$dd_reg_status = filter_var(trim($_POST['reg_status_0']), FILTER_SANITIZE_STRING);}
            else
            {$dd_reg_status = '';}
        }
		
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
	$regstatusdd = TSAdminModelRegistration::dropdown_regstatus($dd_reg_status, '0', true);
    $agedd = tsAdminModeltournament_cost::dropdown_age($dd_age_id, true);
    $tourndd =  tsAdminModeltournament_cost::dropdown_tournament($dd_tournament_id,true);
	
    $rows =  TSAdminModelRegistration::getRegistrationData($dd_reg_status, $dd_tournament_id, $dd_age_id);

?>
     <!-- VIEW MODE START -->
<form action="index.php?option=com_ts&view=registration" method="post" id="adminForm" name="adminForm">
        <table cellpadding="3" cellspacing="0">
        <tr>
			<td><?php echo $regstatusdd; ?></td>
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
							<th nowrap="nowrap" class="title">Season</th>
							<th nowrap="nowrap">Team Name</th>
							<th nowrap="nowrap">Registration Status</th>
							<th nowrap="nowrap">Tournaments</th>
							<th nowrap="nowrap">Team Manager</th>
							<th nowrap="nowrap">Cell Phone</th>
							<th nowrap="nowrap">Email</th>
							<th nowrap="nowrap">Comments</th>
							<th nowrap="nowrap">Submission</th>
					</tr>
			</thead>
			<tbody>
					<?php

					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {

							$row = &$rows[$i];

							$checked = JHTML::_('grid.id', $i, $row->registration_id );
							$link = 'index.php?option=com_ts&view=registration&task=edit&cid[]='. $row->registration_id;

							?>

							<tr class="<?php echo "row$k"; ?>">
									 <td align="center">
											<?php echo $checked; ?>
									</td>
									<td style="text-align: center">
											<?php echo $row->season_name; ?>
									</td>
									<td>
										<a href="<?php echo $link; ?>" title="Edit Tournament">
												<?php echo $row->team_name; ?>
										</a>
									</td>
									<td style="text-align: center">
										<?php 
										$regstatus_dd = TSAdminModelRegistration::dropdown_regstatus($row->reg_status, $row->registration_id, false);
										echo $regstatus_dd; 
										?>
									</td>
									 <td>
											<?php echo TSAdminModelRegistration::getTournamentReg($row->registration_id); ?>
									</td>

									 <td>
											<?php echo $row->team_manager_1; ?>
									</td>
									 <td>
											<?php echo $row->cell_phone_1; ?>
									</td>

									 <td><a href="mailto:<?php echo $row->email_1; ?>"><?php echo $row->email_1; ?></a>
									</td>
									<td>
											<?php echo $row->comments; ?>
									</td>
									<td>
									<?php 
										$date_submitted = new DateTime($row->date_submitted);
										echo $date_submitted->format('m/d/Y');
									?>
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

}
 ?>


