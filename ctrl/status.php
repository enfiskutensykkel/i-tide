<?
namespace ctrl;
use view\StatusView;

final class Status extends Controller
{
    public function __construct()
    {
        parent::registerGetHandler();
    }    

    public function handleGet()
    {
        $view = new StatusView(time());

        parent::registerJsonView($view);
        parent::registerXmlView($view);
        parent::registerTextView($view);
    }

    public function fresh()
    {
        return true;
    }
}

?>
