<?
namespace view;
use ctrl\Controller;
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

    public function asXml(Controller $resource = null)
    {
        $xml = new DOMDocument;
        $node = $xml->createElement("openinghours");
        $node->setAttribute("version", VERSION);
        $node->setAttribute("uri", $resource->getResourceUri($this->view->getDate()));

        $curr = $xml->createElement("current");
        $curr->appendChild($xml->importNode($this->view->asXml(), true));
        $node->appendChild($curr);

        $next = $xml->createElement("next");
        $next->appendChild($xml->importNode($this->next->asXml(), true));
        $node->appendChild($next);

        return $node;
    }

    public function asJson(Controller $resource = null)
    {
        return (object) array(
            'version' => VERSION,
            'openinghours' => (object) array(
                'current' => $this->view->asJson(),
                'next' => $this->next->asJson()
            )
        );
    }

    public function asText()
    {
        $line_curr = "I dag: ".$this->view->asText();
        $line_next = "Neste: ".$this->next->asText();

        $len = max(
            mb_strlen($line_curr, $this->getCharSet()),
            mb_strlen($line_next, $this->getCharSet())
        );

        $text = "Utsalgstider for øl og vinmonopol\n";
        $text .= str_repeat("=", $len) . "\n";
        $text .= $line_curr;
        $text .= $line_next;

        return $text;
    }
}

?>
