<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alerts Scheduler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Alerts Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Alerts_Controller extends Controller
{
	public function __construct()
    {
        parent::__construct();
		
		set_time_limit(60);
		
		// $profiler = new Profiler;
		
		
		// PREVENT EXECUTION UNTIL WE FIX THE DUPLICATE ISSUE
		exit;
	}
	
	public function index() 
	{
		$settings = ORM::factory('settings')->find(1);
		$site_name = $settings->site_name;
		$alerts_email = $settings->alerts_email;
		$unsubscribe_message = Kohana::lang('alerts.unsubscribe')
								.url::site().'alerts/unsubscribe/';
		$sms_from = NULL;
		$clickatell = NULL;
		$miles = FALSE; // Change to True to calculate distances in Miles
		$max_recipients = 20; // Limit each script execution to 50 recipients
		
		if ($settings->email_smtp == 1)
		{
			Kohana::config_set('email.driver', 'smtp');
			Kohana::config_set('email.options',
				 array(
					'hostname'=>$settings->alerts_host, 
					'port'=>$settings->alerts_port, 
					'username'=>$settings->alerts_username, 
					'password'=>$settings->alerts_password, 
					//'encryption' => 'tls'	// Secure
				));
		}

		$db = new Database();
		
		/* Find All Alerts with the following parameters
		- incident_active = 1 -- An approved incident
		- incident_alert_status = 1 -- Incident has been tagged for sending
		
		Incident Alert Statuses
		  - 0, Incident has not been tagged for sending. Ensures old incidents are not sent out as alerts
		  - 1, Incident has been tagged for sending by updating it with 'approved' or 'verified'
		  - 2, Incident has been tagged as sent. No need to resend again
		*/
		
		
		// 1 - Retrieve All the Incidents that haven't been sent (Process only 1 per script execution)
		$incidents = $db->query("SELECT incident.id, incident_title, 
								 incident_description, incident_verified, 
								 location.latitude, location.longitude
								 FROM incident INNER JOIN location ON incident.location_id = location.id
								 WHERE incident.incident_active=1 AND incident.incident_alert_status = 1 LIMIT 1 ");
		
		foreach ($incidents as $incident)
		{
			
			$latitude = (double) $incident->latitude;
			$longitude = (double) $incident->longitude;
			
			// 2 - Retrieve All Qualifying Alertees Based on Distance and Make sure they haven't received this alert
			$distance_type = ($miles) ? "" : " * 1.609344";
			$alertees = $db->query('SELECT DISTINCT alert.*, ((ACOS(SIN('.$latitude.' * PI() / 180) * 
									SIN(`alert`.`alert_lat` * PI() / 180) + COS('.$latitude.' * PI() / 180) * 
									COS(`alert`.`alert_lat` * PI() / 180) * COS(('.$longitude.' - `alert`.`alert_lon`)
									 * PI() / 180)) * 180 / PI()) * 60 * 1.1515 '.$distance_type.') AS distance
									FROM alert WHERE alert.alert_confirmed = 1 
									HAVING distance <= alert_radius ');	
			
			$i = 0;
			foreach ($alertees as $alertee)
			{
				// 3 - Has this incident alert been sent to this alertee?
				$alert_sent = ORM::factory('alert_sent')
					->where('alert_id', $alertee->id)
					->where('incident_id', $incident->id)
					->find();
				
				if ($alert_sent->loaded == FALSE)
				{
					// 4 - Get Alert Type. 1=SMS, 2=EMAIL
					$alert_type = (int) $alertee->alert_type;

					if ($alert_type == 1) // SMS alertee
					{
						// Save this first
						$alert = ORM::factory('alert_sent');
						$alert->alert_id = $alertee->id;
						$alert->incident_id = $incident->id;
						$alert->alert_date = date("Y-m-d H:i:s");
						$alert->save();
						
						// Get SMS Numbers
						if (!empty($settings->sms_no3))
							$sms_from = $settings->sms_no3;
						elseif (!empty($settings->sms_no2))
							$sms_from = $settings->sms_no2;
						elseif (!empty($settings->sms_no1))
							$sms_from = $settings->sms_no1;
						else
							$sms_from = "000";      // Admin needs to set up an SMS number

						$clickatell = new Clickatell();
						$clickatell->api_id = $settings->clickatell_api;
						$clickatell->user = $settings->clickatell_username;
						$clickatell->password = $settings->clickatell_password;
						$clickatell->use_ssl = false;
						$clickatell->sms();	

						$message = text::limit_chars($incident->incident_description, 150, "...");
						
						//++ We won't verify for now if sms was sent
						// Leaves too much room for duplicates to be sent out
						$clickatell->send($alertee->alert_recipient, $sms_from, $message);
					}

					elseif ($alert_type == 2) // Email alertee
					
					{
						// Save this first
						$alert = ORM::factory('alert_sent');
						$alert->alert_id = $alertee->id;
						$alert->incident_id = $incident->id;
						$alert->alert_date = date("Y-m-d H:i:s");
						$alert->save();
							
	                   	//for some reason, mail function complains about bad parameters 
	                   	// in the function so i'm disallowing these characters in the 
	                   	// subject field to allow the mail function to work.
	                   	$disallowed_chars = array("(",")","[","]","-");

						$to = $alertee->alert_recipient;
						$from = $alerts_email;
						$subject = trim(str_replace($disallowed_chars,"",$site_name).": ".str_replace($disallowed_chars,"",$incident->incident_title));

						$message = $incident->incident_description
	                                                                       ."<p>".url::base()."reports/view/".$incident->id."</p>"
									."<p>".$unsubscribe_message
	                                                                       ."?c=".$alertee->alert_code."</p>";
						
						//++ We won't verify for now if email was sent
						// Leaves too much room for duplicates to be sent out
						email::send($to, $from, $subject, $message, TRUE);
					}
					
					sleep(1);
					
					$i++;
				}
				
				if ($i == $max_recipients)
				{	
					exit;
				}

			} // End For Each Loop
			
			
			// Update Incident - All Alerts Have Been Sent!
			$update_incident = ORM::factory('incident', $incident->id);
			if ($update_incident->loaded)
			{
				$update_incident->incident_alert_status = 2;
				$update_incident->save();
			}
		}
	}
}
