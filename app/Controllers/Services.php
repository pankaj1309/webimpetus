<?php namespace App\Controllers;
 
use CodeIgniter\Controller;
use App\Models\Service_model;
use App\Models\Users_model;
use App\Models\Tenant_model;
use App\Models\Cat_model;
use App\Models\Secret_model;
use App\Models\Template_model;
use App\Models\Meta_model;
use App\Models\Amazon_s3_model;
use App\Models\Core\Common_model;

class Services extends Api
{	
	public function __construct()
	{
		parent::__construct(); 
		$this->session = \Config\Services::session();
		$this->serviceModel = new Service_model();
		$this->user_model = new Users_model();
		$this->tmodel = new Tenant_model();
		$this->cmodel = new Cat_model();
		$this->secret_model = new Secret_model();
		$this->template_model = new Template_model();
		$this->meta_model = new Meta_model();
		$this->Amazon_s3_model = new Amazon_s3_model();
		$this->db = \Config\Database::connect();
		helper(["global"]);

		$this->common_model = new Common_model();
	  	$this->common_model->getMenuCode("/services");
		$this->businessUuid = session('uuid_business');
		$this->whereCond['uuid_business_id'] = $this->businessUuid;
		$menucode = $this->getMenuCode("/services");
		$this->session->set("menucode", $menucode);

	}

    public function index()
    {        
        $data['services'] = $this->serviceModel->getRows();
		$data['tableName'] = "services";
        $data['rawTblName'] = "service";
		$data['is_add_permission'] = 1;
		echo view('services/list',$data);
    }
	 
	public function edit($id=0)
    {        
		$data['tableName'] = "services";
        $data['rawTblName'] = "service";
        $data['service'] = $this->serviceModel->getRows($id)->getRow();
		$data['tenants'] = $this->tmodel->getRows();
		$data['category'] = $this->cmodel->getRows();
		$data['users'] = $this->user_model->getUser();
		$data['secret_services'] = $this->secret_model->getSecrets($id);
      
		
        echo view('services/edit', $data);
    }
	

    public function update()
    {
        $id = $this->request->getPost('id');

	
        $data = array(
			'name'  => $this->request->getPost('name'),
			'code' => $this->request->getPost('code'),				
			'notes' => $this->request->getPost('notes'),	
			'uuid' => $this->request->getPost('uuid'),
			//'nginx_config' => $this->request->getPost('nginx_config'),
			//'varnish_config' => $this->request->getPost('varnish_config'),
			'cid' => $this->request->getPost('cid'),
			'tid' => $this->request->getPost('tid'),
			
			'uuid_business_id' => $this->businessUuid,
		);
		
		$image_logo = $this->request->getPost('image_logo');
		$brand_logo = $this->request->getPost('brand_logo');
		if(strlen($image_logo) > 0){

			$data['image_logo'] = $this->request->getPost('image_logo');
		}
		if(strlen($brand_logo) > 0){

			$data['image_brand'] = $this->request->getPost('brand_logo');
		}

		 
        $id = $this->serviceModel->insertOrUpdate("services", $id,$data);
		
		$this->secret_model->deleteServiceFromServiceID($id);
		
		$key_name = $this->request->getPost('key_name');
		$key_value = $this->request->getPost('key_value');
		
		foreach ($key_name as $key => $value) {
			//$address_data['service_id'] = $id;
			$address_data['key_name'] = $key_name[$key];
			$address_data['key_value'] = $key_value[$key];
			$address_data['status'] = 1;
			$address_data['uuid_business_id'] = $this->businessUuid;

		
			$secret_id = $this->secret_model->saveOrUpdateData($id , $address_data);

			if($secret_id > 0){
				$dataRelated['secret_id'] = $secret_id;
				$dataRelated['service_id'] = $id;
				$dataRelated['uuid_business_id'] = $this->businessUuid;
				$this->secret_model->saveSecretRelatedData($dataRelated);
			}
	
		}

		
        return redirect()->to('/services');
    }
	

	
	
