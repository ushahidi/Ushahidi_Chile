<?php
/**
 * Main view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Dashboard Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General
 * Public License (LGPL)
 */
?>

				<!-- main body -->
				<div id="main" class="clearingfix">
					<div id="mainmiddle" class="floatbox withright">
				
						<!-- right column -->
						<div id="right" class="clearingfix">
						
							<!-- category filters -->
							<div class="cat-filters clearingfix">
								<strong><?php echo Kohana::lang('ui_main.category_filter');?></strong>
							</div>
						
							<ul class="category-filters">
								<li><a class="active" id="cat_0" href="#"><div class="swatch" style="background-color:#<?php echo $default_map_all;?>"></div><div class="cat-title">All Categories</div></a></li>
								<?php
									//print_r($categories);
									foreach ($categories as $category => $category_info)
									{
										$category_title = $category_info[0];
										$category_color = $category_info[1];
										$category_image = $category_info[2];
										
										//echo '<li><a href="#" id="cat_'. $category .'"><div class="swatch" style="background-color:#'.$category_color.'"></div><div class="category-title">'.$category_title.'</div></a></li>';
										
										if( !empty($category_image)){
											echo '<li><a href="#" id="cat_'. $category .'"><div class="swatch"><img src="'.url::base()."media/uploads/".$category_image.'" border="0"></div>
												<div class="cat-title">'.$category_title.'</div></a>';
										}else{
											echo '<li><a href="#" id="cat_'. $category .'"><div class="swatch" style="background-color:#'.$category_color.'"></div>
												<div class="cat-title">'.$category_title.'</div></a>';
										}


										// Get Children
										echo '<div class="hide" id="child_'. $category .'"><ul>';
										//if(is_array($category_info[3])){
											foreach ($category_info[3] as $child => $child_info)
											{
												$child_title = $child_info[0];
												$child_color = $child_info[1];
												$child_image = $child_info[2 ];
												//echo '<li style="padding-left:20px;"><a href="#" id="cat_'. $child .'"><div class="swatch" style="background-color:#'.$child_color.'"></div><div class="category-title">'.$child_title.'</div></a></li>';
												
												if( !empty($child_image)){
													echo '<li style="padding-left:20px;"><a href="#" id="cat_'. $child .'"><div class="swatch child"><img src="'.url::base()."media/uploads/".$child_image.'" border="0"></div>
												    <div class="cat-title">'.$child_title.'</div></a></li>';
												}else{
													echo '<li style="padding-left:20px;"><a href="#" id="cat_'. $child .'"><div class="swatch child" style="background-color:#'.$child_color.'"></div><div class="cat-title">'.$child_title.'</div></a></li>';
												}												
											}
										//}
										echo '</ul></div></li>';
									}							
								?>
							</ul>
							<!-- / category filters -->
							
							
							
							
							<br />
						
                        <!-- 
                        SMS/Text Local: 4636  
                        SMS/Text International: +44 762 480 2524
                        
                        Email: Chile@Ushahidi.com
                        
                        Twittter: #Chile or #terremotochile
                        
                        Web Form: Submit Report -->
						
                        	<!-- additional content -->
							<div class="additional-content">
								<h5><?php echo Kohana::lang('ui_main.how_to_report'); ?></h5>
								<ol>
									<?php if (!empty($phone_array)) 
									{ ?><li class="report r-sms">
									
										<?php // echo Kohana::lang('ui_main.report_option_1-local').' '.$sms_no2."."; ?>
										<?php echo Kohana::lang('ui_main.report_option_1-international').'&nbsp;+44 762.480.2524.'; ?>
                                        
                                    </li><?php } ?>
								
									<?php if (!empty($report_email)) 
									{ ?><li class="report r-email"><?php echo Kohana::lang('ui_main.report_option_2'); ?> <a href="mailto:<?php echo $report_email?>"><?php echo $report_email?></a></li><?php } ?>
									
									<?php if (!empty($twitter_hashtag_array)) 
									{ ?><li class="report r-twitter"><?php echo Kohana::lang('ui_main.report_option_3'); ?> <?php foreach ($twitter_hashtag_array as $twitter_hashtag) {
									echo "<strong>". $twitter_hashtag ."</strong>";
									if ($twitter_hashtag != end($twitter_hashtag_array)) {
										echo " or ";
									}
									} ?></li><?php } ?>
									
                                    <li class="report r-online"><?php echo Kohana::lang('ui_main.report_option_4'); ?> <a href="<?php echo url::base() . 'reports/submit/'; ?>">Submit Report
                                    </a></li>
								</ol>	
							</div>
							<?php if($latest_activity_week != 0){ ?>
                            <div class="additional-content latest-activity">
								<h5>Latest Activity</h5>
                                <ul>
                                	
                                	<li><span class="announcement small"><?php echo $latest_activity_today; ?></span> report<?php echo ($latest_activity_today == 1) ? " was" : "s were"; ?> added <strong>today</strong>.</li>
                                    
                                    <li><span class="announcement small"><?php echo $latest_activity_yesterday; ?></span> report<?php echo ($latest_activity_yesterday == 1) ? " was" : "s were"; ?> added <strong>yesterday</strong>.</li>
                                    
                                    <li><span class="announcement small"><?php echo $latest_activity_week; ?></span> report<?php echo ($latest_activity_week == 1) ? "" : "s"; ?> have been added <strong>this week</strong>.</li>
                                    
                                    <li><span class="announcement small"><?php echo $latest_activity_total_from_sms; ?></span> <?php echo ($latest_activity_total_from_sms == 1) ? "report" : "total reports"; ?> created via SMS.</li>
                                
                                </ul>
                            </div>
                            <?php } ?>
                            
						</div>
						<!-- / right column -->
					
						<!-- content column -->
						<div id="content" class="clearingfix">
                        	
							<div class="floatbox">
							
								<!-- filters -->
								<div class="filters clearingfix">
								<div style="float:left; width: 65%">
									<strong><?php echo Kohana::lang('ui_main.filters'); ?></strong>
									<ul>
										<li><a id="media_0" class="active" href="#"><span><?php echo Kohana::lang('ui_main.reports'); ?></span></a></li>
										<li><a id="media_4" href="#"><span><?php echo Kohana::lang('ui_main.news'); ?></span></a></li>
										<li><a id="media_1" href="#"><span><?php echo Kohana::lang('ui_main.pictures'); ?></span></a></li>
										<li><a id="media_2" href="#"><span><?php echo Kohana::lang('ui_main.video'); ?></span></a></li>
										<li><a id="media_0" href="#"><span><?php echo Kohana::lang('ui_main.all'); ?></span></a></li>
									</ul>
