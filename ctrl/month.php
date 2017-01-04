<?php
namespace ctrl;
use data\TimeInterval as Calendar;
use data\Hours as Day;
use view\MonthView;
use view\CalendarView;

final class Month extends Controller
{
    public function __construct()
    {
        parent::registerGetHandler();
    }    

    public function handleGet($year = 0, $month = 0, $day = 0)
    {
        $year = \YEAR($year);
        $month = \MONTH($month);
        $day = \DAY($day, $month);
        $now = time();

        $view = new MonthView(Calendar::createForMonth($year, $month), $year, $month);
        //$view = new MonthView(Calendar::createForCalendar($year, $month), $year, $month);
        parent::registerXmlView($view);
        parent::registerJsonView($view);
        parent::registerTextView($view);

        $selected = Day::createFromDate($year, $month, $day);
        $rightnow = Day::createFromTimestamp($now);

        parent::registerHtmlView(
            "html/main.php", 
            new CalendarView(Calendar::createForCalendar($year, $month), $rightnow, $selected, $now)
        );
    }
}

?>
