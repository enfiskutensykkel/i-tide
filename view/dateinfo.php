<?
namespace view;
use \DOMDocument;
use ctrl\Hours;

final class DateInfo extends View
{
    const DATE_FORMAT = "Y-m-d";
    const TIME_FORMAT = "H:i";

    private $date;
    private $beer;
    private $wine;
    private $info;
    private $show_hours;
    private $show_info;

    private function __construct($date)
    {
        $this->date = $date;
        $this->beer = $this->wine = $this->info = null;
        $this->show_hours = $this->show_info = false;
    }

    public static function formatDate($timestamp)
    {
        return date(self::DATE_FORMAT, $timestamp);
    }

    public static function formatTime($timestamp)
    {
        return date(self::TIME_FORMAT, $timestamp);
    }

    private static function formatHours($hours)
    {
        return array(
            date(self::TIME_FORMAT, $hours->open), 
            date(self::TIME_FORMAT, $hours->close)
        );
    }

    private function getUrl()
    {
        return Hours::createResourceUrl('ctrl\Hours', date(self::DATE_FORMAT, $this->date));
    }

    public function getDate()
    {
        return date(self::DATE_FORMAT, $this->date);
    }

    //public static function withHours(&$hours)
    public static function withHours($hours)
    {
        $view = new self($hours->getDate());
        $view->info = $hours->getDateInfo();
        $view->show_info = true;

        $view->show_hours = true;
        $view->beer = $hours->getBeerHours();
        $view->wine = $hours->getWineHours();

        return $view;
    }

    public static function withoutInfo($date)
    {
        return new self($date);
    }

    public static function withoutHours($date, $info)
    {
        $view = new self($date);
        $view->info = $info;
        $view->show_info = true;
        return $view;
    }

    public function asXml(ResourceInfo &$resource)
    {
        $xml = new DOMDocument;
        $node = $xml->createElement("dateinfo");

        $node->setAttribute("url", $this->getUrl());
        $date = date(self::DATE_FORMAT, $this->date);
        $node->appendChild($xml->createElement("date", $date));

        if ($this->show_info)
        {
            $node->appendChild($xml->createElement("description", $this->info));
        }

        if ($this->show_hours)
        {
            $child = $xml->createElement("hours");
            $child->setAttribute("name", "beer");
            if ($this->beer != null)
            {
                $formatted = $this->formatHours($this->beer);
                $child->appendChild($xml->createElement("open", $formatted[0]));
                $child->appendChild($xml->createElement("close", $formatted[1]));
                unset($formatted);
            }
            $node->appendChild($child);

            $child = $xml->createElement("hours");
            $child->setAttribute("name", "wine");
            if ($this->wine != null)
            {
                $formatted = $this->formatHours($this->wine);
                $child->appendChild($xml->createElement("open", $formatted[0]));
                $child->appendChild($xml->createElement("close", $formatted[1]));
                unset($formatted);
            }
            $node->appendChild($child);
        }

        return $node;
    }

    public function asJson(ResourceInfo &$resource)
    {
        $date = date(self::DATE_FORMAT, $this->date);
        $node = array(
            'url' => $this->getUrl(),
            'date' => $date
        );

        if ($this->show_info)
        {
            $node['description'] = $this->info;
        }

        if ($this->show_hours)
        {
            $node['beer'] = null;
            if ($this->beer != null)
            {
                $formatted = $this->formatHours($this->beer);
                $node['beer'] = (object) array(
                    'open' => $formatted[0],
                    'close' => $formatted[1]
                );
                unset($formatted);
            }

            $node['wine'] = null;
            if ($this->wine != null)
            {
                $formatted = $this->formatHours($this->wine);
                $node['wine'] = (object) array(
                    'open' => $formatted[0],
                    'close' => $formatted[1]
                );
                unset($formatted);
            }
        }

        return (object) array(
            'dateinfo' => (object) $node
        );
    }

    public function asText(ResourceInfo &$resource)
    {
        $date = date(self::DATE_FORMAT, $this->date);
        $node = "$date ";
        $weekday = \WEEKDAY($this->date);
        $pad = 8 - mb_strlen($weekday);
        $node .= $weekday . str_repeat(" ", $pad);

        if ($this->show_hours)
        {
            $beer = "--:-- --:--";
            $wine = "--:-- --:--";

            if ($this->beer != null)
            {
                $formatted = $this->formatHours($this->beer);
                $beer = "$formatted[0] $formatted[1]";
                unset($formatted);
            }

            if ($this->wine != null)
            {
                $formatted = $this->formatHours($this->wine);
                $wine = "$formatted[0] $formatted[1]";
                unset($formatted);
            }

            $node .= "  ".str_pad($beer, 11) . "  ";
            $node .= "  ".str_pad($wine, 11) . "  ";
        }

        if ($this->info)
        {
            $node .= " ".$this->info;
        }

        return trim($node)."\n";
    }
}

?>
