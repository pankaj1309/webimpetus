<?php

namespace App\Controllers;

use App\Models\Users_model;
use App\Models\Meta_model;
class Dashboard extends BaseController
{
	public function __construct()
	{
	  parent::__construct();
	  $this->model = new Users_model();
	  $this->meta_model = new Meta_model();
	}
	
    public function index()
    {
        $data['title'] = "Hello World from Codeigniter 4";
        return view('dashboard', $data);
    }
	
	
	
	public function curlcmd(){
		$url = 'http://localhost:8080/auth/login';
		$data = array("email" => "phpcoderorg@gmail.com","password" => "111");

		$ch = curl_init( $url );
		# Setup request to send json via POST.
		$payload = json_encode( $data );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		# Return response instead of printing.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		# Send request.
		$result = curl_exec($ch);
		curl_close($ch);
		# Print response.
		echo "<pre>$result</pre>";
	}
	
	public function chgpwd()
    {
        $data['title'] = "Hello World from Codeigniter 4";
        echo view('change_pwd', $data);
    }
	
	public function savepwd()
    {
		if(!empty($_SESSION['uuid']) && !empty($this->request->getPost('opassword')) && $this->request->getPost('npassword') == $this->request->getPost('cpassword') ){	
		//echo '<pre>';print_r($_SESSION); die;
			$count = $this->model->getWhere(['password' => md5($this->request->getPost('opassword')), 'id'=>$_SESSION['uuid']])->getNumRows();
			if(empty($count)){
				session()->setFlashdata('message', 'Old password does not match in our database!');
				session()->setFlashdata('alert-class', 'alert-danger');
				return redirect()->to('/dashboard/chgpwd');
			}else {
				$data = array(					
					'password' => md5($this->request->getPost('npassword'))					
				);
				$this->model->updateUser($data, $_SESSION['uuid']);
				session()->setFlashdata('message', 'Password changed Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
			}
		}
        return redirect()->to('/dashboard/chgpwd');
    }
	
	public function settings()
    {
        $data['data'] = $this->meta_model->getRows('site_logo')->getRow();
		//echo '<pre>';print_r($data['data']); die;
        echo view('settings', $data);
    }
	
	public function saveset()
    {
		if($_FILES['file']['tmp_name']) {	
			$data = [];
			$imgData = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
			//$imageProperties = getimageSize($_FILES['file']['tmp_name']);
			$data['meta_value'] = $imgData;
			$this->session->set('logo',$imgData);
			$this->meta_model->updateMeta('site_logo', $data);
			session()->setFlashdata('message', 'Logo changed Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
		 }
		 return redirect()->to('/dashboard/settings');
			
	}
	
}
