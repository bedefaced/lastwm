<?
    class DatabaseHelper
    {
        private $db_host="localhost";
        private $db_login="lastwm";
        private $db_password="#############";
        private $db_name="lastwm";
        
        private $admin_mail="##########@#######";
        //private $sms_mail="###########@##############";
        
        private $my_connection;

        public function Connect()
        {
            $this->my_connection=@mysql_connect($this->db_host, $this->db_login, $this->db_password);
            if ($this->my_connection!==FALSE)
            {
                @mysql_selectdb($this->db_name, $this->my_connection);
            }
            else
                return FALSE;
        }
        
        public function Disconnect()
        {
            if ($this->my_connection===FALSE) return NULL;
            
            @mysql_close($this->my_connection);
        }
        
        public function AddOrder($lastfmuser, $months)
        {
            if (!ereg("^[[:alnum:]\_\-]{2,15}$",$lastfmuser)||!ereg("^[[:digit:]]{1,2}$",$months)) return FALSE;
            if ($this->my_connection===FALSE) return FALSE;
            
            @mysql_query("INSERT INTO `orders` (datetime, lastfmuser, months, paid, status) ".
                                      "VALUES (NOW(), '$lastfmuser', $months, 0, 0)", $this->my_connection);
            $my_val=@mysql_insert_id($this->my_connection);
            return $my_val;
        }
        
        public function GetOrderById($id)
        {
            if (!ereg("^[[:digit:]]+$",$id)) return FALSE;
            
            if ($this->my_connection===FALSE) return NULL;
            
            $my_resource=@mysql_query("SELECT * FROM `orders` WHERE id=$id", $this->my_connection);
            if (($my_resource===FALSE)||(mysql_affected_rows($this->my_connection)==0)) return NULL;
            
            return @mysql_fetch_assoc($my_resource);
        }
        
        public function GetOrder()
        {
            if ($this->my_connection===FALSE) return NULL;
            
            $my_resource=@mysql_query("SELECT * FROM `orders` WHERE paid=1 AND status=0 LIMIT 1", $this->my_connection);
            if (($my_resource===FALSE)||(mysql_affected_rows($this->my_connection)==0)) return NULL;
            
            return @mysql_fetch_assoc($my_resource);
        }
        
        public function UpdateOrder($id, $paid, $status)
        {
            if (!ereg("^[[:digit:]]+$",$id)||!ereg("^[[:digit:]]{1}$",$paid)||!ereg("^[[:digit:]]{1}$",$status)) return FALSE;
            
            if ($this->my_connection===FALSE) return FALSE;
            
            $my_resource=@mysql_query("UPDATE `orders` SET paid=$paid, status=$status WHERE id=$id", $this->my_connection);
            return $my_resource;
        }
        
        public function GetOrderStatus($id)
        {
            if (!ereg("^[[:digit:]]+$",$id)) return NULL;
            if ($this->my_connection===FALSE) return NULL;
            
            $my_resource=@mysql_query("SELECT status FROM `orders` WHERE id=$id", $this->my_connection);
            if ($my_resource===FALSE) return NULL;
            
            $value=@mysql_fetch_row($my_resource);
            if ($value===FALSE) return NULL;
            return $value[0];
        }
        
        public function GetPaidStatus($id)
        {
            if (!ereg("^[[:digit:]]+$",$id)) return NULL;
            if ($this->my_connection===FALSE) return NULL;
            
            $my_resource=@mysql_query("SELECT paid FROM `orders` WHERE id=$id", $this->my_connection);
            if ($my_resource===FALSE) return NULL;
            
            $value=@mysql_fetch_row($my_resource);
            if ($value===FALSE) return NULL;
            return $value[0];
        }
               
        public function GetTotalOrders()
        {
            if ($this->my_connection===FALSE) return NULL;
            
            $my_resource=@mysql_query("SELECT COUNT(*) FROM `orders` WHERE status=2", $this->my_connection);
            if ($my_resource===FALSE) return 0;
            
            $value=@mysql_fetch_row($my_resource);
            if ($value===FALSE) return NULL;
            return $value[0];
        }
        
        public function GetQueueOrders()
        {
            if ($this->my_connection===FALSE) return NULL;
            
            $my_resource=@mysql_query("SELECT COUNT(*) FROM `orders` WHERE status=0 AND paid=1 OR status=1", $this->my_connection);
            if ($my_resource===FALSE) return 0;
            
            $value=@mysql_fetch_row($my_resource);
            if ($value===FALSE) return NULL;
            return $value[0];
        }
        
        public function ErrorLog($text, $sms_text)
        {
            //echo $text;
            @mail($this->admin_mail, "[LASTWM Paymer] Error", "Error: $text");
            //@mail($this->sms_mail, "Error", $sms_text);
        }
                
        public function TryBetween($intext, $start, $end, $from)
        {
            $fromidx=strpos($intext,$from);
            if ($fromidx===FALSE) return FALSE;
            
            $startidx=strpos($intext,$start,$fromidx);
            if ($startidx===FALSE) return FALSE;
            else $startidx+=strlen($start);
            
            $endidx=strpos($intext,$end,$startidx);
            if ($endidx===FALSE) return FALSE;
            
            $outs = trim(substr($intext,$startidx,$endidx-$startidx));
            return $outs;
        }
    }
?>