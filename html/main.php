<?
function plural($number, $stem)
{
    $pl = substr($stem, -1) == "e" ? "r" : "er";
    echo $number != 1 ? "$number $stem$pl" : "$number $stem";
}

function fulldate($datestr, $year=false, $day=false, $lcase=false)
{
    $timestamp = strtotime($datestr);
    $l = !$lcase ? WEEKDAY($timestamp) : strtolower(WEEKDAY($timestamp));
    echo ($day ?  $l." " : "") . date('j', $timestamp) . ". " . strtolower(MONTH_NAME(date('m', $timestamp))) . ($year ? " ".date('Y', $timestamp) : "");
}

function monthyear($datestr)
{
    $t = strtotime($datestr);
    echo MONTH_NAME(date('m', $t)) . " " . date('Y', $t);
}

function getclass($hours)
{
    echo "";
}

function geturl($datestr)
{
    $date = explode("-", $datestr);
    $year = $date[0];
    $month = $date[1];
    $day = $date[2];
    echo "?year=$year&month=$month&day=$day";
}

function nextmonth($datestr, $url)
{
    $date = explode("-", $datestr);
    $year = $date[0];
    $month = (int) $date[1] - 1;
    $month = ($month + 1) % 12 + 1;
    if ($month == 1)
    {
        ++$year;
    }
    echo $url ? "?year=$year&month=$month&day=1" : MONTH_NAME($month);
}

function lastmonth($datestr, $url)
{
    $date = explode("-", $datestr);
    $year = $date[0];
    $month = (int) $date[1] - 1;
    $month = ($month - 1) % 12 + 1;
    if ($month == 0)
    {
        $month = 12;
        --$year;
    }
    echo $url ? "?year=$year&month=$month&day=1" : MONTH_NAME($month);
}
?>
<!DOCTYPE html>
<html lang="no">
    <head>
        <link rel="stylesheet" href="css/bootstrap.min.css" media="all"/>
        <script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
        <style>
            td, tr { cursor: default; }
            td a, a:hover, a:link, a:visited, a:focus, a:active { text-decoration: none; color: inherit; display: block;}
        </style>

        <title>Utsalgstider for alkohol</title>
        <meta charset="UTF-8">
        <meta name="description" content="En oversikt over utsalgstidene for alkohol">
        <meta name="author" content="Jonas Markussen">
        <meta name="keywords" content="øl, vin, vinmonopol, ølsalg, alkohol">
        <meta name="robots" content="NOARCHIVE, NOODP">
    </head>
    <body>
        <div class="container-fluid">
            <div class="page-header">
                <h1>iTiDE <small>Utsalgstider for alkohol</small></h1>
            </div>

            <div id="alert-area">
<? if ($data->today->note != null) : ?>
                <div role="alert" class="alert alert-warning">
                    <strong>Merk!</strong> <? fulldate($data->today->note->date, false, true); ?> er <? echo strtolower($data->today->note->info); ?>. Utsalget er stengt frem til <? fulldate($data->today->hours->next->dateinfo->date, false, true, true); ?>.
                </div>
<? endif; ?>
            </div>

            <div id="notification" class="well well-lg" style="min-width: 500px;">
                <div class="row">
                    <div class="col-xs-6">
                        <h2>
                            I dag
                            <small class="text-nowrap"><? fulldate($data->today->date); ?></small>
                        </h2>
<? if ($data->today->beer) : ?>
                        <p id="beer" class="<? getclass($data->today->beer); ?>">
<? if ($data->today->beer->hoursleft > 0) : ?>
                            <strong id="beerleft" class="text-nowrap"><? plural($data->today->beer->hoursleft, "time"); ?> og <? plural($data->today->beer->minsleft, "minutt"); ?></strong> til &oslash;lsalget stenger.
<? else : ?>
                            <strong id="beerleft" class="text-nowrap"><? plural($data->today->beer->minsleft, "minutt"); ?></strong> til &oslash;lsalget stenger!
<? endif; ?>
                        </p>
<? else : ?>
                        <p id="beer" class="text-muted">
                            &Oslash;lsalget er stengt.
                        </p>
<? endif; ?>

<? if ($data->today->wine) : ?>
                        <p id="wine" class="<? getclass($data->today->wine); ?>">
<? if ($data->today->wine->hoursleft > 0) : ?>
                            <strong id="wineleft" class="text-nowrap"><? plural($data->today->wine->hoursleft, "time"); ?> og <? plural($data->today->wine->minsleft, "minutt"); ?></strong> til vinmonopolet stenger.
<? else : ?>
                            <strong id="wineleft" class="text-nowrap"><? plural($data->today->wine->minsleft, "minutt"); ?></strong> til vinmonopolet stenger!
<? endif; ?>
                        </p>
<? else : ?>
                        <p id="wine" class="text-muted">
                            Vinmonopolet er stengt.
                        </p>
<? endif; ?>
                    </div>
                    <div class="col-xs-6">
                        <h2>
                            I morgen
                            <small class="text-nowrap"><? fulldate($data->tomorrow->date); ?></small>
                        </h2>
<? if ($data->tomorrow->beer) : ?>
                        <p>&Oslash;lsalget er &aring;pent fra <? echo $data->tomorrow->beer->open; ?> til <? echo $data->tomorrow->beer->close ?>.</p>
