<div class="modal-header">Add Prize</div>
<div class="modal-body">
    <div class="well">
        <form role="form" id="frm_ft_place">
            <input type="hidden" name="parlayCardId" value="<?= $id; ?>"/>
            <div class="form-group" id="divc_rank">
            <label>Rank: </label>
            <select id="rank" name="rank" class="form-control">                
                <?php for($i = 1; $i <= 20; $i++) : ?>
                <option value="<?=$i; ?>"><?=$i; ?></option>
                <?php endfor; ?>
            </select>
            </div>
            
            <div class="form-group" id="divc_prize">
                <label for="StartDate">Prize</label>
                <input type="text" class="form-control" id="prize" name="prize" placeholder="Prize" value="">
              </div>
        </form>
    </div>   
    </div>
<div class="modal-footer"><button class="btn btn-primary" id="btn_add_prize" type="button">Add</button><button class="btn btn-default" id="btn_ftg_close" data-dismiss="modal" type="button">Close</button></div>

<script>
$(function() {
    $("#btn_add_prize").click(function(){
        $.post("/admin_sports/ajax_add_parlay_place", $("#frm_ft_place").serialize(), function(data){
            location.reload();
        }, 'json');
    });
});
</script>