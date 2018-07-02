<div class="modal-header">Delete Category</div>
<div class="modal-body">
    <div class="well">
        <form role="form" id="frm_ft_game">
            <div class="form-group" id="divc_rank">
            <label>Category: </label>
            <select id="rank" name="finalCategoryId" class="form-control">                
                <?php foreach($categories as $category) : ?>
                <option value="<?=$category->id; ?>"><?=$category->name; ?></option>
                <?php endforeach; ?>
            </select>
            </div>            
        </form>
    </div>   
    </div>
<div class="modal-footer"><button class="btn btn-danger" id="btn_delete_category" type="button">Delete</button><button class="btn btn-default" data-dismiss="modal" type="button">Close</button></div>

<script>
$(function() {
    $("#btn_delete_category").click(function(){
        $.post("/admin_sports/ajax_add_ft_game", $("#frm_ft_game").serialize(), function(data){
            location.reload();
        }, 'json');
    });
    
    $('#dateTime').datetimepicker({
            format:'Y-m-d H:i',            
            step: 5
          });
});
</script>