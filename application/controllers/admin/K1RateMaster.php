<?php

defined('BASEPATH') or exit('No direct script access allowed');

class K1RateMaster extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('K1RateMaster_Model');
        $this->load->model('PurchaseModel');
    }

    public function index()
    {
        if (!has_permission_new('K1RateMaster', '', 'view')) {
            access_denied('K1 Rate Master');
        }

        $data['title'] = 'K1 Rate Master';
        $this->load->view('admin/K1RateMaster/AddEditK1RateMaster', $data);
    }

    public function getRateMasterData()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $fromDate = to_sql_date($this->input->post('FromDate'));
        $toDate   = to_sql_date($this->input->post('ToDate'));

        $data = $this->K1RateMaster_Model->get_RateMaster_data($fromDate ?: null, $toDate ?: null);

        $this->output->set_content_type('application/json');
        echo json_encode($data);
    }

    public function getRateMasterDetail()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id  = $this->input->post('RateCode');
        $row = $this->K1RateMaster_Model->getRateMasterDetails($id);

        $this->output->set_content_type('application/json');

        if ($row) {
            echo json_encode([
                'RateCode'      => $row->id,
                'ItemId'        => $row->ItemID,
                'ItemName'      => $row->ItemName,
                'CenterID'      => $row->CenterID,
                'CenterName'    => $row->CenterName ?? '',
                'HSNCode'       => $row->HSNCode ?? '',
                'UOM'           => $row->UnitShortCode ?? '',
                'UnitWtKg'      => $row->UnitWtKg ?? '',
                'EffectiveDate' => isset($row->EffectiveDate) ? $row->EffectiveDate : '',
                'Rate'          => isset($row->Rate) ? number_format((float) $row->Rate, 2, '.', '') : '',
                'Discount'      => isset($row->disc_amt) ? number_format((float) $row->disc_amt, 2, '.', '') : '',
            ]);
        } else {
            echo json_encode(null);
        }
    }

    public function getItems()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $items = $this->db->select('ProductID as ItemID, ProductName as ItemName, hsn_code, unit, PackingWeight')
            ->from(db_prefix() . 'product')
            ->order_by('ProductName', 'ASC')
            ->get()
            ->result_array();

        $this->output->set_content_type('application/json');
        echo json_encode($items);
    }

    public function getCenters()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $centers = $this->PurchaseModel->GetAllAssignedCenterList();

        $this->output->set_content_type('application/json');
        echo json_encode($centers);
    }

    public function getItemDetails()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $itemId = $this->input->post('ItemID');
        if (!$itemId) {
            $this->output->set_content_type('application/json');
            echo json_encode(['item' => null]);
            return;
        }

        $this->load->model('ItemModel');
        $item = $this->ItemModel->GetProductDetailsbyProductID($itemId);

        $this->output->set_content_type('application/json');
        echo json_encode(['item' => $item ?: null]);
    }

    public function SaveRateMaster()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!has_permission_new('K1RateMaster', '', 'create')) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $itemId    = $this->input->post('ItemName');
        $centerIds = $this->input->post('CenterName');

        if (empty($centerIds) && $this->input->post('CenterName[]')) {
            $centerIds = $this->input->post('CenterName[]');
        }

        if (!$itemId) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Item Name is required']);
            return;
        }

        if (empty($centerIds)) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Center Name is required']);
            return;
        }

        if (!is_array($centerIds)) {
            $centerIds = [$centerIds];
        }

        if (in_array('ALL', $centerIds, true)) {
            $centers   = $this->PurchaseModel->GetAllAssignedCenterList();
            $centerIds = array_column($centers, 'CenterID');
        } else {
            $centerIds = array_values(array_filter($centerIds, function ($id) {
                return $id !== '' && $id !== 'ALL';
            }));
        }

        if (empty($centerIds)) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'No valid center selected']);
            return;
        }

        $dateCheck = $this->validateEffectiveDate($this->input->post('EffectiveDate'));
        if (!$dateCheck['valid']) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => $dateCheck['message']]);
            return;
        }

        $transDate = $dateCheck['transDate'];
        $userId    = $this->session->userdata('username');
        $inserted  = 0;

        foreach ($centerIds as $centerId) {
            $data = $this->K1RateMaster_Model->buildRateData(
                $itemId,
                $centerId,
                $this->input->post('Rate'),
                $this->input->post('Discount'),
                $transDate,
                $userId
            );

            if ($this->K1RateMaster_Model->SaveRateMaster($data)) {
                $inserted++;
            }
        }

        $this->output->set_content_type('application/json');
        echo json_encode([
            'success' => $inserted > 0,
            'message' => $inserted > 0
                ? 'Rate Master saved successfully for ' . $inserted . ' center(s).'
                : 'Error saving Rate Master',
        ]);
    }

    public function UpdateRateMaster()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!has_permission_new('K1RateMaster', '', 'edit')) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $dateCheck = $this->validateEffectiveDate($this->input->post('EffectiveDate'));
        if (!$dateCheck['valid']) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => $dateCheck['message']]);
            return;
        }

        $id     = $this->input->post('RateCode');
        $result = $this->K1RateMaster_Model->UpdateRateMaster($id);

        $this->output->set_content_type('application/json');
        echo json_encode($result);
    }

    public function GetItemsData()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $data = $this->db->select('ProductID as ItemID, ProductName as ItemName')
            ->from(db_prefix() . 'product')
            ->order_by('ProductName', 'ASC')
            ->get()
            ->result_array();

        $this->output->set_content_type('application/json');
        echo json_encode($data);
    }

    public function ImportRateMasterCSV()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!has_permission_new('K1RateMaster', '', 'create')) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $centerIds     = $this->input->post('CenterName');
        $effectiveDate = $this->input->post('EffectiveDate');

        if (empty($centerIds) || !$effectiveDate) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Center Name and Effective Date are required']);
            return;
        }

        if (!is_array($centerIds)) {
            $centerIds = [$centerIds];
        }

        if (in_array('ALL', $centerIds, true)) {
            $centers   = $this->PurchaseModel->GetAllAssignedCenterList();
            $centerIds = array_column($centers, 'CenterID');
        } else {
            $centerIds = array_values(array_filter($centerIds, function ($id) {
                return $id !== '' && $id !== 'ALL';
            }));
        }

        if (empty($centerIds)) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'No valid center selected']);
            return;
        }

        $dateCheck = $this->validateEffectiveDate($effectiveDate);
        if (!$dateCheck['valid']) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => $dateCheck['message']]);
            return;
        }

        if (empty($_FILES['RateCSVFile']['tmp_name'])) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'CSV file is required']);
            return;
        }

        $transDate = $dateCheck['transDate'];
        $userId    = $this->session->userdata('username');

        $handle = fopen($_FILES['RateCSVFile']['tmp_name'], 'r');
        if (!$handle) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Unable to open uploaded CSV file']);
            return;
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid CSV file']);
            return;
        }

        $headerMap       = array_flip(array_map('trim', $headers));
        $requiredHeaders = ['ItemID', 'AssignedRate', 'Discount'];
        foreach ($requiredHeaders as $requiredHeader) {
            if (!isset($headerMap[$requiredHeader])) {
                fclose($handle);
                $this->output->set_content_type('application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'CSV must contain ItemID, AssignedRate, and Discount columns',
                ]);
                return;
            }
        }

        $inserted   = 0;
        $skipped    = 0;
        $errors     = [];
        $lineNumber = 2;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < max($headerMap) + 1) {
                $lineNumber++;
                continue;
            }

            $itemId       = trim($row[$headerMap['ItemID']] ?? '');
            $assignedRate = trim($row[$headerMap['AssignedRate']] ?? '');
            $discount     = trim($row[$headerMap['Discount']] ?? '');

            if (!$itemId || $assignedRate === '') {
                $skipped++;
                $lineNumber++;
                continue;
            }

            $itemExists = $this->db->from(db_prefix() . 'product')
                ->where('ProductID', $itemId)
                ->count_all_results() > 0;

            if (!$itemExists) {
                $errors[] = "ItemID {$itemId} not found in product table on row {$lineNumber}";
                $skipped++;
                $lineNumber++;
                continue;
            }

            foreach ($centerIds as $centerId) {
                $data = $this->K1RateMaster_Model->buildRateData(
                    $itemId,
                    $centerId,
                    $assignedRate,
                    $discount !== '' ? $discount : null,
                    $transDate,
                    $userId
                );

                if ($this->K1RateMaster_Model->SaveRateMaster($data)) {
                    $inserted++;
                } else {
                    $errors[] = "Failed to save row {$lineNumber} for ItemID {$itemId} and center {$centerId}";
                    $skipped++;
                }
            }

            $lineNumber++;
        }

        fclose($handle);

        $successMessage = "Imported {$inserted} row(s) successfully.";
        $errorMessage   = '';

        if ($skipped || !empty($errors)) {
            $errorMessage = "Skipped {$skipped} row(s). " . implode(' | ', $errors);
        }

        $this->output->set_content_type('application/json');
        echo json_encode([
            'success'         => $inserted > 0,
            'message'         => $successMessage . ($errorMessage ? ' ' . $errorMessage : ''),
            'success_message' => $inserted > 0 ? $successMessage : '',
            'error_message'   => $errorMessage,
            'inserted'        => $inserted,
            'skipped'         => $skipped,
        ]);
    }

    private function validateEffectiveDate($displayDate)
    {
        $sqlDate   = to_sql_date($displayDate);
        $startYear = 2000 + (int) $this->session->userdata('finacial_year');
        $fyStart   = $startYear . '-04-01';
        $fyEnd     = ($startYear + 1) . '-03-31';

        if (!$sqlDate || $sqlDate < $fyStart || $sqlDate > $fyEnd) {
            return [
                'valid'   => false,
                'message' => 'Effective date must be within current financial year (' . sprintf('01/04/%d', $startYear) . ' to ' . sprintf('31/03/%d', $startYear + 1) . ').',
            ];
        }

        return [
            'valid'     => true,
            'sqlDate'   => $sqlDate,
            'transDate' => $sqlDate . ' ' . date('H:i:s'),
        ];
    }
}
