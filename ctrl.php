<?
namespace ctrl;
use view\View;
use view\ResourceInfo;
use \NotAcceptable;
use \NotImplemented;
use \MethodNotAllowed;
use \NotFound;

abstract class Controller
{
    private $handlers = array();
    private $views = array();
    private $info = null;

    final protected function registerHtmlView($filename, View &$view)
    {
        if (!is_file($filename) || !is_readable($filename))
        {
            throw new NotFound("File '$filename' is not found");
        }

        $this->info = (object) array(
            'file' => $filename,
            'view' => $view
        );
    }

    final protected function registerXmlView(View &$view)
    {
        $func = function (&$resource) use (&$view) {
            $root = $view->asXml($resource);
            $doc = new \DOMDocument("1.0", "UTF-8");
            $doc->appendChild($doc->importNode($root, true));
            return $doc->saveXML();
        };

        $this->views[MIME_TYPE_APP_XML] = $func;
        $this->views[MIME_TYPE_APP_HTML] = $func;
        $this->views[MIME_TYPE_TEXT_XML] = $func;
    }

    final protected function registerJsonView(View &$view)
    {
        $func = function (&$resource) use (&$view) {
            return json_encode($view->asJson($resource));
        };

        $this->views[MIME_TYPE_APP_JSON] = $func;
        $this->views[MIME_TYPE_TEXT_JSON] = $func;
    }

    final protected function registerTextView(View &$view)
    {
        $this->views[MIME_TYPE_TEXT] = function (&$resource) use (&$view) {
            return (string) $view->asText($resource);
        };
    }

    final public function retrieveModelInfo()
    {
        if ($this->info != null)
        {
            $resinfo = new ResourceInfo($this);
            return $this->info->view->asInfo($resinfo);
        }

        throw new NotImplemented("Resource '".$this->getResourceName()."' does not support model descriptor");
    }

    final public function getView($mimeType)
    {
        if (array_key_exists($mimeType, $this->views))
        {
            $resourceInfo = new ResourceInfo($this);
            return $this->views[$mimeType]($resourceInfo);
        }

        throw new NotAcceptable("MIME type '$mimeType' is not supported for resource '".$this->getResourceName()."'");
    }

    final public function getHtmlFile()
    {
        return $this->info->file;
    }

    final public function getResourceUrl()
    {
        $reflection = new \ReflectionClass($this);
        $resourceName = strtolower($reflection->getShortName());
        $resourceUri = BASE_URL . "/$resourceName";
        
        foreach (func_get_args() as $pathPart)
        {
            $resourceUri .= "/$pathPart";
        }

        return $resourceUri;
    }

    public function getResourceName()
    {
        $reflection = new \ReflectionClass($this);
        return strtolower($reflection->getShortName());
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

    final public static function createResourceUrl($resource)
    {
        $reflection = new \ReflectionClass($resource);
        $resourceName = strtolower($reflection->getShortName());
        $resourceUri = BASE_URL . "/$resourceName";
        
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
