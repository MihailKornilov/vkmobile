<?php
header("Content-type: text/html; charset=windows-1251");
header("Cache-Control: no-store, no-cache,  must-revalidate"); 
header("Expires: ".date("r"));
require_once('../../include/conf.php');

$VK->Query("delete from setup_color_name where id=".$_GET['color_id']);

GvaluesCreate();

echo "1";
?>



