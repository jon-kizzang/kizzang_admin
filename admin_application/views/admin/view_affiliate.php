<div class="form-inline" id="div<?= $index; ?>" style="margin-top: 10px;">
<div class="form-group">
  <label for="GameType">Game Type</label>
  <select name="game[<?= $index; ?>][GameType]" id="GameType<?= $index; ?>" class="form-control game-type" rel="<?= $index; ?>">
      <?php foreach($gameTypes as $gameType) : ?>
      <option value="<?= $gameType; ?>"><?= $gameType; ?></option>
      <?php endforeach; ?>
  </select>
</div>
<div class="form-group">
  <label for="GameType">Theme</label>
  <select name="game[<?= $index; ?>][Theme]" id="Theme<?= $index; ?>" class="form-control">
      <?php foreach($themes['Slot'] as $theme) : ?>
      <option value="<?= $theme; ?>"><?= $theme; ?></option>
      <?php endforeach; ?>
  </select>
</div>
<button type="submit" class="btn btn-danger remove-game" style="float: right;" rel="<?= $index; ?>">Remove Game</button>
</div>