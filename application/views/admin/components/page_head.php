<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title><?php echo $meta_title; ?></title>

<!-- Bootstrap -->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css " rel="stylesheet"> <!--  <?php //echo site_url('css/bootstrap.min.css');?>" rel="stylesheet">  -->
<link href="<?php echo site_url('css/admin/admin.css');?>" rel="stylesheet">
<link href="<?php echo site_url('css/admin/bootstrap-datepicker3.css');?>" rel="stylesheet">

<script type="text/javascript" src="<?php echo site_url('js/tinymce/tinymce.min.js');?>"></script> 
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script> <!--  <?php //echo site_url('js/bootstrap.min.js');?>  -->
<?php if(isset($sortable) && $sortable ===TRUE): ?>
	<script src="<?php echo site_url('js/jquery-ui.min.js');?>"></script>
    <script src="<?php echo site_url('js/jquery.mjs.nestedSortable.js');?>"></script>
<?php endif; ?>
<script src="<?php echo site_url('js/bootstrap-datepicker.js');?>"></script>
 
<!-- Load TinyMCE -->
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    plugins: [
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
});
</script>
</head>  