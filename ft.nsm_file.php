<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * NSM File Fieldtype
 *
 * @package			NsmFile
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-example-addon
 * @see				http://expressionengine.com/public_beta/docs/development/fieldtypes.html
 */

class Nsm_file_ft extends EE_Fieldtype
{
	/**
	 * Field info - Required
	 * 
	 * @access public
	 * @var array
	 */
	public $info = array(
		'name'		=> 'NSM File',
		'version'	=> '1.0.0'
	);

	/**
	 * The fieldtype global settings array
	 * 
	 * @access public
	 * @var array
	 */
	public $settings = array();

	/**
	 * The field type - used for form field prefixes. Must be unique and match the class name. Set in the constructor
	 * 
	 * @access private
	 * @var string
	 */
	public $addon_id = '';

	public $has_array_data = true;

	/**
	 * Constructor
	 * 
	 * @access public
	 */
	public function __construct()
	{
		$this->addon_id = strtolower(substr(__CLASS__, 0, -3));
		parent::EE_Fieldtype();
	}	

	/**
	 * Replaces the custom field tag
	 * 
	 * @access public
	 * @param $data string Contains the field data (or prepped data, if using pre_process)
	 * @param $params array Contains field parameters (if any)
	 * @param $tagdata mixed Contains data between tag (for tag pairs) FALSE for single tags
	 * @return string The HTML replacing the tag
	 * 
	 */
	public function replace_tag($data, $params = FALSE, $tagdata = FALSE)
	{
		$this->_prep_data($data);

		if ($tagdata !== FALSE)
		{
			$tagdata = $this->EE->functions->prep_conditionals($tagdata, $data);
			$tagdata = $this->EE->functions->var_swap($tagdata, $data);
			return $tagdata;
		}
		else
		{
			$full_path = $data['path'].$data['filename'];
			if (isset($params['wrap']))
			{
				if ($params['wrap'] == 'link')
					return '<a href="'.$full_path.'">'.$file_info['filename'].'</a>';

				elseif ($params['wrap'] == 'image')
					return '<img src="'.$full_path.'" alt="'.$data['caption'].'" />';

			}
			return $full_path;
		}
	}

	/**
	 * Install the fieldtype
	 *
	 * @return array The default settings for the fieldtype
	 */
	public function install()
	{
		return array("setting_1" => TRUE);
	}

	public function _prep_data($data)
	{
		$default_data = array(
			'filedir' => false, 	// Uplod directory id
			'path' => false, 		// Directory url
			'thumb' => false, 		// Full server path to the thumbnail
			'filename' => false, 	// Full filename
			'extension' => false,	// File extension
			'caption' => false,
			'credit' => false,
			'subject' => false,
			'style' => false,
			'size' => false,
			'sever_path' => false,
			'error' => false,
			'is_image' => false
		);

		// If there's no data the entry is new. Create a new array
		if(!$data)
			$data = array();
		
		// if the data is a string
		if(is_string($data))
		{
			// parse out old data
			if (preg_match('/^{filedir_(\d+)}(.*)/', $data, $matches))
			{
				$tmp = array();
				$tmp['filedir'] = $matches[1];
				$tmp['filename'] = $matches[2];
				$data = $tmp;
			}
			else
			{
				// unserialise the array
				$data = unserialize(html_entity_decode($data));
			}
		}


		// file exists
		if(isset($data['filename']))
		{
			// get the extension
			$data['extension'] = substr(strrchr($data['filename'], '.'), 1);

			// get the upload directories
			$this->EE->load->model('tools_model');
			$upload_directories = $this->EE->tools_model->get_upload_preferences()->result_array();

			// for each of the file upload directories
			foreach($upload_directories as $d)
			{
				// is it a match
				if($data['filedir'] == $d['id'])
				{
					$data['path'] = $d['url'];
					$data['server_path'] = $d['server_path'];
					
					// Is it a directory?
					if(is_dir($data['server_path'] . $data['filename']))
						$data['error'] = "You selected a folder not a file.";

					// Does the file exist?
					elseif(file_exists($data['server_path'] . $data['filename']))
						$data['thumb'] = $data['path'] . '_thumbs/thumb_' . $data['filename'];

					// Yay winner
					else
						$data['error'] = "File could not be found in:" . $data['server_path'] . $data['filename'];

					// is there an error?
					if(isset($data['error']))
						$data['thumb'] = PATH_CP_GBL_IMG.'default.png';
						
					break;
				}
			}
		}

		return array_merge($default_data, $data);			
		
	}

