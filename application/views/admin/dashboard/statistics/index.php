<section>
	<h2>Lottery Profile Statistics</h2>
	<?php if (isset($message)) : ?> <h4 class="bg-warning" id = "message" style = "margin-top: 20px; text-align:center;"><?=$message; endif; ?></h4>
	<h4 id = "status" style = "margin-top: 20px; text-align:center; display:none;"></h4>	
	<h3 class="bg-success"  id = "counter" style = "margin-top:20px; text-align:center; display:none;">Current Draw has Statistics updated: <div id = "count"></div></h3>
	<div class = "status"></div>
	<div class="table-responsive">
	<div class ="table-responsive-xl">
	<table class="table table-striped">
		<thead>
			<tr> 
				<td style = "text-align:center; white-space: nowrap;">Logo</td>
				<td style = "text-align:center; white-space: nowrap;">Lottery</td>
				<td style = "text-align:center; white-space: nowrap;">State/Province</td>
				<td style = "text-align:center; white-space: nowrap;">Country</td>
				<td style = "text-align:center; white-space: nowrap;">Date</td>
				<td style = "text-align:center; white-space: nowrap;">Drawn Numbers</td>
				<td style = "text-align:center; white-space: nowrap;">Sum(100)</td>
				<td style = "text-align:center; white-space: nowrap;">Sum(LD)</td>
				<td style = "text-align:center; white-space: nowrap;">Repeats</td>
				<th>Stats</th>
				<th>Followers</th>
				<th>Friends</th>
				<th>Calculate</th>
			</tr>
		</thead>
		<tbody>
	<?php if (count($lotteries)): foreach($lotteries as $lottery): ?>
	<tr>
		<td style = "text-align:center;"><?php if (!empty($lottery->lottery_image)) 
			{ 
				$image_info = getimagesize(base_url().'images/uploads/'.$lottery->lottery_image); 
				$extra = array('width' => $image_info[0]/2, 'height' => $image_info[1]/2);
				echo img(base_url().'images/uploads/'.$lottery->lottery_image, FALSE, $extra); 
			} ?></td>
		<td style = "text-align:center;"><?php echo anchor('admin/lotteries/edit/'.$lottery->id, $lottery->lottery_name);?></td>
		<td style = "text-align:center;"><?=$lottery->lottery_state_prov; ?></td>
		<td style = "text-align:center;"><?=$lottery->lottery_country_id; ?></td>
		<td style = "text-align:center;" class = "small text-left"><?=($lottery->last_date!='NA' ? date("D, M-d-Y",strtotime(str_replace('/','-',$lottery->last_date))) : 'N/A'); ?></td>
		<td style = "text-align:center;" class = "small text-left"><?=($lottery->last_draw!='NA' ? $lottery->last_draw : 'N/A'); ?></td>
		<td style = "text-align:center;"><?=$lottery->average_sum; ?></td>
		<td style = "text-align:center;"><?=$lottery->sum_last; ?></td>
		<td style = "text-align:center;"><?=$lottery->repeaters; ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_stat('admin/statistics/view_draws/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_followers('admin/statistics/followers/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_friends('admin/statistics/friends/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_calculate('admin/statistics/calculate/'.$lottery->id); ?></td>
	</tr>
	<?php endforeach; ?> 
	
	<?php else: ?>
		<tr>
			<td colspan="12" style = "text-align:center">No Lotteries are available.</td>
		</tr>
<?php endif; ?>
		</tbody>
	</table>
	</div>
	</div>
</section>
<script>
$(document).ready(function(){
  $('#calculate').click(function(){
	$('#status').css('display', 'block'); 
	document.getElementById("status").innerHTML = "Updating Lottery. Please Wait.";
	//clear_timer = setInterval(get_database_stats, 500);
    setTimeout(fade_out, 1500);
	});
	function fade_out() {
      $("#status").fadeOut();
    }
	function get_database_stats() {
		$.ajax({
    	url:"<?php echo base_url(); ?>admin/statistics/update/<?=$this->lotteries_m->lotto_table_convert($lottery->lottery_name); ?>",
		dataType: "json",
		success:function(data)
		{
			$('#counter').css('display', 'block');
			$('#count').text(data);
			var width = Math.round((data.count/data.total)*100);
			if(width >= 100)
			{
				clearInterval(clear_timer);
				$('#counter').css('display', 'none');
				//$('#message').html('<div class="alert alert-success">Successfully Updated the Statistics for each draw.</div>');
			}
			if (data.error)
			{
				clearInterval(clear_timer);
				$('#counter').css('display', 'none');
				//$('#message').html('<div class="alert alert-danger">Problem with updating Statistics.</div>');
			}
		}
	  } ) 
	}
});
</script>