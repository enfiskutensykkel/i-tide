<!DOCTYPE html>
<html lang="no">
    <head>
        <link rel="stylesheet" href="css/bootstrap.min.css"/>
        <link rel="stylesheet" href="css/main.css"/>
        <title>Utsalgstider for alkohol</title>
    </head>
    <body>
        <div id="container">
            <div id="status" class="jumbotron">
                <noscript>
                    <div class="alert alert-danger" role="alert">
                        <b>Merk:</b>
                        Nettleseren din st&oslash;tter ikke JavaScript.
                        Sanntidsinformasjon vil derfor ikke vises.
                    </div>
                </noscript>
                <h1>N&aring;</h1>
                <p>
                    &Oslash;lsalget er <? echo ""; ?>
                    <? var_dump($data['now']->status->beer); ?>
                </p>
            </div>            

            <div id="calendar">
            </div>
        </div>
    </body>
</html>
