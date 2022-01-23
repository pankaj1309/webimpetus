<?php namespace App\Models;
use CodeIgniter\Model;
 
class Secret_model extends Model
{
    protected $table = 'secrets';
	protected $table2 = 'secrets_services';
     
    public function getRows($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->getWhere(['id' => $id]);
        }   
    }
	
	public function saveData($data)
    {
        $query = $this->db->table($this->table)->insert($data);
        return $query;
    }

    public function saveSecretRelatedData($data)
    {
        $query = $this->db->table($this->table2)->insert($data);
        return $query;
    }

	// public function saveDefaultData($data)
    // {
    //     $query = $this->db->table($this->table2)->insert($data);
    //     return $query;
    // }
	
	public function deleteData($id)
    {
        $query = $this->db->table($this->table)->delete(array('id' => $id));
        return $query;
    }
	
	public function updateData($id = null, $data = null)
	{
		$query = $this->db->table($this->table)->update($data, array('id' => $id));
		return $query;
	}
		
	public function getSecret($code=""){
		return !empty($this->select('key_value')->getWhere(['key_name' => $code])->getRow())?$this->select('key_value')->getWhere(['key_name' => $code])->getRow()->text:'';
	}

    public function getSecretByName($secret_name){
        $this->db->select('key_value');
        $this->db->from($table);
        $this->db->where('key_name',$secret_name);
        $row = $this->db->get()->row();
        if (isset($row)) {
            return $row->key_value;
        } else {
            return false;
        }
    }
	
	public function getLastInserted() {
		return $this->db->insertID();
	}
	
	public function serviceData($data)
    {
        $query = $this->db->table($this->table2)->insert($data);
        return $query;
    }
	
	public function getServicesFromSecret($id)
    {        
        return $this->db->table($this->table)->where(['service_id' => $id])->get()->getResult('array');
    }
		
	public function getServices($id)
    {        
        return $this->db->table($this->table2)->where(['secret_id' => $id])->get()->getResult('array');
    }
	
	public function getSecrets($id)
    { 
		$this->join('secrets_services', 'secrets.id=secrets_services.secret_id', 'LEFT');			
		$this->select('secrets.*');		
			
		return $this->where(['service_id' => $id])->get()->getResult('array');
    }
	
	public function deleteService($id)
    {
        $query = $this->db->table($this->table2)->delete(array('secret_id' => $id));
        return $query;
    }
	
	public function deleteServiceFromServiceID($id)
    {
        $query = $this->db->table($this->table2)->delete(array('service_id' => $id));
        return $query;
    }
	
	public function getSecretsForDeployService($id)
    {
		$this->join('secrets_services', 'secrets_services.service_id=secrets.service_id', 'LEFT');
		$this->join('secrets_default', 'secrets_default.id=secrets_services.secrets_default_id', 'LEFT');
		$this->groupBy('secrets_default.id');
		$this->orderBy('secrets_default.id');
		$this->select('secrets_services.*');
		$this->select('secrets_default.*');
		
		return $this->where(['secrets_services.service_id' => $id])->get()->getResult('array');
    }
}