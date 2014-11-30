<?php
function pageSetup($title) {
	global $book;

	$sheet = $book->getActiveSheet();

	//Глобальные стили для ячеек
	$book->getDefaultStyle()->getFont()->setName('Arial')
		->setSize(9);

	//Ориентация страницы и  размер листа
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
		->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	//Поля документа
	$sheet->getPageMargins()->setTop(0.2)
		->setRight(0.2)
		->setLeft(0.2)
		->setBottom(0.2);

	//Масштаб страницы
	$sheet->getSheetView()->setZoomScale(90);

	//Название страницы
	$sheet->setTitle($title);
}
function xls_zakaz() {
	global $book;

	$sheet = $book->getActiveSheet();
	$line = 1;

	$sheet->getColumnDimension('A')->setWidth(8);  //артикул
	$sheet->getColumnDimension('B')->setWidth(93);  //название
	$sheet->getColumnDimension('C')->setWidth(5);   //количество
	$sheet->getColumnDimension('D')->setWidth(8);   //цена
	$sheet->getColumnDimension('E')->setWidth(8);   //сумма

	$sql = "SELECT
				`p`.`articul`,
				`p`.`name`,
				SUM(`zz`.`count`) `count`,
				`p`.`cena`
			FROM `zp_zakaz` `zz`
				LEFT JOIN `zp_catalog` `c`
				ON `c`.`id`=`zz`.`zp_id`
				LEFT JOIN `zp_price` `p`
				ON `c`.`price_id`=`p`.`id`
			WHERE `c`.`price_id`
			GROUP BY `c`.`id`
			ORDER BY `p`.`name`";
	$q = query($sql);
	$start = $line;
	while($r = mysql_fetch_assoc($q)) {
		$sheet->setCellValue('A'.$line, $r['articul']);
		$sheet->setCellValue('B'.$line, utf8(htmlspecialchars_decode($r['name'])));
		$sheet->setCellValue('C'.$line, $r['count']);
		$sheet->setCellValue('D'.$line, $r['cena']);
		$sheet->setCellValue('E'.$line, $r['count'] * $r['cena']);
		$line++;
	}
	$sheet->getStyle('C'.$start.':C'.$line)->getFont()->setBold(true);

	$line += 2;

	$sql = "SELECT
				c.id,
				`s`.`name` `zp_name`,
				`d`.`name` `device_name`,
				`v`.`name` `vendor_name`,
				`m`.`name` `model_name`,
				`c`.`version`,
				`col`.`name` `color`,
				SUM(`zz`.`count`) `count`
			FROM `zp_zakaz` `zz`
				LEFT JOIN `zp_catalog` `c`
				ON `c`.`id`=`zz`.`zp_id`

				LEFT JOIN `setup_zp_name` `s`
				ON `c`.`name_id`=`s`.`id`

				LEFT JOIN `setup_color_name` `col`
				ON `c`.`color_id`=`col`.`id`

				LEFT JOIN `base_device` `d`
				ON `c`.`base_device_id`=`d`.`id`

				LEFT JOIN `base_vendor` `v`
				ON `c`.`base_vendor_id`=`v`.`id`

				LEFT JOIN `base_model` `m`
				ON `c`.`base_model_id`=`m`.`id`

			WHERE !`c`.`price_id`
			GROUP BY `c`.`id`
			ORDER BY
				`s`.`name`,
				`d`.`name`,
				`v`.`name`,
				`m`.`name`";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q)) {
		$name = $r['zp_name'].' '.$r['vendor_name'].' '.$r['model_name'].' '.$r['color'].' '.$r['version'];
		$sheet->setCellValue('B'.$line, utf8(htmlspecialchars_decode($name)));
		$sheet->setCellValue('C'.$line, $r['count']);
		$line++;
	}
	$sheet->getStyle('A'.($line + 2).':A'.($line + 2));
}//xls_zakaz()


require_once '../config.php';
require_once API_PATH.'/excel/PHPExcel.php';
set_time_limit(10);

$book = new PHPExcel();
$book->setActiveSheetIndex(0);
$sheet = $book->getActiveSheet();

pageSetup('Заказ');
xls_zakaz();

header('Content-Type:application/vnd.ms-excel');
header('Content-Disposition:attachment;filename="zakaz_'.strftime('%Y-%m-%d').'.xls"');
$writer = PHPExcel_IOFactory::createWriter($book, 'Excel5');
$writer->save('php://output');

mysql_close();
exit;


