<!DOCTYPE html>
<?php
	Ini_Set( 'display_errors', true );
	include("service.class.php");
?>
<html lang="en">
	<script>
	// Enable bootstrap tooltips
	$(function ()
	        { $("[rel=tooltip]").tooltip();
	        });
	</script>
<?php 

$services = array(
	new service("pfSense", 80, "192.168.1.1"),
        new service("Proxmox", 8006, "lambda:8006"),
        new service("Epiphany", 80, "epiphany:80"),
        new service("Ubiquiti", 8443, "docker:8443"),
	new service("Grafana", 3000, "grafana:3000"),
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
