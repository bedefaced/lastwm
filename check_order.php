<?  
    $id=isset($_GET["id"])?$_GET["id"]:"";
    if (($id=="")||(!ereg("^[[:digit:]]+$",$id))) exit;
  
    include("database.php");  
    $database=new DatabaseHelper();
    $database->Connect();
    $status=$database->GetOrderStatus($id);
    if ($status===FALSE) $status=4;
    $database->Disconnect();
    
    header("Content-type: text/xml");
    echo "<?xml version=\"1.0\"?><data><status>$status</status></data>";
?>