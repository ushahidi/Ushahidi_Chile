<?php 
/**
 * Messages view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2><?php echo $title; ?>
				<?php
				foreach ($services as $service)
				{
					echo "<a href=\"" . url::base() . "admin/messages/index/".$service->id."\">".$service->service_name."</a>";
				}
				?>
				</h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="?type=1" <?php if ($type == '1') echo "class=\"active\""; ?>>ALL</a></li>
						<li><a href="?type=2" <?php if ($type == '2') echo "class=\"active\""; ?>>Pending</a></li>
						<li><a href="?type=3" <?php if ($type == '3') echo "class=\"active\""; ?>>Active</a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onClick="submitIds()">DELETE</a></li>
							<?php foreach($levels as $level) { ?>
								<li><a href="#" onClick="itemAction('rank', 'Mark As <?php echo $level->level_title?>', '', <?php echo $level->id?>)"><?php echo $level->level_title?></a></li>
							<?php } ?>
							<li><a href="<?php echo url::base(); ?>admin/messages/download/<?php echo $service_id."/".$type."/";?>" class="download">DOWNLOAD THESE MESSAGES</a></li>
						</ul>
					</div>
				</div>
				<?php
				if ($form_error) {
				?>
					<!-- red-box -->
					<div class="red-box">
						<h3>Error!</h3>
						<ul>Please verify that you have checked an item</ul>
					</div>
				<?php
				}

				if ($form_saved) {
				?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3>Messages <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'messagesMain', 'name' => 'messagesMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="level"  id="level"  value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1"><input id="checkallincidents" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'message_id[]' )" /></th>
									<th class="col-2">Message Details</th>
									<th class="col-3">Date</th>
									<th class="col-4">Actions</th>
								</tr>
							</thead>
							<tfoot>
								<tr class="foot">
									<td colspan="4">
										<?php echo $pagination; ?>
									</td>
								</tr>
							</tfoot>
							<tbody>
								<?php
								if ($total_items == 0)
								{
								?>
									<tr>
										<td colspan="4" class="col">
											<h3>No Results To Display!</h3>
										</td>
									</tr>
								<?php	
								}
								foreach ($messages as $message)
								{
									$message_id = $message->id;
									$sms_id = $message->service_messageid;
									$message_from = $message->message_from;
									$message_to = $message->message_to;
									$incident_id = $message->incident_id;
									$message_description = text::auto_link($message->message);
									$message_detail = nl2br(text::auto_link($message->message_detail));
									$message_date = date('Y-m-d', strtotime($message->message_date));
									$message_type = $message->message_type;
									$location_id = $message->location_id;
									$message_read = $message->message_read;
									$message_reply = $message->message_reply;
									
									// Any Replies?
									$reply_count = $replies->where('parent_id', $message_id)
										->count_all();
										
									$last_reply_date = 0;
										
									// Any Locks?
									$username = "";
									$user_id = "";
									if ( count($message->message_lock) > 0 )
									{
										$locked = TRUE;
										foreach ($message->message_lock as $message_lock)
										{
											$username = $message_lock->user->name;
											$user_id = $message_lock->user_id;
										}
									}
									else
									{
										$locked = FALSE;
									}
									?>
									<tr>
										<td class="col-1"><input name="message_id[]" value="<?php echo $message_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<div id="post_<?php echo $message_id; ?>" <?php
												echo " class =\" ";
												if ($locked)
												{
													echo "new_lock ";
												}
												elseif ($message_read)
												{
													echo "new_reply ";
												}
												echo "\"";
												?>><?php if ($service_id == 1) { ?>[<strong><?php echo $sms_id; ?></strong>]<?php } ?>&nbsp;&nbsp;<?php echo $message_description; ?>
												<?php
												if ($locked)
												{
													echo "<div id=\"lock_".$message_id."\" class=\"new_lock_user\">Locked By ".$username."</div>";
												}
												?>
												</div>
												<?php
												if ($locked && $user_id != $_SESSION['auth_user']->id)
												{
													echo "";
												}
												else
												{
													echo "<p>";
													if ($message_detail)
													{?><a href="javascript:preview('message_preview_<?php echo $message_id?>')">+Read More...</a><?php
													}

													if ($service_id == 1 && $message_type == 1) {
													?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:showReply('reply_<?php echo $message_id; ?>')" class="more">+Send Reply</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:showReplies('<?php echo $message_id; ?>')" class="more">+View Replies</a> (<?php echo $reply_count; ?>)<?php } ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:showRead('<?php echo $message_id; ?>')" class="more">+Mark As Read</a>&nbsp;&nbsp;<?php
													if ($locked)
													{
														?>|&nbsp;&nbsp;<span id="lock_message_<?php echo $message_id; ?>"><a href="javascript:showUnlock('<?php echo $message_id; ?>')">+UNLock<?php
													}
													else
													{
														?>|&nbsp;&nbsp;<span id="lock_message_<?php echo $message_id; ?>"><a href="javascript:showLock('<?php echo $message_id; ?>')">+Lock<?php
													}
													?></a></span></p>
													<?php
													if ($message_detail)
													{
														?>
														<div id="message_preview_<?php echo $message_id?>" class="message_more">
															<?php echo $message_detail; ?>
														</div>
														<?php
													}
													?>
													<?php
													if ($service_id == 1 && $message_type == 1)
													{
														?>
														<div id="replies_<?php echo $message_id; ?>" class="replies">
															<?php
															$message_replies = $replies->where('parent_id', $message_id)
																->orderby('message_date', 'desc')
																->find_all();
															
															foreach ($message_replies as $reply)
															{
																$reply_type = $reply->message_type;
																$reply_message = $reply->message;
																$reply_detail = nl2br($reply->message_detail);
																echo "<div class=\"reply_message";
																if ($reply_type == 2)
																	echo " reply_message_out";
																echo "\">";
																if ($reply_type == 1)
																	echo "[<strong>".$reply->service_messageid."</strong>]&nbsp;";
																echo "&middot;&nbsp;".$reply_message;
																if ($reply_detail)
																	echo "<BR>~~~<BR>".$reply_detail;
																echo "</div>";
																
																if (!$last_reply_date)
																{
																	$last_reply_date = date('Y-m-d', strtotime($reply->message_date));
																}
															}
															?>
														</div>
														<div id="reply_<?php echo $message_id; ?>" class="reply">
															<?php print form::open(url::base() . 'admin/messages/send/',array('id' => 'newreply_' . $message_id,
															 	'name' => 'newreply_' . $message_id)); ?>
															<div class="reply_can">
																<a href="javascript:cannedReply('1', 'message_<?php echo $message_id; ?>')">+Location (EN)</a>&nbsp;&nbsp;&nbsp;<a href="javascript:cannedReply('2', 'message_<?php echo $message_id; ?>')">+More Information(EN)</a>&nbsp;&nbsp;&nbsp;<a href="javascript:cannedReply('3', 'message_<?php echo $message_id; ?>')">+Location (KY)</a>&nbsp;&nbsp;&nbsp;<a href="javascript:cannedReply('4', 'message_<?php echo $message_id; ?>')">+More Information (KY)</a></div>
															<div id="replyerror_<?php echo $message_id; ?>" class="reply_error"></div>
															<div class="reply_input"><?php print form::input('message_' .  $message_id, '', ' class="text long2" onkeyup="limitChars(this.id, \'160\', \'replyleft_' . $message_id . '\')" '); ?></div>
															<div class="reply_input"><a href="javascript:sendMessage('<?php echo $message_id; ?>' , 'sending_<?php echo $message_id; ?>')" title="Submit Message"><img src="<?php echo url::base() ?>media/img/admin/btn-send.gif" alt="Submit" border="0" /></a></div>
															<div class="reply_input" id="sending_<?php echo $message_id; ?>"></div>
															<div style="clear:both"></div>
															<?php print form::close(); ?>
															<div id="replyleft_<?php echo $message_id; ?>" class="replychars"></div>
														</div>
														<?php
													}
												}
												?>
											</div>
											<ul class="info">
												<?php
												if ($message_type == 2)
												{
													?><li class="none-separator">To: <strong><?php echo $message_to; ?></strong></li><?php
												}
												else
												{
													?><li class="none-separator">From: <strong><?php echo $message_from; ?></strong></li><?php
													if ($location_id)
													{
														?><li>Location: <strong>Available</strong></li><?php
													}
												}
												?>
											</ul>
										</td>
										<td class="col-3"><?php echo $message_date; 
										
										if ($reply_count > 0)
										{
											echo "<br /><span style=\"font-size:80%; font-weight:normal;color:#7D0013\">Reply On: ".$last_reply_date."</span>";
										}
										?></td>
										<td class="col-4">
											<ul>
												<?php
												if ($locked)
												{
													echo "<li class=\"none-separator\"><a href=\"#\">Locked</a></li>";
												}
												else
												{
													if ($incident_id != 0 && $message_type != 2) {
														echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit/' . $incident_id ."\" class=\"status_yes\"><strong>View Report</strong></a></li>";
													}
													elseif ($message_type != 2)
													{
														echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit?mid=' . $message_id ."\">Create Report?</a></li>";
													}
												}
												?>
												<li>
                                                <a href="<?php echo url::base().'admin/messages/delete/'.$message_id."?service_id=".$service_id."&page=".$pagination->current_page; ?>" onclick="return confirm('Delete cannot be undone. Are you sure you want to continue?')" class="del">Delete</a></li>
											</ul>
										</td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				<?php print form::close(); ?>
			</div>
