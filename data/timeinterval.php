<?
namespace data;
use data\Hours;
use \Iterator;
use \ArrayAccess;

final class TimeInterval implements Iterator, ArrayAccess
{
    private $hours = array();
    private $keys = array();
    private $curr = 0;
    private $count = 0;

    private function insert($year, $month, $day)
    {
        $date = mktime(0, 0, 0, $month, $day, $year);
        $info = new Hours($date);
        $this->keys[] = $date;
        $this->hours[$date] = $info;
        ++$this->count;
    }

    private function populate($year_from, $month_from, $day_from, $year_to, $month_to, $day_to)
    {
        $year = $year_from;
        $month = $month_from;
        $day = $day_from;

        for (; $year < $year_to; ++$year)
        {
            for (; $month <= 12; ++$month)
            {
                $num_days_month = date('t', mktime(0, 0, 0, $month, 1, $year));
                for (; $day <= $num_days_month; ++$day)
                {
                    $this->insert($year, $month, $day);
                }
                $day = 1;
            }
            $month = 1;
        }

        for (; $month < $month_to; ++$month)
        {
            $num_days_month = date('t', mktime(0, 0, 0, $month, 1, $year));
            for (; $day <= $num_days_month; ++$day)
            {
                $this->insert($year, $month, $day);
            }
            $day = 1;
        }

        for (; $day <= $day_to; ++$day)
        {
            $this->insert($year, $month, $day);
        }
    }

    private function __construct($from, $to)
    {
        $year_from = date('Y', $from);
        $month_from = date('m', $from);
        $day_from = date('d', $from);

        $year_to = date('Y', $to);
        $month_to = date('m', $to);
        $day_to = date('d', $to);

        $this->populate($year_from, $month_from, $day_from, $year_to, $month_to, $day_to);
    }

    public static function createForCalendar($year, $month)
    {
        $firstInMonth = mktime(0, 0, 0, $month, 1, $year);
        $offset = date("w", $firstInMonth) - 1;
        if ($offset == -1) 
        {
            $offset = 6;
        }
        $first = strtotime("-$offset days", $firstInMonth);

        $lastInMonth = mktime(0, 0, 0, $month, date('t', $firstInMonth), $year);
        $offset = date("w", $lastInMonth);
        $last = strtotime("-$offset days", strtotime("+7 days", $lastInMonth));
        
        return new self($first, $last);
        
    }

    public static function createForMonth($year, $month)
    {
        $first = mktime(0, 0, 0, $month, 1, $year);
        $days = date('t', $first);
        return new self($first, mktime(0, 0, 0, $month, $days, $year));
    }

    public function __get($property)
    {
        if ($property == "from" || $property == "start" || $property == "begin" || $property == "first")
        {
            return $this->keys[0];
        }
        else if ($property == "to" || $property == "end" || $property == "last" || $property == "until")
        {
            return $this->keys[$this->count - 1];
        }

        return null;
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception("Can't set values");
    }

    public function offsetUnset($offset)
    {
        throw new Exception("Can't unset values");
    }

    public function offsetGet($offset)
    {
        if (array_key_exists($offset, $this->hours))
        {
            return $this->hours[$offset];
        }
        else if (array_key_exists($offset, $this->keys))
        {
            return $this->hours[$this->keys[$offset]];
        }
        echo "<pre>"; var_dump($offset); echo "</pre>"; die();

        return null;
    }

    public function offsetExists($offset)
    {
        if (array_key_exists($offset, $this->hours))
        {
            return true;
        }
        else if (array_key_exists($offset, $this->keys))
        {
            return true;
        }

        return false;
    }

    public function rewind()
    {
        $this->curr = 0;
    }

    public function current()
    {
        return $this->hours[$this->keys[$this->curr]];
    }

    public function key()
    {
        if ($this->curr < $this->count)
        {
            return $this->keys[$this->curr];
        }
        return null;
    }

    public function next()
    {
        ++$this->curr;
    }

    public function valid()
    {
        return $this->curr < $this->count;
    }
}

?>