	/**
	 * Display the field in the publish form
	 * 
	 * @access public
	 * @param $data String Contains the current field data. Blank for new entries.
	 * @param $field_id String The field id - Low variables
	 * @return String The custom field HTML
	 */
	public function display_field($data = false, $input_name = false, $field_id = false)
	{
		if(!$field_id)
			$field_id = $this->field_name;

		if(!$input_name)
			$field_id = $this->field_name;

		$data = $this->_prep_data($data);

		if(!isset($this->EE->cache[__CLASS__]['resources_loaded']))
		{
			$theme_url = $this->_getThemeUrl();
			$this->EE->cp->add_to_head("<link rel='stylesheet' href='{$theme_url}/styles/admin.css' type='text/css' media='screen' charset='utf-8' />");
			$this->EE->cp->add_to_foot("<script src='{$theme_url}/scripts/admin.js' type='text/javascript' charset='utf-8'></script>");
			$this->EE->cache[__CLASS__]['resources_loaded'] = true;
		}

		//print_r($data);
		$vars = array(
			'file' => $data,
			'caption' => false,
			'credit' => false,
			'subject' => false,
			'style' => false,
			'size' => false,
			'layout' => $this->settings['layout'],
			'field_id' => $field_id,
			'input_name' => $input_name
		);


		if(isset($this->settings['fields']['caption']['display']))
		{
			$vars['caption'] = array(
				'label' => lang('Caption', $field_id . '_caption'),
				'field' => form_textarea(array(
					'name' => $input_name . '[caption]',
					'id' => $field_id . '_caption',
					'style' => "height: {$this->settings['fields']['caption']['height']}; width: {$this->settings['fields']['caption']['width']};",
					'value' => $data['caption']
				))
			);
		}

		if(isset($this->settings['fields']['credit']['display']))
		{
			$vars['credit'] = array(
				'label' => lang('Credit', $field_id . '_credit'),
				'field' => form_textarea(array(
					'name' => $input_name . '[credit]',
					'id' => $field_id . '_credit',
					'style' => "height: {$this->settings['fields']['credit']['height']}; width: {$this->settings['fields']['credit']['width']};",
					'value' => $data['credit']
				))
			);
		}

		if(isset($this->settings['fields']['subject']['display']))
		{
			$vars['subject'] = array(
				'label' => lang('Subject', $field_id . '_subject'),
				'field' => form_textarea(array(
					'name' => $input_name . '[subject]',
					'id' => $field_id . '_subject',
					'style' => "height: {$this->settings['fields']['subject']['height']}; width: {$this->settings['fields']['subject']['width']};",
					'value' => $data['subject']
				))
			);
		}

		if(isset($this->settings['fields']['styles']['display']))
		{
			$options = $this->_parse_options($this->settings['fields']['styles']['options']);
			$vars['style'] = array(
				'label' => lang('Style', $field_id . '_style'),
				'field' => form_dropdown(
					$input_name . '[style]', 
					$options, 
					$data['style'],
					"id = '{$field_id}_style'"
				)
			);
		}

		if(isset($this->settings['fields']['sizes']['display']))
		{
			$options = $this->_parse_options($this->settings['fields']['sizes']['options']);
			$vars['size'] = array(
				'label' => lang('Size', $field_id . '_size'),
				'field' => form_dropdown(
						$input_name . '[size]', 
						$options, 
						$data['size'],
						"id = '{$field_id}_size'"						
					)
				);
		}

		// Path is relative to the current addon which could be native, Matrix or Low Varables
		
		return $this->EE->load->_ci_load(array(
			'_ci_vars' => $vars,
			'_ci_path' => PATH_THIRD . 'nsm_file/views/fieldtype/field.php',
			'_ci_return' => true
		));
		
		// return $this->EE->load->view('../../nsm_file/views/fieldtype/' . $type, $vars, TRUE);
	}

	public function _parse_options($str)
	{
		$options = array();
		foreach ($lines = explode("\n", $str) as $line)
		{
			$values = explode(" : ", $line);
			$options[$values[0]] = (isset($values[1])) ? $values[1] : $values[0];
		}
		return $options;		
	}

	/**
	 * Displays the cell - MATRIX COMPATIBILITY
	 * 
	 * @access public
	 * @param $data The cell data
	 * @return string The cell HTML
	 */
	public function display_cell($data)
	{
		return $this->display_field($data, $this->cell_name);
	}

