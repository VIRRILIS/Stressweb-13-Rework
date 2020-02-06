<?php
/**
 * STRESS WEB
 * @author S.T.R.E.S.S.
 * @copyright 2008 - 2012 STRESS WEB
 * @version 13
 * @web http://stressweb.ru
 */
if ( !defined("STRESSWEB") )
    die( "Access denied..." );

/**
 * Класс для отправки E-Mail
 * Email( $l2cfg, $is_html = false )
 * $l2cfg - массив с настройками
 * $is_html - true/false
 * @package R13
 * @author
 * @copyright 2012
 * @version $Id$
 * @access public
 */
class Email
{
    public $site_name = "";
    private $from = "";
    private $to = "";
    private $subject = "";
    private $message = "";
    private $header = "";
    private $error = "";
    public $mail_headers = "";
    public $bcc = false;
    private $html_mail = 0;
    private $charset = 'windows-1251';
    private $smtp_fp = false;
    public $smtp_msg = "";
    private $smtp_port = "";
    private $smtp_host = "localhost";
    private $smtp_user = "";
    private $smtp_pass = "";
    private $smtp_code = "";
    private $smtp_mail = "";
    public $send_error = false;
    private $eol = "\n";
    private $mail_method = 'mail';

    function __construct( $l2cfg, $is_html = false )
    {
        $this->mail_method = $l2cfg['mail_method'];
        $this->from = $l2cfg['mail_admin'];
        $this->charset = $l2cfg['mail_charset'];
        $this->site_name = $l2cfg['mail_from'];
        $this->smtp_mail = trim( $l2cfg['mail_smtpmail'] ) ? trim( $l2cfg['mail_smtpmail'] ):'';
        $this->smtp_host = $l2cfg['mail_smtphost'];
        $this->smtp_port = intval( $l2cfg['mail_smtpport'] );
        $this->smtp_user = $l2cfg['mail_smtpuser'];
        $this->smtp_pass = $l2cfg['mail_smtppass'];
        $this->html_mail = $is_html;
    }

    function compile_headers()
    {

        $this->subject = "=?".$this->charset."?b?".base64_encode( $this->subject )."?=";
        $from = "=?".$this->charset."?b?".base64_encode( $this->site_name )."?=";

        if ( $this->html_mail ) {
            $this->mail_headers .= "MIME-Version: 1.0".$this->eol;
            $this->mail_headers .= "Content-type: text/html; charset=\"".$this->charset."\"".$this->eol;
        } else {
            $this->mail_headers .= "MIME-Version: 1.0".$this->eol;
            $this->mail_headers .= "Content-type: text/plain; charset=\"".$this->charset."\"".$this->eol;
        }

        if ( $this->mail_method == 'smtp' ) {

            $this->mail_headers .= "Subject: ".$this->subject.$this->eol;

            if ( $this->to ) {
                $this->mail_headers .= "To: ".$this->to.$this->eol;
            }

        }

        $this->mail_headers .= "From: \"".$from."\" <".$this->from.">".$this->eol;

        $this->mail_headers .= "Return-Path: <".$this->from.">".$this->eol;
        $this->mail_headers .= "X-Priority: 3".$this->eol;
        $this->mail_headers .= "X-MSMail-Priority: Normal".$this->eol;
        $this->mail_headers .= "X-Mailer: SW PHP".$this->eol;
        if ( $this->bcc ) {
            $this->mail_headers .= "Bcc: ".$this->bcc."".$this->eol;
        }

    }

    function send( $to, $subject, $message, $bcc = false )
    {
        $this->to = preg_replace( "/[ \t]+/", "", $to );
        $this->from = preg_replace( "/[ \t]+/", "", $this->from );

        $this->to = preg_replace( "/,,/", ",", $this->to );
        $this->from = preg_replace( "/,,/", ",", $this->from );

        if ( $this->mail_method != 'smtp' )
            $this->to = preg_replace( "#\#\[\]'\"\(\):;/\$!Ј%\^&\*\{\}#", "", $this->to );
        else
            $this->to = '<'.preg_replace( "#\#\[\]'\"\(\):;/\$!Ј%\^&\*\{\}#", "", $this->to ).'>';

        $this->bcc = $bcc;

        $this->from = preg_replace( "#\#\[\]'\"\(\):;/\$!Ј%\^&\*\{\}#", "", $this->from );

        $this->subject = $subject;
        $this->message = $message;

        $this->message = str_replace( "\r", "", $this->message );
        if ( !$this->html_mail ) {
            $this->message = str_replace( array('<br>', '<br />'), '', $this->message );
        }

        $this->compile_headers();

        if ( ($this->to) and ($this->from) and ($this->subject) ) {
            if ( $this->mail_method != 'smtp' ) {

                if ( !@mail($this->to, $this->subject, $this->message, $this->mail_headers) ) {
                    $this->smtp_msg = "PHP Mail Error.";
                    $this->send_error = true;
                }

            } else {
                $this->smtp_send();
            }

        }

        $this->mail_headers = "";

    }

