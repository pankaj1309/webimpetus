<?php 

namespace App\Models;
use CodeIgniter\Model;
 
class Purchase_orders_model extends Model
{
    protected $table = 'purchase_orders';
     
    public function __construct()
    {
        parent::__construct();
		
        $this->businessUuid = session('uuid_business');
    }
    public function getRows($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->getWhere(['id' => $id]);
        }   
    }
	
	public function getList()
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table.".*, customers.company_name");
        $builder->join('customers', 'customers.id = '.$this->table.'.client_id', 'left');
        $builder->where($this->table.".uuid_business_id",  $this->businessUuid);

        return $builder->get()->getResultArray();
    }
	
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
}