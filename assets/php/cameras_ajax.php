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
	#new service("Nursery", 88, "192.168.1.105:88"),
        #new service("Natalie's Room", 88, "192.168.1.103:88"),
        new service("Shinobi", 8080, "192.168.1.149:8080"),

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
