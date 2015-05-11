<?
namespace ctrl;
use view\HolidaysView as View;
use data\Holidays as Model;

final class Holidays extends Controller
{
    public function __construct()
    {
        parent::registerGetHandler();
    }

    protected function handleGet($year = 0)
    {
        $year = \YEAR($year);

        $model = Model::getFromCache($year);

        $view = new View($model, $year);
        parent::registerJsonView($view);
        parent::registerXmlView($view);
        parent::registerTextView($view);
    }
}

?>
