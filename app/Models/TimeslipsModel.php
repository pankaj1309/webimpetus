<?php

namespace App\Models;

use CodeIgniter\Model;

class TimeslipsModel extends Model
{
    protected $table                = 'timeslips';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'modified_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    private $businessUuid;
    private $whereCond;

    public function __construct()
    {
        parent::__construct();
        $this->businessUuid = session('uuid_business');
        $this->whereCond[$this->table . '.uuid_business_id'] = $this->businessUuid;
    }

    public function getRows($id = false)
    {
        $whereCond = $this->whereCond;
        $table = $this->table;
        $selectFields = array(
            $table . '.id',
            $table . '.uuid',
            'tasks.name as task_name',
            $table . '.week_no',
            'CONCAT_WS(" ", employees.saludation, employees.first_name, employees.surname) as employee_name',
            $table . '.slip_start_date',
            $table . '.slip_timer_started',
            $table . '.slip_end_date',
            $table . '.slip_timer_end',
            '(CASE '.$table . '.break_time WHEN 1 THEN "Yes" WHEN 0 THEN "No" ELSE NULL END) as break_time',
            $table . '.break_time_start',
            $table . '.break_time_end',
            $table . '.slip_hours',
            $table . '.slip_description',
            $table . '.slip_rate',
            $table . '.slip_timer_accumulated_seconds',
            $table . '.billing_status',
            $table . '.created_at',
            $table . '.modified_at',
        );
        $this->select($selectFields);
        $this->join('tasks', 'tasks.id = ' . $table . '.task_name');
        $this->join('employees', 'employees.id = ' . $table . '.employee_name');
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

    public function getSingleData($uuid=0)
    {
        $this->where('uuid', $uuid);
        $this->where('uuid_business_id', $this->businessUuid);
        return $this->first();
    }

    public function getTaskData()
    {
        $db = \Config\Database::connect();
        return $db->table("tasks")->getWhere(array('uuid_business_id' => $this->businessUuid))->getResultArray();
    }

    public function getEmployeesData()
    {
        $db = \Config\Database::connect();
        return $db->table("employees")->select('id,CONCAT_WS(" ", saludation, first_name, surname) as name')->getWhere(array('uuid_business_id' => $this->businessUuid))->getResultArray();
    }

    public function saveByUuid($uuid, $data)
    {
        $db = \Config\Database::connect();
        if (!empty($uuid)) {
            unset($data['uuid']);
            $db->table($this->table)->where('uuid', $uuid)->update($data);
        } else {
            $db->table($this->table)->insert($data);
        }
    }

    public function deleteData($uuid)
    {
        return $this->where('uuid', $uuid)->delete();
    }
}
