<?php
require_once('config.php');

switch(@$_POST['op']) {
	case 'scanner_word':
		$word = win1251(htmlspecialchars(trim($_POST['word'])));
		if(empty($word))
			jsonError();
		if(!preg_match(REGEXP_WORD, $word))
			jsonError();
		$sql = "SELECT `id`
				FROM `zayav`
				WHERE `ws_id`=".WS_ID."
				  AND (`imei`='".$word."'
				   OR `serial`='".$word."')";
		$id = query_value($sql);
		$send = array();
		if($id)
			$send['zayav_id'] = $id;
		elseif(preg_match(REGEXP_NUMERIC, $word) && strlen($word) == 15)
			$send['imei'] = 1;
		jsonSuccess($send);
		break;

	case 'base_device_add':
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();

		$sql = "SELECT `name` FROM `base_device` WHERE `name`='".addslashes($name)."'";
		if(mysql_num_rows(query($sql)))
			jsonError();

		$sort = query_value("SELECT IFNULL(MAX(`sort`)+1,0) FROM `base_device`");
		$sql = "INSERT INTO `base_device` (
				`name`,
				`name_rod`,
				`name_mn`,
				`sort`,
				`viewer_id_add`
			) values (
				'".addslashes($name)."',
				'".addslashes($name)."',
				'".addslashes($name)."',
				".$sort.",
				".VIEWER_ID."
			)";
		query($sql);
		$send['id'] = mysql_insert_id();

		$sql = "UPDATE `workshop` SET `devs`=CONCAT(`devs`,',".$send['id']."') WHERE `id`=".WS_ID;
		query($sql);

		GvaluesCreate();
		xcache_unset(CACHE_PREFIX.'device_name');
		xcache_unset(CACHE_PREFIX.'setup_global');
		xcache_unset(CACHE_PREFIX.'workshop_'.WS_ID);
		jsonSuccess($send);
		break;
	case 'base_vendor_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['device_id']))
			jsonError();

		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();

		$device_id = intval($_POST['device_id']);
		$sql = "SELECT `name` FROM `base_vendor` WHERE `device_id`=".$device_id." AND `name`='".addslashes($name)."'";
		if(mysql_num_rows(query($sql)))
			jsonError();

		$sort = query_value("SELECT IFNULL(MAX(`sort`)+1,0) FROM `base_vendor` WHERE `device_id`=".$device_id);
		$sql = "INSERT INTO `base_vendor` (
				`device_id`,
				`name`,
				`sort`,
				`viewer_id_add`
			) values (
				".$device_id.",
				'".addslashes($name)."',
				".$sort.",
				".VIEWER_ID."
			)";
		query($sql);
		$send['id'] = mysql_insert_id();

		GvaluesCreate();
		xcache_unset(CACHE_PREFIX.'vendor_name');
		xcache_unset(CACHE_PREFIX.'setup_global');
		xcache_unset(CACHE_PREFIX.'workshop_'.WS_ID);
		jsonSuccess($send);
		break;
	case 'base_model_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['device_id']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['vendor_id']))
			jsonError();

		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();

		$device_id = intval($_POST['device_id']);
		$vendor_id = intval($_POST['vendor_id']);
		$sql = "SELECT `name`
				FROM `base_model`
				WHERE `device_id`=".$device_id."
				  AND `vendor_id`=".$vendor_id."
				  AND `name`='".addslashes($name)."'";
		if(mysql_num_rows(query($sql)))
			jsonError();

		$sql = "INSERT INTO `base_model` (
				`device_id`,
				`vendor_id`,
				`name`,
				`viewer_id_add`
			) values (
				".$device_id.",
				".$vendor_id.",
				'".addslashes($name)."',
				".VIEWER_ID."
			)";
		query($sql);
		$send['id'] = mysql_insert_id();

		GvaluesCreate();
		xcache_unset(CACHE_PREFIX.'model_name_count');
		xcache_unset(CACHE_PREFIX.'setup_global');
		xcache_unset(CACHE_PREFIX.'workshop_'.WS_ID);
		jsonSuccess($send);
		break;

	case 'client_sel':
		$send['spisok'] = array();
		if(!empty($_POST['val']) && !preg_match(REGEXP_WORDFIND, win1251($_POST['val'])))
			jsonSuccess($send);
		if(!preg_match(REGEXP_NUMERIC, $_POST['client_id']))
			jsonSuccess($send);
		$val = win1251($_POST['val']);
		$client_id = intval($_POST['client_id']);
		$sql = "SELECT *
				FROM `client`
				WHERE `ws_id`=".WS_ID."
				  AND `deleted`=0".
					(!empty($val) ? " AND (`fio` LIKE '%".$val."%' OR `telefon` LIKE '%".$val."%')" : '').
					($client_id > 0 ? " AND `id`<=".$client_id : '')."
				ORDER BY `id` DESC
				LIMIT 50";
		$q = query($sql);
		while($r = mysql_fetch_assoc($q)) {
			$unit = array(
				'uid' => $r['id'],
				'title' => utf8(htmlspecialchars_decode($r['fio']))
			);
			if($r['telefon'])
				$unit['content'] = utf8($r['fio'].'<span>'.$r['telefon'].'</span>');
			$send['spisok'][] = $unit;
		}
		jsonSuccess($send);
		break;
	case 'client_add':
		$fio = win1251(htmlspecialchars(trim($_POST['fio'])));
		$telefon = win1251(htmlspecialchars(trim($_POST['telefon'])));
		if(empty($fio))
			jsonError();
		$sql = "INSERT INTO `client` (
					`ws_id`,
					`fio`,
					`telefon`,
					`viewer_id_add`
				) VALUES (
					".WS_ID.",
					'".addslashes($fio)."',
					'".addslashes($telefon)."',
					".VIEWER_ID."
				)";
		query($sql);
		$send = array(
			'uid' => mysql_insert_id(),
			'title' => utf8($fio),
			'content' => utf8($fio.'<span>'.$telefon.'</span>')
		);
		history_insert(array(
			'type' => 3,
			'client_id' => $send['uid']
		));
		jsonSuccess($send);
		break;
	case 'client_spisok':
		$filter = clientFilter($_POST);
		$send = client_data(1, $filter);
		$send['all'] = utf8(client_count($send['all'], $filter['dolg']));
		$send['spisok'] = utf8($send['spisok']);
		jsonSuccess($send);
		break;
	case 'client_next':
		if(!preg_match(REGEXP_NUMERIC, $_POST['page']))
			jsonError();
		$send = client_data(intval($_POST['page']), clientFilter($_POST));
		$send['spisok'] = utf8($send['spisok']);
		jsonSuccess($send);
		break;
	case 'client_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['client_id']) || $_POST['client_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_BOOL, $_POST['join']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['client2']))
			jsonError();
		$client_id = intval($_POST['client_id']);
		$fio = win1251(htmlspecialchars(trim($_POST['fio'])));
		$telefon = win1251(htmlspecialchars(trim($_POST['telefon'])));
		$join = intval($_POST['join']);
		$client2 = intval($_POST['client2']);
		if(empty($fio))
			jsonError();
		$sql = "SELECT * FROM `client` WHERE !`deleted` AND `id`=".$client_id;
		if(!$client = mysql_fetch_assoc(query($sql)))
			jsonError();
		if($join && !$client2)
			jsonError();
		if($join && !query_value("SELECT * FROM `client` WHERE !`deleted` AND `id`=".$client2))
			jsonError();
		if($join && $client_id == $client2)
			jsonError();
		query("UPDATE `client`
			   SET `fio`='".addslashes($fio)."',`telefon`='".addslashes($telefon)."'
			   WHERE `id`=".$client_id);
		if($join) {
			query("UPDATE `accrual`	SET `client_id`=".$client_id." WHERE `client_id`=".$client2);
			query("UPDATE `money`	SET `client_id`=".$client_id." WHERE `client_id`=".$client2);
			query("UPDATE `vk_comment` SET `table_id`=".$client_id."  WHERE `table_name`='client' AND `table_id`=".$client2);
			query("UPDATE `zayav`	SET `client_id`=".$client_id." WHERE `client_id`=".$client2);
			query("UPDATE `zp_move`	SET `client_id`=".$client_id." WHERE `client_id`=".$client2);
			query("UPDATE `client`  SET `deleted`=1,`join_id`=".$client_id." WHERE `id`=".$client2);
			clientBalansUpdate($client_id);
			history_insert(array(
				'type' => 11,
				'client_id' => $client_id,
				'value' => _clientLink($client2, 1)
			));
		}

		$changes = '';
		if($client['fio'] != $fio)
			$changes .= '<tr><th>���:<td>'.$client['fio'].'<td>�<td>'.$fio;
		if($client['telefon'] != $telefon)
			$changes .= '<tr><th>���.:<td>'.$client['telefon'].'<td>�<td>'.$telefon;
		if($changes)
			history_insert(array(
				'type' => 10,
				'client_id' => $client_id,
				'value' => '<table>'.$changes.'</table>'
			));

		jsonSuccess();
		break;
	case 'client_zayav_spisok':
		$_POST['limit'] = 10;
		$data = zayav_spisok($_POST);
		if($data['filter']['page'] == 1)
			$send['all'] = utf8($data['result']);
		$send['html'] = utf8($data['spisok']);
		jsonSuccess($send);
		break;

	case 'zayav_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['client']) || !$_POST['client'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['device']) || !$_POST['device'])
			jsonError();
		$client = intval($_POST['client']);
		$device = intval($_POST['device']);
		$vendor = intval($_POST['vendor']);
		$model = intval($_POST['model']);
		if(!empty($_POST['equip'])) {
			$ids = explode(',', $_POST['equip']);
			for($n = 0; $n < count($ids); $n++)
				if(!preg_match(REGEXP_NUMERIC, $ids[$n]))
					jsonError();
		}
		$place = intval($_POST['place']);
		$place_other = $place == 0 ? win1251(htmlspecialchars(trim($_POST['place_other']))) : '';
		$imei = win1251(htmlspecialchars(trim($_POST['imei'])));
		$serial = win1251(htmlspecialchars(trim($_POST['serial'])));
		$color = intval($_POST['color']);
		$color_dop = $color ? intval($_POST['color_dop']) : 0;
		$comm = win1251(htmlspecialchars(trim($_POST['comm'])));
		$reminder = intval($_POST['reminder']);
		$reminder_txt = win1251(htmlspecialchars(trim($_POST['reminder_txt'])));
		$reminder_day = htmlspecialchars(trim($_POST['reminder_day']));
		if($reminder) {
			if(!$reminder_txt)
				jsonError();
			if(!preg_match(REGEXP_DATE, $reminder_day))
				jsonError();
		}
		$modelName = '';
		if($model > 0) {
			$sql = "select `name` FROM `base_model` WHERE `id`=".$model;
			$r = mysql_fetch_assoc(query($sql));
			$modelName = $r['name'];
		}
		$sql = "SELECT IFNULL(MAX(`nomer`),0)+1 AS `nomer` FROM `zayav` WHERE `ws_id`=".WS_ID." LIMIT 1";
		$r = mysql_fetch_assoc(query($sql));
		$nomer = $r['nomer'];

		$sql = "INSERT INTO `zayav` (
					`ws_id`,
					`nomer`,
					`client_id`,

					`base_device_id`,
					`base_vendor_id`,
					`base_model_id`,

					`equip`,
					`imei`,
					`serial`,
					`color_id`,
					`color_dop`,

					`zayav_status`,
					`zayav_status_dtime`,

					`device_status`,
					`device_place`,
					`device_place_other`,

					`viewer_id_add`,
					`find`
				) VALUES (
					".WS_ID.",
					".$nomer.",
					".$client.",

					".$device.",
					".$vendor.",
					".$model.",

					'".$_POST['equip']."',
					'".addslashes($imei)."',
					'".addslashes($serial)."',
					".$color.",
					".$color_dop.",

					1,
					current_timestamp,

					1,
					".addslashes($place).",
					'".$place_other."',

					".VIEWER_ID.",
					'".addslashes($modelName.' '.$imei.' '.$serial)."'
				)";
		query($sql);
		$send['id'] = mysql_insert_id();

		if($comm) {
			$sql = "INSERT INTO `vk_comment` (
						`table_name`,
						`table_id`,
						`txt`,
						`viewer_id_add`
					) VALUES (
						'zayav',
						".$send['id'].",
						'".$comm."',
						".VIEWER_ID."
					)";
			query($sql);
		}

		if($reminder) {
			$sql = "INSERT INTO `reminder` (
				`ws_id`,
				`zayav_id`,
				`txt`,
				`day`,
				`history`,
				`viewer_id_add`
			 ) VALUES (
				".WS_ID.",
				".$send['id'].",
				'".$reminder_txt."',
				'".$reminder_day."',
				'".FullDataTime(curTime())." "._viewer(VIEWER_ID, 'name')." ������� ����������� ��� ������.',
				".VIEWER_ID."
			)";
			query($sql);
		}
		history_insert(array(
			'type' => 1,
			'client_id' => $client,
			'zayav_id' => $send['id']
		));
		jsonSuccess($send);
		break;
	case 'model_img_get':
		if(!preg_match(REGEXP_NUMERIC, $_POST['model_id']))
			jsonError();
		$send['img'] = _imageGet(array(
			'owner' => 'dev'.intval($_POST['model_id']),
			'view' => 1
		));
		jsonSuccess($send);
		break;
	case 'equip_check_get':
		if(!preg_match(REGEXP_NUMERIC, $_POST['device_id']) && $_POST['device_id'] == 0)
			jsonError();
		$device_id = intval($_POST['device_id']);
		$send['spisok'] = utf8(devEquipCheck($device_id));
		jsonSuccess($send);
		break;
	case 'zayav_spisok':
		$_POST['find'] = win1251($_POST['find']);
		$data = zayav_spisok($_POST);
		if($data['filter']['page'] == 1)
			$send['all'] = utf8($data['result']);
		$send['html'] = utf8($data['spisok']);
		jsonSuccess($send);
		break;
	case 'zayav_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zayav_id']) && !$_POST['zayav_id'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['client_id']) && !$_POST['client_id'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['device']) && !$_POST['device'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['vendor']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['model']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['color_id']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['color_dop']))
			jsonError();
		$zayav_id = intval($_POST['zayav_id']);
		$client_id = intval($_POST['client_id']);
		$device = intval($_POST['device']);
		$vendor = intval($_POST['vendor']);
		$model = intval($_POST['model']);
		$imei = win1251(htmlspecialchars(trim($_POST['imei'])));
		$serial = win1251(htmlspecialchars(trim($_POST['serial'])));
		$color_id = intval($_POST['color_id']);
		$color_dop = $color_id ? intval($_POST['color_dop']) : 0;
		if(!empty($_POST['equip'])) {
			$ids = explode(',', $_POST['equip']);
			for($n = 0; $n < count($ids); $n++)
				if(!preg_match(REGEXP_NUMERIC, $ids[$n]))
					jsonError();
		}
		$equip = $_POST['equip'];

		$sql = "SELECT * FROM `zayav` WHERE `ws_id`=".WS_ID." AND `id`=".$zayav_id." LIMIT 1";
		if(!$z = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `zayav` SET
					`client_id`=".$client_id.",
					`base_device_id`=".$device.",
					`base_vendor_id`=".$vendor.",
					`base_model_id`=".$model.",
					`imei`='".addslashes($imei)."',
					`serial`='".addslashes($serial)."',
					`color_id`=".$color_id.",
					`color_dop`=".$color_dop.",
					`equip`='".$equip."',
					`find`='".addslashes(_modelName($model).' '.$imei.' '.$serial)."'
				WHERE `id`=".$zayav_id;
		query($sql);

		if($z['client_id'] != $client_id) {
			$sql = "UPDATE `accrual`
					SET `client_id`=".$client_id."
					WHERE `ws_id`=".WS_ID."
					  AND `zayav_id`=".$zayav_id."
					  AND `client_id`=".$z['client_id'];
			query($sql);
			$sql = "UPDATE `money`
					SET `client_id`=".$client_id."
					WHERE `ws_id`=".WS_ID."
					  AND `zayav_id`=".$zayav_id."
					  AND `client_id`=".$z['client_id'];
			query($sql);
			clientBalansUpdate($z['client_id']);
			clientBalansUpdate($client_id);
		}

		$changes = '';
		if($z['client_id'] != $client_id)
			$changes .= '<tr><th>������:<td>'._clientLink($z['client_id']).'<td>�<td>'._clientLink($client_id);
		if(   $z['base_device_id'] != $device
		   || $z['base_vendor_id'] != $vendor
		   || $z['base_model_id'] != $model) {
			$old = _deviceName($z['base_device_id'])._vendorName($z['base_vendor_id'])._modelName($z['base_model_id']);
			$new = _deviceName($device)._vendorName($vendor)._modelName($model);
			$changes .= '<tr><th>����������:<td>'.$old.'<td>�<td>'.$new;
		}
		if($z['imei'] != $imei)
			$changes .= '<tr><th>imei:<td>'.$z['imei'].'<td>�<td>'.$imei;
		if($z['serial'] != $serial)
			$changes .= '<tr><th>Serial:<td>'.$z['serial'].'<td>�<td>'.$serial;
		if($z['color_id'] != $color_id || $z['color_dop'] != $color_dop)
			$changes .= '<tr><th>����:<td>'._color($z['color_id'], $z['color_dop']).'<td>�<td>'._color($color_id, $color_dop);
		if($z['equip'] != $equip)
			$changes .= '<tr><th>��������:<td>'.zayavEquipSpisok($z['equip']).'<td>�<td>'.zayavEquipSpisok($equip);
		if($changes)
			history_insert(array(
				'type' => 7,
				'client_id' => $z['client_id'],
				'zayav_id' => $zayav_id,
				'value' => '<table>'.$changes.'</table>'
			));

		jsonSuccess();
		break;
	case 'zayav_delete':
		if(empty($_POST['zayav_id']) || !preg_match(REGEXP_NUMERIC, $_POST['zayav_id']))
			jsonError();

		$zayav_id = intval($_POST['zayav_id']);
		$sql = "SELECT * FROM `zayav` WHERE `ws_id`=".WS_ID." AND !`deleted` AND `id`=".$zayav_id;
		if(!$z = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "SELECT COUNT(`sum`)
				FROM `accrual`
				WHERE `ws_id`=".WS_ID."
				  AND !`deleted`
				  AND `zayav_id`=".$zayav_id;
		if(query_value($sql))
			jsonError();

		$sql = "SELECT COUNT(`sum`)
				FROM `money`
				WHERE `ws_id`=".WS_ID."
				  AND !`deleted`
				  AND `sum`>0
				  AND `zayav_id`=".$zayav_id;
		if(query_value($sql))
			jsonError();

		query("UPDATE `zayav` SET `deleted`=1 WHERE `id`=".$zayav_id);
		query("UPDATE `reminder` SET `status`=0 WHERE `status`=1 AND `zayav_id`=".$zayav_id);

		history_insert(array(
			'type' => 2,
			'client_id' => $z['client_id'],
			'zayav_id' => $zayav_id
		));

		$send['client_id'] = $z['client_id'];
		jsonSuccess($send);
		break;
	case 'zayav_status_place':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zayav_id']) && $_POST['zayav_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['zayav_status']) && $_POST['zayav_status'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['dev_status']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['dev_place']))
			jsonError();
		$zayav_id = intval($_POST['zayav_id']);
		$zayav_status = intval($_POST['zayav_status']);
		$dev_status = intval($_POST['dev_status']);
		$dev_place = intval($_POST['dev_place']);
		$place_other = $dev_place == 0 ? win1251(htmlspecialchars(trim($_POST['place_other']))) : '';
		if($dev_place == 0 && !$place_other)
			jsonError();

		$sql = "SELECT * FROM `zayav` WHERE `ws_id`=".WS_ID." AND !`deleted` AND `id`=".$zayav_id." LIMIT 1";
		if(!$z = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `zayav`
				SET `device_status`=".$dev_status.",
					`device_place`=".$dev_place.",
					`device_place_other`='".$place_other."'
					".($z['zayav_status'] != $zayav_status ? ",`zayav_status`=".$zayav_status.",`zayav_status_dtime`=CURRENT_TIMESTAMP" : '')."
				WHERE `id`=".$zayav_id;
		query($sql);

		$send['z_status'] = _zayavStatus($zayav_status);
		$send['z_status']['name'] = utf8($send['z_status']['name']);
		$send['z_status']['dtime'] = utf8(FullDataTime($z['zayav_status_dtime'], 1));
		$send['dev_place'] = utf8($dev_place > 0 ? _devPlace($dev_place) : $place_other);
		$send['dev_status'] = utf8(_devStatus($dev_status));

		if($z['zayav_status'] != $zayav_status) {
			history_insert(array(
				'type' => 4,
				'client_id' => $z['client_id'],
				'zayav_id' => $zayav_id,
				'value' => $zayav_status,
				'value1' => $z['zayav_status']
			));
			$send['z_status']['dtime'] = utf8(FullDataTime(curTime(), 1));
		}
		jsonSuccess($send);
		break;
	case 'zayav_accrual_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zayav_id']) || !$_POST['zayav_id'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['sum']) || !$_POST['sum'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['status']) || !$_POST['status'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['dev_status']))
			jsonError();
		if(!preg_match(REGEXP_BOOL, $_POST['remind']))
			jsonError();
		$remind = intval($_POST['remind']);
		$remind_txt = win1251(htmlspecialchars(trim($_POST['remind_txt'])));
		$remind_day = htmlspecialchars(trim($_POST['remind_day']));
		if($remind) {
			if(!$remind_txt)
				jsonError();
			if(!preg_match(REGEXP_DATE, $remind_day))
				jsonError();
		}

		$zayav_id = intval($_POST['zayav_id']);
		$sum = intval($_POST['sum']);
		$prim = win1251(htmlspecialchars(trim($_POST['prim'])));
		$status = intval($_POST['status']);
		$dev_status = intval($_POST['dev_status']);

		$sql = "SELECT *
				FROM `zayav`
				WHERE `ws_id`=".WS_ID."
				  AND !`deleted`
				  AND `id`=".$zayav_id;
		if(!$z = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "INSERT INTO `accrual` (
					`ws_id`,
					`zayav_id`,
					`client_id`,
					`sum`,
					`prim`,
					`viewer_id_add`
				) VALUES (
					".WS_ID.",
					".$zayav_id.",
					".$z['client_id'].",
					".$sum.",
					'".addslashes($prim)."',
					".VIEWER_ID."
				)";
		query($sql);

		clientBalansUpdate($z['client_id']);
		$send = zayavBalansUpdate($zayav_id);


		history_insert(array(
			'type' => 5,
			'client_id' => $z['client_id'],
			'zayav_id' => $zayav_id,
			'value' => $sum
		));

		//���������� ������� ������, ���� ���������
		$sql = "UPDATE `zayav`
				SET `device_status`=".$dev_status."
					".($z['zayav_status'] != $status ? ",`zayav_status`=".$status.",`zayav_status_dtime`=CURRENT_TIMESTAMP" : '')."
				WHERE `ws_id`=".WS_ID."
				  AND `id`=".$zayav_id;
		query($sql);
		if($z['zayav_status'] != $status) {
			history_insert(array(
				'type' => 4,
				'client_id' => $z['client_id'],
				'zayav_id' => $zayav_id,
				'value' => $status,
				'value1' => $z['zayav_status']
			));
			$send['status'] = _zayavStatus($status);
			$send['status']['name'] = utf8($send['status']['name']);
			$send['status']['dtime'] = utf8(FullDataTime(curTime()));
		}

		//�������� �����������, ���� ����
		if($remind) {
			$sql = "INSERT INTO `reminder` (
				`ws_id`,
				`zayav_id`,
				`txt`,
				`day`,
				`history`,
				`viewer_id_add`
			 ) VALUES (
				".WS_ID.",
				".$zayav_id.",
				'".$remind_txt."',
				'".$remind_day."',
				'".FullDataTime(curTime())." "._viewer(VIEWER_ID, 'name')." ������� ����������� ��� �������� ����������.',
				".VIEWER_ID."
			)";
			query($sql);
			$send['remind'] = utf8(remind_spisok(remind_data(1, array('zayav'=>$zayav_id))));
		}

		$send['html'] = utf8(zayav_info_money($zayav_id));
		jsonSuccess($send);
		break;
	case 'zayav_accrual_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);
		$sql = "SELECT * FROM `accrual` WHERE !`deleted` AND `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();
		$sql = "UPDATE `accrual` SET
					`deleted`=1,
					`viewer_id_del`=".VIEWER_ID.",
					`dtime_del`=CURRENT_TIMESTAMP
				WHERE `id`=".$id;
		query($sql);

		clientBalansUpdate($r['client_id']);
		$send = zayavBalansUpdate($r['zayav_id']);

		history_insert(array(
			'type' => 8,
			'client_id' => $r['client_id'],
			'zayav_id' => $r['zayav_id'],
			'value' => $r['sum'],
			'value1' => $r['prim']
		));
		jsonSuccess($send);
		break;
	case 'zayav_accrual_rest':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);
		$sql = "SELECT
		            *,
					'acc' AS `type`
				FROM `accrual`
				WHERE `ws_id`=".WS_ID."
				  AND `deleted`
				  AND `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();
		$sql = "UPDATE `accrual` SET
					`deleted`=0,
					`viewer_id_del`=0,
					`dtime_del`='0000-00-00 00:00:00'
				WHERE `id`=".$id;
		query($sql);

		clientBalansUpdate($r['client_id']);
		$send = zayavBalansUpdate($r['zayav_id']);

		history_insert(array(
			'type' => 27,
			'client_id' => $r['client_id'],
			'zayav_id' => $r['zayav_id'],
			'value' => $r['sum'],
			'value1' => $r['prim']
		));
		$send['html'] = utf8(zayav_accrual_unit($r));
		jsonSuccess($send);
		break;
	case 'zayav_zp_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zayav_id']) || $_POST['zayav_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['name_id']) || $_POST['name_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['color_id']))
			jsonError();
		if(!preg_match(REGEXP_BOOL, $_POST['bu']))
			jsonError();
		$sql = "SELECT
					`id`,
					`base_device_id`,
					`base_vendor_id`,
					`base_model_id`
				FROM `zayav`
				WHERE `ws_id`=".WS_ID."
				  AND `id`=".intval($_POST['zayav_id']);
		if(!$zp = mysql_fetch_assoc(query($sql)))
			jsonError();
		define('MODEL', _vendorName($zp['base_vendor_id'])._modelName($zp['base_model_id']));
		$zp['name_id'] = intval($_POST['name_id']);
		$zp['version'] = win1251(htmlspecialchars(trim($_POST['version'])));
		$zp['bu'] = intval($_POST['bu']);
		$zp['color_id'] = intval($_POST['color_id']);
		zpAddQuery($zp);
		$send['html'] = utf8(zayav_zp($zp));
		jsonSuccess($send);
		break;
	case 'zayav_zp_zakaz':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zayav_id']) || $_POST['zayav_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['zp_id']) || $_POST['zp_id'] == 0)
			jsonError();

		$sql = "SELECT * FROM `zp_catalog` WHERE `id`=".intval($_POST['zp_id']);
		$zp = mysql_fetch_assoc(query($sql));
		$compat_id = $zp['compat_id'] ? $zp['compat_id'] : $zp['id'];

		$sql = "INSERT INTO `zp_zakaz` (
					`ws_id`,
					`zp_id`,
					`zayav_id`,
					`viewer_id_add`
				) VALUES (
					".WS_ID.",
					".$compat_id.",
					".intval($_POST['zayav_id']).",
					".VIEWER_ID."
				)";
		query($sql);
		$send['msg'] = utf8('�������� <b>'._zpName($zp['name_id']).'</b> ��� '._vendorName($zp['base_vendor_id'])._modelName($zp['base_model_id']).' ��������� � ������.');
		jsonSuccess($send);
		break;
	case 'zayav_zp_set':// ��������� �������� �� ������
		if(empty($_POST['zayav_id']) || !preg_match(REGEXP_NUMERIC, $_POST['zayav_id']))
			jsonError();
		if(empty($_POST['zp_id']) || !preg_match(REGEXP_NUMERIC, $_POST['zp_id']))
			jsonError();
		if(!isset($_POST['count']))
			$_POST['count'] = 1;
		if(empty($_POST['count']) || !preg_match(REGEXP_NUMERIC, $_POST['count']))
			jsonError();

		$zayav_id = intval($_POST['zayav_id']);
		$zp_id = _zpCompatId($_POST['zp_id']);
		$count = intval($_POST['count']) * -1;
		$prim = isset($_POST['prim']) ? win1251(htmlspecialchars(trim($_POST['prim']))) : '';

		$sql = "SELECT * FROM `zayav` WHERE `ws_id`=".WS_ID." AND !`deleted` AND `id`=".$zayav_id;
		if(!$z = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "INSERT INTO `zp_move` (
					`ws_id`,
					`zp_id`,
					`count`,
					`type`,
					`zayav_id`,
					`prim`,
					`viewer_id_add`
				) VALUES (
					".WS_ID.",
					".$zp_id.",
					".$count.",
					'set',
					".$zayav_id.",
					'".$prim."',
					".VIEWER_ID."
				)";
		query($sql);

		$count = _zpAvaiSet($zp_id);

		//�������� �� ������ ��������, ����������� � ������
		query("DELETE FROM `zp_zakaz` WHERE `ws_id`=".WS_ID." AND `zayav_id`=".$zayav_id." AND `zp_id`=".$zp_id);

		$parent_id = 0;
		$sql = "SELECT `id`,`parent_id`
				FROM `vk_comment`
				WHERE `table_name`='zayav'
				  AND `table_id`=".$zayav_id."
				  AND `status`=1
				ORDER BY `id` DESC
				LIMIT 1";
		if($r = mysql_fetch_assoc(query($sql)))
			$parent_id = $r['parent_id'] ? $r['parent_id'] : $r['id'];
		$sql = "SELECT * FROM `zp_catalog` WHERE id=".$zp_id." LIMIT 1";
		$zp = mysql_fetch_assoc(query($sql));
		define('MODEL', _vendorName($zp['base_vendor_id'])._modelName($zp['base_model_id']));
		$sql = "INSERT INTO `vk_comment` (
					`table_name`,
					`table_id`,
					`txt`,
					`parent_id`,
					`viewer_id_add`
				) VALUES (
					'zayav',
					".$zayav_id.",
					'".addslashes('��������� ��������: <a href="'.URL.'&p=zp&d=info&id='.$zp_id.'">'._zpName($zp['name_id']).' '.MODEL.'</a>')."',
					".$parent_id.",
					".VIEWER_ID."
				)";
		query($sql);

		history_insert(array(
			'type' => 13,
			'client_id' => $z['client_id'],
			'zayav_id' => $zayav_id,
			'value' => $count,
			'zp_id' => $zp_id
		));

		$zp['avai'] = $count;
		$send['zp_unit'] = utf8(zayav_zp($zp));
		$send['comment'] = utf8(_vkComment('zayav', $zayav_id));
		jsonSuccess($send);
		break;
	case 'zayav_tooltip':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);

		$z = query_assoc("SELECT * FROM `zayav` WHERE `id`=".$id);
		$client = query_assoc("SELECT * FROM `client` WHERE !`deleted` AND `id`=".$z['client_id']);

		$html =
			'<table>'.
				'<tr><td><div class="image">'._zayavImg($z).'</div>'.
					'<td class="inf">'.
						'<div style="background-color:#'._zayavStatusColor($z['zayav_status']).'" '.
							 'class="tstat'._tooltip('������ ������: '._zayavStatusName($z['zayav_status']), -7, 'l').
						'</div>'.
						_deviceName($z['base_device_id']).
						'<div class="tname">'._vendorName($z['base_vendor_id'])._modelName($z['base_model_id']).'</div>'.
						'<table>'.
							'<tr><td class="label top">������:'.
								'<td>'.$client['fio'].
									   ($client['telefon'] ? '<br />'.$client['telefon'] : '').
							'<tr><td class="label">������:'.
								'<td><span class="bl" style=color:#'.($client['balans'] < 0 ? 'A00' : '090').'>'.$client['balans'].'</span>'.
						'</table>'.
			'</table>';

		$send['html'] = utf8($html);
		jsonSuccess($send);
		break;
	case 'zayav_nomer_info'://��������� ������ � ������ �� ������
		if(empty($_POST['nomer']) || !preg_match(REGEXP_NUMERIC, $_POST['nomer']))
			jsonError();
		$nomer = intval($_POST['nomer']);
		$sql = "SELECT *
				FROM `zayav`
				WHERE `ws_id`=".WS_ID."
				  AND `nomer`=".$nomer."
				  AND `zayav_status`
				LIMIT 1";
		if(!$z = mysql_fetch_assoc(query($sql)))
			$send['html'] = '<span class="zayavNomerTab">������ �� �������</span>';
		else
			$send['html'] = '<table class="zayavNomerTab">'.
				'<tr><td>'._zayavImg($z).
					'<td><a href="'.URL.'&p=zayav&d=info&id='.$z['id'].'">'._deviceName($z['base_device_id']).'<br />'.
						   _vendorName($z['base_vendor_id'])._modelName($z['base_model_id']).
						'</a>'.
			'</table>'.
			'<input type="hidden" id="zayavNomerId" value="'.$z['id'].'" />';
		$send['html'] = utf8($send['html']);
		jsonSuccess($send);
		break;

	case 'zp_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['name_id']) || $_POST['name_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['device_id']) || $_POST['device_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['vendor_id']) || $_POST['vendor_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['model_id']) || $_POST['model_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_BOOL, $_POST['bu']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['color_id']))
			jsonError();

		$zp = array(
			'name_id' => intval($_POST['name_id']),
			'base_device_id' => intval($_POST['device_id']),
			'base_vendor_id' => intval($_POST['vendor_id']),
			'base_model_id' => intval($_POST['model_id']),
			'version' => win1251(htmlspecialchars(trim($_POST['version']))),
			'bu' => intval($_POST['bu']),
			'color_id' => intval($_POST['color_id']),
		);
		zpAddQuery($zp);

		jsonSuccess();
		break;
	case 'zp_spisok':
		$_POST['find'] = win1251($_POST['find']);
		$data = zp_spisok($_POST);
		if($data['filter']['page'] == 1)
			$send['all'] = utf8($data['result']);
		$send['html'] = utf8($data['spisok']);
		jsonSuccess($send);
		break;
	case 'zp_avai_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zp_id']) || $_POST['zp_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['count']) || $_POST['count'] == 0)
			jsonError();
		if(!preg_match(REGEXP_CENA, $_POST['cena']))
			jsonError();
		$zp_id = _zpCompatId($_POST['zp_id']);
		$count = intval($_POST['count']);
		$cena = round($_POST['cena'], 2);
		$summa = round($count * $cena, 2);
		$sql = "INSERT INTO `zp_move` (
					`ws_id`,
					`zp_id`,
					`count`,
					`cena`,
					`summa`,
					`viewer_id_add`
				) VALUES (
					".WS_ID.",
					".$zp_id.",
					".$count.",
					'".$cena."',
					'".$summa."',
					".VIEWER_ID."
				)";
		query($sql);
		history_insert(array(
			'type' => 18,
			'zp_id' => $zp_id,
			'value' => $count
		));
		$send['count'] = _zpAvaiSet($zp_id);
		jsonSuccess($send);
		break;
	case 'zp_zakaz_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zp_id']) || $_POST['zp_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['count']))
			jsonError();
		$zp_id = _zpCompatId($_POST['zp_id']);
		$count = intval($_POST['count']);
		$zakazId = query_value("SELECT `id` FROM `zp_zakaz` WHERE `ws_id`=".WS_ID." AND `zp_id`=".$zp_id." AND `zayav_id`=0 LIMIT 1");
		if($count > 0) {
			$sql = "SELECT IFNULL(SUM(`count`),0)
					FROM `zp_zakaz`
					WHERE `ws_id`=".WS_ID."
					  AND `zp_id`=".$zp_id."
					  AND `zayav_id`>0
					LIMIT 1";
			$zakazZayavCount = query_value($sql);
			if($zakazZayavCount)
				$count -= $zakazZayavCount;
		}
		if($count > 0) {
			if($zakazId)
				query("UPDATE `zp_zakaz` SET `count`=".$count." WHERE `id`=".$zakazId);
			else {
				$sql = "INSERT INTO `zp_zakaz` (
							`ws_id`,
							`zp_id`,
							`count`,
							`viewer_id_add`
						) VALUES (
							".WS_ID.",
							".$zp_id.",
							".$count.",
							".VIEWER_ID."
						)";
				query($sql);
			}
		} else
			query("DELETE FROM `zp_zakaz` WHERE `ws_id`=".WS_ID." AND `zp_id`=".$zp_id);
		jsonSuccess();
		break;
	case 'zp_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zp_id']) || $_POST['zp_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['name_id']) || $_POST['name_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['device_id']) || $_POST['device_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['vendor_id']) || $_POST['vendor_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['model_id']) || $_POST['model_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_BOOL, $_POST['bu']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['color_id']))
			jsonError();

		$zp_id = intval($_POST['zp_id']);
		$name_id = intval($_POST['name_id']);
		$device_id = intval($_POST['device_id']);
		$vendor_id = intval($_POST['vendor_id']);
		$model_id = intval($_POST['model_id']);
		$version = win1251(htmlspecialchars(trim($_POST['version'])));
		$bu = intval($_POST['bu']);
		$color_id = intval($_POST['color_id']);

		$sql = "UPDATE `zp_catalog`
				SET `name_id`=".$name_id.",
					`base_device_id`=".$device_id.",
					`base_vendor_id`=".$vendor_id.",
					`base_model_id`=".$model_id.",
					`version`='".$version."',
					`bu`=".$bu.",
					`color_id`=".$color_id."
				WHERE `id`=".$zp_id;
		query($sql);

		$compat_id = _zpCompatId($zp_id);
		if($compat_id != $zp_id) {
			$sql = "UPDATE `zp_catalog`
					SET `name_id`=".$name_id.",
						`version`='".$version."',
						`bu`=".$bu.",
						`color_id`=".$color_id."
					WHERE `id`=".$compat_id;
			query($sql);
		}


		jsonSuccess();
		break;
	case 'zp_sale':// ������� ��������
		if(!preg_match(REGEXP_NUMERIC, $_POST['zp_id']) || !$_POST['zp_id'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['income_id']) || !$_POST['income_id'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['count']) || !$_POST['count'])
			jsonError();
		if(!preg_match(REGEXP_CENA, $_POST['cena']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['client_id']))
			jsonError();

		$cena = round($_POST['cena'], 2);
		$count = intval($_POST['count']);

		$v = array(
			'income_id' => $_POST['income_id'],
			'zp_id' => _zpCompatId($_POST['zp_id']),
			'client_id' => intval($_POST['client_id']),
			'sum' => round($count * $cena, 2),
			'prim' => $_POST['prim']
		);

		$sql = "INSERT INTO `zp_move` (
					`ws_id`,
					`zp_id`,
					`count`,
					`cena`,
					`summa`,
					`type`,
					`client_id`,
					`prim`,
					`viewer_id_add`
				) VALUES (
					".WS_ID.",
					".$v['zp_id'].",
					-".$count.",
					".$cena.",
					".$v['sum'].",
					'sale',
					".$v['client_id'].",
					'".win1251(htmlspecialchars(trim($v['prim'])))."',
					".VIEWER_ID."
				)";
		query($sql);

		_zpAvaiSet($v['zp_id']);

		if(!$v = income_insert($v))
			jsonError();

		jsonSuccess();
		break;
	case 'zp_other':// ������� ��������
		if(!preg_match(REGEXP_NUMERIC, $_POST['zp_id']) || $_POST['zp_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['count']) || $_POST['count'] == 0)
			jsonError();
		switch($_POST['type']) {
			case 'defect': $type = 17; break;
			case 'return': $type = 16; break;
			case 'writeoff': $type = 15; break;
			default: jsonError();
		}

		$zp_id = _zpCompatId($_POST['zp_id']);
		$count = intval($_POST['count']) * -1;
		$prim = win1251(htmlspecialchars(trim($_POST['prim'])));

		$sql = "INSERT INTO `zp_move` (
					`ws_id`,
					`zp_id`,
					`count`,
					`type`,
					`prim`,
					`viewer_id_add`
				) VALUES (
					".WS_ID.",
					".$zp_id.",
					".$count.",
					'".$_POST['type']."',
					'".$prim."',
					".VIEWER_ID."
				)";
		query($sql);

		_zpAvaiSet($zp_id);

		history_insert(array(
			'type' => $type,
			'zp_id' => $zp_id
		));

		jsonSuccess();
		break;
	case 'zp_avai_update':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zp_id']) || $_POST['zp_id'] == 0)
			jsonError();
		$zp_id = _zpCompatId($_POST['zp_id']);
		$send['count'] = _zpAvaiSet($zp_id);
		$send['move'] = utf8(zp_move($zp_id));
		jsonSuccess($send);
		break;
	case 'zp_move_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']) || $_POST['id'] == 0)
			jsonError();
		$id = intval($_POST['id']);
		$sql = "SELECT * FROM `zp_move` WHERE `ws_id`=".WS_ID." AND `id`=".$id;
		if(!$move = mysql_fetch_assoc(query($sql)))
			jsonError();
		$lastMoveId = query_value("SELECT `id`
								   FROM `zp_move`
								   WHERE `ws_id`=".WS_ID." AND `zp_id`="._zpCompatId($move['zp_id'])."
								   ORDER BY `id` DESC
								   LIMIT 1");
		if($id != $lastMoveId)
			jsonError();
		$sql = "DELETE FROM `zp_move` WHERE `ws_id`=".WS_ID." AND `id`=".$id;
		query($sql);
		jsonSuccess();
		break;
	case 'zp_move_next':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zp_id']) || $_POST['zp_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['page']))
			jsonError();
		$zp_id = _zpCompatId($_POST['zp_id']);
		$send['spisok'] = utf8(zp_move($zp_id, intval($_POST['page'])));
		jsonSuccess($send);
		break;
	case 'zp_compat_find':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zp_id']) || $_POST['zp_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_BOOL, $_POST['bu']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['name_id']) || $_POST['name_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['device_id']) || $_POST['device_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['vendor_id']) || $_POST['vendor_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['model_id']) || $_POST['model_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['color_id']))
			jsonError();

		$zp_id = intval($_POST['zp_id']);
		$bu = intval($_POST['bu']);
		$name_id = intval($_POST['name_id']);
		$device_id = intval($_POST['device_id']);
		$vendor_id = intval($_POST['vendor_id']);
		$model_id = intval($_POST['model_id']);
		$color_id = intval($_POST['color_id']);

		$sql = "SELECT `id`,`compat_id`
				FROM `zp_catalog`
				WHERE `id`!=".$zp_id."
				  AND `bu`=".$bu."
				  AND `name_id`=".$name_id."
				  AND `base_device_id`=".$device_id."
				  AND `base_vendor_id`=".$vendor_id."
				  AND `base_model_id`=".$model_id."
				  AND `color_id`=".$color_id."
				LIMIT 1";
		$send = mysql_fetch_assoc(query($sql));
		$send['name'] = utf8(_zpName($name_id).' ��� '._deviceName($device_id, 1)._vendorName($vendor_id)._modelName($model_id));
		jsonSuccess($send);
		break;
	case 'zp_compat_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zp_id']) || $_POST['zp_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['device_id']) || $_POST['device_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['vendor_id']) || $_POST['vendor_id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['model_id']) || $_POST['model_id'] == 0)
			jsonError();

		$zp_id = intval($_POST['zp_id']);
		$compat_id = _zpCompatId($zp_id);
		$sql = "SELECT * FROM `zp_catalog` WHERE `id`=".$compat_id;
		if(!$zp = mysql_fetch_assoc(query($sql)))
			jsonError();

		$device_id = intval($_POST['device_id']);
		$vendor_id = intval($_POST['vendor_id']);
		$model_id = intval($_POST['model_id']);

		$sql = "SELECT `id`,`compat_id`
				FROM `zp_catalog`
				WHERE `id`!=".$zp_id."
				  AND `bu`=".$zp['bu']."
				  AND `name_id`=".$zp['name_id']."
				  AND `base_device_id`=".$device_id."
				  AND `base_vendor_id`=".$vendor_id."
				  AND `base_model_id`=".$model_id."
				  AND `color_id`=".$zp['color_id']."
				LIMIT 1";

		if(!$zp['compat_id'])
			query("UPDATE `zp_catalog` SET `compat_id`=".$compat_id." WHERE `id`=".$zp_id);

		if($r = mysql_fetch_assoc(query($sql))) {
			if($r['compat_id'] == $compat_id)
				jsonError();
			if(!$r['compat_id']) {
				query("UPDATE `zp_catalog` SET `compat_id`=".$compat_id." WHERE `id`=".$r['id']);
				$r['compat_id'] = $r['id'];
			}
			query("UPDATE `zp_catalog` SET `compat_id`=".$compat_id." WHERE `compat_id`=".$r['compat_id']);
			query("UPDATE `zp_avai` SET `zp_id`=".$compat_id." WHERE `zp_id`=".$r['compat_id']);
			query("UPDATE `zp_zakaz` SET `zp_id`=".$compat_id." WHERE `zp_id`=".$r['compat_id']);
			query("UPDATE `zp_move` SET `zp_id`=".$compat_id." WHERE `zp_id`=".$r['compat_id']);
			_zpAvaiSet($zp_id);
		} else {
			$zp['base_device_id'] = $device_id;
			$zp['base_vendor_id'] = $vendor_id;
			$zp['base_model_id'] = $model_id;
			$zp['compat_id'] = $compat_id;
			zpAddQuery($zp);
		}
		jsonSuccess();
		break;
	case 'zp_compat_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']) || $_POST['id'] == 0)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['zp_id']) || $_POST['zp_id'] == 0)
			jsonError();
		$id = intval($_POST['id']);
		$zp_id = intval($_POST['zp_id']);
		$sql = "SELECT * FROM `zp_catalog` WHERE `id`=".$id;
		if(!$zp = mysql_fetch_assoc(query($sql)))
			jsonError();
		query("UPDATE `zp_catalog` SET `compat_id`=0 WHERE `id`=".$id);
		if($id == $zp['compat_id']) {
			$sql = "SELECT * FROM `zp_catalog` WHERE `compat_id`=".$id;
			$q = query($sql);
			$r = mysql_fetch_assoc($q);
			if(mysql_num_rows($q) == 1)
				query("UPDATE `zp_catalog` SET `compat_id`=0 WHERE `id`=".$r['id']);
			else
				query("UPDATE `zp_catalog` SET `compat_id`=".$r['id']." WHERE `compat_id`=".$id);
			query("UPDATE `zp_avai` SET `zp_id`=".$r['id']." WHERE `zp_id`=".$id);
			query("UPDATE `zp_zakaz` SET `zp_id`=".$r['id']." WHERE `zp_id`=".$id);
			query("UPDATE `zp_move` SET `zp_id`=".$r['id']." WHERE `zp_id`=".$id);
		}
		$spisok = zp_compat_spisok($zp_id);
		$send['count'] = utf8(zp_compat_count(count($spisok)));
		$send['spisok'] = utf8(implode($spisok));
		jsonSuccess($send);
		break;

	case 'history_spisok':
		$send['html'] = utf8(history_spisok($_POST));
		jsonSuccess($send);
		break;

	case 'remind_spisok':
		if(!preg_match(REGEXP_NUMERIC, $_POST['status']))
			jsonError();
		if(!preg_match(REGEXP_BOOL, $_POST['private']))
			jsonError();
		$filter = array(
			'status' => intval($_POST['status']),
			'private' => intval($_POST['private'])
		);
		$data = remind_data(1, $filter);
		$send['html'] = utf8(!empty($data) ? remind_spisok($data) : '<div class="_empty">������� �� �������.</div>');
		jsonSuccess($send);
		break;
	case 'report_remind_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['client_id']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['zayav_id']))
			jsonError();
		if(!preg_match(REGEXP_DATE, $_POST['day']))
			jsonError();
		if(!preg_match(REGEXP_BOOL, $_POST['private']))
			jsonError();
		if(empty($_POST['txt']))
			jsonError();
		$client_id = intval($_POST['client_id']);
		$zayav_id = intval($_POST['zayav_id']);
		$txt = win1251(htmlspecialchars(trim($_POST['txt'])));
		$private = intval($_POST['private']);
		$sql = "INSERT INTO `reminder` (
					`ws_id`,
					`client_id`,
					`zayav_id`,
					`txt`,
					`day`,
					`private`,
					`history`,
					`viewer_id_add`
				) VALUES (
					".WS_ID.",
					".$client_id.",
					".$zayav_id.",
					'".$txt."',
					'".$_POST['day']."',
					".$private.",
					'".FullDataTime(curTime())." "._viewer(VIEWER_ID, 'name')." ������ �������.',
					".VIEWER_ID."
				)";
		query($sql);
		history_insert(array(
			'type' => 20,
			'client_id' => $client_id,
			'zayav_id' => $zayav_id
		));
		$filter = array();
		if(isset($_POST['from_zayav']) && $zayav_id)
			$filter['zayav'] = $zayav_id;
		if(isset($_POST['from_client']) && $client_id)
			$filter['client'] = $client_id;
		$send['html'] = utf8(remind_spisok(remind_data(1, $filter)));
		xcache_unset(CACHE_PREFIX.'remind_active'.WS_ID);
		jsonSuccess($send);
		break;
	case 'remind_next':
		if(!preg_match(REGEXP_NUMERIC, $_POST['page']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['status']))
			jsonError();
		if(!preg_match(REGEXP_BOOL, $_POST['private']))
			jsonError();
		$filter = array(
			'status' => intval($_POST['status']),
			'private' => intval($_POST['private'])
		);
		$send['html'] = utf8(remind_spisok(remind_data(intval($_POST['page']), $filter)));
		jsonSuccess($send);
		break;
	case 'report_remind_get':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$sql = "SELECT
					`client_id`,
					`zayav_id`,
					`txt`,
					`day`,
					`dtime_add` AS `dtime`,
					`viewer_id_add`
				FROM `reminder` WHERE `id`=".intval($_POST['id'])." AND `status`=1";
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();
		$r['viewer'] = utf8(_viewer($r['viewer_id_add'], 'link'));
		if($r['client_id'] > 0)
			$r['client'] = utf8(_clientLink($r['client_id']));
		if($r['zayav_id'] > 0)
			$r['zayav'] = utf8(_zayavNomerLink($r['zayav_id'], 1));
		$r['txt'] = utf8($r['txt']);
		$r['dtime'] = utf8(FullDataTime($r['dtime']));
		unset($r['client_id']);
		unset($r['zayav_id']);
		unset($r['viewer_id_add']);
		jsonSuccess($r);
		break;
	case 'report_remind_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['status']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['action']))
			jsonError();
		if(!preg_match(REGEXP_DATE, $_POST['day']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['from_zayav']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['from_client']))
			jsonError();
		$history = win1251(htmlspecialchars(trim($_POST['history'])));
		$action = '';
		switch($_POST['action']) {
			case 1: $action = " ������ ����� ����: ".FullData($_POST['day']).". �������: ".$history; break;
			case 2: $action = " �������� �������.".($history ? " (".$history.")" : ''); break;
			case 3: $action = " ������� �������. �������: ".$history; break;
		}
		$sql = "UPDATE `reminder`
				SET `day`='".$_POST['day']."',
					`status`=".$_POST['status'].",
					`history`=CONCAT(`history`,'<BR>".FullDataTime(curTime())." "._viewer(VIEWER_ID, 'name').$action."')
				WHERE `id`=".intval($_POST['id']);
		query($sql);
		$filter = array();
		if($_POST['from_zayav'])
			$filter['zayav'] = $_POST['from_zayav'];
		if($_POST['from_client'])
			$filter['client'] = $_POST['from_client'];
		$data = remind_data(1, $filter);
		$html = remind_spisok($data);
		if(empty($data) && !isset($filter['zayav']))
			$html = '<div class="_empty">������� ���.</div>';
		$send['html'] = utf8($html);
		xcache_unset(CACHE_PREFIX.'remind_active'.WS_ID);
		jsonSuccess($send);
		break;

	case 'income_spisok':
		$data = income_spisok($_POST);
		$send['html'] = utf8($data['spisok']);
		$send['path'] = utf8(income_path($data['filter']['period']));
		jsonSuccess($send);
		break;
	case 'income_next':
		$data = income_spisok($_POST);
		$send['html'] = utf8($data['spisok']);
		jsonSuccess($send);
		break;
	case 'income_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zayav_id']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['income_id']) || !$_POST['income_id'])
			jsonError();
		if(!preg_match(REGEXP_CENA, $_POST['sum']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['place']))
			jsonError();

		$place = intval($_POST['place']);
		$place_other = !$place ? win1251(htmlspecialchars(trim($_POST['place_other']))) : '';

		if(!$_POST['zayav_id'] && empty($_POST['prim']))
			jsonError();

		if(!$v = income_insert($_POST))
			jsonError();

		$send = array();
		if($v['zayav_id']) {
			$sql = "SELECT * FROM `zayav` WHERE `id`=".$v['zayav_id'];
			$r = mysql_fetch_assoc(query($sql));
			if($place != $r['device_place'] || $place_other != $r['device_place_other']) {
				$sql = "UPDATE `zayav`
						SET `device_place`=".$place.",
							`device_place_other`='".$place_other."'
						WHERE `id`=".$v['zayav_id'];
				query($sql);
				history_insert(array(
					'type' => 29,
					'client_id' => $v['client_id'],
					'zayav_id' => $v['zayav_id'],
					'value' =>
						'<table><tr>'.
							'<td>'.($r['device_place'] ? @_devPlace($r['device_place']) : $r['device_place_other']).
							'<td>�'.
							'<td>'.($place ? @_devPlace($place) : $place_other).
						'</table>'
				));
			}
			$send['html'] = utf8(zayav_info_money($v['zayav_id']));
		}
		jsonSuccess($send);
		break;
	case 'income_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);

		$sql = "SELECT *
				FROM `money`
				WHERE `ws_id`=".WS_ID."
				  AND !`deleted`
				  AND `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `money` SET
					`deleted`=1,
					`viewer_id_del`=".VIEWER_ID.",
					`dtime_del`=CURRENT_TIMESTAMP
				WHERE `id`=".$id;
		query($sql);

		invoice_history_insert(array(
			'action' => 2,
			'table' => 'money',
			'id' => $id
		));
		clientBalansUpdate($r['client_id']);
		zayavBalansUpdate($r['zayav_id']);

		history_insert(array(
			'type' => 9,
			'client_id' => $r['client_id'],
			'zayav_id' => $r['zayav_id'],
			'zp_id' => $r['zp_id'],
			'value' => round($r['sum'], 2),
			'value1' => $r['prim'],
			'value2' => $r['income_id']
		));
		jsonSuccess();
		break;
	case 'income_rest':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);
		$sql = "SELECT *
				FROM `money`
				WHERE `ws_id`=".WS_ID."
				  AND `deleted`
				  AND `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();
		$sql = "UPDATE `money` SET
					`deleted`=0,
					`viewer_id_del`=0,
					`dtime_del`='0000-00-00 00:00:00'
				WHERE `id`=".$id;
		query($sql);

		invoice_history_insert(array(
			'action' => 3,
			'table' => 'money',
			'id' => $id
		));
		clientBalansUpdate($r['client_id']);
		$send = zayavBalansUpdate($r['zayav_id']);

		history_insert(array(
			'type' => 19,
			'client_id' => $r['client_id'],
			'zayav_id' => $r['zayav_id'],
			'zp_id' => $r['zp_id'],
			'value' => round($r['sum'], 2),
			'value1' => $r['prim'],
			'value2' => $r['income_id']
		));

		jsonSuccess();
		break;

	case 'expense_spisok':
		if(!preg_match(REGEXP_YEAR, $_POST['year']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['month']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['category']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['worker']))
			jsonError();
		$year = intval($_POST['year']);
		$mon = ($_POST['month'] < 10 ? 0 : '').$_POST['month'];
		$cat = intval($_POST['category']);
		$worker = intval($_POST['worker']);
		$send['mon'] = utf8(expenseMonthSum($year, intval($mon), $cat, $worker));
		$send['html'] = utf8(expense_spisok(1, $year.'-'.$mon, $cat, $worker));
		jsonSuccess($send);
		break;
	case 'expense_next':
		if(!preg_match(REGEXP_NUMERIC, $_POST['page']))
			jsonError();
		if(!preg_match(REGEXP_YEAR, $_POST['year']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['month']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['category']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['worker']))
			jsonError();
		$year = intval($_POST['year']);
		$mon = ($_POST['month'] < 10 ? 0 : '').$_POST['month'];
		$cat = intval($_POST['category']);
		$worker = intval($_POST['worker']);
		$send['html'] = utf8(expense_spisok(intval($_POST['page']), $year.'-'.$mon, $cat, $worker));
		jsonSuccess($send);
		break;
	case 'expense_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['expense_id']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['worker_id']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['invoice_id']) || !$_POST['invoice_id'])
			jsonError();
		if(!preg_match(REGEXP_CENA, $_POST['sum']))
			jsonError();
		$expense_id = intval($_POST['expense_id']);
		$prim = win1251(htmlspecialchars(trim($_POST['prim'])));
		if(!$expense_id && empty($prim))
			jsonError();
		$invoice_id = intval($_POST['invoice_id']);
		$sum = str_replace(',', '.', $_POST['sum']);
		$worker_id = intval($_POST['worker_id']);
		$sql = "INSERT INTO `money` (
					`ws_id`,
					`sum`,
					`prim`,
					`invoice_id`,
					`expense_id`,
					`worker_id`,
					`viewer_id_add`
				) VALUES (
					".WS_ID.",
					-".$sum.",
					'".addslashes($prim)."',
					".$invoice_id.",
					".$expense_id.",
					".$worker_id.",
					".VIEWER_ID."
				)";
		query($sql);

		invoice_history_insert(array(
			'action' => 6,
			'table' => 'money',
			'id' => mysql_insert_id()
		));

		history_insert(array(
			'type' => 21,
			'value' => abs($sum),
			'value1' => $prim,
			'value2' => $expense_id ? $expense_id : '',
			'value3' => $worker_id ? $worker_id : ''
		));
		jsonSuccess();
		break;
	case 'expense_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);

		$sql = "SELECT * FROM `money` WHERE !`deleted` AND `sum`<0 AND `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `money` SET
					`deleted`=1,
					`viewer_id_del`=".VIEWER_ID.",
					`dtime_del`=CURRENT_TIMESTAMP
				WHERE `id`=".$id;
		query($sql);

		invoice_history_insert(array(
			'action' => 7,
			'table' => 'money',
			'id' => $id
		));

		history_insert(array(
			'type' => 22,
			'value' => round(abs($r['sum']), 2)
		));
		jsonSuccess();
		break;

	case 'invoice_set':
		if(!preg_match(REGEXP_NUMERIC, $_POST['invoice_id']) || !$_POST['invoice_id'])
			jsonError();
		if(!preg_match(REGEXP_CENA, $_POST['sum']))
			jsonError();

		$invoice_id = intval($_POST['invoice_id']);
		$sum = round(str_replace(',', '.', $_POST['sum']), 2);

		$sql = "SELECT * FROM `invoice` WHERE `id`=".$invoice_id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		if($r['start'] != -1 && !VIEWER_ADMIN)
			jsonError();

		query("UPDATE `invoice` SET `start`="._invoiceBalans($invoice_id, $sum)." WHERE `id`=".$invoice_id);
		xcache_unset(CACHE_PREFIX.'invoice');
		invoice_history_insert(array(
			'action' => 5,
			'invoice_id' => $invoice_id
		));

		history_insert(array(
			'type' => 28,
			'value' => $sum,
			'value1' => $invoice_id
		));

		$send['html'] = utf8(invoice_spisok());
		jsonSuccess($send);
		break;
	case 'invoice_history':
		if(empty($_POST['invoice_id']) || !preg_match(REGEXP_NUMERIC, $_POST['invoice_id']))
			jsonError();
		$send['html'] = utf8(invoice_history($_POST));
		jsonSuccess($send);
		break;
	case 'invoice_transfer':
		if(empty($_POST['from']) || !preg_match(REGEXP_NUMERIC, $_POST['from']))
			jsonError();
		if(empty($_POST['to']) || !preg_match(REGEXP_NUMERIC, $_POST['to']))
			jsonError();
		if(!preg_match(REGEXP_CENA, $_POST['sum']) || $_POST['sum'] == 0)
			jsonError();

		$from = intval($_POST['from']);
		$to = intval($_POST['to']);
		$sum = str_replace(',', '.', $_POST['sum']);
		$about = win1251(htmlspecialchars(trim($_POST['about'])));

		if($from == $to)
			jsonError();

		$invoice_from = $from > 100 ? 0 : $from;
		$invoice_to = $to > 100 ? 0 : $to;
		$sql = "INSERT INTO `invoice_transfer` (
					`invoice_from`,
					`invoice_to`,
					`worker_from`,
					`worker_to`,
					`sum`,
					`about`,
					`viewer_id_add`
				) VALUES (
					".$invoice_from.",
					".$invoice_to.",
					".($from > 100 ? $from : 0).",
					".($to > 100  ? $to : 0).",
					".$sum.",
					'".addslashes($about)."',
					".VIEWER_ID."
				)";
		query($sql);

		invoice_history_insert(array(
			'action' => 4,
			'table' => 'invoice_transfer',
			'id' => mysql_insert_id()
		));

		history_insert(array(
			'type' => 39,
			'value' => $sum,
			'value1' => $from,
			'value2' => $to,
			'value3' => $about
		));

		$send['i'] = utf8(invoice_spisok());
		$send['t'] = utf8(transfer_spisok());
		jsonSuccess($send);
		break;
}

jsonError();