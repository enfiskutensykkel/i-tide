<?
namespace view;
use view\DateInfo;
use ctrl\Controller;
use \DOMDocument;

final class MonthView extends View
{
    private $iterator;
    private $year;
    private $month;

    public function __construct($model, $year, $month)
    {
        $this->iterator = $model;
        $this->year = $year;
        $this->month = $month;
    }

    public function asXml(Controller $resource = null)
    {
        $xml = new DOMDocument;
        $root = $xml->createElement("interval");
        $root->setAttribute("version", VERSION);
        $root->setAttribute("uri", $resource->getResourceUri($this->year, $this->month));

        $node = $root->appendChild($xml->createElement("dates"));
        $node->setAttribute("start", DateInfo::formatDate($this->iterator->from));
        $node->setAttribute("end", DateInfo::formatDate($this->iterator->end));

        foreach ($this->iterator as $date => $hours)
        {
            $node->appendChild($xml->importNode(DateInfo::withHours($hours)->asXml(), true));
        }

        return $root;
    }

    public function asJson(Controller $resource = null)
    {
        $list = array();
        foreach ($this->iterator as $date => $hours)
        {
            $list[] = DateInfo::withHours($hours)->asJson();
        }

        return (object) array(
            'version' => VERSION,
            'interval' => (object) array(
                'uri' => $resource->getResourceUri($this->year, $this->month),
                'start' => DateInfo::formatDate($this->iterator->from),
                'end' => DateInfo::formatDate($this->iterator->to),
                'dates' => $list
            )
        );
    }

    public function asText()
    {
        $list = "";
        $max = 0;

        foreach ($this->iterator as $date => $hours)
        {
            $line = DateInfo::withHours($hours)->asText();
            $max = max($max, mb_strlen($line, "UTF-8"));
            $list .= $line;
        }

        $text = "Utsalgstider for " . strtolower(\MONTHNAME($this->month)) . " $this->year \n";
        $text .= str_repeat("=", $max) . "\n";
        $text .= $list;

        return $text;
    }
}

?>
