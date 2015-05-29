<?
namespace view;
use ctrl\Controller;
use \NotImplemented;

final class ResourceInfo
{
    public function __construct(Controller &$resource)
    {
        $this->resource = $resource;
        $this->name = $resource->getResourceName();
    }

    public function __get($property)
    {
        if (isset($this->$property))
        {
            return $this->$property;
        }

        return null;
    }
}

abstract class View
{
    public function asXml(ResourceInfo &$resource)
    {
        throw new NotImplemented("XML is not implemented for resource '$resource->name'");
    }

    public function asJson(ResourceInfo &$resource)
    {
        throw new NotImplemented("JSON is not implemented for resource '$resource->name'");
    }

    public function asText(ResourceInfo &$resource)
    {
        throw new NotImplemented("Plain text is not implemented for resource '$resource->name'");
    }

    public function asInfo(ResourceInfo &$resource)
    {
        throw new NotImplemented("Model descriptor is not implemented for resource '$resource->name'");
    }
}

?>
