<?php 
namespace App\Controllers; 
use App\Controllers\Core\CommonController; 

 
class Menu extends CommonController
{	
	
    function __construct()
    {
        parent::__construct();


	}

    public function index()
    {        

		$data['columns'] = $this->db->getFieldNames($this->table);
		$data['fields'] = array_diff($data['columns'], $this->notAllowedFields);
        $data[$this->table] = getWithOutUuidResultArray("businesses");
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['is_add_permission'] = 1;
        $data['identifierKey'] = 'id';

		$viewPath = "common/list";
		if (file_exists( APPPATH . 'Views/' . $this->table."/list.php")) {
			$viewPath = $this->table."/list";
		}

        return view($viewPath, $data);
    }
    public function edit($id = 0)
    { 
		$data['tableName'] = $this->table;
        $data['rawTblName'] = $this->table;
		$data["users"] = $this->model->getUser();
		$data["data"] = getRowArray($this->table, ['id' => $id]);
		// if there any special cause we can overried this function and pass data to add or edit view
		$data['additional_data'] = $this->getAdditionalData($id);

        echo view($this->table."/edit",$data);
    }
    
   
}