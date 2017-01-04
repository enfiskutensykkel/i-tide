<?php
namespace ctrl;
use view\ResourcesInfo;

final class Resources extends Controller
{
    public function __construct()
    {
        parent::registerGetHandler();

        $output = array();
        preg_match_all('/\.?\/?(\S+)/', shell_exec("find ./ -type l -name \"*.phps\""), $output);
        $output[1][] = "README";
        $output[1][] = "LICENSE";

        $sourceFiles = array_map(function ($elem) { return BASE_URL."/".$elem; }, $output[1]);

        $controllers = array(
            'ctrl\Status' => 'status',
            'ctrl\Hours' => 'saleshours', 
            'ctrl\Holidays' => 'interval', 
            'ctrl\Month' => 'interval'
        );
        
        $view = new ResourcesInfo($controllers, $sourceFiles);
        parent::registerXmlView($view);
        parent::registerJsonView($view);
    }

    public function handleGet()
    {
    }
}

?>
