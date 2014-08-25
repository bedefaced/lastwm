<?
    $service_works=TRUE;
    
    //ROBO
    $ROBOpass1="################################";
    $ROBOpass2="################################";
    $ROBOlogin="##########";
    $ROBOdefault="PCR";
    $ROBOculture="ru";
    $ROBOencoding="utf-8";
    
    //LASTFM
    $my_lastfmuser="##############";
    $my_lastfmpassword="#############";
    
    //PAYPAL
    $paypal_login="##################";
    $paypal_password="###############";

    $ORDER_STATUS_TEXT=array("в очереди","выполняется","выполнена","ошибка");

    $MONTHS=array(1,3,6,12);
    $MONTHS_TEXT=array("1 месяц","3 месяца","6 месяцев","12 месяцев");
    
    $month_pay_Z=3.0;
    $month_pay_R=96;
    $profit=0.20;
    
    $MONTH_PAY_Z=array();
    $MY_MONTH_PAY_Z=array();
    
    $MONTH_PAY_R=array();
    $MY_MONTH_PAY_R=array();
    
    for($i=0;$i<count($MONTHS);$i++)
    {
        $MONTH_PAY_Z[$i]=$month_pay_Z*$MONTHS[$i];
        $MY_MONTH_PAY_Z[$i]=$month_pay_Z*(1.0+$profit)*$MONTHS[$i];
        
        $MONTH_PAY_R[$i]=$month_pay_R*$MONTHS[$i];
        $MY_MONTH_PAY_R[$i]=$month_pay_R*(1.0+$profit)*$MONTHS[$i];
    }
?>