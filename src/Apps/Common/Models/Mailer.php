<?php

namespace Apps\Common\Models;

/**
 * Description of Mailer
 *
 * @author paul
 */
class Mailer
{

    public static function send($to, $title, $text, $filename = null, $csv = null, $cc = null, $bcc = null)
    {
        $cc = $cc ? implode(",\r\ncc: ", array_map('trim', explode(",", $cc))) : null;
        $bcc = $bcc ? implode(",\r\nbcc :", array_map('trim', explode(",", $bcc))) : null;

        if (!is_null($filename) && !is_null($csv))
        {
            $boundary = '=====' . md5(uniqid()) . '==';
            $headers = "From: no-reply@egallys.com\r\n" .
                    "MIME-Version: 1.0\r\n" .
                    ($cc ? "cc: $cc\r\n" : "" ) .
                    ($bcc ? "Bcc: $bcc\r\n" : "" ) .
                    "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n\n";

            $body = "--" . $boundary . "\n" .
                    "Content-Type: text/plain; charset=\"utf-8\"\r\n\n" .
                    "$text\n" .
                    "--" . $boundary . "\n" .
                    "Content-Type: text/plain; name=\"" . $filename . "\"\r\n" .
                    "MIME-Version: 1.0\r\n" .
                    "Content-Transfer-Encoding: base64\r\n" .
                    "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\n" .
                    chunk_split(base64_encode($csv)) . "\n" .
                    "--" . $boundary . "--";
        } else
        {
            $headers = "Content-Type: text/plain; charset=\"utf-8\"\r\n" .
                    "Content-Transfer-Encoding: 8bit\r\n" .
                    "From: no-reply@egallys.com\r\n" .
                    ($cc ? "cc: $cc\r\n" : "" ) .
                    ($bcc ? "Bcc: $bcc\r\n" : "" ) .
                    "MIME-Version: 1.0\r\n\n";

            $body = "$text";
        }
        mail($to, $title, $body, $headers, "-f no-reply@egallys.com");
    }

    public static function sendFiles($to, $title, $text, array $files = null, $cc = null, $bcc = null, $from = 'no-reply@egallys.com')
    {
        $cc = $cc ? implode(",\r\ncc: ", array_map('trim', explode(",", $cc))) : null;
        $bcc = $bcc ? implode(",\r\nbacc: ", array_map('trim', explode(",", $bcc))) : null;

        $boundary = '=====' . md5(uniqid()) . '==';
        $headers = "From: no-reply@egallys.com\r\n" .
                "MIME-Version: 1.0\r\n" .
                ($cc ? "cc: $cc\r\n" : "" ) .
                ($bcc ? "Bcc: $bcc\r\n" : "" ) .
                "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n\n";

        $body = "--" . $boundary . "\n" .
                "Content-Type: text/plain; charset=\"utf-8\"\r\n\n" .
                "$text\n";
        if (is_array($files))
        {
            foreach ($files as $filename => $content)
            {
                $mimeType = 'text/plain';
                if (is_array($content))
                {
                    $mimeType = isset($content['mime-type']) ? $content['mime-type'] : $mimeType;
                    $filename = isset($content['filename']) ? $content['filename'] : $filename;
                    $content = isset($content['content']) ? $content['content'] : null;
                }


                $body .= "--" . $boundary . "\n" .
                        "Content-Type: $mimeType; name=\"" . $filename . "\"\r\n" .
                        "MIME-Version: 1.0\r\n" .
                        "Content-Transfer-Encoding: base64\r\n" .
                        "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\n" .
                        chunk_split(base64_encode($content)) . "\n";
            }
        }
        $body .= "--" . $boundary . "--";
        mail($to, $title, $body, $headers, "-f '$from'");
    }

}

?>
