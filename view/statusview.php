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
        $this->time = date("H:i:s", $timestamp);
        $this->date = date("Y-m-d", $timestamp);
        $this->data = $hours;

        $beer = $hours->getBeerHours();
        $wine = $hours->getWineHours();

        $this->beer = $beer != null && $beer->open <= $timestamp && $timestamp < $beer->close;
        $this->wine = $wine != null && $wine->open <= $timestamp && $timestamp < $wine->close;

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

        return (object) array(
            'version' => VERSION,
            'status' => (object) array(
                'uri' => $resource->getResourceUri(),
                'date' => $this->date,
                'time' => $this->time,
                'beer' => $this->beer,
                'wine' => $this->wine,
                'openinghours' => $view->asJson($resource)->openinghours,
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
        $xml->appendChild($doc->createElement("beer", $this->beer ? "open" : "closed"));
        $xml->appendChild($doc->createElement("wine", $this->wine ? "open" : "closed"));

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

    public function asText()
    {
        $beer = $this->beer ? "åpent" : "stengt";
        $wine = $this->wine ? "åpent" : "stengt";

        $possible = $this->data->getNextPossible();
        $curr = "I dag: " . DateInfo::withHours($this->data)->asText();
        $next = "Neste: " . DateInfo::withHours($possible)->asText();
        $info = $this->note != null ? \WEEKDAY($this->note->getDate()) . " er " . $this->note->getDateInfo() : "";
        $status = "Tid: $this->time   Ølutsalg: $beer   Vinmonopol: $wine\n";

        $len = max(
            mb_strlen($status, $this->getCharSet()), 
            mb_strlen($curr, $this->getCharSet()), 
            mb_strlen($next, $this->getCharSet()), 
            mb_strlen($info, $this->getCharSet())
        );

        $text = $status."\n";
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
