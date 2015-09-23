<?
namespace view;
use \DOMDocument;

final class ResourcesInfo extends View
{
    public function asJson(ResourceInfo &$resource)
    {
        $output = array();
        preg_match_all('/\.?\/?(\S+)/', shell_exec("find ./ -type l -name \"*.phps\""), $output);
        $output[1][] = "README";
        $output[1][] = "LICENSE";

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
            ),
            'files' => array_map(function ($elem) { return BASE_URL."/".$elem; }, $output[1]),
        );
    }

    public function asXml(ResourceInfo &$resource)
    {
        $xml = new DOMDocument;
        $json = $this->asJson($resource);

        $root = $xml->createElement("resources");
        $root->setAttribute("version", $json->version);
        $root->setAttribute("url", $json->url);

        foreach ($json->resources as $endpoint)
        {
            $ep = $xml->createElement("resource");
            $ep->setAttribute("type", $endpoint->type);
            $ep->setAttribute("url", $endpoint->url);
            $root->appendChild($ep);
        }

        $filelist = $xml->createElement("files");
        foreach ($json->files as $file)
        {
            $fileNode = $xml->createElement("file", $file);
            $filelist->appendChild($fileNode);
        }
        $root->appendChild($filelist);

        return $root;
    }

    public function asInfo(ResourceInfo &$resource)
    {
        return $this->asJson($resource)->resources;
    }
}

?>
