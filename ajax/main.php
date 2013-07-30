<?php
require_once('../include/conf.php');//todo ��� ��������
require_once('../config.php');
require_once(DOCUMENT_ROOT.'/view/main.php');

function jsonError($values=null) {
    $send['error'] = 1;
    if(empty($values))
        $send['text'] = '��������� ����������� ������.<br />���������� �������.';
    elseif(is_array($values))
        $send += $values;
    else
        $send['text'] = $values;
    die(json_encode($send));
}//end of jsonError()

function jsonSuccess($send=array()) {
    $send['success'] = 1;
    die(json_encode($send));
}//end of jsonSuccess()

switch(@$_POST['op']) {
    case 'report_history_load':
        if(!preg_match(REGEXP_NUMERIC, $_POST['worker']))
            $_POST['worker'] = 0;
        if(!preg_match(REGEXP_NUMERIC, $_POST['action']))
            $_POST['action'] = 0;
        $send['html'] = utf8(report_history_spisok(intval($_POST['worker']), intval($_POST['action'])));
        jsonSuccess($send);
        break;
    case 'report_history_next':
        if(!preg_match(REGEXP_NUMERIC, $_POST['page']))
            jsonError();
        if(!preg_match(REGEXP_NUMERIC, $_POST['worker']))
            $_POST['worker'] = 0;
        if(!preg_match(REGEXP_NUMERIC, $_POST['action']))
            $_POST['action'] = 0;
        $send['html'] = utf8(report_history_spisok(intval($_POST['worker']), intval($_POST['action']), intval($_POST['page'])));
        jsonSuccess($send);
        break;
    case 'report_remind_load':
        if(!preg_match(REGEXP_NUMERIC, $_POST['status']))
            $_POST['status'] = 1;
        if(!preg_match(REGEXP_BOOL, $_POST['private']))
            $_POST['private'] = 0;
        $send['html'] = utf8(report_remind_spisok(1, intval($_POST['status']), intval($_POST['private'])));
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
                    '".FullDataTime(strftime("%Y-%m-%d %H:%M:%S", time()))." ".viewerName()." ������ �������.',
                    ".VIEWER_ID."
                )";
        query($sql);
        history_insert(array(
            'type' => 20,
            'client_id' => $client_id,
            'zayav_id' => $zayav_id
        ));
        $send['html'] = utf8(report_remind_spisok());
        jsonSuccess($send);
        break;
    case 'report_remind_next':
        if(!preg_match(REGEXP_NUMERIC, $_POST['page']))
            jsonError();
        $send['html'] = utf8(report_remind_spisok(intval($_POST['page'])));
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
        $r['viewer'] = utf8(viewerName(true, $r['viewer_id_add']));
        if($r['client_id'] > 0) {
            $c = get_clients_info(array($r['client_id']));
            $r['client'] = utf8($c[$r['client_id']]);
        }
        if($r['zayav_id'] > 0) {
            $z = get_zayav_info(array($r['zayav_id']));
            $r['zayav'] = utf8($z[$r['zayav_id']]);
        }
        $r['txt'] = utf8($r['txt']);
        $r['dtime'] = utf8(FullDataTime($r['dtime']));
        unset($r['client_id']);
        unset($r['zayav_id']);
        unset($r['viewer_id_add']);
        //echo 1;
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
                    `history`=CONCAT(`history`,'<BR>".FullDataTime(strftime("%Y-%m-%d %H:%M:%S", time()))." ".viewerName().$action."')
                WHERE `id`=".intval($_POST['id']);
        query($sql);
        $send['html'] = utf8(report_remind_spisok());
        jsonSuccess($send);
        break;
    case 'report_prihod_load':
        if(!preg_match(REGEXP_DATE, $_POST['day_begin']))
            $_POST['day_begin'] = currentMonday();
        if(!preg_match(REGEXP_DATE, $_POST['day_end']))
            $_POST['day_end'] = currentSunday();
        if(!preg_match(REGEXP_BOOL, $_POST['del_show']) || !ADMIN)
            $_POST['del_show'] = 0;
        $send['html'] = utf8(report_prihod_spisok($_POST['day_begin'], $_POST['day_end'], intval($_POST['del_show'])));
        jsonSuccess($send);
        break;
    case 'report_prihod_next':
        if(!preg_match(REGEXP_NUMERIC, $_POST['page']))
            jsonError();
        if(!preg_match(REGEXP_DATE, $_POST['day_begin']))
            jsonError();
        if(!preg_match(REGEXP_DATE, $_POST['day_end']))
            jsonError();
        if(!preg_match(REGEXP_BOOL, $_POST['del_show']) || !ADMIN)
            $_POST['del_show'] = 0;
        $send['html'] = utf8(report_prihod_spisok($_POST['day_begin'], $_POST['day_end'], intval($_POST['del_show']), intval($_POST['page'])));
        jsonSuccess($send);
        break;
    case 'report_prihod_add':
        if(empty($_POST['about']))
            jsonError();
        if(!preg_match(REGEXP_NUMERIC, $_POST['sum']))
            jsonError();
        if(!preg_match(REGEXP_BOOL, $_POST['kassa']))
            jsonError();
        $about = win1251(htmlspecialchars(trim($_POST['about'])));
        $sum = intval($_POST['sum']);
        $kassa = intval($_POST['kassa']);
        $sql = "INSERT INTO `money`
                    (`ws_id`,`summa`,`prim`,`kassa`,`viewer_id_add`)
                VALUES
                    (".WS_ID.",".$sum.",'".$about."',".$kassa.",".VIEWER_ID.")";
        query($sql);

        if ($kassa == 1) {
            $sql = "INSERT INTO `kassa`
                        (`ws_id`, `sum`, `txt`, `money_id`, `viewer_id_add`)
                    VALUES
                        (".WS_ID.", ".$sum.", '".$about."', ".mysql_insert_id().", ".VIEWER_ID.")";
            query($sql);
        }
        history_insert(array(
            'type' => 6,
            'value' => $sum,
            'value1' => $about
        ));
        jsonSuccess();
        break;
    case 'report_prihod_del':
        if(!ADMIN)
            jsonError();
        if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
            jsonError();
        $id = intval($_POST['id']);
        $sql = "UPDATE `money` SET
                    `status`=0,
                    `viewer_id_del`=".VIEWER_ID.",
                    `dtime_del`=CURRENT_TIMESTAMP
                WHERE `id`=".$id;
        query($sql);
        $sql = "SELECT * FROM `money` WHERE `id`=".$id;
        $r = mysql_fetch_assoc(query($sql));
        history_insert(array(
            'type' => 9,
            'value' => $r['summa'],
            'value1' => $r['prim'],
            'client_id' => $r['client_id'],
            'zayav_id' => $r['zayav_id'],
            'zp_id' => $r['zp_id'],
        ));
        jsonSuccess();
        break;
    case 'report_prihod_rest':
        if(!ADMIN)
            jsonError();
        if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
            jsonError();
        $id = intval($_POST['id']);
        $sql = "UPDATE `money` SET
                    `status`=1,
                    `viewer_id_del`=0,
                    `dtime_del`='0000-00-00 00:00:00'
                WHERE `id`=".$id;
        query($sql);
        $sql = "SELECT * FROM `money` WHERE `id`=".$id;
        $r = mysql_fetch_assoc(query($sql));
        history_insert(array(
            'type' => 19,
            'value' => $r['summa'],
            'value1' => $r['prim'],
            'client_id' => $r['client_id'],
            'zayav_id' => $r['zayav_id'],
            'zp_id' => $r['zp_id'],
        ));
        jsonSuccess();
        break;
    case 'report_rashod_next':
        if(!preg_match(REGEXP_NUMERIC, $_POST['page']))
            jsonError();
        $send['html'] = utf8(report_rashod_spisok(intval($_POST['page'])));
        jsonSuccess($send);
        break;
    case 'report_rashod_load':
        if(!preg_match(REGEXP_YEAR, $_POST['year']))
            jsonError();
        if(!preg_match(REGEXP_NUMERIC, $_POST['month']))
            jsonError();
        if(!preg_match(REGEXP_NUMERIC, $_POST['category']))
            jsonError();
        if(!preg_match(REGEXP_NUMERIC, $_POST['worker']))
            jsonError();
        $year = intval($_POST['year']);
        $send['summ'] = report_rashod_monthSum($year, intval($_POST['category']), intval($_POST['worker']));
        $send['html'] = utf8(report_rashod_spisok(1, $year.'-'.$_POST['month'], intval($_POST['category']), intval($_POST['worker'])));
        jsonSuccess($send);
        break;
    case 'report_rashod_add':
        if(!preg_match(REGEXP_NUMERIC, $_POST['category']))
            jsonError();
        if(!preg_match(REGEXP_NUMERIC, $_POST['worker']))
            jsonError();
        if(!preg_match(REGEXP_NUMERIC, $_POST['sum']))
            jsonError();
        if(!preg_match(REGEXP_BOOL, $_POST['kassa']))
            jsonError();
        $category = intval($_POST['category']);
        $about = win1251(htmlspecialchars(trim($_POST['about'])));
        if($category == 0 && empty($about))
            jsonError();
        $sum = intval($_POST['sum']) * -1;
        $kassa = intval($_POST['kassa']);
        $worker = intval($_POST['worker']);
        $sql = "INSERT INTO `money`
                    (`ws_id`,`summa`,   `prim`,   `kassa`,  `rashod_category`,  `worker_id`,`viewer_id_add`)
                VALUES
                    (".WS_ID.",".$sum.",'".$about."',".$kassa.",".$category.",".$worker.",".VIEWER_ID.")";
        query($sql);

        if($kassa == 1) {
            $sql = "INSERT INTO `kassa`
                        (`ws_id`, `sum`, `txt`, `money_id`, `viewer_id_add`)
                    VALUES
                        (".WS_ID.", ".$sum.", '".$about."', ".mysql_insert_id().", ".VIEWER_ID.")";
            query($sql);
        }
        history_insert(array(
            'type' => 21,
            'value' => abs($sum),
            'value1' => $about
        ));
        jsonSuccess();
        break;
    case 'report_rashod_del':
        if(!ADMIN)
            jsonError();
        if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
            jsonError();
        $id = intval($_POST['id']);
        $sql = "UPDATE `money` SET
                    `status`=0,
                    `viewer_id_del`=".VIEWER_ID.",
                    `dtime_del`=CURRENT_TIMESTAMP
                WHERE `id`=".$id;
        query($sql);
        $sql = "SELECT * FROM `money` WHERE `id`=".$id;
        $r = mysql_fetch_assoc(query($sql));
        history_insert(array(
            'type' => 22,
            'value' => abs($r['summa'])
        ));
        jsonSuccess();
        break;
    case 'report_rashod_get':
        if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
            jsonError();
        $sql = "SELECT
                    `summa` * -1 AS `sum`,
                    `prim` AS `about`,
                    `kassa`,
                    `worker_id`,
                    `rashod_category` AS `category`
                FROM `money`
                WHERE `status`=1
                  AND `id`=".intval($_POST['id'])."
                LIMIT 1";
        if(!$send = mysql_fetch_assoc(query($sql)))
            jsonError();
        $send['about'] = utf8($send['about']);
        jsonSuccess($send);
        break;
    case 'report_rashod_edit':
        if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
            jsonError();
        if(!preg_match(REGEXP_NUMERIC, $_POST['worker']))
            jsonError();
        if(!preg_match(REGEXP_NUMERIC, $_POST['sum']))
            jsonError();
        if(!preg_match(REGEXP_BOOL, $_POST['kassa']))
            jsonError();
        $id = intval($_POST['id']);
        $category = intval($_POST['category']);
        $about = win1251(htmlspecialchars(trim($_POST['about'])));
        if($category == 0 && empty($about))
            jsonError();
        $sum = intval($_POST['sum']) * -1;
        $kassa = intval($_POST['kassa']);
        $worker = intval($_POST['worker']);
        $sql = "UPDATE `money` SET
                    `summa`=".$sum.",
                    `prim`='".$about."',
                    `kassa`=".$kassa.",
                    `rashod_category`=".$category.",
                    `worker_id`=".$worker."
                WHERE `id`=".$id;
        query($sql);

        $sql = "DELETE FROM `kassa` WHERE `money_id`=".$id;
        query($sql);
        if($kassa == 1) {
            $sql = "SELECT `dtime_add` FROM `money` WHERE `id`=".$id;
            $r = mysql_fetch_assoc(query($sql));
            $sql = "INSERT INTO `kassa`
                        (`ws_id`,`sum`,`txt`,`money_id`,`viewer_id_add`,`dtime_add`)
                    VALUES
                        (".WS_ID.",".$sum.",'".$about."',".$id.",".VIEWER_ID.",'".$r['dtime_add']."')";
            query($sql);
        }
        history_insert(array(
            'type' => 23,
            'value' => abs($sum),
            'value1' => $about
        ));
        jsonSuccess();
        break;
    case 'setup_rashod_category_add':
        if(empty($_POST['name']))
            jsonError();
        $sql = "INSERT INTO `setup_rashod_category` (
                    `name`,`viewer_id_add`
                ) VALUES (
                    '".win1251(htmlspecialchars(trim($_POST['name'])))."',".VIEWER_ID."
                )";
        query($sql);
        $send['id'] = mysql_insert_id();
        jsonSuccess($send);
        break;
}

jsonError();