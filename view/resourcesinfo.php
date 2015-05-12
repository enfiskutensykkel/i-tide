<?
namespace view;
use ctrl\Controller;
use \DOMDocument;

final class ResourcesInfo extends View
{
    public function asJson(Controller $resource = null)
    {
        return (object) array(
            'version' => VERSION,
            'resources' => array(
                (object) array(
                    'type' => "status",
                    'uri' => $resource->createResourceUri('ctrl\Status')
                ),
                (object) array(
                    'type' => "saleshours",
                    'uri' => $resource->createResourceUri('ctrl\Hours')
                ),
                (object) array(
                    'type' => "interval",
                    'uri' => $resource->createResourceUri('ctrl\Holidays')
                ),
                (object) array(
                    'type' => "interval",
                    'uri' => $resource->createResourceUri('ctrl\Month')
                ),
            )
        );
    }

    public function asXml(Controller $resource = null)
    {
        $xml = new DOMDocument;

        $root = $xml->createElement("resources");
        $root->setAttribute("version", VERSION);
        $root->setAttribute("uri", $resource->getResourceUri());

        $json = $this->asJson($resource);

        foreach ($json->resources as $endpoint)
        {
            $ep = $xml->createElement("resource");
            $ep->setAttribute("type", $endpoint->type);
            $ep->setAttribute("uri", $endpoint->uri);
            $root->appendChild($ep);
        }

        return $root;
    }

}

?>
