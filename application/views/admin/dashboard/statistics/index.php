<style>
	table{
    	width:100%;
	}
	tr{
		font-size: 0.60em; /* Minimum size before horizontal toolbar appears under list */
	}
	label {
    	display: inline-flex;
    	margin-bottom: .5rem;
    	margin-top: .5rem;
}
</style>	
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
				<td style = "text-align:center; white-space: nowrap;">State/Prov</td>
				<td style = "text-align:center; white-space: nowrap;">Country</td>
				<td style = "text-align:center; white-space: nowrap;">Date</td>
				<td style = "text-align:center; white-space: nowrap;">Drawn Numbers</td>
				<td style = "text-align:center; white-space: nowrap;">Sum(100)</td>
				<td style = "text-align:center; white-space: nowrap;">Sum(LD)</td>
				<td style = "text-align:center; white-space: nowrap;">Repeaters</td>
				<th>Stats</th>
				<th style = "text-align:center; white-space: nowrap;">H-W-C</th>
				<th>Followers</th>
				<th>Friends</th>
				<th>Calculate</th>
				<th style = "text-align:center; white-space: nowrap;">ReCalc?</th>
			</tr>
		</thead>
		<tbody>
	<?php if (count($lotteries)): foreach($lotteries as $lottery): ?>
	<tr>
		<td style = "text-align:center;"><?php if (!empty($lottery->lottery_image)) 
			{ 
				if (DIRECTORY_SEPARATOR === '/') 
				{
    			// unix, linux, mac
					$path = $this->input->server('DOCUMENT_ROOT').'/images/uploads/'.$lottery->lottery_image;
				}
				else
				{
				// windows	
					$path =	base_url().'images/uploads/'.$lottery->lottery_image;
				}
				$image_info = getimagesize($path); 
				$extra = array('width' => $image_info[0]/2, 'height' => $image_info[1]/2);
				echo img(base_url().'images/uploads/'.$lottery->lottery_image, FALSE, $extra); 
			} ?></td>
		<td style = "text-align:center; white-space: nowrap;"><?php echo anchor('admin/statistics/view_draws/'.$lottery->id, $lottery->lottery_name);?></td>
		<td style = "text-align:center; white-space: nowrap;"><?=$lottery->lottery_state_prov; ?></td>
		<td style = "text-align:center; white-space: nowrap;"><?=$lottery->lottery_country_id; ?></td>
		<td style = "text-align:center; white-space: nowrap;"><?=($lottery->last_date!='NA' ? date("D, M-d-Y",strtotime(str_replace('/','-',$lottery->last_date))) : 'N/A'); ?></td>
		<td style = "text-align:center; white-space: nowrap;"><?=($lottery->last_draw!='NA' ? $lottery->last_draw : 'N/A'); ?></td>
		<td style = "text-align:center;"><?=$lottery->average_sum; ?></td>
		<td style = "text-align:center;"><?=$lottery->sum_last; ?></td>
		<td style = "text-align:center;"><?=$lottery->repeaters; ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_stat('admin/statistics/view_draws/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_hwc('admin/statistics/h_w_c/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_followers('admin/statistics/followers/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_friends('admin/statistics/friends/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_calculate('admin/statistics/calculate/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><input type="checkbox" name="recalc" value="<?=$lottery->id;?>" class="recalc<?=$lottery->id;?>" id="recalc" <?=($lottery->last_draw!='NA' ? '' : 'disabled');?> >
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
 var url;
  $('.calculate').click(function(){
	$('#status').css('display', 'block');
	$('#message').css('display', 'none'); 
	document.getElementById("status").innerHTML = "Updating Lottery. Please Wait.";
	setTimeout(fade_out, 1500);
	});
	<?php if (count($lotteries)): foreach($lotteries as $lottery): ?>
	$('.recalc<?=$lottery->id;?>').click(function(){
	url = "<?=base_url();?>admin/statistics/recalc/<?=$lottery->id;?>";
	$('#message').css('display', 'none'); 
	$('#status').css('display', 'block');
	document.getElementById("status").innerHTML = "Updating the <?=$lottery->lottery_name;?> H-W-C, Followers and Friends Statistics to <?=date("l, M d, Y",strtotime(str_replace('/','-',$lottery->last_date)))?>.";
	redirect(url);
	});
	<?php endforeach; ?>
	<?php endif; ?>
	$('.stats').click(function(){
	$('#status').css('display', 'block');
	$('#message').css('display', 'none'); 
	document.getElementById("status").innerHTML = "Retrieving the Culumlative Statistics and History. Please Wait.";
	setTimeout(fade_out, 10500);
	});
	$('.h-w-c').click(function(){
	$('#status').css('display', 'block');
	$('#message').css('display', 'none'); 
	document.getElementById("status").innerHTML = "Retrieving the Hot - Warm - Cold (H-W-C) Numbers and History. Please Wait.";
	setTimeout(fade_out, 10500);
	});
	$('.followers').click(function(){
	$('#status').css('display', 'block');
	$('#message').css('display', 'none'); 
	document.getElementById("status").innerHTML = "Retrieving the Followers and History for the next draw. Please Wait.";
	});
	$('.friends').click(function(){
	$('#status').css('display', 'block');
	$('#message').css('display', 'none'); 
	document.getElementById("status").innerHTML = "Retrieving the Friends of numbers and History for the next draw. Please Wait.";
	});
	function fade_out() {
      $("#status").fadeOut();
    }
});
function redirect(url) {
   window.location.href = url; }   
</script>