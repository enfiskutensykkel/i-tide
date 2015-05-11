<?
namespace view;
use \NotImplemented;
use \DOMDocument;

abstract class View
{
    public function getCharSet()
    {
        return "UTF-8";
    }

    public function asXml()
    {
        throw new NotImplemented("XML is not implemented for this resource");
    }

    public function asJson()
    {
        throw new NotImplemented("JSON is not implemented for this resource");
    }

    public function asText()
    {
        throw new NotImplemented("Plain text is not implemented for this resource");
    }
}

?>
