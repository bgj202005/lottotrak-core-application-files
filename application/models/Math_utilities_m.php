<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mathematical Utilities Model
 * Handles mathematical calculations and combinatorial functions
 */
class Math_utilities_m extends MY_Model
{
    /**
     * This function returns the total count of the number of possible unique
     * combinations there are of N distinct items selected R at a time. The
     * sequential order of the items in each group is NOT important.
     * Only the collective content matters, regardless of order. 
     * Author   : Jay Tanner - 2014
     * Language : PHP v5.x
     * 
     * @param	integer	$N	distinct items (3 - 9 Numbers Drawn)
     * @param 	integer $R  Number of Predicted Numbers (3 - 50)
     * @return	integer	$C	Number of Distinct Combinations
     */
    public function bcComb_N_R($N, $R)
    {
        $C = 1;
        for ($i = 0; $i < $N - $R; $i++) {
            $C = bcdiv(bcmul($C, $N - $i), $i + 1);
        }
        return $C;
    }

    /**
     * Iterates the number of predictions and returns them in an array to
     * be used in the Math Combinatorics combination methods
     * 
     * @param	integer	$Pr		Predicted Numbers (e.g. 1 to 15)
     * @return	array	$combs	Array of the number of predicted (1,2,3,4,5,6,7,8,9...15)
     */
    public function wheeled($Pr)
    {
        $c = 1;
        $combs = array();
        for ($i = 0; $i < $Pr; $i++) {
            $combs[] = $c; // Add next predicted element onto the array
            $c++;
        }
        return $combs;
    }

    /**
     * Calculate factorial of a number
     * 
     * @param integer $n Number to calculate factorial for
     * @return integer Factorial result
     */
    public function factorial($n)
    {
        if ($n <= 1) {
            return 1;
        }
        
        $result = 1;
        for ($i = 2; $i <= $n; $i++) {
            $result *= $i;
        }
        
        return $result;
    }

    /**
     * Calculate combinations C(n,r) using standard formula
     * 
     * @param integer $n Total items
     * @param integer $r Items to choose
     * @return integer Number of combinations
     */
    public function combinations($n, $r)
    {
        if ($r > $n || $r < 0) {
            return 0;
        }
        
        if ($r == 0 || $r == $n) {
            return 1;
        }
        
        // Use the property C(n,r) = C(n,n-r) to minimize calculations
        if ($r > $n - $r) {
            $r = $n - $r;
        }
        
        $result = 1;
        for ($i = 0; $i < $r; $i++) {
            $result = $result * ($n - $i) / ($i + 1);
        }
        
        return round($result);
    }

    /**
     * Calculate permutations P(n,r) = n! / (n-r)!
     * 
     * @param integer $n Total items
     * @param integer $r Items to arrange
     * @return integer Number of permutations
     */
    public function permutations($n, $r)
    {
        if ($r > $n || $r < 0) {
            return 0;
        }
        
        $result = 1;
        for ($i = $n; $i > $n - $r; $i--) {
            $result *= $i;
        }
        
        return $result;
    }

    /**
     * Calculate probability as percentage
     * 
     * @param integer $favorable_outcomes Number of favorable outcomes
     * @param integer $total_outcomes Total possible outcomes
     * @param integer $decimal_places Number of decimal places
     * @return float Probability as percentage
     */
    public function probability_percentage($favorable_outcomes, $total_outcomes, $decimal_places = 2)
    {
        if ($total_outcomes == 0) {
            return 0;
        }
        
        $probability = ($favorable_outcomes / $total_outcomes) * 100;
        return round($probability, $decimal_places);
    }

    /**
     * Calculate odds ratio
     * 
     * @param integer $favorable_outcomes Number of favorable outcomes
     * @param integer $total_outcomes Total possible outcomes
     * @return string Odds ratio as string (e.g., "1 in 292,201,338")
     */
    public function odds_ratio($favorable_outcomes, $total_outcomes)
    {
        if ($favorable_outcomes == 0) {
            return "0";
        }
        
        $odds = round($total_outcomes / $favorable_outcomes);
        return "1 in " . number_format($odds);
    }

