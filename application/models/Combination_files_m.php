<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Combination Files Model
 * Handles all combination file operations and database management
 */
class Combination_files_m extends MY_Model
{
    protected $_table_name = 'lottery_combination_files';
    protected $_order_by = 'file_name';
    
    const DIR = 'combinations';

    /**
     * Returns the Lottery Combination File(s), if does not exist return FALSE
     * 
     * @param       integer	$pick_id	Related to the number of picks in a lottery. eg. 3, 4, 5, 6, 7, 8, 9
     * @return     	object 	$result		Return row, if lottery combination file(s) previously exists for the given lottery, else no record found and return false			
     */
    public function lottery_combination_files($R)
    {
        // Fetch combination files based on pick_id
        return $this->db->where('R', $R)
                        ->get('lottery_combination_files')
                        ->result();
    }

    /**
     * Returns the Lottery Combination File(s), if does not exist return FALSE
     * 
     * @param       none
     * @return     	object 	$result		Return row, if lottery combination file(s) previously exists for the given lottery, else no record found and return false			
     */
    public function all_combination_files()
    {
        $sql = "SELECT * FROM `lottery_combination_files`";
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
        $result = $this->db->where('file_name', $name)
                           ->limit(1)
                           ->get('lottery_combination_files');
        
        if (empty($result->row())) return FALSE;
        return $result->row();
    }
    
