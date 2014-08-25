<?
    $lastfmuser=isset($_GET["lastfmuser"])?$_GET["lastfmuser"]:"";
    $curl=FALSE;
    if ($lastfmuser!="")
        $curl=@curl_init();
        
    $ok=0;
    
    if (($curl!==FALSE)&&($lastfmuser!=""))
    {
        $curl_options=array(
            CURLOPT_AUTOREFERER => FALSE,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_USERAGENT => "Opera/9.80 (Windows NT 5.1; U; en) Presto/2.2.15 Version/10.10",
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HEADER => FALSE,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_MAXREDIRS => 1,
            CURLOPT_FRESH_CONNECT => FALSE
        );
        @curl_setopt_array($curl, $curl_options);
        echo curl_error($curl);
        @curl_setopt($curl, CURLOPT_URL, "http://www.last.fm/user/$lastfmuser");
        
        @curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
        @curl_setopt($curl, CURLOPT_POST, FALSE);
        
        $page=@curl_exec($curl);
        
        if (stripos($page,"<strong>$lastfmuser</strong>")!==FALSE) $ok=1;
        
        @curl_close($curl);
    }

    header("Content-type: text/xml");
    echo "<?xml version=\"1.0\"?><data><ok>$ok</ok></data>";
?>