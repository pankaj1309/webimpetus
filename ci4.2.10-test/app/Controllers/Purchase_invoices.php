<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Core\Common_model;
use App\Models\Purchase_invoice_model;
use stdClass;

ini_set("display errors", 1);
class Purchase_invoices extends CommonController
{
    private $purchase_invoice_model;

    function __construct()
    {
        parent::__construct();

        $this->purchase_invoice_model = new Purchase_invoice_model();

        $this->purchase_invoice_items = "purchase_invoice_items";
        $this->purchase_invoice_notes = "purchase_invoice_notes";
        $this->purchase_invoices = "purchase_invoices";
    }

    public function index()
    {

        $data[$this->table] = $this->purchase_invoice_model->getInvoice();
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['is_add_permission'] = 1;

        echo view($this->table . "/list", $data);
    }

    public function edit($id = 0)
    {
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data["users"] = $this->model->getUser();
        $data[$this->rawTblName] = $this->model->getRows($id)->getRow();


        /* // This just create entry on table when click on add button
        if (empty($id)) {

            $insert['date'] = time();
            $insert['status'] = 'Invoiced';

            $invoice_number = findMaxFieldValue($this->purchase_invoices, "invoice_number");

            if (empty($invoice_number)) {
                $invoice_number = 1001;
            } else {
                $invoice_number += 1;
            }

            $insert['invoice_number'] = $invoice_number;
            $insert['uuid_business_id'] = session('uuid_business');

            $id = $this->purchase_invoice_model->insertData($insert);

            if ($id) {
                return redirect()->to('/' . $this->table . '/edit/' . $id);
            }
        } */

        if (empty($id)) {
            if (empty($data[$this->rawTblName])) {
                $data[$this->rawTblName] = new stdClass();
            }
            $data[$this->rawTblName]->date = time();
            $data[$this->rawTblName]->status = 'Invoiced';
        }

        // if there any special cause we can overried this function and pass data to add or edit view
        $data['additional_data'] = $this->getAdditionalData($id);

        echo view($this->table . "/edit", $data);
    }

    public function update()
    {
        $id = $this->request->getPost('id');

        $data = $this->request->getPost();
        $itemIds = @$data['item_id'];
        unset($data['item_id']);

        $data['due_date'] = strtotime($data['due_date']);
        $data['date'] = strtotime($data['date']);
        $data['paid_date'] = strtotime($data['paid_date']);

        if (empty($id)) {
            $data['invoice_number'] = findMaxFieldValue($this->purchase_invoices, "invoice_number");

            if (empty($data['invoice_number'])) {
                $data['invoice_number'] = 1001;
            } else {
                $data['invoice_number'] += 1;
            }

            $data['custom_invoice_number'] = $data['custom_invoice_number'] . $data['invoice_number'];
        }
        
        $data['is_locked'] = isset($data['is_locked']) ? 1 : 0;
        $response = $this->model->insertOrUpdate($id, $data);
        if (!$response) {
            session()->setFlashdata('message', 'Something wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        } else {

            $id = $response;
            if ($itemIds) {
                foreach ($itemIds as $itemId) {

                    $this->db->table($this->purchase_invoice_items)->where('id', $itemId)->update(array(
                        'purchase_invoices_id' => $id,
                    ));
                }
            }
        }

        return redirect()->to('/' . $this->table);
    }

    public function removeInvoiceItem()
    {

        $id = $this->request->getPost('id');
        $mainTableId = $this->request->getPost('mainTableId');

        if ($id > 0) {

            $this->model->deleteTableData($this->purchase_invoice_items, $id);
            $response['status'] = true;
        }

        echo json_encode($response);
    }
    public function updateInvoice()
    {

        $mainTableId = $this->request->getPost('mainTableId');
        $data['balance_due'] = $this->request->getPost('totalAmountWithTax');
        $data['total'] = $this->request->getPost('totalAmountWithTax');
        $data['total_due_with_tax'] = $this->request->getPost('totalAmountWithTax');
        $data['total_hours'] = $this->request->getPost('totalHour');
        $data['total_due'] = $this->request->getPost('totalAmount');
        $data['total_tax'] = $this->request->getPost('total_tax');

        $res = $this->model->updateTableData($mainTableId, $data, $this->purchase_invoices);

        $response['status'] = true;
        $response['msg'] = "Data updated successfully";
        $response['status'] = true;
        $response['data'] = $res;

        echo json_encode($response);
    }
    public function saveNotes()
    {

        $id = $this->request->getPost('id');
        $data['notes'] = $this->request->getPost('notes');
        $data['purchase_invoices_id'] = $this->request->getPost('mainTableId');

        if ($id > 0) {

            $res = $this->model->updateTableData($id, $data, $this->purchase_invoice_notes);
        } else {

            $data['created_by'] = $_SESSION['uuid'];
            $data['uuid_business_id'] = session('uuid_business');
            $id = $this->model->insertTableData($data, $this->purchase_invoice_notes);
        }

        $response['name'] = getUserInfo()->name;
        $response['status'] = true;
        $response['msg'] = "Data updated successfully";
        $response['data'] = getRowArray($this->purchase_invoice_notes, ["id" => $id]);

        echo json_encode($response);
    }
    public function addInvoiceItem()
    {

        $id = $this->request->getPost('id');
        $mainTableId = $this->request->getPost('mainTableId');
        $data['description'] = $this->request->getPost('description');
        $data['rate'] = $this->request->getPost('rate');
        $data['hours'] = $this->request->getPost('hours');
        $data['amount'] = $data['rate'] * $data['hours'];
        $data['uuid_business_id'] = session('uuid_business');

        // echo $this->purchase_invoice_items;die;

        if ($id > 0) {

            $this->model->updateTableData($id, $data, $this->purchase_invoice_items);
            $response['status'] = true;
        } else {

            $data['uuid_business_id'] = session('uuid_business');
            $data['purchase_invoices_id'] = $mainTableId;
            $data['uuid'] = UUID::v5(UUID::v4(), 'purchase_invoice_items');
            $id = $this->model->insertTableData($data, $this->purchase_invoice_items);

            if ($id > 0) {
                $response['msg'] = "Data added successfully";
                $response['status'] = true;
            } else {
                $response['msg'] = "Data insertion failed";
                $response['status'] = false;
            }
        }

        $response['data'] = getRowArray($this->purchase_invoice_items, ["id" => $id]);

        echo json_encode($response);
    }

    public function deleteNote()
    {

        $id = $this->request->getPost('id');
        $res = $this->model->deleteTableData($this->purchase_invoice_notes, $id);

        $response['id'] = $id;
        if ($res) {

            $response['status'] = true;
            $response['msg'] = "Data deleted successfully";
        } else {
            $response['status'] = false;
            $response['msg'] = "Failed";
        }


        echo json_encode($response);
    }

    public function loadBillToData()
    {
        $clientId = $this->request->getPost('clientId');
        $commonModel = new Common_model();
        $response = $commonModel->loadBillToData($clientId);
        echo json_encode($response);
    }

    public function calculateDueDate()
    {
        $term = $this->request->getPost('term');
        $currentDate = $this->request->getPost('currentDate');
        $commonModel = new Common_model();
        $response = $commonModel->calculateDueDate($term, $currentDate);
        echo json_encode($response);
    }
}
