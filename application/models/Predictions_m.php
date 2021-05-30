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

	/**
	 * Returns the complete full path of the combination file including .txt file extension
	 * 
	 * @param       string	$name			Filename of the combination file without the .txt extention
	 * @return     	string	$full_path		The complete path of the filename. Different depending on Windows or Linux machines
	 */
	public function full_path($name)
	{
		if(DIRECTORY_SEPARATOR=='\\')
		{
		// Windows	
			$full_path = 'd:\\wamp64\\www\\lottotrak\\'.self::DIR.'\\'.$name.'.txt';
		}
		else 
		// Linux
		{
			$full_path = DIRECTORY_SEPARATOR.self::DIR.DIRECTORY_SEPARATOR.$name.'.txt';
		}
	return $full_path; // Full Path of Filename
	}

	/**
	 * Removes the record in the database from the filename (excluding the .txt extension)
	 * 
	 * @param       string	$name			The name of the file_name of the combination file without the .txt extention
	 * @return     	boolean	TRUE/FALSE		Returns TRUE on successful removal of the record, FALSE if the record could not be deleted
	 */
	public function delete_combination_record($name)
	{
		$this -> db -> where('file_name', $name);
    return $this -> db -> delete('lottery_combination_files');
	}
	/**
	 * Returns a Lottery Combination Record (only one), if does not exist return FALSE
	 * 
	 * @param       string	$name			The name of the file_name of the combination file without the .txt extention
	 * @return     	boolean	TRUE/FALSE		Returns TRUE on successful removal of the file in the /combinations/ directory, FALSE if the file could not be deleted
	 */
	public function delete_combination_file($name)
	{
		$full_path = $this->full_path($name);

	return unlink($full_path); // Remove File, TRUE successful, FALSE on error
	}
	/** Iterates the number of predictions and returns them in an array to
	 * be used in the Math Combinatorics combination methods
 	* @param	integer	$Pr		Predicted Numbers (e.g. 1 to 15)
 	* @return	array	$combs	Array of the number of predicted (1,2,3,4,5,6,7,8,9...15)
	*/
 	public function wheeled($Pr)
	{
		$c = 1;
		$combs = array();
		for ($i=0;   $i < $Pr;   $i++)
			{
				$combs[] = $c; // Add next predicted element onto the array
				$c++;
			}
	return $combs;
	}
	/** 
	* Sort the array into a text line and save to the provided text file after complete
	* 
	* @param 	array	$data		Array of Combinations in the form of [0] => [1] = 1, [2] = 2, [3] = 3, [4] = 4, [5] = 5, [6] = 6
	* @param 	string 	$name		Filename without the .txt extension
	* @return   boolean $success	TRUE / FALSE, TRUE = Saved to text file successfully, FALSE = Something when wrong	
	*/
	public function text_combo_save($name, $combo_array)
	{
		
		$success = TRUE;
		$fp = fopen($name.'txt', 'a');
		if($fp)	
		{
			foreach($combo_array as $combo => $key)
			{
				$str = implode(' ', $combo[$key]);
				fwrite($fp, $str);
				fwrite($fp, "\n"); // NB double quotes must be used here
			}
			fclose($fp);
		}
		else	// Can't Open the File?
		{
			$success = FALSE;
		}

	return $success;
	}
}