	public function deploy_service($uuid=0)
    {
		if(!empty($uuid)) {

			$this->export_service_json($uuid);
			$this->gen_service_env_file($uuid);
			$this->push_service_env_vars($uuid);
			$this->gen_service_yaml_file($uuid);
						
			//exec('/bin/sh /var/www/html/writable/tizohub_deploy_service.sh', $output, $return);
			$output = shell_exec('/bin/sh /var/www/html/writable/tizohub_deploy_service.sh');
			//echo $output;
			echo "Service deployment process started OK. Verify the deployment using kubectl get pods command";
			
		} else { echo "Uuid is empty!!"; }
		
    }

	public function delete_service($uuid=0)
    {
		if(!empty($uuid)) {

			$this->export_service_json($uuid);
			$this->gen_service_env_file($uuid);
			$this->push_service_env_vars($uuid);
			$this->gen_service_yaml_file($uuid);
						
			//exec('/bin/bash /var/www/html/writable/tizohub_deploy_service.sh', $output, $return);
			$output = shell_exec('/bin/sh /var/www/html/writable/tizohub_delete_service.sh');
			//echo $output;
			echo "Service deletion process started OK. Note: This process does not delete the tenant database.";
			
		} else { echo "Uuid is empty!!"; }
		
    }

	
	public function export_service_json($uuid) 
	{
		//export service json same format as provided by the api
		// url/api/service/uuid.json -> json
		// write json to to file	
		
		$myfile = fopen(WRITEPATH . "tizohub_deployments/service-".$uuid.".json", "w") or die("Unable to open file!");
		
		fwrite($myfile, $this->services($uuid,true));
		fclose($myfile);
	}

	
	public function push_service_env_vars($uuid) 
	{
		// Get the contents of the JSON file for service and add as env variables to pass to the deployment
		$svcJsonFileContents = file_get_contents(WRITEPATH . "tizohub_deployments/service-".$uuid.".json");
		// Convert to array
		$svcJsonFileObj = json_decode($svcJsonFileContents);
		putenv("SERVICE_ID=".$uuid);
		putenv("SERVICE_NAME=".$svcJsonFileObj->name);
		// loop through all global secrets required for kubernetes deployment 
		$secrets = $this->secret_model->getRows();
		if(!empty($secrets)){
				foreach($secrets as $key=>$val){
					if ($val['key_name'] == 'KUBECONFIG') {
						$myfile = fopen(WRITEPATH . "kube_config_auth", "w") or die("Unable to open file!");
						fwrite($myfile, $val['key_value']);
						fclose($myfile);
					}
					
					if ($val['key_name'] == 'TIZOHUB_DOCKER_IMAGE' || $val['key_name'] == 'TIZOHUB_DOCKER_IMAGE_TAG' || $val['key_name'] == 'KUBENETES_CLUSTER_NAME' || $val['key_name'] == 'AWS_ACCESS_KEY_ID' || $val['key_name'] == 'AWS_SECRET_ACCESS_KEY' || $val['key_name'] == 'AWS_DEFAULT_REGION') {
					putenv($val['key_name']."=".$val['key_value']);
					}
			}
		}

		// loop through all secrets of this service 
		$secrets = $this->secret_model->getSecrets($uuid);
		if(!empty($secrets)){
			foreach($secrets as $key=>$val){
				putenv($val['key_name']."=".$val['key_value']);
			}
		}
		
	}


	public function gen_service_env_file($uuid)
	{
	
		$service_data = file_get_contents(WRITEPATH. 'tizohub.values.template');
		$secrets = $this->secret_model->getSecrets($uuid);
		if(!empty($secrets)){
			foreach($secrets as $key=>$val){
				$pattern = "/{{".$val['key_name']."}}/i";
				$service_data = preg_replace($pattern, $val['key_value'], $service_data);
		
			}
		}
	
		// loop through all global secrets required for kubernetes deployment 
		$secrets = $this->secret_model->getRows();
		if(!empty($secrets)){
				foreach($secrets as $key=>$val){					
					if ($val['key_name'] == 'TIZOHUB_DOCKER_IMAGE' || $val['key_name'] == 'TIZOHUB_DOCKER_IMAGE_TAG' || $val['key_name'] == 'KUBENETES_CLUSTER_NAME' || $val['key_name'] == 'AWS_ACCESS_KEY_ID' || $val['key_name'] == 'AWS_SECRET_ACCESS_KEY' || $val['key_name'] == 'AWS_DEFAULT_REGION') {
						$pattern = "/{{".$val['key_name']."}}/i";
						$service_data = preg_replace($pattern, $val['key_value'], $service_data);
					}
			}
		}
		
		$myfile = fopen(WRITEPATH . "tizohub_deployments/values-".$uuid.".yaml", "w") or die("Unable to open file!");
		fwrite($myfile, $service_data);
		fclose($myfile);
	
		//create php seed
		// $myfile = fopen(WRITEPATH . "tizohub_deployments/service-".$uuid.".php", "w") or die("Unable to open file!");
		// fwrite($myfile, $service_data);
		// fclose($myfile);
	
	}
	

