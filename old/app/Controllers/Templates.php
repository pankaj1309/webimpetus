<?php namespace App\Controllers;
use App\Controllers\BaseController;
 
use CodeIgniter\Controller;
use App\Models\Content_model;
use App\Models\Users_model;
 
class Templates extends Controller
{	
	public function __construct()
	{
		$this->session = \Config\Services::session();
		$this->model = new Content_model();
		$this->user_model = new Users_model();
	}
    public function index()
    {        
        $data['content'] = $this->model->where(['type' => 3])->findAll();
        echo view('templates',$data);
    }
	
	public function add()
    {
		$data['users'] = $this->user_model->getUser();
        echo view('add_temp',$data);
    }
 
    public function save()
    {		      
		if(!empty($this->request->getPost('title'))){
			
			//echo '<pre>';print_r($this->request); die;
			 if($file = $this->request->getFile('file')) {
				
				   // Set Session
				   session()->setFlashdata('message', 'Data entered Successfully!');
				   session()->setFlashdata('alert-class', 'alert-success');
				   
				   $data = array(
						'title'  => $this->request->getPost('title'),				
						'sub_title' => $this->request->getPost('sub_title'),
						'content' => $this->request->getPost('content'),
						'meta_keywords' => $this->request->getPost('meta_keywords'),
						'meta_title' => $this->request->getPost('meta_title'),
						'meta_description' => $this->request->getPost('meta_description'),
						'status' => $this->request->getPost('status'),
						'publish_date' => ($this->request->getPost('publish_date')?str_replace('T',' ',$this->request->getPost('publish_date')):date('Y-m-d H:i')),
						'type' => ($this->request->getPost('type')?$this->request->getPost('type'):1),
						//'image_logo' => $filepath
					);
					
					if($_FILES['file']['tmp_name']) {	
												
						$imgData = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
						$imageProperties = getimageSize($_FILES['file']['tmp_name']);
				
						$data['custom_assets'] = $imgData;
						
					 }
					$this->model->saveData($data);

				}else{
				   // Set Session
				   session()->setFlashdata('message', 'File not uploaded.');
				   session()->setFlashdata('alert-class', 'alert-danger');

				}
			 //}

		 //}
			
		}
        return redirect()->to('//templates');
    }
	
	public function edit($id)
    {
		$data['content'] = $this->model->getRows($id)->getRow();
		$data['users'] = $this->user_model->getUser();
        echo view('edit_temp',$data);
    }
	
	public function rmimg($id)
    {
		if(!empty($id)){
			$data['custom_assets'] = null;
			$this->model->updateData($id,$data);
			session()->setFlashdata('message', 'Image deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
			
		}
		return redirect()->to('//templates/edit/'.$id);
		
	}
	
    public function update()
    {        
        $id = $this->request->getPost('id');
		if(!empty($id)){
			$data = array(
						'title'  => $this->request->getPost('title'),				
						'sub_title' => $this->request->getPost('sub_title'),
						'content' => $this->request->getPost('content'),
						'meta_keywords' => $this->request->getPost('meta_keywords'),
						'meta_title' => $this->request->getPost('meta_title'),
						'meta_description' => $this->request->getPost('meta_description'),
						'status' => $this->request->getPost('status'),
						'publish_date' => ($this->request->getPost('publish_date')?str_replace('T',' ',$this->request->getPost('publish_date')):date('Y-m-d H:i')),
						'type' => ($this->request->getPost('type')?$this->request->getPost('type'):1),
						//'image_logo' => $filepath
					);
					
					if($_FILES['file']['tmp_name']) {	
												
						$imgData = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
						$imageProperties = getimageSize($_FILES['file']['tmp_name']);
				
						$data['custom_assets'] = $imgData;
						
					 }
			$this->model->updateData($id, $data);
			
			session()->setFlashdata('message', 'Data updated Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}else {
			session()->setFlashdata('message', 'Something wrong!');
			session()->setFlashdata('alert-class', 'alert-danger');				   
		}
        return redirect()->to('//templates');
    }
	
	public function status()
    {  
	if(!empty($id = $this->request->getPost('id'))){
		$data = array(            
			'status' => $this->request->getPost('status')
        );
        $this->model->updateUser($data, $id);
	}
	echo '1';
	}
	
	public function delete($id)
    {       
		//echo $id; die;
        if(!empty($id)) {
			$this->model->deleteData($id);		
			session()->setFlashdata('message', 'Data deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}
		
        return redirect()->to('/templates');
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