<?php

namespace Tdatabass\Includes;

defined('ABSPATH') || die();

class Tdatabass_Base {
	public function __construct() {
		add_filter('post_row_actions', [$this, 'remove_view_option'], 10, 2);
	}

	public function set_data($table_name, $data, $date_type, $array) {
		if (isset($_POST[$data])) {
			$existingData = get_option($table_name, []);
			if (!isset($existingData[$array])) {
				$existingData[$array] = [];
			}

			$existingData[$array][$date_type] = $_POST[$data];
			update_option($table_name, $existingData);
		}
	}

	private  function remove_view_option($actions, $post) {
		// Replace 'your_custom_post_type' with the slug of your custom post type
		if ($post->post_type == 'srv_booking') {
			unset($actions['view']);
		}

		return $actions;
	}

	public function get_data($table_name, $array_key, $date_type) {
		$array = get_option($table_name);

		if (isset($array[$array_key])) {
			if (isset($array[$array_key][$date_type])) {
				return $array[$array_key][$date_type];
			} else {
				return '';
			}
		} else {
			return '';
		}
	}
}
