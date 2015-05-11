<?
define('DEBUG', true);
define('VERSION', "beta-0");
define('BASE_URI', "/jonassm/i-tide/res");
require_once( 'http.php' );
require_once( 'mime.php' );
require_once( 'util.php' );
require_once( 'view.php' );
require_once( 'ctrl.php' );

ob_start();
error_reporting( E_ALL );
date_default_timezone_set('Europe/Oslo');

set_error_handler(
    function ($errno, $errstr, $errfile, $errline)
    {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    }, E_ALL);

set_exception_handler(
    function ($exception)
    {
        if (!$exception instanceof HttpException)
        {
            $exception = new InternalServerError("Internal server error", $exception);
        }

        $code = $exception->getStatusCode();
        $message = $exception->getStatusMessage();

        ob_clean();
        header("HTTP/1.1 $code");

        if ( defined('DEBUG') && DEBUG )
        {
            //$trace = debug_backtrace();
            echo "<html><head><title>$message</title></head><body>";
            echo "<h1>$code $message</h1>";
            echo "<pre>$exception</pre>";
            echo "</body></html>";
        }

        ob_end_flush();
    }
);

spl_autoload_register(
    function ($classname)
    {
        $file = str_replace('\\', '/', strtolower($classname)) . ".php";

        if (file_exists($file) && is_file($file) && is_readable($file))
        {
            require_once($file);
            return;
        }

        throw new NotFound("Resource '$classname' not found");
    }
);

$class = 'ctrl\\'.$_GET['ctrl'];
$accept = $_GET['view'] ? MIME_TYPE($_GET['view']) : $_GET['type'];
unset($_GET['ctrl'], $_GET['view'], $_GET['type']);

$ctrl = new $class;
$ctrl->handle($_SERVER['REQUEST_METHOD'], $_GET, $_POST);

$view = $ctrl->retrieveView($accept, $_SERVER['HTTP_ACCEPT']);

$maxage = 86400 * 20;
$cache = !$ctrl->fresh() ? "max=age=$maxage" : "no-cache";
$expires = !$ctrl->fresh() ? date("D, d M Y H:i:s T", time() + $maxage) : date("D, d M Y H:i:s T", time() + 5);
$charset = $ctrl->getDefaultCharSet();

header("HTTP/1.1 200");
header("Content-Type: $accept; charset=$charset");
header("Content-Language: no");
header("Cache-Control: $cache");
header("Expires: $expires");

echo((string) $view);

?>
