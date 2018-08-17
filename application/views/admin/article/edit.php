
<?php echo validation_errors(); ?>
<?php echo form_open(); ?>
<h2><?php echo empty($article->id) ? 'Add a new article' : 'Edit an Article '.$article->title; ?></h2>

<table class="table" style="width: 80%;">
	<tr>
		<td style="width: 20%;">Publication Date:</td>
		<td>
			<div class="form-group">
				<div class="input-group date" id="datepicker1" data-provide="datepicker"> 
					<?php echo form_input('pubdate', set_value('pubdate', date("d-m-Y",strtotime(str_replace('/','-',$article->pubdate)))),'class="datepicker" style ="width:100%"'); ?>  
					<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
				</div>
			</div> <!--  <div class="input-group date" data-provi/**
			 * Function Name
			 *
			 * Function description
			 *
			 * @access	public
			 * @param	type	name
			 * @return	type	
			 */
			 
			if (! function_exists('function_name'))
			{
				function function_name($param = '')
				{
					
				}
			}de="datepicker" style = "postion: absolute; float:left; width:30%">
		<?php // echo form_input('pubdate', set_value('pubdate', $article->pubdate),'class="datepicker" style ="width:100%"'); ?> 
		 <div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div></div> -->
		</td>
	</tr>
	<tr>
		<td style="width: 20%;">Title:</td>
		<td><?php echo form_input('title', set_value('title', $article->title), 'style = "width:100%;"'); ?></td>
	</tr>
	<tr>
		<td style="width: 20%;">Slug:</td>
		<td><?php echo form_input('slug', set_value('slug', $article->slug), 'style = "width:100%;"'); ?></td>
	</tr>
	<tr>
		<td style="width: 20%;">Body:</td>
		<td><?php echo form_textarea('body', $article->body, 'class=tinymce'); ?></td>
	</tr>
	<tr>
		<td><?php echo form_submit('submit', 'Submit', '
			class="btn btn-primary"'); ?></td>
	</tr>
</table>

<script type="text/javascript">
$(function() {
  $('.input-group').datepicker({
	Default: 'dd-mm-yyyy',
	format: 'dd-mm-yyyy',  
    orientation: 'bottom auto',
    todayBtn: "linked",
    autoclose: true,
    clearBtn: true
  });
});
</script>