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
			<table id="draws" class="table table-striped table-bordered display" style="width:100%">
				<thead>
					<tr>
						<th><input type="checkbox" onclick="checkAll(this)"></th>
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
						<?php if (intval($lottery->extra_ball)>1): ?><th class="no-sort">Second Extra Ball</th><?php endif; ?>				
					</tr>
				</thead>
				<tbody>
				<?php foreach ($draws as $draw): ?>
				<tr>
						<td><input type="checkbox" name = ""></td>
						<td><?= $draw->draw; ?></td>
						<td><?php echo date("D, M d, Y", strtotime(str_replace('/','-',$draw->draw_date))); ?></td>
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
						<?php if (intval($lottery->extra_ball)>1): ?><td><?= $draw->extra; ?><?php endif; ?></td>
				</tr>
					<?php  endforeach; ?>		
				</tbody>
				<tfoot>
					<tr>
						<th><input type="checkbox" onclick="checkAll(this)"></th>
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
						<?php if (intval($lottery->extra_ball)>1): ?><th class="no-sort">Second Extra Ball</th><?php endif; ?>			
					</tr>	
				</tfoot>
			</table>
		</div>
	<div style = "text-align: center;">
	<?php $js = "location.href='".base_url()."admin/lotteries/add_draw/".$lottery->id."'";
		$class = "btn btn-primary btn-lg btn-danger";

		
		$attributes = array(
			'class' 	=> "$class", 
			'onClick' 	=> "$js", 
			'style' 	=> "margin-left:20px; padding:5px;",
			'role'		=> 'button'
		);
		echo form_button('lotteries_add', 'Add Lottery Draw', $attributes);
		$js = "location.href='".base_url()."admin/lotteries/manual_edit/".$lottery->id."'";
		$class = "btn btn-primary btn-lg btn-info";
		$attributes = array(
			'class' 	=> "$class", 
			'onClick' 	=> "$js", 
			'style' 	=> "margin-left:20px; padding:5px;",
			'role'		=> 'button'
		);
		echo form_button('lotteries_add', 'Manual Edit Draw(s)', $attributes);  					
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
		echo form_button('lotteries_manual', 'Import New Draws', $attributes); 
		$js = "location.href='".base_url()."admin/lotteries/'";
		$attributes = array(
			'class' 	=> "$class",
			'onClick' 	=> "$js", 
			'style' 	=> "margin-left:20px; padding:5px;"
		);
		echo form_button('lotteries_list', 'Back to Lotteries List', $attributes); 
		?>
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