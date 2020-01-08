<?php
function maketimeleft($timeleft)
{
    $hours = $timeleft->hoursleft != 1 ? "timer" : "time";
    $mins = $timeleft->minsleft != 1 ? "minutter" : "minutt";

    if ($timeleft->hoursleft > 0)
    {
        echo "$timeleft->hoursleft $hours og $timeleft->minsleft $mins";
    }
    else
    {
        echo "$timeleft->minsleft $mins";
    }
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
    $class = "";
    if ($hours->hoursleft == 0)
    {
        $class = "text-warning";
        if ($hours->minsleft <= 30)
        {
            $class = "text-danger";
        }
    }
    echo $class;
}

function makeurl($datestr)
{
    $date = explode("-", $datestr);
    $year = $date[0];
    $month = $date[1];
    $day = $date[2];
    echo BASE_URL."/$year-$month-$day";
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
    if ($url)
    {
        printf(BASE_URL."/%04d-%02d-01", $year, $month);
    }
    else
    {
        echo MONTH_NAME($month);
    }
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
    
    if ($url)
    {
        printf(BASE_URL."/%04d-%02d-01", $year, $month);
    }
    else
    {
        echo MONTH_NAME($month);
    }
}
?>
<!DOCTYPE html>
<html lang="no">
    <head>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/bootstrap.min.css" media="all"/>
        <script type="text/javascript" src="<?php echo BASE_URL; ?>/js/jquery-1.11.3.min.js"></script>
        <style>
            td, tr { cursor: default; }
            td a, td a:hover, td a:link, td a:visited, td a:focus, td a:active { text-decoration: none; color: inherit; display: block;}
        </style>

        <title>Utsalgstider for alkohol i Oslo</title>
        <meta charset="UTF-8">
        <meta name="description" content="En oversikt over utsalgstidene for alkohol">
        <meta name="author" content="Jonas Markussen">
        <meta name="keywords" content="øl, vin, vinmonopol, ølsalg, alkohol, vinmonopolet, butikktider, øl i butikk, butikk, utsalgstider, utsalg, ølutsalg, ølutsalgstider, ølsalgstider">
        <meta name="robots" content="NOARCHIVE, NOODP">
    </head>
    <body>
        <div class="container-fluid">
            <div class="page-header">
                <h1>iTiDE <small>Utsalgstider for alkohol i Oslo</small></h1>
            </div>

            <div id="alert-area">
<?php if ($data->today->note != null) : ?>
                <div role="alert" class="alert alert-warning">
                    <strong>Merk!</strong> <?php fulldate($data->today->note->date, false, true); ?> er <?php echo strtolower($data->today->note->info); ?>. Utsalget er stengt frem til <?php fulldate($data->today->note->next, false, true, true); ?>.
                </div>
<?php endif; ?>
            </div>

            <div id="notification" class="well well-lg" style="min-width: 500px;">
                <div class="row">
                    <div class="col-xs-6">
                        <h2>
                            I dag
                            <small class="text-nowrap"><?php fulldate($data->today->date); ?></small>
                        </h2>
<?php if ($data->today->beer) : ?>
                        <p id="beer" class="<?php getclass($data->today->beer); ?>">
                            <strong id="beerleft" class="text-nowrap"><?php maketimeleft($data->today->beer); ?></strong> til &oslash;lsalget stenger.
                        </p>
<?php else : ?>
                        <p id="beer" class="text-muted">
                            &Oslash;lsalget er stengt.
                        </p>
<?php endif; ?>

<?php if ($data->today->wine) : ?>
                        <p id="wine" class="<?php getclass($data->today->wine); ?>">
                            <strong id="wineleft" class="text-nowrap"><?php maketimeleft($data->today->wine); ?></strong> til vinmonopolet stenger.
                        </p>
<?php else : ?>
                        <p id="wine" class="text-muted">
                            Vinmonopolet er stengt.
                        </p>
<?php endif; ?>
                    </div>
                    <div class="col-xs-6">
                        <h2>
                            I morgen
                            <small class="text-nowrap"><?php fulldate($data->tomorrow->date); ?></small>
                        </h2>
<?php if ($data->tomorrow->beer) : ?>
                        <p>&Oslash;lsalget er &aring;pent fra <?php echo $data->tomorrow->beer->open; ?> til <?php echo $data->tomorrow->beer->close ?>.</p>
<?php else : ?>
                        <p class="text-muted">&Oslash;lsalget er stengt.</p>
<?php endif; ?>

<?php if ($data->tomorrow->wine) : ?>
                        <p>Vinmonopolet er &aring;pent fra <?php echo $data->tomorrow->wine->open; ?> til <?php echo $data->tomorrow->wine->close ?>.</p>
<?php else : ?>
                        <p class="text-muted">Vinmonopolet er stengt.</p>
<?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="container" style="min-width: 500px; max-width: 500px;">
                <div id="calendar" class="panel panel-primary">
                    <div class="panel-heading"><h1 class="panel-title"><?php monthyear($data->selected->date); ?></h1></div>
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
<?php foreach ($data->calendar as $idx => $day) : ?>
<?php if ($idx % 7 == 0) : ?>
<?php if ($idx > 0) : ?>
                            </tr>