<? else : ?>
                        <p class="text-muted">&Oslash;lsalget er stengt.</p>
<? endif; ?>

<? if ($data->tomorrow->wine) : ?>
                        <p>Vinmonopolet er &aring;pent fra <? echo $data->tomorrow->wine->open; ?> til <? echo $data->tomorrow->wine->close ?>.</p>
<? else : ?>
                        <p class="text-muted">Vinmonopolet er stengt.</p>
<? endif; ?>
                    </div>
                </div>
            </div>

            <div class="container" style="min-width: 500px; max-width: 500px;">
                <div id="calendar" class="panel panel-primary">
                    <div class="panel-heading"><h1 class="panel-title"><? monthyear($data->selected->date); ?></h1></div>
                    <table class="table table-bordered table-condensed table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-nowrap text-center">Mandag</th>
                                <th class="text-nowrap text-center">Tirsdag</th>
                                <th class="text-nowrap text-center">Onsdag</th>
                                <th class="text-nowrap text-center">Torsdag</th>
                                <th class="text-nowrap text-center">Fredag</th>
                                <th class="text-nowrap text-center">L&oslash;rdag</th>
                                <th class="text-nowrap text-center">S&oslash;ndag</th>
                            </tr>
                        </thead>
                        <tbody>
<? foreach ($data->calendar as $idx => $day) : ?>
<? if ($idx % 7 == 0) : ?>
<? if ($idx > 0) : ?>
                            </tr>
<? endif; ?>
                            <tr>
                                <th class="text-center text-nowrap"><? echo date("W", strtotime($day->date)); ?></th>
<? endif; ?>

<? 
$bgclass = $day->warn ? "text-warning" : "";
$bgclass = $day->mark ? "text-danger" : $bgclass;
$bgclass = $day->date == $data->today->date ? "bg-info" : $bgclass; 
$bgclass = $day->date == $data->selected->date ? "bg-primary" : $bgclass;
?>

                                <td class="<? echo $bgclass; ?> text-nowrap text-center">
<? if (!$day->part) : ?>
                                    <span class="text-muted">
                                        <? echo date("j", strtotime($day->date)); ?>
                                    </span>
<? else : 
$title = "&#13;";
$title .= $day->beer ? "&Oslash;lutsalg: ".$day->beer->open." - ".$day->beer->close : "&Oslash;lutsalg: Stengt";
$title .= "&#13;";
$title .= $day->wine ? "Vinmonopolet: ".$day->wine->open." - ".$day->wine->close : "Vinmonopolet: Stengt";
?>
                                        <a href="<? geturl($day->date); ?>" title="<? fulldate($day->date, true, true); echo $title;?> ">
                                        <? echo date("j", strtotime($day->date)); ?>
                                    </a>
<?
unset($title);
endif; ?>
                                </td>
<? endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <ul class="pager">
                    <li><a href="<? lastmonth($data->selected->date, true); ?>"><span aria-hidden="true">&larr;</span> <? lastmonth($data->selected->date, false); ?></a></li>
                    <li><a href="<? geturl($data->today->date); ?>">I dag</a></li>
                    <li><a href="<? geturl($data->tomorrow->date); ?>">I morgen</a></li>
                    <li><a href="<? nextmonth($data->selected->date, true); ?>"><? nextmonth($data->selected->date, false); ?> <span aria-hidden="true">&rarr;</span></a></li>
              </ul>
            </div>

            <div class="container" style="min-width: 500px;">
                <div id="info" class="panel panel-default">
                    <div class="panel-heading"><h1 class="panel-title"><? fulldate($data->selected->date, true, true); ?></h1></div>
                    <div class="panel-body">
<? if ($data->selected->info) : ?>
                        <p>
                            <strong><? echo $data->selected->info; ?></strong>
                        </p>
<? endif; ?>

<? if ($data->selected->beer) : ?>
                        <p>&Oslash;lsalget er &aring;pent fra <? echo $data->selected->beer->open; ?> til <? echo $data->selected->beer->close; ?>.</p>
<? else : ?>
                        <p>&Oslash;lsalget er stengt.</p>
<? endif; ?>

<? if ($data->selected->wine) : ?>
                        <p>Vinmonopolet er &aring;pent fra <? echo $data->selected->wine->open; ?> til <? echo $data->selected->wine->close; ?>.</p>
<? else : ?>
                        <p>Vinmonopolet er stengt.</p>
<? endif; ?>

                        <p>
                            Neste mulige er <? fulldate($data->selected->next, false, true, true); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

<script type="text/javascript">
$(document).ready(function () {
    var update = function (cb) {
        $.ajax({
            url: '<? echo BASE_URL; ?>/res/status',
            dataType: 'json',
            success: function (data) {
                var beer = data.status.beer;
                var wine = data.status.wine;

                if (beer && beer.timeleft) {
                } else {
                }

                if (wine && wine.timeleft) {

                } else {
                    $("#wine").removeClass("text-warning text-danger").addClass("text-muted").text("Vinmonopolet er stengt");
                }

                if ((beer && beer.timeleft) || (wine && wine.timeleft)) {
                    window.setTimeout(function () { cb(cb); }, 1000);
                }
            }
        });
    };

    //update(update);
});
</script>
    </body>
</html>