</div>
								<div style="float:right; width: 31%">
									<strong><?php echo Kohana::lang('ui_main.views'); ?></strong>
									<ul>
										<li><a id="view_0" <?php if($map_enabled === 'streetmap') { echo 'class="active" '; } ?>href="#"><span><?php echo Kohana::lang('ui_main.clusters'); ?></span></a></li>
										<li style="display:none;"><a id="view_1" <?php if($map_enabled === '3dmap') { echo 'class="active" '; } ?>href="#"><span><?php echo Kohana::lang('ui_main.time'); ?></span></a></li>
                  </ul>
</div>
								</div>
								<!-- / filters -->
						
								<!-- map -->
								<?php
									// My apologies for the inline CSS. Seems a little wonky when styles added to stylesheet, not sure why.
								?>
								<div class="<?php echo $map_container; ?>" id="<?php echo $map_container; ?>" <?php if($map_container === 'map3d') { echo 'style="width:573px; height:573px;"'; } ?>></div>
								<div style="clear:both;"></div>
								<div id="mapStatus">
									<div id="mapScale" style="border-right: solid 1px #999"></div>
									<div id="mapMousePosition" style="min-width: 135px;border-right: solid 1px #999;text-align: center"></div>
									<div id="mapProjection" style="border-right: solid 1px #999"></div>
									<div id="mapOutput"></div>
								</div>
								<div style="clear:both;"></div>
                                
                                <div class="cat-filters clearingfix" style="margin-top:20px;">
                                    <strong>Timeline of Events</strong>
                                </div>
                               <?php if($map_container === 'map') { ?>
								<div class="slider-holder">
									<form action="">
										<input type="hidden" value="0" name="currentCat" id="currentCat">
										<fieldset>
											<div class="play"><a href="#" id="playTimeline">PLAY</a></div>
											<label for="startDate">From:</label>
											<select name="startDate" id="startDate"><?php echo $startDate; ?></select>
											<label for="endDate">To:</label>
											<select name="endDate" id="endDate"><?php echo $endDate; ?></select>
										</fieldset>
									</form>
								</div>
								<?php } ?>
								<!-- / map -->
								<div id="graph" class="graph-holder"></div>
                             
							</div>
                            
						</div>
						<!-- / content column -->
				
					</div>
                    <?php
					if ($layers)
					{
						?>
						<!-- Layers (KML/KMZ) -->
						<div class="cat-filters clearingfix" style="margin-top:20px;">
							<strong><?php echo Kohana::lang('ui_main.layers_filter');?></strong>
						</div>
						<ul id="kml-layers" class="category-filters cf-kml">
							<?php
							foreach ($layers as $layer => $layer_info)
							{
								$layer_name = $layer_info[0];
								$layer_color = $layer_info[1];
								$layer_url = $layer_info[2];
								$layer_file = $layer_info[3];
								$layer_link = (!$layer_url) ?
									url::base().'media/uploads/'.$layer_file :
									$layer_url;
								echo '<li><a href="#" id="layer_'. $layer .'"
								onclick="switchLayer(\''.$layer.'\',\''.$layer_link.'\',\''.$layer_color.'\'); return false;"><div class="swatch" style="background-color:#'.$layer_color.'"></div>
								<div class="cat-title">'.$layer_name.'</div></a></li>';
							}
							?>
						</ul>
						<!-- /Layers -->
						<?php
					}
					?>
				</div>
				<!-- / main body -->
			
				<!-- content -->
				<div class="content-container">
                
			
					<!-- content blocks -->
					<div class="content-blocks clearingfix">
					<div class="multimedia-integration">
                    	<table id="multimedia">
                        	<tr>
                            	<td class="mm-twitter">
                                	<h3>En Twitter</h3>
                                    
									<script src="http://widgets.twimg.com/j/2/widget.js"></script>
									<script>
                                    new TWTR.Widget({
                                      version: 2,
                                      type: 'search',
                                      search: 'chile',
                                      interval: 6000,
                                      title: '',
                                      subject: '#chile',
                                      width: 400,
                                      height: 300,
                                      theme: {
                                        shell: {
                                          background: '#0925c2',
                                          color: '#ffffff'
                                        },
                                        tweets: {
                                          background: '#ffffff',
                                          color: '#444444',
                                          links: '#d21c00'
                                        }
                                      },
                                      features: {
                                        scrollbar: false,
                                        loop: true,
                                        live: true,
                                        hashtags: true,
                                        timestamp: true,
                                        avatars: true,
                                        behavior: 'default'
                                      }
                                    }).render().start();
                                    </script>
                                </td>
                                
                               <!-- <td class="mm-flickr">
                                	<h3 style="margin-bottom:10px">Help Tag Photos</h3>
                                 
                                    
                                </td> -->
                                
                                
								<td class="mm-youtube" style="padding:0 0 0 50px">
                                    <h3>Buscador de Personas</h3>
                                    <iframe src="http://chilepersonfinder.appspot.com/?small=yes" width="400" height="368" frameborder="0" style="border: dashed 2px #77c; background:#fff;"></iframe>
								</td>
                            </tr>
                        </table>
                    </div>
						<!-- left content block -->
						<table id="feed-grid-table">
                        <tr>
                        <td class="fgt-left">
                        <div class="feed-grid">
							<h5><?php echo Kohana::lang('ui_main.incidents_listed'); ?></h5>
							<table class="table-list">
								<thead>
									<tr>
										<th scope="col" class="title"><?php echo Kohana::lang('ui_main.title'); ?></th>
										<th scope="col" class="location"><?php echo Kohana::lang('ui_main.location'); ?></th>
										<th scope="col" class="date"><?php echo Kohana::lang('ui_main.date'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
	 								if ($total_items == 0)
									{
									?>
									<tr><td colspan="3">No Reports In The System</td></tr>

									<?php
									}
									foreach ($incidents as $incident)
									{
										$incident_id = $incident->id;
										$incident_title = text::limit_chars($incident->incident_title, 40, '...', True);
										$incident_date = $incident->incident_date;
										$incident_date = date('M j Y', strtotime($incident->incident_date));
										$incident_location = $incident->location->location_name;
									?>
									<tr>
										<td class="title"><a href="<?php echo url::base() . 'reports/view/' . $incident_id; ?>"> <?php echo $incident_title ?></a></td>
										<td class="location"><?php echo $incident_location ?></td>
										<td class="date"><?php echo $incident_date; ?></td>
									</tr>
									<?php
									}
									?>

								</tbody>
							</table>
							<p><a class="more" href="<?php echo url::base() . 'reports/' ?>">View More &raquo;</a></p>
						</div>
						<!-- / left content block -->
						</td>
                        <td class="fgt-right">
						<!-- right content block -->
						<div class="feed-grid">
							<h5><?php echo Kohana::lang('ui_main.official_news'); ?></h5>
							<table class="table-list">
								<thead>
									<tr>
										<th scope="col" class="title"><?php echo Kohana::lang('ui_main.title'); ?></th>
										<th scope="col" class="location"><?php echo Kohana::lang('ui_main.source'); ?></th>
										<th scope="col" class="date"><?php echo Kohana::lang('ui_main.date'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($feeds as $feed)
									{
										$feed_id = $feed->id;
										$feed_title = text::limit_chars($feed->item_title, 40, '...', True);
										$feed_link = $feed->item_link;
										$feed_date = date('M j Y', strtotime($feed->item_date));
										$feed_source = text::limit_chars($feed->feed->feed_name, 15, "...");
									?>
									<tr>
										<td class="title"><a href="<?php echo $feed_link; ?>" target="_blank"><?php echo $feed_title ?></a></td>
										<td class="location"><?php echo $feed_source; ?></td>
										<td class="date"><?php echo $feed_date; ?></td>
									</tr>
									<?php
									}
									?>
								</tbody>
							</table>
							<a class="more" href="<?php echo url::base() . 'feeds' ?>">View More &raquo;</a>
                            
						</div>
                        <div class="feed-grid hide">
                            <h5>Citizen Journalism</h5>
                            <table class="table-list">
                                <thead>
                                    <tr>
                                        <th scope="col" class="title"><?php echo Kohana::lang('ui_main.title'); ?></th>
                                        <th scope="col" class="location"><?php echo Kohana::lang('ui_main.source'); ?></th>
                                        <th scope="col" class="date"><?php echo Kohana::lang('ui_main.date'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($gvfeeds as $feed)
                                    {
                                        $feed_id = $feed->id;
                                        $feed_title = text::limit_chars($feed->item_title, 40, '...', True);
                                        $feed_link = $feed->item_link;
                                        $feed_date = date('M j Y', strtotime($feed->item_date));
                                        $feed_source = text::limit_chars($feed->feed->feed_name, 15, "...");
                                    ?>
                                    <tr>
                                        <td class="title"><a href="<?php echo $feed_link; ?>" target="_blank"><?php echo $feed_title ?></a></td>
                                        <td class="location"><?php echo $feed_source; ?></td>
                                        <td class="date"><?php echo $feed_date; ?></td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <a class="more" href="<?php echo url::base() . 'feeds' ?>">View More &raquo;</a>
                        </div>
                        
                        </td>
                        </tr>
						
					</table>
					</div>
                    
					<!-- /content blocks -->
                    
				</div>
				<!-- content -->
		
			</div>
		</div>
		<!-- / main body -->

	</div>
	<!-- / wrapper -->
