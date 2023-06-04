<!DOCTYPE html>
<?php
	Ini_Set( 'display_errors', true );
	include("functions.php");
	$weatherdata_json = getWeatherData();
	makeNewWeatherSidebar($weatherdata_json);
		//plexMovieStats();
	
?>