	/**
	 * Displays the Low Variable field
	 * 
	 * @access public
	 * @param $var_data The variable data
	 * @return string The cell HTML
	 * @see http://loweblog.com/software/low-variables/docs/fieldtype-bridge/
	public function display_var_field($var_data)
	{
		$this->_load_filebrowser();
		return $this->display_field($var_data, false, __CLASS__ . '_' . substr($this->field_name, 4, 1));
	}
	 */


	/**
	 * Publish form validation
	 * 
	 * @access public
	 * @param $data array Contains the submitted field data.
	 * @return mixed TRUE or an error message
	 */
	public function validate($data)
	{
		return TRUE;
	}

	public function save($data)
	{
		return (empty($data['filename'])) ? false : serialize($data);
	}

	public function save_cell($data)
	{
		return $this->save($data);
	}

	/**
	 * Default settngs
	 * 
	 * @access public
	 * @param $settings array The field / cell settings
	 * @return array Labels and form inputs
	 */
	private function _defaultFieldSettings()
	{
		$default_text_values = array(
			'display' => false,
			'height' => false,
			'width' => false
		);
		return array(
			"fields" => array(
				'caption' => $default_text_values,
				'credit' => $default_text_values,
				'subject' => $default_text_values,
				'sizes' => array(
					'display' => false,
					'options' => "sm : Small\nmed : Medium\nlrg : Large",
				),
				'styles' => array(
					'display' => false,
					'options' => "figure-a : Left\nfigure-b : Center\nfigure-c : Right"
				),
			),
			"layout" => "horizontal"
		);
	}