    function smtp_get_line()
    {
        $this->smtp_msg = "";

        while ( $line = fgets($this->smtp_fp, 515) ) {
            $this->smtp_msg .= $line;

            if ( substr($line, 3, 1) == " " ) {
                break;
            }
        }
    }

    function smtp_send()
    {
        $this->smtp_fp = @fsockopen( $this->smtp_host, intval($this->smtp_port), $errno, $errstr, 30 );

        if ( !$this->smtp_fp ) {
            $this->smtp_error( "Could not open a socket to the SMTP server" );
            return;
        }

        $this->smtp_get_line();

        $this->smtp_code = substr( $this->smtp_msg, 0, 3 );

        if ( $this->smtp_code == 220 ) {
            $data = $this->smtp_crlf_encode( $this->mail_headers."\n".$this->message );

            $this->smtp_send_cmd( "HELO ".$this->smtp_host );

            if ( $this->smtp_code != 250 ) {
                $this->smtp_error( "HELO" );
                return;
            }

            if ( $this->smtp_user and $this->smtp_pass ) {
                $this->smtp_send_cmd( "AUTH LOGIN" );

                if ( $this->smtp_code == 334 ) {
                    $this->smtp_send_cmd( base64_encode($this->smtp_user) );

                    if ( $this->smtp_code != 334 ) {
                        $this->smtp_error( "Username not accepted from the server" );
                        return;
                    }

                    $this->smtp_send_cmd( base64_encode($this->smtp_pass) );

                    if ( $this->smtp_code != 235 ) {
                        $this->smtp_error( "Password not accepted from the SMTP server" );
                        return;
                    }
                } else {
                    $this->smtp_error( "This SMTP server does not support authorisation" );
                    return;
                }
            }

            if ( !$this->smtp_mail )
                $this->smtp_mail = $this->from;

            $this->smtp_send_cmd( "MAIL FROM:<".$this->smtp_mail.">" );

            if ( $this->smtp_code != 250 ) {
                $this->smtp_error( "Incorrect FROM address: $this->smtp_mail" );
                return;
            }

            $this->smtp_send_cmd( "RCPT TO:".$this->to );

            if ( $this->smtp_code != 250 ) {
                $this->smtp_error( "Incorrect email address: $this->to" );
                return;

            }

            $this->smtp_send_cmd( "DATA" );

            if ( $this->smtp_code == 354 ) {
                fputs( $this->smtp_fp, $data."\r\n" );
            } else {
                $this->smtp_error( "Error on write to SMTP server" );
                return;
            }

            $this->smtp_send_cmd( "." );

            if ( $this->smtp_code != 250 ) {
                $this->smtp_error( "Error on send mail" );
                return;
            }

            $this->smtp_send_cmd( "quit" );

            if ( $this->smtp_code != 221 ) {
                $this->smtp_error( "Error on quit" );
                return;
            }

            @fclose( $this->smtp_fp );
        } else {
            $this->smtp_error( "SMTP service unaviable" );
            return;
        }
    }

    function smtp_send_cmd( $cmd )
    {
        $this->smtp_msg = "";
        $this->smtp_code = "";

        fputs( $this->smtp_fp, $cmd."\r\n" );

        $this->smtp_get_line();

        $this->smtp_code = substr( $this->smtp_msg, 0, 3 );

        return $this->smtp_code == "" ? false:true;
    }

    function smtp_error( $err = "" )
    {
        $this->smtp_msg = $err;
        $this->send_error = true;
        return;
    }

    function smtp_crlf_encode( $data )
    {
        $data .= "\n";
        $data = str_replace( "\n", "\r\n", str_replace("\r", "", $data) );
        $data = str_replace( "\n.\r\n", "\n. \r\n", $data );

        return $data;
    }
}
?>