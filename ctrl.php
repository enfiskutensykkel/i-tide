<?
namespace ctrl;
use view\View;
use \NotAcceptable;
use \NotImplemented;
use \MethodNotAllowed;

abstract class Controller
{
    private $handlers = array();
    private $views = array();

    final protected function registerXmlView(View &$view)
    {
        $res = $this;
        $this->views[MIME_TYPE_XML] = function ($mimeType) use (&$res, &$view) {
            $root = $view->asXml($res, $mimeType);
            $doc = new \DOMDocument("1.0", $view->getCharSet());
            $doc->appendChild($doc->importNode($root, true));
            return $doc->saveXML();
        };
    }

    final protected function registerJsonView(View &$view)
    {
        $res = $this;
        $this->views[MIME_TYPE_JSON] = function ($mimeType) use (&$res, &$view) {
            return json_encode($view->asJson($res, $mimeType));
        };
    }

    final protected function registerTextView(View &$view)
    {
        $res = $this;
        $this->views[MIME_TYPE_TEXT] = function ($mimeType) use (&$res, &$view) {
            return (string) $view->asText($res, $mimeType);
        };
    }

    final public function retrieveView($mimeType)
    {
        if (array_key_exists($mimeType, $this->views))
        {
            return $this->views[$mimeType]($mimeType);
        }

        throw new NotAcceptable("MIME type '$mimeType' is not supported for this resource");
    }

    final public function getDefaultCharSet()
    {
        return "UTF-8";
    }

    final public function getResourceUri()
    {
        $reflection = new \ReflectionClass($this);
        $resourceName = strtolower($reflection->getShortName());
        $resourceUri = BASE_URI . "/$resourceName";
        
        foreach (func_get_args() as $pathPart)
        {
            $resourceUri .= "/$pathPart";
        }

        return $resourceUri;
    }

    final protected function registerGetHandler()
    {
        $this->handlers['GET'] = function (&$res, &$params, &$args) {
            call_user_func_array(array($res, 'handleGet'), $params);
        };
    }

    final protected function registerPostHandler()
    {
        $this->handlers['POST'] = function (&$res, &$params, &$args) {
            call_user_func_array(array($res, 'handleQueryParams', &$params));
            call_user_func_array(array($res, 'handlePost'), $args);
        };
    }

    final public static function createResourceUri($resource)
    {
        $reflection = new \ReflectionClass($resource);
        $resourceName = strtolower($reflection->getShortName());
        $resourceUri = BASE_URI . "/$resourceName";
        
        foreach (array_slice(func_get_args(), 1) as $pathPart)
        {
            $resourceUri .= "/$pathPart";
        }

        return $resourceUri;
    }

    final public function handle($method, &$params, &$data)
    {
        if (array_key_exists($method, $this->handlers))
        {
            $this->handlers[$method]($this, $params, $data);
            return;
        }

        throw new MethodNotAllowed("Method '$method' is not supported for this resource");
    }

    protected function handleGet()
    {
        throw new NotImplemented("GET is not implemented for this resource");
    }

    protected function handlePost($queryParams)
    {
        throw new NotImplemented("POST is not implemented for this resource");
    }

    // Override to handle query arguments
    protected function handleQueryParams()
    {
    }

    // Override to prevent caching
    public function fresh()
    {
        return false;
    }

}

?>
