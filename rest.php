<?php
require_once( 'common.php' );
require_once( 'mime.php' );
require_once( 'util.php' );
require_once( 'view.php' );
require_once( 'ctrl.php' );

$class = 'ctrl\\'.$_GET['ctrl'];
$accept = $_GET['view'] ? MIME_TYPE($_GET['view']) : $_GET['type'];
$charset = "UTF-8";
unset($_GET['ctrl'], $_GET['view'], $_GET['type']);

$ctrl = new $class;
$ctrl->handle($_SERVER['REQUEST_METHOD'], $_GET, $_POST);

$view = $ctrl->getView($accept, $charset);

$maxage = 86400 * 20;
$cache = !$ctrl->fresh() ? "max=age=$maxage" : "no-cache";
$expires = !$ctrl->fresh() ? date("D, d M Y H:i:s T", time() + $maxage) : date("D, d M Y H:i:s T", time() + 5);

header("HTTP/1.1 200");
header("Content-Type: $accept; charset=$charset");
header("Content-Language: no");
header("Cache-Control: $cache");
header("Expires: $expires");

echo((string) $view);

?>
