<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<style>
	table{
    width:100%;
	}
	#draws_filter{
    float:right;
	}
	#draws_paginate{
    float:right;
	}
	label {
    	display: inline-flex;
    	margin-bottom: .5rem;
    	margin-top: .5rem;
}	
</style>
	<section>
		<h2><?php echo 'View Lottery Draws for: '.$lottery->lottery_name; ?></h2>
		<h3 style = "text-align:center;" id="view_message" class = "text-danger"><?= $message; ?></h3>
			<div class="table-responsive">
			<?php echo form_open(''); 
			if (isset($lottery->next_draw_date)) echo form_hidden('next_date', $lottery->next_draw_date); 
			if(isset($add)) echo form_hidden('add', $add); 
			if(isset($edit)) echo form_hidden('edit', $edit); ?>
			<table id="draws" class="table table-striped table-bordered display" style="width:100%" data-order='[[ 1, "desc" ]]'>
				<thead>
					<tr>
						<th><input type="checkbox" onclick="checkAll(this)" <?php if(isset($add)) echo "disabled"; ?>></th>
						<th>Draw #</th>
						<th>Draw Date</th>
						<th class="no-sort">Ball 1</th>
						<th class="no-sort">Ball 2</th>
						<th class="no-sort">Ball 3</th>
							<?php if (intval($lottery->balls_drawn)>=4): ?><th class="no-sort">Ball 4</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=5): ?><th class="no-sort">Ball 5</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=6): ?><th class="no-sort">Ball 6</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=7): ?><th class="no-sort">Ball 7</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=8): ?><th class="no-sort">Ball 8</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)==9): ?><th class="no-sort">Ball 9</th><?php endif; ?>
						<?php if (intval($lottery->extra_ball)==1): ?><th class="no-sort">Extra Ball</th><?php endif; ?>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($draws as $draw): ?>
				<tr>
						<td><input type="checkbox" name = "draw[]" value = "<?=$draw->id; ?>" <?php if(isset($add)) echo "disabled"; 
						elseif (isset($edit)&&$selected) 
						{
							$flagged = FALSE;
							foreach ($selected as $choice)
							{
								if ($choice==$draw->id) 
								{
									echo 'CHECKED';
									$flagged = TRUE;
								}
							}
						} ?>></td>
						<td><?= $draw->draw ?></td>
						<td><?php 
							echo date("D, M d, Y", strtotime(str_replace('/','-',$draw->draw_date))); ?></td>
						<?php if (isset($edit)&&isset($flagged)&&$flagged)	// Draw is to be edited
						{ 
							$extra = array('id' => 'formGroupInputLarge', 'maxlength' => '2', 'size' => '4');
							$extra['class'] = (form_error('ball_1[]') ? 'form-control is-invalid' : 'form-control is-valid');
							if (empty(set_value('ball_1[]'))) $extra['class'] = 'form-control';
							echo '<td>'.form_input('ball_1[]', set_value('ball_1[]',$draw->ball1), $extra).'</td>';
							$extra['class'] = (form_error('ball_2[]') ? 'form-control is-invalid' : 'form-control is-valid');
							if (empty(set_value('ball_2[]'))) $extra['class'] = 'form-control';
							echo '<td>'.form_input('ball_2[]', set_value('ball_2[]',$draw->ball2), $extra).'</td>';
							$extra['class'] = (form_error('ball_3[]') ? 'form-control is-invalid' : 'form-control is-valid');
							if (empty(set_value('ball_3[]'))) $extra['class'] = 'form-control';
							echo '<td>'.form_input('ball_3[]', set_value('ball_3[]',$draw->ball3), $extra).'</td>';
							if (intval($lottery->balls_drawn)>=4): 
								$extra['class'] = (form_error('ball_4[]') ? 'form-control is-invalid' : 'form-control is-valid');
								if (empty(set_value('ball_4[]'))) $extra['class'] = 'form-control';
								echo '<td>'.form_input('ball_4[]', set_value('ball_4[]',$draw->ball4), $extra).'</td>'; endif;
							if (intval($lottery->balls_drawn)>=5):
								$extra['class'] = (form_error('ball_5[]') ? 'form-control is-invalid' : 'form-control is-valid');
								if (empty(set_value('ball_5[]'))) $extra['class'] = 'form-control'; 
								echo '<td>'.form_input('ball_5[]', set_value('ball_5[]',$draw->ball5), $extra).'</td>'; endif;
							if (intval($lottery->balls_drawn)>=6): 
								$extra['class'] = (form_error('ball_6[]') ? 'form-control is-invalid' : 'form-control is-valid');
								if (empty(set_value('ball_6[]'))) $extra['class'] = 'form-control';
								echo '<td>'.form_input('ball_6[]', set_value('ball_6[]',$draw->ball6), $extra).'</td>'; endif;
							if (intval($lottery->balls_drawn)>=7): 
								$extra['class'] = (form_error('ball_7[]') ? 'form-control is-invalid' : 'form-control is-valid');
								if (empty(set_value('ball_7[]'))) $extra['class'] = 'form-control';
								echo '<td>'.form_input('ball_7[]', set_value('ball_7[]', $draw->ball7), $extra).'</td>'; endif;
							if (intval($lottery->balls_drawn)>=8): 
								$extra['class'] = (form_error('ball_8[]') ? 'form-control is-invalid' : 'form-control is-valid');
								if (empty(set_value('ball_8[]'))) $extra['class'] = 'form-control';
								echo '<td>'.form_input('ball_8[]', set_value('ball_8[]', $draw->ball8), $extra).'</td>'; endif;
							if (intval($lottery->balls_drawn)==9): 
								$extra['class'] = (form_error('ball_9[]') ? 'form-control is-invalid' : 'form-control is-valid');
								if (empty(set_value('ball_9[]'))) $extra['class'] = 'form-control';
								echo '<td>'.form_input('ball_9[]', set_value('ball_9[]', $draw->ball9), $extra).'</td>'; endif;
							if (intval($lottery->extra_ball)==1): 
								$extra['class'] = (form_error('extra_ball[]') ? 'form-control is-invalid' : 'form-control is-valid');
								if (empty(set_value('extra_ball[]'))) $extra['class'] = 'form-control';
								echo '<td>'.form_input('extra_ball[]', set_value('extra_ball[]', $draw->extra), $extra).'</td>'; endif;
						} 
						else // Only Display
						{ ?>
							<td><?= $draw->ball1; ?></td>
							<td><?= $draw->ball2; ?></td>
							<td><?= $draw->ball3; ?></td>
							<?php if (intval($lottery->balls_drawn)>=4): ?><td><?= $draw->ball4; ?></td><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=5): ?><td><?= $draw->ball5; ?></td><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=6): ?><td><?= $draw->ball6; ?></td><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=7): ?><td><?= $draw->ball7; ?></td><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=8): ?><td><?= $draw->ball8; ?></td><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)==9): ?><td><?= $draw->ball9; ?></td><?php endif; ?>			
							<?php if (intval($lottery->extra_ball)==1): ?><td><?= $draw->extra; ?><?php endif; ?></td>
					<?php } ?>
				</tr>
					<?php  endforeach; ?>		
					<?php if(isset($add))
					{
						echo '<tr>';
						echo '<td></td>';
						echo '<td>'.$lottery->num.'</td>';
						echo '<td>'.date("D, M d, Y", strtotime($lottery->next_draw_date)).'</td>';
						$extra = array('id' => 'formGroupInputLarge', 'maxlength' => '2', 'size' => '4');
						$extra['class'] = (form_error('ball1') ? 'form-control is-invalid' : 'form-control is-valid');
						if (empty(set_value('ball1'))) $extra['class'] = 'form-control';
						echo '<td>'.form_input('ball1', set_value('ball1'), $extra).'</td>';
						$extra['class'] = (form_error('ball2') ? 'form-control is-invalid' : 'form-control is-valid');
						if (empty(set_value('ball2'))) $extra['class'] = 'form-control';
						echo '<td>'.form_input('ball2', set_value('ball2'), $extra).'</td>';
						$extra['class'] = (form_error('ball3') ? 'form-control is-invalid' : 'form-control is-valid');
						if (empty(set_value('ball3'))) $extra['class'] = 'form-control';
						echo '<td>'.form_input('ball3', set_value('ball3'), $extra).'</td>';
						if (intval($lottery->balls_drawn)>=4): 
							$extra['class'] = (form_error('ball4')||empty(set_value('ball4')) ? 'form-control is-invalid' : 'form-control is-valid');
							if (empty(set_value('ball1'))) $extra['class'] = 'form-control';
							echo '<td>'.form_input('ball4', set_value('ball4'), $extra).'</td>'; endif;
						if (intval($lottery->balls_drawn)>=5): 
							$extra['class'] = (form_error('ball5') ? 'form-control is-invalid' : 'form-control is-valid');
							if (empty(set_value('ball5'))) $extra['class'] = 'form-control';
							echo '<td>'.form_input('ball5', set_value('ball5'), $extra).'</td>'; endif;
						if (intval($lottery->balls_drawn)>=6): 
							$extra['class'] = (form_error('ball6') ? 'form-control is-invalid' : 'form-control is-valid');
							if (empty(set_value('ball6'))) $extra['class'] = 'form-control';
							echo '<td>'.form_input('ball6', set_value('ball6'), $extra).'</td>'; endif;
						if (intval($lottery->balls_drawn)>=7): 
							$extra['class'] = (form_error('ball7') ? 'form-control is-invalid' : 'form-control is-valid');
							if (empty(set_value('ball7'))) $extra['class'] = 'form-control';
							echo '<td>'.form_input('ball7', set_value('ball7'), $extra).'</td>'; endif;
						if (intval($lottery->balls_drawn)>=8): 
							$extra['class'] = (form_error('ball8') ? 'form-control is-invalid' : 'form-control is-valid');
							if (empty(set_value('ball8'))) $extra['class'] = 'form-control';
							echo '<td>'.form_input('ball8', set_value('ball8'), $extra).'</td>'; endif;
						if (intval($lottery->balls_drawn)==9): 
							$extra['class'] = (form_error('ball9') ? 'form-control is-invalid' : 'form-control is-valid');
							if (empty(set_value('ball9'))) $extra['class'] = 'form-control';
							echo '<td>'.form_input('ball9', set_value('ball9'), $extra).'</td>'; endif;
						if ($lottery->extra_ball): 
							$extra['class'] = (form_error('extra_ball') ? 'form-control is-invalid' : 'form-control is-valid');
							if (empty(set_value('extra_ball'))) $extra['class'] = 'form-control';
							echo '<td>'.form_input('extra_ball', set_value('extra_ball'), $extra).'</td>'; endif;
						echo '</tr>';
					} ?>		
				</tbody>
				<tfoot>
					<tr>
						<th><input type="checkbox" onclick="checkAll(this)" <?php if(isset($add)) echo "disabled"; ?>></th>
						<th>Draw #</th>
						<th>Draw Date</th>
						<th class="no-sort">Ball 1</th>
						<th class="no-sort">Ball 2</th>
						<th class="no-sort">Ball 3</th>
						<?php if (intval($lottery->balls_drawn)>=4): ?><th class="no-sort">Ball 4</th><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)>=5): ?><th class="no-sort">Ball 5</th><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)>=6): ?><th class="no-sort">Ball 6</th><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)>=7): ?><th class="no-sort">Ball 7</th><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)>=8): ?><th class="no-sort">Ball 8</th><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)==9): ?><th class="no-sort">Ball 9</th><?php endif; ?>
						<?php if (intval($lottery->extra_ball)==1): ?><th class="no-sort">Extra Ball</th><?php endif; ?>
					</tr>	
				</tfoot>
			</table>
		</div>
	<div style = "text-align: center;">
	<?php $js = "javascript: form.action='".base_url()."admin/lotteries/draw_add/".$lottery->id."'";
		$class = "btn btn-primary btn-lg btn-danger";
		$attributes = array(
			'class' 	=> "$class",
			'onClick' 	=> "$js", 
			'style' 	=> "margin-left:20px; padding:5px;",
		);
		if(isset($edit)) $attributes['disabled'] = 'disabled';
		if(isset($lottery->next_draw_date)&&isset($lottery->num))
		{
			$label = (isset($add) ? 'Save New Draw' : 'Next: '.$lottery->next_draw_date.' ('.$lottery->num.')');
		}
		else
		{
			$label = "No Draws";
			$attributes['disabled'] = 'disabled';
		}
		echo form_submit('draw_add', $label, $attributes);
		$js = "javascript: form.action='".base_url()."admin/lotteries/draw_edit/".$lottery->id."'";
		$class = "btn btn-primary btn-lg btn-info";
		$attributes = array(
			'class' 	=> "$class",
			'onClick' 	=> "$js", 
			'style' 	=> "margin-left:20px; padding:5px;",
		);
		if(isset($add)) $attributes['disabled'] = 'disabled';
		$label = (isset($edit) ? 'Update Selected Draw(s)' : 'Manually Edit Draw(s)');
		echo form_submit('draw_edit', $label, $attributes);  					
		$js = "location.href='".base_url()."admin/lotteries/edit/".$lottery->id."'";
		$class = "btn btn-primary btn-lg btn-info";
		$attributes = array(
			'class' 	=> "$class", 
			'onClick' 	=> "$js", 
			'style' 	=> "margin-left:20px; padding:5px;",
			'role'		=> 'button'
		);
		echo form_button('lotteries_edit', 'Edit Lottery Profile', $attributes); 
		$js = "location.href='".base_url()."admin/lotteries/import/".$lottery->id."'";
		$attributes = array(
			'class' 	=> "$class", 
			'onClick' 	=> "$js", 
			'style' 	=> "margin-left:20px; padding:5px;",
			'role'		=> 'button'
		);
		echo form_button('lotteries_import', 'Import New Draws', $attributes); 
		$js = "location.href='".base_url()."admin/lotteries/'";
		$attributes = array(
			'class' 	=> "$class",
			'onClick' 	=> "$js", 
			'style' 	=> "margin-left:20px; padding:5px;"
		);
		echo form_button('lotteries_list', 'Back to Lotteries List', $attributes); 
		echo form_close(); ?>
	</div>
	</section>
<script>
$(document).ready(function() {
    $('#draws').DataTable(
         {     
      "aLengthMenu": [[5, 10, 25, -1], [5, 10, 25, "All"]],
        "iDisplayLength": 10,
		"columnDefs": [ {
          "targets": 'no-sort',
          "orderable": false,
    } ]
       } 
        );
}
 );
function checkAll(bx) {
  var cbs = document.getElementsByTagName('input');
  for(var i=0; i < cbs.length; i++) {
    if(cbs[i].type == 'checkbox') {
      cbs[i].checked = bx.checked;
    }
  }
}
</script>