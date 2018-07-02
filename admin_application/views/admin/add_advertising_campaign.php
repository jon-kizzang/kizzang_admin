<div class="panel panel-primary">
    <div class="panel-heading"><?php if($campaign) : ?>Edit<?php else : ?>Add<?php endif;?> Advertising Campaign</div>
    <div class="panel-body">
        <div id="sponsor_message"></div>
        <form role="form" id="frm_sponsor">
           <div class="form-group" id="div_id">
            <label for="contactEmail">ID (id)</label>
            <input type="text" class="form-control" id="id" name="id" placeholder="alphanumic" value="<?php if($campaign) echo $campaign->id; ?>">
          </div>
        <div class="form-group" id="div_StartDate">
        <label for="StartDate">Start Date</label>
        <input type="text" class="form-control" id="StartDate" name="start_date" placeholder="StartDate" value="<?php if($campaign) echo date("Y-m-d", strtotime($campaign->start_date)); ?>">
        </div>
        <div class="form-group" id="div_EndDate">
        <label for="EndDate">End Date</label>
        <input type="text" class="form-control" id="EndDate" name="end_date" placeholder="EndDate" value="<?php if($campaign) echo date("Y-m-d", strtotime($campaign->end_date)); ?>">
        </div>
        <div class="form-group" id="div_redirect_url">
          <label for="sponsorType">Redirect URL</label>
          <select type="text" class="form-control" id="redirect_url" name="redirect_url">
              <?php foreach($urls as $url) : ?>
              <option value="<?= $url; ?>" <?php if($campaign && $campaign->redirect_url == $url) echo "selected=''"; ?>><?= $url; ?></option>
              <?php endforeach;?>
          </select>
        </div>
        <div class="form-group" id="div_sponsorType">
          <label for="sponsorType">Sponsor Type (s)</label>
          <select type="text" class="form-control" id="utm_source" name="utm_source">
              <?php foreach($sponsors as $sponsor) : ?>
              <option value="<?= $sponsor->id; ?>" <?php if($campaign && $campaign->utm_source == $sponsor->id) echo "selected=''"; ?>><?= $sponsor->name; ?></option>
              <?php endforeach;?>
          </select>
        </div>
        <div class="form-group" id="div_cdn">
          <label for="sponsorType">CDN</label>
          <select  class="form-control" id="utm_medium" name="cdn">
              <?php foreach($cdns as $cdn) : ?>
              <option value="<?= $cdn; ?>" <?php if($campaign && $campaign->cdn == $cdn) echo "selected=''"; ?>><?= $cdn; ?></option>
              <?php endforeach;?>
          </select>
        </div>
        <div class="form-group" id="div_utm_medium">
          <label for="sponsorType">Medium (m)</label>
          <select type="text" class="form-control" id="utm_medium" name="advertising_medium_id">
              <?php foreach($mediums as $medium) : ?>
              <option value="<?= $medium->id; ?>" <?php if($campaign && $campaign->advertising_medium_id == $medium->id) echo "selected=''"; ?>><?= $medium->description; ?></option>
              <?php endforeach;?>
          </select>
        </div>
        <div class="form-group" id="div_utm_content">
          <label for="contactEmail">UTM Content (c)</label>
          <input type="text" class="form-control" id="utm_content" name="utm_content" placeholder="A/B Test" value="<?php if($campaign) echo $campaign->utm_content; ?>">
        </div>
        <div class="form-group" id="div_utm_campaign">
          <label for="contactPhone">UTM Campaign (t)</label>
          <input type="text" class="form-control" id="utm_campaign" name="utm_campaign" placeholder="#" value="<?php if($campaign) echo $campaign->utm_campaign; ?>">
        </div>
        <div class="form-group" id="div_utm_campaign">
          <label for="contactPhone">Description</label>
          <textarea class="form-control" id="description" name="description" ><?php if($campaign) echo $campaign->description; ?></textarea>
        </div>
        <div class="form-group" id="div_image">
          <label for="image">Image</label>
          <input type="text" class="form-control" id="image" name="image"  value="<?php if($campaign) echo $campaign->image; ?>"/>
        </div>
        <div class="form-group" id="div_utm_campaign">
          <label for="contactPhone">Web Landing Spot (d)</label>
          <select id="d" name="d" class="form-control">
              <?php foreach($ds as $d) : ?>
              <option value="<?= $d; ?>" <?php if($campaign && $d == $campaign->d) echo "selected=''"; ?>><?= $d; ?></option>
              <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="message">Message</label>
          <textarea class="form-control" id="message" name="message" ><?php if($campaign) echo $campaign->message; ?></textarea>
        </div>
        <div class="form-group" id="div_utm_campaign">
          <label for="contactPhone">Code (Affiliate Only)</label>
          <input type="text" class="form-control" id="code" name="code" placeholder="code" value="<?php if($campaign) echo $campaign->code; ?>">
        </div>
        <div class="form-group" id="div_final_url">
          <label for="contactEmail">Final URL</label>
          <input type="text" class="form-control" value="<?php if($campaign) echo $campaign->url; ?>" readonly="">
        </div>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_campaign" class="btn btn-primary"><?php if($campaign) : ?>Update<?php else : ?>Add<?php endif;?> Advertising Campaign</button></div>
</form>
</div>

<?php if($reports) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">Impression Breakdown</div>
    <div class="panel-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Platform</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reports as $report) : ?>
                <tr>
                    <td><?= $report->date ? $report->date : "<strong>GRAND TOTAL</strong>"; ?></td>
                    <td><?= $report->platform ? $report->platform : "<strong>TOTAL</strong>" ?></td>
                    <td><?= $report->cnt; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<script>
$(function() {
        $("#update_campaign").click(function(){
                $.post('/admin/ajax_add_advertising_campaign', $("#frm_sponsor").serialize(), function(data){
                    $("#frm_sponsor div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#sponsor_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "location.reload();";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                $("#sponsor_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                                for(var key in data.errors)
                                    $("#div_" + key).addClass("alert-danger");
                                $('html,body').scrollTop(0);
                        }
                },'json');
        });        
        
        $( "#StartDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "+1d", 
            changeMonth: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
                $( "#EndDate" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        
        $( "#EndDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "+1w",
            changeMonth: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
                $( "#StartDate" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
    });
</script>