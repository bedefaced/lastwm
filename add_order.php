<?
    include("database.php");
    include("config.php");

    header("Content-type: text/xml");
    echo "<?xml version=\"1.0\"?>";

    if (!isset($_GET['months'])||!isset($_GET['lastfmuser'])) die("<data><ok>0</ok><id>0</id></data>");
    
    $month_val=$_GET['months'];
    $lastfmuser=$_GET['lastfmuser'];      
    
    $month_i=array_search($month_val, $MONTHS);
    if ($month_i===FALSE) die("<data><ok>0</ok><id>0</id></data>");
    
    if (!ereg("^[[:alnum:]\_\-]{2,15}$",$lastfmuser)||!ereg("^[[:digit:]]{1,2}$",$month_val)) die("<data><ok>0</ok><id>0</id></data>");
    $database=new DatabaseHelper();
    $database->Connect();
    $id=$database->AddOrder($lastfmuser,$month_val);
    if ($id===FALSE) die("<data><ok>0</ok><id>0</id></data>");
    $database->Disconnect();
    
    echo "<data><ok>1</ok><id>$id</id></data>";
?>