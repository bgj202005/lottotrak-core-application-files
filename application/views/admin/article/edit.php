
<?php echo validation_errors(); ?>
<?php echo form_open(base_url()."admin/article/edit/".$article->id); ?>
<h2><?php echo empty($article->id) ? 'Add a new article' : 'Edit an Article '.$article->title; ?></h2>

<table class="table" style="width: 90%;">
	<tr>
		<td style="width: 20%;">Publication Date:</td>
		<td>
			<div class="form-group">
				<div class="input-group date" id="datepicker1" data-provide="datepicker"> 
					<?php $attr = array ('maxlength' => '10', 'class' => 'datepicker', 'style' => 'width:12%');  
					echo form_input('pubdate', set_value('pubdate', date("d-m-Y",strtotime(str_replace('/','-',$article->pubdate)))), $attr); ?>  
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				</div>
			</div> 
		 <div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div></div> 
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
		<td style="width: 20%;">Lottery Article Content:</td>
		<td><?php echo form_textarea('body', strip_slashes($article->body), 'id="editarea"');
			?></td>
	</tr>
	<tr>
		<td colspan = "2">
			<div id="accordion">
    		<div class="card">
    			<div class="card-header" id="heading-1">
					<h5 class="mb-0">
        			<a role="button" data-toggle="collapse" href="#collapse-1" aria-expanded="true" aria-controls="collapse-1">
          			Optional SEO</a></h5>
    			</div>
    				<div id="collapse-1" class="collapse" data-parent="#accordion" aria-labelledby="heading-1">
      				<div class="card-body">
						Page Description (80 Char Max.):<br />
						<?php $attr = array('maxlength' => '80', 'style' => 'width:90%;'); 
						echo form_input('desription', set_value('description', (!empty($article->description) ? $article->description : '')), $attr); ?>
						<br /><br />Canonical Tag?
						<?php $extra = array('class' => 'col-form-label col-form-label-md', 'id' => 'defaultCheck2', 'style' => 'margin-left:10px; text-align:left;');
						 echo form_checkbox('canonical', set_value('canonical', '1'), set_checkbox('canonical', '1', (!empty($article->canonical))), $extra);  ?>
					</div>
    			</div>
  			</div>
		</div>
		</td><td></td>  
	</tr>
	<tr>	
		<td><?php echo form_submit('submit', 'Save and Exit', '
			class="btn btn-primary"'); ?></td>
		<td><?php 
		$js = "javascript: form.action='".base_url()."admin/article/edit/".$article->id."/save'";
		$class = "btn btn-primary";
		$attributes = array(
			'class' 	=> "$class",
			'onClick' 	=> "$js", 
			'style' 	=> "margin-right:20px; padding:5px;",
		);
		echo form_submit('article_edit', 'Save and No Exit', $attributes);  
		$js = "location.href='".base_url()."admin/article/'";
		$class = "btn btn-primary";
		$attributes = array(
			'class' 	=> "$class",
			'onClick' 	=> "$js", 
			'style' 	=> "margin-left:20px; padding:5px;"
		);
		echo form_button('article_cancel', 'Cancel Article Edit', $attributes); ?>
	</td>	
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