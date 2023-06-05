<!DOCTYPE html>
<?php
	Ini_Set( 'display_errors', true );
	include("functions.php");
?>
<html lang="en">
	<script>
		// Enable bootstrap tooltips
		$(function ()
		        { $("[rel=tooltip]").tooltip();
		        });
	</script>
<?php
//$weatherdata_json = getWeatherData();
//echo uvindex($weatherdata_json);
echo uvindex();
?>
