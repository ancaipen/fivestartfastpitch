<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$season_name = mod_ts_tourn_summary::GetCurrentSeasonName();
?>

<div class="schedule">


    <div class="row schedule-header display-flex">
	
	<div class="col-xs-6">
	<h5>TOURNAMENT</h5>
	</div>
	
	<div class="col-xs-3">
	<h5>DATES</h5>
	</div>
	
	
		<div class="col-xs-3">
	<h5 style="text-align: center">SCHEDULE/RESULTS</h5>
	</div>	
	</div>

    <?php

    echo $html;

    ?>
</div>


 <div class="schedule_footer"></div>