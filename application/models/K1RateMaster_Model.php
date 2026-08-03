<?php

defined('BASEPATH') or exit('No direct script access allowed');

class K1RateMaster_Model extends App_Model
{
    private $masterTable  = 'tblK1RateMaster';
    private $historyTable = 'tblK1RateMasterHistory';

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Returns current FY date range as ['start' => 'YYYY-MM-DD', 'end' => 'YYYY-MM-DD']
     */
    private function getFYRange()
    {
        $fy    = (int) $this->session->userdata('finacial_year');
        $start = 2000 + $fy;
        return [
            'start' => $start . '-04-01',
            'end'   => ($start + 1) . '-03-31',
        ];
    }

    /**
     * Copies a master row array into the history table.
     */
    private function archiveToHistory(array $row)
    {
        $this->db->insert($this->historyTable, [
            'PartyID'    => $row['PartyID'],
            'ItemID'     => $row['ItemID'],
            'CenterID'   => $row['CenterID'],
            'taxrate'    => $row['taxrate'],
            'sale_rate'  => $row['sale_rate'],
            'basic_rate' => $row['basic_rate'],
            'disc_amt'   => $row['disc_amt'],
            'UserID'     => $row['UserID'],
            'TransDate'  => $row['TransDate'],
        ]);
    }

    // ─── Read ─────────────────────────────────────────────────────────────────

    public function get_RateMaster_data($fromDate = null, $toDate = null)
    {
        $this->db->select([
            'rm.id', 'rm.ItemID as item_id', 'rm.CenterID as center_id',
            'rm.sale_rate as Rate', 'rm.disc_amt as dis_per',
            'rm.TransDate as EffectiveDate', 'rm.UserID',
            'rm.basic_rate', 'rm.taxrate',
            'p.ProductName as ItemName', 'p.ProductID as ItemID',
            'p.hsn_code as HSNCode', 'p.unit as UnitShortCode',
            'p.PackingWeight as UnitWtKg', 'cm.CenterName',
        ])
        ->from($this->masterTable . ' as rm')
        ->join(db_prefix() . 'product as p',      'p.ProductID = rm.ItemID',       'left')
        ->join(db_prefix() . 'CenterMaster as cm', 'cm.CenterID = rm.CenterID',    'left');

        if ($fromDate) {
            $this->db->where('DATE(rm.TransDate) >=', $fromDate)
                     ->where('DATE(rm.TransDate) <=', $toDate ?: date('Y-m-d'));
        } else {
            $fy      = $this->getFYRange();
            $listEnd = min(date('Y-m-d'), $fy['end']);
            $this->db->where('DATE(rm.TransDate) >=', $fy['start'])
                     ->where('DATE(rm.TransDate) <=', $listEnd);
        }

        return $this->db
            ->order_by('rm.TransDate', 'ASC')
            ->order_by('cm.CenterName', 'ASC')
            ->order_by('rm.id', 'ASC')
            ->get()->result_array();
    }

    public function getRateMasterDetails($id)
    {
        return $this->db
            ->select('rm.id, rm.ItemID, rm.CenterID,
                      rm.sale_rate AS Rate, rm.disc_amt, rm.TransDate AS EffectiveDate,
                      rm.taxrate, rm.basic_rate,
                      p.ProductName AS ItemName, p.hsn_code AS HSNCode,
                      p.PackingWeight AS UnitWtKg, p.unit AS UnitShortCode,
                      cm.CenterName')
            ->from($this->masterTable . ' AS rm')
            ->join(db_prefix() . 'product AS p',       'p.ProductID = rm.ItemID',    'left')
            ->join(db_prefix() . 'CenterMaster AS cm', 'cm.CenterID = rm.CenterID',  'left')
            ->where('rm.id', $id)
            ->get()->row();
    }

    // ─── Build ────────────────────────────────────────────────────────────────

    public function buildRateData($itemId, $centerId, $saleRate, $discAmt, $transDate, $userId)
    {
        $product  = $this->db
            ->select('tbltaxes.taxrate')
            ->from(db_prefix() . 'product')
            ->join(db_prefix() . 'taxes', db_prefix() . 'taxes.id = ' . db_prefix() . 'product.gst', 'left')
            ->where(db_prefix() . 'product.ProductID', $itemId)
            ->get()->row();

        $taxrate   = $product ? (float) $product->taxrate : 0;
        $saleRate  = (float) $saleRate;
        $basicRate = $taxrate > 0 ? ($saleRate * 100) / (100 + $taxrate) : $saleRate;

        return [
            'PartyID'    => 'KASPL',
            'ItemID'     => $itemId,
            'CenterID'   => $centerId,
            'taxrate'    => $taxrate,
            'sale_rate'  => $saleRate,
            'basic_rate' => round($basicRate, 2),
            'disc_amt'   => ($discAmt !== '' && $discAmt !== null) ? $discAmt : null,
            'UserID'     => $userId,
            'TransDate'  => $transDate ?: date('Y-m-d H:i:s'),
        ];
    }

    // ─── Write ────────────────────────────────────────────────────────────────

    /**
     * Insert or replace a rate. If a record for the same Item+Center already
     * exists it is archived to history before being replaced.
     */
    public function SaveRateMaster(array $data)
    {
        $existing = $this->db
            ->where('ItemID',   $data['ItemID'])
            ->where('CenterID', $data['CenterID'])
            ->get($this->masterTable)->row_array();

        if ($existing) {
            $this->archiveToHistory($existing);
            $this->db->where('ItemID', $data['ItemID'])
                     ->where('CenterID', $data['CenterID'])
                     ->delete($this->masterTable);
        }

        $this->db->insert($this->masterTable, $data);
        return $this->db->affected_rows() > 0;
    }

    public function UpdateRateMaster($id)
    {
        $current = $this->db->where('id', $id)->get($this->masterTable)->row_array();

        if (!$current) {
            return ['success' => false, 'message' => 'Record not found.'];
        }

        $date      = to_sql_date($this->input->post('EffectiveDate'));
        $transDate = ($date ?: date('Y-m-d')) . ' ' . date('H:i:s');

        $discount = $this->input->post('Discount');
        $newData  = $this->buildRateData(
            $this->input->post('ItemName') ?: $current['ItemID'],
            $current['CenterID'],
            $this->input->post('Rate')     ?: $current['sale_rate'],
            ($discount !== false && $discount !== '') ? $discount : $current['disc_amt'],
            $transDate,
            $this->session->userdata('username')
        );

        $this->db->trans_begin();

        $this->archiveToHistory($current);
        $this->db->where('id', $id)->delete($this->masterTable);
        $this->db->insert($this->masterTable, $newData);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Database error during update.'];
        }

        $this->db->trans_commit();
        return ['success' => true, 'action' => 'corrected', 'message' => 'Rate updated — previous rate archived to history.'];
    }
}