    /** 
     * Insert Lottery Combination File data of current lottery
     * 
     * @param 	array	$data		key / value pairs of new Lottery Combination File to be inserted / updated
     * @return   none	
     */
    public function lottery_combo_save($data)
    {
        // Ensure the data includes pick_id instead of lottery_id
        $combo_data = [
            'file_name' => $data['file_name'],
            'N' => $data['N'], // Number of predictions
            'R' => $data['R'], // Pick game (e.g., 3, 4, 5, 6, etc.)
            'CCCC' => $data['CCCC'], // Calculated combinations
        ];
        return $this->db->insert('lottery_combination_files', $combo_data);
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
            // This is a Linux server, so the path must be changed to reflect the server
            $full_path = '/home/metad231/lottotrak.com/'.self::DIR.'/'.$name.'.txt';
        }
        if(!file_exists($full_path))
        {
            // If the file does not exist, create it
            $fp = fopen($full_path, 'w');
            fclose($fp);
        }
        else //This is a Linux server, so the path must be changed to reflect the server
        {
            $full_path = self::DIR.DIRECTORY_SEPARATOR.$name.'.txt';
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

    /** 
     * Sort the array into a text line and save to the provided text file after complete
     * 
     * @param 	string 	$name				Filename without the .txt extension
     * @param 	array	$combs_array		Array of Combinations in the form of [0] => [1] = 1, [2] = 2, [3] = 3, [4] = 4, [5] = 5, [6] = 6
     * @return   boolean $success	TRUE / FALSE, TRUE = Saved to text file successfully, FALSE = Something when wrong	
     */
    public function text_combs_save($name, $combs_array)
    {
        $success = TRUE;
        $returned_path = $this->full_path($name); 
        
        // Open file in write mode to truncate/clear existing content
        $fp = fopen($returned_path, 'w');
        if($fp)	
        {
            foreach($combs_array as $combo => $key)
            {
                $str = implode(' ', $key);
                fwrite($fp, $str);
                fwrite($fp, "\n"); // NB double quotes must be used here
            }
        }
        else	// Can't Open the File?
        {
            $success = FALSE;
        }
        fclose($fp);
        return $success;
    }

    /** 
     * Sort the array into a text line and save to the provided text file after complete
     * 
     * @param 	string	$name			Filename without the .txt extension
     * @param	integer	$combs_count	Number of actual combinations in the array
     * @return   boolean $saved			TRUE / FALSE, True $combos >= $combos_count, Everything else is FALSE
     */
    public function combs_already($name, $combs_count)
    {
        $saved = FALSE;
        $returned_path = $this->full_path($name); 
        $combs = 0;
        $fp = fopen($returned_path, 'r');
        while (!feof($fp)) 
        {
            $combs++;
            if($combs>=$combs_count)
            {
                $saved = TRUE;
                break; // Abruptly leave the loop
            }
            if(!fgets($fp)) break; // No Return of anything, indicated no combinations have been previously saved.
        }
        fclose($fp);	
        
        return $saved; // Returns TRUE or False based on the actual count of the combinations in the text file.
    }

    /** Checks if a combination file has been generated for the given filename.
     */
    public function is_combination_generated($filename)
    {
        $full_path = $this->full_path($filename);
        
        if (file_exists($full_path) && filesize($full_path) > 0) {
            return TRUE;
        }
        
        return FALSE; // File does not exist or is empty
    }

    /**
     * Check if a combination file has been generated for the given number of balls drawn
     * 
     * @param integer $balls_drawn Number of balls drawn for the lottery
     * @return boolean TRUE if file exists, FALSE otherwise
     */
    public function has_generated_file($balls_drawn)
    {
        $this->db->where('R', $balls_drawn);
        $query = $this->db->get('lottery_combination_files');
        
        return $query->num_rows() > 0;
    }

    /**
     * Get combination files for a lottery with active status based on saved filters
     * @param int $lottery_id
     * @return array
     */
    public function get_combination_files($lottery_id)
    {
        // Get the balls_drawn value for the lottery
        $this->db->select('balls_drawn');
        $this->db->from('lottery_profiles');
        $this->db->where('id', $lottery_id);
        $lottery = $this->db->get()->row();
        if (!$lottery) {
            return []; // Return an empty array if the lottery is not found
        }
        $balls_drawn = $lottery->balls_drawn;
        
        // Get current user ID for active status check
        $CI =& get_instance();
        $current_user_id = $CI->session->userdata('id');
        
        // Fetch combination files with left join to check for active saved filters
        $this->db->select('lcf.id, lcf.file_name, lcf.N, lcf.CCCC, 
                          COALESCE(MAX(lfc.active), 0) as active');
        $this->db->from('lottery_combination_files lcf');
        $this->db->join('lottery_combination_filters lfc', 
                       'lcf.id = lfc.combo_id AND lfc.user = 1 AND lfc.user_id = ' . (int)$current_user_id, 'left');
        $this->db->where('lcf.R', $balls_drawn); // Match the balls_drawn value
        $this->db->group_by('lcf.id, lcf.file_name, lcf.N, lcf.CCCC');
        $this->db->order_by('lcf.file_name', 'ASC');
        $query = $this->db->get();
        return $query->result_array(); // Return the result as an array
    }

    /**
     * Get combination ID by filename
     * 
     * @param string $name The combination filename (without .txt extension)
     * @return integer|null Unique ID for the combination or NULL if not found
     */
    public function get_combination_id($name)
    {
        // Trim the input to remove any leading/trailing whitespace
        $name = trim($name);
        // Check if the combination already exists in the database
        $this->db->select('id');
        $this->db->from('lottery_combination_files');
        $this->db->where('file_name', $name);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->id; // Return existing ID
        } else {
            return NULL; // return NULL if not found, or error
        }
    }

    /**
     * Load combination data for processing
     * 
     * @param string $name Filename
     * @param integer $pick Pick number
     * @param integer $i Iterator
     * @return array|false Combination data or FALSE on error
     */
    public function load_draws($name, $pick, $i)
    {
        $full_path = $this->full_path($name);
        
        if (!file_exists($full_path)) {
            return FALSE;
        }
        
        $draws = [];
        $lines = file($full_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $combination = explode(' ', trim($line));
            if (count($combination) == $pick) {
                $draws[] = $combination;
            }
        }
        
        if (empty($draws)) {
            return FALSE;
        }
        
        return $draws;
    }
}
