<?php namespace App\Models;
use CodeIgniter\Model;
 
class Content_model extends Model
{
    protected $table = 'content_list';
     
    public function getRows($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->getWhere(['id' => $id]);
        }   
    }
	
	public function jobsbycat($cat = false, $limit=false, $offset=false)
    {
        if($cat !== false && $limit!== false){
			$this->join('content_category', 'content_category.contentid=content_list.id', 'LEFT');
            $this->join('categories', 'categories.ID = content_category.categoryid', 'LEFT');			
			$this->select('content_list.*');
            return $this->where(['categories.Code'=>$cat,'content_list.type'=>4,'content_list.status'=>1])->orderBy('content_list.id','desc')->findAll($limit,$offset);
        }else{            
            $this->join('content_category', 'content_category.contentid=content_list.id', 'LEFT');
            $this->join('categories', 'categories.ID = content_category.categoryid', 'LEFT');			
			$this->select('content_list.*');
            return $this->getWhere(['categories.Code'=>$cat,'content_list.type'=>4,'content_list.status'=>1])->getNumRows();
        }   
    }
	
	public function blogposts($limit=false, $offset=false, $con=false)
    { 
		if($limit!== false){	
			$this->join('enquiries', 'content_list.id=enquiries.contentid and enquiries.type=3', 'LEFT');			
			$this->select('content_list.*,COUNT(DISTINCT enquiries.id) AS cmt_count');
			if($con!==false){
				$this->where($con);
			}
            return $this->where(['content_list.type' => 2,'content_list.status'=>1])->orderBy('content_list.publish_date', 'desc')->groupBy('content_list.id')->findAll($limit,$offset);
		} else {
            //$this->join('enquiries', 'enquiries.contentid = content_list.id', 'LEFT');			
			//$this->select('content_list.id');
			if($con!==false){
				$this->where($con);
			}
            return $this->getWhere(['type' => 2,'status'=>1])->getNumRows();
        }   
    }
	
	public function saveData($data)
    {
        $query = $this->db->table($this->table)->insert($data);
        return $this->getLastInserted();
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
	
	/* public function getjoins(){
		$this->join('content_category', 'categories.ID = content_category.categoryid', 'LEFT');
		$this->join('content_list', 'content_category.contentid=content_list.id', 'LEFT');
		$this->select('distinct(categories.ID), categories.Name, categories.Code');
        return $this->where(['content_list.type'=>4,'content_list.status'=>1])->findAll();
	} */
	
	public function format_uri( $string, $separator = '-', $id = 0)
	{
		$accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
		$special_cases = array( '&' => 'and', "'" => '');
		$string = mb_strtolower( trim( $string ), 'UTF-8' );
		$string = str_replace( array_keys($special_cases), array_values( $special_cases), $string );
		$string = preg_replace( $accents_regex, '$1', htmlentities( $string, ENT_QUOTES, 'UTF-8' ) );
		$string = preg_replace("/[^a-z0-9]/u", "$separator", $string);
		$string = preg_replace("/[$separator]+/u", "$separator", $string);
		$i = 0;
		if(!empty($id)){
			$arr = ['code' => $string, 'id!='=>$id];
		}else {
			$arr = ['code' => $string];
		}
		while ($this->db->table($this->table)->getWhere($arr)->getNumRows())
		{  
			if (!preg_match ('/-{1}[0-9]+$/', $string ))
			$string .= '-' . ++$i;
			else
			$string = preg_replace ('/[0-9]+$/', ++$i, $string );
		}  
	
	
		return $string;
	}
	
	public function getLastInserted() {
		return $this->db->insertID();
	}

	public function saveDataInTable($data, $tableName)
	{
		$query = $this->db->table($tableName)->insert($data);
		return $query;
	}
}