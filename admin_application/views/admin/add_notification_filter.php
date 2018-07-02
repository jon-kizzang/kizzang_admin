        <div class="form-group" id="filter_<?= $id; ?>">
         <label for="Name">Filter: </label>
         <select class="form-inline" id="key" name="key[<?= $id; ?>]">
             <?php foreach($tags as $tag) : ?>                                
                    <option value="<?=$tag?>"><?=$tag?></option>
             <?php endforeach; ?>
         </select>
         <select class="form-inline" id="relation" name="relation[<?= $id; ?>]" placeholder="included_segments">                
             <?php foreach($relations as $relation) : ?>                                
                    <option value="<?=$relation?>"><?=$relation?></option>
             <?php endforeach; ?>
         </select>
         <input type="text" class="form-inline" placeholder="value" name="value[<?= $id; ?>]"/>
         <button class="btn btn-primary add-filter">Add Filter</button>
         <button class="btn btn-danger delete-filter" onClick="$('#filter_<?= $id; ?>').remove();">Remove Filter</button>
         </div>