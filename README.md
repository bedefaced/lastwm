lastwm
======

Sources of LastWM.Ru (LastWM.Com) service

Description
-----
Source code of LastWM service (lastwm.ru, lastwm.com). This site provided service for making automatic payments through PayPal for buying Last.Fm subscriptions. Clients made payments through Russian online payment aggregator (Robokassa), LastWM service processed orders and made payments to Last.Fm through the service's PayPal account.

Files
-----
  - **add_order.php** - creating order (ajax)
  - **check_lastfm.php** - checking Last.Fm account for existence (ajax)
  - **check_order.php** - checking order's status
  - **config.php** - payment accounts and finance settings
  - **database.php** - functions for order management
  - **index.php** - almost original web-page of LastWM service
  - **operation.php** - making payment to Last.Fm through PayPal using cURL; it was called by cron
  - **robo_genform.php** - generating parameters for Robokassa merchant (ajax)
  - _other files_ - css, scripts, images...

License
-------
[LGPLv3][1] (see `LICENSE` file)


  [1]: http://en.wikipedia.org/wiki/GNU_Lesser_General_Public_License
