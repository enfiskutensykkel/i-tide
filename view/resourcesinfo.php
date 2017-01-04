<?php
namespace view;
use \DOMDocument;

final class ResourcesInfo extends View
{
    private $files = null;
    private $resources = null;

    public function __construct($resources, $files)
    {
        $this->files = $files;
        $this->resources = $resources;
    }

    public function asJson(ResourceInfo &$resource)
    {
        $endpoints = array();
        foreach ($this->resources as $ctrl => $type)
        {
            $endpoints[] = (object) array(
                'type' => $type,
                'url' => $resource->resource->createResourceUrl($ctrl)
            );
        }

        return (object) array(
            'version' => VERSION,
            'url' => $resource->resource->getResourceUrl(),
            'resources' => $endpoints,
            'files' => $this->files
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
