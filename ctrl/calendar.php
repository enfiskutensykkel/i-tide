<?
namespace ctrl;
use data\Hours;
use view\StatusView;
use view\CalendarView;

final class Calendar extends Controller
{
    private $timestamp;

    public function __construct()
    {
        parent::registerGetHandler();
        $this->timestamp = time();
    }

    public function handleGet()
    {
        parent::registerHtmlView("html/main.php", new CalendarView);
    }

    public function getStatusView()
    {
        return new StatusView($this->timestamp, Hours::createFromTimestamp($this->timestamp));
    }
}

?>
