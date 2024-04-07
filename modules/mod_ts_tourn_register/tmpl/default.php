<?php // no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );
$active_tourn = mod_ts_tourn_register::CheckActiveTournaments();
    
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


input, select, textarea{
    border: solid 1px #D3D3D3;	
	padding: 5px;
	border-radius: 2px;
	font-weight: normal;
}

.next{
	padding: 5px 15px;
	border-radius: 2px;
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

.ohiobaseball_form_container td{
	padding: 10px 0;
}
</style>
<!-- START REGISTRATION FORM -->
<script type="text/javascript" language="javascript">

var submitted = false;

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
        //this.defaultShowErrors();
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

<div class="ohiobaseball_form_container">
<?php echo $err_msg; ?>
<form action="<?php echo $post_base_url; ?>" method="post" id="register_form_form">
 
 
 
 
 
 <table width="90%" border="0" cellpadding="0" cellspacing="0"  class="contact_form">

     <tr class="left_col">
      <td colspan="2" valign="middle"><h3>Tournament Registration</h3></td>
    </tr>
    <tr class="left_col">
      <td colspan="2" style="padding: 15px 0 15px 0;"> 
		<p>Registration Forms can be processed immediately by paying with a debit or credit card through the Pay Pal Option on the next page - you do not need a Pay Pal Account to pay with a credit or debit card.</p>
		<p>You can also pay by check - print a copy of the registration on the next page and mail the registration and check to the address at the bottom of the registration form.  Your Entry will be confirmed when the check is received.</p>
		<p>Any registrations within 2 weeks of the tournament will only be accepted if paid by credit or debit card - if unable to pay with credit or debit call the Tournament Director to make arrangements. </p>
	  </td>
    </tr>

<tr class="left_col">
    <td><label for="team_name">Team Name:</label></td>
	<td><input  id="team_name" maxlength="40" name="team_name" class="invalid" size="35" type="text" /></td>
	
</tr>

<tr class="left_col">

	<td valign="top" colspan="2" style="padding-top: 15px;"><label for="tournaments_desired">Tournaments Desired:</label></td>
</tr>
 <tr>
     <td colspan="2"><strong>1.</strong>

    <label for="tournament_id_1">Tournament:</label>
    <select name="tournament_id_1" id="tournament_id_1" class="tournament invalid">
      <?php
        echo $tourn_vals;
      ?>
    </select>
    <div style="clear:both;padding-bottom: 3px;"></div>
    <label for="age_id_1">Age:</label>
    <select name="age_id_1" id="age_id_1" class="age_id invalid">
    </select>
    <div style="border-bottom:solid 1px #ccc;padding-bottom: 3px;"></div>
  </td>
</tr>
<tr class="left_col">

<td colspan="2"><strong>2.</strong>
    <label for="tournament_id_2">Tournament:</label>
    <select name="tournament_id_2" id="tournament_id_2" class="tournament">
          <?php
        echo $tourn_vals;
      ?>
    </select>
    <div style="clear:both;padding-bottom: 3px;"></div>
    <label for="age_id_2">Age:</label>
    <select name="age_id_2" id="age_id_2" class="age_id">
    </select>
    <div style="border-bottom:solid 1px #ccc;padding-bottom: 3px;"></div>
 </td>
</tr>

<tr class="left_col">
<td colspan="2"><strong>3.</strong>
    <label for="tournament_id_3">Tournament:</label>
    <select name="tournament_id_3" id="tournament_id_3" class="tournament">
          <?php
        echo $tourn_vals;
      ?>
    </select>
    <div style="clear:both;padding-bottom: 3px;"></div>
    <label for="age_id_3">Age:</label>
    <select name="age_id_3" id="age_id_3" class="age_id">
    </select>
    <div style="border-bottom:solid 1px #ccc;padding-bottom: 3px;"></div>
  </td>
</tr>
<tr class="left_col">

<td colspan="2"><strong>4.</strong>
    <label for="tournament_id_4">Tournament:</label>
    <select name="tournament_id_4" id="tournament_id_4" class="tournament">
          <?php
        echo $tourn_vals;
      ?>
    </select>
    <div style="clear:both;padding-bottom: 3px;"></div>
    <label for="age_id_4">Age:</label>
    <select name="age_id_4" id="age_id_4" class="age_id">
    </select>
    <div style="border-bottom:solid 1px #ccc;padding-bottom: 3px;"></div>
  </td>
</tr>
<tr class="left_col">

<td colspan="2"><strong>5.</strong>
    <label for="tournament_id_5">Tournament:</label>
    <select name="tournament_id_5" id="tournament_id_5" class="tournament">
      <?php
        echo $tourn_vals;
      ?>
    </select>
    <div style="clear:both;padding-bottom: 3px;"></div>
    <label for="age_id_5">Age:</label>
    <select name="age_id_5" id="age_id_5" class="age_id">
    </select>
    <div style="border-bottom:solid 1px #ccc;padding-bottom: 3px;"></div>
 </td>
</tr>

<tr class="left_col">
  <td valign="top"><p>Please qualify your team's
   level of play:</p></td>
  <td><label>
    <select name="level_play" id="level_play">
      <option>A</option>
      <option>B</option>
      <option>C</option>
	  <option>Rec</option>
    </select>
  </label></td>
</tr>
<tr class="left_col">
  <td  style="padding-top: 15px;"><h4>Team Information:</h4></td>
  <td>&nbsp;</td>
</tr>
<tr class="left_col">
  <td  style="padding-top: 10px;">Manager/Team Contact 1:</td>
  <td><label>
    <input name="team_manager_1" type="text" id="team_manager_1" class="invalid" size="35">
  </label></td>
</tr>
<tr class="left_col">
  <td>Manager/Team Contact 2:</td>
  <td><label>
    <input name="team_manager_2" type="text" id="team_manager_2" size="35">
  </label></td>
</tr>
<tr class="left_col">
  <td>Address:</td>
  <td><label>
    <input name="team_address" type="text" class="invalid" id="team_address" size="35">
  </label></td>
</tr>
<tr class="left_col">
  <td>City</td>
  <td><label>
    <input type="text" name="team_city" class="invalid" id="team_city">
  </label></td>
</tr>
<tr class="left_col">
  <td>State:</td>
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
  <td>Zip Code:</td>
  <td><label>
    <input name="team_zip" type="text" id="team_zip" class="invalid" size="15">
  </label></td>
</tr>
<tr class="left_col">
  <td>Home Phone:</td>
  <td><label>
    <input type="text" name="home_phone" id="home_phone">
  </label></td>
</tr>
<tr class="left_col">
  <td>Cell Phone 1:</td>
  <td><label>
    <input type="text" name="cell_phone_1" class="invalid" id="cell_phone_1">
  </label></td>
</tr>


<tr class="left_col">
  <td>Cell Phone 2:</td>
  <td><label>
    <input type="text" name="cell_phone_2" id="cell_phone_2">
  </label></td>
</tr>
<tr class="left_col">
  <td>Email 1:</td>
  <td><label>
    <input name="email_1" type="text" class="invalid" id="email_1" size="35" />
  </label></td>
</tr>

<tr class="left_col">
  <td>Email 2:</td>
  <td><label>
    <input name="email_2" type="text" id="email_2" size="35">
  </label></td>
</tr>
<tr class="left_col">
  <td>Comments:</td>
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
  <td><input type="submit" class="next " name="Next" id="Next" value="Next"></td>
</tr>
 </table>
<input type="text" name="salutation" value="" class="salutation" style="display:none;" />
<div id="more_inputs"></div>
</form>
<script type="text/javascript" language="javascript">

jQuery(document).ready(function(){
	
	jQuery("select.tournament").change(function(){
		
		var select_id = jQuery(this).attr('id');
		var tournament_id = jQuery(this).val();
		
		jQuery.getJSON( "/modules/mod_ts_tourn_register/process/get_tournament_ages.php?tournament_id=" + tournament_id + "&ajax=true", function( data ) {
			
			var select_nums = select_id.split('_');
			var select_num = select_nums[2];
			var options = '';
		
			if(select_num != null)
			{
				jQuery('select#age_id_' + select_num).find('option').remove();
				jQuery.each( data, function( key, val ) {
					options += '<option value="' + val.optionValue + '">' + val.optionDisplay + '</option>';
				});
				jQuery("select#age_id_" + select_num).html(options);
			}			

		  })
	  });

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
jQuery(document).ready(function(){

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
<div class="contact_form">

<h2 style="margin-bottom: 15px">Registration Confirmation</h2>
<p>
<strong>Team Name:</strong> <?php echo $team_name; ?>
</p>
<p>
<strong>Tournaments Desired:</strong> <?php
    echo $arr_tourn [0];
    ?>
</p>

<p>


<strong>Total Due:</strong> $<?php echo $arr_tourn [1]; ?>

</p>


<p>
<strong>Please qualify your team's level of play:</strong> <?php
    echo $level_play;
    ?>
</p>
<p>
<strong>Team Information:</strong>
</p>
<p>
<strong>Manager/Team Contact 1:</strong> <?php
    echo $team_manager_1;
    ?>
</p>
<p>
<strong>Manager/Team Contact 2:</strong> <?php
    echo $team_manager_2;
    ?>
</p>
<p>
 <strong>Address:</strong> <?php
    echo $team_address;
    ?>
</p>
<p>
<strong>City</strong> <?php
    echo $team_city;
    ?>
</p>
<p>
 <strong>State:</strong> <?php
    echo $team_state;
    ?>
</p>
<p>
<strong>Zip Code:</strong> <?php
    echo $team_zip;
    ?>
</p>
<p>
 <strong> Home Phone:</strong> <?php
    echo $home_phone;
    ?>
</p>
<p>
<strong> Cell Phone 1:</strong> <?php

    echo $cell_phone_1;
    ?>
</p>


<p>
  <strong> Cell Phone 2:</strong> <?php
    echo $cell_phone_2;
    ?>
</p>
<p>
  <strong> Email 1:</strong> <?php
    echo $email_1;
    ?>
</p>

<p>
 <strong> Email 2:</strong> <?php
    echo $email_2;
    ?>
</p>
<p>
<strong> Comments:</strong> <?php
    echo $comments;
    ?>
</p>
<p>
Pay online via Paypal, or mail a printed copy of this form along with a check payable to Mark Hoisington c/o 5 Star Fastpitch to the following address:</p>
    <p style="text-align: center">5 Star Fastpitch<br/>9754 Emerald Bluff Circle NW<br/>Canal Fulton, OH  44614</p>Please note registration forms received without payment will not be processed.
</p>

</div>
<div class="tourn_menu">
<ul>

<li><input type="submit" name="Print" id="print_reg" value="Print & Pay By Check" class="tp-button home-slider-button-blue"/></li>
<li><input type="button" class="tp-button home-slider-button" name="paypal" id="paypal" value="Pay by Credit Card" /></li>
</ul>

<input type="hidden" name="print_text" id="print_text"/>
</div>
</form>
<!-- END PAYMENT FORM -->
<?php } else if($mode=="thank_you") { ?>
<!-- START THANK YOU -->
<h4>Thank you for registering with 5 Star Fastpitch.  Please check the website as your tournament date approaches for additional details.</h4>
<!-- END THANK YOU -->
<?php } ?>