<?php endif; ?>
                            <tr>
                                <th class="text-center text-nowrap"><?php echo date("W", strtotime($day->date)); ?></th>
<?php endif; ?>

<?php 
$bgclass = $day->warn ? "text-warning" : "";
$bgclass = $day->mark ? "text-danger" : $bgclass;
$bgclass = $day->date == $data->today->date ? "bg-info" : $bgclass; 
$bgclass = $day->date == $data->selected->date ? "bg-primary" : $bgclass;
?>

                                <td class="<?php echo $bgclass; ?> text-nowrap text-center">
<?php if (!$day->part) : ?>
                                    <span class="text-muted">
                                        <?php echo date("j", strtotime($day->date)); ?>
                                    </span>
<?php else : 
$title = "&#13;";
$title .= $day->beer ? "&Oslash;lutsalg: ".$day->beer->open." - ".$day->beer->close : "&Oslash;lutsalg: Stengt";
$title .= "&#13;";
$title .= $day->wine ? "Vinmonopolet: ".$day->wine->open." - ".$day->wine->close : "Vinmonopolet: Stengt";
?>
                                        <a href="<?php makeurl($day->date); ?>" title="<?php fulldate($day->date, true, true); echo $title;?> ">
                                        <?php echo date("j", strtotime($day->date)); ?>
                                    </a>
<?php
unset($title);
endif; ?>
                                </td>
<?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <ul class="pager">
                    <li><a href="<?php lastmonth($data->selected->date, true); ?>"><span aria-hidden="true">&larr;</span> <?php lastmonth($data->selected->date, false); ?></a></li>
                    <li><a href="<?php makeurl($data->today->date); ?>">I dag</a></li>
                    <li><a href="<?php makeurl($data->tomorrow->date); ?>">I morgen</a></li>
                    <li><a href="<?php nextmonth($data->selected->date, true); ?>"><?php nextmonth($data->selected->date, false); ?> <span aria-hidden="true">&rarr;</span></a></li>
              </ul>
            </div>

            <div class="container" style="min-width: 500px;">
                <div id="info" class="panel panel-default">
                    <div class="panel-heading"><h1 class="panel-title"><?php fulldate($data->selected->date, true, true); ?></h1></div>
                    <div class="panel-body">
<?php if ($data->selected->info) : ?>
                        <p>
                            <strong><?php echo $data->selected->info; ?></strong>
                        </p>
<?php endif; ?>

<?php if ($data->selected->beer) : ?>
                        <p>&Oslash;lsalget er &aring;pent fra <?php echo $data->selected->beer->open; ?> til <?php echo $data->selected->beer->close; ?>.</p>
<?php else : ?>
                        <p>&Oslash;lsalget er stengt.</p>
<?php endif; ?>

<?php if ($data->selected->wine) : ?>
                        <p>Vinmonopolet er &aring;pent fra <?php echo $data->selected->wine->open; ?> til <?php echo $data->selected->wine->close; ?>.</p>
<?php else : ?>
                        <p>Vinmonopolet er stengt.</p>
<?php endif; ?>

                        <p>
                            Neste mulige er <?php fulldate($data->selected->next, false, true, true); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

<script type="text/javascript">
$(document).ready(function () {
    var update = function (cb) {
        var makeclass = function (timeleft) {
            var time = timeleft.split(":");
            var hours = parseInt(time[0]);
            var mins = parseInt(time[1]);
            
            if (hours == 0) {
                if (mins <= 30) {
                    return "text-danger";
                }

                return "text-warning";
            }

            return "";
        };

        var maketext = function (timeleft) {
            var time = timeleft.split(":");
            var hours = parseInt(time[0]);
            var mins = parseInt(time[1]);

            var plural_mins = mins != 1 ? "er" : "";
            var plural_hours = hours != 1 ? "er" : "e";

            if (hours > 0) {
                return hours + " tim" + plural_hours + " og " + mins + " minutt" + plural_mins;
            }

            return mins + " minutt" + plural_mins;
        };

        $.ajax({
            url: '<?php echo BASE_URL; ?>/status',
            dataType: 'json',
            success: function (data) {
                var beer = data.status.beer;
                var wine = data.status.wine;

                if (beer && beer.timeleft) {
                    $("#beerleft").text(maketext(beer.remaining));
                    $("#beer").removeClass("text-warning text-danger").addClass(makeclass(beer.remaining));
                } else {
                    $("#beer").removeClass("text-warning text-danger").addClass("text-muted").text("Ølsalget er stengt.");
                }

                if (wine && wine.timeleft) {
                    $("#wineleft").text(maketext(wine.remaining));
                    $("#wine").removeClass("text-warning text-danger").addClass(makeclass(wine.remaining));
                } else {
                    $("#wine").removeClass("text-warning text-danger").addClass("text-muted").text("Vinmonopolet er stengt.");
                }

                if ((beer && beer.timeleft) || (wine && wine.timeleft)) {
                    window.setTimeout(function () { cb(cb); }, 15000);
                }
            }
        });
    };

<?php if ($data->today->beer || $data->today->wine) : ?>
    update(update);
<?php endif; ?>
});
</script>
    </body>
</html>
