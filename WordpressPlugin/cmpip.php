<?php
/*
Plugin Name: CMPIP
Plugin URI: http://cmpip.org
Description: CMPIP-related functionality
Version: 2013.07.10
Author: Sergej
Author URI: http://cmpip.org
License: Internal
*/

$FIELDS = array(
	array('passport_first_name', 'Имя в загранпаспорте', 'text', 'required' => true),
	array('passport_last_name', 'Фамилия в загранпаспорте', 'text', 'required' => true),
	array('sex', 'Пол', 'choice',
		'required' => true,
		'choices' => array('M' => 'Мужской', 'F' => 'Женский'))
);

add_action('signup_extra_fields', 'cmpip_signup_extra_fields');
function cmpip_signup_extra_fields($errors) {
	global $FIELDS;

	foreach ($FIELDS as $field) {
		list($key, $label, $type) = $field;
		$value = isset($_POST[$key]) ? $_POST[$key] : '';
		
		if ($type == 'text') {
			echo '<label for="' . $key . '">' . __($label, 'cmpip fields') . '</label>';
			if ($errmsg = $errors->get_error_message($key)) {
				echo '<p class="error">'.$errmsg.'</p>';
			}
			echo '<input type="text" id="' . $key . '" name="' . $key . '" value="' . esc_attr($value) . '" /><br />';
			#_e( '(Must be at least 4 characters, letters and numbers only.)' );
		} elseif ($type == 'choice') {
			$choices = $field['choices'];
			echo '<label for="' . $key . '">' . __($label, 'cmpip fields') . '</label>';
			if ($errmsg = $errors->get_error_message($key)) {
				echo '<p class="error">'.$errmsg.'</p>';
			}

			foreach ($choices as $v => $l) {
				echo '<input type="radio" name="' . $key . '" value="' . esc_attr($v) . '"' . ($value == $v ? ' checked="checked"' : '') . '/>&nbsp;';
				echo __($l, 'cmpip fields') . '<br />';
			}
		}
	}
}

add_filter('wpmu_validate_user_signup', 'cmpip_validate_user_signup');
function cmpip_validate_user_signup($result) {
	global $FIELDS;

	if ($_POST['stage'] == 'validate-user-signup') {
		foreach ($FIELDS as $field) {
			$value = $_POST[$field[0]];
			if (empty($value)) {
				if ($field['required']) {
					$result['errors']->add(
						$field[0],
						sprintf(__('Пожалуйста, заполните поле %s', 'cmpip'),
							__($field[1], 'cmpip fields')));
				}
			} else if (isset($field['choices']) && !array_key_exists($value, $field['choices'])) {
				$result['errors']->add(
					$field[0],
					sprintf(__('Пожалуйста, выберите одно из значений поля %s', 'cmpip'),
						__($field[1], 'cmpip fields')));
			}
		}
	}

	return $result;
}

add_filter('add_signup_meta', 'cmpip_add_signup_meta');
function cmpip_add_signup_meta($meta) {
	global $FIELDS;

	foreach ($FIELDS as $field) {
		$key = $field[0];
		$value = $_POST[$key];
		
		if (isset($value)) {
			$meta[$key] = $value;
		}
	}

	return $meta;
}

add_action('wpmu_activate_user', 'cmpip_activate_user', 10, 3);
function cmpip_activate_user($user_id, $password, $meta) {
	global $FIELDS;

	foreach ($FIELDS as $field) {
		$key = $field[0];
		$value = $meta[$key];
		
		if (isset($value)) {
			update_user_meta($user_id, $key, $value);
		}
	}
    return $user_id;
}
