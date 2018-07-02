<?php foreach($payouts as $payout) : ?>
<form class="form-inline" style="margin-bottom: 20px;" id="frm_<?= $payout->id; ?>">
    <input type="hidden" name="id" value="<?= $payout->id; ?>"/>
  <div class="form-group">
    <label for="gameType">Game Type</label>
    <select class="form-control" name="gameType">
        <?php foreach($gameTypes as $gameType) : ?>
        <option value="<?= $gameType; ?>" <?php if($gameType == $payout->gameType) echo "selected='selected'"; ?>><?= $gameType; ?></option>
        <?php endforeach; ?>
    </select>
  </div>
    <div class="form-group" style="margin-left: 10px;">
    <label for="gameType">Pay Type</label>
    <select class="form-control" name="payType">
        <?php foreach($payTypes as $payType) : ?>
        <option value="<?= $payType; ?>" <?php if($payType == $payout->payType) echo "selected='selected'"; ?>><?= $payType; ?></option>
        <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group" style="margin-left: 10px;">
    <label for="startRank">Start Rank</label>
    <input type="email" class="form-control" name="startRank" value="<?= $payout->startRank; ?>">
  </div>
  <div class="form-group" style="margin-left: 10px;">
    <label for="endRank">End Rank</label>
    <input type="email" class="form-control" name="endRank" value="<?= $payout->endRank; ?>">
  </div>
  <div class="form-group" style="margin-left: 10px;">
    <label for="endRank">Amount</label>
    <input type="email" class="form-control" name="amount" value="<?= $payout->amount; ?>">
  </div>
  <button type="submit" rel="<?= $payout->id; ?>" class="btn btn-default add-payout">Update</button>
</form>
<?php endforeach; ?>
<form class="form-inline" id="frm_0">
  <div class="form-group">
      <input type="hidden" name="id" value="0"/>
    <label for="gameType">Game Type</label>
    <select class="form-control" name="gameType">
        <?php foreach($gameTypes as $gameType) : ?>
        <option value="<?= $gameType; ?>" <?php if($gameType == $currentGameType) echo "selected='selected'"; ?>><?= $gameType; ?></option>
        <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group" style="margin-left: 10px;">
    <label for="gameType">Pay Type</label>
    <select class="form-control" name="payType">
        <?php foreach($payTypes as $payType) : ?>
        <option value="<?= $payType; ?>"><?= $payType; ?></option>
        <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group" style="margin-left: 10px;">
    <label for="startRank">Start Rank</label>
    <input type="email" class="form-control" name="startRank" placeholder="0">
  </div>
  <div class="form-group" style="margin-left: 10px;">
    <label for="endRank">End Rank</label>
    <input type="email" class="form-control" name="endRank" placeholder="0">
  </div>
  <div class="form-group" style="margin-left: 10px;">
    <label for="endRank">Amount</label>
    <input type="email" class="form-control" name="amount" placeholder="0">
  </div>
    <button type="button" rel="0" class="btn btn-primary add-payout">Add</button>
</form>