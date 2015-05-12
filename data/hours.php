<?
namespace data;
use data\Holidays;

/* 
 * Keep track of sales hours
 * http://www.lovdata.no/all/tl-19890602-027-006.html
 */
final class Hours
{
    private $holidays;
    private $today;
    private $tomorrow;
    private $saturday;
    private $sunday;
    private $today_type;
    private $tomorrow_type;

    public function __construct($date)
    {
        $this->holidays = Holidays::getFromCache(date('Y', $date));
        $this->today = $date;
        $this->tomorrow = strtotime("+1 day", $this->today);
        $this->sunday = date('w', $this->today) == 0;
        $this->saturday = !$this->sunday && date('w', $this->tomorrow) == 0;

        $this->today_type = $this->holidays->getDateType($this->today);
        $this->tomorrow_type = $this->holidays->getDateType($this->tomorrow);
    }

    public static function createFromTimestamp($timestamp)
    {
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $day = date('d', $timestamp);
        return new self(mktime(0, 0, 0, $month, $day, $year));
    }

    public static function createFromDate($year, $month, $day)
    {
        return new self(mktime(0, 0, 0, $month, $day, $year));
    }

    public function getWineHours()
    {
        if ($this->sunday || $this->today_type != Holidays::NOTHING)
        {
            // Sunday or any non-regular day (holidays, "eves", election day, other special days)
            return null;
        }

        if ($this->isDayBeforeHoliday())
        {
            // Day before holiday
            return (object) array(
                'open' => $this->today + 10 * 3600,
                'close' => $this->today + 15 * 3600
            );
        }
        else
        {
            // Normal sales hours
            return (object) array(
                'open' => $this->today + 10 * 3600,
                'close' => $this->today + 18 * 3600
            );
        }
    }

    public function getBeerHours()
    {
        if ($this->isHoliday() || $this->isElectionDay())
        {
            // Sunday or holiday
            return null;
        }
        else if ($this->today_type == Holidays::EVE)
        {
            // One of the "eves"
            return (object) array(
                'open' => $this->today + 9 * 3600,
                'close' => $this->today + 15 * 3600
            );
        }
        else if ($this->isDayBeforeHoliday())
        {
            // Day before holiday
            return (object) array(
                'open' => $this->today + 9 * 3600,
                'close' => $this->today + 18 * 3600
            );
        }
        else
        {
            // Normal sales hours
            return (object) array(
                'open' => $this->today + 9 * 3600,
                'close' => $this->today + 20 * 3600
            );
        }
    }

    public function getDate()
    {
        return $this->today;
    }

    public function isSunday()
    {
        return $this->sunday;
    }

    public function isDayBeforeHoliday()
    {
        if ($this->saturday || $this->tomorrow_type == Holidays::HOLIDAY)
        {
            return true;
        }

        return false;
    }

    public function isHoliday()
    {
        return $this->sunday || $this->today_type == Holidays::HOLIDAY || $this->today_type == Holidays::OTHER;
    }

    public function isElectionDay()
    {
        return $this->today_type == Holidays::ELECTION;
    }

    public function getDateInfo()
    {
        return $this->holidays->getDateInfo($this->today);
    }

    public function getNextPossible()
    {
        $date = new Hours($this->tomorrow);

        while ($date->isHoliday() || $date->isElectionDay())
        {
            $date = new Hours($date->tomorrow);
        }

        return $date;
    }

    public function getUpcomingEvent($max_days = 3)
    {
        $date = $this;

        for ($days = 0; $days < $max_days && $date->today_type == Holidays::NOTHING; ++$days, $date = new Hours($date->tomorrow));

        if ($date->today_type != Holidays::NOTHING)
        {
            return $date;
        }

        return null;
    }

    public function getRemainingBeerTime($timestamp)
    {
        if ($this->today <= $timestamp && $timestamp < $this->tomorrow)
        {
            $hours = $this->getBeerHours();
            if ($hours != null && $hours->open <= $timestamp && $timestamp < $hours->close)
            {
                return $hours->close - 3600 - $timestamp;
            }

            return 0;
        }

        return -1;
    }

    public function getRemainingWineTime($timestamp)
    {
        if ($this->today <= $timestamp && $timestamp < $this->tomorrow)
        {
            $hours = $this->getWineHours();
            if ($hours != null && $hours->open <= $timestamp && $timestamp < $hours->close)
            {
                return $hours->close - 3600 - $timestamp;
            }

            return 0;
        }

        return -1;
    }
}

?>
