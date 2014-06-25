<?php

namespace Apps\Common\Models;

/**
 *
 * @author paul
 */
interface NotifierInterface
{
    public function initialize(\Silex\Application &$app, $parameters);

    public function notify($title, $message, $context = array(), $filename = null, $file = null);

    public function send($from, $to, $cc, $bcc, $title, $text, $filename = null, $file = null);
}

?>
