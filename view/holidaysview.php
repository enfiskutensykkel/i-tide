<?
namespace view;
use \DOMDocument;
use view\DateInfo;

final class HolidaysView extends View
{
    private $model;
    private $year;

    public function __construct($model, $year)
    {
        $this->year = $year;
        $this->model = $model->getIterator();
    }

    public function asJson(ResourceInfo &$res)
    {
        $data = array();

        foreach ($this->model as $date => $name)
        {
            $data[] = DateInfo::withoutHours($date, $name)->asJson($res);
        }

        return (object) array(
            'version' => VERSION,
            'url' => $res->resource->getResourceUrl($this->year),
            'interval' => (object) array(
                'url' => $res->resource->getResourceUrl($this->year),
                'start' => DateInfo::formatDate(mktime(0, 0, 0, 1, 1, $this->year)),
                'end' => DateInfo::formatDate(mktime(0, 0, 0, 12, 31, $this->year)),
                'dates' => $data
            )
        );
    }

    public function asXml(ResourceInfo &$res)
    {
        $xml = new DOMDocument;
        $root = $xml->createElement("interval");
        $root->setAttribute("version", VERSION);
        $root->setAttribute("uri", $res->resource->getResourceUrl($this->year));

        $node = $xml->createElement("dates");
        $node->setAttribute("start", DateInfo::formatDate(mktime(0, 0, 0, 1, 1, $this->year)));
        $node->setAttribute("end", DateInfo::formatDate(mktime(0, 0, 0, 12, 31, $this->year)));

        foreach ($this->model as $date => $name)
        {
            $node->appendChild($xml->importNode(DateInfo::withoutHours($date, $name)->asXml($res), true));
        }
        $root->appendChild($node);

        return $root;
    }

    public function asText(ResourceInfo &$res)
    {
        $list = "";
        $max = 0;

        foreach ($this->model as $date => $name)
        {
            $line = DateInfo::withoutHours($date, $name)->asText($res);
            $max = max($max, mb_strlen($line));
            $list .= $line;
        }

        $text = "Røde dager i $this->year\n" . str_repeat("=", $max) . "\n" . $list;
        return $text;
    }
}

?>
