<?php namespace App\Controllers;
 
use CodeIgniter\Controller;
use App\Models\Service_model;
use App\Models\Users_model;
use App\Models\Tenant_model;
use App\Models\Cat_model;
class Services extends Controller
{	
	public function __construct()
	{
		$this->session = \Config\Services::session();
	  $this->model = new Service_model();
	  $this->user_model = new Users_model();
	  $this->tmodel = new Tenant_model();
	  $this->cmodel = new Cat_model();
	}
    public function index()
    {        
        $data['services'] = $this->model->getRows();
        echo view('services',$data);
    }
	
	public function add()
    {
		$data['users'] = $this->user_model->getUser();
		$data['tenants'] = $this->tmodel->getRows();
		$data['category'] = $this->cmodel->getRows();
        echo view('add_service',$data);
    }
 
    public function save()
    {        
		//echo '<pre>';print_r($this->request); die;        
		if(!empty($this->request->getPost('code'))){		

				   // File path to display preview
					//$filepath = $this->upload('file');	
					//echo '<pre>'; print_r($this->request->getFile('file')); die;
					//$filepath2 = $this->upload('file2');		   
				   
				   $data = array(
						'name'  => $this->request->getPost('name'),
						'code' => $this->request->getPost('code'),				
						'notes' => $this->request->getPost('notes'),	
						'uuid' => $this->request->getPost('uuid'),
						'nginx_config' => $this->request->getPost('nginx_config'),
						'varnish_config' => $this->request->getPost('varnish_config'),
						/* 'image_logo' => $filepath,
						'image_brand' => $filepath2, */
						'cid' => $this->request->getPost('cid'),
						'tid' => $this->request->getPost('tid'),
					);
					
					if($_FILES['file']['tmp_name']) {		
						//echo '<pre>';print_r($_FILES['file']); die;											
						$imgData = base64_encode(file_get_contents($_FILES['file']['tmp_name']));				
						$data['image_logo'] = $imgData;
					 }
					 
					 if($_FILES['file2']['tmp_name']) {		
						//echo '<pre>';print_r($_FILES['file']); die;											
						$imgData2 = base64_encode(file_get_contents($_FILES['file2']['tmp_name']));				
						$data['image_brand'] = $imgData2;
					 }
			 
			 
					$this->model->saveData($data);	
					// Set Session
				   session()->setFlashdata('message', 'Data entered Successfully!');
				   session()->setFlashdata('alert-class', 'alert-success');
				   					

		 }	 
        return redirect()->to('/services');
    }
	
	public function edit($id)
    {        
        $data['service'] = $this->model->getRows($id)->getRow();
		$data['tenants'] = $this->tmodel->getRows();
		$data['category'] = $this->cmodel->getRows();
		$data['users'] = $this->user_model->getUser();
        echo view('edit_service', $data);
    }
	
	public function rmimg($type="", $id)
    {
		if(!empty($id)){
			$data[$type] = "";
			$this->model->updateData($id,$data);
			session()->setFlashdata('message', 'Image deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
			
		}
		return redirect()->to('/services/edit/'.$id);
		
	}
	
    public function update()
    {        
        $id = $this->request->getPost('id');
		if(!empty($id)){
        $data = array(
						'name'  => $this->request->getPost('name'),
						'code' => $this->request->getPost('code'),				
						'notes' => $this->request->getPost('notes'),	
						'uuid' => $this->request->getPost('uuid'),
						'nginx_config' => $this->request->getPost('nginx_config'),
						'varnish_config' => $this->request->getPost('varnish_config'),
						'cid' => $this->request->getPost('cid'),
						'tid' => $this->request->getPost('tid'),
						//'image_logo' => $filepath,
						//'image_brand' => $filepath2
					);
					
					if($_FILES['file']['tmp_name']) {		
						//echo '<pre>';print_r($_FILES['file']); die;											
						$imgData = base64_encode(file_get_contents($_FILES['file']['tmp_name']));				
						$data['image_logo'] = $imgData;
					 }
					 
					 if($_FILES['file2']['tmp_name']) {		
						//echo '<pre>';print_r($_FILES['file']); die;											
						$imgData2 = base64_encode(file_get_contents($_FILES['file2']['tmp_name']));				
						$data['image_brand'] = $imgData2;
					 }
        $this->model->updateData($id,$data);
		
		session()->setFlashdata('message', 'Data updated Successfully!');
		session()->setFlashdata('alert-class', 'alert-success');
		} else{
			session()->setFlashdata('message', 'Something wrong!');
			session()->setFlashdata('alert-class', 'alert-danger');
		}
		
        return redirect()->to('/services');
    }
	
	public function status()
    {  
	if(!empty($id = $this->request->getPost('id'))){
		$data = array(            
			'status' => $this->request->getPost('status')
        );
        $this->model->updateData($id,$data);
	}
	echo '1';
	}
	
	public function delete($id)
    {       
		//echo $id; die;
        if(!empty($id)){
			$this->model->deleteData($id);
			session()->setFlashdata('message', 'Data deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}
		
        return redirect()->to('/services');
    }
	
	public function upload($filename = null){
		$input = $this->validate([
			$filename => "uploaded[$filename]|max_size[$filename,1024]|ext_in[$filename,jpg,jpeg,docx,pdf],"
		 ]);

		 if (!$input) { // Not valid
			return '';
		 }else{ // Valid

			 if($file = $this->request->getFile($filename)) {
				if ($file->isValid() && ! $file->hasMoved()) {
				   // Get file name and extension
				   $name = $file->getName();
				   $ext = $file->getClientExtension();

				   // Get random file name
				   $newName = $file->getRandomName(); 

				   // Store file in public/uploads/ folder
				   $file->move('../public/uploads', $newName);

				   // File path to display preview
				   return $filepath = base_url()."/uploads/".$newName;
				   
				}
				
			 }
			 
		 }
		 
	}
}