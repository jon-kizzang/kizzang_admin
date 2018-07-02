<div style="max-width:600px;min-width: 280px; margin:10px auto 0;display:block;">
    <img src="https://d1w836my735uqw.cloudfront.net/logos/24-12_hour_emails.jpg" alt="Notice - Kizzang" style="width:100%;" />
</div> 
 <div style="text-align: center; font-size: 36px; margin: 3px; font-weight: bold; color:#345E9E;">
     Documents Needed
 </div>
<div style="text-align: center; font-size: 20px; margin: 10px auto; height: auto; max-width: 350px; min-width: 95px; font-weight: bold; color:#FFFFFF; background-color: #537BBE">
    <span style="font-size: 14px; color: white;">
        Prize: <?= $prize; ?>
        <br>
        Game Name: <?= $game; ?>
        <br>
        Serial Number: <?= $serialNumber; ?>
        <br>
        Entry Number: <?= $entryId; ?>
    </span>
</div>
 <div style="font-size: 16px;  margin:20px auto;display:block; max-width:600px;min-width: 280px;">
     To start processing your prize, click the button below within <strong>24 hours</strong> to send a photo of your DMV-issued ID and any necessary Prize Claim Documents to Kizzang&reg;.
 </div>
<?php if($signinGuid) : ?>
<a href="https://<?php echo getenv("ENV") == "dev" ? "dev." : ""; ?>kizzang.com/w2redirect/<?= $signinGuid; ?>" style="text-decoration: none;">
<div style="color: #FFFFFF; background-color: #61b606; mso-line-height-rule:exactly; border-radius: 30px; height: 20px; padding-top: 5px; border-style: none;margin: 10px auto; text-align: center; max-width:300px;min-width: 280px;">
    COMPLETE DOCUMENTS
</div>
<?php else : ?>
<a href="https://<?php echo getenv("ENV") == "dev" ? "dev." : ""; ?>kizzang.com" style="text-decoration: none;">
<div style="color: #FFFFFF; background-color: #61b606; mso-line-height-rule:exactly; border-radius: 30px; height: 20px; padding-top: 5px; border-style: none;margin: 10px auto; text-align: center; max-width:300px;min-width: 280px;">
    CLAIM PRIZE
</div>
<?php endif; ?>
</a>
 <div style="font-size: 16px;  margin:20px auto;display:block; max-width:600px;min-width: 280px;">
     Please complete and submit your documents by <?= date("h:m A", strtotime($expirationDate)); ?> PT on <?= date("m/d/y", strtotime($expirationDate)); ?>, otherwise your prize will be forfeited.  Make sure your information is up-to-date.  All winnings of $10,000 or less will be paid via PayPal&trade;.  It may take up to 90 days to process your prize claim.
 </div>
 <div style="font-size: 16px;  margin:20px auto;display:block; max-width:600px;min-width: 280px;">
     Our prize team will verify that your personal information is accurate and complete.  If you are eligible to receive this prize, <span style="font-weight: bold;">you will receive a phone call during regular business hours (9am - 6pm PST) within 3 business days.</span>
 </div>
 <div style="font-size: 16px;  margin:20px auto;display:block; max-width:600px;min-width: 280px;">
     You will not be able to play games at Kizzang&reg; until this process is successfully completed.  If you have any questions, please email <a href="mailto:winners@kizzang.com">winners@kizzang.com</a>.  We will be glad to assist you through this process.
 </div>
 <div style="font-size: 16px;  margin:20px auto;display:block; max-width:600px;min-width: 280px;">
     Thank you for playing at Kizzang!  We hope to see you again soon.
 </div>