<?
namespace ctrl;
use view\ResourcesInfo;

final class Resources extends Controller
{
    public function __construct()
    {
        parent::registerGetHandler();
        $view = new ResourcesInfo;

        parent::registerXmlView($view);
        parent::registerJsonView($view);
    }

    public function handleGet()
    {
    }
}

?>
