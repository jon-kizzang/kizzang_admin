<div class="well">
    <label>Select Language: </label>
    <select id="language_sel">
        <?php foreach($languages as $language) : ?>
        <option value="<?= $language->id; ?>" <?php if($id == $language->id) echo 'selected=""'; ?>><?= $language->language; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>                        
            <th>Identifier</th>
            <th>Description</th>                        
            <th>Translation</th> 
            <th>Edit</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($strings as $string) : ?>
        <tr>            
            <td><?= $string->identifier; ?></td>
            <td><?= $string->description; ?></td>
            <td><?= $string->translation; ?></td>                   
            <td><a href="/admin/add_string/<?= $string->id?>" class="btn btn-primary">Edit</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50});
                
        } );
        
        
        $("#language_sel").change(function(){
            $.post('/admin/ajax_change_language', {id: $(this).val()}, function(data){
                if(data.success)
                    location.reload();
            }, 'json');
        });
        
</script>