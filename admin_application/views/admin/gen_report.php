ID  AccountCreated  Email   ScratchCard SportsEvent SlotTournament  SweepstakesEntered  LoginDays   CurrentDay
<?php foreach($people as $person) : ?>
<?= $person['id'] . "\t" . $person['accountCreated'] . "\t" . $person['email'] . "\t" . $person['ScratchCard'] . "\t" . $person['SportsEvent'] . "\t" . $person['SlotTournament'] . "\t" . $person['SweepstakeEntries'] . "\t" . $person['LoginDays'] . "\t" . $person['CurrentDay'] . "\n"; ?>
<?php endforeach; ?>
