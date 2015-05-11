<?
require_once( 'http.php' );
require_once( 'mime.php' );

function MIME_TYPE($shortname, $default = MIME_TYPE_APP_JSON)
{
    $map = array(
        'json' => MIME_TYPE_APP_JSON,
        'xml' => MIME_TYPE_APP_XML,
        'txt' => MIME_TYPE_TEXT
    );

    return array_key_exists($shortname, $map) ? $map[$shortname] : $default;
}

function YEAR($year)
{
    if ($year == 0)
    {
        return date('Y');
    }

    if (2010 < $year && $year < 2072)
    {
        return (int) $year;
    }

    throw new BadRequest("Invalid year, must be in range 2010-2072");
}

function MONTH($month)
{
    $longnames = array(
        "january",
        "february",
        "march",
        "april",
        "may",
        "june",
        "july",
        "august",
        "september",
        "october",
        "november",
        "december"
    );

    $idx = array_search(strtolower($month), $longnames);
    if ($idx !== false)
    {
        return $idx + 1;
    }

    $shortnames = array(
        "jan",
        "feb",
        "mar",
        "apr",
        "may",
        "jun",
        "jul",
        "aug",
        "sep",
        "oct",
        "nov",
        "dec"
    );

    $idx = array_search(strtolower($month), $shortnames);
    if ($idx !== false)
    {
        return $idx + 1;
    }

    if ($month == 0)
    {
        return date('m');
    }


    if (1 <= $month && $month <= 12)
    {
        return (int) $month;
    }

    throw new BadRequest("Invalid month, must be in range 1-12");
}

function DAY($day, $month=0)
{
    if ($day == 0)
    {
        return date('d');
    }

    if (1 <= $day && $day <= 31)
    {
        if ($month > 1)
        {
            if ($month & 1 == 0 && $day > 30)
            {
                throw new \BadRequest("Invalid day, must be in range 1-30 for month $month");
            }
            if ($month == 2 && $day > 29)
            {
                throw new \BadRequest("Invalid day, must be in range 1-29 for month $month");
            }
        }

        return (int) $day;
    }

    throw new BadRequest("Invalid day, must be in range 1-31");
}

function WEEKDAY($timestamp)
{
    $days = array("Søndag", "Mandag", "Tirsdag", "Onsdag", "Torsdag", "Fredag", "Lørdag");
    return $days[date('w', $timestamp)];
}

function MONTHNAME($month)
{
    $months = array("Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember");
    return $months[$month-1];
}

?>
