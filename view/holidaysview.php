<?
namespace view;
use \DOMDocument;
use view\DateInfo;
use ctrl\Controller;

final class HolidaysView extends View
{
    private $model;
    private $year;

    public function __construct($model, $year)
    {
        $this->year = $year;
        $this->model = $model->getIterator();
    }

    public function asJson(Controller $res = null)
    {
        $data = array();

        foreach ($this->model as $date => $name)
        {
            $data[] = DateInfo::withoutHours($date, $name)->asJson();
        }

        return (object) array(
            'version' => VERSION,
            'interval' => (object) array(
                'uri' => $res->getResourceUri($this->year),
                'start' => DateInfo::formatDate(mktime(0,0,0,1,1,$this->year)),
                'end' => DateInfo::formatDate(mktime(0,0,0,12,31,$this->year)),
                'dates' => $data
            )
        );
    }

    public function asXml(Controller $res = null)
    {
        $xml = new DOMDocument;
        $root = $xml->createElement("interval");
        $root->setAttribute("version", VERSION);
        $root->setAttribute("uri", $res->getResourceUri($this->year));

        $node = $xml->createElement("dates");
        $node->setAttribute("start", DateInfo::formatDate(mktime(0,0,0,1,1,$this->year)));
        $node->setAttribute("end", DateInfo::formatDate(mktime(0,0,0,12,31,$this->year)));

        foreach ($this->model as $date => $name)
        {
            $node->appendChild($xml->importNode(DateInfo::withoutHours($date, $name)->asXml($xml), true));
        }
        $root->appendChild($node);

        return $root;
    }

    public function asText()
    {
        $list = "";
        $max = 0;

        foreach ($this->model as $date => $name)
        {
            $line = DateInfo::withoutHours($date, $name)->asText();
            $max = max($max, mb_strlen($line, $this->getCharSet()));
            $list .= $line;
        }

        $text = "Røde dager i $this->year\n" . str_repeat("=", $max) . "\n" . $list;
        return $text;
    }
}

?>
