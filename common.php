<?php
define('DEBUG', true);
define('VERSION', "0.1-beta");
define('BASE_URL', "/jonassm/i-tide");
require_once( 'errors.php' );

ob_start();
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
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

?>
