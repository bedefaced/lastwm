<?
    require("config.php");
    
    if (!isset($_POST['months'])||!isset($_POST['lastfmuser'])||!isset($_POST['id'])) die('Error params');
    $month_val=$_POST['months'];
    $lastfmuser=$_POST['lastfmuser'];
    $id=$_POST['id'];
    
    if (!ereg("^[[:alnum:]\_\-]{2,15}$",$lastfmuser)||!ereg("^[[:digit:]]{1,2}$",$month_val)
        ||!ereg("^[[:digit:]]+$",$id)) die('Error params');
    
    $month_i=array_search($month_val, $MONTHS);
    if ($month_i===FALSE) die('Error params');
    
    $crc=md5("$ROBOlogin:".$MY_MONTH_PAY_Z[$month_i].":$id:$ROBOpass1");
    
    echo "http://merchant.roboxchange.com/Index.aspx?".
      "MrchLogin=$ROBOlogin&OutSum=".$MY_MONTH_PAY_Z[$month_i]."&InvId=$id&IncCurrLabel=$ROBOdefault".
      "&Desc=".urlencode("Subscribe ($month_val months) for $lastfmuser")."&SignatureValue=$crc".
      "&Culture=$ROBOculture&Encoding=$ROBOencoding";
?>