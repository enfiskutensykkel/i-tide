<?
namespace view;
use ctrl\Controller;
use view\HoursView;
use \DOMDocument;

final class StatusView extends View
{
    private $time;
    private $date;
    private $data;
    private $beer;
    private $wine;
    private $note;

    public function __construct($timestamp, $hours)
    {
        $this->time = DateInfo::formatTime($timestamp);
        $this->date = DateInfo::formatDate($timestamp);
        $this->data = $hours;
        $this->beer = $hours->getRemainingBeerTime($timestamp);
        $this->wine = $hours->getRemainingWineTime($timestamp);
        $this->note = $hours->getUpcomingEvent();
    }

    public function asJson(Controller $resource = null)
    {
        $note = null;
        if ($this->note != null)
        {
            $note = DateInfo::withoutHours($this->note->getDate(), $this->note->getDateInfo())->asJson();
        }
        $view = new HoursView($this->data);

        if ($this->beer != 0)
        {
            $beer = (object) array(
                'timeleft' => true,
                'remaining' => DateInfo::formatTime($this->beer)
            );
        }
        else
        {
            $beer = (object) array(
                'timeleft' => false,
                'remaining' => null
            );
        }

        if ($this->wine != 0)
        {
            $wine = (object) array(
                'timeleft' => true,
                'remaining' => DateInfo::formatTime($this->wine)
            );
        }
        else
        {
            $wine = (object) array(
                'timeleft' => false,
                'remaining' => null
            );
        }

        return (object) array(
            'version' => VERSION,
            'status' => (object) array(
                'uri' => $resource->getResourceUri(),
                'date' => $this->date,
                'time' => $this->time,
                'beer' => $beer,
                'wine' => $wine,
                'saleshours' => $view->asJson($resource)->saleshours,
                'note' => $note
            )
        );
    }

    public function asXml(Controller $resource = null)
    {
        $doc = new \DOMDocument;
        $xml = $doc->createElement("status");
        $xml->setAttribute("version", VERSION);
        $xml->setAttribute("uri", $resource->getResourceUri());

        $xml->appendChild($doc->createElement("date", $this->date));
        $xml->appendChild($doc->createElement("time", $this->time));

        $beer = $doc->createElement("beer");
        if ($this->beer != 0)
        {
            $beer->setAttribute("timeleft", "true");
            $beer->appendChild($doc->createTextNode(DateInfo::formatTime($this->beer)));
        }
        else
        {
            $beer->setAttribute("timeleft", "false");
        }
        $xml->appendChild($beer);

        $wine = $doc->createElement("wine");
        if ($this->wine != 0)
        {
            $wine->setAttribute("timeleft", "true");
            $wine->appendChild($doc->createTextNode(DateInfo::formatTime($this->wine)));
        }
        else
        {
            $wine->setAttribute("timeleft", "false");
        }
        $xml->appendChild($wine);

        $view = new HoursView($this->data);
        $xml->appendChild($doc->importNode($view->asXml($resource), true));

        $note = $doc->createElement("note");
        if ($this->note != null)
        {
            $note->appendChild($doc->importNode(DateInfo::withoutHours($this->note->getDate(), $this->note->getDateInfo())->asXml(), true));
        }
        $xml->appendChild($note);

        return $xml;
    }

    private static function formatts($ts)
    {
        return intval(date("H", $ts))."t ".intval(date("i", $ts))."m";
    }

    public function asText()
    {
        $beer = $this->beer != null ? self::formatts($this->beer) : "stengt";
        $wine = $this->wine != null ? self::formatts($this->wine) : "stengt";

        $curr = "I dag: " . DateInfo::withHours($this->data)->asText();
        $next = "Neste: " . DateInfo::withHours($this->data->getNextPossible())->asText();
        $info = $this->note != null ? \WEEKDAY($this->note->getDate()) . " er " . $this->note->getDateInfo() : "";
        $status = "Ølutsalg: $beer\nVinmonopol: $wine\n";
  
        $len = max(
            mb_strlen($curr, $this->getCharSet()), 
            mb_strlen($next, $this->getCharSet()), 
            mb_strlen($info, $this->getCharSet())
        );

        $date = $this->data->getDate();
        $text = \WEEKDAY($date).", ".date("j", $date).". ".strtolower(\MONTHNAME(date('m',$date)))." ".date('Y',$date).", kl. $this->time\n";
        $text .= $status."\n";
        $text .= "Utsalgstider for øl og vinmonopol\n";
        $text .= str_repeat("=", $len)."\n";
        $text .= $curr;
        $text .= $next;

        if ($info)
        {
            $text .= str_repeat("-", $len)."\n";
            $text .= "Merk: $info\n";
        }

        return $text;
    }
}

?>
