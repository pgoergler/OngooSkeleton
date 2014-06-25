<?php

namespace Apps\Common\Models;

/**
 * Description of MailNotifier
 *
 * @author paul
 */
class MailNotifier implements NotifierInterface
{
    protected $app;
    protected $from;
    protected $to;
    protected $bcc;

    public function initialize(\Silex\Application &$app, $parameters)
    {
        $this->app = $app;
        $this->from = isset($parameters['from']) ? $parameters['from'] : 'root';
        $this->to = isset($parameters['to']) ? $parameters['to'] : 'root';
        $this->bcc = isset($parameters['bcc']) ? $parameters['bcc'] : '';
        $this->cc = isset($parameters['cc']) ? $parameters['cc'] : '';
    }

    public function notify($title, $message, $context = array(), $filename = null, $file = null)
    {
        $title = $this->app['interpolate']($title, $context);
        $text = $this->app['dump']($message, $context);

        $this->send($this->from, $this->to, $this->cc, $this->bcc, $title, $text, $filename, $file);
    }

    public function send($from, $to, $cc, $bcc, $title, $text, $filename = null, $file = null)
    {
        $from = is_null($from) ? $this->from : $from;
        $to = is_null($to) ? $this->to : $to;
        $cc = is_null($cc) ? $this->cc : $cc;
        $bcc = is_null($bcc) ? $this->bcc : $bcc;

        $files = $filename;
        if( !is_array($filename) )
        {
            $files = array(
                $filename => array(
                    'mime-type' => 'text/plain',
                    'filename' => $filename,
                    'content' => $file
                )
            );
        }
        
        return Mailer::sendFiles($to, $title, $text, $files, $cc, $bcc, $from);
    }

}

?>
