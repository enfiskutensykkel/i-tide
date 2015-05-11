<?
namespace ctrl;
use data\Hours;
use view\StatusView;

final class Status extends Controller
{
    public function __construct()
    {
        parent::registerGetHandler();
    }    

    public function handleGet()
    {
        $ts = time();
        $view = new StatusView($ts, Hours::createFromTimestamp($ts));

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
