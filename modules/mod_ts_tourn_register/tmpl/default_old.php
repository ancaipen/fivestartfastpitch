<?php // no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );
$active_tourn = mod_ts_tourn_register::CheckActiveTournaments();

error_reporting(E_ALL);
ini_set('display_errors', '1');

?>

<?php if($mode=="register") { ?>
<?php if($active_tourn==true) { ?>
<style type="text/css">
/* message styles */

.message_error
{
    background-image:url('../images/warning.png');
    background-repeat:no-repeat;
    background-position: 5px center;
    padding: 5px 5px 5px 25px;
    border: solid 1px #ff0000;
    background-color:#FFCCCC;
    font-weight:bold;
    font-size: 14px;
    margin: 0 0 3px 0;
    clear:both;
}
.message_success
{
    background-image:url('../images/accept.png');
    background-repeat:no-repeat;
    background-position: 5px center;
    padding: 5px 5px 5px 25px;
    border: solid 1px #00CC33;
    background-color:#CCFFCC;
    font-weight:bold;
    font-size: 14px;
    margin: 0 0 3px 0;
    clear:both;
}
.salutation
{
    display: none;
}
.help_text
{
    font-size: 11px;
    font-style: italic;
    color: #999966;
}
.left_col
{
    width: 200px;
}
input.invalid, select.invalid
{
    border: solid 1px #ff0000;
}
.error_summary ul
{
    list-style: none;
    padding: 0;
    margin: 0;
}
.error_summary li
{
    padding: 5px;
    margin-top: 3px;
    border: solid 1px #ff0000;
    background-color: #FFE6E6;
}
</style>
<!-- START REGISTRATION FORM -->
<script type="text/javascript" language="javascript">
 //jquery and jquery validate plugin (include)
jQuery(document).ready(function(){

setTimeout("writeInputs()", 6000);

jQuery("#register_form_form").validate({

	rules: {
		team_name: { required: true },
		team_manager_1:{ required: true },
		team_address: { required: true },
		team_city: { required: true},
		team_zip: { required: true},
		cell_phone_1: { required: true },
		email_1: { required: true, email:true }
	},
	messages: {
		team_name: {required: " <span class='error_form' style='color: #bc2c2c;'>* Team name is required</span>"},
		team_manager_1: {required: " <span class='error_form' style='color: #bc2c2c;'>* Team Manager is required</span>"},
		team_address: {required: " <span class='error_form' style='color: #bc2c2c;'>* Team address is required</span>"},
		team_city: {required: " <span class='error_form' style='color: #bc2c2c;'>* Team city is required</span>"},
		email_1: {required: " <span class='error_form' style='color: #bc2c2c;'>* Team email is required</span>", email: " <span class='error_form'>* Please enter a valid email address</span>"},
		team_zip: {required: " <span class='error_form' style='color: #bc2c2c;'>* Team zipcode is required</span>"},
		cell_phone_1: {required: " <span class='error_form' style='color: #bc2c2c;'>* Team cell phone is required</span>"}
	},
        errorClass: "invalid",
        showErrors: function(errorMap, errorList) {
            if(submitted)
            {
                var summary = "<h3>You have the following errors:</h3><ul class='error_list'>";
                jQuery.each(errorList, function() 
                { 
                    summary += "<li>" + this.message + "</li>"; 
                });
                jQuery('.error_summary').html(summary + '</ul>');
                this.defaultShowErrors();
                submitted = false;
            }
        },
        invalidHandler: function(form, validator) {
            submitted = true;
        }

});

jQuery("form").submit(function (e) {
    var allowSubmission = validateTournSelection();
    if(allowSubmission == false)
    { 
        e.preventDefault();
        var summary = "<h3>You have the following errors:</h3><ul class='error_list'>";
        summary += "<li>Please select a tournament/age to continue registration.</li>"; 
        jQuery('.error_summary').html(summary + '</ul>');
        this.defaultShowErrors();
    }
});

});

function validateTournSelection()
{
    var allowProcess = false;
    
    var tournament_id_1 = jQuery("#tournament_id_1").val();
    var tournament_id_2 = jQuery("#tournament_id_2").val();
    var tournament_id_3 = jQuery("#tournament_id_3").val();
    var tournament_id_4 = jQuery("#tournament_id_4").val();
    var tournament_id_5 = jQuery("#tournament_id_5").val();
    var age_id_1 = jQuery("#age_id_1").val();
    var age_id_2 = jQuery("#age_id_2").val();
    var age_id_3 = jQuery("#age_id_3").val();
    var age_id_4 = jQuery("#age_id_4").val();
    var age_id_5 = jQuery("#age_id_5").val();
    
    if(tournament_id_1 != "-1")
    {
        allowProcess = true;
    }

    if(tournament_id_2 != "-1")
    {
        allowProcess = true;
    }

    if(tournament_id_3 != "-1")
    {
        allowProcess = true;
    }

    if(tournament_id_4 != "-1")
    {
        allowProcess = true;
    }

    if(tournament_id_5 != "-1")
    {
        allowProcess = true;
    }

    //make sure age has been selected
    if(age_id_1 != "-1")
    {
        allowProcess = true;
    }

    if(age_id_2 != "-1")
    {
        allowProcess = true;
    }

    if(age_id_3 != "-1")
    {
        allowProcess = true;
    }

    if(age_id_4 != "-1")
    {
        allowProcess = true;
    }

    if(age_id_5 != "-1")
    {
        allowProcess = true;
    }
    
    return allowProcess;
    
}

function writeInputs()
{
    jQuery("#more_inputs").html('<input type="hidden" name="the_date" value="10" id="the_date" />');
}

</script>

<div class="ohiobaseball_form_container" style="margin-left: 20px; font-family:Arial, Helvetica, sans-serif;">
<?php echo $err_msg; ?>
<form action="<?php echo $post_base_url; ?>" method="post" id="register_form_form">
 <table width="525px" border="0" cellpadding="5" cellspacing="0" style="padding-top:10px; font-size: 12px;" class="contact_form">

     <tr class="left_col">
      <td colspan="2" valign="middle" style="padding: 15px 0 15px 0; background-image: url(modules/mod_ts_tourn_register/images/logo_thumbnail.jpg); background-position:left; background-repeat: no-repeat;"><h1 style="color: #11326d; margin: 0 0 0 105px;">Tournament Registration</h1></td>
    </tr>
    <tr class="left_col">
      <td colspan="2" style="padding: 15px 0 15px 0;"> Registration deadline is 2 weeks prior to tournament start date and schedules will be set 1 week prior to start date.  Only standby registration will be accepted after the 2 week deadline.  There will be no refunds after the 2 week deadline unless the tournament in which you are entered is cancelled due to weather or some other unforeseen circumstance.  Please be aware that tournaments fill up very quickly and many have sold out prior to March 1.  <strong>Registration Forms received without entry fee WILL NOT BE PROCESSSED.</strong> Scheduling requests will only be considered if included with this registration.</td>
    </tr>

<tr class="left_col">
    <td width="45%;"><label for="team_name"><strong>Team Name:</strong></label></td><td><input  id="team_name" maxlength="40" name="team_name" class="invalid" size="35" type="text" /></td>
</tr>
<tr class="left_col">
<td valign="top" colspan="2" style="padding-top: 15px;"><label for="tournaments_desired"><strong>Tournaments Desired:</strong></label></td>
</tr>
 <tr>
     <td colspan="2"><strong>1.</strong>

    <label for="tournament_id_1">Tournament:</label>
    <select name="tournament_id_1" id="tournament_id_1" class="invalid">
      <?php
        echo $tourn_vals;
      ?>
    </select>
    <div style="clear:both;padding-bottom: 3px;"></div>
    <label for="age_id_1">Age:</label>
    <select name="age_id_1" id="age_id_1" class="invalid">
      <?php
        echo $age_vals;
      ?>
    </select>
    <div style="border-bottom:solid 1px #ccc;padding-bottom: 3px;"></div>
  </td>
</tr>
<tr class="left_col">

<td colspan="2"><strong>2.</strong>
    <label for="tournament_id_2">Tournament:</label>
    <select name="tournament_id_2" id="tournament_id_2">
          <?php
        echo $tourn_vals;
      ?>
    </select>
    <div style="clear:both;padding-bottom: 3px;"></div>
    <label for="age_id_2">Age:</label>
    <select name="age_id_2" id="age_id_2">
      <?php
        echo $age_vals;
      ?>
    </select>
    <div style="border-bottom:solid 1px #ccc;padding-bottom: 3px;"></div>
 </td>
</tr>

<tr class="left_col">
<td colspan="2"><strong>3.</strong>
    <label for="tournament_id_3">Tournament:</label>
    <select name="tournament_id_3" id="tournament_id_3">
          <?php
        echo $tourn_vals;
      ?>
    </select>
    <div style="clear:both;padding-bottom: 3px;"></div>
    <label for="age_id_3">Age:</label>
    <select name="age_id_3" id="age_id_3">
      <?php
        echo $age_vals;
      ?>
    </select>
    <div style="border-bottom:solid 1px #ccc;padding-bottom: 3px;"></div>
  </td>
</tr>
<tr class="left_col">

<td colspan="2"><strong>4.</strong>
    <label for="tournament_id_4">Tournament:</label>
    <select name="tournament_id_4" id="tournament_id_4">
          <?php
        echo $tourn_vals;
      ?>
    </select>
    <div style="clear:both;padding-bottom: 3px;"></div>
    <label for="age_id_4">Age:</label>
    <select name="age_id_4" id="age_id_4">
      <?php
        echo $age_vals;
      ?>
    </select>
    <div style="border-bottom:solid 1px #ccc;padding-bottom: 3px;"></div>
  </td>
</tr>
<tr class="left_col">

<td colspan="2"><strong>5.</strong>
    <label for="tournament_id_5">Tournament:</label>
    <select name="tournament_id_5" id="tournament_id_5">
      <?php
        echo $tourn_vals;
      ?>
    </select>
    <div style="clear:both;padding-bottom: 3px;"></div>
    <label for="age_id_5">Age:</label>
    <select name="age_id_5" id="age_id_5">
      <?php
        echo $age_vals;
      ?>
    </select>
    <div style="border-bottom:solid 1px #ccc;padding-bottom: 3px;"></div>
 </td>
</tr>

<tr class="left_col">
  <td valign="top"><p><strong>Please qualify your team's
  </strong><strong> level of play:</strong></p></td>
  <td><label>
    <select name="level_play" id="level_play">
      <option>Open roster &ndash; Major Level</option>
      <option>60% or more from same School District -  Mid - Level</option>
      <option>Community - 100% of roster from same school district</option>
    </select>
  </label></td>
</tr>
<tr class="left_col">
  <td valign="top"><strong>Current League Affiliation:</strong></td>
  <td><label>
    <input type="text" name="league_affiliation" id="league_affiliation">
    </label></td>
</tr>
<tr class="left_col">
  <td  style="padding-top: 15px;"><strong>Team Information:</strong></td>
  <td>&nbsp;</td>
</tr>
<tr class="left_col">
  <td  style="padding-top: 10px;"><strong>Manager/Team Contact 1:</strong></td>
  <td><label>
    <input name="team_manager_1" type="text" id="team_manager_1" class="invalid" size="35">
  </label></td>
</tr>
<tr class="left_col">
  <td><strong>Manager/Team Contact 2:</strong></td>
  <td><label>
    <input name="team_manager_2" type="text" id="team_manager_2" size="35">
  </label></td>
</tr>
<tr class="left_col">
  <td><strong>Address:</strong></td>
  <td><label>
    <input name="team_address" type="text" class="invalid" id="team_address" size="35">
  </label></td>
</tr>
<tr class="left_col">
  <td><strong>City</strong></td>
  <td><label>
    <input type="text" name="team_city" class="invalid" id="team_city">
  </label></td>
</tr>
<tr class="left_col">
  <td><strong>State:</strong></td>
  <td><label>
    <select name="team_state" id="team_state" class="invalid">
    <option value="-1">- Select State or Province -</option>
    <option value="AK">AK</option>
	<option value="AL">AL</option>
	<option value="AR">AR</option>
	<option value="AZ">AZ</option>
	<option value="CA">CA</option>
	<option value="CO">CO</option>
	<option value="CT">CT</option>
	<option value="DC">DC</option>
	<option value="DE">DE</option>
	<option value="FL">FL</option>
	<option value="GA">GA</option>
	<option value="HI">HI</option>
	<option value="IA">IA</option>
	<option value="ID">ID</option>
	<option value="IL">IL</option>
	<option value="IN">IN</option>
	<option value="KS">KS</option>
	<option value="KY">KY</option>
	<option value="LA">LA</option>
	<option value="MA">MA</option>
	<option value="MD">MD</option>
	<option value="ME">ME</option>
	<option value="MI">MI</option>
	<option value="MN">MN</option>
	<option value="MO">MO</option>
	<option value="MS">MS</option>
	<option value="MT">MT</option>
	<option value="NC">NC</option>
	<option value="ND">ND</option>
	<option value="NE">NE</option>
	<option value="NH">NH</option>
	<option value="NJ">NJ</option>
	<option value="NM">NM</option>
	<option value="NV">NV</option>
	<option value="NY">NY</option>
	<option value="OH">OH</option>
	<option value="OK">OK</option>
	<option value="OR">OR</option>
	<option value="PA">PA</option>
	<option value="RI">RI</option>
	<option value="SC">SC</option>
	<option value="SD">SD</option>
	<option value="TN">TN</option>
	<option value="TX">TX</option>
	<option value="UT">UT</option>
	<option value="VA">VA</option>
	<option value="VT">VT</option>
	<option value="WA">WA</option>
	<option value="WI">WI</option>
	<option value="WV">WV</option>
	<option value="WY">WY</option>
    <option value="AB">AB</option>
	<option value="BC">BC</option>
	<option value="MB">MB</option>
	<option value="NB">NB</option>
	<option value="NF">NF</option>
	<option value="NT">NT</option>
	<option value="NS">NS</option>
	<option value="NU">NU</option>
	<option value="ON">ON</option>
	<option value="PE">PE</option>
	<option value="QC">QC</option>
	<option value="SK">SK</option>
	<option value="YT">YT</option>
    </select>
  </label></td>
</tr>
<tr class="left_col">
  <td><strong>Zip Code:</strong></td>
  <td><label>
    <input name="team_zip" type="text" id="team_zip" class="invalid" size="15">
  </label></td>
</tr>
<tr class="left_col">
  <td><strong> Home Phone:</strong></td>
  <td><label>
    <input type="text" name="home_phone" id="home_phone">
  </label></td>
</tr>
<tr class="left_col">
  <td><strong> Cell Phone 1:</strong></td>
  <td><label>
    <input type="text" name="cell_phone_1" class="invalid" id="cell_phone_1">
  </label></td>
</tr>


<tr class="left_col">
  <td><strong> Cell Phone 2:</strong></td>
  <td><label>
    <input type="text" name="cell_phone_2" id="cell_phone_2">
  </label></td>
</tr>
<tr class="left_col">
  <td><strong> Email 1:</strong></td>
  <td><label>
    <input name="email_1" type="text" class="invalid" id="email_1" size="35" />
  </label></td>
</tr>

<tr class="left_col">
  <td><strong> Email 2:</strong></td>
  <td><label>
    <input name="email_2" type="text" id="email_2" size="35">
  </label></td>
</tr>
<tr class="left_col">
  <td><strong> Comments:</strong></td>
  <td><label>
    <textarea name="comments" id="comments" cols="30" rows="5"></textarea>
  </label></td>
</tr>
<tr class="left_col">
    <td colspan="2">
        <div class="error_summary"></div>
    </td>
</tr>
<tr class="left_col">
  
<td colspan="2" valign="top" style="padding: 10px 0 10px 5px;">Click Next to confirm your registration and view payment options. </td>
</tr>
<tr class="left_col">
  <td>&nbsp;</td>
  <td><input type="submit" name="Next" id="Next" value="Next"></td>
</tr>
 </table>
<input type="text" name="salutation" value="" class="salutation" />
<div id="more_inputs"></div>
</form>
<script type="text/javascript" charset="utf-8">
$(function(){
  $("select#tournament_id_1").change(function(){
    $.getJSON("modules/mod_ts_tourn_register/process/get_tournament_ages.php",{tournament_id_1: $(this).val(), ajax: 'true'}, function(j){
      var options = '';
      for (var i = 0; i < j.length; i++) {
        options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
      }
      $("select#age_id_1").html(options);
    })
  })
})
$(function(){
  $("select#tournament_id_2").change(function(){
    $.getJSON("modules/mod_ts_tourn_register/process/get_tournament_ages.php",{tournament_id_2: $(this).val(), ajax: 'true'}, function(j){
      var options = '';
      for (var i = 0; i < j.length; i++) {
        options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
      }
      $("select#age_id_2").html(options);
    })
  })
})
$(function(){
  $("select#tournament_id_3").change(function(){
    $.getJSON("modules/mod_ts_tourn_register/process/get_tournament_ages.php",{tournament_id_3: $(this).val(), ajax: 'true'}, function(j){
      var options = '';
      for (var i = 0; i < j.length; i++) {
        options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
      }
      $("select#age_id_3").html(options);
    })
  })
})
$(function(){
  $("select#tournament_id_4").change(function(){
    $.getJSON("modules/mod_ts_tourn_register/process/get_tournament_ages.php",{tournament_id_4: $(this).val(), ajax: 'true'}, function(j){
      var options = '';
      for (var i = 0; i < j.length; i++) {
        options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
      }
      $("select#age_id_4").html(options);
    })
  })
})
$(function(){
  $("select#tournament_id_5").change(function(){
    $.getJSON("modules/mod_ts_tourn_register/process/get_tournament_ages.php",{tournament_id_5: $(this).val(), ajax: 'true'}, function(j){
      var options = '';
      for (var i = 0; i < j.length; i++) {
        options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
      }
      $("select#age_id_5").html(options);
    })
  })
})
</script>
</div>
<?php } else { ?>
<h1>No active tournaments are accepting registrations.  Please check back soon.</h1>
<?php } ?>
<!-- END REGISTRATION FORM -->
<?php } else if($mode=="payment") { ?>
<script type="text/javascript" language="javascript">
 //jquery and jquery validate plugin (include)
$(document).ready(function(){

        //on page load
        var html = document.getElementById('print_results').innerHTML;
        jQuery('#print_text').val(html);

        jQuery('input#paypal').click(function() {
            var url = "<?php echo str_replace('"','',$paypal_url); ?>";
            window.location.replace(url);
        });

})

</script>
<!-- START PAYMENT FORM -->
<form action="ts_register/print.php" method="post" id="payment_form" target="_blank">
<div id="print_results">
<table width="525px" border="0" cellpadding="5" cellspacing="0" style="padding-top:10px; font-size: 12px;" class="contact_form">

     <tr class="left_col">
      <td colspan="2" valign="middle" style="padding: 15px 0 15px 0; background-image: url(modules/mod_ts_tourn_register/images/logo_thumbnail.jpg); background-position:left; background-repeat: no-repeat;"><h1 style="color: #11326d; margin: 0 0 0 85px;">Registration Confirmation</h1></td>
    </tr>

<tr class="left_col">
    <td width="45%;"><label for="team_name"><strong>Team Name:</strong></label></td><td><?php echo $team_name; ?></td>
</tr>
<tr class="left_col">
<td valign="top" colspan="2" style="padding-top: 15px;"><label for="tournaments_desired"><strong>Tournaments Desired:</strong></label></td>
</tr>

<tr><td colspan ="2">
    <?php
    echo $arr_tourn [0];
    ?>

    <div style="font-weight: bold; font-size: 14px;">Total Due: $ <?php echo $arr_tourn [1]; ?></div>

    </td></tr>


<tr class="left_col">
  <td valign="top"><p><strong>Please qualify your team's
  </strong><strong> level of play:</strong></p></td>
  <td><?php
    echo $level_play;
    ?></td>
</tr>
<tr class="left_col">
  <td valign="top"><strong>Current League Affiliation:</strong></td>
  <td><?php
    echo $league_affiliation;
    ?></td>
</tr>
<tr class="left_col">
  <td  style="padding-top: 15px;"><strong>Team Information:</strong></td>
  <td>&nbsp;</td>
</tr>
<tr class="left_col">
  <td  style="padding-top: 10px;"><strong>Manager/Team Contact 1:</strong></td>
  <td><?php
    echo $team_manager_1;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong>Manager/Team Contact 2:</strong></td>
  <td><?php
    echo $team_manager_2;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong>Address:</strong></td>
  <td><?php
    echo $team_address;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong>City</strong></td>
  <td><?php
    echo $team_city;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong>State:</strong></td>
  <td><?php
    echo $team_state;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong>Zip Code:</strong></td>
  <td><?php
    echo $team_zip;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong> Home Phone:</strong></td>
  <td><?php
    echo $home_phone;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong> Cell Phone 1:</strong></td>
  <td><?php

    echo $cell_phone_1;
    ?></td>
</tr>


<tr class="left_col">
  <td><strong> Cell Phone 2:</strong></td>
  <td><?php
    echo $cell_phone_2;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong> Email 1:</strong></td>
  <td><?php
    echo $email_1;
    ?></td>
</tr>

<tr class="left_col">
  <td><strong> Email 2:</strong></td>
  <td><?php
    echo $email_2;
    ?></td>
</tr>
<tr class="left_col">
  <td><strong> Comments:</strong></td>
  <td><?php
    echo $comments;
    ?></td>
</tr>
<tr class="left_col">
<td colspan="2" valign="top" style="padding: 10px 0 10px 5px;">Pay online via Paypal, or mail a printed copy of this form along with a check payable to Mark Hoisington c/o Team Sports Tournaments to the following address:
    <p style="text-align: center">Team Sports Tournaments<br/>454 Keyser Parkway<br/>Cuyahoga Falls, OH  44223</p>Please note registration forms received without payment will not be processed.</td>
</tr>
 </table>
</div>
<table width="525px" border="0" cellpadding="5" cellspacing="0" style="padding-top:10px; font-size: 12px;" class="contact_form">
<tr class="left_col">
<td></td>
<td><input type="submit" name="Print" id="print_reg" value="Print" />&nbsp;&nbsp;&nbsp;<input type="button" name="paypal" id="paypal" value="Pay via Paypal" /></td>
</tr>
</table>

<input type="hidden" name="print_text" id="print_text"/>
</form>
<!-- END PAYMENT FORM -->
<?php } else if($mode=="thank_you") { ?>
<!-- START THANK YOU -->
<div style="font-size: 16px; padding: 15px 20px 0 15px; min-height: 400px;">Thank you for registering with Team Sports Tournaments.  Please check the website as your tournament date approaches for additional details.</div>
<!-- END THANK YOU -->
<?php } ?>


