<?php
namespace view;
use view\DateInfo;
use \DOMDocument;

final class HoursView extends View
{
    private $view;
    private $next;

    public function __construct($model)
    {
        $this->view = DateInfo::withHours($model);
        $next = $model->getNextPossible();
        $this->next = DateInfo::withHours($next);
    }

    public function asXml(ResourceInfo &$res)
    {
        $xml = new DOMDocument;
        $node = $xml->createElement("saleshours");
        $node->setAttribute("version", VERSION);
        $node->setAttribute("url", $res->resource->getResourceUrl($this->view->getDate()));

        $curr = $xml->createElement("current");
        $curr->appendChild($xml->importNode($this->view->asXml($res), true));
        $node->appendChild($curr);

        $next = $xml->createElement("next");
        $next->appendChild($xml->importNode($this->next->asXml($res), true));
        $node->appendChild($next);

        return $node;
    }

    public function asJson(ResourceInfo &$res)
    {
        return (object) array(
            'version' => VERSION,
            'url' => $res->resource->getResourceUrl($this->view->getDate()),
            'saleshours' => (object) array(
                'current' => $this->view->asJson($res),
                'next' => $this->next->asJson($res)
            )
        );
    }

    public function asText(ResourceInfo &$res)
    {
        $line_curr = "I dag: ".$this->view->asText($res);
        $line_next = "Neste: ".$this->next->asText($res);

        $len = max(
            mb_strlen($line_curr),
            mb_strlen($line_next)
        );

        $text = "Utsalgstider for øl og vinmonopol\n";
        $text .= str_repeat("=", $len) . "\n";
        $text .= $line_curr;
        $text .= $line_next;

        return $text;
    }

    public function asInfo(ResourceInfo &$res)
    {
        return (object) array(
            'current' => $this->view->asJson($res),
            'next' => $this->next->asJson($res),
        );
    }
}

?>
