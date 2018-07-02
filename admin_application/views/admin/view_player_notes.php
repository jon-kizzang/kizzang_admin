<div class="modal-header">Player Notes</div>
<div class="modal-body">
    <?php if($notes) : ?>
    <div class="panel panel-primary">    
        <div class="panel-heading">Latest Notes</div>
        <div class="panel-body">
    <?php foreach($notes as $note) : ?>
    <div class="panel panel-info">    
        <div class="panel-heading"><?= $note->title; ?></div>
        <div class="panel-body">
            <pre><?= $note->message; ?></pre>
        </div>
        <div class="panel-footer" style="text-align: right;"><?= $note->author; ?></div>
    </div>
    <?php endforeach; ?>  
        </div>
    </div>
    <?php else : ?>
        <div class="alert alert-info">No Notes for this Player.</div>
    <?php endif; ?>

    <div class="panel panel-primary">    
        <div class="panel-heading">Add Player Note</div>
        <div class="panel-body">
            <form id="frm_note">
                <div class="form-group" id="div_title">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="title" value="">
                </div>
                <div class="form-group" id="div_message">
                <label for="message">Title</label>
                <textarea class="form-control" id="message" name="message" placeholder="message"></textarea>
                </div>
                <input type="hidden" name="playerId" value="<?= $id; ?>"/>
            </form>
        </div>
        <div class="panel-footer" style="text-align: right;"><button id="btn_add_note" class="btn btn-success">Add Note</button></div>
    </div>    
</div>
<div class="modal-footer"><button data-dismiss="modal" type="button" id="close_notes" class="btn btn-primary">Close</button></div>
<script>

   $("#btn_add_note").click(function(){
       $.post("/admin/ajax_save_player_note", $("#frm_note").serialize(), function(data){
           if(data.success)
           {
               $("#close_notes").click();
               alert("Note Added");
           }
       }, 'json');
   }); 

</script>