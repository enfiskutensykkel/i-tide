<?

define( 'MIME_TYPE_ALL', "*/*" );
define( 'MIME_TYPE_APP_JSON', "application/json" );
define( 'MIME_TYPE_APP_XML', "application/xml" );
define( 'MIME_TYPE_APP_HTML', "application/xhtml+xml" );
define( 'MIME_TYPE_TEXT_JSON', "text/json" );
define( 'MIME_TYPE_TEXT_XML', "text/xml" );
define( 'MIME_TYPE_TEXT_HTML', "text/html" );
define( 'MIME_TYPE_TEXT', "text/plain" );
define( 'MIME_TYPE_JSON', MIME_TYPE_APP_JSON );
define( 'MIME_TYPE_XML', MIME_TYPE_APP_XML );
define( 'MIME_TYPE_HTML', MIME_TYPE_TEXT_HTML );

function MIME_TYPE($shortname, $default = MIME_TYPE_APP_JSON)
{
    $map = array(
        'json' => MIME_TYPE_APP_JSON,
        'xml' => MIME_TYPE_APP_XML,
        'txt' => MIME_TYPE_TEXT
    );

    return array_key_exists($shortname, $map) ? $map[$shortname] : $default;
}

?>
