<?php

function request_get($name, $type = null, $default_value = false)
{
	if (!isset($_GET[$name])) {
		return $default_value;
	}
	
	$value = $_GET[$name];
	switch ($type) {
		case 'int':
			if ($value != (int)$value) {
				return $default_value;
			}
			break;
		case 'array':
			if (!is_array($value)) {
				return $default_value;
			}
			break;
	}
	return $value;
}


function request_post($name, $type = null, $default_value = false)
{
	if (!isset($_POST[$name])) {
		return $default_value;
	}

	$value = $_POST[$name];
	switch ($type) {
		case 'int':
			if ($value != (int)$value) {
				return $default_value;
			}
			break;
		case 'array':
			if (!is_array($value)) {
				return $default_value;
			}
			break;
	}
	return $value;
}
