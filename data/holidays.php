<?php
namespace data;
use data\HolidaysIterator;

/*
 * Keep track of holidays
 * http://www.lovdata.no/all/hl-19950224-012.html#2
 */
final class Holidays
{
    const NOTHING = 0;
    const ELECTION = 1;
    const OTHER = 2;
    const EVE = 3;
    const NEWYEARS_EVE = 4;
    const HOLIDAY = 5;

    private $year;
    private $other;
    private $eves;
    private $newyear;
    private $holidays;
    private $aggregated = null;

    private static $years = array();
    private static $elections = null;

    private function __construct($year)
    {
        $easter = easter_date($year);
        $this->year = $year;

        $this->holidays = array(
            mktime(0, 0, 0, 1, 1, $year)        => '1. nyttårsdag',
            strtotime('-7 days', $easter)       => 'Palmesøndag',
            strtotime('-3 days', $easter)       => 'Skjærtorsdag',
            strtotime('-2 days', $easter)       => 'Langfredag',
            $easter                             => '1. påskedag',
            strtotime('+1 day', $easter)        => '2. påskedag',
            strtotime('+49 days', $easter)      => '1. pinsedag',
            strtotime('+50 days', $easter)      => '2. pinsedag', 
            mktime(0, 0, 0, 12, 25, $year)      => '1. juledag',
            mktime(0, 0, 0, 12, 26, $year)      => '2. juledag'
        );

        $this->eves = array(
            strtotime('-1 day', $easter)        => 'Påskeaften',
            strtotime('+48 days', $easter)      => 'Pinseaften',
            mktime(0, 0, 0, 12, 24, $year)      => 'Julaften',
        );

        $this->newyear = array(
            mktime(0, 0, 0, 12, 31, $year)      => 'Nyttårsaften'
        );

        $this->other = array(
            mktime(0, 0, 0, 5, 1, $year)        => 'Arbeidernes internasjonale kampdag',
            mktime(0, 0, 0, 5, 17, $year)       => 'Grunnlovsdag',
            strtotime('+39 days', $easter)      => 'Kristi himmelfartsdag'
        );
    }

    // Creating dates from strtotime is heavy but convenient
    // Our trade-off is to do it as seldom as possible
    public static function getFromCache($year)
    {
        // Parse election dates from file
        if (self::$elections == null)
        {
            self::$elections = array();

            if (is_file('election.txt') && is_readable('election.txt') && ($handle = @fopen('election.txt', 'r')))
            {
                while (($line = fgets($handle, 13)) !== false)
                {
                    if (preg_match('((\\d{4})-(\\d{2})-(\\d{2}) (\\d{1}))', $line, $match))
                    {
                        self::$elections[mktime(0, 0, 0, $match[2], $match[3], $match[1])] = $match[4] == 1 ? 'Stortingsvalg' : 'Fylkesting- og kommunevalg';
                    }
                }
                fclose($handle);
            }
        }

        // Instantiate or load old instance
        if (!array_key_exists($year, self::$years))
        {
            self::$years[$year] = new self($year);
        }

        return self::$years[$year];
    }

    public function getDateInfo($date)
    {
        if (array_key_exists($date, $this->holidays))
        {
            return $this->holidays[$date];
        }

        if (array_key_exists($date, $this->eves))
        {
            return $this->eves[$date];
        }

        if (array_key_exists($date, $this->other))
        {
            return $this->other[$date];
        }

        if (array_key_exists($date, self::$elections))
        {
            return self::$elections[$date];
        }

        return null;
    }

    public function getDateType($date)
    {
        if (array_key_exists($date, $this->holidays))
        {
            return self::HOLIDAY;
        }

        if (array_key_exists($date, $this->eves))
        {
            return self::EVE;
        }

        if (array_key_exists($date, $this->newyear))
        {
            return self::NEWYEARS_EVE;
        }

        if (array_key_exists($date, $this->other))
        {
            return self::OTHER;
        }

        if (array_key_exists($date, self::$elections))
        {
            return self::ELECTION;
        }

        return self::NOTHING;
    }

    public function getIterator()
    {
        if ($this->aggregated == null)
        {
            $this->aggregated = $this->holidays + $this->eves + $this->newyear + $this->other;

            foreach (self::$elections as $date => $type)
            {
                if (date('Y', $date) == $this->year)
                {
                    $this->aggregated += array($date => $type);
                }
            }
            ksort($this->aggregated);
        }

        return new HolidaysIterator($this->aggregated);
    }
}

?>
