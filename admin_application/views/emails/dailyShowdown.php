<?php foreach($cards as $index => $card) : ?>
<div style="max-width:600px;min-width: 280px; margin:10px auto 0;display:block;">
    <img src="<?= $image; ?>" alt="Daily Showdown - Kizzang" style="width:100%;" />
</div>
 <div class="date">
     <h5 style="text-align: center; font-size: 20px;margin-top: 5px;"><?= date('l, F  jS', strtotime($cardDate)); ?></h5>
 </div>
 <div class="questionsAnswers" style="padding-left: 4px;padding-right: 4px">
     <?php foreach ($card['events'] as $event) : ?>
     <h3 style="text-align: center;font-size:16px;margin-bottom:5px;"><?= $event['title']; ?></h3>
     <h4 style="text-align: center;font-size: 20px; color: #8534d7;margin: 0px;"><?= $event['winner']; ?></h4>
     <?php endforeach; ?>
 </div>
 <div style="margin-top: 25px; margin-bottom: 25px;padding-left: 4px;padding-right: 4px;">
     <h3 style="text-align: center; font-size: 15px; margin: 2px;">Entry Submitted</h3>
     <h3 style="text-align: center; font-size: 15px; margin: 2px;"><?= date('F j, Y', strtotime($card['date'])); ?></h3>
     <h3 style="text-align: center; font-size: 15px; margin: 2px;"><?= date('g:i A', strtotime($card['date'])); ?> PDT</h3>
 </div>
 <div class="barcode_entry" style="max-width: 442px; min-width: 280px; width: 80%; height: 30px; margin: 0 auto;padding-left: 4px;padding-right: 4px;">
     <span style="float: left;font-family: monospace;padding-bottom: 5px;"><?= $card['serial_number']; ?></span>
     <span style="float: right;font-family: monospace;padding-bottom: 5px;">Ticket Number: <?= $card['id']; ?></span>
 </div>
 <?php if($index != count($cards) - 1) : ?>
<div class="barcode_entry" style="max-width: 442px; min-width: 280px; width: 80%; height: 35px; margin: 0 auto;padding-left: 4px;padding-right: 4px;">
    <hr/>
</div>
 <?php endif; ?>
 <?php endforeach; ?>