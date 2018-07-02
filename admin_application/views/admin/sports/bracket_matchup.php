<div id="div_<?= $id; ?>">
    <input type="hidden" id="bracketId_<?= $id; ?>" value="<?php if($rec) echo $rec->id; else echo "0"; ?>"/>
    <label>Team 1</label>
    <select id="teamId1_<?= $id; ?>" class="form-inline">
    <?php foreach($teams as $team) : ?>
        <option value="<?= $team->id; ?>" <?php if($rec && $rec->teamId1 == $team->id) echo "selected='selected'"; ?>><?= $team->name; ?></option>
    <?php endforeach; ?>
    </select>
    <label>Team 1 Rank</label>
    <input type="text" class="form-inline" id="teamRank1_<?= $id; ?>" value="<?php if($rec) echo $rec->teamRank1; ?>"/>
    <label>Team 2</label>
    <select id="teamId2_<?= $id; ?>" class="form-inline">
    <?php foreach($teams as $team) : ?>
        <option value="<?= $team->id; ?>" <?php if($rec && $rec->teamId2 == $team->id) echo "selected='selected'"; ?>><?= $team->name; ?></option>
    <?php endforeach; ?>
    </select>
    <label>Team 2 Rank</label>
    <input type="text" class="form-inline" id="teamRank2_<?= $id; ?>" value="<?php if($rec) echo $rec->teamRank2; ?>"/>
    <button class="btn btn-default matchup-update" rel="<?= $id; ?>"><?php if($isNew) echo "Create"; else echo "Update"; ?></button>
    <button class="btn btn-danger matchup-delete" rel="<?= $id; ?>" rec="<?php echo $isNew ? 1 : 0; ?>">Delete</button>
</div>