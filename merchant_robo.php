<?
    require("database.php");
    require("config.php");
    
    if (!isset($_POST['InvId'])) die('Error params');
    $id=$_POST['InvId'];
    
    if (!ereg("^[[:digit:]]+$",$id)) die('Error params');
    
    $db=new DatabaseHelper();
    $db->Connect();
    
    $data=$db->GetOrderById($id);
    if (($data!==NULL)&&($data!==FALSE))
    {
        $month_val=$data["MONTHS"];
        $lastfmuser=$data["LASTFMUSER"];
    }
    else die('Error params');
    
    $month_i=array_search($month_val, $MONTHS);
    if ($month_i===FALSE) die('Error params');

    $amount_z=strval(doubleval($MY_MONTH_PAY_Z[$month_i]));

    $crc = $_POST["SignatureValue"];    
    $crc = strtoupper($crc);    
    $my_crc = strtoupper(md5($_POST["OutSum"].":$id:$ROBOpass2"));
    
    if (($my_crc!=$crc)||($amount_z!=strval(doubleval($_POST["OutSum"])))) die("bad sign or amount");
    
    $db->UpdateOrder($id, 1, 0);
    echo "OK$id";
    
    $db->Disconnect();
?>