<?php

$title = "Hot News";
include_once 'header.php';
$row = my_fetch_object(my_query("SELECT * FROM hot_news WHERE recid=1"));
$status_arr = array('Active', 'Block');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <form class="form-horizontal" action="cms_hot_news_model.php" method="post" enctype="multipart/form-data">
                <div class="panel-body">
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <textarea class="form-control" id="hot_news" name="hot_news" required="required" rows="10"><?php echo $row->hot_news; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="status" class="col-sm-3 col-form-label">Select Image</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="file" id="image" name="image" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="status" class="col-sm-3 col-form-label">Status *</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="status" id="status" required="required">
                                <?php foreach ($status_arr as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>" <?php if ($key == $row->status) {
                                                                            echo "selected='selected'";
                                                                        } ?>><?php echo $value; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-left">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<!-- STRAT PAGE LABEL PLUGINS -->
<script src="../assets/plugins/ckeditor/ckeditor.js"></script>
<script>
    $(document).ready(function() {
        "use strict"; // Start of use strict
        // Replace the <textarea id="editor1"> with a CKEditor
        // instance, using default configuration.
        CKEDITOR.replace('hot_news');
    });
</script>