	/**
	 * Prepares settings array for fields and matrix cells
	 * 
	 * @access public
	 * @param $settings array The field / cell settings
	 * @return array Labels and form inputs
	 */
	private function _displaySettings($settings)
	{
		$r = array();

		//print_r($settings);

		if(!isset($this->EE->cache[__CLASS__]['resources_loaded']))
		{
			$theme_url = $this->_getThemeUrl();
			$this->EE->cp->add_to_head("<link rel='stylesheet' href='{$theme_url}/styles/admin.css' type='text/css' media='screen' charset='utf-8' />");
			$this->EE->cp->add_to_foot("<script src='{$theme_url}/scripts/admin.js' type='text/javascript' charset='utf-8'></script>");
			$this->EE->cache[__CLASS__]['resources_loaded'] = true;
		}

		// Load this manually
		$file_attributes = $this->EE->load->_ci_load(array(
			'_ci_vars' => array(
				'settings' => $settings,
				'text_fields' => array('caption','credit','subject'),
				'select_fields' => array('sizes','styles'),
				'input_name' => $this->field_name
			),
			'_ci_path' => PATH_THIRD . 'nsm_file/views/fieldtype/_file_attributes.php',
			'_ci_return' => true
		));

		$r[] = array("
			<label class='low-label'>File Attributes</label>
			<div class='low-var-notes nsm_file-notes'>
				Put each Sizes and Styles options on a seperate line. <br/><br />Ex1: <code>Label</code><br />Ex2: <code>name : Label</code>
			</div>
		", $file_attributes);

		/* Field Layout */
		$layout = form_dropdown(
							__CLASS__ . "[layout]", 
							array(
								'horizontal' => 'Horizontal',
							    'vertical' => 'Vertical'
							),
							$settings['layout']
						);

		$r[] = array("<label class='low-label'>Layout</label>", $layout);

		return $r;
	}

	/**
	 * Display a global settings page. The current available global settings are in $this->settings.
	 *
	 * @access public
	 * @return string The global settings form HTML
	 */
	public function display_global_settings()
	{
		return "Global settings";
	}
	
	/**
	 * Display the settings form for each custom field
	 * 
	 * @access public
	 * @param $data mixed Not sure what this data is yet :S
	 * @return string Override the field custom settings with custom html
	 */
	public function display_settings($field_settings)
	{
		$field_settings = $this->_merge_recursive($this->_defaultFieldSettings(), $field_settings);
		$rows = $this->_displaySettings($field_settings);

		// add the rows
		foreach ($rows as $row)
			$this->EE->table->add_row($row[0], $row[1]);
	}

	/**
	 * Display Cell Settings - MATRIX
	 * 
	 * @access public
	 * @param $cell_settings array The cell settings
	 * @return array Label and form inputs
	 */
	public function display_cell_settings($cell_settings)
	{
		$cell_settings = $this->_merge_recursive($this->_defaultFieldSettings(), $cell_settings);
		$rows = $this->_displaySettings($cell_settings);
		return $rows;
	}

	/**
	 * Display Variable Settings
	 * 
	 * @access public
	 * @param $var_settings array The variable settings
	 * @return array Label and form inputs
	public function display_var_settings($var_settings)
	{
		$var_settings = $this->_merge_recursive($this->_defaultFieldSettings(), $var_settings);
		return $this->_displaySettings($var_settings);
	}
	 */


	/**
	 * Save the global settngs
	 *
	 * @access public
	 * @return array The new global settings
	 */
	 public function save_global_settings()
	 {
	 	$new_settings = $this->_merge_recursive($this->settings, $this->EE->input->post(__CLASS__));
	 	return $new_settings;
	 }

	/**
	 * Save the custom field settings
	 * 
	 * @param $data array The submitted post data.
	 * @return array Field settings
	 */
	public function save_settings($data)
	{
		$field_settings = $this->EE->input->post(__CLASS__);

		// Add an empty array if no fields are selected
		// if(!isset($field_settings['fields']))
		//	$field_settings['fields'] = array();

		// Force formatting
		// $field_settings['field_fmt'] = 'none';
		// $field_settings['field_show_fmt'] = 'n';
		// $field_settings['field_type'] = $this->addon_id;

		// Cleanup
		unset($_POST[__CLASS__]);
		foreach (array_keys($field_settings) as $setting)
		{
			if (isset($_POST[__CLASS__."_".$setting]))
				unset($_POST[__CLASS__."_".$setting]);
		}

		return $field_settings;
	}

	/**
	 * Process the cell settings before saving
	 * 
	 * @access public
	 * @param $col_settings array The settings for the column
	 * @return array The new settings
	 */
	public function save_cell_settings($col_settings)
	{
		$col_settings = $col_settings[__CLASS__];
		return $col_settings;
	}

	/**
	 * Save the Low variable settings
	 * 
	 * @access public
	 * @param $var_settings The variable settings
	 * @see http://loweblog.com/software/low-variables/docs/fieldtype-bridge/
	 */
	public function save_var_settings($var_settings)
	{
		return $this->save_settings();
	}

	/**
	 * Get the current themes URL from the theme folder + / + the addon id
	 * 
	 * @access private
	 * @return string The theme URL
	 */
	private function _getThemeUrl()
	{
		$EE =& get_instance();
		if(!isset($EE->session->cache[$this->addon_id]['theme_url']))
		{
			$theme_url = $EE->config->item('theme_folder_url');
			if (substr($theme_url, -1) != '/') $theme_url .= '/';
			$theme_url .= "third_party/" . $this->addon_id;
			$EE->session->cache[$this->addon_id]['theme_url'] = $theme_url;
		}
		return $EE->session->cache[$this->addon_id]['theme_url'];
	}

	/**
	 * Merges any number of arrays / parameters recursively, replacing 
	 * entries with string keys with values from latter arrays. 
	 * If the entry or the next value to be assigned is an array, then it 
	 * automagically treats both arguments as an array.
	 * Numeric entries are appended, not replaced, but only if they are 
	 * unique
	 *
	 * PHP's array_merge_recursive does indeed merge arrays, but it converts
	 * values with duplicate keys to arrays rather than overwriting the value 
	 * in the first array with the duplicate value in the second array, as 
	 * array_merge does. e.g., with array_merge_recursive, this happens 
	 * (documented behavior):
	 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
	 *     returns: array('key' => array('org value', 'new value'));
	 * 
	 * calling: result = array_merge_recursive_distinct(a1, a2, ... aN)
	 *
	 * @author <mark dot roduner at gmail dot com>
	 * @link http://www.php.net/manual/en/function.array-merge-recursive.php#96201
	 * @access private
	 * @param $array1, [$array2, $array3, ...]
	 * @return array Resulting array, once all have been merged
	 */
	 private function _merge_recursive () {
		$arrays = func_get_args();
		$base = array_shift($arrays);
		if(!is_array($base)) $base = empty($base) ? array() : array($base);
	
		foreach($arrays as $append) {
	
			if(!is_array($append)) $append = array($append);
	
			foreach($append as $key => $value) {
				if(!array_key_exists($key, $base) and !is_numeric($key)) {
					$base[$key] = $append[$key];
					continue;
				}
				if(is_array($value) or is_array($base[$key])) {
					$base[$key] = $this->_merge_recursive($base[$key], $append[$key]);
				} else if(is_numeric($key)) {
					if(!in_array($value, $base)) $base[] = $value;
				} else {
					$base[$key] = $value;
				}
			}
		}
	
		return $base;
	}


}
//END CLASS