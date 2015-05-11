<?
namespace ctrl;
use data\TimeInterval as Model;
use view\MonthView as View;

final class Month extends Controller
{
    public function __construct()
    {
        parent::registerGetHandler();
    }    

    public function handleGet($year = 0, $month = 0)
    {
        $year = \YEAR($year);
        $month = \MONTH($month);

        $view = new View(Model::createForMonth($year, $month), $year, $month);
        parent::registerXmlView($view);
        parent::registerJsonView($view);
        parent::registerTextView($view);
    }
}

?>
