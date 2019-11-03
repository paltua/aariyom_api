<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function parrot()
	{
		$responseData = [
			'status' => 'success',
			'message' => 'Fetched successfully.',
			'data' => array(
				base_url('images/parrot/') . 'a1.jpeg',
				base_url('images/parrot/') . 'a2.jpeg',
				base_url('images/parrot/') . 'a3.jpeg',
			)
		];
		echo json_encode($responseData);
	}
}
