<?php

$navbar_activity = '主页';

function navbar_set_activity($name)
{
	global $navbar_activity;
	$navbar_activity = $name;
}

function navbar_get_activity($name)
{
	global $navbar_activity;
	if ($navbar_activity === $name) return 'active';
}