    /**
     * Generate all possible combinations of r elements from array of n elements
     * 
     * @param array $elements Array of elements to combine
     * @param integer $r Number of elements in each combination
     * @return array Array of all combinations
     */
    public function generate_combinations($elements, $r)
    {
        $n = count($elements);
        if ($r > $n || $r <= 0) {
            return [];
        }
        
        if ($r == 1) {
            $combinations = [];
            foreach ($elements as $element) {
                $combinations[] = [$element];
            }
            return $combinations;
        }
        
        if ($r == $n) {
            return [$elements];
        }
        
        $combinations = [];
        $first_element = array_shift($elements);
        
        // Include first element
        $sub_combinations = $this->generate_combinations($elements, $r - 1);
        foreach ($sub_combinations as $combination) {
            $combinations[] = array_merge([$first_element], $combination);
        }
        
        // Exclude first element
        $sub_combinations = $this->generate_combinations($elements, $r);
        $combinations = array_merge($combinations, $sub_combinations);
        
        return $combinations;
    }

    /**
     * Calculate the sum of an array
     * 
     * @param array $numbers Array of numbers
     * @return integer Sum of all numbers
     */
    public function array_sum_safe($numbers)
    {
        if (!is_array($numbers)) {
            return 0;
        }
        
        $sum = 0;
        foreach ($numbers as $number) {
            if (is_numeric($number)) {
                $sum += $number;
            }
        }
        
        return $sum;
    }

    /**
     * Calculate statistical mean (average)
     * 
     * @param array $numbers Array of numbers
     * @return float Mean value
     */
    public function mean($numbers)
    {
        if (empty($numbers)) {
            return 0;
        }
        
        return $this->array_sum_safe($numbers) / count($numbers);
    }

    /**
     * Calculate statistical median
     * 
     * @param array $numbers Array of numbers
     * @return float Median value
     */
    public function median($numbers)
    {
        if (empty($numbers)) {
            return 0;
        }
        
        sort($numbers);
        $count = count($numbers);
        $middle = floor($count / 2);
        
        if ($count % 2 == 0) {
            return ($numbers[$middle - 1] + $numbers[$middle]) / 2;
        } else {
            return $numbers[$middle];
        }
    }

    /**
     * Calculate statistical mode (most frequent value)
     * 
     * @param array $numbers Array of numbers
     * @return array Array of mode values
     */
    public function mode($numbers)
    {
        if (empty($numbers)) {
            return [];
        }
        
        $frequency = array_count_values($numbers);
        $max_frequency = max($frequency);
        
        $modes = [];
        foreach ($frequency as $value => $freq) {
            if ($freq == $max_frequency) {
                $modes[] = $value;
            }
        }
        
        return $modes;
    }

    /**
     * Calculate standard deviation
     * 
     * @param array $numbers Array of numbers
     * @return float Standard deviation
     */
    public function standard_deviation($numbers)
    {
        if (count($numbers) < 2) {
            return 0;
        }
        
        $mean = $this->mean($numbers);
        $sum_squares = 0;
        
        foreach ($numbers as $number) {
            $sum_squares += pow($number - $mean, 2);
        }
        
        $variance = $sum_squares / (count($numbers) - 1);
        return sqrt($variance);
    }

    /**
     * Calculate range (difference between max and min)
     * 
     * @param array $numbers Array of numbers
     * @return integer Range value
     */
    public function range($numbers)
    {
        if (empty($numbers)) {
            return 0;
        }
        
        return max($numbers) - min($numbers);
    }

    /**
     * Check if a number is prime
     * 
     * @param integer $number Number to check
     * @return boolean True if prime, false otherwise
     */
    public function is_prime($number)
    {
        if ($number < 2) {
            return false;
        }
        
        if ($number == 2) {
            return true;
        }
        
        if ($number % 2 == 0) {
            return false;
        }
        
        for ($i = 3; $i <= sqrt($number); $i += 2) {
            if ($number % $i == 0) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Generate Fibonacci sequence up to n terms
     * 
     * @param integer $n Number of terms
     * @return array Fibonacci sequence
     */
    public function fibonacci($n)
    {
        if ($n <= 0) {
            return [];
        }
        
        if ($n == 1) {
            return [0];
        }
        
        if ($n == 2) {
            return [0, 1];
        }
        
        $sequence = [0, 1];
        for ($i = 2; $i < $n; $i++) {
            $sequence[] = $sequence[$i - 1] + $sequence[$i - 2];
        }
        
        return $sequence;
    }

    /**
     * Calculate greatest common divisor (GCD)
     * 
     * @param integer $a First number
     * @param integer $b Second number
     * @return integer GCD
     */
    public function gcd($a, $b)
    {
        while ($b != 0) {
            $temp = $b;
            $b = $a % $b;
            $a = $temp;
        }
        
        return abs($a);
    }

    /**
     * Calculate least common multiple (LCM)
     * 
     * @param integer $a First number
     * @param integer $b Second number
     * @return integer LCM
     */
    public function lcm($a, $b)
    {
        return abs($a * $b) / $this->gcd($a, $b);
    }
}
