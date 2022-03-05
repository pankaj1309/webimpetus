<?php 
namespace App\Controllers; 
use App\Controllers\Core\CommonController; 
use App\Models\Documents_model;
 
class Documents extends CommonController
{	
	
    function __construct()
    {
        parent::__construct();

        $this->documents_model = new Documents_model();

	}
    
    public function index()
    {        

        $data[$this->table] = $this->documents_model->getList();
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['is_add_permission'] = 1;

        echo view($this->table."/list",$data);
    }
    public function edit($id = 0)
    {
		$data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
		$data["users"] = $this->model->getUser();
		$data[$this->rawTblName] = $this->model->getRows($id)->getRow();
		// if there any special cause we can overried this function and pass data to add or edit view
		$data['additional_data'] = $this->getAdditionalData($id);

        echo view($this->table."/edit",$data);
    }
    public function update()
    {        
        $id = $this->request->getPost('id');

		$data = $this->request->getPost();

        $data['document_date'] = strtotime($data['document_date']);

        if( isset($_FILES['file']['tmp_name']) && strlen($_FILES['file']['tmp_name']) > 0) {	

            $response = $this->Amazon_s3_model->doUpload("file", "category-file");						
            $data['file'] = $response["filePath"];
        }
        
		$response = $this->model->insertOrUpdate($id, $data);
		if(!$response){
			session()->setFlashdata('message', 'Something wrong!');
			session()->setFlashdata('alert-class', 'alert-danger');	
		}

        return redirect()->to('/'.$this->table);
    }
}