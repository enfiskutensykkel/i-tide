<?

abstract class HttpException extends Exception
{
    private $status_code;
    private $status_message;

    public function __construct($code, $type, $message = "", $previous = null)
    {
        parent::__construct($message, E_USER_NOTICE, $previous);
        $this->status_code = $code;
        $this->status_message = $type;
    }

    final public function getStatusCode()
    {
        return $this->status_code;
    }

    final public function getStatusMessage()
    {
        return $this->status_message;
    }
}

class BadRequest extends HttpException
{
    public function __construct($message, $previous = null)
    {
        parent::__construct(400, "Bad request", $message, $previous);
    }
}

class NotFound extends HttpException
{
    public function __construct($message, $previous = null)
    {
        parent::__construct(404, "Not found", $message, $previous);
    }
}

class MethodNotAllowed extends HttpException
{
    public function __construct($message, $previous = null)
    {
        parent::__construct(405, "Method not allowed", $message, $previous);
    }
}

class NotAcceptable extends HttpException
{
    public function __construct($message, $previous = null)
    {
        parent::__construct(406, "Not acceptable", $message, $previous);
    }
}

class InternalServerError extends HttpException
{
    public function __construct($message, $previous = null)
    {
        parent::__construct(500, "Internal server error", $message, $previous);
    }
}

class NotImplemented extends HttpException
{
    public function __construct($message, $previous = null)
    {
        parent::__construct(501, "Not implemented", $message, $previous);
    }
}

?>
