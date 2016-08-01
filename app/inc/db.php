<?php
/**
 * 
 * @return PDO
 */
function db_open()
{
	global $db_instance;
	if (empty($db_instance)) {
		global $db_file;
		$db_instance = new PDO("sqlite:{$db_file}");
	}
    return $db_instance;
}

/**
 * 
 * @param string $file
 * @return boolean
 */
function db_set($file)
{
	global $db_file;
	$realFile = realpath($file);
	//var_dump($realFile);
	if (false === $realFile) {
		return false;
	}
	$db_file = $realFile;
	return true;
}

/**
 * @return string|null
 */
function db_get()
{
	global $db_file;
	return $db_file;
}
