<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title><?php echo $meta_title; ?></title>
<META NAME="robots" CONTENT="noindex,nofollow">
<!-- BootstrapCND v4.3 -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) - FULL VERSION -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<!-- End of BootstrapCND 4.3 -->	
<!-- Font Awesome 4.7 - Single source to prevent duplicates -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous">
<!-- End of Font Awesome -->
<link href="<?php echo site_url('css/admin/admin.css');?>?v=<?php echo time(); ?>" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker3.min.css" rel="stylesheet">
<?php if(isset($sortable) && $sortable ===TRUE): ?>
  	<script src="<?php echo site_url('js/jquery-ui.min.js');?>"></script>
    <script src="<?php echo site_url('js/jquery.mjs.nestedSortable.js');?>"></script> 
<?php endif; ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
 
<!-- Load TinyMCE -->
<script src="https://cdn.tiny.cloud/1/m0o7m7f3a0bazb6hfkqddm0ej4v4roygur9habrlfdkului7/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
  tinymce.init({
    selector: '#editarea',
    height: 500,
    
    plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons link image code',
      toolbar: 'link image code | undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | anchor | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen preview save print',
      menubar: 'file edit view insert format tools table help',
      relative_urls: false,
      toolbar_mode: 'floating',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Lottotrak',
      images_upload_base_path: '/',
      images_upload_url: '<?php echo base_url(); ?>postAcceptor.php',
        // enable title field in the Image dialog
      image_title: true, 
      // enable automatic uploads of images represented by blob or data URIs
      automatic_uploads: false,
      // add custom filepicker only to Image dialog
      file_picker_types: 'image media',
      images_reuse_filename: true,
      images_upload_handler: function (blobInfo, success, failure) {
      var xhr, formData;

      xhr = new XMLHttpRequest();
      xhr.withCredentials = false;
      xhr.open('POST', '<?php echo base_url(); ?>postAcceptor.php');

      xhr.onload = function() {
        var json;
        if (xhr.status < 200 || xhr.status >= 300) {
        failure('HTTP Error: ' + xhr.status);
        return;
        }

        json = JSON.parse(xhr.responseText);

        if (!json || typeof json.location != 'string') {
        failure('Invalid JSON: ' + xhr.responseText);
        return;
        }
        success(json.location);
      };
      formData = new FormData();
      formData.append('file', blobInfo.blob(), fileName(blobInfo));
      xhr.send(formData);
      },
      file_picker_callback: function(cb, value, meta) {
        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');

        input.onchange = function() {
          var file = this.files[0];
          var reader = new FileReader();
          
          reader.onload = function () {
            var id = 'blobid' + (new Date()).getTime();
            var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
            var base64 = reader.result.split(',')[1];
            var blobInfo = blobCache.create(id, file, base64);
            blobCache.add(blobInfo);

            // call the callback and populate the Title field with the file name
            cb(blobInfo.blobUri(), { title: file.name });
          };
          reader.readAsDataURL(file);
        };
    
    input.click();
  },
  images_upload_handler: function (blobInfo, success, failure) {
      var xhr, formData;
      xhr = new XMLHttpRequest();
      xhr.withCredentials = false;

      xhr.open('POST', '<?php echo base_url(); ?>postAcceptor.php');
      xhr.onload = function() {
        var json;

        if (xhr.status != 200) {
        failure('HTTP Error: ' + xhr.status);
        return;
        }
        json = JSON.parse(xhr.responseText);

        if (!json || typeof json.location != 'string') {
        failure('Invalid JSON: ' + xhr.responseText);
        return;
        }
        success(json.location);
      };
      formData = new FormData();
      formData.append('file', blobInfo.blob(), fileName(blobInfo));
      xhr.send(formData);
      },
});
</script>
<!-- End TinyMCE Script -->

<!-- Admin Font Weight Override - Fixed for Font Awesome Icons -->
<style type="text/css">
/* Font Awesome Icon Fix - Preserve original font properties */
.fa, 
[class^="fa-"], 
[class*=" fa-"],
i.fa,
i[class^="fa-"],
i[class*=" fa-"] {
	font-family: FontAwesome !important;
	font-weight: normal !important;
	font-style: normal !important;
	text-decoration: inherit !important;
	-webkit-font-smoothing: antialiased !important;
	-moz-osx-font-smoothing: grayscale !important;
	display: inline-block !important;
	font-variant: normal !important;
	text-transform: none !important;
	line-height: 1 !important;
	vertical-align: baseline !important;
}

/* Ensure Font Awesome icons are not affected by our font overrides */
* .fa:before, 
* [class^="fa-"]:before, 
* [class*=" fa-"]:before {
	font-family: FontAwesome !important;
	font-weight: normal !important;
	font-style: normal !important;
}

/* Admin Font Weight Fix - Exclude Font Awesome */
/* Navigation elements */
.navbar:not(.fa),
.navbar *:not(.fa):not([class^="fa-"]):not([class*=" fa-"]),
.navbar-nav:not(.fa),
.navbar-nav *:not(.fa):not([class^="fa-"]):not([class*=" fa-"]),
.nav-item:not(.fa),
.nav-item *:not(.fa):not([class^="fa-"]):not([class*=" fa-"]),
.nav-link:not(.fa),
.dropdown-item:not(.fa),
.dropdown-menu:not(.fa),
.dropdown-menu *:not(.fa):not([class^="fa-"]):not([class*=" fa-"]) {
	font-weight: normal !important;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
}

/* Sidebar elements - Exclude Font Awesome */
.col-md-4:not(.fa),
.col-md-4 *:not(.fa):not([class^="fa-"]):not([class*=" fa-"]),
.col-md-4 section:not(.fa),
.col-md-4 section *:not(.fa):not([class^="fa-"]):not([class*=" fa-"]),
.col-md-4 section a:not(.fa),
.col-md-4 section span:not(.fa) {
	font-weight: normal !important;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
}

/* Chrome-specific rendering improvements - Exclude Font Awesome */
.navbar-nav .nav-link:not(.fa),
.navbar-nav .nav-item a:not(.fa),
.dropdown-item:not(.fa),
.col-md-4 section a:not(.fa),
.col-md-4 section span:not(.fa) {
	font-weight: normal !important;
	-webkit-font-smoothing: antialiased !important;
	-moz-osx-font-smoothing: grayscale !important;
	text-rendering: optimizeLegibility !important;
}
</style>

</head>  