<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Download Controller.
 * This controller will take care of allowing regular users download report in CSV.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Reports Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Download_Controller extends Main_Controller
{
	function __construct()
	{
		parent::__construct();
	
		$this->template->this_page = 'reports';
	}
	
	
	/**
	* Download Reports in CSV format
    */
	
	function index($page = 1)
	{
		$this->template->content = new View('download');
		$this->template->content->title = 'Download Reports';
		
		$form = array(
			'data_point'      => '',
			'data_include'      => '',
			'from_date'    => '',
			'to_date'    => ''
		);
		$errors = $form;
		$form_error = FALSE;
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
            // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
	        $post->add_rules('data_point.*','required','numeric','between[1,4]');
			$post->add_rules('data_include.*','numeric','between[1,5]');
			$post->add_rules('from_date','date_mmddyyyy');
			$post->add_rules('to_date','date_mmddyyyy');
			
			// Validate the report dates, if included in report filter
			if (!empty($_POST['from_date']) || !empty($_POST['to_date']))
			{	
				// Valid FROM Date?
				if (empty($_POST['from_date']) || (strtotime($_POST['from_date']) > strtotime("today"))) {
					$post->add_error('from_date','range');
				}
				
				// Valid TO date?
				if (empty($_POST['to_date']) || (strtotime($_POST['to_date']) > strtotime("today"))) {
					$post->add_error('to_date','range');
				}
				
				// TO Date not greater than FROM Date?
				if (strtotime($_POST['from_date']) > strtotime($_POST['to_date'])) {
					$post->add_error('to_date','range_greater');
				}
			}
			
			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
				// Add Filters
				$filter = " ( 1=1";
				// Report Type Filter
				foreach($post->data_point as $item)
				{
					if ($item == 1) {
						$filter .= " OR incident_active = 1 ";
					}
					if ($item == 2) {
						$filter .= " OR incident_verified = 1 ";
					}
					if ($item == 3) {
						$filter .= " OR incident_active = 0 ";
					}
					if ($item == 4) {
						$filter .= " OR incident_verified = 0 ";
					}
				}
				$filter .= ") ";
				
				// Report Date Filter
				if (!empty($post->from_date) && !empty($post->to_date)) 
				{
					$filter .= " AND ( incident_date >= '" . date("Y-m-d H:i:s",strtotime($post->from_date)) . "' AND incident_date <= '" . date("Y-m-d H:i:s",strtotime($post->to_date)) . "' ) ";					
				}
				
				// Retrieve reports
				$incidents = ORM::factory('incident')->where($filter)->orderby('incident_dateadd', 'desc')->find_all();
				
				// Column Titles
				$report_csv = "#,INCIDENT TITLE,INCIDENT DATE";
				foreach($post->data_include as $item)
				{
					if ($item == 1) {
						$report_csv .= ",LOCATION";
					}
					if ($item == 2) {
						$report_csv .= ",DESCRIPTION";
					}
					if ($item == 3) {
						$report_csv .= ",CATEGORY";
                                        }
                                        if ($item == 4) {
                                                $report_csv .= ",LATITUDE";
                                        }
                                        if($item == 5) {
                                                $report_csv .= ",LONGITUDE";
                                        }
				}
				$report_csv .= ",APPROVED,VERIFIED";
				$report_csv .= "\n";
				
				foreach ($incidents as $incident)
				{
					$report_csv .= '"'.$incident->id.'",';
					$report_csv .= '"'.htmlspecialchars($incident->incident_title).'",';
					$report_csv .= '"'.$incident->incident_date.'"';
					
					foreach($post->data_include as $item)
					{
						if ($item == 1) {
                                                        $report_csv .= ',"'.htmlspecialchars($incident->location->location_name).'"';
						}
						if ($item == 2) {
							$report_csv .= ',"'.htmlspecialchars($incident->incident_description).'"';
						}
						if ($item == 3) {
							$report_csv .= ',"';
							foreach($incident->incident_category as $category) 
							{ 
								$report_csv .= htmlspecialchars($category->category->category_title) . ", ";
							}
							$report_csv .= '"';
                                                }
                                                if ($item == 4) {
                                                        $report_csv .= ',"'.htmlspecialchars($incident->location->latitude).'"';
                                                }
                                                if ($item == 5) {
                                                        $report_csv .= ',"'.htmlspecialchars($incident->location->longitude).'"';
                                                }
					}
					if ($incident->incident_active) {
						$report_csv .= ",YES";
					}
					else
					{
						$report_csv .= ",NO";
					}
					if ($incident->incident_verified) {
						$report_csv .= ",YES";
					}
					else
					{
						$report_csv .= ",NO";
					}
					$report_csv .= "\n";
				}
				
				// Output to browser
				header("Content-type: text/x-csv");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Disposition: attachment; filename=" . time() . ".csv");
				header("Content-Length: " . strlen($report_csv));
				echo $report_csv;
				exit;
				
	        }
			// No! We have validation errors, we need to show the form again, with the errors
	        else   
			{
	            // repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	            // populate the error fields, if any
	            $errors = arr::overwrite($errors, $post->errors('download'));
				$form_error = TRUE;
	        }
		}
		
		$this->template->content->form = $form;
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		
		// Javascript Header
		$this->template->js = new View('admin/reports_download_js');
		$this->template->js->calendar_img = url::base() . "media/img/icon-calendar.gif";
	}
	

    /**
    * Save newly added dynamic categories
    */
	function save_category()
	{
		$this->auto_render = FALSE;
		$this->template = "";
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('category_title','required', 'length[3,200]');
			$post->add_rules('category_description','required');
			$post->add_rules('category_color','required', 'length[6,6]');
			
			
			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
				// SAVE Category
				$category = new Category_Model();
				$category->category_title = $post->category_title;
				$category->category_description = $post->category_description;
				$category->category_color = $post->category_color;
				$category->save();
				$form_saved = TRUE;

				echo json_encode(array("status"=>"saved", "id"=>$category->id));
	        }
            
	        else
	        
			{
	            echo json_encode(array("status"=>"error"));
	        }
	    }
		else
		{
			echo json_encode(array("status"=>"error"));
		}
	}

	/** 
    * Delete Photo 
    * @param int $id The unique id of the photo to be deleted
    */
	function deletePhoto ( $id )
	{
		$this->auto_render = FALSE;
		$this->template = "";
		
		if ( $id )
		{
			$photo = ORM::factory('media', $id);
			$photo_large = $photo->media_link;
			$photo_thumb = $photo->media_thumb;
			
			// Delete Files from Directory
			if (!empty($photo_large))
				unlink(Kohana::config('upload.directory', TRUE) . $photo_large);
			if (!empty($photo_thumb))
				unlink(Kohana::config('upload.directory', TRUE) . $photo_thumb);

			// Finally Remove from DB
			$photo->delete();
		}
	}
	
	/* private functions */
	
	// Return thumbnail photos
	//XXX: This needs to be fixed, it's probably ok to return an empty iterable instead of "0"
	private function _get_thumbnails( $id )
	{
		$incident = ORM::factory('incident', $id);
		
		if ( $id )
		{
			$incident = ORM::factory('incident', $id);
			
			return $incident;
		
		}
		return "0";
	}
	
    private function _get_categories()
    {
 	    // get categories array
		//$this->template->content->bind('categories', $categories);
				
        $categories_total = ORM::factory('category')->where('category_visible', '1')->count_all();
        $this->template->content->categories_total = $categories_total;

		$categories = array();
		foreach (ORM::factory('category')->where('category_visible', '1')->find_all() as $category)
		{
			// Create a list of all categories
			$categories[$category->id] = array($category->category_title, $category->category_color);
		}
		
	    return $categories;
		
	}

    // Dynamic categories form fields
    private function _new_categories_form_arr()
    {
        return array
        (
            'category_name' => '',
            'category_description' => '',
            'category_color' => '',
        );
    }

    // Time functions
    private function _hour_array()
    {
        for ($i=1; $i <= 12 ; $i++) 
        { 
		    $hour_array[sprintf("%02d", $i)] = sprintf("%02d", $i); 	// Add Leading Zero
		}
	    return $hour_array;	
	}
									
	private function _minute_array()
	{								
		for ($j=0; $j <= 59 ; $j++) 
		{ 
			$minute_array[sprintf("%02d", $j)] = sprintf("%02d", $j);	// Add Leading Zero
		}
		
		return $minute_array;
	}
	
	private function _ampm_array()
	{								
	    return $ampm_array = array('pm'=>'pm','am'=>'am');
	}
	
	// Javascript functions
	 private function _color_picker_js()
    {
     return "<script type=\"text/javascript\">
				$(document).ready(function() {
                $('#category_color').ColorPicker({
                        onSubmit: function(hsb, hex, rgb) {
                            $('#category_color').val(hex);
                        },
                        onChange: function(hsb, hex, rgb) {
                            $('#category_color').val(hex);
                        },
                        onBeforeShow: function () {
                            $(this).ColorPickerSetColor(this.value);
                        }
                    })
                .bind('keyup', function(){
                    $(this).ColorPickerSetColor(this.value);
                });
				});
            </script>";
    }
    
    private function _date_picker_js() 
    {
        return "<script type=\"text/javascript\">
				$(document).ready(function() {
				$(\"#incident_date\").datepicker({ 
				showOn: \"both\", 
				buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\", 
				buttonImageOnly: true 
				});
				});
			</script>";	
    }
    

    private function _new_category_toggle_js()
    {
        return "<script type=\"text/javascript\">
			    $(document).ready(function() {
			    $('a#category_toggle').click(function() {
			    $('#category_add').toggle(400);
			    return false;
				});
				});
			</script>";
    }


	/**
	 * Checks if translation for this report & locale exists
     * @param Validation $post $_POST variable with validation rules 
	 * @param int $iid The unique incident_id of the original report
	 */
	public function translate_exists_chk(Validation $post)
	{
		// If add->rules validation found any errors, get me out of here!
		if (array_key_exists('locale', $post->errors()))
			return;
		
		$iid = $_GET['iid'];
		if (empty($iid)) {
			$iid = 0;
		}
		$translate = ORM::factory('incident_lang')->where('incident_id',$iid)->where('locale',$post->locale)->find();
		if ($translate->loaded == true) {
			$post->add_error( 'locale', 'exists');		
		// Not found
		} else {
			return;
		}
	}


	/**
	 * Retrieve Custom Form Fields
	 * @param bool|int $incident_id The unique incident_id of the original report
	 * @param int $form_id The unique form_id. Uses default form (1), if none selected
	 * @param bool $field_names_only Whether or not to include just fields names, or field names + data
	 * @param bool $data_only Whether or not to include just data
	 */
	private function _get_custom_form_fields($incident_id = false, $form_id = 1, $data_only = false)
    {
		$fields_array = array();
		
		if (!$form_id)
		{
			$form_id = 1;
		}
		$custom_form = ORM::factory('form', $form_id)->orderby('field_position','asc');
		foreach ($custom_form->form_field as $custom_formfield)
		{
			if ($data_only)
			{ // Return Data Only
				$fields_array[$custom_formfield->id] = '';
				
				foreach ($custom_formfield->form_response as $form_response)
				{
					if ($form_response->incident_id == $incident_id)
					{
						$fields_array[$custom_formfield->id] = $form_response->form_response;
					}
				}
			}
			else
			{ // Return Field Structure
				$fields_array[$custom_formfield->id] = array(
					'field_id' => $custom_formfield->id,
					'field_name' => $custom_formfield->field_name,
					'field_type' => $custom_formfield->field_type,
					'field_required' => $custom_formfield->field_required,
					'field_maxlength' => $custom_formfield->field_maxlength,
					'field_height' => $custom_formfield->field_height,
					'field_width' => $custom_formfield->field_width,
					'field_isdate' => $custom_formfield->field_isdate,
					'field_response' => ''
					);
			}
		}
		
		return $fields_array;
    }


	/**
	 * Validate Custom Form Fields
	 * @param array $custom_fields Array
	 */
	private function _validate_custom_form_fields($custom_fields = array())
    {
		$custom_fields_error = "";
		
		foreach ($custom_fields as $field_id => $field_response)
		{
			// Get the parameters for this field
			$field_param = ORM::factory('form_field', $field_id);
			if ($field_param->loaded == true)
			{
				// Validate for required
				if ($field_param->field_required == 1 && $field_response == "")
				{
					return false;
				}

				// Validate for date
				if ($field_param->field_isdate == 1 && $field_response != "")
				{
					$myvalid = new Valid();
					return $myvalid->date_mmddyyyy($field_response);
				}
			}
		}
		return true;
    }


	/**
	 * Ajax call to update Incident Reporting Form
	 */
	public function switch_form()
    {
		$this->template = "";
		$this->auto_render = FALSE;
		
		isset($_POST['form_id']) ? $form_id = $_POST['form_id'] : $form_id = "1";
		isset($_POST['incident_id']) ? $incident_id = $_POST['incident_id'] : $incident_id = "";
			
		$html = "";
		$fields_array = array();		
		$custom_form = ORM::factory('form', $form_id)->orderby('field_position','asc');
		
		foreach ($custom_form->form_field as $custom_formfield)
		{
			$fields_array[$custom_formfield->id] = array(
				'field_id' => $custom_formfield->id,
				'field_name' => $custom_formfield->field_name,
				'field_type' => $custom_formfield->field_type,
				'field_required' => $custom_formfield->field_required,
				'field_maxlength' => $custom_formfield->field_maxlength,
				'field_height' => $custom_formfield->field_height,
				'field_width' => $custom_formfield->field_width,
				'field_isdate' => $custom_formfield->field_isdate,
				'field_response' => ''
				);
			
			// Load Data, if Any
			foreach ($custom_formfield->form_response as $form_response)
			{
				if ($form_response->incident_id = $incident_id)
				{
					$fields_array[$custom_formfield->id]['field_response'] = $form_response->form_response;
				}
			}
		}
		
		foreach ($fields_array as $field_property)
		{
			$html .= "<div class=\"row\">";
			$html .= "<h4>" . $field_property['field_name'] . "</h4>";
			if ($field_property['field_type'] == 1)
			{ // Text Field
				// Is this a date field?
				if ($field_property['field_isdate'] == 1)
				{
					$html .= form::input('custom_field['.$field_property['field_id'].']', $field_property['field_response'],
						' id="custom_field_'.$field_property['field_id'].'" class="text"');
					$html .= "<script type=\"text/javascript\">
							$(document).ready(function() {
							$(\"#custom_field_".$field_property['field_id']."\").datepicker({ 
							showOn: \"both\", 
							buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\", 
							buttonImageOnly: true 
							});
							});
						</script>";
				}
				else
				{
					$html .= form::input('custom_field['.$field_property['field_id'].']', $field_property['field_response'],
						' id="custom_field_'.$field_property['field_id'].'" class="text custom_text"');
				}
			}
			elseif ($field_property['field_type'] == 2)
			{ // TextArea Field
				$html .= form::textarea('custom_field['.$field_property['field_id'].']',
					$field_property['field_response'], ' class="custom_text" rows="3"');
			}
			$html .= "</div>";
		}
		
		echo json_encode(array("status"=>"success", "response"=>$html));
    }

	/**
	 * Creates a SQL string from search keywords
	 */
	private function _get_searchstring($keyword_raw)
	{
		$or = '';
		$where_string = '';
		
		
		// Stop words that we won't search for
		// Add words as needed!!
		$stop_words = array('the', 'and', 'a', 'to', 'of', 'in', 'i', 'is', 'that', 'it', 
		'on', 'you', 'this', 'for', 'but', 'with', 'are', 'have', 'be', 
		'at', 'or', 'as', 'was', 'so', 'if', 'out', 'not');
		
		$keywords = explode(' ', $keyword_raw);
		if (is_array($keywords) && !empty($keywords)) {
			array_change_key_case($keywords, CASE_LOWER);
			$i = 0;
			foreach($keywords as $value) {
				if (!in_array($value,$stop_words) && !empty($value))
				{
					$chunk = mysql_real_escape_string($value);
					if ($i > 0) {
						$or = ' OR ';
					}
					$where_string = $where_string.$or."incident_title LIKE '%$chunk%' OR incident_description LIKE '%$chunk%'  OR location_name LIKE '%$chunk%'";
					$i++;
				}
			}
		}
		
		if ($where_string)
		{
			return $where_string;
		}
		else
		{
			return "1=1";
		}
	}
}
