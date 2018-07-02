<div style="max-width:600px;min-width: 280px; margin:10px auto 0;display:block;">
    <img src="https://kizzang-email.s3.amazonaws.com/logos/roal_header.jpg" alt="Run of a Lifetime - Kizzang" style="width:100%;" />
</div>
 <div class="date">
     <h5 style="text-align: center; font-size: 20px;margin-top: 5px;"><?= date('l, F  jS', strtotime($cardDate)); ?></h5>
 </div>
 <hr/>
 <div class="questionsAnswers" style="text-align: center; font-size: 20px; margin: 3px; font-weight: bold; color:#1087dd;">
     <?= $screenName; ?>'s run: <?= $currentStreak; ?>/50
 </div>
 <hr/>
 <div>
     <div style="text-align: center; font-size: 16px; margin-top: 25px; font-weight: bold;"><i>Who will win this matchup?</i></div>
     <div style="text-align: center; font-size: 16px; margin: 2px; font-weight: bold;">Your Answer: <span style="color:#1087dd"><?= $answer; ?></span></div>
     <div style="text-align: center; font-size: 16px; margin: 2px; font-weight: bold; margin-top: 25px;">Good luck! Be sure to come back tomorrow to extend your run!</div>
 </div>
 <div style="margin-top: 25px; margin-bottom: 25px;padding-left: 4px;padding-right: 4px;">
     <h3 style="text-align: center; font-size: 16px; margin: 2px;">Entry Submitted</h3>
     <h3 style="text-align: center; font-size: 16px; margin: 2px;"><?= date('F j, Y', strtotime($created)); ?></h3>
     <h3 style="text-align: center; font-size: 16px; margin: 2px;"><?= date('g:i A', strtotime($created)); ?> PDT</h3>
 </div>
 <div class="barcode_entry" style="max-width:600px;min-width: 280px; margin:10px auto 0;display:block;">
     <span style="float: left;font-family: monospace;padding-bottom: 5px; margin-left: 5px;"><?= sprintf("KR%05d", $configId); ?></span>
     <span style="float: right;font-family: monospace;padding-bottom: 5px; margin-right: 5px;">Ticket Number: <?= $answerId; ?></span>
 </div>