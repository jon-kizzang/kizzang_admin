<style>
    .checkbox {margin-left: 20px}
</style>
<div class="well">
    <form id="frmSearch">
        <label>First Name</label>
        <input class="form-control" id="firstName" name="firstName" <?php if($rec) echo 'value="' . $rec->firstName . '"'; ?>/>
        <label>Last Name</label>
        <input class="form-control" id="lastName" name="lastName" <?php if($rec) echo 'value="' . $rec->lastName . '"'; ?>/>
        <label>Paypal Email</label>
        <input class="form-control" id="payPalEmail" name="payPalEmail"/>
        <label>Date of Birth (YYYY-MM-DD)</label>
        <input class="form-control" id="dob" name="dob" <?php if($rec) echo 'value="' . $rec->dob . '"'; ?>/>
        <label>City</label>
        <input class="form-control" id="city" name="city"/>
        <label>State</label>
        <input class="form-control" id="state" name="state"/>
        <label>Zip</label>
        <input class="form-control" id="zip" name="zip"/>
        <label>Phone</label>
        <input class="form-control" id="phone" name="phone"/>
        <div class="form-group" id="div_duplicates" style="margin-top: 20px;">
        <label>
                Duplicate?
            </label>
            <label class="checkbox">
                <input type="checkbox" name="duplicate[]" value="firstName" <?php if($rec) echo 'checked=""'; ?>> First Name
            </label>
            <label class="checkbox">
                <input type="checkbox" name="duplicate[]" value="lastName" <?php if($rec) echo 'checked=""'; ?>> Last Name
            </label>
            <label class="checkbox">
                <input type="checkbox" name="duplicate[]" value="payPalEmail"> PayPal Email
            </label>
            <label class="checkbox">
                <input type="checkbox" name="duplicate[]" value="dob" <?php if($rec) echo 'checked=""'; ?>> Date of Birth
            </label>
            <label class="checkbox">
                <input type="checkbox" name="duplicate[]" value="city"> City
            </label>
            <label class="checkbox">
                <input type="checkbox" name="duplicate[]" value="state"> State
            </label>
            <label class="checkbox">
                <input type="checkbox" name="duplicate[]" value="zip"> Zip Code
            </label>
            <label class="checkbox">
                <input type="checkbox" name="duplicate[]" value="phone"> Phone
            </label>            
    </div>
        <button id="btn_search" class="btn btn-success">Search</button>
    </form>    
</div>
<div class="panel panel-primary">
    <div class="panel-heading">Results</div>
    <div class="panel-body" id="results">

    </div>
</div>
<script>
    $("#btn_search").click(function(e){
        e.preventDefault();
       $.post("/admin_reports/ajax_find_dups", $("#frmSearch").serialize(), function(data){
           $("#results").html(data);
       }, 'html') 
    });
</script>