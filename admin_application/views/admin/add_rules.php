<form role="form" id="frm_rule" method="post" enctype="multipart/form-data" action="/admin/file_add_rule">
<div class="modal-header"><?php if($rule) : ?>Edit<?php else : ?>Add<?php endif; ?> Rules for <?= $game->Name; ?></div>
<div class="modal-body">

 <div id="rule_message"></div>
   <?php if($rule) : ?><input type="hidden" name="id" value="<?=$rule->id?>"/><?php endif; ?>
   <input type="hidden" name="GameTypeId" value="<?= $extra->game_type_id; ?>"/>
   <input type="hidden" name="GameId" value="<?= $extra->game_id; ?>"/>
   <input type="hidden" name="slug" value="<?= $game->slug; ?>"/>
  <div class="form-group" id="div_TermsOfService">
    <label>Terms Of Service</label>
    <input type="file" class="form-control" id="TermsOfService" name="TermsOfService">
  </div>
  <div class="form-group" id="div_PrivacyPolicy">
    <label>Privacy Policy</label>
    <input type="file" class="form-control" id="PrivacyPolicy" name="PrivacyPolicy">
  </div>
  <div class="form-group" id="div_ParticipationRules">
    <label>Participation Rules</label>
    <input type="file" class="form-control" id="ParticipationRules" name="ParticipationRules">
  </div>

</div>
</div>
<div class="modal-footer"><button class="btn btn-default" data-dismiss="modal" type="button">Close</button><input type="submit" id="update_prize" class="btn btn-primary" value="Upload Files"/>
</form>