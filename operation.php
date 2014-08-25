<?
    include("database.php");
    include("config.php");
    
    $database=new DatabaseHelper();
    
    if ($database->Connect()===FALSE)
    {
        $database->ErrorLog("Cannot connect to database.", "DB connect error");
        exit;
    }
    $order=$database->GetOrder();

    if ($order!==NULL)
    {
        $database->UpdateOrder($order['ID'], $order['PAID'], 1);
        $curl=FALSE;
        $curl=@curl_init();
        if ($curl!==FALSE)
        {
            $curl_options=array(
                CURLOPT_AUTOREFERER => FALSE,
                CURLOPT_FOLLOWLOCATION => TRUE,
                CURLOPT_HTTPGET => TRUE,
                CURLOPT_USERAGENT => "Opera/9.80 (Windows NT 6.0; U; en) Presto/2.6.30 Version/10.61",
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HEADER => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_COOKIEFILE => "",
                CURLOPT_MAXREDIRS => 1,
                CURLOPT_FRESH_CONNECT => FALSE,
                CURLOPT_COOKIESESSION => TRUE
            );
                                  
            @curl_setopt_array($curl, $curl_options);
            @curl_setopt($curl, CURLOPT_URL, "https://www.last.fm/login");
            //@curl_setopt($curl, CURLOPT_PROXY, "127.0.0.1:8888");
            @curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
            @curl_setopt($curl, CURLOPT_POST, FALSE);
            
            $page_loginpage=@curl_exec($curl);
            if ($page_loginpage!==FALSE)
            {
                //echo "we are on login page ";
                @curl_setopt($curl, CURLOPT_URL, "https://www.last.fm/login/");
                @curl_setopt($curl, CURLOPT_HTTPGET, FALSE);
                @curl_setopt($curl, CURLOPT_POST, TRUE);
                @curl_setopt($curl, CURLOPT_POSTFIELDS, "refererKey=&username=$my_lastfmuser&password=$my_lastfmpassword&login=Come+on+in");
                $page_loginresult=@curl_exec($curl);
                
                @curl_setopt($curl, CURLOPT_URL, "http://www.last.fm/home");
                @curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
                @curl_setopt($curl, CURLOPT_POST, FALSE);
                $page_loginresult=@curl_exec($curl);
                
                if (($page_loginresult!==FALSE)&&(strpos($page_loginresult, "Hi $my_lastfmuser")>0))
                {
                    //echo "we are on login result page ";
                    @curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
                    @curl_setopt($curl, CURLOPT_POST, FALSE);
                    @curl_setopt($curl, CURLOPT_URL, "http://www.last.fm/subscribe?gift=1");
                    $page_subscribe=@curl_exec($curl);
                    if ($page_subscribe!==FALSE)
                        $formtoken=$database->TryBetween($page_subscribe, "value=\"", "\"", "name=\"formtoken\"");
                    
                    if (($page_subscribe!==FALSE)&&(strpos($page_subscribe, "Buy Now")>0)&&($formtoken!==FALSE))
                    {
                        //echo "we are on subscribe page ";
                        @curl_setopt($curl, CURLOPT_HTTPGET, FALSE);
                        @curl_setopt($curl, CURLOPT_POST, TRUE);
                        @curl_setopt($curl, CURLOPT_URL, "http://www.last.fm/subscribe/paymentlevel2/");
                        @curl_setopt($curl, CURLOPT_POSTFIELDS, "formtoken=".urlencode($formtoken)."&username=".$order['LASTFMUSER'].
                                     "&currency=USD&type=one-off&nummonths=".$order['MONTHS'].
                                     "&terms=on&submit=Buy+Now");
                        
                        $page_subscribe2=@curl_exec($curl);
                        
                        //$database->ErrorLog($page_subscribe2, "Subscribe>Paypal");
                        
                        if ($page_subscribe2!==FALSE)
                            $encrypted=$database->TryBetween($page_subscribe2, "value=\"", "\"", "name=\"encrypted\"");
                        if (($page_subscribe2!==FALSE)&&($encrypted!==FALSE))
                        {
                                                                                                             
                            //echo "we are on redirector-to-paypal page ";
                            @curl_setopt($curl, CURLOPT_HTTPGET, FALSE);
                            @curl_setopt($curl, CURLOPT_POST, TRUE);
                            @curl_setopt($curl, CURLOPT_URL, "https://www.paypal.com/cgi-bin/webscr");
                            @curl_setopt($curl, CURLOPT_POSTFIELDS, "cmd=".urlencode("_s-xclick")."&encrypted=".urlencode($encrypted));
                                                       
                            $page_prepayment=@curl_exec($curl);
                            
                            //$database->ErrorLog($page_prepayment, "Page Pre-Payment");
                                                       
                            if ($page_prepayment!==FALSE)
                            {
                                $actionurl=$database->TryBetween($page_prepayment, "action=\"", "\"", "name=\"billing_form\"");
                                $CONTEXT=$database->TryBetween($page_prepayment, "value=\"", "\"", "name=\"CONTEXT\"");
                                $currentSession=$database->TryBetween($page_prepayment, "value=\"", "\"", "name=\"currentSession\"");
                                $currentDispatch=$database->TryBetween($page_prepayment, "value=\"", "\"", "name=\"currentDispatch\"");
                                $SESSION=$database->TryBetween($page_prepayment, "value=\"", "\"", "id=\"pageSession\"");
                                $dispatch=$database->TryBetween($page_prepayment, "value=\"", "\"", "name=\"dispatch\"");
                            }
                            
                            if (($page_prepayment!==FALSE)&&($actionurl!==FALSE)&&($CONTEXT!==FALSE)&&
                                ($currentSession!==FALSE)&&($currentDispatch!==FALSE)&&($SESSION!==FALSE)&&($dispatch!==FALSE))
                            {
                                $post="cmd=_flow&myAllTextSubmitID=&login_button=Have%20a%20PayPal%20account%3F%20&".
                                "currentSession=".urlencode($currentSession)."&pageState=billing&currentDispatch=".
                                urlencode($currentDispatch)."&refresh_country_code=0&country_code=GB&cc_brand=&".
                                "credit_card_type=&cc_country_code=GB&cc_brand=&shadow_bank_acct_routing_number=".
                                "&shadow_bank_acct_account_number=&shadow_cc_number=&first_name=&last_name=&address1=".
                                "&address2=&city=&state=&zip=&H_PhoneNumber=&email=&signUpButtonLabelexpd=".
                                "Agree%20and%20Continue&signUpButtonLabelcol=Continue&back-button-form-fields=".
                                "&javascript_enabled=false&SESSION=".urlencode($SESSION)."&dispatch=".
                                urlencode($dispatch)."&pageServerName=merchantpaymentweb&CONTEXT=".urlencode($CONTEXT).
                                "&cmd=_flow&id=&note=&close_external_flow=false&external_close_account_payment_flow=".
                                "payment_flow&form_charset=UTF-8";
                                
                                @curl_setopt($curl, CURLOPT_HTTPGET, FALSE);
                                @curl_setopt($curl, CURLOPT_POST, TRUE);
                                @curl_setopt($curl, CURLOPT_URL, html_entity_decode($actionurl));
                                @curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
                                $page_prepayment=@curl_exec($curl);

                            }
                            else
                            {
                                $database->ErrorLog("Cannot navigate to paypal login page for cookies!\r\n\r\n".$page_prepayment, "Cannot navigate to PP pre-payment page.");
                                $database->UpdateOrder($order['ID'], $order['PAID'], 0);
                                exit;
                            }
                                                       
                            if ($page_prepayment!==FALSE)
                            {
                                $actionurl=$database->TryBetween($page_prepayment, "action=\"", "\"", "name=\"login_form\"");
                                $CONTEXT=$database->TryBetween($page_prepayment, "value=\"", "\"", "name=\"CONTEXT\"");
                                $currentSession=$database->TryBetween($page_prepayment, "value=\"", "\"", "name=\"currentSession\"");
                                $currentDispatch=$database->TryBetween($page_prepayment, "value=\"", "\"", "name=\"currentDispatch\"");
                                $SESSION=$database->TryBetween($page_prepayment, "value=\"", "\"", "id=\"pageSession\"");
                                $dispatch=$database->TryBetween($page_prepayment, "value=\"", "\"", "name=\"dispatch\"");
                            }
                            
                            if (($page_prepayment!==FALSE)&&($actionurl!==FALSE)&&($CONTEXT!==FALSE)&&
                                ($currentSession!==FALSE)&&($currentDispatch!==FALSE)&&($SESSION!==FALSE)&&($dispatch!==FALSE))
                            {
                                //$database->ErrorLog($page_prepayment, "Page Prepayment");
                                @curl_setopt($curl, CURLOPT_HTTPGET, FALSE);
                                @curl_setopt($curl, CURLOPT_POST, TRUE);
                                @curl_setopt($curl, CURLOPT_URL, html_entity_decode($actionurl));
                                //login operation here
                                $post="cmd=_flow&myAllTextSubmitID=&miniPager=&currentSession=".urlencode($currentSession).
                                "&pageState=login&currentDispatch=".urlencode($currentDispatch)."&flag_non_js=true".
                                "&email_recovery=false&password_recovery=false&login_email=".urlencode($paypal_login).
                                "&login_password=".urlencode($paypal_password)."&login.x=Log+In&SESSION=".urlencode($SESSION).
                                "&dispatch=".urlencode($dispatch)."&CONTEXT=".urlencode($CONTEXT)."&cmd=_flow&id=&close_external_flow=".
                                "false&external_close_account_payment_flow=payment_flow&form_charset=UTF-8";
                                
                                @curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
                                
                                $page_paymentresult=@curl_exec($curl);
                                if ($page_paymentresult!==FALSE)
                                {
                                    //echo "page confirmation";
                                    //$database->ErrorLog($page_paymentresult, "Page Confirmation");
                                    if (strpos($page_paymentresult, "error")===FALSE)
                                    {
                                        if ($page_paymentresult!==FALSE)
                                        {
                                            $actionurl=$database->TryBetween($page_paymentresult, "action=\"", "\"", "name=\"reviewForm\"");
                                            $CONTEXT=$database->TryBetween($page_paymentresult, "value=\"", "\"", "name=\"CONTEXT\"");
                                            //$funding_type=$database->TryBetween($page_paymentresult, "value=\"", "\"", "name=\"funding_type\"");
                                            //$funding_bufs=$database->TryBetween($page_paymentresult, "value=\"", "\"", "name=\"funding_bufs\"");
                                            $funding_source_id=$database->TryBetween($page_paymentresult, "value=\"", "\"", "name=\"funding_source_id\"");
                                            $currentSession=$database->TryBetween($page_paymentresult, "value=\"", "\"", "name=\"currentSession\"");
                                            $currentDispatch=$database->TryBetween($page_paymentresult, "value=\"", "\"", "name=\"currentDispatch\"");
                                            $SESSION=$database->TryBetween($page_paymentresult, "value=\"", "\"", "id=\"pageSession\"");
                                            $dispatch=$database->TryBetween($page_paymentresult, "value=\"", "\"", "name=\"dispatch\"");
                                        }
                                        
                                        if (($page_paymentresult!==FALSE)&&(strpos($page_paymentresult, "name=\"reviewForm\"")>0)
                                            &&($actionurl!==FALSE)&&($CONTEXT!==FALSE)&&($funding_source_id!==FALSE)&&($SESSION!==FALSE)&&
                                            ($currentDispatch!==FALSE)&&($currentSession!==FALSE)&&($dispatch!==FALSE))
                                        {
                                            @curl_setopt($curl, CURLOPT_HTTPGET, FALSE);
                                            @curl_setopt($curl, CURLOPT_POST, TRUE);
                                            @curl_setopt($curl, CURLOPT_URL, html_entity_decode($actionurl));
                                            
                                            $post="cmd=_flow&myAllTextSubmitID=&CONTEXT=".urlencode($CONTEXT)."&miniPager=&".
                                            "funding_type=C&funding_bufs=&currentSession=".urlencode($currentSession).
                                            "&pageState=review&currentDispatch=".urlencode($currentDispatch).
                                            "&SESSION=".urlencode($SESSION)."&dispatch=".urlencode($dispatch).
                                            "&pageServerName=merchantpaymentweb&funding_source_id=".urlencode($funding_source_id).
                                            "&CONTEXT=".urlencode($CONTEXT)."&continue=Pay+Now&form_charset=UTF-8";
                                
                                            @curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
                                            
                                            $page_paymentresult2=@curl_exec($curl);
                                            //$database->ErrorLog($page_paymentresult2,"Payment Result 2");
                                            //echo "process complete";
                                            if ((strpos($page_paymentresult2, "error")===FALSE)&&(strpos($page_paymentresult2, "Sorry")===FALSE))
                                            {
                                                $database->UpdateOrder($order['ID'], $order['PAID'], 2);
                                            }
                                            else
                                            {
                                                $database->ErrorLog("Paypal account does not work!\r\n\r\n".$page_paymentresult."\r\n\r\n".$page_paymentresult2, "PAYPAL DOESN'T WORK!!!");
                                                $database->UpdateOrder($order['ID'], $order['PAID'], 0);
                                            }
                                        }
                                        else $database->ErrorLog("Cannot navigate to paypal payment confirmation page!\r\n\r\n".$page_paymentresult, "Payment confirm page error!");
                                    }
                                    else
                                    {
                                        $database->ErrorLog("Paypal account does not work!\r\n\r\n".$page_paymentresult, "PAYPAL DOESN'T WORK!!!");
                                        $database->UpdateOrder($order['ID'], $order['PAID'], 0);
                                    }
                                }
                                else
                                {
                                    $database->ErrorLog("Cannot navigate to paypal payment page!\r\n\r\n".$page_paymentresult, "Cannot navigate to PP payment page.");
                                    $database->UpdateOrder($order['ID'], $order['PAID'], 0);
                                }
                            }
                            else
                            {
                                $database->ErrorLog("Cannot navigate to paypal pre-payment page!\r\n\r\n".$page_prepayment, "Cannot navigate to PP pre-payment page.");
                                $database->UpdateOrder($order['ID'], $order['PAID'], 0);
                            }
                        }
                        else
                        {
                            $database->ErrorLog("Cannot navigate to payment level 2 page!\r\n\r\n".$page_subscribe2, "Cannot navigate to PP level 2 page.");
                            $database->UpdateOrder($order['ID'], $order['PAID'], 0);
                        }
                    }
                    else
                    {
                        $database->ErrorLog("Cannot navigate to subscription page!\r\n\r\n".$page_subscribe, "Cannot navigate to subscription page.");
                        $database->UpdateOrder($order['ID'], $order['PAID'], 0);
                    }
                }
                else
                {
                    $database->ErrorLog("Cannot login request!\r\n\r\n".curl_error($curl), "Cannot login request.");
                    $database->UpdateOrder($order['ID'], $order['PAID'], 0);
                }
            }
            else
            {
                $database->ErrorLog("Cannot perform first page request!\r\n\r\n".curl_error($curl), "Cannot navigate first page!");
                $database->UpdateOrder($order['ID'], $order['PAID'], 0);
            }
            @curl_close($curl);
        }
        else
        {
            $database->ErrorLog("Cannot use cURL!", "CANNOT USE CURL!!!");
            $database->UpdateOrder($order['ID'], $order['PAID'], 0);
        }
    }
    $database->Disconnect();
?>