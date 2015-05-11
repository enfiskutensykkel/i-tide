<?
namespace ctrl;
use data\Hours as Model;
use data\Holidays as HModel;
use view\HoursView as View;

final class Hours extends Controller
{
    public function __construct()
    {
        parent::registerGetHandler();
    }

    protected function handleGet($year = 0, $month = 0, $day = 0, $override = null)
    {
        if ($override == "today")
        {
            $now = strtotime("now");
            $date = mktime(0, 0, 0, date('m', $now), date('j', $now), date('Y', $now));
        }
        else if ($override == "tomorrow")
        {
            $tomorrow = strtotime("+1 day");
            $date = mktime(0, 0, 0, date('m', $tomorrow), date('j', $tomorrow), date('Y', $tomorrow));
        }
        else
        {
            $year = \YEAR($year);
            $month = \MONTH($month);
            $day = \DAY($day, $month);
            $date = mktime(0, 0, 0, $month, $day, $year);
        }

        $view = new View(new Model($date), HModel::getFromCache(date('Y', $date)));
        parent::registerXmlView($view);
        parent::registerJsonView($view);
        parent::registerTextView($view);
    }
}

?>
