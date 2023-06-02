<!DOCTYPE html>
<?php
	Error_Reporting( E_ALL | E_STRICT );
	Ini_Set( 'display_errors', true);

	include("assets/php/functions.php");
	include('assets/php/Mobile_Detect.php');

	$detect = new Mobile_Detect;
	//$plexSessionXML = simplexml_load_file($config['network']['plex_server_ip'].'/status/sessions');
?>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Dashboard</title>
		<meta name="author" content="dash">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- Le styles -->
		<link href="assets/fonts/stylesheet.css" rel="stylesheet">
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0-wip/css/bootstrap.min.css"> 
		<link rel="stylesheet" href="assets/css/custom.css">
		<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.0/css/font-awesome.css" rel="stylesheet">
		<style type="text/css">
			body {
				text-align: center;
			}
			.alert {
  			white-space: nowrap; /* Prevents line breaks */
  			overflow: hidden; /* Hides any overflow */
			}

			.bandwidth-heading {
				display: flex;
				justify-content: center;
				position: relative;
			}

			.bandwidth-heading .badge {
				position: absolute;
				right: 0;
				top: 50%;
				transform: translateY(-50%);
			}

			.weather-heading {
				display: flex;
				justify-content: center;
				position: relative;
			}

			.weather-heading .badge {
				position: absolute;
				right: 0;
				top: 50%;
				transform: translateY(-50%);
			}

			.center {
				margin-left:auto;
				margin-right:auto;
			}
			.no-link-color 
				a {
					color:#999999;
				}
				a:hover {
					color:#999999;	
				}
			
			.exoextralight {
				font-family:"exoextralight";
			}
			.exolight {
				font-family:"exolight";
				white-space:nowrap;
				overflow:hidden;
			}

			[data-icon]:before {
				font-family: 'MeteoconsRegular';
				content: attr(data-icon);
			}
			.exoregular {
				font-family:"exoregular";
			}
			/* Changes carousel slide transition to fade transition */
			.carousel {
				overflow: hidden;
			}
			.carousel .item {
				-webkit-transition: opacity 1s;
				-moz-transition: opacity 1s;
				-ms-transition: opacity 1s;
				-o-transition: opacity 1s;
				transition: opacity 1s;
			}
			.carousel .active.left, .carousel .active.right {
				left:0;
				opacity:0;
				z-index:2;
			}
			.carousel .next, .carousel .prev {
				left:0;
				opacity:1;
				z-index:1;
			}
			/* Disables shadowing on right and left sides of carousel images for a crisp look */
			.carousel-control.left {
				background-image: none;
			}
			.carousel-control.right {
				background-image: none;
			}
		</style>
		<link rel="apple-touch-icon-precomposed" href="/assets/ico/apple-touch-icon.png" />
		<link rel="shortcut icon" href="assets/ico/favicon.ico">
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="//code.jquery.com/jquery.js"></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0-wip/js/bootstrap.min.js"></script>
		<script>
		// Enable bootstrap tooltips
		$(function () { 
			$("[rel=tooltip]").tooltip();
			$("[rel=popover]").popover();
			}); 
		// Auto refresh things
		(function($) {
			$(document).ready(function() {
				$.ajaxSetup({
		            		cache: false,
		            		beforeSend: function() {
		            			$('#left_column_top').show();
		            			$('#bandwidth').show();
		            			$('#ping').show();
								$('#comfort').show();
		            			$('#services').show();
                                $('#calender').show();
								$('#sabqueue').show();
                                $('#infrastructure').show();
								$('#system_IO').show();
								$('#system_load').show();
								$('#disk_space').show();
                                $('#disk_space_darpa').show();
                                $('#disk_space_lambda').show();
								$('#system_ram').show();
								$('#uv').show();
		            		},
				            complete: function() {
				            	$('#left_column_top').show();
				            	$('#bandwidth').show();
				            	$('#ping').show();
								$('#comfort').show();
				            	$('#services').show();
                                $('#calender').show();
                                $('#sabqueue').show();
                                $('#infrastructure').show();
                                $('#system_IO').show();
								$('#system_load').show();
								$('#disk_space').show();
                                $('#disk_space_darpa').show();
                                $('#disk_space_lambda').show();
								$('#system_ram').show();
								$('#uv').show();
				            },
				            success: function() {
				            	$('#left_column_top').show();
				            	$('#bandwidth').show();
				            	$('#ping').show();
								$('#comfort').show();
				            	$('#services').show();
                                $('#calender').show();
                                $('#sabqueue').show();
                                $('#infrastructure').show();
                                $('#cameras').show();
								$('#system_load').show();
                                $('#system_IO').show();
								$('#disk_space').show();
                                $('#disk_space_darpa').show();
                                $('#disk_space_lambda').show();
								$('#system_ram').show();
								$('#uv').show();
				            }
	
				});
				// Assign varibles to DOM sections
				var $left_column_top_refresh = $('#left_column_top');
				var $bandwidth_refresh = $('#bandwidth');
				var $ping_refresh = $('#ping');
				var $comfort_refresh = $('#comfort');
				var $services_refresh = $('#services');
				var $calender_refresh = $('#calender');
                var $infrastructure_refresh = $('#infrastructure');
				var $forecast = $('#forecast');
				var $sabqueue_refresh = $('#sabqueue');
			    var $system_load_refresh = $('#system_load');
				var $system_IO_refresh = $('#system_IO');
			    var $disk_space_refresh = $('#disk_space');
                var $disk_space_darpa_refresh = $('#disk_space_darpa');
                var $disk_space_lambda_refresh = $('#disk_space_lambda');
			    var $system_ram_refresh = $('#system_ram');
				var $uv_refresh = $('#uv');


			        	// Load external php files & assign variables
			        	$left_column_top_refresh.load('assets/php/left_column_top_ajax.php');
			        	$bandwidth_refresh.load("assets/php/bandwidth_ajax.php");
			        	$ping_refresh.load("assets/php/ping_ajax.php");
						$comfort_refresh.load("assets/php/comfort_ajax.php");
			        	$services_refresh.load("assets/php/services_ajax.php");
						$calender_refresh.load("assets/php/calender_ajax.php");
						$sabqueue_refresh.load("assets/php/SabNZBqueue_ajax.php");
						$infrastructure_refresh.load("assets/php/infrastructure_ajax.php");
						$forecast.load("assets/php/forecast_ajax.php");
			        	$system_load_refresh.load("assets/php/lambda_metrics_ajax.php");
 						$system_IO_refresh.load("assets/php/cpu_IO_wait_ajax.php");
			        	$disk_space_refresh.load("assets/php/disk_space_ajax.php");
                        $disk_space_darpa_refresh.load("assets/php/disk_space_darpa_ajax.php");
                        $disk_space_lambda_refresh.load("assets/php/disk_space_lambda_ajax.php");
			        	$system_ram_refresh.load("assets/php/system_ram_ajax.php");
						$uv_refresh.load("assets/php/uvindex_ajax.php");

			        
						var refreshIdfastest = setInterval(function(){
			        	}, 10000); // at 3, 5 seconds python was crashing.

			        	var refreshIdfastest = setInterval(function(){
			        	}, 5000); // 5 seconds

			        	var refreshId30 = setInterval(function(){
			        		$bandwidth_refresh.load("assets/php/bandwidth_ajax.php");
			        		$ping_refresh.load("assets/php/ping_ajax.php");
			        		$services_refresh.load("assets/php/services_ajax.php");
	                        $infrastructure_refresh.load("assets/php/infrastructure_ajax.php");

			        	}, 30000); // 30 seconds

			        	var refreshId60 = setInterval(function(){
			        		
			        	}, 60000); // 60 seconds

			        	var refreshIdslow = setInterval(function(){
			            	$system_ram_refresh.load('assets/php/system_ram_ajax.php');
			            //	$zfs_refresh.load("assets/php/zfs_ajax.php");
			            //	$plex_movie_stats_refresh.load("assets/php/plex_movie_stats_ajax.php")
				        	$system_IO_refresh.load("assets/php/cpu_IO_wait_ajax.php");
                            $system_load_refresh.load('assets/php/lambda_metrics_ajax.php');
							$left_column_top_refresh.load('assets/php/left_column_top_ajax.php');
							$uv_refresh.load("assets/php/uvindex_ajax.php");
			        	}, 300000); // 5 minutes

			        	var refreshtopleft = setInterval(function(){
			            	//$left_column_top_refresh.load('assets/php/left_column_top_ajax.php');
                            $sabqueue_refresh.load("assets/php/SabNZBqueue_ajax.php");
                            $calender_refresh.load("assets/php/calender_ajax.php");
                            $disk_space_refresh.load("assets/php/disk_space_ajax.php");
                            $disk_space_darpa_refresh.load("assets/php/disk_space_darpa_ajax.php");
                            $disk_space_lambda_refresh.load("assets/php/disk_space_lambda_ajax.php");
							$comfort_refresh.load("assets/php/comfort_ajax.php");

			        	}, 300000); // 5 minutes

			        	var refreshlongest = setInterval(function(){
							$forecast.load("assets/php/forecast_ajax.php");

			        	}, 3600000); // 1 hour

 						var refreshconditional = setInterval(function(){
				          if(localStorage["resourcemodified"]) {
				               $.ajax({
				                    url:theResource,
				                    type:"head",
				                    success:function(res,code,xhr) {
				                         console.log("Checking Plex XML "+ localStorage["resourcemodified"] + " to "+ xhr.getResponseHeader("Last-Modified"))
				                         if(localStorage["resourcemodified"] != xhr.getResponseHeader("Last-Modified")) getResource();
				                    }
				               })
				 
				          } else getResource();
				 
				          function getResource() {
				               $.ajax({
				                    url:theResource,
				                    type:"get",
				                    cache:false,
				                    success:function(res,code,xhr) {
				                         localStorage["resourcemodified"] = xhr.getResponseHeader("Last-Modified");
				                         console.log("Updating our cache and refreshing Now Playing divs");
				                         $left_column_top_refresh.load('assets/php/left_column_top_ajax.php');
				                    }                    
				               })
				          }
				}, 5000); // 5 seconds

				if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
					// some code..
				} else {
					var resizeTimer;
					$(window).resize(function() {
						clearTimeout(resizeTimer);
						resizeTimer = setTimeout(doResizeNowPlaying, 100);
					});

					$(function(){
	   					clearTimeout(resizeTimer);
						resizeTimer = setTimeout(doResizeNowPlaying, 100);
					});
				}
		    	});
		})(jQuery);
		</script>

	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="row">
						 
						<!-- Left sidebar -->
						<div class="col-md-3" style="padding-top: 20px;">
							<!-- Weather-->
							<div class="panel panel-default">
							<div class="panel-heading">
							<h4 class="panel-title exoextralight weather-heading">
										Weather
            							<span id="uv" class="badge" rel="tooltip" data-toggle="tooltip" data-placement="left" title="uv"></span>
        							</h4>
									</h4>
							</div>		
								<div class="panel-body">	
									<div id="left_column_top"></div>
								</div>
							</div>
							<!-- Bandwidth -->
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title exoextralight bandwidth-heading">
										Bandwidth
            							<span id="ping" class="badge" rel="tooltip" data-toggle="tooltip" data-placement="left" title="Ping"></span>
        							</h4>
								</div>
								<div class="panel-body" style="height:175px">
									<div id="bandwidth"></div>
								</div>
							</div>
							<!-- Services -->
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title exoextralight">
										Services
									</h4>
								</div>
                                                                <div id="services" class="panel-body">
								</div>
							</div>

							<!-- Infrastructure -->
                                                        <div class="panel panel-default">
                                                                <div class="panel-heading">
                                                                        <h4 class="panel-title exoextralight">
                                                                               Infrastructure 
                                                                        </h4>
                                                                </div>
                                                                <div id="infrastructure" class="panel-body">
                                                                </div>
                                                        </div>
						</div>

						<!-- Center Area -->
                                                <div class="col-md-6" style="padding-top: 20px;">

                                                        <div class="panel panel-default">
                                                        <div class="panel-heading">
									<h4 class="panel-title exoextralight">
                                                                              Forecast 
                                                                        </h4>
							</div>
                                                                <div class="panel-body">
                                                                        <div id="forecast" class="panel-body" ></div>
                                                                </div>
							</div>
                                                        <!-- SabNZB Queue -->
                                                        <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                                        <h4 class="panel-title exoextralight">
                                                                               SabNZB
                                                                        </h4>
                                                        </div>
                                                                <div class="panel-body">
                                                                        <div id="sabqueue" class="panel-body" ></div>
                                                                </div>
                                                        </div>

						
                                                         <!-- Calender -->
                                                        <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                                        <h4 class="panel-title exoextralight">
									Upcoming Events
                                                                        </h4>
                                                        </div>
                                                                <div class="panel-body">
                                                                        <div id="calender" class="panel-body" ></div>
                                                                </div>
                                                        </div>
						</div>

						<!-- Right sidebar -->
						<?php echo '<div class="col-md-3"';
						// Only apply padding on top of this column if its not on a mobile device
						if ( $detect->isMobile() ):
							echo '>';
						else:
							echo ' style="padding-top: 20px;">';
						endif;?>
							<!-- Server info -->
							<div class="panel panel-default">
							<div class="panel-heading">
									<h4 class="panel-title exoextralight">
										Server Metrics 
									</h4>
									</div>
								<div class="panel-body">
									<h4 class="exoextralight">Lambda Hypervisor<br/></h4>
									<div id="system_load" style="height:160px"></div>
									<br>
                                    <div id="disk_space_lambda" style="height:100px"></div>
                                    <hr>
									<h4 class="exoextralight">Epiphany Disk space<br/></h4>
									<div id="disk_space" style="height:160px"></div>
									<hr>
									<h4 class="exoextralight">Darpa Disk space<br/></h4>
                                                                        <div id="disk_space_darpa" style="height:40px"></div>
								</div>
							</div>


 													 <div class="panel panel-default">
                                                                <div class="panel-heading">
                                                                        <h4 class="panel-title exoextralight">
                                                                               Comfort 
                                                                        </h4>
                                                                </div>
                                                                <div id="comfort" class="panel-body">
                                                                </div>
																
                                                        </div>
                  
		</body>
</html>
