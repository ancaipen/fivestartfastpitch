<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Anand
 * @author     Super User <dev@component-creator.com>
 * @copyright  2023 Super User
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

$app = Factory::getApplication();
$model = $this->getModel();
$data = $model->GetTournamentRegInfo($this->item->id);

?>
<form action="../ts_register/print.php" method="post" id="payment_form" target="_blank">
<div class="item_fields" id="print_results">

	<table class="table">
	<tr class="left_col">
		<td width="45%;" colspan="2" style="font-size: 18px; font-weight: bold;"><?php echo $this->item->team_name; ?></td>
	</tr>
	<tr class="left_col">
		<td valign="top" colspan="2" style="padding-top: 15px;">
		<label for="tournaments_desired"><strong>Tournaments Desired:</strong></label></td>
	</tr>
	<tr>
		<td colspan ="2"> <?php  echo $data[0];?>
    		<div style="font-weight: bold; font-size: 14px;">Total Due: $ <?php echo $data[1]; ?></div>
	    </td>
	</tr>

	<tr class="left_col">
		<td valign="top"><p><strong>Please qualify your team's
		</strong><strong> level of play:</strong></p></td>
		<td><?php echo $this->item->level_play;?></td>
	</tr>

		<tr class="left_col">
		<td valign="top"><p><strong>Current League Affiliation:</strong></p></td>
		<td><?php
			echo $this->item->league_affiliation;
			?></td>
		</tr>
		<tr class="left_col">
		<td  style="padding-top: 15px;"><strong>Team Information:</strong></td>
		<td>&nbsp;</td>
		</tr>
		<tr class="left_col">
		<td  style="padding-top: 10px;"><strong>Manager/Team Contact 1:</strong></td>
		<td><?php
			echo $this->item->team_manager_1;
			?></td>
		</tr>
		<tr class="left_col">
		<td><strong>Manager/Team Contact 2:</strong></td>
		<td><?php
			echo $this->item->team_manager_2;
			?></td>
		</tr>
		<tr class="left_col">
		<td><strong>Address:</strong></td>
		<td><?php
			echo $this->item->team_address;
			?></td>
		</tr>
		<tr class="left_col">
		<td><strong>City</strong></td>
		<td><?php
			echo $this->item->team_city;
			?></td>
		</tr>
		<tr class="left_col">
		<td><strong>State:</strong></td>
		<td><?php
			echo $this->item->team_state;
			?></td>
		</tr>
		<tr class="left_col">
		<td><strong>Zip Code:</strong></td>
		<td><?php
			echo $this->item->team_zip;
			?></td>
		</tr>
		<tr class="left_col">
		<td><strong> Home Phone:</strong></td>
		<td><?php
			echo $this->item->home_phone;
			?></td>
		</tr>
		<tr class="left_col">
		<td><strong> Cell Phone 1:</strong></td>
		<td><?php

			echo $this->item->cell_phone_1;
			?></td>
		</tr>


		<tr class="left_col">
		<td><strong> Cell Phone 2:</strong></td>
		<td><?php
			echo $this->item->cell_phone_2;
			?></td>
		</tr>
		<tr class="left_col">
		<td><strong> Email 1:</strong></td>
		<td><?php
			echo $this->item->email_1;
			?></td>
		</tr>

		<tr class="left_col">
		<td><strong> Email 2:</strong></td>
		<td><?php
			echo $this->item->email_2;
			?></td>
		</tr>
		<tr class="left_col">
		<td><strong> Comments:</strong></td>
		<td><?php
			echo $this->item->comments;
			?></td>
		</tr>


	</table>
	<input type="submit" name="Print" id="print_reg" value="Print" onClick="load_print_text();"/>
	<input type="hidden" name="print_text" id="print_text"/>
</div>
</form>

<script type="text/javascript" language="javascript">

//on page load
function load_print_text()
{
    var html = document.getElementById('print_results').innerHTML;
    document.getElementById('print_text').value = html;
}

</script>