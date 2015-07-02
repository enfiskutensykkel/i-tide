<?
namespace view;
use view\MonthView;
use view\StatusView;
use view\HoursView;
use view\DateInfo;

final class CalendarView extends View
{
    private $monthInterval;
    private $today;
    private $selected;
    private $statusView;

    public function __construct($month, $today, $selected, $now)
    {
        $this->monthInterval = $month;
        $this->today = $today;
        $this->selected = $selected;

        $this->statusView = new StatusView($now);
    }
    
    public function asInfo(ResourceInfo &$res)
    {
        $status = $this->statusView->asInfo($res);
        $tomorrow = (new HoursView($this->today->getNextDay()))->asInfo($res);

        $selected = (new HoursView($this->selected))->asInfo($res);
        $selectedNext = DateInfo::withoutInfo($this->selected->getNextPossible()->getDate());

        $month = date('m', $this->selected->getDate());

        $calendar = array();
        foreach ($this->monthInterval as $date => $hours)
        {
            $dateinfo = DateInfo::withHours($hours)->asJson($res);

            $calendar[] = (object) array(
                'date' => $dateinfo->dateinfo->date,
                'beer' => $dateinfo->dateinfo->beer,
                'wine' => $dateinfo->dateinfo->wine,
                'info' => $dateinfo->dateinfo->description,
                'mark' => $hours->isHoliday() || $hours->isSunday(),
                'warn' => $hours->isDayBeforeHoliday(false),
                'part' => date('m', $hours->getDate()) == $month
            );
        }

        return (object) array(
            'today' => $status,
            'tomorrow' => (object) array(
                'date' => $tomorrow->current->dateinfo->date,
                'beer' => $tomorrow->current->dateinfo->beer,
                'wine' => $tomorrow->current->dateinfo->wine,
                'info' => $tomorrow->current->dateinfo->description
            ),
            'selected' => (object) array(
                'date' => $selected->current->dateinfo->date,
                'beer' => $selected->current->dateinfo->beer,
                'wine' => $selected->current->dateinfo->wine,
                'info' => $selected->current->dateinfo->description,
                'next' => $selectedNext->asJson($res)->dateinfo->date
            ),
            'calendar' => $calendar
        );
    }
}

?>
