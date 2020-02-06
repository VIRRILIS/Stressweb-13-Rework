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
class La2
{
    /**
     * Check server status. Return 'on' or 'off'
     * La2::GetStatus()
     *
     * @param mixed $host
     * @param mixed $port
     * @param integer $timeout
     * @return
     */
    public function GetStatus( $host, $port, $timeout = 1 )
    {
        $sock = @fsockopen( $host, $port, $errno, $errstr, $timeout );
        $online = ( $sock > 0 );
        if ( $online )
            @fclose( $sock );
        return $online ? "on":"off";
    }
    /**
     * Return Castle Name by $castle_id
     * La2::getCastleName()
     *
     * @param mixed $castle_id
     * @return
     */
    public function getCastleName( $castle_id )
    {
        $castleList = array( 1 => "Gludio", 2 => "Dion", 3 => "Giran", 4 => "Oren", 5 => "Aden", 6 => "Innadril", 7 => "Goddard", 8 => "Rune", 9 => "Schuttgart", );
        return isset( $castleList[$castle_id] ) ? $castleList[$castle_id]:"&nbsp;";
    }
    /**
     * Return formated date
     * La2::DateFormat()
     *
     * @param mixed $date
     * @param integer $timezone
     * @return
     */
    public function DateFormat( $date, $timezone = 0 )
    {
        if ( $date > 0 )
            return date( 'H\:i d M Y', intval(substr($date, 0, 10) + $timezone) );
        else
            return "n/a";
    }
    /**
     * Return formated count
     * La2::CountFormat()
     *
     * @param mixed $num
     * @return
     */
    public function CountFormat( $num )
    {
        if ( $num > 1 ) {
            return number_format( $num, 0, ".", "," );
        }
        return "";
    }
    /**
     * Return formated Online Time in hours and minutes
     * La2::OnlineTime()
     *
     * @param mixed $time
     * @return
     */
    public function OnlineTime( $time )
    {
        global $lang;
        if ( $time / 60 / 60 - 0.5 <= 0 )
            $onlinetimeH = 0;
        else
            $onlinetimeH = round( ($time / 60 / 60) - 0.5 );
        $onlinetimeM = round( (($time / 60 / 60) - $onlinetimeH) * 60 );
        return "{$onlinetimeH} {$lang["hours"]} {$onlinetimeM} {$lang["minutes"]}";
    }
    /**
     * Return encoded password by specified encode type
     * La2::PassEncode()
     *
     * @param mixed $pass
     * @param string $type
     * @return
     */
    public function PassEncode( $pass, $type = "sha1" )
    {
        if ( $type == "whirlpool" )
            return base64_encode( hash('whirlpool', $pass, true) );
        else
            return base64_encode( pack('H*', sha1(utf8_encode($pass))) );
    }
    /**
     * Return pagination
     * La2::PageList()
     *
     * @param mixed $url
     * @param mixed $numpages
     * @param mixed $page
     * @return
     */
    public function PageList( $url, $numpages, $page )
    {
        global $lang;
        $pager = "<div id='pager'>";
        if ( $numpages > 5 )
            $pager .= "<a href='{$url}1'>{$lang["start"]}</a>&nbsp;&nbsp;...&nbsp;&nbsp;";
        if ( $numpages > 5 ) {
            $start = $page - 2;
            $end = $page + 2;
            if ( $page - 1 == 1 ) {
                $start = 1;
                $end = $page + 3;
            }
            if ( $page - 1 == 0 ) {
                $start = 1;
                $end = $page + 4;
            }
            if ( $page == $numpages ) {
                $start = $page - 4;
                $end = $page;
            }
            if ( $page + 1 == $numpages ) {
                $start = $page - 3;
                $end = $page + 1;
            }
        } else {
            $start = 1;
            $end = $numpages;
        }
        for ( $i = $start; $i <= $end; $i++ ) {
            if ( $i == $page )
                $pager .= "<a class='nopager'>{$i}</a>\n";
            else
                $pager .= "<a href='{$url}{$i}'>{$i}</a>\n";
        }
        if ( $numpages > 5 )
            $pager .= "&nbsp;&nbsp;...&nbsp;&nbsp;<a href='{$url}{$numpages}'>{$lang["end"]}</a>";
        $pager .= "</div>";
        return $pager;
    }
}
?>