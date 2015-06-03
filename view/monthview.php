<?
namespace view;
use view\DateInfo;
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

    public function asXml(ResourceInfo &$res)
    {
        $xml = new DOMDocument;
        $root = $xml->createElement("interval");
        $root->setAttribute("version", VERSION);
        $root->setAttribute("url", $res->resource->getResourceUrl($this->year, $this->month));

        $node = $root->appendChild($xml->createElement("dates"));
        $node->setAttribute("start", DateInfo::formatDate($this->iterator->from));
        $node->setAttribute("end", DateInfo::formatDate($this->iterator->end));

        foreach ($this->iterator as $date => $hours)
        {
            $node->appendChild($xml->importNode(DateInfo::withHours($hours)->asXml($res), true));
        }

        return $root;
    }

    public function asJson(ResourceInfo &$res)
    {
        $list = array();
        foreach ($this->iterator as $date => $hours)
        {
            $list[] = DateInfo::withHours($hours)->asJson($res);
        }

        return (object) array(
            'version' => VERSION,
            'url' => $res->resource->getResourceUrl($this->year, $this->month),
            'interval' => (object) array(
                'url' => $res->resource->getResourceUrl($this->year, $this->month),
                'start' => DateInfo::formatDate($this->iterator->from),
                'end' => DateInfo::formatDate($this->iterator->to),
                'dates' => $list
            )
        );
    }

    public function asText(ResourceInfo &$res)
    {
        $list = "";
        $max = 0;

        foreach ($this->iterator as $date => $hours)
        {
            $line = DateInfo::withHours($hours)->asText($res);
            $max = max($max, mb_strlen($line));
            $list .= $line;
        }

        $text = "Utsalgstider for " . strtolower(\MONTH_NAME($this->month)) . " $this->year \n";
        $text .= str_repeat("=", $max) . "\n";
        $text .= $list;

        return $text;
    }

}

?>
