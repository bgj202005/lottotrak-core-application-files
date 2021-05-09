<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Predictions_m extends MY_Model
{
	protected $_table_name = 'lottery_predictions';
	protected $_order_by = 'id';
	public $rules = array(
        'ball_predict' => array(
            'field' => 'ball_predict',
            'label' => 'Number of Balls to Predict',
            'rules' => 'trim|required|callback__range_ball_values'
		)
	);

	const DIR = 'combinations';

/** This function returns the total count of the number of possible unique
 * 	combinations there are of N distinct items selected R at a time. The
 * 	sequential order of the items in each group is NOT important.
 * 	Only the collective content matters, regardless of order. 
 *	 Author   : Jay Tanner - 2014
 *   Language : PHP v5.x
 * 	 @param		integer	$N	distinct items (3 - 9 Numbers Drawn)
 *   @param 	integer $R  Number of Predicted Numbers (3 - 50)
 *	 @return	integer	$C	Number of Distinct Combinations
*/
 	public function bcComb_N_R ($N, $R)
	{
	$C = 1;

	for ($i=0;   $i < $N-$R;   $i++)
		{
		$C = bcdiv(bcmul($C, $N-$i), $i+1);
		}
	return $C;
	}
	/**
	 * Returns the Lottery Combination File(s), if does not exist return FALSE
	 * 
	 * @param       integer	$lotto_id	Foriegn Key to the orresponding Lottery
	 * @return     	object 	$result		Return row, if lottery combination file(s) previously exists for the given lottery, else no record found and return false			
	 */
	public function lottery_combination_files($lotto_id)
	{

			$sql = "SELECT * FROM `lottery_combination_files` WHERE `lottery_id`=".$lotto_id;
			$result = $this->db->query($sql);
			
			if (empty($result->row())) return FALSE;
	return $result->result_object;
	}

	/**
	 * Returns a Lottery Combination Record (only one), if does not exist return FALSE
	 * 
	 * @param       string	$name		The name of the file_name of the combination file without the .txt extention
	 * @return     	object 	$result		Return row, if lottery combination file(s) previously exists for the given lottery, else no record found and return false			
	 */
	public function lottery_combination_record($name)
	{

			$sql = "SELECT * FROM `lottery_combination_files` WHERE `file_name`=".$name." LIMIT 1";
			$result = $this->db->query($sql);
			
			if (empty($result->row())) return FALSE;
	return $result->result_object;
	}
	/** 
	* Insert Lottery Combination File data of current lottery
	* 
	* @param 	array	$data		key / value pairs of new Lottery Combination File to be inserted / updated
	* @return   none	
	*/
	public function lottery_combo_save($data)
	{
		$this->db->reset_query();
		$this->db->set($data);		// Set the query with the key / value pairs
		return $this->db->insert('lottery_combination_files');
	}
	
}