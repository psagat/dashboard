<!DOCTYPE html>
<?php
	Ini_Set( 'display_errors', true );
	include("functions.php");
	include("service.class.php");
	include("serviceSAB.class.php");
?>
<html lang="en">
	<script>
	// Enable bootstrap tooltips
	$(function ()
	        { $("[rel=tooltip]").tooltip();
	        });
	</script>
<?php 
$sabnzbdXML = simplexml_load_file('http://darpa:8080/api?mode=queue&output=xml&apikey='.$sabnzbd_api);

if (($sabnzbdXML->status) == 'Downloading'):
	$timeleft = $sabnzbdXML->timeleft;
	$sabTitle = 'SABnzbd ('.$timeleft.')';
else:
	$sabTitle = 'SABnzbd';
endif;

$services = array(
	new serviceSAB($sabTitle, 8080, "darpa:8080", "darpa:8080"),
	//new service("Deluge", 8112, "deluge:8112"),
	new service("Sonarr", 8989, "darpa:8989"),
	new service("Radarr", 7878, "darpa:7878"),
	new service("Overseer", 5055, "docker:5055"),
	new service("Emby", 8096, "emby:8096"),
    new service("Navidrome", 4533, "docker:4533"),
	new service("LMS", 9000, "docker:9000"),
    new service("Mealie", 9925, "docker:9925"),
);
?>

<table class ="center">
	<?php foreach($services as $service){ ?>
		<tr>
			<td style="text-align: right; padding-right:5px;" class="exoextralight"><?php echo $service->name; ?></td>
			<td style="text-align: left;"><?php echo $service->makeButton(); ?></td>
		</tr>
	<?php }?>
</table>




