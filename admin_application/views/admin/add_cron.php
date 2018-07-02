<div class="panel panel-primary">
    <div class="panel-heading">Cron</div>
    <div class="panel-body">
        <form id="frm_cron">
        <?php if($cron) : ?><input type="hidden" name="id" value="<?= $cron->id; ?>"/><?php endif; ?>
        <div class="form-group" id="div_name">
        <label for="screen_name">Name</label>
        <input type="text" class="form-control" id="screen_name" name="name" placeholder="name" value="<?php if($cron) echo $cron->name; ?>"/>
        </div>        
        <div class="form-group" id="div_url">
            <label for="screen_name">Job to Run</label>
            <select class="form-control" id="routine_id" name="routine_id">
                <?php foreach($links as $link) : ?>
                <option value="<?= $link->id; ?>" <?php if($cron && $cron->routine_id == $link->id) echo "selected=''"; ?>><?= $link->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>    
        <div class="form-group" id="div_minutes">
            <label for="screen_name">Minutes</label>
            <select class="form-control" id="minutes" name="minutes">
                <?php foreach($minutes as $minute) : ?>
                <option value="<?= $minute; ?>" <?php if($cron && (int) $cron->minutes === (int) $minute && $minute != "*") echo "selected=''"; ?>><?= $minute; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" id="div_hours">
            <label for="screen_name">Hours</label>
            <select class="form-control" id="hours" name="hours">
                <?php foreach($hours as $hour) : ?>
                <option value="<?= $hour; ?>" <?php if($cron &&  (int) $cron->hours === (int) $hour && $hour != "*") echo "selected=''"; ?>><?= $hour; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" id="div_day_of_months">
            <label for="screen_name">Day of Month</label>
            <select class="form-control" id="day_of_month" name="day_of_month">
                <?php foreach($days_of_month as $day_of_month) : ?>
                <option value="<?= $day_of_month; ?>" <?php if($cron && $cron->day_of_month === $day_of_month && $day_of_month  != "*") echo "selected=''"; ?>><?= $day_of_month; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" id="div_months">
            <label for="screen_name">Months</label>
            <select class="form-control" id="months" name="months">
                <?php foreach($months as $index => $month) : ?>
                <option value="<?php if($index) echo $index; else echo "*"; ?>" <?php if($cron && $cron->months == $index && $month != "*") echo "selected=''"; ?>><?= $month; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" id="div_day_of_weeks">
            <label for="screen_name">Day of Week</label>
            <select class="form-control" id="day_of_week" name="day_of_week">
                <?php foreach($days_of_week as $index => $day_of_week) : ?>
                <option value="<?php if($index) echo $index; else echo "*"; ?>" <?php if($cron && $cron->day_of_week == $index && $day_of_week != "*") echo "selected=''"; ?>><?= $day_of_week; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        </form>
    </div>
    <div class="panel-footer" style="text-align: right;"><button class="btn btn-primary" id="btn_update"><?php if($cron) : ?>Update <?php else: ?>Add<?php endif; ?></button></div>
</div>

<script>
    $("#btn_update").click(function(){
        $.post("/admin/ajax_add_cron", $("#frm_cron").serialize(), function(data){
            if(data.success)
            {
                alert("Cron Job added");
                location.href = "/admin/cron";
            }
        }, 'json');
    });
</script>