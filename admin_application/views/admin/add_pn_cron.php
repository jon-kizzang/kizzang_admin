<div class="panel panel-primary">
    <div class="panel-heading">Cron</div>
    <div class="panel-body">
        <form id="frm_cron">
        <input type="hidden" name="id" value="<?= $id; ?>"/>
        <input type="hidden" name="preview" id="preview" value="1"/>
        <div class="form-group" id="div_minutes">
            <label for="screen_name">Minutes</label>
            <select class="form-control" id="minutes" name="minutes">
                <option value="*">*</option>
                <?php for($i = 0; $i < 60; $i++) : ?>
                <option value="<?= $i; ?>"><?= $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group" id="div_hours">
            <label for="screen_name">Hours</label>
            <select class="form-control" id="hours" name="hours">
                <option value="*">*</option>
                <?php for($i = 0; $i < 24; $i++) : ?>
                <option value="<?= $i; ?>"><?= $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group" id="div_day_of_months">
            <label for="screen_name">Day of Month</label>
            <select class="form-control" id="day_of_month" name="day_of_month">
                <option value="*">*</option>
                <?php for($i = 1; $i < 31; $i++) : ?>
                <option value="<?= $i; ?>"><?= $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group" id="div_months">
            <label for="screen_name">Months</label>
            <select class="form-control" id="months" name="months">
                <?php foreach($months as $index => $month) : ?>
                <option value="<?php if($index) echo $index; else echo "*"; ?>"><?= $month; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" id="div_day_of_weeks">
            <label for="screen_name">Day of Week</label>
            <select class="form-control" id="day_of_week" name="day_of_week">
                <?php foreach($days_of_week as $index => $day_of_week) : ?>
                <option value="<?php if($index) echo $index; else echo "*"; ?>"><?= $day_of_week; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" id="div_expire_date">
            <label for="screen_name">Last Date</label>
            <input type="text" class="form-control" name="expireDate" id="expireDate"/>
        </div>
        </form>
    </div>
    <div class="panel-footer" style="text-align: right;"><button class="btn btn-primary" id="btn_update">Add</button></div>
</div>

<script>
    $(document).ready(function(){
        $( "#expireDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            numberOfMonths: 3
        });
    });
    
    $("#btn_update").click(function(){
        $.post("/admin/ajax_add_pn_cron", $("#frm_cron").serialize(), function(data){
            if(data.success)
            {
                if(confirm('Are you sure you want to add these dates?\n\n' + data.dates))
                {
                    $("#preview").val(0);
                    $.post("/admin/ajax_add_pn_cron", $("#frm_cron").serialize(), function(data)
                    {
                        $("#preview").val(1);
                        if(data.success)
                            location.href = "/admin/view_notifications";
                        else
                            alert("Error adding in cron schedule");
                    }, 'json');
                }
            }
        }, 'json');
    });
</script>