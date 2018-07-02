<div class="modal-header">Edit Team</div>
<div class="modal-body">    
    <form id="frm_team">
        <div class="form-group" id="div_name">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="name" value="<?= $team->name; ?>">
        </div>        
        <div class="form-group" id="div_alt">
        <label for="alt">Alternate Name</label>
        <input type="text" class="form-control" id="alt" name="alt" placeholder="alt" value="<?= $team->alt; ?>">
        </div>
        <div class="form-group" id="div_abbr">
        <label for="abbr">Abbreviation</label>
        <input type="text" class="form-control" id="abbr" name="abbr" placeholder="abbr" value="<?= $team->abbr; ?>">
        </div>
        <div class="form-group" id="div_wins">
        <label for="wins">Wins</label>
        <input type="text" class="form-control" id="wins" name="wins" placeholder="wins" value="<?= $team->wins; ?>">
        </div>
        <div class="form-group" id="div_losses">
        <label for="losses">Losses</label>
        <input type="text" class="form-control" id="losses" name="losses" placeholder="losses" value="<?= $team->losses; ?>">
        </div>
        <input type="hidden" name="id" value="<?= $team->id; ?>"/>
        <input type="hidden" name="sportCategoryID" value="<?= $team->sportCategoryID; ?>"/>
    </form>        
</div>
<div class="modal-footer"><button type="button" id="udpate_team" class="btn btn-primary">Update</button></div>
<script>

   $("#udpate_team").click(function(e){
       e.preventDefault();
       $.post("/admin_sports/ajax_update_sports_team", $("#frm_team").serialize(), function(data){
           if(data.success)
           {
               alert("Team Updated");
               location.reload();
           }
       }, 'json');
   }); 

</script>