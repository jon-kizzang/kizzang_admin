<form class="form-inline" method="POST" action="/query">
<?php if($message) : ?>
    <div class="alert alert-danger"><?= $message; ?></div>
<?php endif; ?>
<div class="well">    
  <div class="form-group">    
      <label>Select DB:</label>
      <input type="radio" style="margin-left: 20px;" name="db" <?php if($db == "main") : ?> checked=""<?php endif; ?> class="form-control db_name" id="exampleInputName2" value="main"> Main
      <input type="radio" style="margin-left: 20px;" name="db" <?php if($db == "rsdocs") : ?> checked=""<?php endif; ?> class="form-control db_name" id="exampleInputName2" value="rsdocs"> RS Docs      
      <label style="margin-left: 40px;">Output:</label>
      <input type="radio" style="margin-left: 20px;" name="out" <?php if($out == "print") : ?> checked=""<?php endif; ?> class="form-control db_output" id="exampleInputName2" value="print"> Print
      <input type="radio" style="margin-left: 20px;" name="out" <?php if($out == "csv") : ?> checked=""<?php endif; ?> class="form-control db_output" id="exampleInputName2" value="csv"> CSV
  </div>  

</div>
<div class="panel panel-primary">
    <div class="panel-heading">Query</div>
    <div class="panel-body">
        <textarea class="form-control" id="query" name="query" style="min-height: 300px; width: 100%;"><?= $query; ?></textarea>
    </div>
    <div class="panel-footer" style="text-align:right;"><button id="btn_query" type="submit" class="btn btn-success">Query</button></div>
</div>
</form>
<div id="results">    
    <?php if($cols) : ?>
    <table id="query_tbl" class="table table-condensed table-responsive">     
        <thead>
        <tr>
            <?php foreach($cols as $col) : ?>
            <th><?= $col; ?></th>
            <?php endforeach; ?>
        </tr>    
        </thead>
        <tbody>
            <?php foreach($data as $row) : ?>
        <tr>
            <?php foreach($row as $point) :?>
            <td><?= $point; ?></td>
            <?php endforeach; ?>
        </tr>
            <?php endforeach; ?>    
        </tbody>
    </table>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
        $('#query_tbl').dataTable({pageLength: 50});
} );
</script>