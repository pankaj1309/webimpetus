<?php namespace App\Models\Core;
use CodeIgniter\Model;
 
class Common_model extends Model
{
    protected $table = '';
    private $whereCond = array();
    private $doesUuidBusinessIdFieldExists = false;

    function __construct()
    {
        parent::__construct();
        $this->session = session();
        $this->table = $this->getTableNameFromUri();
        if ($this->db->fieldExists('uuid_business_id', $this->table)) {

            $this->whereCond['uuid_business_id'] = session('uuid_business');
            $this->doesUuidBusinessIdFieldExists = true;
        }
    }


    public function getTableNameFromUri ()
    {
        $uri = service('uri');
        $tableNameFromUri = $uri->getSegment(1);
        return $tableNameFromUri;
    }
    

    public function getRows($id = false)
    {
        $whereCond = $this->whereCond;

        if ($id === false) {

            if (empty($whereCond)) {

                return $this->findAll();
            } else {

                return $this->getWhere($whereCond)->getResultArray();
            }
        } else {

            $whereCond = array_merge(array('id' => $id), $whereCond);
            return $this->getWhere($whereCond);
        }   
    }
	
	public function getCats($id = false)
    {
		return $this->findAll();
	}
	

	public function deleteData($id)
    {
        $query = $this->db->table($this->table)->delete(array('id' => $id));
        return $query;
    }
	
	public function insertOrUpdate($id = null, $data = null)
	{
        unset($data["id"]);
        if ($this->doesUuidBusinessIdFieldExists) {

            $data['uuid_business_id'] = session('uuid_business');
        }
        if(@$id){
            $query = $this->db->table($this->table)->update($data, array('id' => $id));
            if( $query){
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
                return $id;
            }
        }else{
            $query = $this->db->table($this->table)->insert($data);
            if($query){
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
                return $this->db->insertID();
            }

        }
	
		return false;
	}

    public function getAllDataFromTable($tableName)
    {
		$query = $this->db->table($tableName)->get()->getResultArray();
        return $query;
	}

    public function getUser($id = false)
    {
        $whereCond = $this->whereCond;
        $builder = $this->db->table("users");
        if($id === false){
            $whereCond = array_merge(['role!='=>1], $whereCond);
            return $builder->where($whereCond)->get()->getResultArray();
        }else{
            $whereCond = array_merge(['id' => $id], $whereCond);
            return $builder->getWhere($whereCond)->getRowArray();
        }   
    }

    public function updateColumn($tableName , $id = null, $data = null){
        $query = $this->db->table($tableName, $this->table)->update($data, array('id' => $id));
        return $query;
    }

    public function updateData($id = null, $data = null)
	{
		$query = $this->db->table($this->table)->update($data, array('id' => $id));
		return $query;
	}
    public function updateTableData($id = null, $data = null, $tableName)
	{
		$query = $this->db->table($tableName)->update($data, array('id' => $id));
		return $query;
	}
    public function insertTableData( $data = null, $tableName)
	{
		$query = $this->db->table($tableName)->insert($data);
        return $this->db->insertID();
	}
    public function saveDataInTable($data, $tableName)
	{
		$query = $this->db->table($tableName)->insert($data);
		return $query;
	}
    public function getDataWhere($tableName, $value, $field = "id")
    {
		$result = $this->db->table($tableName)->getWhere([
            $field => $value
        ])->getResultArray();

        return $result;
	}

    public function deleteTableData($tableName, $id)
    {
        $query = $this->db->table($tableName)->delete(array('id' => $id));
        return $query;
    }

    public function getMenuCode($value)
    {
		$result = $this->db->table("menu")->getWhere([
            "link" => $value
        ])->getRowArray();

        return @$result['id'];
	}


}