<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Predictions extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();
		 $this->load->model('lotteries_m');
		 $this->load->model('predictions_m');
		 $this->load->helper('file');
		 $this->load->library('image_lib');
	}

	/**
	 * Retrieves List of All Lotteries
	 * 
	 * @param       none
	 * @return      none
	 */
	public function index() { 
		// Fetch all lotteries from the database
		$this->data['lotteries'] = $this->lotteries_m->get();
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->data['predictions'] = $this;		// Access the methods in the view
		$this->data['subview'] = 'admin/dashboard/predictions/index';
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * Initialize the calculation form for the generator
	 * 
	 * @param       $id		Lottery id
	 * @return      none
	 */
	public function combinations($id)
	{
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$predict = $this->input->post('predict', TRUE);
		$this->data['lottery']->predict = (isset($predict) ? $predict : '');
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the Statistics menu
		$this->data['subview'] = 'admin/dashboard/predictions/combinations';
		$this->load->view('admin/_layout_main', $this->data);
	}
	/**
	 * Generate Full Wheeling Tables
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_generate($uri) 
	{
		return anchor($uri, '<i class="fa fa-circle-o-notch fa-2x" aria-hidden="true">', array('title' => 'Generate Full Wheeling Table Text Files'));
	}

	/**
	 * Saved Full Wheeling Table Files for Filtering
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_files($uri)
	{
		return anchor($uri, '<i class="fa fa-file-text-o fa-2x" aria-hidden="true">', array('title' => 'View Generated Full Wheeling Table Files', 'class' => 'followers'));
	}

	/**
	 * View Historic Wins from Generated Full Wheeling Tables with Filters
	 * 
	 * @param      string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_wins($uri)
	{
		return anchor($uri, '<i class="fa fa-money fa-2x" aria-hidden="true">', array('title' => 'View Historic Win History and Prizes from the wheeling table and filtering'));
	}

	/**
	 * Calculate the Current History or Update to the latest Draw
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_calculate($uri)
	{
		return anchor($uri, '<i class="fa fa-calculator fa-2x" aria-hidden="true">', array('title' => 'Calculate the Number of Combinations from Total Number of Predictions
		(e.g. 15 Balls) for a sample size (e.g. Pick - 6, Pick 7)', 'class' => 'calculate'));
	}

	/**
	 * Predictions for the next draw
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_predicts($uri)
	{
		return anchor($uri, '<i class="fa fa-eye fa-2x" aria-hidden="true">', 
		array('title' => 'The Best Predictions for the next draw', 'class' => 'predict'));
	}
}
	