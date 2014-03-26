<?php
require_once('../config.php');

function jsonError($values=null) {
	$send['error'] = 1;
	if(empty($values))
		$send['text'] = utf8('��������� ����������� ������.');
	elseif(is_array($values))
		$send += $values;
	else
		$send['text'] = utf8($values);
	die(json_encode($send));
}//jsonError()
function jsonSuccess($send=array()) {
	$send['success'] = 1;
	die(json_encode($send));
}//jsonSuccess()
