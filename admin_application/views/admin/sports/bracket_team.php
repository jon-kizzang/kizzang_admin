<div id="team_<?= $num; ?>">
    <form>
        <label class="form-inline">Position</label>
        <select id="position_<?= $num; ?>">
            <?php for($i = 1; $i < 9; $i++) : ?>
            <option value="<?= $i; ?>"><?= $i; ?></option>
            <?php endfor; ?>
        </select>
        <label class="form-inline">Team 1</label>
        <select id="team1_<?= $num; ?>">
            <?php foreach($teams as $team) : ?>
            <option value="<?= $team->id; ?>"><?= $team->name; ?></option>
            <?php endforeach; ?>
        </select>
        <label class="form-inline">Team 2</label>
        <select id="team2_<?= $num; ?>">
            <?php foreach($teams as $team) : ?>
            <option value="<?= $team->id; ?>"><?= $team->name; ?></option>
            <?php endforeach; ?>
        </select>
        <button id="add_<?= $num ;?>" class="form-inline add_team btn btn-success" rel="<?= $num; ?>">Add Matchup</button>
        <button id="remove_<?= $num ;?>" class="form-inline remove_team btn btn-danger" rel="<?= $num; ?>">Delete Matchup</button>
    </form>
</div>