	public function gen_service_yaml_file($uuid)
	{
		$service_data = file_get_contents(WRITEPATH. 'tizohub.yaml.template');
	
			//then go through service secrets vars and may override any global var values
			$secrets = $this->secret_model->getSecrets($uuid);
			if(!empty($secrets)){
				foreach($secrets as $key=>$val){
					$pattern = "/{{".$val['key_name']."}}/i";
					$service_data = preg_replace($pattern, $val['key_value'], $service_data);
			
				}
			}
				// loop through all global secrets required for kubernetes deployment 
				$secrets = $this->secret_model->getRows();
				if(!empty($secrets)){
						foreach($secrets as $key=>$val){					
							if ($val['key_name'] == 'TIZOHUB_DOCKER_IMAGE' || $val['key_name'] == 'TIZOHUB_DOCKER_IMAGE_TAG' || $val['key_name'] == 'KUBENETES_CLUSTER_NAME' || $val['key_name'] == 'AWS_ACCESS_KEY_ID' || $val['key_name'] == 'AWS_SECRET_ACCESS_KEY' || $val['key_name'] == 'AWS_DEFAULT_REGION') {
								$pattern = "/{{".$val['key_name']."}}/i";
								$service_data = preg_replace($pattern, $val['key_value'], $service_data);			
							}
					}
				}
	
		$myfile = fopen(WRITEPATH."tizohub_deployments/service-".$uuid.".yaml", "w") or die("Unable to open file!");
		fwrite($myfile, $service_data);
		fclose($myfile);
	
	}
	

public function delete($id)
{       
	//echo $id; die;
	if(!empty($id)) {
		$response = $this->serviceModel->deleteData($id);		
		if($response){
			session()->setFlashdata('message', 'Data deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}else{
			session()->setFlashdata('message', 'Something wrong delete failed!');
			session()->setFlashdata('alert-class', 'alert-danger');		
		}

	}
	
	return redirect()->to('/services');
}


public function getMenuCode($value)
{
	$result = $this->db->table("menu")->getWhere([
		"link" => $value
	])->getRowArray();

	return @$result['id'];
}


public function uploadMediaFiles(){

	$response = $this->Amazon_s3_model->doUpload("file", "service-logo");													
			
	if ($response["status"]) {

		$id = 0;
		$file_path = $response['filePath'];
		$status = 1;
		$file_views = view("services/uploadedFileView", array("file_path" => $file_path, "id" => $id));
		$msg = "success";

	} else {
		$status = 0;
		$file_views = '';
		$msg = "error";
	}
	
	echo json_encode(array("status" => $status, "file_path" => $file_views, "msg" => $msg));
}

public function deleteRow(){

	$id = $this->request->getPost("id");

	$res = $this->common_model->deleteTableData("secrets", $id);
	echo $this->db->getlastQuery();
	echo json_encode($res);
}

public function uploadMediaFiles2(){

	$folder = $this->request->getPost("mainTable");

	$response = $this->Amazon_s3_model->doUpload("file", $folder);													
			
	if ($response["status"]) {

		$file_path = $response['filePath'];
		$status = 1;
		$file_views = '<input type="hidden" value="'.$file_path.'" name="brand_logo">
		<img class="img-rounded" src="'.$file_path.'" width="100px">
		<a href="" id="delete_image_logo2" class="btn btn-danger"><i class="fa fa-trash"></i></a>';
		$msg = "success";

	} else {
		$status = 0;
		$file_views = '';
		$msg = "error";
	}
	
	echo json_encode(array("status" => $status, "file_path" => $file_views, "msg" => $msg));
}


}