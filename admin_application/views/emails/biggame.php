<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Kizzang</title>
    </head>
    <body style="font-family:Arial, Helvetica, sans-serif;">
        <div class="body" style="box-shadow:0px 0px 2px 2px #cecece;border: 1px solid #000;max-width:600px; min-width:320px;width:100%; margin: 0 auto; min-height:300px;background: #e7e6e9; 
        /* Old browsers */background: -moz-linear-gradient(top,  #e7e6e9 0%, #889099 48%, #889099 53%, #4e227b 100%); 
        /* FF3.6+ */background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#e7e6e9), color-stop(48%,#889099), color-stop(53%,#889099), color-stop(100%,#4e227b)); 
        /* Chrome,Safari4+ */background: -webkit-linear-gradient(top,  #e7e6e9 0%,#889099 48%,#889099 53%,#4e227b 100%); 
        /* Chrome10+,Safari5.1+ */background: -o-linear-gradient(top,  #e7e6e9 0%,#889099 48%,#889099 53%,#4e227b 100%); 
        /* Opera 11.10+ */background: -ms-linear-gradient(top,  #e7e6e9 0%,#889099 48%,#889099 53%,#4e227b 100%); 
        /* IE10+ */background: linear-gradient(to bottom,  #e7e6e9 0%,#889099 48%,#889099 53%,#4e227b 100%); 
        /* W3C */filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e7e6e9', endColorstr='#4e227b',GradientType=0 ); /* IE6-9 */">
            <div class="img-wrap" style="max-width: 263px; min-width: 150px;width: 80%; display: block; margin: 0 auto 0; padding-top: 10px;">
                <img class="img" src="https://d1w836my735uqw.cloudfront.net/email_kizzang_logo.png" style="width:100%;" alt="Kizzang" />
            </div>
            <div class="content" style="background-color: #fff;max-width: 525px;min-width: 300px;width: 80%;margin: 10px auto 5px;box-shadow: 0px 0px 20px #000;min-height: 600px;display: block;border-radius: 5px;border: 2px solid #a0abb7;">                
                <?php foreach($cards as $card) : ?>
                <div class="img-wrap" style="max-width:338px;min-width: 280px; width: 80%;height:252px;margin: 20px auto;display:block;">
                    <img class="img" src="https://d1w836my735uqw.cloudfront.net/logos/big_game_30_email_logo.png"  alt="Big Game 30 - Kizzang" style="width: 100%;" />
                </div>
                <div class="date">
                    <h5 style="text-align: center; font-size: 20px;margin-top: 30px;">Game Answers</h5>
                </div>
                
                <?php foreach($card->answers as $answer) : ?>
                <div style="padding-left: 4px;padding-right: 4px;">
                    <h3 style="text-align: center;font-size:16px;margin-bottom:5px;"><?= $answer->question; ?></h3>
                    <h4 style="text-align: center;font-size: 20px; color: #8534d7;margin: 0px;"><?= $answer->answer; ?></h4>
                </div>
                <?php endforeach; ?>
                <div style="margin-top: 25px; margin-bottom: 25px;padding-left: 4px;padding-right: 4px;">
                    <h3 style="text-align: center; font-size: 15px; margin: 2px;">Entry Submitted</h3>
                    <h3 style="text-align: center; font-size: 15px; margin: 2px;"><?= date("l, F j, Y", strtotime($card->dateTime)); ?></h3>
                    <h3 style="text-align: center; font-size: 15px; margin: 2px;"><?= date("g:i:s A", strtotime($card->dateTime)); ?></h3>
                </div>
                <div class="barcode_entry" style="max-width: 442px; min-width: 280px; width: 80%; margin: 0 auto;padding-left: 4px;padding-right: 4px;">
                    <span style="float: left;font-family: monospace;padding-bottom: 5px;"><?= $card->serialNumber; ?></span>
                    <span style="float: right;font-family: monospace;padding-bottom: 5px;">Ticket Number: <?= sprintf("%09d", $card->id); ?></span>
                    <div class="barcode_wrap" style="max-width: 442px; min-width: 280px; width: 100%;height:59px;margin: 0 auto;display: block;">
                        <img class="img" src="https://d1w836my735uqw.cloudfront.net/barcode.png" style="width:100%;" alt="Big Game 21 Entry" />
                    </div>
                </div>
                <?php endforeach; ?>
                <div style="max-width: 442px; min-width: 290px; width: 90%; margin: 0 auto 30px;padding-left: 4px;padding-right: 4px;">
                    <h3 style="text-align: center; color: blue;margin-top: 30px;"><a target="_blank" href="https://<?= $url; ?>"><?= strtoupper($url); ?></a></h3>
                    <h6 style="text-align: center;"><a target="_blank" href="https://<?= $url; ?>/accounts/optout/<?= $emailCode; ?>">KIZZANG&#8482; EMAIL OPTOUT</a></h6>
                    <p style="text-align: center;margin: 0px;">NO PURCHASE NECESSARY. FREE TO PLAY. </p>
                    <p style="text-align: center;margin: 0px;">&#0169; Copyright 2016 Kizzang. All Rights Reserved. </p>
                </div>
            </div>
        </div>
    </body>
</html>