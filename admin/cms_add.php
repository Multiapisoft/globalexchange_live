<?php
$title = "CMS";
$mid = isset($_GET['mid']) ? (int) $_GET['mid'] : 0;
include '../lib/config.php';
$row = my_fetch_object(my_query("SELECT * FROM cms_menu WHERE recid='".$mid."' AND status = 0"));
$title = isset($row->title) ? 'Add '.$row->title : 'CMS';
include_once 'header.php';
if (!$row) {
    redirect('./cms.php?mid='.$mid);
    die();
}
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <form class="form-horizontal" action="cms_model.php" method="post">
                <div class="panel-body">
                    <div class="form-group row">
                        <label for="title" class="col-sm-3 col-form-label">Title *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="title" name="title" maxlength="100" required="required">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description" class="col-sm-3 col-form-label">Description *</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="description" name="description" rows="3" required="required" maxlength="1000"></textarea>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-left">
                    <input type="hidden" name="mid" value="<?php echo $row->recid;?>" />
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>