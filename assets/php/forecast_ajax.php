<!DOCTYPE html>
<?php
	Ini_Set( 'display_errors', true );
	include("functions.php");
		$weatherdata_json = getWeatherData();
		makeWeatherForecast($weatherdata_json);

?>

<html lang="en">
	<script>
	// Enable bootstrap tooltips
	$(function ()
	        { $("[rel=tooltip]").tooltip();
	        });
	</script>







