<link rel="stylesheet" href="/css/jtree.css"/>
<script src="/js/jstree.js"></script>
<form method="POST" id="frm_cloudfront">
<div class="panel panel-primary">
    <div class="panel-heading">Select File for Invalidation</div>
    <div class="panel-body">
        <label>Select Bucket</label>
        <select id="s3_bucket" name="bucket">
            <?php foreach($buckets as $key => $bucket) : ?>
            <option value="<?= $key; ?>" <?php if($key == $cur_bucket) echo "selected=''"; ?>><?= $bucket;?></option>
            <?php endforeach; ?>
        </select>
        <div id="jstree_demo_div"></div>
    </div>
    <div class="panel-footer" style="text-align:right;"><button class="btn btn-danger" id="btn_invalidate">Invalidate</button></div>
</div>
</form>

<script>
    var selected = [];
    $(function () 
    { 
        $('#jstree_demo_div').on('changed.jstree', function (e, data) {
            var i, j;
            selected = []
            for(i = 0, j = data.selected.length; i < j; i++) {
              selected.push(data.instance.get_node(data.selected[i]).id);
            } 
            
          }).jstree(
        {core : 
            {data : <?= $json; ?>}
        }); 
        
        $("#s3_bucket").change(function(){
            $("#frm_cloudfront").submit();
        });
        
        $("#btn_invalidate").click(function(e){
            e.preventDefault();
            if(selected.length)
            {
                $.post("/admin/ajax_invalidate_cloudfront_file", {paths: selected, bucket: $("#s3_bucket").val()}, function(data){
                    if(data.success)
                    {
                        alert("Files successfully Invalidated");
                    }
                    else
                    {
                        alert("Invalidation Failed");
                    }
                }, 'json');
            }
        })
    });
</script>