<?
require_once( 'common.php' );
require_once( 'ctrl.php' );
require_once( 'view.php' );

$class = "ctrl\\".$_GET['view']; 
unset($_GET['view']);

$resource = new $class;
$resource->handle($_SERVER['REQUEST_METHOD'], $_GET, $_POST);
$data = $resource->retrieveModelInfo();

require_once( $resource->getHtmlFile() );
?>
