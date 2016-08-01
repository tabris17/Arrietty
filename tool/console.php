<?php
function console_write($str)
{
	echo mb_convert_encoding($str, 'GBK', 'UTF-8');
}

