<?
namespace view;

final class CalendarView extends View
{
    public function asInfo(ResourceInfo &$res)
    {
        return array(
            'now' =>$res->resource->getStatusView()->asJson($res)
        );
    }
}
?>
