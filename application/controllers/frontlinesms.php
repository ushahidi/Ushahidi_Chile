<?php defined('SYSPATH') or die('No direct script access.');
/**
* FrontlineSMS HTTP Post Controller
* Gets HTTP Post data from a FrontlineSMS Installation
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   FrontlineSMS Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Frontlinesms_Controller extends Controller
{
	function index()
	{
		if (isset($_GET['key'])) {
			$frontlinesms_key = $_GET['key'];
		}
		
		if (isset($_GET['s'])) {
			$message_from = $_GET['s'];
			// Remove non-numeric characters from string
			$message_from = ereg_replace("[^0-9]", "", $message_from);
			$message_from = intval($message_from);
		}
		
		if (isset($_GET['m'])) {
			$message_description = $_GET['m'];
		}
		
		if (!empty($frontlinesms_key) 
			&& !empty($message_from) 
			&& !empty($message_description))
		{			
			// Is this a valid FrontlineSMS Key?
			$keycheck = ORM::factory('settings', 1)
							->where('frontlinesms_key', $frontlinesms_key)
							->find();

			if ($keycheck->loaded == TRUE)
			{
				$parent_id = 0;
				
				$services = new Service_Model();
				$service = $services->where('service_name', 'SMS')->find();
				if (!$service) 
					return;
			
				$reporter = ORM::factory('reporter')
									->where('service_id', $service->id)
									->where('service_account', $message_from)
									->find();

				if (!$reporter->loaded)
				{
					// get default reporter level (Untrusted)
					$level = ORM::factory('level')
						->where('level_weight', 0)
						->find();
					
					$reporter->service_id = $service->id;
					$reporter->level_id = $level->id;
					$reporter->service_userid = null;
					$reporter->service_account = $message_from;
					$reporter->reporter_first = null;
					$reporter->reporter_last = null;
					$reporter->reporter_email = null;
					$reporter->reporter_phone = null;
					$reporter->reporter_ip = null;
					$reporter->reporter_date = date('Y-m-d');
					$reporter->save();
				}
				// Number is in our database
				else
				{
					// Find previous message and use it as parent
					$parent = ORM::factory('message')
						->where('reporter_id', $reporter->id)
						->where('message_type', '1')
						->where('parent_id', '0')
						->where('message_trash', '0')
						->orderby('message_date', 'desc')
						->find();
					if ($parent->loaded)
					{
						$parent_id = $parent->id;
						$parent->message_reply = 1;
						$parent->save($parent->id);
					}
				}
				
				// Save Message
				$message = new Message_Model();
				$message->parent_id = $parent_id;
				$message->incident_id = 0;
				$message->user_id = 0;
				$message->reporter_id = $reporter->id;
				$message->message_from = $message_from;
				$message->message_to = null;
				$message->message = $message_description;
				$message->message_type = 1; // Inbox
				$message->message_date = date("Y-m-d H:i:s",time());
				$message->service_messageid = null;
				$message->save();
				
				// Notify Admin Of New Email Message
				$send = notifications::notify_admins(
					"[".Kohana::config('settings.site_name')."] ".
						Kohana::lang('notifications.admin_new_sms.subject'),
					Kohana::lang('notifications.admin_new_sms.message')
					);
			}
		}
	}
}
