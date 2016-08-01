<?php
const CATEGORY_SEPARATOR = '/';
const CATEGORY_ERROR_NOT_EXISTS = 1000;

/**
 * 
 * @param integer $code
 * @param string $message
 * @return boolean 永远返回false
 */
function category_setError($code = 0, $message = '')
{
	global $category_lastErrorCode, $category_lastErrorMessage;
	$category_lastErrorCode = $code;
	$category_lastErrorMessage = $message;
	return false;
}

/**
 * 
 * @param integer $code 输出错误代码a
 * @return string 返回错误消息
 */
function category_getLastError(&$code = null)
{
	global $category_lastErrorCode, $category_lastErrorMessage;
	$code = $category_lastErrorCode;
	return $category_lastErrorMessage;
}

/**
 * 获取分类编号
 * 
 * 如果分类不存在则创建一个。
 * 
 * @param string $fullName
 * @return integer|boolean 返回分类编号，失败的话返回 false
 */
function category_get($fullName, &$leftVal = null, &$rightVal = null, &$root = null, &$depth = null)
{
	if (empty($fullName)) {
		return category_setError(0, '分类名称不能为空');
	}
	$pdo = db_open();
	$stm = $pdo->prepare("SELECT * FROM category WHERE full_name=?");
	$stm->bindValue(1, $fullName);
	$stm->execute();
	$result = $stm->fetch();
	if ($result) {
		$leftVal = $result['left_value'];
		$rightVal = $result['right_value'];
		$depth = $result['depth'];
		$root = $result['root'];
		return $result['id'];
	}
	$pos = strrpos($fullName, CATEGORY_SEPARATOR);
	if ($pos === false) {
		$parent = $depth = $root = 0;
		$name = $fullName;
		list($maxRightVal) = $pdo->query("SELECT MAX(right_value) FROM category")->fetch();
		$leftVal = $maxRightVal + 1;
		$rightVal = $maxRightVal + 2;
	} else {
		$parent = category_get(substr($fullName, 0, $pos), $parentLeftVal, $parentRightVal, $root, $depth);
		if ($parent === false) {
			return false;
		}
		$depth ++;
		$name = substr($fullName, $pos + 1);
		$leftVal = $parentRightVal;
		$rightVal = $parentRightVal + 1;
		if (false === $pdo->exec("UPDATE category SET left_value = left_value + 2 WHERE left_value>=$leftVal") ||
			false === $pdo->exec("UPDATE category SET right_value = right_value + 2 WHERE right_value>=$leftVal")
		) {
			list(, $code, $message) = $pdo->errorInfo();
			return category_setError($code, $message);
		}
	}
	$stm = $pdo->prepare("INSERT INTO category (name,full_name,parent,left_value,right_value,root,depth,type,items) VALUES (?,?,?,?,?,?,?,0,0)");
	$stm->bindValue(1, $name);
	$stm->bindValue(2, $fullName);
	$stm->bindValue(3, $parent);
	$stm->bindValue(4, $leftVal);
	$stm->bindValue(5, $rightVal);
	$stm->bindValue(6, $root);
	$stm->bindValue(7, $depth);
	if ($stm->execute()) {
		return $pdo->lastInsertId();
	} else {
		list(, $code, $message) = $pdo->errorInfo();
		return category_setError($code, $message);
	}
}

function category_removeFromArchive($id, $fullName)
{
	$pdo = db_open();
	$stm = $pdo->prepare("SELECT `archive`.id,`archive`.`categories` FROM `archive` JOIN `assn_category_archive` ON `archive`.id=`assn_category_archive`.archive_id WHERE `assn_category_archive`.category_id=?");
	$stm->bindValue(1, $id);
	if (false === $stm->execute()) {
		list(, $code, $message) = $pdo->errorInfo();
		return category_setError($code, $message);
	}
	foreach ($stm->fetchAll() as $row) {
		$categories = explode("\n", $row['categories']);
		foreach ($categories as $k => $v) {
			if ($v === $fullName) {
				unset($categories[$k]);
				break;
			}
		}
		$categories = implode("\n", $categories);
		$categories = $pdo->quote($categories);
		if (false === $pdo->exec("UPDATE `archive` SET `categories`=$categories WHERE id={$row['id']}")) {
			list(, $code, $message) = $pdo->errorInfo();
			return category_setError($code, $message);
		}
	}
	
	$stm = $pdo->prepare("DELETE FROM `assn_category_archive` WHERE category_id=?");
	$stm->bindValue(1, $id);
	if (false === $stm->execute()) {
		list(, $code, $message) = $pdo->errorInfo();
		return category_setError($code, $message);
	}
	return true;
}

function category_delete($id)
{
	$pdo = db_open();
	$stm = $pdo->prepare("SELECT * FROM `category` WHERE id=?");
	$stm->bindValue(1, $id);
	if (false === $stm->execute()) {
		list(, $code, $message) = $pdo->errorInfo();
		return category_setError($code, $message);
	}
	$category = $stm->fetch();
	$left = $category['left_value'];
	$right = $category['right_value'];
	$distance = $right - $left + 1;
	$stm1 = $pdo->prepare("SELECT * FROM `category` WHERE left_value>=? AND right_value<=?");
	$stm1->bindValue(1, $left);
	$stm1->bindValue(2, $right);
	if (false === $stm1->execute()) {
		list(, $code, $message) = $pdo->errorInfo();
		return category_setError($code, $message);
	}
	foreach ($stm1->fetchAll() as $row) {
		if (false === category_removeFromArchive($row['id'], $row['full_name']))
			return false;
	}
	if (false === $pdo->exec("DELETE FROM `category` WHERE left_value>=$left AND right_value<=$right")) {
		list(, $code, $message) = $pdo->errorInfo();
		return category_setError($code, $message);
	}
	if (false === $pdo->exec("UPDATE `category` SET left_value=left_value-$distance WHERE left_value>$left")) {
		list(, $code, $message) = $pdo->errorInfo();
		return category_setError($code, $message);
	}
	if (false === $pdo->exec("UPDATE `category` SET right_value=right_value-$distance WHERE right_value>$right")) {
		list(, $code, $message) = $pdo->errorInfo();
		return category_setError($code, $message);
	}
	return true;
}

function category_rename($id, $newFullName)
{
	$pdo = db_open();
}

function category_find($name)
{
	category_setError();
	$pdo = db_open();
}

