<?
    include("database.php");
    include("config.php");
    
    $paid=FALSE;
    $database=new DatabaseHelper();
    $database->Connect();
    
    if (isset($_GET["paid"])&&($_GET["paid"]=='1')) 
    {
        $id=FALSE;
        if (isset($_POST['id'])&&(ereg("^[[:digit:]]+$",$_POST['id'])))
            $id=$_POST['id'];
            
        if (isset($_POST['InvId'])&&(ereg("^[[:digit:]]+$",$_POST['InvId'])))
            $id=$_POST['InvId'];
            
        if ($id!==FALSE)
        {
            $paid=($database->GetPaidStatus($id)==1);
        }
    }  

    $TOTALCOUNT=$database->GetTotalOrders();
    $QUEUECOUNT=$database->GetQueueOrders();
    if ($paid==TRUE)
    {
        $order_status=$database->GetOrderStatus($id);
        $status_text=$ORDER_STATUS_TEXT[$order_status];
    }
    $database->Disconnect();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
    <head>
        <title>LastWM.Ru - Оплата подписки Last.fm через WebMoney, Яндекс.Деньги, RBK Money, SMS, терминалы</title>
        <meta name="keywords" content="lastwm.ru, оплата last.fm, lasfm webmoney, last fm webmoney, lastfm, подписка за webmoney, яндекс.деньги, автоматическая оплата, last.fm, lastfm.ru, subscribe, lastwm, оплата lastfm, last fm оплата, подписка lastfm яндекс, как оплатить last.fm, оплата last.fm webmoney, оплатить last.fm, last fm оплата через яндекс, last fm подписка, last fm подписка webmoney, last.fm webmoney, last.fm подписка, lastfm wabmany, webmoney оплата через смс, webmoney last.fm, webmoney last fm, как оплатить ласт фм, как оплатить подписку lastfm в россии, как оплатить подписку в last.fm, ласт фм оплата вебмани,  можно ли оплатить last.fm через webmoney, опалата last fm webmoney, оплата last.fm c помощью яндекса, оплата ласт фм, оплата через смс на last.fm">
        <meta name="description" content="Сервис автоматической оплаты подписки Last.fm через WebMoney, Яндекс.Деньги, RBK Money, Деньги@Mail.Ru, MoneyMail, SMS, банкомат, терминалы">
        <meta http-equiv="Content-Language" content="ru">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="icon" href="http://www.last.fm/favicon.ico" type="image/x-icon">
        <link rel="SHORTCUT ICON" href="http://www.last.fm/favicon.ico">
        <script language="javascript" type="text/javascript" src="jquery-1.4.2.min.js"></script>
        <script type="text/javascript" language="JavaScript" src="jquery-ui-1.8.4.custom.min.js"></script>
        <link href="styles.css" rel="stylesheet" type="text/css">
        <link type="text/css" href="css/blitzer/jquery-ui-1.8.4.custom.css" rel="stylesheet">
    </head>
    
    <body>
    <? if (isset($service_works)&&($service_works===FALSE))
    {
    ?>
    <div id="overlay">
        <p class="error">
            Сервис временно отключён. Обращайтесь в ICQ #-###-### для ручной активации подписки.
        </p>
    </div>
    <?
    }
    ?>
    <div id="left">&nbsp;</div>
    <div id="content">
        
        <div id="title">
                <div style="float: left;">
                <a href="http://www.lastwm.ru/"><img src="logo.png" border="0" width="313" height="57"></a>
                </div>
                            
                <div style="float: left; clear: both; width: 100%;">
                <small>Сервис автоматической оплаты подписки <i>Last.fm</i> через WebMoney, <br>Яндекс.Деньги, RBK Money, Деньги@Mail.Ru, MoneyMail, SMS, банкомат, терминалы</small>
                </div>
        </div>
        
        <div id="body">
            
            <div id="order">
            <?
            if ($paid==TRUE)
            {
            ?>
            <table style="border: 1px solid black;" cellpadding=0 cellspacing=0 width="100%">
                <tr><td class="tableheader" colspan=2>Статус заявки</td></tr>
                <tr><td colspan=2>&nbsp;</td></tr>
                <tr><td width="40%">Статус заявки:</td><td>
                    <input type="text" style="width: 130px;" id="statustext" value="<? echo $status_text; ?>" disabled>
                    <label id="checkresult"></label>
                </td></tr>
                <tr><td colspan=2>&nbsp;</td></tr>
                <tr><td></td><td><input type="button" id="updatestatus" value="Обновить статус" style="width: 120px;"></td></tr>
                <tr><td colspan=2>&nbsp;</td></tr>
            </table>
            <script language="javascript" type="text/javascript">
            $("#updatestatus").click( function() {
                $("#checkresult").html('&nbsp;<img src="ajax-loader.gif" border="0">');
                $("#updatestatus").attr("disabled","disabled");
                $.get('check_order.php', {id: '<? echo $id; ?>'}, function(data) {
<?
                for($i=0; $i<count($ORDER_STATUS_TEXT); $i++)
                {
                    echo "\t\tif ($(data).find('status').text()=='$i')\r\n";
                    echo "\t\t\t$(\"#statustext\").val('".$ORDER_STATUS_TEXT[$i]."');\r\n";
                }
                ?>
                $("#updatestatus").attr("disabled","");
                $("#checkresult").html("");
                },'xml');
            });
            </script>
            <?
            }
            else
            {
            ?>
            <input type="hidden" name="id" id="id">
            <label id="dialogurl" style="display: none"></label>
            <table style="border: 1px solid black;" cellpadding=0 cellspacing=0 width="100%">
                <tr><td class="tableheader" colspan=2>Новая заявка</td></tr>
                <tr><td colspan=2>&nbsp;</td></tr>
                <tr><td width="40%">Last.fm логин:</td><td>
                <input type="text" name="lastfmuser" id="lastfmuser" style="width: 150px;">&nbsp;<label id="checkresult"></label></td></tr>
                <tr><td colspan=2>&nbsp;</td></tr>
                <tr><td>Срок оплаты:</td><td>
                <select name="months" id="months" style="width: 210px;">
                    <?
                        for($i=0;$i<count($MONTHS);$i++)
                            echo "<option value=\"".$MONTHS[$i]."\">".$MONTHS_TEXT[$i]." / ".$MY_MONTH_PAY_Z[$i].
                            " $ / ".$MY_MONTH_PAY_R[$i]." руб.</option>";
                    ?>
                </select>
                </td></tr>
                <tr><td colspan=2>&nbsp;</td></tr>
                <tr><td colspan=2 align="right"><input type="button" id="paybutton" style="margin-right: 12px;" value="Перейти к оплате" onclick="Validate();">
                <tr><td colspan=2>&nbsp;</td></tr>
            </table>
            <br>
            <small>Активация подписки выполняется в <b>автоматическом</b> режиме в течение <b>3-4 минут</b> после оплаты.</small>
            <br><br>
            <?
                if (isset($_GET["paid"])&&($_GET["paid"]==0))
                echo "<u>Оплата заявки была отменена или произошла ошибка.</u>";
            ?>
            <script language="javascript" type="text/javascript">
            
            var checked='';
            var submit=false;
            var month_pay_z=new Array();
            var month_pay_r=new Array();
            
<?
            for($i=0;$i<count($MONTHS);$i++)
            {
                echo "\t\tmonth_pay_z.push(".$MY_MONTH_PAY_Z[$i].");\r\n";
                echo "\t\tmonth_pay_r.push(".$MY_MONTH_PAY_R[$i].");\r\n\r\n";
            }
            ?>
            
            function Check()
            {
                if (checked==$("#lastfmuser").val()) return true;
                checked='';
                $("#checkresult").html('&nbsp;<img src="ajax-loader.gif" border="0">');
                $("#paybutton").attr("disabled","disabled");
                $.get('check_lastfm.php', {lastfmuser: $("#lastfmuser").val()}, function(data) {
                    if ($(data).find('ok').text()=='1')
                    {
                        $("#checkresult").html('<font color="green">ok<\/font>');
                        checked=$("#lastfmuser").val();
                        $("#paybutton").attr("disabled","");
                        if (submit==true) Validate();
                    }
                    else
                    {
                        $("#checkresult").html('<font color="red">ошибка<\/font>');
                        submit=false;
                        $("#paybutton").attr("disabled","");
                    }
                },'xml');
            }
                       
            function Validate()
            {
                if (checked!=$("#lastfmuser").val()) {
                    submit=true;
                    Check();
                    return false;
                }
           
                $("#paybutton").attr("disabled","disabled");
                $.get("add_order.php",{lastfmuser: $("#lastfmuser").val(), months: $("#months").val()}, function(data){
                    
                if ($(data).find('ok').text()=='1')
                    {
                        $("#id").val($(data).find('id').text());
                        $("#dialogurl").load("robo_genform.php",{lastfmuser: $("#lastfmuser").val(),
                                                              months: $("#months").val(),
                                                              id: $("#id").val()},
                                                              function(){
                            window.location=$("#dialogurl").text();
                        });
                    }
                    else
                    {
                        $("#checkresult").html('<font color="red">ошибка<\/font>');
                        $("#paybutton").attr("disabled","");
                    }
                }, 'xml');
                
                return false;
            }
                       
            //костыль для firefox
            $(function()
            {
                $("#paybutton").attr("disabled","");
            });
            
            </script>
            <?
            }
            ?>
            </div>
            
            <div id="stats">
            <table style="border: 1px solid black;" cellpadding=0 cellspacing=0 width="100%">
                <tr><td class="tableheader">Статистика сервиса</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td>Заявок в очереди: <? echo $QUEUECOUNT; ?></td></tr>
                <tr><td>Заявок выполнено: <? echo $TOTALCOUNT; ?></td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td>Сервис работает с 01.06.10</td></tr>
                <tr><td><!--<font color="red">Сервис временно работает в ручном режиме. Заявки исполняются с задержкой.</font>-->&nbsp;</td></tr>
            </table>
            <br><br>
            </div>
            
        </div>
        
        <div id="footer">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Здесь Вы можете оплатить подписку <b>Last.fm</b> различными способами (WebMoney, Яндекс.Деньги, SMS, терминалы и т.д.).
            <a href="http://last.fm"><b>Last.fm</b></a> — интернет-проект музыкальной тематики, основным сервисом
            которого является сбор информации о музыке, которую слушает пользователь, и её каталогизация в индивидуальных и общих чартах.
            Возможности, предоставляемые <b>платной подпиской</b>: радио без ограничений, трансляция музыки и просмотр сайта без рекламы,
            доступ к VIP-зоне. Подробнее на <a href="http://www.lastfm.ru/subscribe">официальном сайте</a>.<br><br>
            &copy; <a href="#">WebSystems</a>, 2010-2011 &nbsp; <img src="icq.png" border="0" alt="ICQ">
            <script language="javascript" type="text/javascript">
            document.write("#-"+"###-#"+"##");
            </script>
            &nbsp;
            <img src="email.png" border="0" alt="E-mail">
            <script language="javascript" type="text/javascript">
            document.write("<a href='mailto:supp"+"ort@#####"+"#####.##'>sup"+"port@##########."+"##<\/a>");
            </script>
            &nbsp;
            <img src="twitter.gif" border="0" alt="Твиттер сервиса LastWM">
            <a href="http://twitter.com/lastwm" target="_blank">lastwm</a>
        </div>    
    </div>
    <div id="right">&nbsp;</div>
    </body>
</html>