<?
namespace view;
use \DOMDocument;

final class ResourcesInfo extends View
{
    public function asJson(ResourceInfo &$resource)
    {
        return (object) array(
            'version' => VERSION,
            'url' => $resource->resource->getResourceUrl(),
            'resources' => array(
                (object) array(
                    'type' => "status",
                    'url' => $resource->resource->createResourceUrl('ctrl\Status')
                ),
                (object) array(
                    'type' => "saleshours",
                    'url' => $resource->resource->createResourceUrl('ctrl\Hours')
                ),
                (object) array(
                    'type' => "interval",
                    'url' => $resource->resource->createResourceUrl('ctrl\Holidays')
                ),
                (object) array(
                    'type' => "interval",
                    'url' => $resource->resource->createResourceUrl('ctrl\Month')
                ),
            )
        );
    }

    public function asXml(ResourceInfo &$resource)
    {
        $xml = new DOMDocument;

        $root = $xml->createElement("resources");
        $root->setAttribute("version", VERSION);
        $root->setAttribute("url", $resource->resource->getResourceUrl());

        $json = $this->asJson($resource);

        foreach ($json->resources as $endpoint)
        {
            $ep = $xml->createElement("resource");
            $ep->setAttribute("type", $endpoint->type);
            $ep->setAttribute("url", $endpoint->url);
            $root->appendChild($ep);
        }

        return $root;
    }

}

?>
