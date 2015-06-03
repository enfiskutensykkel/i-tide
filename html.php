<?
require_once( 'common.php' );
require_once( 'ctrl.php' );
require_once( 'view.php' );
require_once( 'util.php' );
require_once( 'mime.php' );

$class = "ctrl\\".$_GET['view']; 
unset($_GET['view']);

$resource = new $class;
$resource->handle($_SERVER['REQUEST_METHOD'], $_GET, $_POST);
$data = $resource->retrieveModelInfo();

require_once( $resource->getHtmlFile() );
?>
