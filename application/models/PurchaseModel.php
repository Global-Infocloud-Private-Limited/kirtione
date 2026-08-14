<?php
defined("BASEPATH") or exit("No direct script access allowed");
class PurchaseModel extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_all_data($tbl, $where)
    {
        $this->db->select("*");
        $this->db->from($tbl);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }
    //============= Get All Active Center ==========================================
    public function GetAllAssignedCenterList($data = "")
    {
        $UserID = $this->session->userdata("username");
        if (is_admin()) {
            $this->db->select("tblCenterMaster.*");
            $this->db->from(db_prefix() . "CenterMaster");
            if (isset($data["CenterID"]) && !empty($data["CenterID"])) {
                $this->db->where_in(
                    "tblCenterMaster.CenterID",
                    $data["CenterID"]
                );
            }
            $this->db->where("tblCenterMaster.status", "Y");
            return $this->db->get()->result_array();
        } else {
            $this->db->select("tblCenterMaster.*");
            $this->db->from(db_prefix() . "CenterMaster");
            $this->db->join(
                "tblstaff_wise_center",
                "tblstaff_wise_center.CenterID = " .
                    db_prefix() .
                    "CenterMaster.CenterID"
            );
            $this->db->where("tblCenterMaster.status", "Y");
            if (isset($data["CenterID"]) && !empty($data["CenterID"])) {
                $this->db->where_in(
                    "tblCenterMaster.CenterID",
                    $data["CenterID"]
                );
            }
            $this->db->where("tblstaff_wise_center.AccountID", $UserID);
            return $this->db->get()->result_array();
        }
    }
    public function get_data($tbl, $where)
    {
        $this->db->select("*");
        $this->db->from($tbl);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function edit_data($tbl, $where, $arr)
    {
        $this->db->where($where);
        if ($this->db->update($tbl, $arr)) {
            return true;
        } else {
            return false;
        }
    }
    public function get_all_table_data($tbl)
    {
        $this->db->select("*");
        $this->db->from($tbl);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function get_items_code()
    {
        $selected_company = $this->session->userdata("root_company");
        return $this->db
            ->query(
                'SELECT ProductID as id, CONCAT(ProductID," - ",ProductName) as label,ProductName ,ProductID FROM ' .
                    db_prefix() .
                    "product WHERE PlantID = " .
                    $selected_company
            )
            ->result_array();
    }
    //=================== Add Kirti One Purchase Request =============================
    public function AddKirtiOnePurchaseRequest($data)
    {
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $ItemID = $value[0];
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $PlantID = $this->session->userdata("root_company");
        $FY = $this->session->userdata("finacial_year");
        $prefix = "PR";
        $purchase_orderNumbar = get_option(
            "next_purchase_request_number_for_kirtione"
        );
        $new_purchase_orderNumbar = $prefix . $FY . "1" . $purchase_orderNumbar;
        $Transdate = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $VendorID = $data["vendor"];
        $CenterID = $data["centername"];
        $this->db->select("tblCenterMaster.*");
        $this->db->from(db_prefix() . "CenterMaster");
        $this->db->where(db_prefix() . "CenterMaster.CenterID", $CenterID);
        $CenterDetails = $this->db->get()->row();
        $CenterState = $CenterDetails->state;
        $State = $data["state"];
        $PurchAmt = $data["total_amt_in_mt"];
        $discountAMT = $data["total_disc_in_mt"];
        $cgstamt = $data["total_cgst_amt"];
        $sgstamt = $data["total_sgst_amt"];
        $igstamt = $data["total_igst_amt"];
        $roundoffamt = $data["total_roundoff_amt"];
        $invoiceamt = $data["netpayableamt"];
        $ItCount = count($es_detail);
        $this->db->select("tblclients.*");
        $this->db->from(db_prefix() . "clients");
        $this->db->where(db_prefix() . "clients.AccountID", $VendorID);
        $traderlist = $this->db->get()->row();
        $nextPaymentnumber = get_option("next_payment_number_for_kirti");
        $KirtiOnePurchMaster = [
            "PlantID" => $PlantID,
            "FY" => $FY,
            "PurchID" => $new_purchase_orderNumbar,
            "Transdate" => $Transdate,
            "PartyID" => "KASPL",
            "CenterID" => $CenterID,
            "AccountID" => $VendorID,
            "Purchamt" => $PurchAmt,
            "Discamt" => $discountAMT,
            "cgstamt" => $cgstamt,
            "sgstamt" => $sgstamt,
            "igstamt" => $igstamt,
            "RoundOffAmt" => $roundoffamt,
            "Invamt" => $invoiceamt,
            "ItCount" => $ItCount,
            "Userid" => $_SESSION["username"],
        ];
        $this->db->insert(
            db_prefix() . "K1purchase_request_master",
            $KirtiOnePurchMaster
        );
        if ($this->db->affected_rows() > 0) {
            //$insert_id = $this->db->insert_id();
            $this->increment_next_number(
                "next_purchase_request_number_for_kirtione"
            );
            if ($traderlist->state == "") {
                $state_result = [
                    "state" => $State,
                ];
                $this->db->where("AccountID", $VendorID);
                $this->db->update(db_prefix() . "clients", $state_result);
            }
            $i = 1;
            foreach ($es_detail as $value) {
                $productId = $value["ItemID"];
                $brand = $value["Brand"];
                $unit = $value["MeasuredIn"];
                $packing_qty = $value["PackingQty"];
                $packing_weight = $value["PackingWeight"];
                $saleunit = $value["PurchaseUnit"];
                $qty = $value["Qty"];
                $amount = $value["PurchRate"];
                $discountAmount = $value["Discount"] * $qty;
                $gst = $value["GST"];
                if ($saleunit == $unit) {
                    $orderquantity = $packing_qty * $qty;
                    $finalOrderAmt = $totalAmount = $qty * $amount;
                } else {
                    $orderquantity = $qty;
                    $amountval = ($amount / $packing_qty) * $qty;
                    $finalOrderAmt = $totalAmount = $amountval;
                }
                $discount = 0;
                if ($discountAmount > 0) {
                    $discount = ($discountAmount / $totalAmount) * 100;
                    $finalOrderAmt = $totalAmount - $discountAmount;
                }
                $CGSTPer = null;
                $SGSTPer = null;
                $IGSTPer = null;
                $CGSTAmt = 0;
                $SGSTAmt = 0;
                $IGSTAmt = 0;
                $GSTAmt = $finalOrderAmt * ($gst / 100);
                if ($CenterState == $State) {
                    $CGSTPer = $gst / 2;
                    $SGSTPer = $gst / 2;
                    $CGSTAmt = $GSTAmt / 2;
                    $SGSTAmt = $GSTAmt / 2;
                } else {
                    $IGSTPer = $gst;
                    $IGSTAmt = $GSTAmt;
                }
                $netAmount = $finalOrderAmt + $GSTAmt;
                if ($saleunit == "Loose") {
                    $caseqty = 1;
                } else {
                    $caseqty = $packing_qty;
                }
                $data_array_result = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "OrderID" => $new_purchase_orderNumbar,
                    "BillID" => $new_purchase_orderNumbar,
                    "TransID" => $new_purchase_orderNumbar,
                    "TransDate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "TType" => "P",
                    "TType2" => "Request",
                    "AccountID" => $data["vendor"],
                    "ItemID" => $productId,
                    "CenterID" => $CenterID,
                    "PartyID" => "KASPL",
                    "PurchRate" => $amount,
                    "SaleRate" => $amount,
                    "BasicRate" => $amount,
                    "SuppliedIn" => $saleunit,
                    "OrderQty" => $orderquantity,
                    "BilledQty" => $orderquantity,
                    "DiscPerc" => $discount,
                    "DiscAmt" => $discountAmount,
                    "cgst" => $CGSTPer,
                    "cgstamt" => $CGSTAmt,
                    "sgst" => $SGSTPer,
                    "sgstamt" => $SGSTAmt,
                    "igst" => $IGSTPer,
                    "igstamt" => $IGSTAmt,
                    "CaseQty" => $caseqty,
                    "Cases" => 0.0,
                    "OrderAmt" => $totalAmount,
                    "ChallanAmt" => $totalAmount,
                    "NetOrderAmt" => $netAmount,
                    "NetChallanAmt" => $netAmount,
                    "Ordinalno" => $i,
                    "rowid" => "",
                    "UserID" => $_SESSION["username"],
                    "cnfid" => "",
                ];
                $this->db->insert(
                    db_prefix() . "K1history",
                    $data_array_result
                );
                $i++;
            }
            return $new_purchase_orderNumbar;
        }
    }
    //=================== Add Kirti One Purchase Order =============================
    public function AddKirtiOnePurchaseOrderNew($data)
    {
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "PRQty";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $ItemID = $value[0];
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $PlantID = $this->session->userdata("root_company");
        $FY = $this->session->userdata("finacial_year");
        /*echo "<pre>";
			print_r($es_detail);
			print_r($data);
			die;*/
        $prefix = "PO";
        $purchase_orderNumbar = get_option("next_purchase_number_for_kirtione");
        $new_purchase_orderNumbar = $prefix . $FY . "1" . $purchase_orderNumbar;
        $Transdate = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $VendorID = $data["vendor"];
        $Pr_no = null;
        if ($data["Pr_no"]) {
            $Pr_no = $data["Pr_no"];
            //$Pr_data = $this->GetPurchaseRequestDetails($Pr_no);
            //$CenterID = $Pr_data->CenterID;
        } else {
            $Pr_no = $new_purchase_orderNumbar;
        }
        $CenterID = $data["CenterName"];
        $this->db->select("tblCenterMaster.*");
        $this->db->from(db_prefix() . "CenterMaster");
        $this->db->where(db_prefix() . "CenterMaster.CenterID", $CenterID);
        $CenterDetails = $this->db->get()->row();
        $CenterState = $CenterDetails->state;
        $State = $data["state"];
        $PurchAmt = $data["total_amt_in_mt"];
        $discountAMT = $data["total_disc_in_mt"];
        $cgstamt = $data["total_cgst_amt"];
        $sgstamt = $data["total_sgst_amt"];
        $igstamt = $data["total_igst_amt"];
        $roundoffamt = $data["total_roundoff_amt"];
        $invoiceamt = $data["netpayableamt"];
        $VendorDocNo = $data["VendorDocNo"];
        $ItCount = count($es_detail);
        $reminderDate = !empty($data["reminder_date"])
            ? to_sql_date($data["reminder_date"])
            : null;
        $reminderRemark = !empty($data["reminder_remark"])
            ? trim($data["reminder_remark"])
            : null;
        unset($data["reminder_date"], $data["reminder_remark"]);
        $this->db->select("tblclients.*");
        $this->db->from(db_prefix() . "clients");
        $this->db->where(db_prefix() . "clients.AccountID", $VendorID);
        $traderlist = $this->db->get()->row();
        $KirtiOnePurchMaster = [
            "PlantID" => $PlantID,
            "FY" => $FY,
            "PurchID" => $new_purchase_orderNumbar,
            "Transdate" => $Transdate,
            "OrderStatus" => "P",
            "PartyID" => "KASPL",
            "Flag" => "Y",
            "Pr_no" => $Pr_no,
            "CenterID" => $CenterID,
            "InvoiceNo" => $VendorDocNo,
            "AccountID" => $VendorID,
            "Purchamt" => $PurchAmt,
            "Discamt" => $discountAMT,
            "cgstamt" => $cgstamt,
            "sgstamt" => $sgstamt,
            "igstamt" => $igstamt,
            "RoundOffAmt" => $roundoffamt,
            "Invamt" => $invoiceamt,
            "ItCount" => $ItCount,
            "Userid" => $_SESSION["username"],
            "ReminderDate" => $reminderDate,
            "ReminderRemark" => $reminderRemark,
            "ReminderSent" => 0,
        ];
        $this->db->insert(
            db_prefix() . "K1purchasemaster",
            $KirtiOnePurchMaster
        );
        if ($this->db->affected_rows() > 0) {
            //$insert_id = $this->db->insert_id();
            $this->increment_next_number("next_purchase_number_for_kirtione");
            if ($traderlist->state == "") {
                $state_result = [
                    "state" => $State,
                ];
                $this->db->where("AccountID", $VendorID);
                $this->db->update(db_prefix() . "clients", $state_result);
            }
            if ($Pr_no) {
                $PurchReq = [
                    "OrderStatus" => "F",
                ];
                $this->db->where("PurchID", $Pr_no);
                $this->db->update(
                    db_prefix() . "K1purchase_request_master",
                    $PurchReq
                );
                //$Pr_item_data = $this->GetPurchaseRequestItemListInvoiceAdd($Pr_no);
            }
            $i = 1;
            $itemcount = 0;
            $TotalPurchAmt = 0;
            $TotalDISCAmt = 0;
            $TotalCGSTAmt = 0;
            $TotalSGSTAmt = 0;
            $TotalIGSTAmt = 0;
            $TotalNetAmt = 0;
            foreach ($es_detail as $value) {
                $productId = $value["ItemID"];
                $brand = $value["Brand"];
                $unit = $value["MeasuredIn"];
                $packing_qty = $value["PackingQty"];
                $packing_weight = $value["PackingWeight"];
                $saleunit = $value["PurchaseUnit"];
                $qty = $value["Qty"];
                $PurchRate = $value["PurchRate"];
                $gst = $value["GST"];
                $salerate = $PurchRate + $PurchRate * ($gst / 100);
                $ItemQty = $packing_qty * $qty;
                $ItemAmt = $qty * $PurchRate;
                $ItemDisc = 0;
                $DiscPer = 0;
                $UnitDisc = 0;
                if ($value["Discount"] > 0 && $qty > 0) {
                    $ItemDisc = $value["Discount"] * $qty;
                    $UnitDisc = $value["Discount"];
                    $DiscPer = ($value["Discount"] / $PurchRate) * 100;
                }
                $ItemTaxableAmt = $ItemAmt - $ItemDisc;
                $ItemGSTAmt = $ItemTaxableAmt * ($gst / 100);
                $CGSTPer = null;
                $SGSTPer = null;
                $IGSTPer = null;
                $CGSTAmt = 0;
                $SGSTAmt = 0;
                $IGSTAmt = 0;
                if ($CenterState == $State) {
                    $CGSTPer = $gst / 2;
                    $SGSTPer = $gst / 2;
                    $CGSTAmt = $ItemGSTAmt / 2;
                    $SGSTAmt = $ItemGSTAmt / 2;
                } else {
                    $IGSTPer = $gst;
                    $IGSTAmt = $ItemGSTAmt;
                }
                $ItemNetAmt = $ItemTaxableAmt + $ItemGSTAmt;
                $TotalPurchAmt += $ItemAmt;
                $TotalDISCAmt += $ItemDisc;
                $TotalCGSTAmt += $CGSTAmt;
                $TotalSGSTAmt += $SGSTAmt;
                $TotalIGSTAmt += $IGSTAmt;
                $TotalNetAmt += $ItemNetAmt;
                if ($saleunit == "Loose") {
                    $caseqty = 1;
                } else {
                    $caseqty = $packing_qty;
                }
                $data_array_result = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "OrderID" => $new_purchase_orderNumbar,
                    "BillID" => $Pr_no,
                    "TransID" => $Pr_no,
                    "TransDate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "TType" => "P",
                    "TType2" => "Purchase Order",
                    "AccountID" => $data["vendor"],
                    "ItemID" => $productId,
                    "CenterID" => $CenterID,
                    "PartyID" => "KASPL",
                    "PurchRate" => $PurchRate,
                    "SaleRate" => $salerate,
                    "BasicRate" => $PurchRate,
                    "SuppliedIn" => $saleunit,
                    "OrderQty" => $ItemQty,
                    "BilledQty" => $ItemQty,
                    "DiscPerc" => $DiscPer,
                    "DiscAmt" => $UnitDisc,
                    "cgst" => $CGSTPer,
                    "cgstamt" => $CGSTAmt,
                    "sgst" => $SGSTPer,
                    "sgstamt" => $SGSTAmt,
                    "igst" => $IGSTPer,
                    "igstamt" => $IGSTAmt,
                    "CaseQty" => $caseqty,
                    "Cases" => 0.0,
                    "OrderAmt" => $ItemAmt,
                    "ChallanAmt" => $ItemAmt,
                    "NetOrderAmt" => $ItemNetAmt,
                    "NetChallanAmt" => $ItemNetAmt,
                    "Ordinalno" => $i,
                    "rowid" => "",
                    "UserID" => $_SESSION["username"],
                    "cnfid" => "",
                ];
                $this->db->insert(
                    db_prefix() . "K1history",
                    $data_array_result
                );
                $i++;
            }
            $roundedTotal = round($TotalNetAmt);
            $roundOffAmt = $roundedTotal - $TotalNetAmt;
            $KirtiOnePurchMaster = [
                "Purchamt" => $TotalPurchAmt,
                "Discamt" => $TotalDISCAmt,
                "cgstamt" => $TotalCGSTAmt,
                "sgstamt" => $TotalSGSTAmt,
                "igstamt" => $TotalIGSTAmt,
                "RoundOffAmt" => $roundOffAmt,
                "Invamt" => $roundedTotal,
                "ItCount" => $i,
            ];
            $this->db->where("PurchID", $new_purchase_orderNumbar);
            $this->db->update(
                db_prefix() . "K1purchasemaster",
                $KirtiOnePurchMaster
            );
            return $new_purchase_orderNumbar;
        }
    }

    public function CreatePurchaseOrder($data)
    {
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "PRQty";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $ItemID = $value[0];
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }

        // session data
        $PlantID = $this->session->userdata("root_company");
        $FY = $this->session->userdata("finacial_year");
        // echo "<pre>"; print_r($es_detail); print_r($data); die;

        // payload data
        $State          = $data["state"];
        $PurchAmt       = $data["total_amt_in_mt"];
        $discountAMT    = $data["total_disc_in_mt"];
        $cgstamt        = $data["total_cgst_amt"];
        $sgstamt        = $data["total_sgst_amt"];
        $igstamt        = $data["total_igst_amt"];
        $roundoffamt    = $data["total_roundoff_amt"];
        $invoiceamt     = $data["netpayableamt"];
        $VendorDocNo    = $data["VendorDocNo"];
        $ItCount        = count($es_detail);
        $reminderDate   = !empty($data["reminder_date"]) ? to_sql_date($data["reminder_date"]) : null;
        $reminderRemark = !empty($data["reminder_remark"]) ? trim($data["reminder_remark"]) : null;
        unset($data["reminder_date"], $data["reminder_remark"]);

        // generate new purchase order number
        $prefix = "PO";
        $purchase_orderNumbar = get_option("next_purchase_number_for_kirtione");
        $new_purchase_orderNumbar = $prefix . $FY . "1" . $purchase_orderNumbar;
        $Transdate = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $VendorID = $data["vendor"];
        $Pr_no = null;
        if ($data["Pr_no"]) {
            $Pr_no = $data["Pr_no"];
            $Pr_data = $this->db->get_where(db_prefix() . "K1purchase_request_master", ["PurchID" => $Pr_no])->row();
            $CenterID = $Pr_data->CenterID;
        } else {
            $Pr_no = $new_purchase_orderNumbar;
            $CenterID = $data["CenterName"];
        }

        // get center details
        $this->db->select("tblCenterMaster.*");
        $this->db->from(db_prefix() . "CenterMaster");
        $this->db->where(db_prefix() . "CenterMaster.CenterID", $CenterID);
        $CenterDetails = $this->db->get()->row();
        $CenterState = $CenterDetails->state;

        // get vendor details
        $this->db->select("tblclients.*");
        $this->db->from(db_prefix() . "clients");
        $this->db->where(db_prefix() . "clients.AccountID", $VendorID);
        $traderlist = $this->db->get()->row();

        // insert purchase order master
        $KirtiOnePurchMaster = [
            "PlantID"       => $PlantID,
            "FY"            => $FY,
            "PurchID"       => $new_purchase_orderNumbar,
            "Transdate"     => $Transdate,
            "OrderStatus"   => "P",
            "PartyID"       => "KASPL",
            "Flag"          => "Y",
            "Pr_no"         => $Pr_no,
            "CenterID"      => $CenterID,
            "InvoiceNo"     => $VendorDocNo,
            "AccountID"     => $VendorID,
            "Purchamt"      => $PurchAmt,
            "Discamt"       => $discountAMT,
            "cgstamt"       => $cgstamt,
            "sgstamt"       => $sgstamt,
            "igstamt"       => $igstamt,
            "RoundOffAmt"   => $roundoffamt,
            "Invamt"        => $invoiceamt,
            "ItCount"       => $ItCount,
            "Userid"        => $_SESSION["username"],
            "ReminderDate"  => $reminderDate,
            "ReminderRemark"=> $reminderRemark,
            "ReminderSent"  => 0,
        ];
        $this->db->insert(db_prefix() . "K1PurchaseOrderMaster", $KirtiOnePurchMaster);

        if ($this->db->affected_rows() > 0) {
            //$insert_id = $this->db->insert_id();
            $this->increment_next_number("next_purchase_number_for_kirtione");
            if ($traderlist->state == "") {
                $state_result = [
                    "state" => $State,
                ];
                $this->db->where("AccountID", $VendorID);
                $this->db->update(db_prefix() . "clients", $state_result);
            }

            if ($Pr_no) {
                $PurchReq = [
                    "OrderStatus" => "F",
                ];
                $this->db->where("PurchID", $Pr_no);
                $this->db->update(db_prefix() . "K1purchase_request_master", $PurchReq);
            }

            $i = 1;
            $itemcount = 0;
            $TotalPurchAmt = 0;
            $TotalDISCAmt = 0;
            $TotalCGSTAmt = 0;
            $TotalSGSTAmt = 0;
            $TotalIGSTAmt = 0;
            $TotalNetAmt = 0;
            foreach ($es_detail as $value) {
                $productId      = $value["ItemID"];
                $brand          = $value["Brand"];
                $unit           = $value["MeasuredIn"];
                $packing_qty    = $value["PackingQty"]; // 12
                $packing_weight = $value["PackingWeight"];
                $saleunit       = $value["PurchaseUnit"];
                $qty            = $value["Qty"]; // 100
                $PurchRate      = $value["PurchRate"]; // 1200
                $gst            = $value["GST"]; // 5

                $PurchRate  = $value["PurchRate"] / $value["PackingQty"]; // 1200 / 12 = 100
                $salerate   = ($PurchRate + ($PurchRate * ($gst / 100))); // 100 + (100 * (5 / 100)) = 105
                $ItemQty = $packing_qty * $qty; // 12 * 100 = 1200
                $ItemAmt = $ItemQty * $PurchRate; // 1200 * 100 = 120000
                $ItemDisc = 0;
                $DiscPer = 0;
                $UnitDisc = 0;
                if ($value["Discount"] > 0 && $ItemQty > 0) {
                    $UnitDisc = $value["Discount"]; // 5
                    $ItemDisc = $value["Discount"] * $ItemQty; // 5 * 1200 = 6000
                    $DiscPer = ($value["Discount"] / $PurchRate) * 100; // (5 / 100) * 100 = 5
                }
                $ItemTaxableAmt = $ItemAmt - $ItemDisc; // 120000 - 6000 = 114000
                $ItemGSTAmt = $ItemTaxableAmt * ($gst / 100); // 114000 * (5 / 100) = 5700
                $CGSTPer = null;
                $SGSTPer = null;
                $IGSTPer = null;
                $CGSTAmt = 0;
                $SGSTAmt = 0;
                $IGSTAmt = 0;
                if ($CenterState == $State) {
                    $CGSTPer = $gst / 2;
                    $SGSTPer = $gst / 2;
                    $CGSTAmt = $ItemGSTAmt / 2;
                    $SGSTAmt = $ItemGSTAmt / 2;
                } else {
                    $IGSTPer = $gst;
                    $IGSTAmt = $ItemGSTAmt;
                }
                $ItemNetAmt = $ItemTaxableAmt + $ItemGSTAmt;
                $TotalPurchAmt += $ItemAmt;
                $TotalDISCAmt += $ItemDisc;
                $TotalCGSTAmt += $CGSTAmt;
                $TotalSGSTAmt += $SGSTAmt;
                $TotalIGSTAmt += $IGSTAmt;
                $TotalNetAmt += $ItemNetAmt;
                if ($saleunit == "Loose") {
                    $caseqty = 1;
                } else {
                    $caseqty = $packing_qty;
                }

                $data_array_result = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "OrderID" => $new_purchase_orderNumbar,
                    "BillID" => $Pr_no,
                    "TransID" => $Pr_no,
                    "TransDate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "TType" => "P",
                    "TType2" => "Purchase Order",
                    "AccountID" => $data["vendor"],
                    "ItemID" => $productId,
                    "CenterID" => $CenterID,
                    "PartyID" => "KASPL",
                    "PurchRate" => $PurchRate,
                    "SaleRate" => $salerate,
                    "BasicRate" => $PurchRate,
                    "SuppliedIn" => $saleunit,
                    "OrderQty" => $ItemQty,
                    "BilledQty" => $ItemQty,
                    "DiscPerc" => $DiscPer,
                    "DiscAmt" => $UnitDisc,
                    "cgst" => $CGSTPer,
                    "cgstamt" => $CGSTAmt,
                    "sgst" => $SGSTPer,
                    "sgstamt" => $SGSTAmt,
                    "igst" => $IGSTPer,
                    "igstamt" => $IGSTAmt,
                    "CaseQty" => $caseqty,
                    "Cases" => 0.0,
                    "OrderAmt" => $ItemAmt,
                    "ChallanAmt" => $ItemAmt,
                    "NetOrderAmt" => $ItemNetAmt,
                    "NetChallanAmt" => $ItemNetAmt,
                    "Ordinalno" => $i,
                    "rowid" => "",
                    "UserID" => $_SESSION["username"],
                    "cnfid" => "",
                ];
                $this->db->insert(
                    db_prefix() . "K1history",
                    $data_array_result
                );
                $i++;
            }

            $roundedTotal = round($TotalNetAmt);
            $roundOffAmt = $roundedTotal - $TotalNetAmt;
            $KirtiOnePurchMaster = [
                "Purchamt"  => $TotalPurchAmt,
                "Discamt"   => $TotalDISCAmt,
                "cgstamt"   => $TotalCGSTAmt,
                "sgstamt"   => $TotalSGSTAmt,
                "igstamt"   => $TotalIGSTAmt,
                "RoundOffAmt" => $roundOffAmt,
                "Invamt"    => $roundedTotal,
                "ItCount"   => $i,
            ];
            $this->db->where("PurchID", $new_purchase_orderNumbar);
            $this->db->update(db_prefix() . "K1PurchaseOrderMaster", $KirtiOnePurchMaster);
            return $new_purchase_orderNumbar;
        }
    }

    public function AddDemandList($data)
    {
        if (isset($data["pur_order_detail"])) {
            $CenterID = $data["centername"];
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "Item_Name";
            $header[] = "Qty";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $Item_Name = $value[0];
                    $es_detail[] = array_combine($header, $value);
                }
            }
            foreach ($es_detail as $value) {
                $Item_Name = $value["Item_Name"];
                $Qty = $value["Qty"];
                $data_array_result = [
                    "CenterID" => $CenterID,
                    "ItemID" => $Item_Name,
                    "Qty" => $Qty,
                    "UserID" => $_SESSION["username"],
                    "TransDate" => date("Y-m-d H:i:s"),
                ];
                $this->db->insert(
                    db_prefix() . "DemandList",
                    $data_array_result
                );
            }
            return true;
        }
        return false;
    }
    public function UpdateDemandList($data, $id)
    {
        if (isset($data["pur_order_detail"])) {
            $CenterID = $data["centername"];
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "Item_Name";
            $header[] = "Qty";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $Item_Name = $value[0];
                    $es_detail[] = array_combine($header, $value);
                }
            }
            foreach ($es_detail as $value) {
                $Item_Name = $value["Item_Name"];
                $Qty = $value["Qty"];
                $data_array_result = [
                    "Qty" => $Qty,
                    "UserID2" => $_SESSION["username"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                ];
                $this->db->where("id", $id);
                $this->db->where("ItemID", $Item_Name);
                $this->db->update(
                    db_prefix() . "DemandList",
                    $data_array_result
                );
            }
            return true;
        }
        return false;
    }
    public function GetdemandListByCenter($center)
    {
        $this->db->select("DemandList.ItemID AS Item_Name, DemandList.Qty");
        $this->db->from(db_prefix() . "DemandList");
        $this->db->where("DemandList.CenterID", $center);
        $items = $this->db->get()->result_array();
        return $items;
    }
    public function GetDemandList($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");
        $this->db->select(
            "tblDemandList.*,tblDemandList.ItemID AS Item_Name,tblDemandList.Qty AS Qty"
        );
        $this->db->from(db_prefix() . "DemandList");
        $this->db->where(db_prefix() . "DemandList.id", $id);
        return $this->db->get()->row();
    }

    //=================== Add Kirti One Purchase Inward =============================
    public function CreatePurchaseInward($data)
    {
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "POrderQty";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            $header[] = "BatchNo";
            $header[] = "ExpDate";
            $header[] = "Ordinalno";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $ItemID = $value[0];
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $PlantID = $this->session->userdata("root_company");
        $FY = $this->session->userdata("finacial_year");
        // batch no duplication check added 21apr26
        foreach ($es_detail as $value) {
            $where = [
                "TType" => "P",
                "TType2" => "Purchase",
                "BatchNo" => $value["BatchNo"],
            ];
            $find = $this->db
                ->select("*")
                ->from("tblK1history")
                ->where($where)
                ->get()
                ->row();
            if (!empty($find)) {
                return [
                    "status" => false,
                    "message" => "Batch No Present",
                    "data" => $value["BatchNo"],
                ];
                die();
            }
        }
        // end
        $prefix = "PI";
        $purchase_Inv_Numbar = get_option(
            "next_purchase_invoice_number_for_kirtione"
        );
        $new_purchase_Inv_Number = $prefix . $FY . "1" . $purchase_Inv_Numbar;

        $Transdate      = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $VendorID       = $data["vendor"];
        $PurchID        = $data["PurchID"];
        $CenterName     = $data["CenterName"];
        $State          = $data["state"];
        $CenterState    = $data["CenterState"];
        $PurchAmt       = $data["total_amt_in_mt"];
        $discountAMT    = $data["total_disc_in_mt"];
        $cgstamt        = $data["total_cgst_amt"];
        $sgstamt        = $data["total_sgst_amt"];
        $igstamt        = $data["total_igst_amt"];
        $roundoffamt    = $data["total_roundoff_amt"];
        $invoiceamt     = $data["netpayableamt"] + ($data["OtherAmt"] ?? 0);
        $EwayBill       = $data["ewaybillno"];
        $VehicleNo      = $data["VehicleNo"];
        $InvoiceNo      = $data["InvoiceNo"];
        $OtherAmt       = $data["OtherAmt"] ?? 0;
        $OthEffectOn    = $data["OthEffectOn"] ?? null;
        $total_tcs_amt  = $data["total_tcs_amt"];
        $ItCount        = count($es_detail);

        // Purchase Order Details
        $this->db->select('tblK1PurchaseOrderMaster.*');
        $this->db->from(db_prefix() . 'K1PurchaseOrderMaster');
        $this->db->where(db_prefix() . 'K1PurchaseOrderMaster.PurchID', $PurchID);
        $POD = $this->db->get()->row();
        $CenterID = $POD->CenterID;
        $PurchReqNo = $POD->Pr_no;

        $this->db->select("tblclients.*,tblGstRecord.gstin");
        $this->db->from(db_prefix() . "clients");
        $this->db->join(
            db_prefix() . "GstRecord",
            'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"',
            "LEFT"
        );
        $this->db->where(db_prefix() . "clients.AccountID", $VendorID);
        $traderlist = $this->db->get()->row();

        $GSTIN = null;
        if ($traderlist->gstin) {
            $BT = "Y";
            $GSTIN = $traderlist->gstin;
        } else {
            $BT = "N";
        }
        
        $KirtiOnePurchMaster = [
            "PlantID"       => $PlantID,
            "FY"            => $FY,
            "PurchID"       => $PurchID,
            "Inv_No"        => $new_purchase_Inv_Number,
            "BT"            => $BT,
            "GSTIN"         => $GSTIN,
            "Inv_date"      => $Transdate,
            "EwayBillNo"    => $EwayBill,
            "VehicleNo"     => $VehicleNo,
            "InvoiceNo"     => $InvoiceNo,
            "Transdate"     => $Transdate,
            "OrderStatus"   => "P",
            "PartyID"       => "KASPL",
            "Flag"          => "Y",
            "Pr_no"         => $POD->Pr_no,
            "CenterID"      => $CenterID,
            "AccountID"     => $VendorID,
            "Purchamt"      => $PurchAmt,
            "Discamt"       => $discountAMT,
            "cgstamt"       => $cgstamt,
            "sgstamt"       => $sgstamt,
            "igstamt"       => $igstamt,
            "RoundOffAmt"   => $roundoffamt,
            "Invamt"        => $invoiceamt,
            "ItCount"       => $ItCount,
            "Userid"        => $_SESSION["username"],
            "ReminderDate"  => $POD->ReminderDate,
            "ReminderRemark"    => $POD->ReminderRemark,
            "ReminderSent"  => $POD->ReminderSent,
        ];
        $this->db->insert(db_prefix() . "K1purchasemaster",$KirtiOnePurchMaster);

        if ($this->db->affected_rows() > 0) {
            
            $this->increment_next_number("next_purchase_invoice_number_for_kirtione");

            if ($traderlist->state == "") {
                $state_result = [
                    "state" => $State,
                ];
                $this->db->where("AccountID", $VendorID);
                $this->db->update(db_prefix() . "clients", $state_result);
            }
            $i = 1;
            $TotalPurchAmt = 0;
            $TotalDISCAmt = 0;
            $TotalCGSTAmt = 0;
            $TotalSGSTAmt = 0;
            $TotalIGSTAmt = 0;
            $TotalNetAmt = 0;
            foreach ($es_detail as $value) {
                $productId      = $value["ItemID"];
                $Ordinalno      = $value["Ordinalno"];
                $brand          = $value["Brand"];
                $unit           = $value["MeasuredIn"];
                $packing_qty    = $value["PackingQty"]; // 12
                $packing_weight = $value["PackingWeight"];
                $saleunit       = $value["PurchaseUnit"];
                $qty            = $value["Qty"]; // 100
                $PurchRate      = $value["PurchRate"]; // 1200
                $gst            = $value["GST"]; // 5

                $PurchRate  = $value["PurchRate"] / $value["PackingQty"]; // 1200 / 12 = 100
                $salerate   = ($PurchRate + ($PurchRate * ($gst / 100))); // 100 + (100 * (5 / 100)) = 105
                $ItemQty = $packing_qty * $qty; // 12 * 100 = 1200
                $ItemAmt = $ItemQty * $PurchRate; // 1200 * 100 = 120000
                $ItemDisc = 0;
                $DiscPer = 0;
                $UnitDisc = 0;
                if ($value["Discount"] > 0 && $ItemQty > 0) {
                    $UnitDisc = $value["Discount"]; // 5
                    $ItemDisc = $value["Discount"] * $ItemQty; // 5 * 1200 = 6000
                    $DiscPer = ($value["Discount"] / $PurchRate) * 100; // (5 / 100) * 100 = 5
                }
                $ItemTaxableAmt = $ItemAmt - $ItemDisc; // 120000 - 6000 = 114000
                $ItemGSTAmt = $ItemTaxableAmt * ($gst / 100); // 114000 * (5 / 100) = 5700
                
                $CGSTPer = null;
                $SGSTPer = null;
                $IGSTPer = null;
                $CGSTAmt = 0;
                $SGSTAmt = 0;
                $IGSTAmt = 0;
                if ($CenterState == $State) {
                    $CGSTPer = $gst / 2;
                    $SGSTPer = $gst / 2;
                    $CGSTAmt = $ItemGSTAmt / 2;
                    $SGSTAmt = $ItemGSTAmt / 2;
                } else {
                    $IGSTPer = $gst;
                    $IGSTAmt = $ItemGSTAmt;
                }
                $ItemNetAmt = $ItemTaxableAmt + $ItemGSTAmt;
                $TotalPurchAmt += $ItemAmt;
                $TotalDISCAmt += $ItemDisc;
                $TotalCGSTAmt += $CGSTAmt;
                $TotalSGSTAmt += $SGSTAmt;
                $TotalIGSTAmt += $IGSTAmt;
                $TotalNetAmt += $ItemNetAmt;
                if ($saleunit == "Loose") {
                    $caseqty = 1;
                } else {
                    $caseqty = $packing_qty;
                }
                $data_array_result = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "OrderID" => $PurchID,
                    "BillID" => $PurchReqNo,
                    "TransDate" => date("Y-m-d H:i:s"),
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "TType" => "P",
                    "TType2" => "Purchase",
                    "AccountID" => $VendorID,
                    "ItemID" => $productId,
                    "CenterID" => $CenterID,
                    "PartyID" => "KASPL",
                    "TransID" => $new_purchase_Inv_Number,
                    "PurchRate" => $PurchRate,
                    "SaleRate" => $salerate,
                    "BasicRate" => $PurchRate,
                    "SuppliedIn" => $saleunit,
                    "OrderQty" => $ItemQty,
                    "BilledQty" => $ItemQty,
                    "DiscPerc" => $DiscPer,
                    "DiscAmt" => $UnitDisc,
                    "cgst" => $CGSTPer,
                    "cgstamt" => $CGSTAmt,
                    "sgst" => $SGSTPer,
                    "sgstamt" => $SGSTAmt,
                    "igst" => $CGSTPer,
                    "igstamt" => $IGSTAmt,
                    "CaseQty" => $caseqty,
                    "Cases" => 0.0,
                    "OrderAmt" => $ItemAmt,
                    "ChallanAmt" => $ItemAmt,
                    "NetOrderAmt" => $ItemNetAmt,
                    "NetChallanAmt" => $ItemNetAmt,
                    "Ordinalno" => $i,
                    "BatchNo" => $value["BatchNo"],
                    "ExpDate" => to_sql_date($value["ExpDate"]),
                    "UserID2" => $_SESSION["username"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                ];
                $this->db->insert(db_prefix() . "K1history", $data_array_result);
                
                $i++;
            }

            // Final Calculation for Total Amounts and Round Off
            $roundedTotal = round($TotalNetAmt + $OtherAmt);
            $roundOffAmt = $roundedTotal - ($TotalNetAmt + $OtherAmt);
            $KirtiOnePurchMaster = [
                "Purchamt" => $TotalPurchAmt,
                "Discamt" => $TotalDISCAmt,
                "Othamt" => $OtherAmt,
                "OthAmtEffectOn" => $OthEffectOn,
                "tcsAmt" => $total_tcs_amt,
                "cgstamt" => $TotalCGSTAmt,
                "sgstamt" => $TotalSGSTAmt,
                "igstamt" => $TotalIGSTAmt,
                "RoundOffAmt" => $roundOffAmt,
                "Invamt" => $roundedTotal,
                "ItCount" => $i,
            ];
            $this->db->where(["PurchID" => $PurchID, "Inv_No" => $new_purchase_Inv_Number]);
            $this->db->update(db_prefix() . "K1purchasemaster", $KirtiOnePurchMaster);

            // Order Status Update
            // Total Order qty
            $this->db->select_sum('BilledQty');
            $this->db->from(db_prefix() . 'K1history');
            $this->db->where([
                'OrderID' => $PurchID,
                'TType' => 'P',
                'TType2' => 'Purchase Order'
            ]);
            $total_order_qty = $this->db->get()->row()->BilledQty ?? 0;

            // Total Inward qty
            $this->db->select_sum('BilledQty');
            $this->db->from(db_prefix() . 'K1history');
            $this->db->where([
                'OrderID' => $PurchID,
                'TType' => 'P',
                'TType2' => 'Purchase'
            ]);
            $total_inward_qty = $this->db->get()->row()->BilledQty ?? 0;

            $order_status = 'P'; // Pending by default
            if (($total_order_qty - $total_inward_qty) <= 0) {
                $order_status = 'F'; // Fully received
            } else{
                $order_status = 'I'; // Partially received
            }

            $KirtiOnePurchMaster = [
                "OrderStatus" => $order_status,
            ];
            $this->db->where("PurchID", $PurchID);
            $this->db->update(db_prefix() . "K1PurchaseOrderMaster", $KirtiOnePurchMaster); 
            
            return $new_purchase_Inv_Number;
        }
    }

    //=================== Add Kirti One Purchase Invoice =============================
    public function AddKirtiOnePurchaseInvoice($data)
    {
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            $header[] = "BatchNo";
            $header[] = "ExpDate";
            $header[] = "Ordinalno";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $ItemID = $value[0];
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $PlantID = $this->session->userdata("root_company");
        $FY = $this->session->userdata("finacial_year");
        // batch no duplication check added 21apr26
        foreach ($es_detail as $value) {
            $where = [
                "TType" => "P",
                "TType2" => "Purchase",
                "BatchNo" => $value["BatchNo"],
            ];
            $find = $this->db
                ->select("*")
                ->from("tblK1history")
                ->where($where)
                ->get()
                ->row();
            if (!empty($find)) {
                return [
                    "status" => false,
                    "message" => "Batch No Present",
                    "data" => $value["BatchNo"],
                ];
                die();
            }
        }
        // end
        $prefix = "PI";
        $purchase_Inv_Numbar = get_option(
            "next_purchase_invoice_number_for_kirtione"
        );
        $new_purchase_Inv_Number = $prefix . $FY . "1" . $purchase_Inv_Numbar;
        $Transdate = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $VendorID = $data["vendor"];
        $PurchID = $data["PurchID"];
        $CenterID = $data["CenterID"];
        // Purchase Order Details
        $this->db->select('tblK1purchasemaster.*');
        $this->db->from(db_prefix() . 'K1purchasemaster');
        $this->db->where(db_prefix() . 'K1purchasemaster.PurchID', $PurchID);
        $PurchOrderDetails = $this->db->get()->row();
        $CenterID = $PurchOrderDetails->CenterID;
        $PurchReqNo = $PurchOrderDetails->Pr_no;

        // $this->db->select('tblCenterMaster.*');
        // $this->db->from(db_prefix() . 'CenterMaster');
        // $this->db->where(db_prefix() . 'CenterMaster.CenterID', $CenterID);
        // $CenterDetails = $this->db->get()->row();
        // $CenterState = $CenterDetails->state;

        $State = $data["state"];
        $CenterState = $data["CenterState"];
        $PurchAmt = $data["total_amt_in_mt"];
        $discountAMT = $data["total_disc_in_mt"];
        $cgstamt = $data["total_cgst_amt"];
        $sgstamt = $data["total_sgst_amt"];
        $igstamt = $data["total_igst_amt"];
        $roundoffamt = $data["total_roundoff_amt"];
        $invoiceamt = $data["netpayableamt"] + ($data["OtherAmt"] ?? 0);
        $EwayBill = $data["ewaybillno"];
        $VehicleNo = $data["VehicleNo"];
        $InvoiceNo = $data["InvoiceNo"];
        $OtherAmt = $data["OtherAmt"] ?? 0;
        $OthEffectOn = $data["OthEffectOn"] ?? null;
        $total_tcs_amt = $data["total_tcs_amt"];
        $ItCount = count($es_detail);
        $this->db->select("tblclients.*,tblGstRecord.gstin");
        $this->db->from(db_prefix() . "clients");
        $this->db->join(
            db_prefix() . "GstRecord",
            'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"',
            "LEFT"
        );
        $this->db->where(db_prefix() . "clients.AccountID", $VendorID);
        $traderlist = $this->db->get()->row();
        $GSTIN = null;
        if ($traderlist->gstin) {
            $BT = "Y";
            $GSTIN = $traderlist->gstin;
        } else {
            $BT = "N";
        }
        $KirtiOnePurchMaster = [
            "Inv_No" => $new_purchase_Inv_Number,
            "BT" => $BT,
            "GSTIN" => $GSTIN,
            "Inv_date" => $Transdate,
            "EwayBillNo" => $EwayBill,
            "VehicleNo" => $VehicleNo,
            "InvoiceNo" => $InvoiceNo,
            "OrderStatus" => "F",
            "UserID2" => $_SESSION["username"],
            "Lupdate" => date("Y-m-d H:i:s"),
        ];
        $this->db->where("PurchID", $PurchID);
        $this->db->update(
            db_prefix() . "K1purchasemaster",
            $KirtiOnePurchMaster
        );
        if ($this->db->affected_rows() > 0) {
            //$insert_id = $this->db->insert_id();
            $this->increment_next_number(
                "next_purchase_invoice_number_for_kirtione"
            );
            if ($traderlist->state == "") {
                $state_result = [
                    "state" => $State,
                ];
                $this->db->where("AccountID", $VendorID);
                $this->db->update(db_prefix() . "clients", $state_result);
            }
            $i = 1;
            $TotalPurchAmt = 0;
            $TotalDISCAmt = 0;
            $TotalCGSTAmt = 0;
            $TotalSGSTAmt = 0;
            $TotalIGSTAmt = 0;
            $TotalNetAmt = 0;
            foreach ($es_detail as $value) {
                $productId = $value["ItemID"];
                $Ordinalno = $value["Ordinalno"];
                $brand = $value["Brand"];
                $unit = $value["MeasuredIn"];
                $packing_qty = $value["PackingQty"];
                $packing_weight = $value["PackingWeight"];
                $saleunit = $value["PurchaseUnit"];
                $qty = $value["Qty"];
                $PurchRate = $value["PurchRate"];
                $gst = $value["GST"];
                $salerate = $PurchRate + $PurchRate * ($gst / 100);
                $ItemQty = $packing_qty * $qty;
                $ItemAmt = $qty * $PurchRate;
                $ItemDisc = 0;
                $DiscPer = 0;
                $UnitDisc = 0;
                if ($value["Discount"] > 0 && $qty > 0) {
                    $ItemDisc = $value["Discount"] * $qty;
                    $UnitDisc = $value["Discount"];
                    $DiscPer = ($value["Discount"] / $PurchRate) * 100;
                }
                $ItemTaxableAmt = $ItemAmt - $ItemDisc;
                $ItemGSTAmt = $ItemTaxableAmt * ($gst / 100);
                $CGSTPer = null;
                $SGSTPer = null;
                $IGSTPer = null;
                $CGSTAmt = 0;
                $SGSTAmt = 0;
                $IGSTAmt = 0;
                if ($CenterState == $State) {
                    $CGSTPer = $gst / 2;
                    $SGSTPer = $gst / 2;
                    $CGSTAmt = $ItemGSTAmt / 2;
                    $SGSTAmt = $ItemGSTAmt / 2;
                } else {
                    $IGSTPer = $gst;
                    $IGSTAmt = $ItemGSTAmt;
                }
                $ItemNetAmt = $ItemTaxableAmt + $ItemGSTAmt;
                $TotalPurchAmt += $ItemAmt;
                $TotalDISCAmt += $ItemDisc;
                $TotalCGSTAmt += $CGSTAmt;
                $TotalSGSTAmt += $SGSTAmt;
                $TotalIGSTAmt += $IGSTAmt;
                $TotalNetAmt += $ItemNetAmt;
                if ($saleunit == "Loose") {
                    $caseqty = 1;
                } else {
                    $caseqty = $packing_qty;
                }
                $data_array_result = [
                    "TransID" => $new_purchase_Inv_Number,
                    "PurchRate" => $PurchRate,
                    "SaleRate" => $salerate,
                    "BasicRate" => $PurchRate,
                    "SuppliedIn" => $saleunit,
                    "OrderQty" => $ItemQty,
                    "BilledQty" => $ItemQty,
                    "DiscPerc" => $DiscPer,
                    "DiscAmt" => $UnitDisc,
                    "cgst" => $CGSTPer,
                    "cgstamt" => $CGSTAmt,
                    "sgst" => $SGSTPer,
                    "sgstamt" => $SGSTAmt,
                    "igst" => $CGSTPer,
                    "igstamt" => $IGSTAmt,
                    "CaseQty" => $caseqty,
                    "Cases" => 0.0,
                    "OrderAmt" => $ItemAmt,
                    "ChallanAmt" => $ItemAmt,
                    "NetOrderAmt" => $ItemNetAmt,
                    "NetChallanAmt" => $ItemNetAmt,
                    "Ordinalno" => $i,
                    "BatchNo" => $value["BatchNo"],
                    "ExpDate" => to_sql_date($value["ExpDate"]),
                    "UserID2" => $_SESSION["username"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                ];
                // $this->db->where("OrderID", $PurchID);
                // $this->db->where("Ordinalno", $Ordinalno);
                // $this->db->where("ItemID", $productId);
                // $this->db->where("AccountID", $VendorID);

                // extra conditions
                $where = ['OrderID' => $PurchID, 'ItemID' => $productId, 'AccountID' => $VendorID, 'TransID' => NULL];
                $check_existing = $this->db->select('*')->from(db_prefix() . "K1history")->where($where)->get()->row();
                if (!empty($check_existing)) {
                    $this->db->where($where);
                    $this->db->update(db_prefix() . "K1history", $data_array_result);
                } else {
                    $data_array_result["PlantID"] = $PlantID;
                    $data_array_result["FY"] = $FY;
                    $data_array_result["OrderID"] = $PurchID;
                    $data_array_result["BillID"] = $PurchReqNo;
                    $data_array_result["TransDate"] = date("Y-m-d H:i:s");
                    $data_array_result["TransDate2"] = date("Y-m-d H:i:s");
                    $data_array_result["TType"] = "P";
                    $data_array_result["TType2"] = "Purchase";
                    $data_array_result["AccountID"] = $VendorID;
                    $data_array_result["ItemID"] = $productId;
                    $data_array_result["CenterID"] = $CenterID;
                    $data_array_result["PartyID"] = "KASPL";

                    $this->db->insert(db_prefix() . "K1history", $data_array_result);
                }
                // $this->db->update(db_prefix() . "K1history", $data_array_result, $where);
                $i++;
            }
            $roundedTotal = round($TotalNetAmt + $OtherAmt);
            $roundOffAmt = $roundedTotal - ($TotalNetAmt + $OtherAmt);
            $KirtiOnePurchMaster = [
                "Purchamt" => $TotalPurchAmt,
                "Discamt" => $TotalDISCAmt,
                "Othamt" => $OtherAmt,
                "OthAmtEffectOn" => $OthEffectOn,
                "tcsAmt" => $total_tcs_amt,
                "cgstamt" => $TotalCGSTAmt,
                "sgstamt" => $TotalSGSTAmt,
                "igstamt" => $TotalIGSTAmt,
                "RoundOffAmt" => $roundOffAmt,
                "Invamt" => $roundedTotal,
                "ItCount" => $i,
            ];
            $this->db->where("PurchID", $PurchID);
            $this->db->update(
                db_prefix() . "K1purchasemaster",
                $KirtiOnePurchMaster
            );
            return $new_purchase_Inv_Number;
        }
    }
    //=================== Add Kirti One Purchase Invoice =============================
    public function AddKirtiOnePurchaseInvoiceLedger($data)
    {
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $ItemID = $value[0];
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $PlantID = $this->session->userdata("root_company");
        $FY = $this->session->userdata("finacial_year");
        $VendorID = $data["vendor"];
        $VendorDocNo = $data["VendorDocNo"];
        $Inv_No = $data["PurchID"];
        $State = $data["state"];
        // $CenterState = $data["state"];
        $PurchaseType = $data["purchasetype"];
        $PaymentMode = $data["paymode"];
        $PaymentMethod = $data["paymentmethod"];
        $Refno = $data["referenceno"];
        $Effecton = $data["Effecton"];
        $ExpenceType = $data["expense_type"];
        $ExpenceAmt = $data["expense_amt"];
        $IncomeType = $data["income_type"];
        $IncomeAmt = $data["income_amt"];
        $PurchAmt = $data["total_amt_in_mt"];
        $discountAMT = $data["total_disc_in_mt"];
        $cgstamt = $data["total_cgst_amt"];
        $sgstamt = $data["total_sgst_amt"];
        $igstamt = $data["total_igst_amt"];
        $roundoffamt = $data["total_roundoff_amt"];
        $invoiceamt = $data["netpayableamt"];
        $InvData = $this->GetPurchaseInvoiceDetails($Inv_No);
        $CenterID = $InvData->CenterID;
        $Transdate = substr($InvData->Inv_date, 0, 19);
        $ItCount = count($es_detail);
        $nextPaymentnumber = get_option("next_payment_number_for_kirti");
        if ($PurchaseType == 2) {
            $PaymentMode = null;
            $Effecton = null;
            $PaymentMethod = null;
            $Refno = null;
        }
        if ($PurchaseType == 1) {
            $PaymentNo = $nextPaymentnumber;
        } else {
            $PaymentNo = null;
        }

        // rate update code new ============
        foreach ($es_detail as $value) {
            $productId = $value["ItemID"];
            $qty = $value["Qty"];
            $PurchRate = $value["PurchRate"];
            $gst = $value["GST"];
            $salerate = $PurchRate + $PurchRate * ($gst / 100);
            $ItemAmt = $qty * $PurchRate;
            $ItemDisc = 0;
            $DiscPer = 0;
            $UnitDisc = 0;
            if ($value["Discount"] > 0 && $qty > 0) {
                $ItemDisc = $value["Discount"] * $qty;
                $UnitDisc = $value["Discount"];
                $DiscPer = ($value["Discount"] / $PurchRate) * 100;
            }
            $ItemTaxableAmt = $ItemAmt - $ItemDisc;
            $ItemGSTAmt = $ItemTaxableAmt * ($gst / 100);
            // $CGSTPer = null;
            // $SGSTPer = null;
            // $IGSTPer = null;
            // $CGSTAmt = 0;
            // $SGSTAmt = 0;
            // $IGSTAmt = 0;
            // if ($CenterState == $State) {
            //     $CGSTPer = $gst / 2;
            //     $SGSTPer = $gst / 2;
            //     $CGSTAmt = $ItemGSTAmt / 2;
            //     $SGSTAmt = $ItemGSTAmt / 2;
            // } else {
            //     $IGSTPer = $gst;
            //     $IGSTAmt = $ItemGSTAmt;
            // }

            $data_array_result = [
                "PurchRate" => $PurchRate,
                "SaleRate" => $salerate,
                "BasicRate" => $PurchRate,
                "DiscPerc" => $DiscPer,
                "DiscAmt" => $UnitDisc,
                "OrderAmt" => $ItemAmt,
                "ChallanAmt" => $ItemAmt,
                "UserID2" => $_SESSION["username"],
                "Lupdate" => date("Y-m-d H:i:s"),
            ];
            $this->db->where("TransID", $Inv_No);
            $this->db->where("OrderQty", $qty);
            $this->db->where("ItemID", $productId);
            $this->db->where("AccountID", $VendorID);
            // $this->db->where("BatchNo", $value["BatchNo"]);
            $this->db->update(db_prefix() . "K1history", $data_array_result);
        }
        // rate update code new ============
        
        $this->db->select("tblclients.*");
        $this->db->from(db_prefix() . "clients");
        $this->db->where(db_prefix() . "clients.AccountID", $VendorID);
        $traderlist = $this->db->get()->row();
        $KirtiOnePurchMaster = [
            "Is_Ledger" => "Y",
            "InvoiceNo" => $VendorDocNo,
            "PaymentNo" => $PaymentNo,
            "PurchaseType" => $PurchaseType,
            "PaymentMode" => $PaymentMode,
            "PaymentMethod" => $PaymentMethod,
            "RefNo" => $Refno,
			"Invamt" => $invoiceamt,
            "EffectOn" => $Effecton,
            "UserID2" => $_SESSION["username"],
            "Lupdate" => date("Y-m-d H:i:s"),
        ];
        $this->db->where("Inv_No", $Inv_No);
        $this->db->update(
            db_prefix() . "K1purchasemaster",
            $KirtiOnePurchMaster
        );
        if ($this->db->affected_rows() > 0) {
            // save expense entry
            if(count($ExpenceType) > 0){
                for($i=0; $i<count($ExpenceType); $i++){
                    if(empty($ExpenceAmt[$i]) || $ExpenceAmt[$i] == 0){
                        continue;
                    }
                    $expence_entry = [
                        "PlantID" => $PlantID,
                        "FY" => $FY,
                        "Transdate" => $Transdate,
                        "UserID" => $_SESSION["username"],
                        "Inv_No" => $Inv_No,
                        "LedgerCategory" => 'Direct Expense',
                        "LedgerType" => $ExpenceType[$i],
                        "Amount" => $ExpenceAmt[$i],
                    ];
                    $this->db->insert(db_prefix() . "K1PurchaseMasterExpenses", $expence_entry);
                }
            }
            // save income entry
            if(count($IncomeType) > 0){
                for($i=0; $i<count($IncomeType); $i++){
                    if(empty($IncomeAmt[$i]) || $IncomeAmt[$i] == 0){
                        continue;
                    }
                    $income_entry = [
                        "PlantID" => $PlantID,
                        "FY" => $FY,
                        "Transdate" => $Transdate,
                        "UserID" => $_SESSION["username"],
                        "Inv_No" => $Inv_No,
                        "LedgerCategory" => 'Direct Income',
                        "LedgerType" => $IncomeType[$i],
                        "Amount" => $IncomeAmt[$i],
                    ];
                    $this->db->insert(db_prefix() . "K1PurchaseMasterExpenses", $income_entry);
                }
            }
            if ($traderlist->state == "") {
                $state_result = [
                    "state" => $State,
                ];
                $this->db->where("AccountID", $VendorID);
                $this->db->update(db_prefix() . "clients", $state_result);
            }
            //Ledger entry code
            $ord_n = 1;
            $narrations = "By Purchase no." . $Inv_No;
            // Credit to Vendor
            $ledger_credit = [
                "PlantID" => $PlantID,
                "FY" => $FY,
                "Transdate" => $Transdate,
                "TransDate2" => date("Y-m-d H:i:s"),
                "VoucherID" => $Inv_No,
                "PartyID" => "KASPL",
                "AccountID" => $VendorID,
                "CounterAccount" => "PURCH",
                "CenterID" => $CenterID,
                "EntryFor" => "2",
                "TType" => "C",
                "Amount" => $invoiceamt,
                "Narration" => $narrations,
                "PassedFrom" => "PURCHASE",
                "OrdinalNo" => $ord_n,
                "UserID" => $_SESSION["username"],
            ];
            $this->db->insert(db_prefix() . "accountledger", $ledger_credit);
            $ord_n++;
            // Debit to Purchase Account
            $ledger_debit = [
                "PlantID" => $PlantID,
                "FY" => $FY,
                "Transdate" => $Transdate,
                "TransDate2" => date("Y-m-d H:i:s"),
                "VoucherID" => $Inv_No,
                "PartyID" => "KASPL",
                "AccountID" => "PURCH",
                "CounterAccount" => $VendorID,
                "CenterID" => $CenterID,
                "EntryFor" => "2",
                "TType" => "D",
                "Amount" => $PurchAmt,
                "Narration" => $narrations,
                "PassedFrom" => "PURCHASE",
                "OrdinalNo" => $ord_n,
                "UserID" => $_SESSION["username"],
            ];
            $this->db->insert(db_prefix() . "accountledger", $ledger_debit);
            $ord_n++;
            //Debit to Tax Account
            if ($cgstamt != 0.0 && $sgstamt != 0.0) {
                //CGST Tax Ledger Entry
                $Cgst_Ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "CGST",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $data["total_cgst_amt"],
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $CgstLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $Cgst_Ledger_entry
                );
                $ord_n++;
                //SGST Tax Ledger Entry
                $Sgst_Ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "SGST",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $data["total_sgst_amt"],
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $SgstLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $Sgst_Ledger_entry
                );
                $ord_n++;
            } elseif ($igstamt != 0.0) {
                //Igst Ledger Entry
                $Igst_Ledger_Entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "IGST",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $data["total_igst_amt"],
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $IgstLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $Igst_Ledger_Entry
                );
                $ord_n++;
            }
            $ord_n++;
            // expense ledger entry
            if(count($ExpenceType) > 0){
                for($i=0; $i<count($ExpenceType); $i++){
                    if(empty($ExpenceAmt[$i]) || $ExpenceAmt[$i] == 0){
                        continue;
                    }
                    $ExpenceLedgerEntry = [
                        "PlantID" => $PlantID,
                        "FY" => $FY,
                        "Transdate" => $Transdate,
                        "TransDate2" => date("Y-m-d H:i:s"),
                        "VoucherID" => $Inv_No,
                        "PartyID" => "KASPL",
                        "AccountID" => $ExpenceType[$i],
                        "CounterAccount" => $VendorID,
                        "CenterID" => $CenterID,
                        "EntryFor" => "2",
                        "TType" => "D",
                        "Amount" => $ExpenceAmt[$i],
                        "Narration" => $narrations,
                        "PassedFrom" => "PURCHASE",
                        "OrdinalNo" => $ord_n,
                        "UserID" => $_SESSION["username"],
                    ];
                    $ExpenseLedgerEntry = $this->db->insert(db_prefix() . "accountledger", $ExpenceLedgerEntry);
                    $ord_n++;
                }
            }
            // income ledger entry
            if(count($IncomeType) > 0){
                for($i=0; $i<count($IncomeType); $i++){
                    if(empty($IncomeAmt[$i]) || $IncomeAmt[$i] == 0){
                        continue;
                    }
                    $IncomeLedgerEntry = [
                        "PlantID" => $PlantID,
                        "FY" => $FY,
                        "Transdate" => $Transdate,
                        "TransDate2" => date("Y-m-d H:i:s"),
                        "VoucherID" => $Inv_No,
                        "PartyID" => "KASPL",
                        "AccountID" => $IncomeType[$i],
                        "CounterAccount" => $VendorID,
                        "CenterID" => $CenterID,
                        "EntryFor" => "2",
                        "TType" => "C",
                        "Amount" => $IncomeAmt[$i],
                        "Narration" => $narrations,
                        "PassedFrom" => "PURCHASE",
                        "OrdinalNo" => $ord_n,
                        "UserID" => $_SESSION["username"],
                    ];
                    $IncomeLedgerEntry = $this->db->insert(db_prefix() . "accountledger", $IncomeLedgerEntry);
                    $ord_n++;
                }
            }
            //Debit to Discount Ledger Entry
            if ($discountAMT > 0) {
                $disc_ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "DISC",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "C",
                    "Amount" => $discountAMT,
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $DiscountLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $disc_ledger_entry
                );
                $ord_n++;
            }
            //Debit to RoundAmt Ledger Entry
            if ($roundoffamt >= 0) {
                $roundledgerentry_debit = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "ROUNDOFF",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "C",
                    "Amount" => $roundoffamt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $Round_Debit_LedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $roundledgerentry_debit
                );
                $ord_n++;
            } else {
                $amt = abs($roundoffamt);
                $roundledgerentry_credit = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "ROUNDOFF",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $amt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $Round_credit_LedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $roundledgerentry_credit
                );
                $ord_n++;
            }
            //$nextPaymentnumber = get_option('next_payment_number_for_kirti');
            if ($PurchaseType == 1) {
                $ordinalno = 1;
                //Payment Voucher Debit Entry to Company
                $paymententry_Debit_tocompany = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $PaymentNo,
                    "PartyID" => "KASPL",
                    "bill_no" => $Inv_No,
                    "AccountID" => $VendorID,
                    "CounterAccount" => $Effecton,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $invoiceamt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PAYMENTS",
                    "OrdinalNo" => $ordinalno,
                    "UserID" => $_SESSION["username"],
                ];
                $DebitToCompany = $this->db->insert(
                    db_prefix() . "accountledger",
                    $paymententry_Debit_tocompany
                );
                $ordinalno++;
                //Payment Voucher Credit Entry to Vendor
                $paymententry_Credit_tovendor = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $PaymentNo,
                    "PartyID" => "KASPL",
                    "bill_no" => $Inv_No,
                    "AccountID" => $Effecton,
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "C",
                    "Amount" => $invoiceamt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PAYMENTS",
                    "OrdinalNo" => $ordinalno,
                    "UserID" => $_SESSION["username"],
                ];
                $CreditToVendor = $this->db->insert(
                    db_prefix() . "accountledger",
                    $paymententry_Credit_tovendor
                );
                $this->increment_next_number("next_payment_number_for_kirti");
            }
            return true;
        }
    }
    public function CreateInvoiceLedger($data)
    {
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            $header[] = "BatchNo";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $ItemID = $value[0];
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $PlantID = $this->session->userdata("root_company");
        $FY = $this->session->userdata("finacial_year");
        $VendorID       = $data["vendor"];
        $VendorDocNo    = $data["VendorDocNo"];
        $Inv_No         = $data["PurchID"];
        $State          = $data["state"];
        // $CenterState = $data["state"];
        $PurchaseType   = $data["purchasetype"];
        $PaymentMode    = $data["paymode"];
        $PaymentMethod  = $data["paymentmethod"];
        $Refno          = $data["referenceno"];
        $Effecton       = $data["Effecton"];
        $ExpenceType    = $data["expense_type"];
        $ExpenceAmt     = $data["expense_amt"];
        $IncomeType     = $data["income_type"];
        $IncomeAmt      = $data["income_amt"];
        $PurchAmt       = $data["total_amt_in_mt"];
        $discountAMT    = $data["total_disc_in_mt"];
        $cgstamt        = $data["total_cgst_amt"];
        $sgstamt        = $data["total_sgst_amt"];
        $igstamt        = $data["total_igst_amt"];
        $roundoffamt    = $data["total_roundoff_amt"];
        $invoiceamt     = $data["netpayableamt"];
        
        $InvData = $this->GetPurchaseInvoiceDetails($Inv_No);
        $CenterID = $InvData->CenterID;
        $Transdate = substr($InvData->Inv_date, 0, 19);
        $ItCount = count($es_detail);
        // $nextPaymentnumber = get_option("next_payment_number_for_kirti");
        if ($PurchaseType == 2) {
            $PaymentMode    = null;
            $Effecton       = null;
            $PaymentMethod  = null;
            $Refno          = null;
        }
        if ($PurchaseType == 1) {
            $PaymentNo = $this->generateNextVoucherIDNew($Transdate, $selected_company, 'PAYMENTS');
        } else {
            $PaymentNo = null;
        }

        // rate update code new ============
        foreach ($es_detail as $value) {
            $productId      = $value["ItemID"];
            $brand          = $value["Brand"];
            $unit           = $value["MeasuredIn"];
            $packing_qty    = $value["PackingQty"]; // 12
            $packing_weight = $value["PackingWeight"];
            $saleunit       = $value["PurchaseUnit"];
            $qty            = $value["Qty"]; // 100
            $PurchRate      = $value["PurchRate"]; // 1200
            $gst            = $value["GST"]; // 5

            $PurchRate  = $value["PurchRate"] / $value["PackingQty"]; // 1200 / 12 = 100
            $salerate   = ($PurchRate + ($PurchRate * ($gst / 100))); // 100 + (100 * (5 / 100)) = 105
            $ItemQty = $packing_qty * $qty; // 12 * 100 = 1200
            $ItemAmt = $ItemQty * $PurchRate; // 1200 * 100 = 120000
            $ItemDisc = 0;
            $DiscPer = 0;
            $UnitDisc = 0;
            if ($value["Discount"] > 0 && $ItemQty > 0) {
                $UnitDisc = $value["Discount"]; // 5
                $ItemDisc = $value["Discount"] * $ItemQty; // 5 * 1200 = 6000
                $DiscPer = ($value["Discount"] / $PurchRate) * 100; // (5 / 100) * 100 = 5
            }
            $ItemTaxableAmt = $ItemAmt - $ItemDisc; // 120000 - 6000 = 114000
            $ItemGSTAmt = $ItemTaxableAmt * ($gst / 100); // 114000 * (5 / 100) = 5700
            $CGSTPer = null;
            $SGSTPer = null;
            $IGSTPer = null;
            $CGSTAmt = 0;
            $SGSTAmt = 0;
            $IGSTAmt = 0;
            if ($CenterState == $State) {
                $CGSTPer = $gst / 2;
                $SGSTPer = $gst / 2;
                $CGSTAmt = $ItemGSTAmt / 2;
                $SGSTAmt = $ItemGSTAmt / 2;
            } else {
                $IGSTPer = $gst;
                $IGSTAmt = $ItemGSTAmt;
            }
            $ItemNetAmt = $ItemTaxableAmt + $ItemGSTAmt;
            $TotalPurchAmt += $ItemAmt;
            $TotalDISCAmt += $ItemDisc;
            $TotalCGSTAmt += $CGSTAmt;
            $TotalSGSTAmt += $SGSTAmt;
            $TotalIGSTAmt += $IGSTAmt;
            $TotalNetAmt += $ItemNetAmt;

            $data_array_result = [
                "PurchRate" => $PurchRate,
                "SaleRate" => $salerate,
                "BasicRate" => $PurchRate,
                "DiscPerc" => $DiscPer,
                "DiscAmt" => $UnitDisc,
                "cgst" => $CGSTPer,
                "cgstamt" => $CGSTAmt,
                "sgst" => $SGSTPer,
                "sgstamt" => $SGSTAmt,
                "igst" => $IGSTPer,
                "igstamt" => $IGSTAmt,
                "OrderAmt" => $ItemAmt,
                "ChallanAmt" => $ItemAmt,
                "NetOrderAmt" => $ItemNetAmt,
                "NetChallanAmt" => $ItemNetAmt,
                "UserID2" => $_SESSION["username"],
                "Lupdate" => date("Y-m-d H:i:s"),
            ];
            
            $this->db->where([
                "TransID" => $Inv_No,
                "OrderQty" => $ItemQty,
                "ItemID" => $productId,
                "AccountID" => $VendorID,
                "BatchNo" => $value["BatchNo"]
            ]);
            $this->db->update(db_prefix() . "K1history", $data_array_result);
        }
        // rate update code new ============
        
        $this->db->select("tblclients.*");
        $this->db->from(db_prefix() . "clients");
        $this->db->where(db_prefix() . "clients.AccountID", $VendorID);
        $traderlist = $this->db->get()->row();
        
        $roundedTotal = round($TotalNetAmt + $OtherAmt);
        $roundOffAmt = $roundedTotal - ($TotalNetAmt + $OtherAmt);
        $KirtiOnePurchMaster = [
            "Is_Ledger" => "Y",
            "OrderStatus" => "F",
            "InvoiceNo" => $VendorDocNo,
            "PaymentNo" => $PaymentNo,
            "PurchaseType" => $PurchaseType,
            "PaymentMode" => $PaymentMode,
            "PaymentMethod" => $PaymentMethod,
            "RefNo" => $Refno,
            "EffectOn" => $Effecton,
            "Purchamt"  => $TotalPurchAmt,
            "Discamt"   => $TotalDISCAmt,
            "cgstamt"   => $TotalCGSTAmt,
            "sgstamt"   => $TotalSGSTAmt,
            "igstamt"   => $TotalIGSTAmt,
            "RoundOffAmt" => $roundOffAmt,
            "Invamt"    => $roundedTotal,
            "UserID2" => $_SESSION["username"],
            "Lupdate" => date("Y-m-d H:i:s"),
        ];
        $this->db->where("Inv_No", $Inv_No);
        $this->db->update(
            db_prefix() . "K1purchasemaster",
            $KirtiOnePurchMaster
        );
        if ($this->db->affected_rows() > 0) {
            // save expense entry
            if(count($ExpenceType) > 0){
                for($i=0; $i<count($ExpenceType); $i++){
                    if(empty($ExpenceAmt[$i]) || $ExpenceAmt[$i] == 0){
                        continue;
                    }
                    $expence_entry = [
                        "PlantID" => $PlantID,
                        "FY" => $FY,
                        "Transdate" => $Transdate,
                        "UserID" => $_SESSION["username"],
                        "Inv_No" => $Inv_No,
                        "LedgerCategory" => 'Direct Expense',
                        "LedgerType" => $ExpenceType[$i],
                        "Amount" => $ExpenceAmt[$i],
                    ];
                    $this->db->insert(db_prefix() . "K1PurchaseMasterExpenses", $expence_entry);
                }
            }
            // save income entry
            if(count($IncomeType) > 0){
                for($i=0; $i<count($IncomeType); $i++){
                    if(empty($IncomeAmt[$i]) || $IncomeAmt[$i] == 0){
                        continue;
                    }
                    $income_entry = [
                        "PlantID" => $PlantID,
                        "FY" => $FY,
                        "Transdate" => $Transdate,
                        "UserID" => $_SESSION["username"],
                        "Inv_No" => $Inv_No,
                        "LedgerCategory" => 'Direct Income',
                        "LedgerType" => $IncomeType[$i],
                        "Amount" => $IncomeAmt[$i],
                    ];
                    $this->db->insert(db_prefix() . "K1PurchaseMasterExpenses", $income_entry);
                }
            }
            if ($traderlist->state == "") {
                $state_result = [
                    "state" => $State,
                ];
                $this->db->where("AccountID", $VendorID);
                $this->db->update(db_prefix() . "clients", $state_result);
            }
            //Ledger entry code
            $ord_n = 1;
            $narrations = "By Purchase no." . $Inv_No;
            // Credit to Vendor
            $ledger_credit = [
                "PlantID" => $PlantID,
                "FY" => $FY,
                "Transdate" => $Transdate,
                "TransDate2" => date("Y-m-d H:i:s"),
                "VoucherID" => $Inv_No,
                "PartyID" => "KASPL",
                "AccountID" => $VendorID,
                "CounterAccount" => "PURCH",
                "CenterID" => $CenterID,
                "EntryFor" => "2",
                "TType" => "C",
                "Amount" => $invoiceamt,
                "Narration" => $narrations,
                "PassedFrom" => "PURCHASE",
                "OrdinalNo" => $ord_n,
                "UserID" => $_SESSION["username"],
            ];
            $this->db->insert(db_prefix() . "accountledger", $ledger_credit);
            $ord_n++;
            // Debit to Purchase Account
            $ledger_debit = [
                "PlantID" => $PlantID,
                "FY" => $FY,
                "Transdate" => $Transdate,
                "TransDate2" => date("Y-m-d H:i:s"),
                "VoucherID" => $Inv_No,
                "PartyID" => "KASPL",
                "AccountID" => "PURCH",
                "CounterAccount" => $VendorID,
                "CenterID" => $CenterID,
                "EntryFor" => "2",
                "TType" => "D",
                "Amount" => $PurchAmt,
                "Narration" => $narrations,
                "PassedFrom" => "PURCHASE",
                "OrdinalNo" => $ord_n,
                "UserID" => $_SESSION["username"],
            ];
            $this->db->insert(db_prefix() . "accountledger", $ledger_debit);
            $ord_n++;
            //Debit to Tax Account
            if ($cgstamt != 0.0 && $sgstamt != 0.0) {
                //CGST Tax Ledger Entry
                $Cgst_Ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "CGST",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $data["total_cgst_amt"],
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $CgstLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $Cgst_Ledger_entry
                );
                $ord_n++;
                //SGST Tax Ledger Entry
                $Sgst_Ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "SGST",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $data["total_sgst_amt"],
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $SgstLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $Sgst_Ledger_entry
                );
                $ord_n++;
            } elseif ($igstamt != 0.0) {
                //Igst Ledger Entry
                $Igst_Ledger_Entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "IGST",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $data["total_igst_amt"],
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $IgstLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $Igst_Ledger_Entry
                );
                $ord_n++;
            }
            $ord_n++;
            // expense ledger entry
            if(count($ExpenceType) > 0){
                for($i=0; $i<count($ExpenceType); $i++){
                    if(empty($ExpenceAmt[$i]) || $ExpenceAmt[$i] == 0){
                        continue;
                    }
                    $ExpenceLedgerEntry = [
                        "PlantID" => $PlantID,
                        "FY" => $FY,
                        "Transdate" => $Transdate,
                        "TransDate2" => date("Y-m-d H:i:s"),
                        "VoucherID" => $Inv_No,
                        "PartyID" => "KASPL",
                        "AccountID" => $ExpenceType[$i],
                        "CounterAccount" => $VendorID,
                        "CenterID" => $CenterID,
                        "EntryFor" => "2",
                        "TType" => "D",
                        "Amount" => $ExpenceAmt[$i],
                        "Narration" => $narrations,
                        "PassedFrom" => "PURCHASE",
                        "OrdinalNo" => $ord_n,
                        "UserID" => $_SESSION["username"],
                    ];
                    $ExpenseLedgerEntry = $this->db->insert(db_prefix() . "accountledger", $ExpenceLedgerEntry);
                    $ord_n++;
                }
            }
            // income ledger entry
            if(count($IncomeType) > 0){
                for($i=0; $i<count($IncomeType); $i++){
                    if(empty($IncomeAmt[$i]) || $IncomeAmt[$i] == 0){
                        continue;
                    }
                    $IncomeLedgerEntry = [
                        "PlantID" => $PlantID,
                        "FY" => $FY,
                        "Transdate" => $Transdate,
                        "TransDate2" => date("Y-m-d H:i:s"),
                        "VoucherID" => $Inv_No,
                        "PartyID" => "KASPL",
                        "AccountID" => $IncomeType[$i],
                        "CounterAccount" => $VendorID,
                        "CenterID" => $CenterID,
                        "EntryFor" => "2",
                        "TType" => "C",
                        "Amount" => $IncomeAmt[$i],
                        "Narration" => $narrations,
                        "PassedFrom" => "PURCHASE",
                        "OrdinalNo" => $ord_n,
                        "UserID" => $_SESSION["username"],
                    ];
                    $IncomeLedgerEntry = $this->db->insert(db_prefix() . "accountledger", $IncomeLedgerEntry);
                    $ord_n++;
                }
            }
            //Debit to Discount Ledger Entry
            if ($discountAMT > 0) {
                $disc_ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "DISC",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "C",
                    "Amount" => $discountAMT,
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $DiscountLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $disc_ledger_entry
                );
                $ord_n++;
            }
            //Debit to RoundAmt Ledger Entry
            if ($roundoffamt >= 0) {
                $roundledgerentry_debit = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "ROUNDOFF",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "C",
                    "Amount" => $roundoffamt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $Round_Debit_LedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $roundledgerentry_debit
                );
                $ord_n++;
            } else {
                $amt = abs($roundoffamt);
                $roundledgerentry_credit = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "ROUNDOFF",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $amt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $Round_credit_LedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $roundledgerentry_credit
                );
                $ord_n++;
            }
            //$nextPaymentnumber = get_option('next_payment_number_for_kirti');
            if ($PurchaseType == 1) {
                $ordinalno = 1;
                //Payment Voucher Debit Entry to Company
                $paymententry_Debit_tocompany = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $PaymentNo,
                    "PartyID" => "KASPL",
                    "bill_no" => $Inv_No,
                    "AccountID" => $VendorID,
                    "CounterAccount" => $Effecton,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $invoiceamt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PAYMENTS",
                    "OrdinalNo" => $ordinalno,
                    "UserID" => $_SESSION["username"],
                ];
                $DebitToCompany = $this->db->insert(
                    db_prefix() . "accountledger",
                    $paymententry_Debit_tocompany
                );
                $ordinalno++;
                //Payment Voucher Credit Entry to Vendor
                $paymententry_Credit_tovendor = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $PaymentNo,
                    "PartyID" => "KASPL",
                    "bill_no" => $Inv_No,
                    "AccountID" => $Effecton,
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "C",
                    "Amount" => $invoiceamt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PAYMENTS",
                    "OrdinalNo" => $ordinalno,
                    "UserID" => $_SESSION["username"],
                ];
                $CreditToVendor = $this->db->insert(
                    db_prefix() . "accountledger",
                    $paymententry_Credit_tovendor
                );
                $this->increment_next_number("next_payment_number_for_kirti");
            }
            return true;
        }
    }
    
    //=================== Add Kirti One Purchase Order =============================
    public function AddKirtiOneInward($data)
    {
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            $header[] = "BatchNo";
            $header[] = "ExpDate";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $ItemID = $value[0];
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $PlantID = $this->session->userdata("root_company");
        $FY = $this->session->userdata("finacial_year");
        $VendorID = $data["vendor"];
        $CenterID = $data["centername"];
        $prefix = "INV";
        $purchase_orderNumbar = get_option("next_inward_number_for_kirtione");
        $new_purchase_orderNumbar = $prefix . $FY . "1" . $purchase_orderNumbar;
        $Transdate = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $PurchAmt = $data["total_amt_in_mt"];
        $discountAMT = $data["total_disc_in_mt"];
        $cgstamt = $data["total_cgst_amt"];
        $sgstamt = $data["total_sgst_amt"];
        $igstamt = $data["total_igst_amt"];
        $Frtamt = $data["Freight_AMT"];
        $Othamt = $data["Other_amt"];
        $roundoffamt = $data["total_roundoff_amt"];
        $invoiceamt = $data["netpayableamt"];
        $ItCount = count($es_detail);
        $State = $data["state"];
        $InvNo = $data["Invno"];
        $InvDate = to_sql_date($data["inv_date"]) . " " . date("H:i:s");
        $Drivername = $data["drivername"];
        $DriverNo = $data["driverno"];
        $vehicleNo = $data["VehicleNo"];
        $EwayBill = $data["ewaybillno"];
        //$EntryType = $data['entrytype'];
        $this->db->select("tblclients.*");
        $this->db->from(db_prefix() . "clients");
        $this->db->where(db_prefix() . "clients.AccountID", $VendorID);
        $traderlist = $this->db->get()->row();
        // echo $EntryType;die;
        $TType = "I";
        $TType2 = "INWARD";
        if ($PurchAmt != 0) {
            $KirtiOneInwardMaster = [
                "PlantID" => $PlantID,
                "FY" => $FY,
                "PurchID" => $new_purchase_orderNumbar,
                "Inv_No" => $InvNo,
                "Inv_date" => $InvDate,
                "EwayBillNo" => $EwayBill,
                "VehicleNo" => $vehicleNo,
                "DriverName" => $Drivername,
                "DriverNo" => $DriverNo,
                "Transdate" => $Transdate,
                "OrderStatus" => "F",
                "PartyID" => "KASPL",
                "CenterID" => $CenterID,
                "AccountID" => $VendorID,
                "Purchamt" => $PurchAmt,
                "Discamt" => $discountAMT,
                "cgstamt" => $cgstamt,
                "sgstamt" => $sgstamt,
                "igstamt" => $igstamt,
                "Frtamt" => $Frtamt,
                "Othamt" => $Othamt,
                "RoundOffAmt" => $roundoffamt,
                "Invamt" => $invoiceamt,
                "ItCount" => $ItCount,
                "Userid" => $_SESSION["username"],
            ];
            $this->db->insert(
                db_prefix() . "K1Inwardmaster",
                $KirtiOneInwardMaster
            );
            if ($this->db->affected_rows() > 0) {
                //$insert_id = $this->db->insert_id();
                $this->increment_next_number("next_inward_number_for_kirtione");
                if ($traderlist->state == "") {
                    $state_result = [
                        "state" => $State,
                    ];
                    $this->db->where("AccountID", $VendorID);
                    $this->db->update(db_prefix() . "clients", $state_result);
                }
                $i = 1;
                foreach ($es_detail as $value) {
                    $productId = $value["ItemID"];
                    $brand = $value["Brand"];
                    $unit = $value["MeasuredIn"];
                    $packing_qty = $value["PackingQty"];
                    $packing_weight = $value["PackingWeight"];
                    $saleunit = $value["PurchaseUnit"];
                    $qty = $value["Qty"];
                    $amount = $value["PurchRate"];
                    $discount = $value["Discount"];
                    $gst = $value["GST"];
                    $cgstamt = $value["CGSTAMT"];
                    $sgstamt = $value["SGSTAMT"];
                    $igstamt = $value["IGSTAMT"];
                    $netAmount = $value["total_money"];
                    $batchno = $value["BatchNo"];
                    $expdate = to_sql_date($value["ExpDate"]);
                    if ($saleunit == $unit) {
                        $orderquantity = $packing_qty * $qty;
                        $totalAmount = $qty * $amount;
                    } else {
                        $orderquantity = $qty;
                        $amountval = ($amount / $packing_qty) * $qty;
                        $totalAmount = $amountval;
                    }
                    $discountAmount = ($discount / 100) * $totalAmount;
                    $finalOrderAmt = $totalAmount - $discountAmount;
                    if ($gst != "") {
                        if ($cgstamt > 0 && $sgstamt > 0) {
                            $cgst = $cgstamt;
                            $sgst = $sgstamt;
                            $cgstPercentage = ($cgst / $finalOrderAmt) * 100;
                            $sgstPercentage = $cgstPercentage;
                            $totalPercentage =
                                $cgstPercentage + $sgstPercentage;
                            $salerate = $amount * (1 + $totalPercentage / 100);
                            $igst = 0;
                            $igstPercentage = 0;
                        } elseif ($igstamt > 0) {
                            $igst = $igstamt;
                            $igstPercentage = ($igst / $finalOrderAmt) * 100;
                            $salerate = $amount * (1 + $igstPercentage / 100);
                        }
                    }
                    if ($saleunit == "Loose") {
                        $caseqty = 1;
                    } else {
                        $caseqty = $packing_qty;
                    }
                    $data_array_result = [
                        "PlantID" => $PlantID,
                        "FY" => $FY,
                        "OrderID" => $new_purchase_orderNumbar,
                        "BillID" => $new_purchase_orderNumbar,
                        "TransID" => $new_purchase_orderNumbar,
                        "TransDate" => $Transdate,
                        "TransDate2" => date("Y-m-d H:i:s"),
                        "TType" => $TType,
                        "TType2" => $TType2,
                        "AccountID" => $data["vendor"],
                        "ItemID" => $productId,
                        "CenterID" => $CenterID,
                        "PartyID" => "KASPL",
                        "PurchRate" => $amount,
                        "SaleRate" => $salerate,
                        "BasicRate" => $amount,
                        "SuppliedIn" => $saleunit,
                        "OrderQty" => $orderquantity,
                        "BilledQty" => $orderquantity,
                        "DiscPerc" => $discount,
                        "DiscAmt" => $discountAmount,
                        "cgst" => $cgstPercentage,
                        "cgstamt" => $cgst,
                        "sgst" => $sgstPercentage,
                        "sgstamt" => $sgst,
                        "igst" => $igstPercentage,
                        "igstamt" => $igst,
                        "CaseQty" => $caseqty,
                        "Cases" => 0.0,
                        "OrderAmt" => $totalAmount,
                        "ChallanAmt" => $totalAmount,
                        "NetOrderAmt" => $netAmount,
                        "NetChallanAmt" => $netAmount,
                        "Ordinalno" => $i,
                        "rowid" => "",
                        "UserID" => $_SESSION["username"],
                        "cnfid" => "",
                        "BatchNo" => $batchno,
                        "ExpDate" => $expdate,
                    ];
                    $this->db->insert(
                        db_prefix() . "K1history",
                        $data_array_result
                    );
                    $i++;
                }
                return true;
            }
        }
    }
    public function UpdateKirtiOnePurchaseRequest($data, $id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $PurchID = $id;
        $AccountID = $data["vendor"];
        $CenterID = $data["centername"];
        $this->db->select("tblCenterMaster.*");
        $this->db->from(db_prefix() . "CenterMaster");
        $this->db->where(db_prefix() . "CenterMaster.CenterID", $CenterID);
        $CenterDetails = $this->db->get()->row();
        $CenterState = $CenterDetails->state;
        $State = $data["state"];
        $new_date = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $purchAmt = $data["total_amt_in_mt"];
        $Discamt = $data["total_disc_in_mt"];
        $cgstamt = $data["total_cgst_amt"];
        $sgstamt = $data["total_sgst_amt"];
        $igstamt = $data["total_igst_amt"];
        $RoundOffAmt = $data["total_roundoff_amt"];
        $Invamt = $data["netpayableamt"];
        $ItCount = count($es_detail);
        $this->db->select("tblK1purchase_request_master.*");
        $this->db->from(db_prefix() . "K1purchase_request_master");
        $this->db->where(
            db_prefix() . "K1purchase_request_master.PurchID",
            $PurchID
        );
        $purchaselist = $this->db->get()->row();
        $data_array = [
            "Transdate" => $new_date,
            "CenterID" => $CenterID,
            "AccountID" => $AccountID,
            "Purchamt" => $purchAmt,
            "Discamt" => $Discamt,
            "cgstamt" => $cgstamt,
            "sgstamt" => $sgstamt,
            "igstamt" => $igstamt,
            "RoundOffAmt" => $RoundOffAmt,
            "Invamt" => $Invamt,
            "ItCount" => $ItCount,
            "Lupdate" => date("Y-m-d H:i:s"),
            "UserID2" => $this->session->userdata("username"),
        ];
        $this->db->where("PlantID", $selected_company);
        $this->db->LIKE("FY", $fy);
        $this->db->where("PurchID", $PurchID);
        $this->db->update(
            db_prefix() . "K1purchase_request_master",
            $data_array
        );
        if ($this->db->affected_rows() > 0) {
            $old_pur_details = $this->PurchaseModel->get_purchase_request_detail(
                $PurchID
            );
            // Move record from tblK1history to tblK1history_audit
            $TType = "";
            $TType2 = "";
            foreach ($old_pur_details as $key => $value) {
                $TType = $value["TType"];
                $TType2 = $value["TType2"];
                if ($value["igst"] == null) {
                    $value["igst"] = "";
                    $value["igstamt"] = "";
                } elseif ($value["cgst"] == null) {
                    $value["cgst"] = "";
                    $value["cgstamt"] = "";
                    $value["sgst"] = "";
                    $value["sgstamt"] = "";
                }
                $old_data = [
                    "PlantID" => $value["PlantID"],
                    "FY" => $value["FY"],
                    "OrderID" => $value["OrderID"],
                    "BillID" => $value["BillID"],
                    "TransID" => $value["TransID"],
                    "TransDate" => $value["TransDate"],
                    "TransDate2" => $value["TransDate2"],
                    "TType" => $value["TType"],
                    "TType2" => $value["TType2"],
                    "AccountID" => $value["AccountID"],
                    "ItemID" => $value["ItemID"],
                    "CenterID" => $value["CenterID"],
                    "GodownID" => $value["GodownID"],
                    "PartyID" => $value["PartyID"],
                    "PurchRate" => $value["PurchRate"],
                    "SaleRate" => $value["SaleRate"],
                    "BasicRate" => $value["BasicRate"],
                    "SuppliedIn" => $value["SuppliedIn"],
                    "OrderQty" => $value["OrderQty"],
                    "eOrderQty" => $value["eOrderQty"],
                    "BilledQty" => $value["BilledQty"],
                    "DiscPerc" => $value["DiscPerc"],
                    "DiscAmt" => $value["DiscAmt"],
                    "cgst" => $value["cgst"],
                    "cgstamt" => $value["cgstamt"],
                    "sgst" => $value["sgst"],
                    "sgstamt" => $value["sgstamt"],
                    "igst" => $value["igst"],
                    "igstamt" => $value["igstamt"],
                    "CaseQty" => $value["CaseQty"],
                    "Cases" => $value["Cases"],
                    "OrderAmt" => $value["OrderAmt"],
                    "ChallanAmt" => $value["ChallanAmt"],
                    "NetOrderAmt" => $value["NetOrderAmt"],
                    "NetChallanAmt" => $value["NetChallanAmt"],
                    "Ordinalno" => $value["Ordinalno"],
                    "UserID" => $value["UserID"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                    "UserID2" => $_SESSION["username"],
                ];
                $this->db->insert(db_prefix() . "K1history_audit", $old_data);
            }
            // Delete Live history table record
            $this->db->where("PlantID", $selected_company);
            $this->db->where("FY", $fy);
            $this->db->where("OrderID", $PurchID);
            $this->db->delete(db_prefix() . "K1history");
            // Add New history detail record
            $i = 1;
            foreach ($es_detail as $value) {
                $productId = $value["ItemID"];
                $brand = $value["Brand"];
                $unit = $value["MeasuredIn"];
                $packing_qty = $value["PackingQty"];
                $packing_weight = $value["PackingWeight"];
                $saleunit = $value["PurchaseUnit"];
                $qty = $value["Qty"];
                $amount = $value["PurchRate"];
                $discountAmount = $value["Discount"] * $qty;
                $gst = $value["GST"];
                if ($saleunit == $unit) {
                    $orderquantity = $packing_qty * $qty;
                    $totalAmount = $qty * $amount;
                } else {
                    $orderquantity = $qty;
                    $amountval = ($amount / $packing_qty) * $qty;
                    $totalAmount = $amountval;
                }
                $discount = 0;
                if ($discountAmount > 0) {
                    $discount = ($discountAmount / $totalAmount) * 100;
                    $finalOrderAmt = $totalAmount - $discountAmount;
                }
                $CGSTPer = null;
                $SGSTPer = null;
                $IGSTPer = null;
                $CGSTAmt = 0;
                $SGSTAmt = 0;
                $IGSTAmt = 0;
                $GSTAmt = $finalOrderAmt * ($gst / 100);
                if ($CenterState == $State) {
                    $CGSTPer = $gst / 2;
                    $SGSTPer = $gst / 2;
                    $CGSTAmt = $GSTAmt / 2;
                    $SGSTAmt = $GSTAmt / 2;
                } else {
                    $IGSTPer = $gst;
                    $IGSTAmt = $GSTAmt;
                }
                $netAmount = $finalOrderAmt + $GSTAmt;
                if ($saleunit == "Loose") {
                    $caseqty = 1;
                } else {
                    $caseqty = $packing_qty;
                }
                $data_array_result = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "OrderID" => $PurchID,
                    "BillID" => $PurchID,
                    "TransID" => $PurchID,
                    "TransDate" => $new_date,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "TType" => $TType,
                    "TType2" => $TType2,
                    "AccountID" => $AccountID,
                    "ItemID" => $productId,
                    "CenterID" => $CenterID,
                    "PartyID" => "KASPL",
                    "PurchRate" => $amount,
                    "SaleRate" => $salerate,
                    "BasicRate" => $amount,
                    "SuppliedIn" => $saleunit,
                    "OrderQty" => $orderquantity,
                    "BilledQty" => $orderquantity,
                    "DiscPerc" => $discount,
                    "DiscAmt" => $discountAmount,
                    "cgst" => $CGSTPer,
                    "cgstamt" => $CGSTAmt,
                    "sgst" => $SGSTPer,
                    "sgstamt" => $SGSTAmt,
                    "igst" => $IGSTPer,
                    "igstamt" => $IGSTAmt,
                    "CaseQty" => $caseqty,
                    "Cases" => 0.0,
                    "OrderAmt" => $totalAmount,
                    "ChallanAmt" => $totalAmount,
                    "NetOrderAmt" => $netAmount,
                    "NetChallanAmt" => $netAmount,
                    "Ordinalno" => $i,
                    "UserID" => $_SESSION["username"],
                ];
                $this->db->insert(
                    db_prefix() . "K1history",
                    $data_array_result
                );
                $i++;
            }
        }
        return true;
    }
    public function UpdateKirtiOnePurchaseOrder($data, $id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "PRQty";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $PurchID = $id;
        $AccountID = $data["vendor"];
        $Pr_no = null;
        if ($data["Pr_no"]) {
            $Pr_no = $data["Pr_no"];
            //$Pr_data = $this->GetPurchaseRequestDetails($Pr_no);
            //$CenterID = $Pr_data->CenterID;
        } else {
            $Pr_no = $PurchID;
        }
        $CenterID = $data["CenterName"];
        $this->db->select("tblCenterMaster.*");
        $this->db->from(db_prefix() . "CenterMaster");
        $this->db->where(db_prefix() . "CenterMaster.CenterID", $CenterID);
        $CenterDetails = $this->db->get()->row();
        $CenterState = $CenterDetails->state;
        $State = $data["state"];
        $new_date = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $PurchAmt = $data["total_amt_in_mt"];
        $discountAMT = $data["total_disc_in_mt"];
        $cgstamt = $data["total_cgst_amt"];
        $sgstamt = $data["total_sgst_amt"];
        $igstamt = $data["total_igst_amt"];
        $roundoffamt = $data["total_roundoff_amt"];
        $invoiceamt = $data["netpayableamt"];
        $VendorDocNo = $data["VendorDocNo"];
        $ItCount = count($es_detail);
        $reminderDate = !empty($data["reminder_date"])
            ? to_sql_date($data["reminder_date"])
            : null;
        $reminderRemark = !empty($data["reminder_remark"])
            ? trim($data["reminder_remark"])
            : null;
        unset($data["reminder_date"], $data["reminder_remark"]);
        $this->db->select("tblK1purchasemaster.*");
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->where(db_prefix() . "K1purchasemaster.PurchID", $PurchID);
        $purchaselist = $this->db->get()->row();
        $data_array = [
            "Transdate" => $new_date,
            "Pr_no" => $Pr_no,
            "CenterID" => $CenterID,
            "InvoiceNo" => $VendorDocNo,
            "AccountID" => $AccountID,
            "Lupdate" => date("Y-m-d H:i:s"),
            "UserID2" => $this->session->userdata("username"),
            "ReminderDate" => $reminderDate,
            "ReminderRemark" => $reminderRemark,
        ];
        if (
            $reminderDate !== $purchaselist->ReminderDate ||
            $reminderRemark !== $purchaselist->ReminderRemark
        ) {
            $data_array["ReminderSent"] = 0;
        }
        $this->db->where("PlantID", $selected_company);
        $this->db->LIKE("FY", $fy);
        $this->db->where("PurchID", $PurchID);
        $this->db->update(db_prefix() . "K1purchasemaster", $data_array);
        if ($this->db->affected_rows() > 0) {
            $old_pur_details = $this->get_purchase_order_detail($PurchID);
            // Move record from tblK1history to tblK1history_audit
            $TType = "";
            $TType2 = "";
            foreach ($old_pur_details as $key => $value) {
                $TType = $value["TType"];
                $TType2 = $value["TType2"];
                if ($value["igst"] == null) {
                    $value["igst"] = "";
                    $value["igstamt"] = "";
                } elseif ($value["cgst"] == null) {
                    $value["cgst"] = "";
                    $value["cgstamt"] = "";
                    $value["sgst"] = "";
                    $value["sgstamt"] = "";
                }
                $old_data = [
                    "PlantID" => $value["PlantID"],
                    "FY" => $value["FY"],
                    "OrderID" => $value["OrderID"],
                    "BillID" => $value["BillID"],
                    "TransID" => $value["TransID"],
                    "TransDate" => $value["TransDate"],
                    "TransDate2" => $value["TransDate2"],
                    "TType" => $value["TType"],
                    "TType2" => $value["TType2"],
                    "AccountID" => $value["AccountID"],
                    "ItemID" => $value["ItemID"],
                    //'TypeID'=>$value["TypeID"],
                    "CenterID" => $value["CenterID"],
                    "GodownID" => $value["GodownID"],
                    "PartyID" => $value["PartyID"],
                    "PurchRate" => $value["PurchRate"],
                    "SaleRate" => $value["SaleRate"],
                    "BasicRate" => $value["BasicRate"],
                    "SuppliedIn" => $value["SuppliedIn"],
                    "OrderQty" => $value["OrderQty"],
                    "eOrderQty" => $value["eOrderQty"],
                    "BilledQty" => $value["BilledQty"],
                    "DiscPerc" => $value["DiscPerc"],
                    "DiscAmt" => $value["DiscAmt"],
                    "cgst" => $value["cgst"],
                    "cgstamt" => $value["cgstamt"],
                    "sgst" => $value["sgst"],
                    "sgstamt" => $value["sgstamt"],
                    "igst" => $value["igst"],
                    "igstamt" => $value["igstamt"],
                    "CaseQty" => $value["CaseQty"],
                    "Cases" => $value["Cases"],
                    "OrderAmt" => $value["OrderAmt"],
                    "ChallanAmt" => $value["ChallanAmt"],
                    "NetOrderAmt" => $value["NetOrderAmt"],
                    "NetChallanAmt" => $value["NetChallanAmt"],
                    "Ordinalno" => $value["Ordinalno"],
                    "UserID" => $value["UserID"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                    "UserID2" => $_SESSION["username"],
                ];
                $this->db->insert(db_prefix() . "K1history_audit", $old_data);
            }
            // Delete Live history table record
            $this->db->where("PlantID", $selected_company);
            $this->db->where("FY", $fy);
            $this->db->where("OrderID", $PurchID);
            $this->db->delete(db_prefix() . "K1history");
            // Add New history detail record
            //$Pr_item_data = $this->GetPurchaseRequestItemListInvoiceAdd($Pr_no);
            $i = 1;
            $TotalPurchAmt = 0;
            $TotalDISCAmt = 0;
            $TotalCGSTAmt = 0;
            $TotalSGSTAmt = 0;
            $TotalIGSTAmt = 0;
            $TotalNetAmt = 0;
            foreach ($es_detail as $value) {
                $productId = $value["ItemID"];
                $brand = $value["Brand"];
                $unit = $value["MeasuredIn"];
                $packing_qty = $value["PackingQty"];
                $packing_weight = $value["PackingWeight"];
                $saleunit = $value["PurchaseUnit"];
                $qty = $value["Qty"];
                $PurchRate = $value["PurchRate"];
                $gst = $value["GST"];
                $salerate = $PurchRate + $PurchRate * ($gst / 100);
                $ItemQty = $packing_qty * $qty;
                $ItemAmt = $qty * $PurchRate;
                $ItemDisc = 0;
                $DiscPer = 0;
                $UnitDisc = 0;
                if ($value["Discount"] > 0 && $qty > 0) {
                    $UnitDisc = $value["Discount"];
                    $ItemDisc = $value["Discount"] * $qty;
                    $DiscPer = ($value["Discount"] / $PurchRate) * 100;
                }
                $ItemTaxableAmt = $ItemAmt - $ItemDisc;
                $ItemGSTAmt = $ItemTaxableAmt * ($gst / 100);
                $CGSTPer = null;
                $SGSTPer = null;
                $IGSTPer = null;
                $CGSTAmt = 0;
                $SGSTAmt = 0;
                $IGSTAmt = 0;
                if ($CenterState == $State) {
                    $CGSTPer = $gst / 2;
                    $SGSTPer = $gst / 2;
                    $CGSTAmt = $ItemGSTAmt / 2;
                    $SGSTAmt = $ItemGSTAmt / 2;
                } else {
                    $IGSTPer = $gst;
                    $IGSTAmt = $ItemGSTAmt;
                }
                $ItemNetAmt = $ItemTaxableAmt + $ItemGSTAmt;
                $TotalPurchAmt += $ItemAmt;
                $TotalDISCAmt += $ItemDisc;
                $TotalCGSTAmt += $CGSTAmt;
                $TotalSGSTAmt += $SGSTAmt;
                $TotalIGSTAmt += $IGSTAmt;
                $TotalNetAmt += $ItemNetAmt;
                if ($saleunit == "Loose") {
                    $caseqty = 1;
                } else {
                    $caseqty = $packing_qty;
                }
                $data_array_result = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "OrderID" => $PurchID,
                    "BillID" => $Pr_no,
                    "TransID" => $Pr_no,
                    "TransDate" => $new_date,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "TType" => $TType,
                    "TType2" => $TType2,
                    "AccountID" => $AccountID,
                    "ItemID" => $productId,
                    "CenterID" => $CenterID,
                    "PartyID" => "KASPL",
                    "PurchRate" => $PurchRate,
                    "SaleRate" => $salerate,
                    "BasicRate" => $PurchRate,
                    "SuppliedIn" => $saleunit,
                    "OrderQty" => $ItemQty,
                    "BilledQty" => $ItemQty,
                    "DiscPerc" => $DiscPer,
                    "DiscAmt" => $UnitDisc,
                    "cgst" => $CGSTPer,
                    "cgstamt" => $CGSTAmt,
                    "sgst" => $SGSTPer,
                    "sgstamt" => $SGSTAmt,
                    "igst" => $IGSTPer,
                    "igstamt" => $IGSTAmt,
                    "CaseQty" => $caseqty,
                    "Cases" => 0.0,
                    "OrderAmt" => $ItemAmt,
                    "ChallanAmt" => $ItemAmt,
                    "NetOrderAmt" => $ItemNetAmt,
                    "NetChallanAmt" => $ItemNetAmt,
                    "Ordinalno" => $i,
                    "UserID" => $_SESSION["username"],
                ];
                $this->db->insert(
                    db_prefix() . "K1history",
                    $data_array_result
                );
                $i++;
            }
            $roundedTotal = round($TotalNetAmt);
            $roundOffAmt = $roundedTotal - $TotalNetAmt;
            $KirtiOnePurchMaster = [
                "Purchamt" => $TotalPurchAmt,
                "Discamt" => $TotalDISCAmt,
                "cgstamt" => $TotalCGSTAmt,
                "sgstamt" => $TotalSGSTAmt,
                "igstamt" => $TotalIGSTAmt,
                "RoundOffAmt" => $roundOffAmt,
                "Invamt" => $roundedTotal,
                "ItCount" => $i,
            ];
            $this->db->where("PurchID", $PurchID);
            $this->db->update(
                db_prefix() . "K1purchasemaster",
                $KirtiOnePurchMaster
            );
        }
        return true;
    }

    public function UpdatePurchaseOrder($data, $id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "PRQty";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }

        // Payload data
        $PurchID        = $id;
        $Pr_no          = $data["Pr_no"] ?? $PurchID;
        $CenterID       = $data["CenterName"];
        $AccountID      = $data["vendor"];
        $State          = $data["state"];
        $new_date       = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $PurchAmt       = $data["total_amt_in_mt"];
        $discountAMT    = $data["total_disc_in_mt"];
        $cgstamt        = $data["total_cgst_amt"];
        $sgstamt        = $data["total_sgst_amt"];
        $igstamt        = $data["total_igst_amt"];
        $roundoffamt    = $data["total_roundoff_amt"];
        $invoiceamt     = $data["netpayableamt"];
        $VendorDocNo    = $data["VendorDocNo"];
        $ItCount        = count($es_detail);
        $reminderDate   = !empty($data["reminder_date"]) ? to_sql_date($data["reminder_date"]) : null;
        $reminderRemark = !empty($data["reminder_remark"]) ? trim($data["reminder_remark"]) : null;
        unset($data["reminder_date"], $data["reminder_remark"]);

        // Get Center Details
        $this->db->select("tblCenterMaster.*");
        $this->db->from(db_prefix() . "CenterMaster");
        $this->db->where(db_prefix() . "CenterMaster.CenterID", $CenterID);
        $CenterDetails = $this->db->get()->row();
        $CenterState = $CenterDetails->state;

        // Get Purchase Order Details
        $this->db->select(db_prefix() . "K1PurchaseOrderMaster.*");
        $this->db->from(db_prefix() . "K1PurchaseOrderMaster");
        $this->db->where(db_prefix() . "K1PurchaseOrderMaster.PurchID", $PurchID);
        $purchaselist = $this->db->get()->row();

        $data_array = [
            "Transdate" => $new_date,
            "Pr_no" => $Pr_no,
            "CenterID" => $CenterID,
            "InvoiceNo" => $VendorDocNo,
            "AccountID" => $AccountID,
            "Lupdate" => date("Y-m-d H:i:s"),
            "UserID2" => $this->session->userdata("username"),
            "ReminderDate" => $reminderDate,
            "ReminderRemark" => $reminderRemark,
        ];
        if (
            $reminderDate !== $purchaselist->ReminderDate ||
            $reminderRemark !== $purchaselist->ReminderRemark
        ) {
            $data_array["ReminderSent"] = 0;
        }
        $this->db->where("PlantID", $selected_company);
        $this->db->LIKE("FY", $fy);
        $this->db->where("PurchID", $PurchID);
        $this->db->update(db_prefix() . "K1PurchaseOrderMaster", $data_array);
        if ($this->db->affected_rows() > 0) {
            $old_pur_details = $this->get_POH_detail($PurchID);
            // Move record from tblK1history to tblK1history_audit
            $TType = "";
            $TType2 = "";
            foreach ($old_pur_details as $key => $value) {
                $TType = $value["TType"];
                $TType2 = $value["TType2"];
                if ($value["igst"] == null) {
                    $value["igst"] = "";
                    $value["igstamt"] = "";
                } elseif ($value["cgst"] == null) {
                    $value["cgst"] = "";
                    $value["cgstamt"] = "";
                    $value["sgst"] = "";
                    $value["sgstamt"] = "";
                }
                $old_data = [
                    "PlantID" => $value["PlantID"],
                    "FY" => $value["FY"],
                    "OrderID" => $value["OrderID"],
                    "BillID" => $value["BillID"],
                    "TransID" => $value["TransID"],
                    "TransDate" => $value["TransDate"],
                    "TransDate2" => $value["TransDate2"],
                    "TType" => $value["TType"],
                    "TType2" => $value["TType2"],
                    "AccountID" => $value["AccountID"],
                    "ItemID" => $value["ItemID"],
                    //'TypeID'=>$value["TypeID"],
                    "CenterID" => $value["CenterID"],
                    "GodownID" => $value["GodownID"],
                    "PartyID" => $value["PartyID"],
                    "PurchRate" => $value["PurchRate"],
                    "SaleRate" => $value["SaleRate"],
                    "BasicRate" => $value["BasicRate"],
                    "SuppliedIn" => $value["SuppliedIn"],
                    "OrderQty" => $value["OrderQty"],
                    "eOrderQty" => $value["eOrderQty"],
                    "BilledQty" => $value["BilledQty"],
                    "DiscPerc" => $value["DiscPerc"],
                    "DiscAmt" => $value["DiscAmt"],
                    "cgst" => $value["cgst"],
                    "cgstamt" => $value["cgstamt"],
                    "sgst" => $value["sgst"],
                    "sgstamt" => $value["sgstamt"],
                    "igst" => $value["igst"],
                    "igstamt" => $value["igstamt"],
                    "CaseQty" => $value["CaseQty"],
                    "Cases" => $value["Cases"],
                    "OrderAmt" => $value["OrderAmt"],
                    "ChallanAmt" => $value["ChallanAmt"],
                    "NetOrderAmt" => $value["NetOrderAmt"],
                    "NetChallanAmt" => $value["NetChallanAmt"],
                    "Ordinalno" => $value["Ordinalno"],
                    "UserID" => $value["UserID"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                    "UserID2" => $_SESSION["username"],
                ];
                $this->db->insert(db_prefix() . "K1history_audit", $old_data);
            }
            // Delete Live history table record
            $this->db->where("PlantID", $selected_company);
            $this->db->where("FY", $fy);
            $this->db->where("OrderID", $PurchID);
            $this->db->delete(db_prefix() . "K1history");
            // Add New history detail record
            //$Pr_item_data = $this->GetPurchaseRequestItemListInvoiceAdd($Pr_no);
            $i = 1;
            $TotalPurchAmt = 0;
            $TotalDISCAmt = 0;
            $TotalCGSTAmt = 0;
            $TotalSGSTAmt = 0;
            $TotalIGSTAmt = 0;
            $TotalNetAmt = 0;
            foreach ($es_detail as $value) {
                $productId      = $value["ItemID"];
                $brand          = $value["Brand"];
                $unit           = $value["MeasuredIn"];
                $packing_qty    = $value["PackingQty"]; // 12
                $packing_weight = $value["PackingWeight"];
                $saleunit       = $value["PurchaseUnit"];
                $qty            = $value["Qty"]; // 100
                $PurchRate      = $value["PurchRate"]; // 1200
                $gst            = $value["GST"]; // 5

                $PurchRate  = $value["PurchRate"] / $value["PackingQty"]; // 1200 / 12 = 100
                $salerate   = ($PurchRate + ($PurchRate * ($gst / 100))); // 100 + (100 * (5 / 100)) = 105
                $ItemQty = $packing_qty * $qty; // 12 * 100 = 1200
                $ItemAmt = $ItemQty * $PurchRate; // 1200 * 100 = 120000
                $ItemDisc = 0;
                $DiscPer = 0;
                $UnitDisc = 0;
                if ($value["Discount"] > 0 && $ItemQty > 0) {
                    $UnitDisc = $value["Discount"]; // 5
                    $ItemDisc = $value["Discount"] * $ItemQty; // 5 * 1200 = 6000
                    $DiscPer = ($value["Discount"] / $PurchRate) * 100; // (5 / 100) * 100 = 5
                }
                $ItemTaxableAmt = $ItemAmt - $ItemDisc; // 120000 - 6000 = 114000
                $ItemGSTAmt = $ItemTaxableAmt * ($gst / 100); // 114000 * (5 / 100) = 5700
                $CGSTPer = null;
                $SGSTPer = null;
                $IGSTPer = null;
                $CGSTAmt = 0;
                $SGSTAmt = 0;
                $IGSTAmt = 0;
                if ($CenterState == $State) {
                    $CGSTPer = $gst / 2;
                    $SGSTPer = $gst / 2;
                    $CGSTAmt = $ItemGSTAmt / 2;
                    $SGSTAmt = $ItemGSTAmt / 2;
                } else {
                    $IGSTPer = $gst;
                    $IGSTAmt = $ItemGSTAmt;
                }
                $ItemNetAmt = $ItemTaxableAmt + $ItemGSTAmt;
                $TotalPurchAmt += $ItemAmt;
                $TotalDISCAmt += $ItemDisc;
                $TotalCGSTAmt += $CGSTAmt;
                $TotalSGSTAmt += $SGSTAmt;
                $TotalIGSTAmt += $IGSTAmt;
                $TotalNetAmt += $ItemNetAmt;
                if ($saleunit == "Loose") {
                    $caseqty = 1;
                } else {
                    $caseqty = $packing_qty;
                }
                $data_array_result = [
                    "PlantID"   => $selected_company,
                    "FY"        => $fy,
                    "OrderID"   => $PurchID,
                    "BillID"    => $Pr_no,
                    "TransID"   => $Pr_no,
                    "TransDate" => $new_date,
                    "TransDate2"=> date("Y-m-d H:i:s"),
                    "TType"     => $TType,
                    "TType2"    => $TType2,
                    "AccountID" => $AccountID,
                    "ItemID"    => $productId,
                    "CenterID"  => $CenterID,
                    "PartyID"   => "KASPL",
                    "PurchRate" => $PurchRate,
                    "SaleRate"  => $salerate,
                    "BasicRate" => $PurchRate,
                    "SuppliedIn"=> $saleunit,
                    "OrderQty"  => $ItemQty,
                    "BilledQty" => $ItemQty,
                    "DiscPerc"  => $DiscPer,
                    "DiscAmt"   => $UnitDisc,
                    "cgst"      => $CGSTPer,
                    "cgstamt"   => $CGSTAmt,
                    "sgst"      => $SGSTPer,
                    "sgstamt"   => $SGSTAmt,
                    "igst"      => $IGSTPer,
                    "igstamt"   => $IGSTAmt,
                    "CaseQty"   => $caseqty,
                    "Cases"     => 0.0,
                    "OrderAmt"  => $ItemAmt,
                    "ChallanAmt"    => $ItemAmt,
                    "NetOrderAmt"   => $ItemNetAmt,
                    "NetChallanAmt" => $ItemNetAmt,
                    "Ordinalno" => $i,
                    "UserID"    => $_SESSION["username"],
                ];
                $this->db->insert(db_prefix() . "K1history", $data_array_result);
                $i++;
            }

            $roundedTotal = round($TotalNetAmt);
            $roundOffAmt = $roundedTotal - $TotalNetAmt;
            $KirtiOnePurchMaster = [
                "Purchamt" => $TotalPurchAmt,
                "Discamt" => $TotalDISCAmt,
                "cgstamt" => $TotalCGSTAmt,
                "sgstamt" => $TotalSGSTAmt,
                "igstamt" => $TotalIGSTAmt,
                "RoundOffAmt" => $roundOffAmt,
                "Invamt" => $roundedTotal,
                "ItCount" => $i,
            ];
            $this->db->where("PurchID", $PurchID);
            $this->db->update(db_prefix() . "K1PurchaseOrderMaster", $KirtiOnePurchMaster);
        }
        return true;
    }
    
    public function UpdatePurchaseInward($data, $id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "POrderQty";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            $header[] = "BatchNo";
            $header[] = "ExpDate";
            $header[] = "Ordinalno";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        
        $InvNo = $id;
        $new_date = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $AccountID      = $data["vendor"];
        $PurchID        = $data["PurchID"];
        $CenterName     = $data["CenterName"];
        $State          = $data["state"];
        $CenterState    = $data["CenterState"];
        $PurchAmt       = $data["total_amt_in_mt"];
        $discountAMT    = $data["total_disc_in_mt"];
        $cgstamt        = $data["total_cgst_amt"];
        $sgstamt        = $data["total_sgst_amt"];
        $igstamt        = $data["total_igst_amt"];
        $roundoffamt    = $data["total_roundoff_amt"];
        $invoiceamt     = $data["netpayableamt"] + ($data["OtherAmt"] ?? 0);
        $EwayBill       = $data["ewaybillno"];
        $VehicleNo      = $data["VehicleNo"];
        $InvoiceNo      = $data["InvoiceNo"];
        $OtherAmt       = $data["OtherAmt"];
        $OthEffectOn    = $data["OthEffectOn"];
        $total_tcs_amt  = $data["total_tcs_amt"];
        $ItCount        = count($es_detail);

        // Purchase Order Details
        $this->db->select('tblK1PurchaseOrderMaster.*');
        $this->db->from(db_prefix() . 'K1PurchaseOrderMaster');
        $this->db->where(db_prefix() . 'K1PurchaseOrderMaster.PurchID', $PurchID);
        $POD = $this->db->get()->row();
        $CenterID = $POD->CenterID;
        $PurchReqNo = $POD->Pr_no;

        $data_array = [
            "Inv_date"      => $new_date,
            "EwayBillNo"    => $EwayBill,
            "VehicleNo"     => $VehicleNo,
            "InvoiceNo"     => $InvoiceNo,
            "Purchamt"      => $PurchAmt,
            "Discamt"       => $discountAMT,
            "cgstamt"       => $cgstamt,
            "sgstamt"       => $sgstamt,
            "igstamt"       => $igstamt,
            "RoundOffAmt"   => $roundoffamt,
            "Invamt"        => $invoiceamt,
            "ItCount"       => $ItCount,
            "Lupdate"       => date("Y-m-d H:i:s"),
            "UserID2"       => $this->session->userdata("username"),
        ];

        $this->db->where([
            "PlantID" => $selected_company,
            "FY" => $fy,
            "PurchID" => $PurchID,
            "Inv_No" => $InvNo
        ]);
        $this->db->update(db_prefix() . "K1purchasemaster", $data_array);
        if ($this->db->affected_rows() > 0) {
            // Add New history detail record
            $i = 1;
            $TotalPurchAmt = 0;
            $TotalDISCAmt = 0;
            $TotalCGSTAmt = 0;
            $TotalSGSTAmt = 0;
            $TotalIGSTAmt = 0;
            $TotalNetAmt = 0;
            foreach ($es_detail as $value) {
                $productId      = $value["ItemID"];
                $brand          = $value["Brand"];
                $unit           = $value["MeasuredIn"];
                $packing_qty    = $value["PackingQty"]; // 12
                $packing_weight = $value["PackingWeight"];
                $saleunit       = $value["PurchaseUnit"];
                $qty            = $value["Qty"]; // 100
                $PurchRate      = $value["PurchRate"]; // 1200
                $gst            = $value["GST"]; // 5

                $PurchRate  = $value["PurchRate"] / $value["PackingQty"]; // 1200 / 12 = 100
                $salerate   = ($PurchRate + ($PurchRate * ($gst / 100))); // 100 + (100 * (5 / 100)) = 105
                $ItemQty = $packing_qty * $qty; // 12 * 100 = 1200
                $ItemAmt = $ItemQty * $PurchRate; // 1200 * 100 = 120000
                $ItemDisc = 0;
                $DiscPer = 0;
                $UnitDisc = 0;
                if ($value["Discount"] > 0 && $ItemQty > 0) {
                    $UnitDisc = $value["Discount"]; // 5
                    $ItemDisc = $value["Discount"] * $ItemQty; // 5 * 1200 = 6000
                    $DiscPer = ($value["Discount"] / $PurchRate) * 100; // (5 / 100) * 100 = 5
                }
                $ItemTaxableAmt = $ItemAmt - $ItemDisc; // 120000 - 6000 = 114000
                $ItemGSTAmt = $ItemTaxableAmt * ($gst / 100); // 114000 * (5 / 100) = 5700

                $CGSTPer = null;
                $SGSTPer = null;
                $IGSTPer = null;
                $CGSTAmt = 0;
                $SGSTAmt = 0;
                $IGSTAmt = 0;
                if ($CenterState == $State) {
                    $CGSTPer = $gst / 2;
                    $SGSTPer = $gst / 2;
                    $CGSTAmt = $ItemGSTAmt / 2;
                    $SGSTAmt = $ItemGSTAmt / 2;
                } else {
                    $IGSTPer = $gst;
                    $IGSTAmt = $ItemGSTAmt;
                }
                $ItemNetAmt = $ItemTaxableAmt + $ItemGSTAmt;
                $TotalPurchAmt += $ItemAmt;
                $TotalDISCAmt += $ItemDisc;
                $TotalCGSTAmt += $CGSTAmt;
                $TotalSGSTAmt += $SGSTAmt;
                $TotalIGSTAmt += $IGSTAmt;
                $TotalNetAmt += $ItemNetAmt;
                if ($saleunit == "Loose") {
                    $caseqty = 1;
                } else {
                    $caseqty = $packing_qty;
                }
                $data_array_result = [
                    "PurchRate" => $PurchRate,
                    "SaleRate" => $salerate,
                    "BasicRate" => $PurchRate,
                    "SuppliedIn" => $saleunit,
                    "OrderQty" => $ItemQty,
                    "BilledQty" => $ItemQty,
                    "DiscPerc" => $DiscPer,
                    "DiscAmt" => $UnitDisc,
                    "cgst" => $CGSTPer,
                    "cgstamt" => $CGSTAmt,
                    "sgst" => $SGSTPer,
                    "sgstamt" => $SGSTAmt,
                    "igst" => $IGSTPer,
                    "igstamt" => $IGSTAmt,
                    "CaseQty" => $caseqty,
                    "OrderAmt" => $ItemAmt,
                    "ChallanAmt" => $ItemAmt,
                    "NetOrderAmt" => $ItemNetAmt,
                    "NetChallanAmt" => $ItemNetAmt,
                    "BatchNo" => $value["BatchNo"],
                    "ExpDate" => to_sql_date($value["ExpDate"]),
                    "Ordinalno" => $i,
                    "UserID2" => $_SESSION["username"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                ];

                $this->db->where([
                    "PlantID"   => $selected_company,
                    "FY"        => $fy,
                    "OrderID"   => $PurchID,
                    "TransID"   => $InvNo,
                    "ItemID"    => $productId,
                    "AccountID" => $AccountID,
                    "BatchNo"   => $value["BatchNo"]
                ]);
                $this->db->update(db_prefix() . "K1history", $data_array_result);
                $i++;
            }
            $roundedTotal = round($TotalNetAmt + $OtherAmt);
            $roundOffAmt = $roundedTotal - ($TotalNetAmt + $OtherAmt);
            $KirtiOnePurchMaster = [
                "Purchamt" => $TotalPurchAmt,
                "Discamt" => $TotalDISCAmt,
                "Othamt" => $OtherAmt,
                "OthAmtEffectOn" => $OthEffectOn,
                "tcsAmt" => $total_tcs_amt,
                "cgstamt" => $TotalCGSTAmt,
                "sgstamt" => $TotalSGSTAmt,
                "igstamt" => $TotalIGSTAmt,
                "RoundOffAmt" => $roundOffAmt,
                "Invamt" => $roundedTotal,
                "ItCount" => $i,
            ];
            
            $this->db->where([
                "PurchID" => $PurchID,
                "Inv_No" => $InvNo
            ]);
            $this->db->update(db_prefix() . "K1purchasemaster", $KirtiOnePurchMaster);

            // Order Status Update
            // Total Order qty
            $this->db->select_sum('BilledQty');
            $this->db->from(db_prefix() . 'K1history');
            $this->db->where([
                'OrderID' => $PurchID,
                'TType' => 'P',
                'TType2' => 'Purchase Order'
            ]);
            $total_order_qty = $this->db->get()->row()->BilledQty ?? 0;

            // Total Inward qty
            $this->db->select_sum('BilledQty');
            $this->db->from(db_prefix() . 'K1history');
            $this->db->where([
                'OrderID' => $PurchID,
                'TType' => 'P',
                'TType2' => 'Purchase'
            ]);
            $total_inward_qty = $this->db->get()->row()->BilledQty ?? 0;

            $order_status = 'P'; // Pending by default
            if (($total_order_qty - $total_inward_qty) <= 0) {
                $order_status = 'F'; // Fully received
            } else{
                $order_status = 'I'; // Partially received
            }

            $KirtiOnePurchMaster = [
                "OrderStatus" => $order_status,
            ];
            $this->db->where("PurchID", $PurchID);
            $this->db->update(db_prefix() . "K1PurchaseOrderMaster", $KirtiOnePurchMaster); 
            
        }
        return true;
    }

    public function UpdateKirtiOnePurchaseInvoice($data, $id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            $header[] = "BatchNo";
            $header[] = "ExpDate";
            $header[] = "Ordinalno";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        /*echo "<pre>";
			print_r($pur_order_detail);
			print_r($es_detail);
			die;*/
        $InvNo = $id;
        $AccountID = $data["vendor"];
        $PurchID = $data["PurchID"];
        // Purchase Order Details
        /*$this->db->select('tblK1purchasemaster.*');
        	$this->db->from(db_prefix() . 'K1purchasemaster');
        	$this->db->where(db_prefix() . 'K1purchasemaster.PurchID', $PurchID);
        	$PurchOrderDetails = $this->db->get()->row();
        	$CenterID = $PurchOrderDetails->CenterID;
        	$this->db->select('tblCenterMaster.*');
        	$this->db->from(db_prefix() . 'CenterMaster');
        	$this->db->where(db_prefix() . 'CenterMaster.CenterID', $CenterID);
        	$CenterDetails = $this->db->get()->row();
        	$CenterState = $CenterDetails->state;*/
        $CenterState = $data["CenterState"];
        $State = $data["state"];
        $new_date = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $EwayBill = $data["ewaybillno"];
        $VehicleNo = $data["VehicleNo"];
        $InvoiceNo = $data["InvoiceNo"];
        $OtherAmt = $data["OtherAmt"];
        $OthEffectOn = $data["OthEffectOn"];
        $total_tcs_amt = $data["total_tcs_amt"];
        $ItCount = count($es_detail);
        $data_array = [
            "Inv_date" => $new_date,
            "EwayBillNo" => $EwayBill,
            "VehicleNo" => $VehicleNo,
            "InvoiceNo" => $InvoiceNo,
            "Purchamt" => $purchAmt,
            "Discamt" => $Discamt,
            "Othamt" => $OtherAmt,
            "OthAmtEffectOn" => $OthEffectOn,
            "tcsAmt" => $total_tcs_amt,
            "cgstamt" => $cgstamt,
            "sgstamt" => $sgstamt,
            "igstamt" => $igstamt,
            "RoundOffAmt" => $RoundOffAmt,
            "Invamt" => $Invamt,
            "ItCount" => $ItCount,
            "Lupdate" => date("Y-m-d H:i:s"),
            "UserID2" => $this->session->userdata("username"),
        ];
        $this->db->where("PlantID", $selected_company);
        $this->db->LIKE("FY", $fy);
        $this->db->where("PurchID", $PurchID);
        $this->db->where("Inv_No", $InvNo);
        $this->db->update(db_prefix() . "K1purchasemaster", $data_array);
        if ($this->db->affected_rows() > 0) {
            // Add New history detail record
            $i = 1;
            $TotalPurchAmt = 0;
            $TotalDISCAmt = 0;
            $TotalCGSTAmt = 0;
            $TotalSGSTAmt = 0;
            $TotalIGSTAmt = 0;
            $TotalNetAmt = 0;
            foreach ($es_detail as $value) {
                $productId = $value["ItemID"];
                $Ordinalno = $value["Ordinalno"];
                $brand = $value["Brand"];
                $unit = $value["MeasuredIn"];
                $packing_qty = $value["PackingQty"];
                $packing_weight = $value["PackingWeight"];
                $saleunit = $value["PurchaseUnit"];
                $qty = $value["Qty"];
                $PurchRate = $value["PurchRate"];
                $gst = $value["GST"];
                $salerate = $PurchRate + $PurchRate * ($gst / 100);
                $ItemQty = $packing_qty * $qty;
                $ItemAmt = $qty * $PurchRate;
                $ItemDisc = 0;
                $DiscPer = 0;
                $UnitDisc = 0;
                if ($value["Discount"] > 0 && $qty > 0) {
                    $ItemDisc = $value["Discount"] * $qty;
                    $UnitDisc = $value["Discount"];
                    $DiscPer = ($value["Discount"] / $PurchRate) * 100;
                }
                $ItemTaxableAmt = $ItemAmt - $ItemDisc;
                $ItemGSTAmt = $ItemTaxableAmt * ($gst / 100);
                $CGSTPer = null;
                $SGSTPer = null;
                $IGSTPer = null;
                $CGSTAmt = 0;
                $SGSTAmt = 0;
                $IGSTAmt = 0;
                if ($CenterState == $State) {
                    $CGSTPer = $gst / 2;
                    $SGSTPer = $gst / 2;
                    $CGSTAmt = $ItemGSTAmt / 2;
                    $SGSTAmt = $ItemGSTAmt / 2;
                } else {
                    $IGSTPer = $gst;
                    $IGSTAmt = $ItemGSTAmt;
                }
                $ItemNetAmt = $ItemTaxableAmt + $ItemGSTAmt;
                $TotalPurchAmt += $ItemAmt;
                $TotalDISCAmt += $ItemDisc;
                $TotalCGSTAmt += $CGSTAmt;
                $TotalSGSTAmt += $SGSTAmt;
                $TotalIGSTAmt += $IGSTAmt;
                $TotalNetAmt += $ItemNetAmt;
                if ($saleunit == "Loose") {
                    $caseqty = 1;
                } else {
                    $caseqty = $packing_qty;
                }
                $data_array_result = [
                    "PurchRate" => $PurchRate,
                    "SaleRate" => $salerate,
                    "BasicRate" => $PurchRate,
                    "SuppliedIn" => $saleunit,
                    "OrderQty" => $ItemQty,
                    "BilledQty" => $ItemQty,
                    "DiscPerc" => $DiscPer,
                    "DiscAmt" => $UnitDisc,
                    "cgst" => $CGSTPer,
                    "cgstamt" => $CGSTAmt,
                    "sgst" => $SGSTPer,
                    "sgstamt" => $SGSTAmt,
                    "igst" => $IGSTPer,
                    "igstamt" => $IGSTAmt,
                    "CaseQty" => $caseqty,
                    "OrderAmt" => $ItemAmt,
                    "ChallanAmt" => $ItemAmt,
                    "NetOrderAmt" => $ItemNetAmt,
                    "NetChallanAmt" => $ItemNetAmt,
                    "BatchNo" => $value["BatchNo"],
                    "ExpDate" => to_sql_date($value["ExpDate"]),
                    "Ordinalno" => $i,
                    "UserID2" => $_SESSION["username"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                ];
                $this->db->where("OrderID", $PurchID);
                $this->db->where("TransID", $InvNo);
                $this->db->where("ItemID", $productId);
                $this->db->where("AccountID", $AccountID);
                $this->db->where("BatchNo", $value["BatchNo"]);
                $this->db->update(
                    db_prefix() . "K1history",
                    $data_array_result
                );
                $i++;
            }
            $roundedTotal = round($TotalNetAmt + $OtherAmt);
            $roundOffAmt = $roundedTotal - ($TotalNetAmt + $OtherAmt);
            $KirtiOnePurchMaster = [
                "Purchamt" => $TotalPurchAmt,
                "Discamt" => $TotalDISCAmt,
                "Othamt" => $OtherAmt,
                "OthAmtEffectOn" => $OthEffectOn,
                "tcsAmt" => $total_tcs_amt,
                "cgstamt" => $TotalCGSTAmt,
                "sgstamt" => $TotalSGSTAmt,
                "igstamt" => $TotalIGSTAmt,
                "RoundOffAmt" => $roundOffAmt,
                "Invamt" => $roundedTotal,
                "ItCount" => $i,
            ];
            $this->db->where("PurchID", $PurchID);
            $this->db->where("Inv_No", $InvNo);
            $this->db->update(
                db_prefix() . "K1purchasemaster",
                $KirtiOnePurchMaster
            );
        }
        return true;
    }
    
    public function UpdateInvoiceLedger($data, $id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            $header[] = "BatchNo";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $Inv_No         = $id;
        $VendorID       = $data["vendor"];
        $VendorDocNo    = $data["VendorDocNo"];
        $State          = $data["state"];
        $PurchaseType   = $data["purchasetype"];
        $PaymentMode    = $data["paymode"];
        $PaymentMethod  = $data["paymentmethod"];
        $Refno          = $data["referenceno"];
        $Effecton       = $data["Effecton"];
        $ExpenceType    = $data["expense_type"];
        $ExpenceAmt     = $data["expense_amt"];
        $IncomeType     = $data["income_type"];
        $IncomeAmt      = $data["income_amt"];
        $PurchAmt       = $data["total_amt_in_mt"];
        $discountAMT    = $data["total_disc_in_mt"];
        $cgstamt        = $data["total_cgst_amt"];
        $sgstamt        = $data["total_sgst_amt"];
        $igstamt        = $data["total_igst_amt"];
        $roundoffamt    = $data["total_roundoff_amt"];
        $invoiceamt     = $data["netpayableamt"];

        $InvData = $this->GetPurchaseInvoiceDetails($Inv_No);
        // echo '<pre>';
        // print_r($es_detail);
        // die;

        $CenterID = $InvData->CenterID;
        $Transdate = substr($InvData->Inv_date, 0, 19);
        $ItCount = count($es_detail);
        // $nextPaymentnumber = get_option("next_payment_number_for_kirti");
        if ($PurchaseType == 2) {
            $PaymentMode = null;
            $Effecton = null;
            $PaymentMethod = null;
            $Refno = null;
        }
        if ($PurchaseType == 1) {
            $PaymentNo = $this->generateNextVoucherIDNew($Transdate, $selected_company, 'PAYMENTS');
        } else {
            $PaymentNo = null;
        }

        // get center details
        $this->db->select("tblCenterMaster.*");
        $this->db->from(db_prefix() . "CenterMaster");
        $this->db->where(db_prefix() . "CenterMaster.CenterID", $InvData->CenterID);
        $CenterDetails = $this->db->get()->row();
        $CenterState = $CenterDetails->state;

        // get vendor details
        $this->db->select("tblclients.*");
        $this->db->from(db_prefix() . "clients");
        $this->db->where(db_prefix() . "clients.AccountID", $InvData->AccountID);
        $State = $this->db->get()->row()->state ?? '';

        // rate update code new ============
        foreach ($es_detail as $value) {
            $productId      = $value["ItemID"];
            $brand          = $value["Brand"];
            $unit           = $value["MeasuredIn"];
            $packing_qty    = $value["PackingQty"]; // 12
            $packing_weight = $value["PackingWeight"];
            $saleunit       = $value["PurchaseUnit"];
            $qty            = $value["Qty"]; // 100
            $PurchRate      = $value["PurchRate"]; // 1200
            $gst            = $value["GST"]; // 5

            $PurchRate  = $value["PurchRate"] / $value["PackingQty"]; // 1200 / 12 = 100
            $salerate   = ($PurchRate + ($PurchRate * ($gst / 100))); // 100 + (100 * (5 / 100)) = 105
            $ItemQty = $packing_qty * $qty; // 12 * 100 = 1200
            $ItemAmt = $ItemQty * $PurchRate; // 1200 * 100 = 120000
            $ItemDisc = 0;
            $DiscPer = 0;
            $UnitDisc = 0;
            if ($value["Discount"] > 0 && $ItemQty > 0) {
                $UnitDisc = $value["Discount"]; // 5
                $ItemDisc = $value["Discount"] * $ItemQty; // 5 * 1200 = 6000
                $DiscPer = ($value["Discount"] / $PurchRate) * 100; // (5 / 100) * 100 = 5
            }
            $ItemTaxableAmt = $ItemAmt - $ItemDisc; // 120000 - 6000 = 114000
            $ItemGSTAmt = $ItemTaxableAmt * ($gst / 100); // 114000 * (5 / 100) = 5700
            $CGSTPer = null;
            $SGSTPer = null;
            $IGSTPer = null;
            $CGSTAmt = 0;
            $SGSTAmt = 0;
            $IGSTAmt = 0;
            if ($CenterState == $State) {
                $CGSTPer = $gst / 2;
                $SGSTPer = $gst / 2;
                $CGSTAmt = $ItemGSTAmt / 2;
                $SGSTAmt = $ItemGSTAmt / 2;
            } else {
                $IGSTPer = $gst;
                $IGSTAmt = $ItemGSTAmt;
            }
            $ItemNetAmt = $ItemTaxableAmt + $ItemGSTAmt;
            $TotalPurchAmt += $ItemAmt;
            $TotalDISCAmt += $ItemDisc;
            $TotalCGSTAmt += $CGSTAmt;
            $TotalSGSTAmt += $SGSTAmt;
            $TotalIGSTAmt += $IGSTAmt;
            $TotalNetAmt += $ItemNetAmt;

            $data_array_result = [
                "PurchRate" => $PurchRate,
                "SaleRate" => $salerate,
                "BasicRate" => $PurchRate,
                "DiscPerc" => $DiscPer,
                "DiscAmt" => $UnitDisc,
                "cgst" => $CGSTPer,
                "cgstamt" => $CGSTAmt,
                "sgst" => $SGSTPer,
                "sgstamt" => $SGSTAmt,
                "igst" => $IGSTPer,
                "igstamt" => $IGSTAmt,
                "OrderAmt" => $ItemAmt,
                "ChallanAmt" => $ItemAmt,
                "NetOrderAmt" => $ItemNetAmt,
                "NetChallanAmt" => $ItemNetAmt,
                "UserID2" => $_SESSION["username"],
                "Lupdate" => date("Y-m-d H:i:s"),
            ];
            
            $this->db->where([
                "TransID" => $Inv_No,
                "OrderQty" => $ItemQty,
                "ItemID" => $productId,
                "AccountID" => $VendorID,
                "BatchNo" => $value["BatchNo"]
            ]);
            $this->db->update(db_prefix() . "K1history", $data_array_result);
        }
        // rate update code new ============
        
        $this->db->select("tblK1purchasemaster.*");
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->where(db_prefix() . "K1purchasemaster.Inv_No", $Inv_No);
        $purchaselist = $this->db->get()->row();
        
        $roundedTotal = round($TotalNetAmt + $OtherAmt);
        $roundOffAmt = $roundedTotal - ($TotalNetAmt + $OtherAmt);
        $data_array = [
            "Is_Ledger" => "Y",
            "OrderStatus" => "F",
            "InvoiceNo" => $VendorDocNo,
            "PaymentNo" => $PaymentNo,
            "PurchaseType" => $PurchaseType,
            "PaymentMode" => $PaymentMode,
            "PaymentMethod" => $PaymentMethod,
            "RefNo" => $Refno,
            "EffectOn" => $Effecton,
            "ItCount" => $ItCount,
            "Purchamt"  => $TotalPurchAmt,
            "Discamt"   => $TotalDISCAmt,
            "cgstamt"   => $TotalCGSTAmt,
            "sgstamt"   => $TotalSGSTAmt,
            "igstamt"   => $TotalIGSTAmt,
            "RoundOffAmt" => $roundOffAmt,
            "Invamt"    => $roundedTotal,
            "Lupdate" => date("Y-m-d H:i:s"),
            "UserID2" => $this->session->userdata("username"),
        ];
        $this->db->where("PlantID", $selected_company);
        $this->db->LIKE("FY", $fy);
        $this->db->where("Inv_No", $Inv_No);
        $this->db->update(db_prefix() . "K1purchasemaster", $data_array);
        if ($this->db->affected_rows() > 0) {
            // save expense entry
            if(count($ExpenceType) > 0){
                for($i=0; $i<count($ExpenceType); $i++){
                    if(empty($ExpenceAmt[$i]) || $ExpenceAmt[$i] == 0){
                        continue;
                    }
                    $expence_entry = [
                        "PlantID" => $selected_company,
                        "FY" => $fy,
                        "Transdate" => $Transdate,
                        "UserID" => $_SESSION["username"],
                        "Inv_No" => $Inv_No,
                        "LedgerCategory" => 'Direct Expense',
                        "LedgerType" => $ExpenceType[$i],
                        "Amount" => $ExpenceAmt[$i],
                    ];
                    $where = [
                        "PlantID" => $selected_company,
                        "FY" => $fy,
                        "Inv_No" => $Inv_No,
                        "LedgerCategory" => 'Direct Expense',
                        "LedgerType" => $ExpenceType[$i],
                    ];
                    if($this->db->get_where(db_prefix() . "K1PurchaseMasterExpenses", $where)->num_rows() > 0){
                        $this->db->where($where);
                        $this->db->update(db_prefix() . "K1PurchaseMasterExpenses", $expence_entry);
                    }else{
                        $this->db->insert(db_prefix() . "K1PurchaseMasterExpenses", $expence_entry);
                    }
                }
                // delete expense entry
                $this->db->where("PlantID", $selected_company);
                $this->db->where("FY", $fy);
                $this->db->where("Inv_No", $Inv_No);
                $this->db->where("LedgerCategory", 'Direct Expense');
                $this->db->where_not_in("LedgerType", $ExpenceType);
                $this->db->delete(db_prefix() . "K1PurchaseMasterExpenses");
            }
            // save income entry
            if(count($IncomeType) > 0){
                for($i=0; $i<count($IncomeType); $i++){
                    if(empty($IncomeAmt[$i]) || $IncomeAmt[$i] == 0){
                        continue;
                    }
                    $income_entry = [
                        "PlantID" => $selected_company,
                        "FY" => $fy,
                        "Transdate" => $Transdate,
                        "UserID" => $_SESSION["username"],
                        "Inv_No" => $Inv_No,
                        "LedgerCategory" => 'Direct Income',
                        "LedgerType" => $IncomeType[$i],
                        "Amount" => $IncomeAmt[$i],
                    ];
                    $where = [
                        "PlantID" => $selected_company,
                        "FY" => $fy,
                        "Inv_No" => $Inv_No,
                        "LedgerCategory" => 'Direct Income',
                        "LedgerType" => $IncomeType[$i],
                    ];
                    // echo print_r($income_entry); die;
                    if($this->db->get_where(db_prefix() . "K1PurchaseMasterExpenses", $where)->num_rows() > 0){
                        $this->db->where($where);
                        $this->db->update(db_prefix() . "K1PurchaseMasterExpenses", $income_entry);
                    }else{
                        $this->db->insert(db_prefix() . "K1PurchaseMasterExpenses", $income_entry);
                    }
                }
                // delete income entry
                $this->db->where("PlantID", $selected_company);
                $this->db->where("FY", $fy);
                $this->db->where("Inv_No", $Inv_No);
                $this->db->where("LedgerCategory", 'Direct Income');
                $this->db->where_not_in("LedgerType", $IncomeType);
                $this->db->delete(db_prefix() . "K1PurchaseMasterExpenses");
            }
            // Move Ledger data from ledger table to ledger history table
            $GetLedgerList = $this->GetLedgerListByVoucher($Inv_No);
            $GetLedgerListByPaymentNo = $this->GetLedgerListByPayment(
                $purchaselist->PaymentNo
            );
            foreach ($GetLedgerList as $key => $val) {
                $ledger_audit = [
                    "PlantID" => $val["PlantID"],
                    "FY" => $val["FY"],
                    "Transdate" => $val["Transdate"],
                    "TransDate2" => $val["TransDate2"],
                    "VoucherID" => $val["VoucherID"],
                    "PartyID" => $val["PartyID"],
                    "AccountID" => $val["AccountID"],
                    "CounterAccount" => $val["CounterAccount"],
                    "CenterID" => $val["CenterID"],
                    "CommodityID" => $val["CommodityID"],
                    "EntryFor" => $val["EntryFor"],
                    "TType" => $val["TType"],
                    "Amount" => $val["Amount"],
                    "Narration" => $val["Narration"],
                    "PassedFrom" => $val["PassedFrom"],
                    "OrdinalNo" => $val["OrdinalNo"],
                    "UserID" => $val["UserID"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                    "UserID2" => $this->session->userdata("username"),
                ];
                $this->db->insert(
                    db_prefix() . "accountledgeraudit",
                    $ledger_audit
                );
            }
            foreach ($GetLedgerListByPaymentNo as $key => $val1) {
                $ledger_auditPayment = [
                    "PlantID" => $val1["PlantID"],
                    "FY" => $val1["FY"],
                    "Transdate" => $val1["Transdate"],
                    "TransDate2" => $val1["TransDate2"],
                    "VoucherID" => $val1["VoucherID"],
                    "PartyID" => $val1["PartyID"],
                    "AccountID" => $val1["AccountID"],
                    "CounterAccount" => $val1["CounterAccount"],
                    "CenterID" => $val1["CenterID"],
                    "CommodityID" => $val1["CommodityID"],
                    "EntryFor" => $val1["EntryFor"],
                    "TType" => $val1["TType"],
                    "Amount" => $val1["Amount"],
                    "Narration" => $val1["Narration"],
                    "PassedFrom" => $val1["PassedFrom"],
                    "OrdinalNo" => $val1["OrdinalNo"],
                    "UserID" => $val1["UserID"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                    "UserID2" => $this->session->userdata("username"),
                ];
                $this->db->insert(
                    db_prefix() . "accountledgeraudit",
                    $ledger_auditPayment
                );
            }
            // Delete Previous ledger data
            $this->db->where("PlantID", $selected_company);
            $this->db->where("FY", $fy);
            $this->db->where("VoucherID", $Inv_No);
            $this->db->delete(db_prefix() . "accountledger");
            $this->db->where("PlantID", $selected_company);
            $this->db->where("FY", $fy);
            $this->db->where("VoucherID", $purchaselist->PaymentNo);
            $this->db->where("PassedFrom", "PAYMENTS");
            $this->db->delete(db_prefix() . "accountledger");
            //Ledger entry code
            $ord_n = 1;
            $narrations = "By Purchase no." . $Inv_No;
            // Credit to Vendor
            $ledger_credit = [
                "PlantID" => $selected_company,
                "FY" => $fy,
                "Transdate" => $Transdate,
                "TransDate2" => date("Y-m-d H:i:s"),
                "VoucherID" => $Inv_No,
                "PartyID" => "KASPL",
                "AccountID" => $VendorID,
                "CounterAccount" => "PURCH",
                "CenterID" => $CenterID,
                "EntryFor" => "2",
                "TType" => "C",
                "Amount" => $invoiceamt,
                "Narration" => $narrations,
                "PassedFrom" => "PURCHASE",
                "OrdinalNo" => $ord_n,
                "UserID" => $_SESSION["username"],
            ];
            $this->db->insert(db_prefix() . "accountledger", $ledger_credit);
            $ord_n++;
            // Debit to Purchase Account
            $ledger_debit = [
                "PlantID" => $selected_company,
                "FY" => $fy,
                "Transdate" => $Transdate,
                "TransDate2" => date("Y-m-d H:i:s"),
                "VoucherID" => $Inv_No,
                "PartyID" => "KASPL",
                "AccountID" => "PURCH",
                "CounterAccount" => $VendorID,
                "CenterID" => $CenterID,
                "EntryFor" => "2",
                "TType" => "D",
                "Amount" => $PurchAmt,
                "Narration" => $narrations,
                "PassedFrom" => "PURCHASE",
                "OrdinalNo" => $ord_n,
                "UserID" => $_SESSION["username"],
            ];
            $this->db->insert(db_prefix() . "accountledger", $ledger_debit);
            $ord_n++;
            //Debit to Tax Account
            if ($cgstamt != 0.0 && $sgstamt != 0.0) {
                //CGST Tax Ledger Entry
                $Cgst_Ledger_entry = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "CGST",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $data["total_cgst_amt"],
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $CgstLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $Cgst_Ledger_entry
                );
                $ord_n++;
                //SGST Tax Ledger Entry
                $Sgst_Ledger_entry = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "SGST",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $data["total_sgst_amt"],
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $SgstLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $Sgst_Ledger_entry
                );
                $ord_n++;
            } elseif ($igstamt != 0.0) {
                //Igst Ledger Entry
                $Igst_Ledger_Entry = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "IGST",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $data["total_igst_amt"],
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $IgstLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $Igst_Ledger_Entry
                );
                $ord_n++;
            }
            $ord_n++;
            // expense ledger entry
            if(count($ExpenceType) > 0){
                for($i=0; $i<count($ExpenceType); $i++){
                    if(empty($ExpenceAmt[$i]) || $ExpenceAmt[$i] == 0){
                        continue;
                    }
                    $ExpenceLedgerEntry = [
                        "PlantID" => $selected_company,
                        "FY" => $fy,
                        "Transdate" => $Transdate,
                        "TransDate2" => date("Y-m-d H:i:s"),
                        "VoucherID" => $Inv_No,
                        "PartyID" => "KASPL",
                        "AccountID" => $ExpenceType[$i],
                        "CounterAccount" => $VendorID,
                        "CenterID" => $CenterID,
                        "EntryFor" => "2",
                        "TType" => "D",
                        "Amount" => $ExpenceAmt[$i],
                        "Narration" => $narrations,
                        "PassedFrom" => "PURCHASE",
                        "OrdinalNo" => $ord_n,
                        "UserID" => $_SESSION["username"],
                    ];
                    $ExpenseLedgerEntry = $this->db->insert(db_prefix() . "accountledger", $ExpenceLedgerEntry);
                    $ord_n++;
                }
            }
            // income ledger entry
            if(count($IncomeType) > 0){
                for($i=0; $i<count($IncomeType); $i++){
                    if(empty($IncomeAmt[$i]) || $IncomeAmt[$i] == 0){
                        continue;
                    }
                    $IncomeLedgerEntry = [
                        "PlantID" => $selected_company,
                        "FY" => $fy,
                        "Transdate" => $Transdate,
                        "TransDate2" => date("Y-m-d H:i:s"),
                        "VoucherID" => $Inv_No,
                        "PartyID" => "KASPL",
                        "AccountID" => $IncomeType[$i],
                        "CounterAccount" => $VendorID,
                        "CenterID" => $CenterID,
                        "EntryFor" => "2",
                        "TType" => "C",
                        "Amount" => $IncomeAmt[$i],
                        "Narration" => $narrations,
                        "PassedFrom" => "PURCHASE",
                        "OrdinalNo" => $ord_n,
                        "UserID" => $_SESSION["username"],
                    ];
                    $IncomeLedgerEntry = $this->db->insert(db_prefix() . "accountledger", $IncomeLedgerEntry);
                    $ord_n++;
                }
            }
            //Debit to Discount Ledger Entry
            if ($discountAMT > 0) {
                $disc_ledger_entry = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "DISC",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "C",
                    "Amount" => $discountAMT,
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $DiscountLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $disc_ledger_entry
                );
                $ord_n++;
            }
            //Debit to RoundAmt Ledger Entry
            if ($roundoffamt >= 0) {
                $roundledgerentry_debit = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "ROUNDOFF",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "C",
                    "Amount" => $roundoffamt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $Round_Debit_LedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $roundledgerentry_debit
                );
                $ord_n++;
            } else {
                $amt = abs($roundoffamt);
                $roundledgerentry_credit = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "ROUNDOFF",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $amt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $Round_credit_LedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $roundledgerentry_credit
                );
                $ord_n++;
            }
            //$nextPaymentnumber = get_option('next_payment_number_for_kirti');
            if ($PurchaseType == 1) {
                $ordinalno = 1;
                //Payment Voucher Debit Entry to Company
                $paymententry_Debit_tocompany = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $PaymentNo,
                    "PartyID" => "KASPL",
                    "AccountID" => $VendorID,
                    "CounterAccount" => $Effecton,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $invoiceamt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PAYMENTS",
                    "OrdinalNo" => $ordinalno,
                    "UserID" => $_SESSION["username"],
                ];
                $DebitToCompany = $this->db->insert(
                    db_prefix() . "accountledger",
                    $paymententry_Debit_tocompany
                );
                $ordinalno++;
                //Payment Voucher Credit Entry to Vendor
                $paymententry_Credit_tovendor = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $PaymentNo,
                    "PartyID" => "KASPL",
                    "AccountID" => $Effecton,
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "C",
                    "Amount" => $invoiceamt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PAYMENTS",
                    "OrdinalNo" => $ordinalno,
                    "UserID" => $_SESSION["username"],
                ];
                $CreditToVendor = $this->db->insert(
                    db_prefix() . "accountledger",
                    $paymententry_Credit_tovendor
                );
                $this->increment_next_number("next_payment_number_for_kirti");
            }
        }
        return true;
    }

    public function UpdateKirtiOnePurchaseInvoiceLedger($data, $id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $Inv_No = $id;
        $VendorID = $data["vendor"];
        $VendorDocNo = $data["VendorDocNo"];
        $State = $data["state"];
        $PurchaseType = $data["purchasetype"];
        $PaymentMode = $data["paymode"];
        $PaymentMethod = $data["paymentmethod"];
        $Refno = $data["referenceno"];
        $Effecton = $data["Effecton"];
        $ExpenceType = $data["expense_type"];
        $ExpenceAmt = $data["expense_amt"];
        $IncomeType = $data["income_type"];
        $IncomeAmt = $data["income_amt"];
        $PurchAmt = $data["total_amt_in_mt"];
        $discountAMT = $data["total_disc_in_mt"];
        $cgstamt = $data["total_cgst_amt"];
        $sgstamt = $data["total_sgst_amt"];
        $igstamt = $data["total_igst_amt"];
        $roundoffamt = $data["total_roundoff_amt"];
        $invoiceamt = $data["netpayableamt"];
        $InvData = $this->GetPurchaseInvoiceDetails($Inv_No);
        $CenterID = $InvData->CenterID;
        $Transdate = substr($InvData->Inv_date, 0, 19);
        $ItCount = count($es_detail);
        $nextPaymentnumber = get_option("next_payment_number_for_kirti");
        if ($PurchaseType == 2) {
            $PaymentMode = null;
            $Effecton = null;
            $PaymentMethod = null;
            $Refno = null;
        }
        if ($PurchaseType == 1) {
            $PaymentNo = $nextPaymentnumber;
        } else {
            $PaymentNo = null;
        }

        // rate update code new ============
        foreach ($es_detail as $value) {
            $productId = $value["ItemID"];
            $qty = $value["Qty"];
            $PurchRate = $value["PurchRate"];
            $gst = $value["GST"];
            $salerate = $PurchRate + $PurchRate * ($gst / 100);
            $ItemAmt = $qty * $PurchRate;
            $ItemDisc = 0;
            $DiscPer = 0;
            $UnitDisc = 0;
            if ($value["Discount"] > 0 && $qty > 0) {
                $ItemDisc = $value["Discount"] * $qty;
                $UnitDisc = $value["Discount"];
                $DiscPer = ($value["Discount"] / $PurchRate) * 100;
            }
            $ItemTaxableAmt = $ItemAmt - $ItemDisc;
            $ItemGSTAmt = $ItemTaxableAmt * ($gst / 100);
            // $CGSTPer = null;
            // $SGSTPer = null;
            // $IGSTPer = null;
            // $CGSTAmt = 0;
            // $SGSTAmt = 0;
            // $IGSTAmt = 0;
            // if ($CenterState == $State) {
            //     $CGSTPer = $gst / 2;
            //     $SGSTPer = $gst / 2;
            //     $CGSTAmt = $ItemGSTAmt / 2;
            //     $SGSTAmt = $ItemGSTAmt / 2;
            // } else {
            //     $IGSTPer = $gst;
            //     $IGSTAmt = $ItemGSTAmt;
            // }

            $data_array_result = [
                "PurchRate" => $PurchRate,
                "SaleRate" => $salerate,
                "BasicRate" => $PurchRate,
                "DiscPerc" => $DiscPer,
                "DiscAmt" => $UnitDisc,
                "OrderAmt" => $ItemAmt,
                "ChallanAmt" => $ItemAmt,
                "UserID2" => $_SESSION["username"],
                "Lupdate" => date("Y-m-d H:i:s"),
            ];
            $this->db->where("TransID", $Inv_No);
            $this->db->where("OrderQty", $qty);
            $this->db->where("ItemID", $productId);
            $this->db->where("AccountID", $VendorID);
            // $this->db->where("BatchNo", $value["BatchNo"]);
            $this->db->update(db_prefix() . "K1history", $data_array_result);
        }
        // rate update code new ============
        
        $this->db->select("tblK1purchasemaster.*");
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->where(db_prefix() . "K1purchasemaster.Inv_No", $Inv_No);
        $purchaselist = $this->db->get()->row();
        $data_array = [
            "Is_Ledger" => "Y",
            "InvoiceNo" => $VendorDocNo,
            "PaymentNo" => $PaymentNo,
            "PurchaseType" => $PurchaseType,
            "PaymentMode" => $PaymentMode,
            "PaymentMethod" => $PaymentMethod,
            "RefNo" => $Refno,
						"Invamt" => $invoiceamt,
            "EffectOn" => $Effecton,
            "ItCount" => $ItCount,
            "Lupdate" => date("Y-m-d H:i:s"),
            "UserID2" => $this->session->userdata("username"),
        ];
        $this->db->where("PlantID", $selected_company);
        $this->db->LIKE("FY", $fy);
        $this->db->where("Inv_No", $Inv_No);
        $this->db->update(db_prefix() . "K1purchasemaster", $data_array);
        if ($this->db->affected_rows() > 0) {
					// save expense entry
					if(count($ExpenceType) > 0){
						for($i=0; $i<count($ExpenceType); $i++){
							if(empty($ExpenceAmt[$i]) || $ExpenceAmt[$i] == 0){
								continue;
							}
							$expence_entry = [
								"PlantID" => $selected_company,
								"FY" => $fy,
								"Transdate" => $Transdate,
								"UserID" => $_SESSION["username"],
								"Inv_No" => $Inv_No,
								"LedgerCategory" => 'Direct Expense',
								"LedgerType" => $ExpenceType[$i],
								"Amount" => $ExpenceAmt[$i],
							];
							$where = [
								"PlantID" => $selected_company,
								"FY" => $fy,
								"Inv_No" => $Inv_No,
								"LedgerCategory" => 'Direct Expense',
								"LedgerType" => $ExpenceType[$i],
							];
							if($this->db->get_where(db_prefix() . "K1PurchaseMasterExpenses", $where)->num_rows() > 0){
								$this->db->where($where);
								$this->db->update(db_prefix() . "K1PurchaseMasterExpenses", $expence_entry);
							}else{
								$this->db->insert(db_prefix() . "K1PurchaseMasterExpenses", $expence_entry);
							}
						}
						// delete expense entry
						$this->db->where("PlantID", $selected_company);
						$this->db->where("FY", $fy);
						$this->db->where("Inv_No", $Inv_No);
						$this->db->where("LedgerCategory", 'Direct Expense');
						$this->db->where_not_in("LedgerType", $ExpenceType);
						$this->db->delete(db_prefix() . "K1PurchaseMasterExpenses");
					}
					// save income entry
					if(count($IncomeType) > 0){
						for($i=0; $i<count($IncomeType); $i++){
							if(empty($IncomeAmt[$i]) || $IncomeAmt[$i] == 0){
								continue;
							}
							$income_entry = [
								"PlantID" => $selected_company,
								"FY" => $fy,
								"Transdate" => $Transdate,
								"UserID" => $_SESSION["username"],
								"Inv_No" => $Inv_No,
								"LedgerCategory" => 'Direct Income',
								"LedgerType" => $IncomeType[$i],
								"Amount" => $IncomeAmt[$i],
							];
							$where = [
								"PlantID" => $selected_company,
								"FY" => $fy,
								"Inv_No" => $Inv_No,
								"LedgerCategory" => 'Direct Income',
								"LedgerType" => $IncomeType[$i],
							];
							// echo print_r($income_entry); die;
							if($this->db->get_where(db_prefix() . "K1PurchaseMasterExpenses", $where)->num_rows() > 0){
								$this->db->where($where);
								$this->db->update(db_prefix() . "K1PurchaseMasterExpenses", $income_entry);
							}else{
								$this->db->insert(db_prefix() . "K1PurchaseMasterExpenses", $income_entry);
							}
						}
						// delete income entry
						$this->db->where("PlantID", $selected_company);
						$this->db->where("FY", $fy);
						$this->db->where("Inv_No", $Inv_No);
						$this->db->where("LedgerCategory", 'Direct Income');
						$this->db->where_not_in("LedgerType", $IncomeType);
						$this->db->delete(db_prefix() . "K1PurchaseMasterExpenses");
					}
            // Move Ledger data from ledger table to ledger history table
            $GetLedgerList = $this->GetLedgerListByVoucher($Inv_No);
            $GetLedgerListByPaymentNo = $this->GetLedgerListByPayment(
                $purchaselist->PaymentNo
            );
            foreach ($GetLedgerList as $key => $val) {
                $ledger_audit = [
                    "PlantID" => $val["PlantID"],
                    "FY" => $val["FY"],
                    "Transdate" => $val["Transdate"],
                    "TransDate2" => $val["TransDate2"],
                    "VoucherID" => $val["VoucherID"],
                    "PartyID" => $val["PartyID"],
                    "AccountID" => $val["AccountID"],
                    "CounterAccount" => $val["CounterAccount"],
                    "CenterID" => $val["CenterID"],
                    "CommodityID" => $val["CommodityID"],
                    "EntryFor" => $val["EntryFor"],
                    "TType" => $val["TType"],
                    "Amount" => $val["Amount"],
                    "Narration" => $val["Narration"],
                    "PassedFrom" => $val["PassedFrom"],
                    "OrdinalNo" => $val["OrdinalNo"],
                    "UserID" => $val["UserID"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                    "UserID2" => $this->session->userdata("username"),
                ];
                $this->db->insert(
                    db_prefix() . "accountledgeraudit",
                    $ledger_audit
                );
            }
            foreach ($GetLedgerListByPaymentNo as $key => $val1) {
                $ledger_auditPayment = [
                    "PlantID" => $val1["PlantID"],
                    "FY" => $val1["FY"],
                    "Transdate" => $val1["Transdate"],
                    "TransDate2" => $val1["TransDate2"],
                    "VoucherID" => $val1["VoucherID"],
                    "PartyID" => $val1["PartyID"],
                    "AccountID" => $val1["AccountID"],
                    "CounterAccount" => $val1["CounterAccount"],
                    "CenterID" => $val1["CenterID"],
                    "CommodityID" => $val1["CommodityID"],
                    "EntryFor" => $val1["EntryFor"],
                    "TType" => $val1["TType"],
                    "Amount" => $val1["Amount"],
                    "Narration" => $val1["Narration"],
                    "PassedFrom" => $val1["PassedFrom"],
                    "OrdinalNo" => $val1["OrdinalNo"],
                    "UserID" => $val1["UserID"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                    "UserID2" => $this->session->userdata("username"),
                ];
                $this->db->insert(
                    db_prefix() . "accountledgeraudit",
                    $ledger_auditPayment
                );
            }
            // Delete Previous ledger data
            $this->db->where("PlantID", $selected_company);
            $this->db->where("FY", $fy);
            $this->db->where("VoucherID", $Inv_No);
            $this->db->delete(db_prefix() . "accountledger");
            $this->db->where("PlantID", $selected_company);
            $this->db->where("FY", $fy);
            $this->db->where("VoucherID", $purchaselist->PaymentNo);
            $this->db->where("PassedFrom", "PAYMENTS");
            $this->db->delete(db_prefix() . "accountledger");
            //Ledger entry code
            $ord_n = 1;
            $narrations = "By Purchase no." . $Inv_No;
            // Credit to Vendor
            $ledger_credit = [
                "PlantID" => $selected_company,
                "FY" => $fy,
                "Transdate" => $Transdate,
                "TransDate2" => date("Y-m-d H:i:s"),
                "VoucherID" => $Inv_No,
                "PartyID" => "KASPL",
                "AccountID" => $VendorID,
                "CounterAccount" => "PURCH",
                "CenterID" => $CenterID,
                "EntryFor" => "2",
                "TType" => "C",
                "Amount" => $invoiceamt,
                "Narration" => $narrations,
                "PassedFrom" => "PURCHASE",
                "OrdinalNo" => $ord_n,
                "UserID" => $_SESSION["username"],
            ];
            $this->db->insert(db_prefix() . "accountledger", $ledger_credit);
            $ord_n++;
            // Debit to Purchase Account
            $ledger_debit = [
                "PlantID" => $selected_company,
                "FY" => $fy,
                "Transdate" => $Transdate,
                "TransDate2" => date("Y-m-d H:i:s"),
                "VoucherID" => $Inv_No,
                "PartyID" => "KASPL",
                "AccountID" => "PURCH",
                "CounterAccount" => $VendorID,
                "CenterID" => $CenterID,
                "EntryFor" => "2",
                "TType" => "D",
                "Amount" => $PurchAmt,
                "Narration" => $narrations,
                "PassedFrom" => "PURCHASE",
                "OrdinalNo" => $ord_n,
                "UserID" => $_SESSION["username"],
            ];
            $this->db->insert(db_prefix() . "accountledger", $ledger_debit);
            $ord_n++;
            //Debit to Tax Account
            if ($cgstamt != 0.0 && $sgstamt != 0.0) {
                //CGST Tax Ledger Entry
                $Cgst_Ledger_entry = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "CGST",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $data["total_cgst_amt"],
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $CgstLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $Cgst_Ledger_entry
                );
                $ord_n++;
                //SGST Tax Ledger Entry
                $Sgst_Ledger_entry = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "SGST",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $data["total_sgst_amt"],
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $SgstLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $Sgst_Ledger_entry
                );
                $ord_n++;
            } elseif ($igstamt != 0.0) {
                //Igst Ledger Entry
                $Igst_Ledger_Entry = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "IGST",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $data["total_igst_amt"],
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $IgstLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $Igst_Ledger_Entry
                );
                $ord_n++;
            }
						$ord_n++;
						// expense ledger entry
						if(count($ExpenceType) > 0){
							for($i=0; $i<count($ExpenceType); $i++){
								if(empty($ExpenceAmt[$i]) || $ExpenceAmt[$i] == 0){
									continue;
								}
								$ExpenceLedgerEntry = [
									"PlantID" => $selected_company,
									"FY" => $fy,
									"Transdate" => $Transdate,
									"TransDate2" => date("Y-m-d H:i:s"),
									"VoucherID" => $Inv_No,
									"PartyID" => "KASPL",
									"AccountID" => $ExpenceType[$i],
									"CounterAccount" => $VendorID,
									"CenterID" => $CenterID,
									"EntryFor" => "2",
									"TType" => "D",
									"Amount" => $ExpenceAmt[$i],
									"Narration" => $narrations,
									"PassedFrom" => "PURCHASE",
									"OrdinalNo" => $ord_n,
									"UserID" => $_SESSION["username"],
								];
								$ExpenseLedgerEntry = $this->db->insert(db_prefix() . "accountledger", $ExpenceLedgerEntry);
								$ord_n++;
							}
						}
						// income ledger entry
						if(count($IncomeType) > 0){
							for($i=0; $i<count($IncomeType); $i++){
								if(empty($IncomeAmt[$i]) || $IncomeAmt[$i] == 0){
									continue;
								}
								$IncomeLedgerEntry = [
									"PlantID" => $selected_company,
									"FY" => $fy,
									"Transdate" => $Transdate,
									"TransDate2" => date("Y-m-d H:i:s"),
									"VoucherID" => $Inv_No,
									"PartyID" => "KASPL",
									"AccountID" => $IncomeType[$i],
									"CounterAccount" => $VendorID,
									"CenterID" => $CenterID,
									"EntryFor" => "2",
									"TType" => "C",
									"Amount" => $IncomeAmt[$i],
									"Narration" => $narrations,
									"PassedFrom" => "PURCHASE",
									"OrdinalNo" => $ord_n,
									"UserID" => $_SESSION["username"],
								];
								$IncomeLedgerEntry = $this->db->insert(db_prefix() . "accountledger", $IncomeLedgerEntry);
								$ord_n++;
							}
						}
            //Debit to Discount Ledger Entry
            if ($discountAMT > 0) {
                $disc_ledger_entry = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "DISC",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "C",
                    "Amount" => $discountAMT,
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $DiscountLedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $disc_ledger_entry
                );
                $ord_n++;
            }
            //Debit to RoundAmt Ledger Entry
            if ($roundoffamt >= 0) {
                $roundledgerentry_debit = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "ROUNDOFF",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "C",
                    "Amount" => $roundoffamt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $Round_Debit_LedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $roundledgerentry_debit
                );
                $ord_n++;
            } else {
                $amt = abs($roundoffamt);
                $roundledgerentry_credit = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $Inv_No,
                    "PartyID" => "KASPL",
                    "AccountID" => "ROUNDOFF",
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $amt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PURCHASE",
                    "OrdinalNo" => $ord_n,
                    "UserID" => $_SESSION["username"],
                ];
                $Round_credit_LedgerEntry = $this->db->insert(
                    db_prefix() . "accountledger",
                    $roundledgerentry_credit
                );
                $ord_n++;
            }
            //$nextPaymentnumber = get_option('next_payment_number_for_kirti');
            if ($PurchaseType == 1) {
                $ordinalno = 1;
                //Payment Voucher Debit Entry to Company
                $paymententry_Debit_tocompany = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $PaymentNo,
                    "PartyID" => "KASPL",
                    "AccountID" => $VendorID,
                    "CounterAccount" => $Effecton,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "D",
                    "Amount" => $invoiceamt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PAYMENTS",
                    "OrdinalNo" => $ordinalno,
                    "UserID" => $_SESSION["username"],
                ];
                $DebitToCompany = $this->db->insert(
                    db_prefix() . "accountledger",
                    $paymententry_Debit_tocompany
                );
                $ordinalno++;
                //Payment Voucher Credit Entry to Vendor
                $paymententry_Credit_tovendor = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "Transdate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "VoucherID" => $PaymentNo,
                    "PartyID" => "KASPL",
                    "AccountID" => $Effecton,
                    "CounterAccount" => $VendorID,
                    "CenterID" => $CenterID,
                    "EntryFor" => "2",
                    "TType" => "C",
                    "Amount" => $invoiceamt,
                    "Narration" => $narrations,
                    "PassedFrom" => "PAYMENTS",
                    "OrdinalNo" => $ordinalno,
                    "UserID" => $_SESSION["username"],
                ];
                $CreditToVendor = $this->db->insert(
                    db_prefix() . "accountledger",
                    $paymententry_Credit_tovendor
                );
                $this->increment_next_number("next_payment_number_for_kirti");
            }
        }
        return true;
    }
    public function CancelPILedgerEntryByPIID($PurchInvoiceID)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $InvData = $this->GetPurchaseInvoiceDetails($PurchInvoiceID);
        $CenterID = $InvData->CenterID;
        $this->db->select("tblK1purchasemaster.*");
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->where(
            db_prefix() . "K1purchasemaster.Inv_No",
            $PurchInvoiceID
        );
        $purchaselist = $this->db->get()->row();
        $data_array = [
            "Is_Ledger" => "N",
            "PaymentNo" => null,
            "PurchaseType" => null,
            "PaymentMode" => null,
            "PaymentMethod" => null,
            "RefNo" => null,
            "EffectOn" => null,
            "Lupdate" => date("Y-m-d H:i:s"),
            "UserID2" => $this->session->userdata("username"),
        ];
        $this->db->where("PlantID", $selected_company);
        $this->db->LIKE("FY", $fy);
        $this->db->where("Inv_No", $PurchInvoiceID);
        $this->db->update(db_prefix() . "K1purchasemaster", $data_array);
        if ($this->db->affected_rows() > 0) {
            // Move Ledger data from ledger table to ledger history table
            $GetLedgerList = $this->GetLedgerListByVoucher($PurchInvoiceID);
            $GetLedgerListByPaymentNo = $this->GetLedgerListByPayment(
                $purchaselist->PaymentNo
            );
            foreach ($GetLedgerList as $key => $val) {
                $ledger_audit = [
                    "PlantID" => $val["PlantID"],
                    "FY" => $val["FY"],
                    "Transdate" => $val["Transdate"],
                    "TransDate2" => $val["TransDate2"],
                    "VoucherID" => $val["VoucherID"],
                    "PartyID" => $val["PartyID"],
                    "AccountID" => $val["AccountID"],
                    "CounterAccount" => $val["CounterAccount"],
                    "CenterID" => $val["CenterID"],
                    "CommodityID" => $val["CommodityID"],
                    "EntryFor" => $val["EntryFor"],
                    "TType" => $val["TType"],
                    "Amount" => $val["Amount"],
                    "Narration" => $val["Narration"],
                    "PassedFrom" => $val["PassedFrom"],
                    "OrdinalNo" => $val["OrdinalNo"],
                    "UserID" => $val["UserID"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                    "UserID2" => $this->session->userdata("username"),
                ];
                $this->db->insert(
                    db_prefix() . "accountledgeraudit",
                    $ledger_audit
                );
            }
            foreach ($GetLedgerListByPaymentNo as $key => $val1) {
                $ledger_auditPayment = [
                    "PlantID" => $val1["PlantID"],
                    "FY" => $val1["FY"],
                    "Transdate" => $val1["Transdate"],
                    "TransDate2" => $val1["TransDate2"],
                    "VoucherID" => $val1["VoucherID"],
                    "PartyID" => $val1["PartyID"],
                    "AccountID" => $val1["AccountID"],
                    "CounterAccount" => $val1["CounterAccount"],
                    "CenterID" => $val1["CenterID"],
                    "CommodityID" => $val1["CommodityID"],
                    "EntryFor" => $val1["EntryFor"],
                    "TType" => $val1["TType"],
                    "Amount" => $val1["Amount"],
                    "Narration" => $val1["Narration"],
                    "PassedFrom" => $val1["PassedFrom"],
                    "OrdinalNo" => $val1["OrdinalNo"],
                    "UserID" => $val1["UserID"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                    "UserID2" => $this->session->userdata("username"),
                ];
                $this->db->insert(
                    db_prefix() . "accountledgeraudit",
                    $ledger_auditPayment
                );
            }
            // Delete Previous ledger data
            $this->db->where("PlantID", $selected_company);
            $this->db->where("FY", $fy);
            $this->db->where("VoucherID", $PurchInvoiceID);
            $this->db->delete(db_prefix() . "accountledger");
            $this->db->where("PlantID", $selected_company);
            $this->db->where("FY", $fy);
            $this->db->where("VoucherID", $purchaselist->PaymentNo);
            $this->db->where("PassedFrom", "PAYMENTS");
            $this->db->delete(db_prefix() . "accountledger");
            return true;
        } else {
            return false;
        }
    }
    
    public function CancelPurchaseInvoiceByPIID($PurchInvoiceID)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $InvData = $this->GetPurchaseInvoiceDetails($PurchInvoiceID);
        $CenterID = $InvData->CenterID;
        $this->db->select("tblK1purchasemaster.*");
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->where(
            db_prefix() . "K1purchasemaster.Inv_No",
            $PurchInvoiceID
        );
        $purchaselist = $this->db->get()->row();
        $data_array = [
            "OrderStatus" => "P",
            "Is_Ledger" => "N",
            "PaymentNo" => null,
            "PurchaseType" => null,
            "PaymentMode" => null,
            "PaymentMethod" => null,
            "RefNo" => null,
            "EffectOn" => null,
            "Lupdate" => date("Y-m-d H:i:s"),
            "UserID2" => $this->session->userdata("username"),
        ];
        $this->db->where([
            "PlantID" => $selected_company,
            "FY" => $fy,
            "Inv_No" => $PurchInvoiceID,
        ]);
        $this->db->update(db_prefix() . "K1purchasemaster", $data_array);
        if ($this->db->affected_rows() > 0) {
            // Move Ledger data from ledger table to ledger history table
            $GetLedgerList = $this->GetLedgerListByVoucher($PurchInvoiceID);
            $GetLedgerListByPaymentNo = $this->GetLedgerListByPayment(
                $purchaselist->PaymentNo
            );
            foreach ($GetLedgerList as $key => $val) {
                $ledger_audit = [
                    "PlantID" => $val["PlantID"],
                    "FY" => $val["FY"],
                    "Transdate" => $val["Transdate"],
                    "TransDate2" => $val["TransDate2"],
                    "VoucherID" => $val["VoucherID"],
                    "PartyID" => $val["PartyID"],
                    "AccountID" => $val["AccountID"],
                    "CounterAccount" => $val["CounterAccount"],
                    "CenterID" => $val["CenterID"],
                    "CommodityID" => $val["CommodityID"],
                    "EntryFor" => $val["EntryFor"],
                    "TType" => $val["TType"],
                    "Amount" => $val["Amount"],
                    "Narration" => $val["Narration"],
                    "PassedFrom" => $val["PassedFrom"],
                    "OrdinalNo" => $val["OrdinalNo"],
                    "UserID" => $val["UserID"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                    "UserID2" => $this->session->userdata("username"),
                ];
                $this->db->insert(
                    db_prefix() . "accountledgeraudit",
                    $ledger_audit
                );
            }
            foreach ($GetLedgerListByPaymentNo as $key => $val1) {
                $ledger_auditPayment = [
                    "PlantID" => $val1["PlantID"],
                    "FY" => $val1["FY"],
                    "Transdate" => $val1["Transdate"],
                    "TransDate2" => $val1["TransDate2"],
                    "VoucherID" => $val1["VoucherID"],
                    "PartyID" => $val1["PartyID"],
                    "AccountID" => $val1["AccountID"],
                    "CounterAccount" => $val1["CounterAccount"],
                    "CenterID" => $val1["CenterID"],
                    "CommodityID" => $val1["CommodityID"],
                    "EntryFor" => $val1["EntryFor"],
                    "TType" => $val1["TType"],
                    "Amount" => $val1["Amount"],
                    "Narration" => $val1["Narration"],
                    "PassedFrom" => $val1["PassedFrom"],
                    "OrdinalNo" => $val1["OrdinalNo"],
                    "UserID" => $val1["UserID"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                    "UserID2" => $this->session->userdata("username"),
                ];
                $this->db->insert(
                    db_prefix() . "accountledgeraudit",
                    $ledger_auditPayment
                );
            }
            // Delete Previous ledger data
            $this->db->where([
                "PlantID" => $selected_company,
                "FY" => $fy,
                "VoucherID" => $PurchInvoiceID
            ]);
            $this->db->delete(db_prefix() . "accountledger");

            $this->db->where([
                "PlantID" => $selected_company,
                "FY" => $fy,
                "VoucherID" => $purchaselist->PaymentNo,
                "PassedFrom" => "PAYMENTS"
            ]);
            $this->db->delete(db_prefix() . "accountledger");
            return true;
        } else {
            return false;
        }
    }

    public function UpdateKirtiOneInward($data, $id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "Qty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            $header[] = "BatchNo";
            $header[] = "ExpDate";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "") {
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $PurchID = $id;
        $AccountID = $data["vendor"];
        $CenterID = $data["centername"];
        $new_date = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $purchAmt = $data["total_amt_in_mt"];
        $Discamt = $data["total_disc_in_mt"];
        $cgstamt = $data["total_cgst_amt"];
        $sgstamt = $data["total_sgst_amt"];
        $igstamt = $data["total_igst_amt"];
        $Frtamt = $data["Freight_AMT"];
        $Othamt = $data["Other_amt"];
        $RoundOffAmt = $data["total_roundoff_amt"];
        $Invamt = $data["netpayableamt"];
        $ItCount = count($es_detail);
        $InvNo = $data["Invno"];
        $InvDate = to_sql_date($data["inv_date"]) . " " . date("H:i:s");
        $Drivername = $data["drivername"];
        $DriverNo = $data["driverno"];
        $vehicleNo = $data["VehicleNo"];
        $EwayBill = $data["ewaybillno"];
        $EntryType = $data["entrytype"];
        $this->db->select("tblK1Inwardmaster.*");
        $this->db->from(db_prefix() . "K1Inwardmaster");
        $this->db->where(db_prefix() . "K1Inwardmaster.PurchID", $PurchID);
        $purchaselist = $this->db->get()->row();
        $TType = "I";
        $TType2 = "INWARD";
        $data_array = [
            "Transdate" => $new_date,
            "OrderStatus" => "F",
            "CenterID" => $CenterID,
            "AccountID" => $AccountID,
            "Purchamt" => $purchAmt,
            "Discamt" => $Discamt,
            "cgstamt" => $cgstamt,
            "sgstamt" => $sgstamt,
            "igstamt" => $igstamt,
            "Frtamt" => $Frtamt,
            "Othamt" => $Othamt,
            "RoundOffAmt" => $RoundOffAmt,
            "Invamt" => $Invamt,
            "ItCount" => $ItCount,
            "EwayBillNo" => $EwayBill,
            "VehicleNo" => $vehicleNo,
            "DriverName" => $Drivername,
            "DriverNo" => $DriverNo,
            "Lupdate" => date("Y-m-d H:i:s"),
            "UserID2" => $this->session->userdata("username"),
        ];
        $this->db->where("PlantID", $selected_company);
        $this->db->LIKE("FY", $fy);
        $this->db->where("PurchID", $PurchID);
        $this->db->update(db_prefix() . "K1Inwardmaster", $data_array);
        if ($this->db->affected_rows() > 0) {
            $old_pur_details = $this->PurchaseModel->get_purchase_detail(
                $PurchID
            );
            // Move record from tblK1history to tblK1history_audit
            foreach ($old_pur_details as $key => $value) {
                if ($value["igst"] == null) {
                    $value["igst"] = "";
                    $value["igstamt"] = "";
                } elseif ($value["cgst"] == null) {
                    $value["cgst"] = "";
                    $value["cgstamt"] = "";
                    $value["sgst"] = "";
                    $value["sgstamt"] = "";
                }
                $old_data = [
                    "PlantID" => $value["PlantID"],
                    "FY" => $value["FY"],
                    "OrderID" => $value["OrderID"],
                    "BillID" => $value["BillID"],
                    "TransID" => $value["TransID"],
                    "TransDate" => $value["TransDate"],
                    "TransDate2" => $value["TransDate2"],
                    "TType" => $value["TType"],
                    "TType2" => $value["TType2"],
                    "AccountID" => $value["AccountID"],
                    "ItemID" => $value["ItemID"],
                    //'TypeID'=>$value["TypeID"],
                    "CenterID" => $value["CenterID"],
                    "GodownID" => $value["GodownID"],
                    "PartyID" => $value["PartyID"],
                    "PurchRate" => $value["PurchRate"],
                    "SaleRate" => $value["SaleRate"],
                    "BasicRate" => $value["BasicRate"],
                    "SuppliedIn" => $value["SuppliedIn"],
                    "OrderQty" => $value["OrderQty"],
                    "eOrderQty" => $value["eOrderQty"],
                    "BilledQty" => $value["BilledQty"],
                    "DiscPerc" => $value["DiscPerc"],
                    "DiscAmt" => $value["DiscAmt"],
                    "cgst" => $value["cgst"],
                    "cgstamt" => $value["cgstamt"],
                    "sgst" => $value["sgst"],
                    "sgstamt" => $value["sgstamt"],
                    "igst" => $value["igst"],
                    "igstamt" => $value["igstamt"],
                    "CaseQty" => $value["CaseQty"],
                    "Cases" => $value["Cases"],
                    "OrderAmt" => $value["OrderAmt"],
                    "ChallanAmt" => $value["ChallanAmt"],
                    "NetOrderAmt" => $value["NetOrderAmt"],
                    "NetChallanAmt" => $value["NetChallanAmt"],
                    "Ordinalno" => $value["Ordinalno"],
                    "UserID" => $value["UserID"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                    "UserID2" => $_SESSION["username"],
                ];
                $this->db->insert(db_prefix() . "K1history_audit", $old_data);
            }
            // Delete Live history table record
            $this->db->where("PlantID", $selected_company);
            $this->db->where("FY", $fy);
            $this->db->where("OrderID", $PurchID);
            $this->db->delete(db_prefix() . "K1history");
            // Add New history detail record
            $i = 1;
            foreach ($es_detail as $value) {
                $productId = $value["ItemID"];
                $brand = $value["Brand"];
                $unit = $value["MeasuredIn"];
                $packing_qty = $value["PackingQty"];
                $packing_weight = $value["PackingWeight"];
                $saleunit = $value["PurchaseUnit"];
                $qty = $value["Qty"];
                $amount = $value["PurchRate"];
                $discount = $value["Discount"];
                $gst = $value["GST"];
                $cgstamts = $value["CGSTAMT"];
                $sgstamts = $value["SGSTAMT"];
                $igstamts = $value["IGSTAMT"];
                $netAmount = $value["total_money"];
                $batchno = $value["BatchNo"];
                $expdate = to_sql_date($value["ExpDate"]);
                if ($saleunit == $unit) {
                    $orderquantity = $packing_qty * $qty;
                    $totalAmount = $qty * $amount;
                } else {
                    $orderquantity = $qty;
                    $amountval = ($amount / $packing_qty) * $qty;
                    $totalAmount = $amountval;
                }
                $discountAmount = ($discount / 100) * $totalAmount;
                $finalOrderAmt = $totalAmount - $discountAmount;
                if ($gst != "") {
                    if ($cgstamts > 0 && $sgstamts > 0) {
                        $cgst = $cgstamts;
                        $sgst = $sgstamts;
                        $cgstPercentage = ($cgst / $finalOrderAmt) * 100;
                        $sgstPercentage = $cgstPercentage;
                        $totalPercentage = $cgstPercentage + $sgstPercentage;
                        $salerate = $amount * (1 + $totalPercentage / 100);
                        $igst = 0;
                        $igstPercentage = 0;
                    } elseif ($igstamts > 0) {
                        $igst = $igstamts;
                        $igstPercentage = ($igst / $finalOrderAmt) * 100;
                        $salerate = $amount * (1 + $igstPercentage / 100);
                    }
                }
                if ($saleunit == "Loose") {
                    $caseqty = 1;
                } else {
                    $caseqty = $packing_qty;
                }
                $data_array_result = [
                    "PlantID" => $selected_company,
                    "FY" => $fy,
                    "OrderID" => $PurchID,
                    "BillID" => $PurchID,
                    "TransID" => $PurchID,
                    "TransDate" => $new_date,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "TType" => $TType,
                    "TType2" => $TType2,
                    "AccountID" => $AccountID,
                    "ItemID" => $productId,
                    "CenterID" => $CenterID,
                    "PartyID" => "KASPL",
                    "PurchRate" => $amount,
                    "SaleRate" => $salerate,
                    "BasicRate" => $amount,
                    "SuppliedIn" => $saleunit,
                    "OrderQty" => $orderquantity,
                    "BilledQty" => $orderquantity,
                    "DiscPerc" => $discount,
                    "DiscAmt" => $discountAmount,
                    "cgst" => $cgstPercentage,
                    "cgstamt" => $cgst,
                    "sgst" => $sgstPercentage,
                    "sgstamt" => $sgst,
                    "igst" => $igstPercentage,
                    "igstamt" => $igst,
                    "CaseQty" => $caseqty,
                    "Cases" => 0.0,
                    "OrderAmt" => $totalAmount,
                    "ChallanAmt" => $totalAmount,
                    "NetOrderAmt" => $netAmount,
                    "NetChallanAmt" => $netAmount,
                    "Ordinalno" => $i,
                    "UserID" => $_SESSION["username"],
                    "BatchNo" => $batchno,
                    "ExpDate" => $expdate,
                ];
                $this->db->insert(
                    db_prefix() . "K1history",
                    $data_array_result
                );
                $i++;
            }
        }
        return true;
    }
    public function load_data_for_purchasekirtione($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $UserID = $this->session->userdata("username");
        $sql1 =
            "(" .
            db_prefix() .
            'K1purchasemaster.Transdate BETWEEN "' .
            $from_date .
            ' 00:00:00" AND "' .
            $to_date .
            ' 23:59:59")
			AND tblK1purchasemaster.FY = "' .
            $fy .
            '"
			AND tblK1purchasemaster.PlantID = "' .
            $selected_company .
            '"
			';
        $join = "";
        if (!is_admin()) {
            $join .=
                " INNER JOIN tblstaff_wise_center ON tblstaff_wise_center.CenterID = tblK1purchasemaster.CenterID ";
            $sql1 .= ' AND tblstaff_wise_center.AccountID = "' . $UserID . '"';
        }
        $sql1 .= " ORDER BY PurchID DESC";
        $sql =
            "SELECT " .
            db_prefix() .
            'K1purchasemaster.*,
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' .
            db_prefix() .
            "clients WHERE " .
            db_prefix() .
            "clients.AccountID = " .
            db_prefix() .
            "K1purchasemaster.AccountID AND " .
            db_prefix() .
            "clients.PlantID = " .
            $selected_company .
            ') as AccountName
			FROM ' .
            db_prefix() .
            "K1purchasemaster " .
            $join .
            " WHERE " .
            $sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    public function load_data_for_inwardkirtione($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $sql1 =
            "(" .
            db_prefix() .
            'K1Inwardmaster.Transdate BETWEEN "' .
            $from_date .
            ' 00:00:00" AND "' .
            $to_date .
            ' 23:59:59")
			AND tblK1Inwardmaster.FY = "' .
            $fy .
            '"
			AND tblK1Inwardmaster.PlantID = "' .
            $selected_company .
            '"
			ORDER BY PurchID DESC';
        $sql =
            "SELECT " .
            db_prefix() .
            'K1Inwardmaster.*,
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' .
            db_prefix() .
            "clients WHERE " .
            db_prefix() .
            "clients.AccountID = " .
            db_prefix() .
            "K1Inwardmaster.AccountID AND " .
            db_prefix() .
            "clients.PlantID = " .
            $selected_company .
            ') as AccountName
			FROM ' .
            db_prefix() .
            "K1Inwardmaster WHERE " .
            $sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    public function load_data_for_demandlists($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $center_id = $data["centername"];
        $this->db->select("tblDemandList.*,tblCenterMaster.CenterName");
        $this->db->from(db_prefix() . "DemandList");
        $this->db->join(
            db_prefix() . "CenterMaster",
            "CenterMaster.CenterID = tblDemandList.CenterID"
        );
        $this->db->where(
            "tblDemandList.TransDate >=",
            $from_date . " 00:00:00"
        );
        $this->db->where("tblDemandList.TransDate <=", $to_date . " 23:59:59");
        if (!empty($center_id)) {
            $this->db->where("tblDemandList.CenterID", $center_id);
        }
        return $this->db->get()->result_array();
    }
    public function load_data_for_demandReport_list($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $center_id = $data["centername"];
        $this->db->select("tblDemandList.*,tblCenterMaster.CenterName");
        $this->db->from(db_prefix() . "DemandList");
        $this->db->join(
            db_prefix() . "CenterMaster",
            "CenterMaster.CenterID = tblDemandList.CenterID"
        );
        $this->db->where(
            "tblDemandList.TransDate >=",
            $from_date . " 00:00:00"
        );
        $this->db->where("tblDemandList.TransDate <=", $to_date . " 23:59:59");
        if (!empty($center_id)) {
            $this->db->where("tblDemandList.CenterID", $center_id);
        }
        return $this->db->get()->result_array();
    }
    public function load_data_for_purchase_requestkirtione($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $UserID = $this->session->userdata("username");
        $sql1 =
            "(" .
            db_prefix() .
            'K1purchase_request_master.Transdate BETWEEN "' .
            $from_date .
            ' 00:00:00" AND "' .
            $to_date .
            ' 23:59:59")
			AND tblK1purchase_request_master.FY = "' .
            $fy .
            '"
			AND tblK1purchase_request_master.PlantID = "' .
            $selected_company .
            '"
			';
        $join = "";
        $join .=
            " INNER JOIN tblCenterMaster ON tblCenterMaster.CenterID = tblK1purchase_request_master.CenterID ";
        if (!is_admin()) {
            $join .=
                " INNER JOIN tblstaff_wise_center ON tblstaff_wise_center.CenterID = tblK1purchase_request_master.CenterID ";
            $sql1 .= ' AND tblstaff_wise_center.AccountID = "' . $UserID . '"';
        }
        $sql1 .= " ORDER BY PurchID DESC";
        $sql =
            "SELECT " .
            db_prefix() .
            'K1purchase_request_master.*,tblCenterMaster.CenterName,
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' .
            db_prefix() .
            "clients WHERE " .
            db_prefix() .
            "clients.AccountID = " .
            db_prefix() .
            "K1purchase_request_master.AccountID AND " .
            db_prefix() .
            "clients.PlantID = " .
            $selected_company .
            ') as AccountName
			FROM ' .
            db_prefix() .
            "K1purchase_request_master " .
            $join .
            " WHERE " .
            $sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    public function filter_data_for_purchase_order_kirtione($data)
    {
        $from_date = to_sql_date($data['from_date']) . ' 00:00:00';
        $to_date = to_sql_date($data['to_date']) . ' 23:59:59';

        $fy = $this->session->userdata('finacial_year');
        $plantId = $this->session->userdata('root_company');
        $userId = $this->session->userdata('username');

        $this->db->select('
            pm.*,
            cm.CenterName,
            GROUP_CONCAT(c.company ORDER BY c.company SEPARATOR ",") AS AccountName
        ', false);

        $this->db->from('tblK1PurchaseOrderMaster pm');

        $this->db->join('tblCenterMaster cm', 'cm.CenterID = pm.CenterID', 'inner');

        $this->db->join(
            'tblclients c',
            'c.AccountID = pm.AccountID AND c.PlantID = '.$this->db->escape($plantId),
            'left',
            false
        );

        if (!is_admin()) {
            $this->db->join(
                'tblstaff_wise_center swc',
                'swc.CenterID = pm.CenterID',
                'inner'
            );
            $this->db->where('swc.AccountID', $userId);
        }

        $this->db->where('pm.Transdate >=', $from_date);
        $this->db->where('pm.Transdate <=', $to_date);
        $this->db->where('pm.FY', $fy);
        $this->db->where('pm.PlantID', $plantId);

        $this->db->group_by('pm.PurchID');
        $this->db->order_by('pm.PurchID', 'DESC');

        return $this->db->get()->result_array();
    }

    public function filter_data_for_purchase_inward_kirtione($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $UserID = $this->session->userdata("username");
        $sql1 =
            "(" .
            db_prefix() .
            'K1purchasemaster.Transdate BETWEEN "' .
            $from_date .
            ' 00:00:00" AND "' .
            $to_date .
            ' 23:59:59")
			AND tblK1purchasemaster.FY = "' .
            $fy .
            '" AND tblK1purchasemaster.Flag = "Y" AND tblK1purchasemaster.Inv_No IS NOT NULL
			AND tblK1purchasemaster.PlantID = "' .
            $selected_company .
            '"
			';
        $join = "";
        $join .=
            " INNER JOIN tblCenterMaster ON tblCenterMaster.CenterID = tblK1purchasemaster.CenterID ";
        if (!is_admin()) {
            $join .=
                " INNER JOIN tblstaff_wise_center ON tblstaff_wise_center.CenterID = tblK1purchasemaster.CenterID ";
            $sql1 .= ' AND tblstaff_wise_center.AccountID = "' . $UserID . '"';
        }
        $sql1 .= " ORDER BY PurchID DESC";
        $sql =
            "SELECT " .
            db_prefix() .
            'K1purchasemaster.*,tblCenterMaster.CenterName,
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' .
            db_prefix() .
            "clients WHERE " .
            db_prefix() .
            "clients.AccountID = " .
            db_prefix() .
            "K1purchasemaster.AccountID AND " .
            db_prefix() .
            "clients.PlantID = " .
            $selected_company .
            ') as AccountName
			FROM ' .
            db_prefix() .
            "K1purchasemaster " .
            $join .
            " WHERE " .
            $sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    public function load_data_for_purchase_order_kirtione($data)
    {
        $from_date = to_sql_date($data['from_date']) . ' 00:00:00';
        $to_date = to_sql_date($data['to_date']) . ' 23:59:59';

        $fy = $this->session->userdata('finacial_year');
        $plantId = $this->session->userdata('root_company');
        $userId = $this->session->userdata('username');

        $this->db->select('
            pm.*,
            cm.CenterName,
            GROUP_CONCAT(c.company ORDER BY c.company SEPARATOR ",") AS AccountName
        ', false);

        $this->db->from('tblK1purchasemaster pm');

        $this->db->join('tblCenterMaster cm', 'cm.CenterID = pm.CenterID', 'inner');

        $this->db->join(
            'tblclients c',
            'c.AccountID = pm.AccountID AND c.PlantID = '.$this->db->escape($plantId),
            'left',
            false
        );

        if (!is_admin()) {
            $this->db->join(
                'tblstaff_wise_center swc',
                'swc.CenterID = pm.CenterID',
                'inner'
            );
            $this->db->where('swc.AccountID', $userId);
        }

        $this->db->where('pm.Transdate >=', $from_date);
        $this->db->where('pm.Transdate <=', $to_date);
        $this->db->where('pm.FY', $fy);
        $this->db->where('pm.PlantID', $plantId);

        $this->db->group_by('pm.PurchID');
        $this->db->order_by('pm.PurchID', 'DESC');

        return $this->db->get()->result_array();
    }
    public function load_data_for_purchase_invoice_kirtione($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $UserID = $this->session->userdata("username");
        $sql1 =
            "(" .
            db_prefix() .
            'K1purchasemaster.Transdate BETWEEN "' .
            $from_date .
            ' 00:00:00" AND "' .
            $to_date .
            ' 23:59:59")
			AND tblK1purchasemaster.FY = "' .
            $fy .
            '" AND tblK1purchasemaster.Flag = "Y" AND tblK1purchasemaster.Inv_No IS NOT NULL
			AND tblK1purchasemaster.PlantID = "' .
            $selected_company .
            '"
			';
        $join = "";
        $join .=
            " INNER JOIN tblCenterMaster ON tblCenterMaster.CenterID = tblK1purchasemaster.CenterID ";
        if (!is_admin()) {
            $join .=
                " INNER JOIN tblstaff_wise_center ON tblstaff_wise_center.CenterID = tblK1purchasemaster.CenterID ";
            $sql1 .= ' AND tblstaff_wise_center.AccountID = "' . $UserID . '"';
        }
        $sql1 .= " ORDER BY PurchID DESC";
        $sql =
            "SELECT " .
            db_prefix() .
            'K1purchasemaster.*,tblCenterMaster.CenterName,
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' .
            db_prefix() .
            "clients WHERE " .
            db_prefix() .
            "clients.AccountID = " .
            db_prefix() .
            "K1purchasemaster.AccountID AND " .
            db_prefix() .
            "clients.PlantID = " .
            $selected_company .
            ') as AccountName
			FROM ' .
            db_prefix() .
            "K1purchasemaster " .
            $join .
            " WHERE " .
            $sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    public function GetCenterList()
    {
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $UserID = $this->session->userdata("username");
        $join = "";
        $sql1 = " tblCenterMaster.status = 'Y'";
        if (!is_admin()) {
            $join .=
                " INNER JOIN tblstaff_wise_center ON tblstaff_wise_center.CenterID = tblCenterMaster.CenterID ";
            $sql1 .= ' AND tblstaff_wise_center.AccountID = "' . $UserID . '"';
        }
        $sql1 .= " ORDER BY tblCenterMaster.CenterName ASC";
        $sql =
            "SELECT " .
            db_prefix() .
            'CenterMaster.*
			FROM ' .
            db_prefix() .
            "CenterMaster " .
            $join .
            " WHERE " .
            $sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    public function load_data_for_purchase_return_invoice_kirtione($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $sql1 =
            "(" .
            db_prefix() .
            'K1purchasereturn.Transdate BETWEEN "' .
            $from_date .
            ' 00:00:00" AND "' .
            $to_date .
            ' 23:59:59")
			AND tblK1purchasereturn.FY = "' .
            $fy .
            '" AND tblK1purchasereturn.PurchRtnID IS NOT NULL
			AND tblK1purchasereturn.PlantID = "' .
            $selected_company .
            '"
			ORDER BY PurchRtnID DESC';
        $sql =
            "SELECT " .
            db_prefix() .
            'K1purchasereturn.*,
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' .
            db_prefix() .
            "clients WHERE " .
            db_prefix() .
            "clients.AccountID = " .
            db_prefix() .
            "K1purchasereturn.AccountID AND " .
            db_prefix() .
            "clients.PlantID = " .
            $selected_company .
            ') as AccountName
			FROM ' .
            db_prefix() .
            "K1purchasereturn WHERE " .
            $sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    public function load_data_for_purchase_invoice_ledger_kirtione($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $UserID = $this->session->userdata("username");
        $sql1 =
            "(" .
            db_prefix() .
            'K1purchasemaster.Transdate BETWEEN "' .
            $from_date .
            ' 00:00:00" AND "' .
            $to_date .
            ' 23:59:59")
			AND tblK1purchasemaster.FY = "' .
            $fy .
            '" AND tblK1purchasemaster.Flag = "Y" AND tblK1purchasemaster.Is_Ledger = "Y" AND tblK1purchasemaster.Inv_No IS NOT NULL
			AND tblK1purchasemaster.PlantID = "' .
            $selected_company .
            '"
			';
        $join = "";
        $join .=
            " INNER JOIN tblCenterMaster ON tblCenterMaster.CenterID = tblK1purchasemaster.CenterID ";
        if (!is_admin()) {
            $join .=
                " INNER JOIN tblstaff_wise_center ON tblstaff_wise_center.CenterID = tblK1purchasemaster.CenterID ";
            $sql1 .= ' AND tblstaff_wise_center.AccountID = "' . $UserID . '"';
        }
        $sql1 .= " ORDER BY PurchID DESC";
        $sql =
            "SELECT " .
            db_prefix() .
            'K1purchasemaster.*,tblCenterMaster.CenterName,
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' .
            db_prefix() .
            "clients WHERE " .
            db_prefix() .
            "clients.AccountID = " .
            db_prefix() .
            "K1purchasemaster.AccountID AND " .
            db_prefix() .
            "clients.PlantID = " .
            $selected_company .
            ') as AccountName
			FROM ' .
            db_prefix() .
            "K1purchasemaster " .
            $join .
            " WHERE " .
            $sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    public function load_data_for_purchase_pending_invoice_ledger_kirtione(
        $data
    ) {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $UserID = $this->session->userdata("username");
        $sql1 =
            "(" .
            db_prefix() .
            'K1purchasemaster.Transdate BETWEEN "' .
            $from_date .
            ' 00:00:00" AND "' .
            $to_date .
            ' 23:59:59")
			AND tblK1purchasemaster.FY = "' .
            $fy .
            '" AND tblK1purchasemaster.Flag = "Y" AND tblK1purchasemaster.Is_Ledger = "N" AND tblK1purchasemaster.Inv_No IS NOT NULL
			AND tblK1purchasemaster.PlantID = "' .
            $selected_company .
            '"
			ORDER BY PurchID DESC';
        $join = "";
        $join .=
            " INNER JOIN tblCenterMaster ON tblCenterMaster.CenterID = tblK1purchasemaster.CenterID ";
        if (!is_admin()) {
            $join .=
                " INNER JOIN tblstaff_wise_center ON tblstaff_wise_center.CenterID = tblK1purchasemaster.CenterID ";
            $sql1 .= ' AND tblstaff_wise_center.AccountID = "' . $UserID . '"';
        }
        $sql =
            "SELECT " .
            db_prefix() .
            'K1purchasemaster.*,tblCenterMaster.CenterName,
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' .
            db_prefix() .
            "clients WHERE " .
            db_prefix() .
            "clients.AccountID = " .
            db_prefix() .
            "K1purchasemaster.AccountID AND " .
            db_prefix() .
            "clients.PlantID = " .
            $selected_company .
            ') as AccountName
			FROM ' .
            db_prefix() .
            "K1purchasemaster " .
            $join .
            " WHERE " .
            $sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    public function GetPurchaseDetails($PONumber)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");
        $this->db->select(
            "tblK1purchasemaster.*,tblclients.company,tblclients.phonenumber,tblclients.state,tblxx_statelist.state_name, SUM(tblK1history.OrderQty) AS TotalOrderQty,(tblK1purchasemaster.Purchamt - tblK1purchasemaster.Discamt) AS taxable_amt,tblCenterMaster.CenterName"
        );
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->join(
            db_prefix() . "clients",
            "tblclients.AccountID = tblK1purchasemaster.AccountID AND tblclients.PlantID = tblK1purchasemaster.PlantID"
        );
        $this->db->join(
            db_prefix() . "xx_statelist",
            db_prefix() .
                "xx_statelist.short_name = " .
                db_prefix() .
                "clients.state",
            "left"
        );
        $this->db->join(
            db_prefix() . "CenterMaster",
            db_prefix() .
                "CenterMaster.CenterID = " .
                db_prefix() .
                "K1purchasemaster.CenterID",
            "left"
        );
        $this->db->join(
            db_prefix() . "K1history",
            "tblK1history.OrderID = tblK1purchasemaster.PurchID",
            "left"
        );
        $this->db->where(db_prefix() . "K1purchasemaster.PurchID", $PONumber);
        $this->db->where(
            db_prefix() . "K1purchasemaster.PlantID",
            $selected_company
        );
        $this->db->where(db_prefix() . "K1purchasemaster.FY", $year);
        return $this->db->get()->row();
    }
    public function GetInwardDetails($PONumber)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");
        $this->db->select(
            'tblK1Inwardmaster.*,tblclients.company,center_state.state_name AS StateCenter,tblclients.phonenumber,tblclients.state,tblxx_statelist.state_name, SUM(tblK1history.OrderQty) AS TotalOrderQty,(tblK1Inwardmaster.Purchamt - tblK1Inwardmaster.Discamt) AS taxable_amt,tblCenterMaster.CenterName,CONCAT_WS(", ", tblclients.house, tblclients.street, tblclients.loc,tblclients.vtc, tblclients.po) AS VendorAddress'
        );
        $this->db->from(db_prefix() . "K1Inwardmaster");
        $this->db->join(
            db_prefix() . "clients",
            "tblclients.AccountID = tblK1Inwardmaster.AccountID AND tblclients.PlantID = tblK1Inwardmaster.PlantID"
        );
        $this->db->join(
            db_prefix() . "xx_statelist",
            db_prefix() .
                "xx_statelist.short_name = " .
                db_prefix() .
                "clients.state",
            "left"
        );
        $this->db->join(
            db_prefix() . "CenterMaster",
            db_prefix() .
                "CenterMaster.CenterID = " .
                db_prefix() .
                "K1Inwardmaster.CenterID",
            "left"
        );
        $this->db->join(
            db_prefix() . "xx_statelist as center_state",
            "center_state.short_name = " . db_prefix() . "CenterMaster.state",
            "left"
        );
        $this->db->join(
            db_prefix() . "K1history",
            "tblK1history.OrderID = tblK1Inwardmaster.PurchID",
            "left"
        );
        $this->db->where(db_prefix() . "K1Inwardmaster.PurchID", $PONumber);
        $this->db->where(
            db_prefix() . "K1Inwardmaster.PlantID",
            $selected_company
        );
        $this->db->where(db_prefix() . "K1Inwardmaster.FY", $year);
        return $this->db->get()->row();
    }
    public function GetPurchaseRequestDetails($PRNumber)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");
        $this->db->select(
            'tblK1purchase_request_master.*, tblclients.company, tblclients.phonenumber, tblclients.state, tblxx_statelist.state_name, SUM(tblK1history.OrderQty) AS TotalOrderQty, (tblK1purchase_request_master.Purchamt - tblK1purchase_request_master.Discamt) AS taxable_amt, tblCenterMaster.CenterName, CenterState.state_name AS StateCenter, tblGstRecord.gstin AS gst, GROUP_CONCAT(DISTINCT CONCAT(tblclients.house, ", ", tblclients.street, ", ", tblclients.loc, ", ", tblclients.vtc, ", ", tblxx_statelist.state_name, " - ", tblxx_citylist.city_name)) AS VendorAddress'
        );
        $this->db->from(db_prefix() . "K1purchase_request_master");
        $this->db->join(
            db_prefix() . "clients",
            "tblclients.AccountID = tblK1purchase_request_master.AccountID AND tblclients.PlantID = tblK1purchase_request_master.PlantID"
        );
        $this->db->join(
            db_prefix() . "CenterMaster",
            db_prefix() .
                "CenterMaster.CenterID = " .
                db_prefix() .
                "K1purchase_request_master.CenterID",
            "left"
        );
        $this->db->join(
            db_prefix() . "xx_statelist as CenterState",
            "CenterState.short_name = tblCenterMaster.state",
            "left"
        );
        $this->db->join(
            "tblxx_citylist",
            "tblxx_citylist.id = tblclients.dist",
            "LEFT"
        );
        $this->db->join(
            "tblGstRecord",
            'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"',
            "LEFT"
        );
        $this->db->join(
            db_prefix() . "xx_statelist",
            db_prefix() .
                "xx_statelist.short_name = " .
                db_prefix() .
                "clients.state",
            "left"
        );
        $this->db->join(
            db_prefix() . "K1history",
            "tblK1history.OrderID = tblK1purchase_request_master.PurchID",
            "left"
        );
        $this->db->where(
            db_prefix() . "K1purchase_request_master.PurchID",
            $PRNumber
        );
        $this->db->where(
            db_prefix() . "K1purchase_request_master.PlantID",
            $selected_company
        );
        $this->db->where(db_prefix() . "K1purchase_request_master.FY", $year);
        return $this->db->get()->row();
    }
    public function GetPurchaseRequestItemList($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $this->db->select(
            "tblK1history.*,tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit AS Measuredin,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscAmt AS Discount,tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand"
        );
        $this->db->from(db_prefix() . "K1history");
        $this->db->join(
            db_prefix() . "product",
            "tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID"
        );
        $this->db->join(db_prefix() . "taxes", "tbltaxes.id = tblproduct.gst");
        $this->db->join(
            db_prefix() . "brands",
            "tblbrands.id = tblproduct.BrandId"
        );
        $this->db->where(db_prefix() . "K1history.OrderID", $id);
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        $this->db->where(db_prefix() . "K1history.FY", $fy);
        $this->db->order_by(db_prefix() . "K1history.Ordinalno");
        $results = $this->db->get()->result_array();
        foreach ($results as &$row) {
            if ($row["PackingQty"] == 1) {
                $row["OrderQty"] = $row["OrderQty"];
            } else {
                $row["OrderQty"] = $row["OrderQty"] / $row["PackingQty"];
            }
            $row["Discount"] = $row["Discount"] / $row["OrderQty"];
        }
        return $results;
    }
    public function GetPurchaseRequestItemListInvoiceAdd($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $this->db->select(
            "tblK1history.*,tblproduct.ProductName,tblproduct.unit AS Measuredin,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscAmt AS Discount,tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand"
        );
        $this->db->from(db_prefix() . "K1history");
        $this->db->join(
            db_prefix() . "product",
            "tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID"
        );
        $this->db->join(db_prefix() . "taxes", "tbltaxes.id = tblproduct.gst");
        $this->db->join(
            db_prefix() . "brands",
            "tblbrands.id = tblproduct.BrandId"
        );
        $this->db->where(db_prefix() . "K1history.OrderID", $id);
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        $this->db->where(db_prefix() . "K1history.FY", $fy);
        $results = $this->db->get()->result_array();
        foreach ($results as &$row) {
            if ($row["PackingQty"] == 1) {
                $row["OrderQty"] = $row["OrderQty"];
            } else {
                $row["OrderQty"] = $row["OrderQty"] / $row["PackingQty"];
            }
            $row["Discount"] = $row["Discount"] / $row["OrderQty"];
        }
        return $results;
    }
    public function GetPurchaseOrderDetails($PONumber)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");
        $this
            ->db->select('tblK1purchasemaster.*,tblclients.company,tblclients.phonenumber,tblclients.state,tblxx_statelist.state_name,
			SUM(tblK1history.OrderQty) AS TotalOrderQty,(tblK1purchasemaster.Purchamt - tblK1purchasemaster.Discamt) AS taxable_amt,
			tblCenterMaster.CenterName, tblCenterMaster.state AS CenterStateID,CenterState.state_name AS StateCenter,tblGstRecord.gstin AS gst,
			GROUP_CONCAT(DISTINCT CONCAT(tblclients.house, ", ", tblclients.street, ", ", tblclients.loc, ", ", tblclients.vtc, ", ", tblxx_statelist.state_name, " - ", tblxx_citylist.city_name)) AS VendorAddress');
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->join(
            db_prefix() . "clients",
            "tblclients.AccountID = tblK1purchasemaster.AccountID AND tblclients.PlantID = tblK1purchasemaster.PlantID"
        );
        $this->db->join(
            "tblGstRecord",
            'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"',
            "LEFT"
        );
        $this->db->join(
            db_prefix() . "xx_statelist",
            db_prefix() .
                "xx_statelist.short_name = " .
                db_prefix() .
                "clients.state",
            "left"
        );
        $this->db->join(
            "tblxx_citylist",
            "tblxx_citylist.id = tblclients.dist",
            "LEFT"
        );
        $this->db->join(
            db_prefix() . "CenterMaster",
            db_prefix() .
                "CenterMaster.CenterID = " .
                db_prefix() .
                "K1purchasemaster.CenterID",
            "left"
        );
        $this->db->join(
            db_prefix() . "xx_statelist as CenterState",
            "CenterState.short_name = tblCenterMaster.state",
            "left"
        );
        $this->db->join(
            db_prefix() . "K1history",
            "tblK1history.OrderID = tblK1purchasemaster.PurchID",
            "left"
        );
        $this->db->where(db_prefix() . "K1purchasemaster.PurchID", $PONumber);
        $this->db->where(
            db_prefix() . "K1purchasemaster.PlantID",
            $selected_company
        );
        $this->db->where(db_prefix() . "K1purchasemaster.FY", $year);
        return $this->db->get()->row();
    }

    public function GetPODetails($PONumber)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");

        $this->db->select("
            pom.*,
            c.company,
            c.phonenumber,
            c.state,
            sl.state_name,
            SUM(kh.OrderQty) AS TotalOrderQty,
            (pom.Purchamt - pom.Discamt) AS taxable_amt,
            cm.CenterName,
            cm.state AS CenterStateID,
            CenterState.state_name AS StateCenter,
            gst.gstin AS gst,
            GROUP_CONCAT(
                DISTINCT CONCAT(
                    c.house, ', ',
                    c.street, ', ',
                    c.loc, ', ',
                    c.vtc, ', ',
                    sl.state_name, ' - ',
                    city_list.city_name
                )
            ) AS VendorAddress
        ", false);

        $this->db->from(db_prefix() . "K1PurchaseOrderMaster AS pom");

        $this->db->join(
            db_prefix() . "clients AS c",
            "c.AccountID = pom.AccountID AND c.PlantID = pom.PlantID"
        );

        $this->db->join(
            "tblGstRecord AS gst",
            "gst.AccountID = c.AccountID AND gst.IsPrimary = '1'",
            "left"
        );

        $this->db->join(
            db_prefix() . "xx_statelist AS sl",
            "sl.short_name = c.state",
            "left"
        );

        $this->db->join(
            "tblxx_citylist AS city_list",
            "city_list.id = c.dist",
            "left"
        );

        $this->db->join(
            db_prefix() . "CenterMaster AS cm",
            "cm.CenterID = pom.CenterID",
            "left"
        );

        $this->db->join(
            db_prefix() . "xx_statelist AS CenterState",
            "CenterState.short_name = cm.state",
            "left"
        );

        $this->db->join(
            db_prefix() . "K1history AS kh",
            "kh.OrderID = pom.PurchID",
            "left"
        );

        $this->db->where("pom.PurchID", $PONumber);
        $this->db->where("pom.PlantID", $selected_company);
        $this->db->where("pom.FY", $year);

        return $this->db->get()->row();
    }

    public function GetPurchaseOrderItemList($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $this->db->select(
            'tblK1history.*,tblproduct.hsn_code,tblproduct.ProductName,tblproduct.unit AS Measuredin,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscAmt AS Discount,tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand,
			(Select SUM(PRHistory.OrderQty/tblproduct.PackingQty) from tblK1history as PRHistory where PRHistory.ItemID = tblK1history.ItemID  AND PRHistory.OrderID = tblK1history.BillID AND PRHistory.TType ="P" AND PRHistory.TType2="Request") As PRQty,
			(Select SUM(RcvHistory.OrderQty/tblproduct.PackingQty) from tblK1history as RcvHistory where RcvHistory.ItemID = tblK1history.ItemID  AND RcvHistory.BillID = tblK1history.BillID AND RcvHistory.TType ="P" AND RcvHistory.TType2="Purchase" AND RcvHistory.OrderID != "' .
                $id .
                '") As PenQty'
        );
        $this->db->from(db_prefix() . "K1history");
        $this->db->join(
            db_prefix() . "product",
            "tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID"
        );
        $this->db->join(db_prefix() . "taxes", "tbltaxes.id = tblproduct.gst");
        $this->db->join(
            db_prefix() . "brands",
            "tblbrands.id = tblproduct.BrandId"
        );
        $this->db->where(db_prefix() . "K1history.OrderID", $id);
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        $this->db->where(db_prefix() . "K1history.FY", $fy);
        $this->db->where(db_prefix() . "K1history.TType", 'P');
        $this->db->where(db_prefix() . "K1history.TType2", 'Purchase Order');
        $this->db->order_by(db_prefix() . "K1history.Ordinalno", "ASC");
        $results = $this->db->get()->result_array();
        foreach ($results as &$row) {
            $row["PendingQty"] = $row["PRQty"] - $row["PenQty"];
            if ($row["PackingQty"] == 1) {
                $row["OrderQty"] = $row["OrderQty"];
            } else {
                $row["OrderQty"] = $row["OrderQty"] / $row["PackingQty"];
            }
            $row['PurchRate']   = $row['PurchRate'] * $row['PackingQty'];
            $row['SaleRate']    = $row['SaleRate'] * $row['PackingQty'];
            $row['BasicRate']   = $row['BasicRate'] * $row['PackingQty'];
        }
        return $results;
    }
    public function GetPurchaseOrderInwardItemListByPrNo($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $this->db->select(
            "tblK1history.ItemID,tblproduct.ProductName,SUM(tblK1history.BilledQty) as BilledQty,tblproduct.unit AS Measuredin"
        );
        $this->db->from(db_prefix() . "K1history");
        $this->db->join(
            db_prefix() . "product",
            "tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID"
        );
        $this->db->where(db_prefix() . "K1history.BillID", $id);
        $this->db->where(db_prefix() . "K1history.TType", "P");
        $this->db->where(db_prefix() . "K1history.TType2", "Purchase");
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        $this->db->where(db_prefix() . "K1history.FY", $fy);
        $this->db->group_by(db_prefix() . "K1history.ItemID");
        $results = $this->db->get()->result_array();
        return $results;
    }
    public function GetPurchaseInvoiceDetails($PINumber)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");
        $this->db->select('tblK1purchasemaster.*,tblclients.company,tblclients.phonenumber,tblclients.state,
			tblxx_statelist.state_name, SUM(tblK1history.OrderQty) AS TotalOrderQty,
			(tblK1purchasemaster.Purchamt - tblK1purchasemaster.Discamt) AS taxable_amt,tblCenterMaster.CenterName,tblCenterMaster.state AS CenterState,
			tblGstRecord.gstin AS gst, CenterState.state_name AS StateCenter, GROUP_CONCAT(DISTINCT CONCAT(tblclients.house, ", ", tblclients.street, ", ", tblclients.loc, ", ", tblclients.vtc, ", ", tblxx_statelist.state_name, " - ", tblxx_citylist.city_name)) AS VendorAddress');
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->join(
            db_prefix() . "clients",
            "tblclients.AccountID = tblK1purchasemaster.AccountID AND tblclients.PlantID = tblK1purchasemaster.PlantID"
        );
        $this->db->join(
            "tblGstRecord",
            'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"',
            "LEFT"
        );
        $this->db->join(
            db_prefix() . "xx_statelist",
            db_prefix() .
                "xx_statelist.short_name = " .
                db_prefix() .
                "clients.state",
            "left"
        );
        $this->db->join(
            "tblxx_citylist",
            "tblxx_citylist.id = tblclients.dist",
            "LEFT"
        );
        $this->db->join(
            db_prefix() . "CenterMaster",
            db_prefix() .
                "CenterMaster.CenterID = " .
                db_prefix() .
                "K1purchasemaster.CenterID",
            "left"
        );
        $this->db->join(
            db_prefix() . "xx_statelist as CenterState",
            "CenterState.short_name = tblCenterMaster.state",
            "left"
        );
        $this->db->join(
            db_prefix() . "K1history",
            "tblK1history.OrderID = tblK1purchasemaster.PurchID",
            "left"
        );
        $this->db->where(db_prefix() . "K1purchasemaster.Inv_No", $PINumber);
        $this->db->where(
            db_prefix() . "K1purchasemaster.PlantID",
            $selected_company
        );
        $this->db->where(db_prefix() . "K1purchasemaster.FY", $year);
        return $this->db->get()->row();
    }
    public function GetPurchaseInvoiceItemList($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $this->db->select(
            'tblK1history.*,tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit AS Measuredin,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscAmt AS Discount,tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand,
			(Select SUM(PRHistory.OrderQty/tblproduct.PackingQty) from tblK1history as PRHistory where PRHistory.ItemID = tblK1history.ItemID  AND PRHistory.OrderID = tblK1history.BillID AND PRHistory.TType ="P" AND PRHistory.TType2="Request") As PRQty,
			(Select SUM(RcvHistory.OrderQty/tblproduct.PackingQty) from tblK1history as RcvHistory where RcvHistory.ItemID = tblK1history.ItemID  AND RcvHistory.BillID = tblK1history.BillID AND RcvHistory.TType ="P" AND RcvHistory.TType2="Purchase" AND RcvHistory.OrderID != "' .
                $id .
                '") As PenQty,
			(Select SUM(SaleHistory.BilledQty) from tblK1history as SaleHistory where SaleHistory.ItemID = tblK1history.ItemID  AND SaleHistory.BatchNo = tblK1history.BatchNo AND SaleHistory.TType ="O" AND SaleHistory.TType2="SALE") AS SaleQty'
        );
        $this->db->from(db_prefix() . "K1history");
        $this->db->join(
            db_prefix() . "product",
            "tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID"
        );
        $this->db->join(db_prefix() . "taxes", "tbltaxes.id = tblproduct.gst");
        $this->db->join(
            db_prefix() . "brands",
            "tblbrands.id = tblproduct.BrandId"
        );
        $this->db->where(db_prefix() . "K1history.TransID", $id);
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        $this->db->where(db_prefix() . "K1history.FY", $fy);
        $this->db->order_by(db_prefix() . "K1history.Ordinalno", "ASC");
        $results = $this->db->get()->result_array();
        foreach ($results as &$row) {
            $row["PurchRate"] = $row["PurchRate"] * $row["PackingQty"];
            $row["SaleRate"] = $row["SaleRate"] * $row["PackingQty"];
            $row["BasicRate"] = $row["BasicRate"] * $row["PackingQty"];

            $row["PendingQty"] = $row["PRQty"] - $row["PenQty"];
            if ($row["PackingQty"] == 1) {
                $row["OrderQty"] = $row["OrderQty"];
            } else {
                $row["OrderQty"] = $row["OrderQty"] / $row["PackingQty"];
            }
            $row["Discount"] = $row["Discount"];
            $row["ExpDate"] = _d(substr($row["ExpDate"], 0, 10));

            // Purchase Order Qty
            $PO = $this->db->select('SUM(OrderQty) AS OrderQty, PurchRate')
                ->from('tblK1history')
                ->where([
                    'OrderID' => $row['OrderID'],
                    'ItemID' => $row['ItemID'],
                    'PlantID' => $selected_company,
                    'FY' => $fy,
                    'TType' => 'P',
                    'TType2' => 'Purchase Order'
                ])->get()->row();

            // // Total Inward Qty
            $rcvQty = $this->db->select_sum('OrderQty')
                ->from('tblK1history')
                ->where([
                    'OrderID' => $row['OrderID'],
                    'ItemID' => $row['ItemID'],
                    'PlantID' => $selected_company,
                    'FY' => $fy,
                    'TType' => 'P',
                    'TType2' => 'Purchase'
                ])
                ->get()
                ->row()
                ->OrderQty ?? 0;

            $rcvQty = (float)$rcvQty;

            $POorderQty = $PO->OrderQty - $rcvQty;
            // If using packing conversion
            if ($row["PackingQty"] > 1) {
                $POorderQty /= $row["PackingQty"];
                $rcvQty /= $row["PackingQty"];
            }

            $row['POrderQty'] = $POorderQty + $row["OrderQty"];
        }
        return $results;
    }
    public function GetPurchaseInvoiceItemListForQR($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $this->db->select(
            "k.id, k.PlantID, k.ItemID, k.BatchNo, k.ExpDate, k.CenterID, k.BilledQty, k.OrderQty, p.ProductName, p.Category"
        );
        $this->db->from(db_prefix() . "K1history as k");
        $this->db->join(
            db_prefix() . "product as p",
            "p.ProductID = k.ItemID AND p.PlantID = k.PlantID"
        );
        $this->db->where("k.TransID", $id);
        $this->db->where("k.PlantID", $selected_company);
        $this->db->where("k.FY", $fy);
        $this->db->order_by("k.Ordinalno", "ASC");
        $results = $this->db->get()->result_array();
        return $results;
    }
    public function GetPurchaseItemList($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $this->db->select(
            "tblK1history.*,tblproduct.ProductName,tblproduct.unit AS Measuredin,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscPerc AS Discount,tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand"
        );
        $this->db->from(db_prefix() . "K1history");
        $this->db->join(
            db_prefix() . "product",
            "tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID"
        );
        $this->db->join(db_prefix() . "taxes", "tbltaxes.id = tblproduct.gst");
        $this->db->join(
            db_prefix() . "brands",
            "tblbrands.id = tblproduct.BrandId"
        );
        $this->db->where(db_prefix() . "K1history.OrderID", $id);
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        $this->db->where(db_prefix() . "K1history.FY", $fy);
        $this->db->order_by(db_prefix() . "K1history.Ordinalno", "ASC");
        $results = $this->db->get()->result_array();
        foreach ($results as &$row) {
            if ($row["PackingQty"] == 1) {
                $row["OrderQty"] = $row["OrderQty"];
            } else {
                $row["OrderQty"] = $row["OrderQty"] / $row["PackingQty"];
            }
            $row["ExpDate"] = _d(substr($row["ExpDate"], 0, 10));
        }
        return $results;
    }
    public function GetItemDetails($ItemID)
    {
        $this->db->select(
            "tblproduct.*,tblproduct.ProductName,tblbrands.BrandName,tbltaxes.taxrate"
        );
        $this->db->from(db_prefix() . "product");
        $this->db->join(
            db_prefix() . "brands",
            db_prefix() . "brands.id = " . db_prefix() . "product.BrandId"
        );
        $this->db->join(
            db_prefix() . "taxes",
            db_prefix() . "taxes.id = " . db_prefix() . "product.gst"
        );
        $this->db->where(db_prefix() . "product.ProductID", $ItemID);
        $rs = $this->db->get()->row();
        if (!empty($rs)) {
            $rs->rate = 0.0;
        }
        return $rs;
    }


    public function GetItemDetailsPO($ItemID, $OrderID = null)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');

        $this->db->select('tblproduct.*, tblbrands.BrandName, tbltaxes.taxrate');
        $this->db->from('tblproduct');
        $this->db->join('tblbrands', 'tblbrands.id = tblproduct.BrandId');
        $this->db->join('tbltaxes', 'tbltaxes.id = tblproduct.gst');
        $this->db->where('tblproduct.ProductID', $ItemID);
        $rs = $this->db->get()->row();

        if (!empty($rs)) {

            // Purchase Order Qty
            $PO = $this->db->select('SUM(OrderQty) AS OrderQty, PurchRate')
                ->from('tblK1history')
                ->where([
                    'OrderID' => $OrderID,
                    'ItemID' => $ItemID,
                    'PlantID' => $selected_company,
                    'FY' => $fy,
                    'TType' => 'P',
                    'TType2' => 'Purchase Order'
                ])->get()->row();

            $rs->POrderQty = $PO->OrderQty;
            $rs->PurchRate = $PO->PurchRate;

            // // Total Inward Qty
            $rcvQty = $this->db->select_sum('OrderQty')
                ->from('tblK1history')
                ->where([
                    'OrderID' => $OrderID,
                    'ItemID' => $ItemID,
                    'PlantID' => $selected_company,
                    'FY' => $fy,
                    'TType' => 'P',
                    'TType2' => 'Purchase'
                ])
                ->get()
                ->row()
                ->OrderQty ?? 0;

            $rcvQty = (float)$rcvQty;

            $POorderQty = $PO->OrderQty - $rcvQty;
            // If using packing conversion
            if ($rs->PackingQty > 1) {
                $POorderQty /= $rs->PackingQty;
                $rcvQty /= $rs->PackingQty;
            }

            $rs->OrderQty = $PO->OrderQty;
            $rs->POrderQty = $POorderQty;
        }

        return $rs;
    }
    
    public function GetAccountList()
    {
        $this->db->select("tblclients.*,tblcontacts.*");
        $this->db->join(
            "tblCustomerType",
            "tblCustomerType.id = tblclients.CustomerType"
        );
        $this->db->join(
            "tblcontacts",
            "tblcontacts.AccountID = tblclients.AccountID"
        );
        $this->db->join(
            "tblxx_statelist",
            "tblxx_statelist.id = tblclients.state",
            "LEFT"
        );
        $this->db->join(
            "tblxx_citylist",
            "tblxx_citylist.id = tblclients.dist",
            "LEFT"
        );
        $this->db->where("tblclients.CustomerType", "3");
        $this->db->where("tblclients.IsKirtiOneAccess", "Y");
        $Data = $this->db->get("tblclients")->result_array();
        return $Data;
    }
    public function PendingInwardVendors()
    {
        $UserID = $this->session->userdata("username");
        $this->db->select(
            "tblclients.*,tblcontacts.*,tblK1purchase_request_master.*"
        );
        $this->db->join(
            "tblclients",
            "tblclients.AccountID = tblK1purchase_request_master.AccountID"
        );
        $this->db->join(
            "tblCustomerType",
            "tblCustomerType.id = tblclients.CustomerType"
        );
        $this->db->join(
            "tblcontacts",
            "tblcontacts.AccountID = tblclients.AccountID"
        );
        $this->db->join(
            "tblxx_statelist",
            "tblxx_statelist.id = tblclients.state",
            "LEFT"
        );
        $this->db->join(
            "tblxx_citylist",
            "tblxx_citylist.id = tblclients.dist",
            "LEFT"
        );
        if (!is_admin()) {
            $this->db->join(
                "tblstaff_wise_center",
                "tblstaff_wise_center.CenterID = tblK1purchase_request_master.CenterID"
            );
            $this->db->where("tblstaff_wise_center.AccountID", $UserID);
        }
        $this->db->where("tblclients.CustomerType", "3");
        $this->db->where("tblclients.IsKirtiOneAccess", "Y");
        $this->db->where("tblK1purchase_request_master.OrderStatus", "P");
        $this->db->group_by("tblK1purchase_request_master.AccountID");
        $Data = $this->db->get("tblK1purchase_request_master")->result_array();
        return $Data;
    }
    public function PendingInvoiceVendors()
    {
        $UserID = $this->session->userdata("username");
        $this->db->select("tblclients.*,tblcontacts.*,tblK1purchasemaster.*");
        $this->db->join(
            "tblclients",
            "tblclients.AccountID = tblK1purchasemaster.AccountID"
        );
        $this->db->join(
            "tblCustomerType",
            "tblCustomerType.id = tblclients.CustomerType"
        );
        $this->db->join(
            "tblcontacts",
            "tblcontacts.AccountID = tblclients.AccountID"
        );
        $this->db->join(
            "tblxx_statelist",
            "tblxx_statelist.id = tblclients.state",
            "LEFT"
        );
        $this->db->join(
            "tblxx_citylist",
            "tblxx_citylist.id = tblclients.dist",
            "LEFT"
        );
        if (!is_admin()) {
            $this->db->join(
                "tblstaff_wise_center",
                "tblstaff_wise_center.CenterID = tblK1purchasemaster.CenterID"
            );
            $this->db->where("tblstaff_wise_center.AccountID", $UserID);
        }
        $this->db->where("tblclients.CustomerType", "3");
        $this->db->where("tblclients.IsKirtiOneAccess", "Y");
        $this->db->where("tblK1purchasemaster.Flag", "Y");
        $this->db->where("tblK1purchasemaster.Inv_No IS NULL");
        $this->db->where("tblK1purchasemaster.OrderStatus", "A");
        $this->db->group_by("tblK1purchasemaster.AccountID");
        $Data = $this->db->get("tblK1purchasemaster")->result_array();
        return $Data;
    }
    
    public function PendingOrderVendors()
    {
        $UserID = $this->session->userdata("username");
        $this->db->select("tblclients.*,tblcontacts.*,tblK1PurchaseOrderMaster.*");
        $this->db->join(
            "tblclients",
            "tblclients.AccountID = tblK1PurchaseOrderMaster.AccountID"
        );
        $this->db->join(
            "tblCustomerType",
            "tblCustomerType.id = tblclients.CustomerType"
        );
        $this->db->join(
            "tblcontacts",
            "tblcontacts.AccountID = tblclients.AccountID"
        );
        $this->db->join(
            "tblxx_statelist",
            "tblxx_statelist.id = tblclients.state",
            "LEFT"
        );
        $this->db->join(
            "tblxx_citylist",
            "tblxx_citylist.id = tblclients.dist",
            "LEFT"
        );
        if (!is_admin()) {
            $this->db->join(
                "tblstaff_wise_center",
                "tblstaff_wise_center.CenterID = tblK1PurchaseOrderMaster.CenterID"
            );
            $this->db->where("tblstaff_wise_center.AccountID", $UserID);
        }
        $this->db->where("tblclients.CustomerType", "3");
        $this->db->where("tblclients.IsKirtiOneAccess", "Y");
        $this->db->where("tblK1PurchaseOrderMaster.Flag", "Y");
        $this->db->where_in("tblK1PurchaseOrderMaster.OrderStatus", ["A", "I"]);
        $this->db->group_by("tblK1PurchaseOrderMaster.AccountID");
        $Data = $this->db->get("tblK1PurchaseOrderMaster")->result_array();
        return $Data;
    }

    public function PendingInvoiceCenterwiseVendors($CenterID)
    {
        $this->db->select("tblclients.*,tblcontacts.*,tblK1purchasemaster.*");
        $this->db->join(
            "tblclients",
            "tblclients.AccountID = tblK1purchasemaster.AccountID"
        );
        $this->db->join(
            "tblCustomerType",
            "tblCustomerType.id = tblclients.CustomerType"
        );
        $this->db->join(
            "tblcontacts",
            "tblcontacts.AccountID = tblclients.AccountID"
        );
        $this->db->join(
            "tblxx_statelist",
            "tblxx_statelist.id = tblclients.state",
            "LEFT"
        );
        $this->db->join(
            "tblxx_citylist",
            "tblxx_citylist.id = tblclients.dist",
            "LEFT"
        );
        $this->db->where("tblclients.CustomerType", "3");
        $this->db->where("tblclients.IsKirtiOneAccess", "Y");
        $this->db->where("tblK1purchasemaster.Flag", "Y");
        $this->db->where("tblK1purchasemaster.Inv_No IS NULL");
        $this->db->where("tblK1purchasemaster.CenterID", $CenterID);
        $this->db->group_by("tblK1purchasemaster.AccountID");
        $Data = $this->db->get("tblK1purchasemaster")->result_array();
        return $Data;
    }
    public function PendingInvoiceLedgerVendors()
    {
        $UserID = $this->session->userdata("username");
        $this->db->select("tblclients.*,tblcontacts.*,tblK1purchasemaster.*");
        $this->db->join(
            "tblclients",
            "tblclients.AccountID = tblK1purchasemaster.AccountID"
        );
        $this->db->join(
            "tblCustomerType",
            "tblCustomerType.id = tblclients.CustomerType"
        );
        $this->db->join(
            "tblcontacts",
            "tblcontacts.AccountID = tblclients.AccountID"
        );
        $this->db->join(
            "tblxx_statelist",
            "tblxx_statelist.id = tblclients.state",
            "LEFT"
        );
        $this->db->join(
            "tblxx_citylist",
            "tblxx_citylist.id = tblclients.dist",
            "LEFT"
        );
        if (!is_admin()) {
            $this->db->join(
                "tblstaff_wise_center",
                "tblstaff_wise_center.CenterID = tblK1purchasemaster.CenterID"
            );
            $this->db->where("tblstaff_wise_center.AccountID", $UserID);
        }
        $this->db->where("tblclients.CustomerType", "3");
        $this->db->where("tblclients.IsKirtiOneAccess", "Y");
        $this->db->where("tblK1purchasemaster.Flag", "Y");
        $this->db->where("tblK1purchasemaster.Is_Ledger", "N");
        $this->db->where("tblK1purchasemaster.Inv_No IS NOT NULL");
        $this->db->group_by("tblK1purchasemaster.AccountID");
        $Data = $this->db->get("tblK1purchasemaster")->result_array();
        return $Data;
    }
    public function GetAccountListVendorwise($VendorID)
    {
        $FY = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $this->db->select(
            "tblclients.*,tblxx_statelist.state_name,tblxx_statelist.short_name,tblGstRecord.gstin"
        );
        $this->db->from(db_prefix() . "clients");
        $this->db->join(
            "tblxx_statelist",
            "tblxx_statelist.short_name = tblclients.state",
            "LEFT"
        );
        $this->db->join(
            "tblGstRecord",
            'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"',
            "LEFT"
        );
        $this->db->where(db_prefix() . "clients.AccountID", $VendorID);
        $Data = $this->db->get()->row();
        if ($Data) {
            $Data->Listitems = $this->GetVendorWiseItems($Data->AccountID);
            // Closing Bal
            // Get Opening balance
            $this->db->select("tblaccountbalances.*");
            $this->db->from(db_prefix() . "accountbalances");
            $this->db->where(
                db_prefix() . "accountbalances.AccountID",
                $VendorID
            );
            $this->db->where(
                db_prefix() . "accountbalances.PlantID",
                $selected_company
            );
            $this->db->where(db_prefix() . "accountbalances.FY", $FY);
            $this->db->where(db_prefix() . "accountbalances.PartyID", "KASPL");
            $OpnBalDetails = $this->db->get()->row();
            $OpnBal = 0;
            if ($OpnBalDetails) {
                $OpnBal = $OpnBalDetails->BAL1;
            }
            // Get Transaction Entry
            $this->db->select(
                "SUM(tblaccountledger.Amount) AS TotalAmt,tblaccountledger.TType"
            );
            $this->db->from(db_prefix() . "accountledger");
            $this->db->where(
                db_prefix() . "accountledger.AccountID",
                $VendorID
            );
            $this->db->where(
                db_prefix() . "accountledger.PlantID",
                $selected_company
            );
            $this->db->where(db_prefix() . "accountledger.FY", $FY);
            $this->db->where(db_prefix() . "accountledger.PartyID", "KASPL");
            $this->db->group_by(db_prefix() . "accountledger.TType");
            $LedgerDetails = $this->db->get()->result_array();
            $CreditAmt = 0;
            $DebitAmt = 0;
            foreach ($LedgerDetails as $key => $val) {
                if ($val["TType"] == "C") {
                    $CreditAmt = $val["TotalAmt"];
                } elseif ($val["TType"] == "D") {
                    $DebitAmt = $val["TotalAmt"];
                }
            }
            $ClosingBal = $OpnBal - $CreditAmt + $DebitAmt;
            $CRDR = "CR";
            if ($ClosingBal > 0) {
                $CRDR = "DR";
            }
            $Data->clsBal = $ClosingBal;
            $ClosingBal = abs($ClosingBal) . " " . $CRDR;
            $Data->ClosingBal = $ClosingBal;
        }
        return $Data;
    }
    public function CheckVendorDocNo($InvoiceID, $PurchID, $PurchInvoiceID)
    {
        $this->db->select("tblK1purchasemaster.*");
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->where(
            db_prefix() . "K1purchasemaster.InvoiceNo",
            $InvoiceID
        );
        if ($PurchID) {
            $this->db->where(
                db_prefix() . "K1purchasemaster.PurchID !=",
                $PurchID
            );
        }
        if ($PurchInvoiceID) {
            $this->db->where(
                db_prefix() . "K1purchasemaster.Inv_No !=",
                $PurchInvoiceID
            );
        }
        $Data = $this->db->get()->row();
        return $Data;
    }
    public function GetVendorWiseItems($PartyID)
    {
        /*$this->db->select('ProductID as id, CONCAT(ProductID," - ",ProductName) as label,ProductName ,ProductID');
			$this->db->where('tblproduct.ItemFor', $PartyID);
			$ProductList = $this->db->get('tblproduct')->result_array();
			*/
        $this->db->select(
            'tblk1ItemVendor.ItemID as id, CONCAT(tblk1ItemVendor.ItemID," - ",tblproduct.ProductName) as label,tblproduct.ProductName ,tblproduct.ProductID'
        );
        $this->db->join(
            "tblproduct",
            "tblproduct.ProductID = tblk1ItemVendor.ItemID"
        );
        $this->db->where("tblk1ItemVendor.VendorID", $PartyID);
        $ProductList = $this->db->get("tblk1ItemVendor")->result_array();
        return $ProductList;
    }
    public function getstatelist()
    {
        $Data = $this->db->get("tblxx_statelist")->result_array();
        return $Data;
    }
    public function increment_next_number($name)
    {
        $year = $this->session->userdata("finacial_year");
        $this->db->set("value", "value+1", false);
        $this->db->WHERE("name", $name);
        $this->db->WHERE("FY", $year);
        $this->db->update(db_prefix() . "options");
    }
    public function get_purchase_detail($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");
        $this->db->select();
        $this->db->from(db_prefix() . "K1history");
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        $this->db->where(db_prefix() . "K1history.FY", $year);
        $this->db->where(db_prefix() . "K1history.OrderID", $id);
        return $this->db->get()->result_array();
    }
    public function get_purchase_request_detail($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");
        $this->db->select();
        $this->db->from(db_prefix() . "K1history");
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        $this->db->where(db_prefix() . "K1history.FY", $year);
        $this->db->where(db_prefix() . "K1history.OrderID", $id);
        return $this->db->get()->result_array();
    }
    public function get_purchase_order_detail($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");
        $this->db->select();
        $this->db->from(db_prefix() . "K1history");
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        $this->db->where(db_prefix() . "K1history.FY", $year);
        $this->db->where(db_prefix() . "K1history.OrderID", $id);
        return $this->db->get()->result_array();
    }
    
    public function get_POH_detail($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");
        $this->db->select();
        $this->db->from(db_prefix() . "K1history");
        $this->db->where([
            "PlantID" => $selected_company,
            "FY" => $year,
            "OrderID" => $id,
            "TType" => "P",
            "TType2" => "Purchase Order"
        ]);
        return $this->db->get()->result_array();
    }

    public function GetLedgerListByVoucher($voucherID)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");
        $this->db->select("tblaccountledger.*");
        $this->db->from(db_prefix() . "accountledger");
        $this->db->where(
            db_prefix() . "accountledger.PlantID",
            $selected_company
        );
        $this->db->LIKE(db_prefix() . "accountledger.FY", $year);
        $this->db->where(db_prefix() . "accountledger.VoucherID", $voucherID);
        $this->db->order_by(db_prefix() . "accountledger.id", "ASC");
        return $this->db->get()->result_array();
    }
    public function GetLedgerListByPayment($PaymentNo)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");
        $this->db->select("tblaccountledger.*");
        $this->db->from(db_prefix() . "accountledger");
        $this->db->where(
            db_prefix() . "accountledger.PlantID",
            $selected_company
        );
        $this->db->LIKE(db_prefix() . "accountledger.FY", $year);
        $this->db->where(db_prefix() . "accountledger.VoucherID", $PaymentNo);
        $this->db->where(db_prefix() . "accountledger.PassedFrom", "PAYMENTS");
        $this->db->order_by(db_prefix() . "accountledger.id", "ASC");
        return $this->db->get()->result_array();
    }
    public function GetPurchOrderCenterList($LogInUser = "")
    {
        $this->db->select(
            "tblK1purchasemaster.PurchID,tblCenterMaster.CenterName,tblCenterMaster.CenterID"
        );
        $this->db->join(
            "tblCenterMaster",
            "tblCenterMaster.CenterID = tblK1purchasemaster.CenterID"
        );
        $this->db->where("tblK1purchasemaster.PurchID IS NOT NULL");
        if ($LogInUser) {
            $this->db->where("tblK1purchasemaster.AccountID", $LogInUser);
        }
        $this->db->group_by("tblCenterMaster.CenterID");
        return $this->db->get("tblK1purchasemaster")->result_array();
    }
    public function GetPurchOrderItemList($LogInUser = "")
    {
        $this->db->select(
            "tblK1history.ItemID,tblproduct.ProductName,tblproduct.ProductID"
        );
        $this->db->join(
            "tblproduct",
            "tblproduct.ProductID = tblK1history.ItemID"
        );
        $this->db->where("tblK1history.OrderID IS NOT NULL");
        if ($LogInUser) {
            $this->db->where("tblK1history.AccountID", $LogInUser);
        }
        $this->db->group_by("tblK1history.ItemID");
        return $this->db->get("tblK1history")->result_array();
    }
    public function GetPurchOrderPartyList()
    {
        $this->db->select(
            "tblK1purchasemaster.PurchID,tblclients.company,tblclients.AccountID"
        );
        $this->db->join(
            "tblclients",
            "tblclients.AccountID = tblK1purchasemaster.AccountID"
        );
        $this->db->where("tblK1purchasemaster.PurchID IS NOT NULL");
        $this->db->group_by("tblclients.AccountID");
        return $this->db->get("tblK1purchasemaster")->result_array();
    }
    public function getItemOrderDetailsDB($data)
    {
        $UserID = $this->session->userdata("username");
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        if ($data["Report_type"] == "1") {
            $this->db->select(
                "tblK1purchasemaster.*,tblCenterMaster.CenterName,tblCenterMaster.GSTNo,tblclients.company"
            );
            if (!is_admin()) {
                $this->db->join(
                    "tblstaff_wise_center",
                    "tblstaff_wise_center.CenterID = tblK1purchasemaster.CenterID"
                );
                $this->db->where("tblstaff_wise_center.AccountID", $UserID);
            }
            $this->db->where(
                'tblK1purchasemaster.Transdate BETWEEN "' .
                    $from_date .
                    ' 00:00:00" AND "' .
                    $to_date .
                    ' 23:59:59"'
            );
            if (!empty($data["order_status"])) {
                $this->db->where(
                    "tblK1purchasemaster.OrderStatus",
                    $data["order_status"]
                );
            }
            if (!empty($data["AccountID"])) {
                $this->db->where(
                    "tblK1purchasemaster.AccountID",
                    $data["AccountID"]
                );
            }
            if (!empty($data["CenterID"])) {
                $this->db->where(
                    "tblK1purchasemaster.CenterID",
                    $data["CenterID"]
                );
            }
            if (!empty($data["Entry_type"])) {
                $this->db->where(
                    "tblK1purchasemaster.EntryType",
                    $data["Entry_type"]
                );
            }
            $this->db->join(
                "tblCenterMaster",
                "tblCenterMaster.CenterID = tblK1purchasemaster.CenterID"
            );
            $this->db->join(
                "tblclients",
                "tblclients.AccountID = tblK1purchasemaster.AccountID"
            );
            $this->db->where("tblK1purchasemaster.PurchID IS NOT NULL");
            $this->db->order_by("tblK1purchasemaster.PurchID", "DESC");
            return $this->db->get("tblK1purchasemaster")->result_array();
        } else {
            $this->db
                ->select('tblK1history.*,tblK1purchasemaster.InvoiceNo,tblCenterMaster.CenterName,tblCenterMaster.GSTNo,tblK1purchasemaster.OrderStatus,
				tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit,tblproduct.PackingQty,tblclients.company,tblK1purchasemaster.PurchID,
				tblGstRecord.gstin');
            if (!is_admin()) {
                $this->db->join(
                    "tblstaff_wise_center",
                    "tblstaff_wise_center.CenterID = tblK1history.CenterID"
                );
                $this->db->where("tblstaff_wise_center.AccountID", $UserID);
            }
            $this->db->where(
                'tblK1history.TransDate BETWEEN "' .
                    $from_date .
                    ' 00:00:00" AND "' .
                    $to_date .
                    ' 23:59:59"'
            );
            if (!empty($data["order_status"])) {
                $this->db->where(
                    "tblK1purchasemaster.OrderStatus",
                    $data["order_status"]
                );
            }
            if (!empty($data["AccountID"])) {
                $this->db->where(
                    "tblK1purchasemaster.AccountID",
                    $data["AccountID"]
                );
            }
            if (!empty($data["CenterID"])) {
                $this->db->where(
                    "tblK1purchasemaster.CenterID",
                    $data["CenterID"]
                );
            }
            if (!empty($data["ItemID"])) {
                $this->db->where("tblK1history.ItemID", $data["ItemID"]);
            }
            if (!empty($data["Entry_type"])) {
                $this->db->where(
                    "tblK1purchasemaster.EntryType",
                    $data["Entry_type"]
                );
            }
            $this->db->join(
                "tblproduct",
                "tblproduct.ProductID = tblK1history.ItemID"
            );
            $this->db->join(
                "tblK1purchasemaster",
                "tblK1purchasemaster.PurchID = tblK1history.OrderID"
            );
            $this->db->join(
                "tblclients",
                "tblclients.AccountID = tblK1purchasemaster.AccountID"
            );
            $this->db->join(
                "tblGstRecord",
                'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"',
                "LEFT"
            );
            $this->db->join(
                "tblCenterMaster",
                "tblCenterMaster.CenterID = tblK1purchasemaster.CenterID"
            );
            $this->db->where("tblK1purchasemaster.PurchID IS NOT NULL");
            $this->db->order_by("tblK1purchasemaster.PurchID", "DESC");
            return $this->db->get("tblK1history")->result_array();
        }
    }
    public function get_company_detail()
    {
        $selected_company = $this->session->userdata("root_company");
        $sql =
            "SELECT " .
            db_prefix() .
            'rootcompany.*
			FROM ' .
            db_prefix() .
            'rootcompany WHERE id = "' .
            $selected_company .
            '"';
        $result = $this->db->query($sql)->row();
        return $result;
    }
    public function GetSaledetailsNumberwise($PoNumber)
    {
        $this->db->select(
            "tblK1purchasemaster.*,tblCenterMaster.CenterName,tblclients.company,SUM(tblK1history.OrderQty) AS TotalOrderQty,tblK1history.ItemID"
        );
        $this->db->join(
            db_prefix() . "CenterMaster",
            "tblCenterMaster.CenterID = tblK1purchasemaster.CenterID"
        );
        $this->db->join(
            db_prefix() . "clients",
            "tblclients.AccountID = tblK1purchasemaster.AccountID"
        );
        $this->db->join(
            db_prefix() . "K1history",
            "tblK1history.OrderID = tblK1purchasemaster.PurchID"
        );
        $this->db->join(
            db_prefix() . "product",
            "tblproduct.ProductID = tblK1history.ItemID",
            "left"
        );
        $this->db->where("tblK1purchasemaster.PurchID", $PoNumber);
        return $this->db->get("tblK1purchasemaster")->row();
    }
    public function GetSaledetailsItemwise($PoNumber)
    {
        $this->db->select(
            "tblK1history.*,tblK1purchasemaster.Transdate,tblK1purchasemaster.OrderStatus,tblCenterMaster.CenterName AS CenterName,tblclients.company,tblproduct.ProductName"
        );
        $this->db->join(
            db_prefix() . "K1purchasemaster",
            "tblK1purchasemaster.PurchID = tblK1history.OrderID"
        );
        $this->db->join(
            db_prefix() . "product",
            "tblproduct.ProductID = tblK1history.ItemID",
            "left"
        );
        $this->db->join(
            db_prefix() . "CenterMaster",
            "tblCenterMaster.CenterID = tblK1purchasemaster.CenterID"
        );
        $this->db->join(
            db_prefix() . "clients",
            "tblclients.AccountID = tblK1purchasemaster.AccountID"
        );
        $this->db->where("tblK1history.OrderID", $PoNumber);
        return $this->db->get("tblK1history")->result_array();
    }
    public function get_order_PR_ven_details($id)
    {
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $this->db->select("tblK1purchase_request_master.*");
        $this->db->from(db_prefix() . "K1purchase_request_master");
        $this->db->where("tblK1purchase_request_master.AccountID", $id);
        $this->db->where("tblK1purchase_request_master.OrderStatus", "P");
        $this->db->where(
            "tblK1purchase_request_master.PlantID",
            $selected_company
        );
        $this->db->where("tblK1purchase_request_master.FY", $fy);
        $result = $this->db->get()->result();
        return $result;
    }

    public function PendingPOByVendor($id)
    {
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $this->db->select("*");
        $this->db->from(db_prefix() . "K1PurchaseOrderMaster");
        $this->db->where("AccountID", $id);
        $this->db->where("PlantID", $selected_company);
        $this->db->where("FY", $fy);
        $this->db->where_in("OrderStatus", ['A', 'I']);
        $result = $this->db->get()->result();
        return $result;
    }

    public function get_order_PO_ven_details($id)
    {
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $this->db->select("tblK1purchasemaster.*");
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->where("tblK1purchasemaster.AccountID", $id);
        $this->db->where("tblK1purchasemaster.OrderStatus", "A");
        $this->db->where("tblK1purchasemaster.PlantID", $selected_company);
        $this->db->where("tblK1purchasemaster.FY", $fy);
        $result = $this->db->get()->result();
        return $result;
    }

    public function get_pending_PI_ven_details($id)
    {
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $this->db->select("tblK1purchasemaster.*");
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->where("tblK1purchasemaster.AccountID", $id);
        // $this->db->where("tblK1purchasemaster.OrderStatus", "P");
        $this->db->where("tblK1purchasemaster.Inv_No IS NOT NULL");
        $this->db->where("tblK1purchasemaster.Is_Ledger", "N");
        $this->db->where("tblK1purchasemaster.PlantID", $selected_company);
        $this->db->where("tblK1purchasemaster.FY", $fy);
        $result = $this->db->get()->result();
        return $result;
    }

    public function get_order_PI_ven_details($id)
    {
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $this->db->select("tblK1purchasemaster.*");
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->where("tblK1purchasemaster.AccountID", $id);
        $this->db->where("tblK1purchasemaster.OrderStatus", "F");
        $this->db->where("tblK1purchasemaster.Inv_No IS NOT NULL");
        $this->db->where("tblK1purchasemaster.Is_Ledger", "N");
        $this->db->where("tblK1purchasemaster.PlantID", $selected_company);
        $this->db->where("tblK1purchasemaster.FY", $fy);
        $result = $this->db->get()->result();
        return $result;
    }
    public function GetPurchaseRequestItemListForPO($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $this
            ->db->select('tblK1history.*,tblproduct.hsn_code,tblproduct.ProductName,tblproduct.unit AS Measuredin,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscAmt AS Discount,tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand,
			(Select SUM(RcvHistory.OrderQty/tblproduct.PackingQty) from tblK1history as RcvHistory where RcvHistory.ItemID = tblK1history.ItemID  AND RcvHistory.BillID = tblK1history.OrderID AND RcvHistory.TType ="P" AND RcvHistory.TType2="Purchase") As RcvQty');
        $this->db->from(db_prefix() . "K1history");
        $this->db->join(
            db_prefix() . "product",
            "tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID"
        );
        $this->db->join(db_prefix() . "taxes", "tbltaxes.id = tblproduct.gst");
        $this->db->join(
            db_prefix() . "brands",
            "tblbrands.id = tblproduct.BrandId"
        );
        $this->db->where(db_prefix() . "K1history.OrderID", $id);
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        $this->db->where(db_prefix() . "K1history.FY", $fy);
        $this->db->order_by(db_prefix() . "K1history.Ordinalno");
        $results = $this->db->get()->result_array();
        foreach ($results as &$row) {
            if ($row["PackingQty"] == 1) {
                $row["PRQty"] = $row["OrderQty"];
                $row["OrderQty"] = $row["OrderQty"];
            } else {
                $row["PRQty"] = $row["OrderQty"] / $row["PackingQty"];
                $row["OrderQty"] = $row["OrderQty"] / $row["PackingQty"];
            }
            $row["PendingQty"] = $row["OrderQty"] - $row["RcvQty"];
            $row["OrderQty"] = $row["PendingQty"];
            $row["Discount"] = $row["Discount"] / $row["OrderQty"];
        }
        return $results;
    }

    public function GetPurchaseOrderItemListForInward($id)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');

        $this->db->select('
            tblK1history.*,
            tblproduct.ProductName,
            tblproduct.hsn_code,
            tblproduct.unit AS Measuredin,
            tblproduct.PackingQty,
            tblproduct.PackingWeight AS Packingwgt,
            tblK1history.SuppliedIn AS PurchUnit,
            tblK1history.DiscAmt AS Discount,
            tblK1history.NetOrderAmt AS Netamt,
            tblK1history.ItemID AS id,
            tbltaxes.taxrate AS gst,
            tblbrands.BrandName AS Brand
        ');

        $this->db->from('tblK1history');
        $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
        $this->db->join('tbltaxes', 'tbltaxes.id = tblproduct.gst');
        $this->db->join('tblbrands', 'tblbrands.id = tblproduct.BrandId');

        $this->db->where('tblK1history.OrderID', $id);
        $this->db->where('tblK1history.PlantID', $selected_company);
        $this->db->where('tblK1history.FY', $fy);
        $this->db->where('tblK1history.TType', 'P');
        $this->db->where('tblK1history.TType2', 'Purchase Order');
        $this->db->order_by('tblK1history.Ordinalno', 'ASC');

        $results = $this->db->get()->result_array();

        $final = [];

        foreach ($results as $row) {
            $row['PurchRate'] = $row['PurchRate'] * $row['PackingQty'];
            $row['SaleRate'] = $row['SaleRate'] * $row['PackingQty'];
            $row['BasicRate'] = $row['BasicRate'] * $row['PackingQty'];

            // Convert Order Qty
            $orderQty = ($row['PackingQty'] > 1)
                ? ($row['OrderQty'] / $row['PackingQty'])
                : $row['OrderQty'];

            // Total Received Qty
            $rcvQty = $this->db
                ->select_sum('OrderQty')
                ->from('tblK1history')
                ->where([
                    'OrderID'  => $id,
                    'ItemID'  => $row['ItemID'],
                    'PlantID' => $selected_company,
                    'FY'      => $fy,
                    'TType'   => 'P',
                    'TType2'  => 'Purchase'
                ])
                ->get()
                ->row()
                ->OrderQty;

            $rcvQty = (float)$rcvQty;

            if ($row['PackingQty'] > 1) {
                $rcvQty /= $row['PackingQty'];
            }

            $pendingQty = $orderQty - $rcvQty;

            if ($pendingQty <= 0) {
                continue;
            }

            // $row['OrderQty'] = $orderQty;
            $row['OrderQty'] = $pendingQty;
            $row['RcvQty'] = $rcvQty;
            $row['PendingQty'] = $pendingQty;
            $row['POrderQty'] = $pendingQty;

            $final[] = $row;
        }

        return $final;
    }

    public function GetPurchaseOrderItemListForInv($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $this
            ->db->select('tblK1history.*,tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit AS Measuredin,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscAmt AS Discount,tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand,
			(Select SUM(RcvHistory.OrderQty/tblproduct.PackingQty) from tblK1history as RcvHistory where RcvHistory.ItemID = tblK1history.ItemID  AND RcvHistory.OrderID = tblK1history.OrderID AND RcvHistory.TType ="P" AND RcvHistory.TType2="Purchase") As RcvQty,
			(Select SUM(RqstHistory.OrderQty/tblproduct.PackingQty) from tblK1history as RqstHistory where RqstHistory.ItemID = tblK1history.ItemID  AND RqstHistory.BillID = tblK1history.BillID AND RqstHistory.TType ="P" AND RqstHistory.TType2="Request") As PRQty');
        $this->db->from(db_prefix() . "K1history");
        $this->db->join(
            db_prefix() . "product",
            "tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID"
        );
        $this->db->join(db_prefix() . "taxes", "tbltaxes.id = tblproduct.gst");
        $this->db->join(
            db_prefix() . "brands",
            "tblbrands.id = tblproduct.BrandId"
        );
        $this->db->where(db_prefix() . "K1history.OrderID", $id);
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        $this->db->where(db_prefix() . "K1history.FY", $fy);
        $this->db->order_by(db_prefix() . "K1history.Ordinalno", "ASC");
        $results = $this->db->get()->result_array();
        foreach ($results as &$row) {
            if ($row["PackingQty"] == 1) {
                $row["OrderQty"] = $row["OrderQty"];
            } else {
                $row["OrderQty"] = $row["OrderQty"] / $row["PackingQty"];
            }
            $row["PendingQty"] = $row["PRQty"] - $row["RcvQty"];
            // $row['OrderQty'] = $row['PendingQty'];
            $row["Discount"] = $row["Discount"];
        }
        return $results;
    }
    public function GetPurchaseOrderItemListForInvLedger($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $this
            ->db->select('tblK1history.*,tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit AS Measuredin,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscAmt AS Discount,tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand,
			(Select SUM(RcvHistory.OrderQty/tblproduct.PackingQty) from tblK1history as RcvHistory where RcvHistory.ItemID = tblK1history.ItemID  AND RcvHistory.OrderID = tblK1history.OrderID AND RcvHistory.TType ="P" AND RcvHistory.TType2="Purchase") As RcvQty,
			(Select SUM(RqstHistory.OrderQty/tblproduct.PackingQty) from tblK1history as RqstHistory where RqstHistory.ItemID = tblK1history.ItemID  AND RqstHistory.BillID = tblK1history.BillID AND RqstHistory.TType ="P" AND RqstHistory.TType2="Request") As PRQty');
        $this->db->from(db_prefix() . "K1history");
        $this->db->join(
            db_prefix() . "product",
            "tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID"
        );
        $this->db->join(db_prefix() . "taxes", "tbltaxes.id = tblproduct.gst");
        $this->db->join(
            db_prefix() . "brands",
            "tblbrands.id = tblproduct.BrandId"
        );
        $this->db->where(db_prefix() . "K1history.TransID", $id);
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        $this->db->where(db_prefix() . "K1history.FY", $fy);
        $results = $this->db->get()->result_array();
        foreach ($results as &$row) {
            $row['PurchRate'] = $row['PurchRate'] * $row['PackingQty'];
            $row['SaleRate'] = $row['SaleRate'] * $row['PackingQty'];
            $row['BasicRate'] = $row['BasicRate'] * $row['PackingQty'];
            
            if ($row["PackingQty"] == 1) {
                $row["OrderQty"] = $row["OrderQty"];
            } else {
                $row["OrderQty"] = $row["OrderQty"] / $row["PackingQty"];
            }
            $row["PendingQty"] = $row["PRQty"] - $row["RcvQty"];
            // $row['OrderQty'] = $row['PendingQty'];
            $row["Discount"] = $row["Discount"] / $row["OrderQty"];
        }
        return $results;
    }
    //=================== Purchase return Page =====================================
    //=============== Get Purchase Return Details By Return ID =====================
    public function GetPurchaseReturnInvoiceDetails($PINumber)
    {
        $selected_company = $this->session->userdata("root_company");
        $year = $this->session->userdata("finacial_year");
        $this->db->select(
            'tblK1purchasereturn.*,tblclients.company,tblclients.phonenumber,tblclients.state,tblxx_statelist.state_name, SUM(tblK1history.OrderQty) AS TotalOrderQty,(tblK1purchasereturn.Purchamt - tblK1purchasereturn.Discamt) AS taxable_amt,tblCenterMaster.CenterName,tblGstRecord.gstin AS gst, CenterState.state_name AS StateCenter, GROUP_CONCAT(DISTINCT CONCAT(tblclients.house, ", ", tblclients.street, ", ", tblclients.loc, ", ", tblclients.vtc, ", ", tblxx_statelist.state_name, " - ", tblxx_citylist.city_name)) AS VendorAddress'
        );
        $this->db->from(db_prefix() . "K1purchasereturn");
        $this->db->join(
            db_prefix() . "clients",
            "tblclients.AccountID = tblK1purchasereturn.AccountID AND tblclients.PlantID = tblK1purchasereturn.PlantID"
        );
        $this->db->join(
            "tblGstRecord",
            'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"',
            "LEFT"
        );
        $this->db->join(
            db_prefix() . "xx_statelist",
            db_prefix() .
                "xx_statelist.short_name = " .
                db_prefix() .
                "clients.state",
            "left"
        );
        $this->db->join(
            "tblxx_citylist",
            "tblxx_citylist.id = tblclients.dist",
            "LEFT"
        );
        $this->db->join(
            db_prefix() . "CenterMaster",
            db_prefix() .
                "CenterMaster.CenterID = " .
                db_prefix() .
                "K1purchasereturn.CenterID",
            "left"
        );
        $this->db->join(
            db_prefix() . "xx_statelist as CenterState",
            "CenterState.short_name = tblCenterMaster.state",
            "left"
        );
        $this->db->join(
            db_prefix() . "K1history",
            "tblK1history.OrderID = tblK1purchasereturn.PurchRtnID",
            "left"
        );
        $this->db->where(
            db_prefix() . "K1purchasereturn.PurchRtnID",
            $PINumber
        );
        $this->db->where(
            db_prefix() . "K1purchasereturn.PlantID",
            $selected_company
        );
        $this->db->where(db_prefix() . "K1purchasereturn.FY", $year);
        return $this->db->get()->row();
    }
    //===================== Purchase return Item Details ===========================
    public function GetPurchaseReturnInvoiceItemList($id, $PurchRtnID)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $this
            ->db->select('tblK1history.ItemID AS id,tblproduct.hsn_code,tblbrands.BrandName AS Brand,tblproduct.unit AS Measuredin,
		tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS PurchUnit,tblK1history.BilledQty,
		tblK1history.PurchRate,tblK1history.DiscAmt,tbltaxes.taxrate AS gst,tblK1history.BatchNo,DATE_FORMAT(tblK1history.ExpDate, "%d/%m/%Y") AS ExpDate ,tblK1history.CenterID,tblK1history.ItemID');
        $this->db->from(db_prefix() . "K1history");
        $this->db->join(
            db_prefix() . "product",
            "tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID"
        );
        $this->db->join(db_prefix() . "taxes", "tbltaxes.id = tblproduct.gst");
        $this->db->join(
            db_prefix() . "brands",
            "tblbrands.id = tblproduct.BrandId"
        );
        $this->db->where(db_prefix() . "K1history.TransID", $id);
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        //$this->db->where(db_prefix() . 'K1history.FY', $fy);
        if (str_contains($id, "TRF")) {
            $this->db->where(db_prefix() . "K1history.TType2", "IN");
        }
        //$this->db->where(db_prefix() . 'K1history.TType', "P");
        //$this->db->where(db_prefix() . 'K1history.TType2', "Purchase");
        $results = $this->db->get()->result_array();
        $this->db->select("tblK1history.*");
        $this->db->from(db_prefix() . "K1history");
        $this->db->where("OrderID", $PurchRtnID);
        $retuenitems = $this->db->get()->result_array();
        foreach ($results as &$row) {
            $row["Discount"] =
                $row["BilledQty"] != 0
                    ? $row["DiscAmt"] / $row["BilledQty"]
                    : 0;
            $RtnBilledQty = 0;
            $RtnCGSTAmt = 0;
            $RtnSGSTAmt = 0;
            $RtnIGSTAmt = 0;
            $RtnNetAmt = 0;
            $RtnDiscAmt = 0;
            foreach ($retuenitems as &$val1) {
                if (
                    $row["ItemID"] == $val1["ItemID"] &&
                    $row["BatchNo"] == $val1["BatchNo"]
                ) {
                    $RtnBilledQty = $val1["BilledQty"];
                    $RtnCGSTAmt = $val1["cgstamt"];
                    $RtnSGSTAmt = $val1["sgstamt"];
                    $RtnIGSTAmt = $val1["igstamt"];
                    $RtnNetAmt = $val1["NetChallanAmt"];
                    $RtnDiscAmt = $val1["DiscAmt"] / $val1["BilledQty"];
                    $row["Discount"] = $RtnDiscAmt;
                }
            }
            $row["ReturnOrderQty"] = $RtnBilledQty;
            $filterdata = [
                "ItemID" => $row["ItemID"],
                "CenterID" => $row["CenterID"],
                "BatchID" => $row["BatchNo"],
            ];
            $ItemBatchStock = $this->GetItemBatchCurrentStock($filterdata);
            $totalPIQty = $ItemBatchStock + $row["ReturnOrderQty"];
            $row["PIAvlQty"] = $totalPIQty;
            $row["cgstamt"] = $RtnCGSTAmt;
            $row["sgstamt"] = $RtnSGSTAmt;
            $row["igstamt"] = $RtnIGSTAmt;
            $row["Netamt"] = $RtnNetAmt;
        }
        return $results;
    }
    //================== Get Assigned and PI Center List ===========================
    public function GetAllAssignedAndPurchaseCenterList($data = "")
    {
        $UserID = $this->session->userdata("username");
        if (is_admin()) {
            $this->db->select("tblCenterMaster.*");
            $this->db->from(db_prefix() . "CenterMaster");
            if ($data["CenterID"]) {
                $this->db->where_in(
                    "tblCenterMaster.CenterID",
                    $data["CenterID"]
                );
            }
            $this->db->join(
                "tblK1purchasemaster",
                "tblK1purchasemaster.CenterID = " .
                    db_prefix() .
                    "CenterMaster.CenterID"
            );
            $this->db->where("tblCenterMaster.status", "Y");
            $this->db->group_by("tblCenterMaster.CenterID");
            return $this->db->get()->result_array();
        } else {
            $this->db->select("tblCenterMaster.*");
            $this->db->from(db_prefix() . "CenterMaster");
            $this->db->join(
                "tblstaff_wise_center",
                "tblstaff_wise_center.CenterID = " .
                    db_prefix() .
                    "CenterMaster.CenterID"
            );
            $this->db->join(
                "tblK1purchasemaster",
                "tblK1purchasemaster.CenterID = " .
                    db_prefix() .
                    "CenterMaster.CenterID"
            );
            $this->db->where("tblCenterMaster.status", "Y");
            if ($data["CenterID"]) {
                $this->db->where_in(
                    "tblCenterMaster.CenterID",
                    $data["CenterID"]
                );
            }
            $this->db->where("tblstaff_wise_center.AccountID", $UserID);
            $this->db->group_by("tblCenterMaster.CenterID");
            return $this->db->get()->result_array();
        }
    }
    //========================== Get PI Vendor List By CenterID ====================
    public function GetPIVendorListByCenterID($CenterID)
    {
        $UserID = $this->session->userdata("username");
        // Purchase Accounts
        $this->db->select("tblclients.company,tblK1purchasemaster.AccountID");
        $this->db->join(
            "tblclients",
            "tblclients.AccountID = tblK1purchasemaster.AccountID"
        );
        $this->db->where("tblK1purchasemaster.Inv_No IS NOT NULL"); // Check Generate PI
        $this->db->where("tblK1purchasemaster.OrderStatus", "F"); // Check Finish PI
        $this->db->group_by("tblK1purchasemaster.AccountID");
        $this->db->where("tblK1purchasemaster.CenterID", $CenterID);
        $PIVendorList = $this->db->get("tblK1purchasemaster")->result_array();
        // Transfer Accounts
        $this->db->select(
            "tblclients.company,tblK1stocktransfermaster.AccountID"
        );
        $this->db->join(
            "tblclients",
            "tblclients.AccountID = tblK1stocktransfermaster.AccountID"
        );
        $this->db->group_by("tblK1stocktransfermaster.AccountID");
        $this->db->where("tblK1stocktransfermaster.TransferTo", $CenterID);
        $TransferVendorList = $this->db
            ->get("tblK1stocktransfermaster")
            ->result_array();
        $response = [];
        foreach ($PIVendorList as $val) {
            $new = [
                "AccountID" => $val["AccountID"],
                "company" => $val["company"],
            ];
            array_push($response, $new);
        }
        foreach ($TransferVendorList as $val) {
            $new = [
                "AccountID" => $val["AccountID"],
                "company" => $val["company"],
            ];
            array_push($response, $new);
        }
        return $response;
    }
    //==================== Get PI List By CenterID And VendorID ====================
    public function GetPIListByCenterIDAndVendorID($CenterID, $AccountID)
    {
        $UserID = $this->session->userdata("username");
        // Purchase Invoice List Against CenterID and VendorID
        $this->db->select(
            'tblK1purchasemaster.Inv_No,DATE_FORMAT(Inv_date, "%d/%m/%Y") AS formatted_PIdate'
        );
        $this->db->where("tblK1purchasemaster.Inv_No IS NOT NULL"); // Check Generate PI
        $this->db->where("tblK1purchasemaster.OrderStatus", "F"); // Check Finish PI
        $this->db->where("tblK1purchasemaster.CenterID", $CenterID);
        $this->db->where("tblK1purchasemaster.AccountID", $AccountID);
        $this->db->group_by("tblK1purchasemaster.Inv_No");
        $this->db->order_by("tblK1purchasemaster.Inv_No", "DESC");
        $PIList = $this->db->get("tblK1purchasemaster")->result_array();
        // STock Transfer List Against CenterID and VendorID
        $this->db->select(
            'tblK1stocktransfermaster.TransferID AS Inv_No,DATE_FORMAT(TransferDate, "%d/%m/%Y") AS formatted_PIdate'
        );
        $this->db->where("tblK1stocktransfermaster.TransferTo", $CenterID);
        $this->db->where("tblK1stocktransfermaster.AccountID", $AccountID);
        $this->db->group_by("tblK1stocktransfermaster.TransferID");
        $this->db->order_by("tblK1stocktransfermaster.TransferID", "DESC");
        $TransferList = $this->db
            ->get("tblK1stocktransfermaster")
            ->result_array();
        $response = [];
        foreach ($PIList as $val) {
            $new = [
                "Inv_No" => $val["Inv_No"],
                "formatted_PIdate" => $val["formatted_PIdate"],
            ];
            array_push($response, $new);
        }
        foreach ($TransferList as $val) {
            $new = [
                "Inv_No" => $val["Inv_No"],
                "formatted_PIdate" => $val["formatted_PIdate"],
            ];
            array_push($response, $new);
        }
        return $response;
    }
    //=============== Get PI Item data for purchase return =========================
    public function GetPIItemDetailsForReturn($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $this->db->select('tblK1history.*,
		tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit AS Measuredin,tblproduct.PackingQty,
		tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscAmt AS Discount,
		tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand');
        $this->db->from(db_prefix() . "K1history");
        $this->db->join(
            db_prefix() . "product",
            "tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID"
        );
        $this->db->join(db_prefix() . "taxes", "tbltaxes.id = tblproduct.gst");
        $this->db->join(
            db_prefix() . "brands",
            "tblbrands.id = tblproduct.BrandId"
        );
        $this->db->where(db_prefix() . "K1history.TransID", $id);
        if (str_contains($id, "TRF")) {
            $this->db->where(db_prefix() . "K1history.TType2", "IN");
        }
        $this->db->where(db_prefix() . "K1history.PlantID", $selected_company);
        //$this->db->where(db_prefix() . 'K1history.FY', $fy);
        $results = $this->db->get()->result_array();
        foreach ($results as &$row) {
            $filterdata = [
                "ItemID" => $row["ItemID"],
                "CenterID" => $row["CenterID"],
                "BatchID" => $row["BatchNo"],
                "TransID" => $id,
            ];
            $ItemBatchStock = $this->GetItemBatchCurrentStock($filterdata);
            $row["ReturnOrderQty"] = 0;
            $row["Netamt"] = 0;
            $row["ExpDate"] = _d($row["ExpDate"]);
            $row["PIAvlQty"] = $ItemBatchStock;
            $row["Discount"] = $row["DiscAmt"] / $row["BilledQty"];
            $row["PurchRate"] = number_format(
                $row["PurchRate"] / $row["CaseQty"],
                2,
                ".",
                ""
            );
        }
        return $results;
    }
    //===================== Get Batch Current Stock ================================
    public function GetItemBatchCurrentStock($filterdata)
    {
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        // Batch List From Opening Stock
        $this->db->select("SUM(tblK1stockmaster.OQty) AS TotalOpnQty");
        $this->db->where("tblK1stockmaster.ItemID", $filterdata["ItemID"]);
        $this->db->where("tblK1stockmaster.CenterID", $filterdata["CenterID"]);
        $this->db->where("tblK1stockmaster.BatchNo", $filterdata["BatchID"]);
        $this->db->where("tblK1stockmaster.FY", $fy);
        $this->db->group_by("tblK1stockmaster.BatchNo");
        $this->db->order_by("tblK1stockmaster.ExpDate", "ASC");
        $OpnBatchQty = $this->db->get(db_prefix() . "K1stockmaster")->row();
        // Batch List From History
        $this
            ->db->select('tblK1history.BatchNo,SUM(tblK1history.BilledQty) AS TotalQty, tblK1history.TType,
		tblK1history.TType2,tblK1history.ExpDate,tblK1history.PurchRate,tblK1history.CaseQty');
        $this->db->where("tblK1history.ItemID", $filterdata["ItemID"]);
        $this->db->where("tblK1history.CenterID", $filterdata["CenterID"]);
        $this->db->where("tblK1history.BatchNo", $filterdata["BatchID"]);
        //$this->db->where('tblK1history.TransID', $filterdata["TransID"]);
        $this->db->where("tblK1history.OrderID IS NOT NULL");
        $this->db->where("tblK1history.BillID IS NOT NULL");
        $this->db->where("tblK1history.TransID IS NOT NULL");
        $this->db->where("tblK1history.FY", $fy);
        $this->db->group_by("tblK1history.TType,tblK1history.TType2");
        $BatchTransaction = $this->db
            ->get(db_prefix() . "K1history")
            ->result_array();
        $OQty = 0;
        $PurchQty = 0;
        $InwardQty = 0;
        $PurchRtnQty = 0;
        $SaleQty = 0;
        $SaleRtnQty = 0;
        $PrdQty = 0;
        $IssueQty = 0;
        $AdjQty = 0;
        $InQty = 0;
        $OutQty = 0;
        $BalQty = 0;
        if ($OpnBatchQty) {
            $OQty = $OpnBatchQty->TotalOpnQty;
        }
        foreach ($BatchTransaction as $stockkey => $stockval) {
            if ($stockval["TType"] == "O" && $stockval["TType2"] == "SALE") {
                $SaleQty += $stockval["TotalQty"];
            } elseif (
                $stockval["TType"] == "SR" &&
                $stockval["TType2"] == "FRESH RETURN"
            ) {
                $SaleRtnQty += $stockval["TotalQty"];
            } elseif (
                $stockval["TType"] == "P" &&
                $stockval["TType2"] == "Purchase"
            ) {
                $PurchQty += $stockval["TotalQty"];
            } elseif (
                $stockval["TType"] == "P" &&
                $stockval["TType2"] == "PURCHASE RETURN"
            ) {
                $PurchRtnQty += $stockval["TotalQty"];
            } elseif (
                $stockval["TType"] == "T" &&
                $stockval["TType2"] == "IN"
            ) {
                $InQty += $stockval["TotalQty"];
            } elseif (
                $stockval["TType"] == "T" &&
                $stockval["TType2"] == "OUT"
            ) {
                $OutQty += $stockval["TotalQty"];
            } elseif (
                $stockval["TType"] == "I" &&
                $stockval["TType2"] == "INWARD"
            ) {
                $InwardQty += $stockval["TotalQty"];
            } elseif ($stockval["TType"] == "X") {
                $AdjQty += $stockval["TotalQty"];
            }
        }
        $BalQty =
            $OQty +
            $InwardQty +
            $PurchQty -
            $PurchRtnQty -
            $SaleQty +
            $SaleRtnQty +
            $PrdQty -
            $IssueQty -
            $AdjQty +
            $InQty -
            $OutQty;
        return $BalQty;
    }
    //=============== Get Purchase Return By ReturnID ==============================
    public function GetPurchaseReturnDetail($id)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $this->db->select("tblK1history.*");
        $this->db->from(db_prefix() . "K1history");
        $this->db->where(db_prefix() . "K1history.OrderID", $id);
        $results = $this->db->get()->result_array();
        return $results;
    }
    //=================== Add Kirti One Return Purchase Order =============================
    public function AddKirtiOneReturnPurchaseOrderNew($data)
    {
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "BilledQty";
            $header[] = "PIAvlQty";
            $header[] = "ReturnOrderQty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            $header[] = "BatchNo";
            $header[] = "ExpDate";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "" && $value[9] != "" && $value[9] > 0) {
                    $ItemID = $value[0];
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $PlantID = $this->session->userdata("root_company");
        $FY = $this->session->userdata("finacial_year");
        $prefix = "PRT";
        $purchase_orderNumbar = get_option(
            "next_purchase_rtn_number_for_kirtione"
        );
        $new_purchase_ReturnorderNumbar =
            $prefix . $FY . "1" . $purchase_orderNumbar;
        $Transdate = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $PurchID = $data["PurchID"];
        $AccountID = $data["AccountID"];
        $CenterID = $data["centername"];
        $State = $data["state"];
        $PurchAmt = $data["total_amt_in_mt"];
        $discountAMT = $data["total_disc_in_mt"];
        $cgstamt = $data["total_cgst_amt"];
        $sgstamt = $data["total_sgst_amt"];
        $igstamt = $data["total_igst_amt"];
        $roundoffamt = $data["total_roundoff_amt"];
        $invoiceamt = $data["netpayableamt"];
        $ItCount = count($es_detail);
        $PurchID = $data["PurchID"];
        $KirtiOnePurchMaster = [
            "PlantID" => $PlantID,
            "FY" => $FY,
            "BT" => "T",
            "PurchRtnID" => $new_purchase_ReturnorderNumbar,
            "PurchID" => $PurchID,
            "Transdate" => $Transdate,
            "CenterID" => $CenterID,
            "AccountID" => $AccountID,
            "Purchamt" => $PurchAmt,
            "Discamt" => $discountAMT,
            "cgstamt" => $cgstamt,
            "sgstamt" => $sgstamt,
            "igstamt" => $igstamt,
            "RoundOffAmt" => $roundoffamt,
            "Invamt" => $invoiceamt,
            "ItCount" => $ItCount,
            "UserID" => $_SESSION["username"],
        ];
        /*echo "<pre>";
	    print_r($es_detail);
	    print_r($KirtiOnePurchMaster);
	    die;*/
        $this->db->insert(
            db_prefix() . "K1purchasereturn",
            $KirtiOnePurchMaster
        );
        if ($this->db->affected_rows() > 0) {
            $this->increment_next_number(
                "next_purchase_rtn_number_for_kirtione"
            );
            $i = 1;
            foreach ($es_detail as $value) {
                $ItemID = $value["ItemID"];
                $PurchRate = $value["PurchRate"];
                $saleunit = $value["PurchaseUnit"];
                $unit = $value["MeasuredIn"];
                $packing_qty = $value["PackingQty"];
                $packing_weight = $value["PackingWeight"];
                $BilledQty = $value["ReturnOrderQty"];
                $gst = $value["GST"];
                $BatchNo = $value["BatchNo"];
                $ExpDate = to_sql_date($value["ExpDate"]);
                $caseqty = 1;
                $ItemTotal = $BilledQty * $PurchRate;
                $TotalItemAmt += $ItemTotal;
                $TotalDisc = $value["Discount"] * $BilledQty;
                $TotalDiscAmt += $TotalDisc;
                $TaxableAmt = $ItemTotal - $TotalDisc;
                $TotalTaxableAmt += $TaxableAmt;
                $Discountperc =
                    ($value["Discount"] / $value["PurchRate"]) * 100;
                $CGST = 0;
                $CGSTAmt = 0;
                $SGST = 0;
                $SGSTAmt = 0;
                $IGST = 0;
                $IGSTAmt = 0;
                if ($State == "MH") {
                    $CGST = $gst / 2;
                    $SGST = $gst / 2;
                    $IGST = 0;
                    $CGSTAmt = $TaxableAmt * ($CGST / 100);
                    $SGSTAmt = $TaxableAmt * ($SGST / 100);
                    $IGSTAmt = 0;
                } else {
                    $CGST = 0;
                    $SGST = 0;
                    $IGST = $gst;
                    $CGSTAmt = 0;
                    $SGSTAmt = 0;
                    $IGSTAmt = $TaxableAmt * ($IGST / 100);
                }
                $TotaCGSTAmt += $CGSTAmt;
                $TotaSGSTAmt += $SGSTAmt;
                $TotaIGSTAmt += $IGSTAmt;
                $NetAmt = $TaxableAmt + $CGSTAmt + $SGSTAmt + $IGSTAmt;
                $TotalNetAmt += $NetAmt;
                $data_array_result = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "OrderID" => $new_purchase_ReturnorderNumbar,
                    "TransID" => $new_purchase_ReturnorderNumbar,
                    "BillID" => $PurchID,
                    "TransDate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "TType" => "P",
                    "TType2" => "PURCHASE RETURN",
                    "AccountID" => $AccountID,
                    "ItemID" => $ItemID,
                    "CenterID" => $CenterID,
                    "PartyID" => "KASPL",
                    "PurchRate" => $PurchRate,
                    "SaleRate" => "0",
                    "BasicRate" => $PurchRate,
                    "SuppliedIn" => $unit,
                    "OrderQty" => $BilledQty,
                    "BilledQty" => $BilledQty,
                    "DiscPerc" => $Discountperc,
                    "DiscAmt" => $TotalDisc,
                    "cgst" => $CGST,
                    "cgstamt" => $CGSTAmt,
                    "sgst" => $SGST,
                    "sgstamt" => $SGSTAmt,
                    "igst" => $IGST,
                    "igstamt" => $IGSTAmt,
                    "CaseQty" => $caseqty,
                    "Cases" => 0.0,
                    "OrderAmt" => $TaxableAmt,
                    "ChallanAmt" => $TaxableAmt,
                    "NetOrderAmt" => $NetAmt,
                    "NetChallanAmt" => $NetAmt,
                    "Ordinalno" => $i,
                    "rowid" => "0",
                    "UserID" => $_SESSION["username"],
                    "BatchNo" => $BatchNo,
                    "ExpDate" => $ExpDate,
                    "cnfid" => "",
                ];
                $this->db->insert(
                    db_prefix() . "K1history",
                    $data_array_result
                );
                $i++;
            }
            // update Purchase return table
            $roundoffamt = round($TotalNetAmt) - $TotalNetAmt;
            $data_array = [
                "Purchamt" => $TotalItemAmt,
                "Discamt" => $TotalDiscAmt,
                "cgstamt" => $TotaCGSTAmt,
                "sgstamt" => $TotaSGSTAmt,
                "igstamt" => $TotaIGSTAmt,
                "RoundOffAmt" => $roundoffamt,
                "Invamt" => round($TotalNetAmt),
            ];
            $this->db->where("PlantID", $PlantID);
            $this->db->LIKE("FY", $FY);
            $this->db->where("PurchRtnID ", $PurchRetID);
            $this->db->update(db_prefix() . "K1purchasereturn", $data_array);
            $UserID = $_SESSION["username"];
            $narration = "Purchase Return Against " . $PurchID;
            $ord = 1;
            // Add Ledger Entry
            // Debit to Party
            $Purchase_ledger_entry = [
                "PlantID" => $PlantID,
                "FY" => $FY,
                "Transdate" => date("Y-m-d h:i:s"),
                "VoucherID" => $new_purchase_ReturnorderNumbar,
                "Transdate2" => date("Y-m-d h:i:s"),
                "PartyID" => "KASPL",
                "AccountID" => $AccountID,
                "CounterAccount" => "PURCHASER",
                "CenterID" => $CenterID,
                "EntryFor" => 2,
                "TType" => "D",
                "Amount" => $TotalNetAmt,
                "Narration" => $narration,
                "PassedFrom" => "PURCHASER RETURN",
                "OrdinalNo" => $ord,
                "UserID" => $UserID,
            ];
            $this->db->insert(
                db_prefix() . "accountledger",
                $Purchase_ledger_entry
            );
            $ord++;
            if ($TotalDiscAmt > 0) {
                // Debit to Discount Ledger
                $Purchase_ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => date("Y-m-d h:i:s"),
                    "VoucherID" => $new_purchase_ReturnorderNumbar,
                    "Transdate2" => date("Y-m-d h:i:s"),
                    "PartyID" => "KASPL",
                    "AccountID" => "DISCR",
                    "CounterAccount" => "PURCHASER",
                    "CenterID" => $CenterID,
                    "EntryFor" => 2,
                    "TType" => "D",
                    "Amount" => $TotalDiscAmt,
                    "Narration" => $narration,
                    "PassedFrom" => "PURCHASER RETURN",
                    "OrdinalNo" => $ord,
                    "UserID" => $UserID,
                ];
                $this->db->insert(
                    db_prefix() . "accountledger",
                    $Purchase_ledger_entry
                );
                $ord++;
            }
            // Cradit to purchase Return Ledger
            $Purchase_ledger_entry = [
                "PlantID" => $PlantID,
                "FY" => $FY,
                "Transdate" => date("Y-m-d h:i:s"),
                "VoucherID" => $new_purchase_ReturnorderNumbar,
                "Transdate2" => date("Y-m-d h:i:s"),
                "PartyID" => "KASPL",
                "AccountID" => "PURCHASER",
                "CounterAccount" => $AccountID,
                "CenterID" => $CenterID,
                "EntryFor" => 2,
                "TType" => "C",
                "Amount" => $TotalItemAmt,
                "Narration" => $narration,
                "PassedFrom" => "PURCHASE RETURN",
                "OrdinalNo" => $ord,
                "UserID" => $UserID,
            ];
            $this->db->insert(
                db_prefix() . "accountledger",
                $Purchase_ledger_entry
            );
            $ord++;
            if ($TotaIGSTAmt > 0) {
                // Cradit  to CGST Ledger
                $Purchase_ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => date("Y-m-d h:i:s"),
                    "VoucherID" => $new_purchase_ReturnorderNumbar,
                    "Transdate2" => date("Y-m-d h:i:s"),
                    "PartyID" => "KASPL",
                    "AccountID" => "IGST",
                    "CounterAccount" => $AccountID,
                    "CenterID" => $CenterID,
                    "EntryFor" => 2,
                    "TType" => "C",
                    "Amount" => $TotaIGSTAmt,
                    "Narration" => $narration,
                    "PassedFrom" => "PURCHASE RETURN",
                    "OrdinalNo" => $ord,
                    "UserID" => $UserID,
                ];
                $this->db->insert(
                    db_prefix() . "accountledger",
                    $Purchase_ledger_entry
                );
                $ord++;
            } else {
                // Cradit  to CGST Ledger
                $Purchase_ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => date("Y-m-d h:i:s"),
                    "VoucherID" => $new_purchase_ReturnorderNumbar,
                    "Transdate2" => date("Y-m-d h:i:s"),
                    "PartyID" => "KASPL",
                    "AccountID" => "CGST",
                    "CounterAccount" => $AccountID,
                    "CenterID" => $CenterID,
                    "EntryFor" => 2,
                    "TType" => "C",
                    "Amount" => $TotaCGSTAmt,
                    "Narration" => $narration,
                    "PassedFrom" => "PURCHASER RETURN",
                    "OrdinalNo" => $ord,
                    "UserID" => $UserID,
                ];
                $this->db->insert(
                    db_prefix() . "accountledger",
                    $Purchase_ledger_entry
                );
                $ord++;
                // Cradit  to CGST Ledger
                $Purchase_ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => date("Y-m-d h:i:s"),
                    "VoucherID" => $new_purchase_ReturnorderNumbar,
                    "Transdate2" => date("Y-m-d h:i:s"),
                    "PartyID" => "KASPL",
                    "AccountID" => "SGST",
                    "CounterAccount" => $AccountID,
                    "CenterID" => $CenterID,
                    "EntryFor" => 2,
                    "TType" => "C",
                    "Amount" => $TotaSGSTAmt,
                    "Narration" => $narration,
                    "PassedFrom" => "PURCHASER RETURN",
                    "OrdinalNo" => $ord,
                    "UserID" => $UserID,
                ];
                $this->db->insert(
                    db_prefix() . "accountledger",
                    $Purchase_ledger_entry
                );
                $ord++;
            }
            return $new_purchase_ReturnorderNumbar;
        }
    }
    //================== update Kirti One Return Purchase Invoice ===================
    public function UpdateKirtiOneReturnPurchaseInvoice($data, $id)
    {
        $PlantID = $this->session->userdata("root_company");
        $FY = $this->session->userdata("finacial_year");
        if (isset($data["pur_order_detail"])) {
            $pur_order_detail = json_decode($data["pur_order_detail"]);
            unset($data["pur_order_detail"]);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = "ItemID";
            $header[] = "HSN";
            $header[] = "Brand";
            $header[] = "MeasuredIn";
            $header[] = "PackingQty";
            $header[] = "PackingWeight";
            $header[] = "PurchaseUnit";
            $header[] = "BilledQty";
            $header[] = "PIAvlQty";
            $header[] = "ReturnOrderQty";
            $header[] = "PurchRate";
            $header[] = "Discount";
            $header[] = "GST";
            $header[] = "CGSTAMT";
            $header[] = "SGSTAMT";
            $header[] = "IGSTAMT";
            $header[] = "total_money";
            $header[] = "BatchNo";
            $header[] = "ExpDate";
            foreach ($pur_order_detail as $key => $value) {
                if ($value[0] != "" && $value[9] != "" && $value[9] > 0) {
                    $ItemID = $value[0];
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        $PurchRetID = $id;
        $Transdate = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $new_date = to_sql_date($data["prd_date"]) . " " . date("H:i:s");
        $PurchID = $data["PurchID"];
        $AccountID = $data["vendor"];
        $CenterID = $data["centername"];
        $State = $data["state"];
        $PurchAmt = $data["total_amt_in_mt"];
        $discountAMT = $data["total_disc_in_mt"];
        $cgstamt = $data["total_cgst_amt"];
        $sgstamt = $data["total_sgst_amt"];
        $igstamt = $data["total_igst_amt"];
        $roundoffamt = $data["total_roundoff_amt"];
        $invoiceamt = $data["netpayableamt"];
        //$PurchID = $data["PurchID"];
        $ItCount = count($es_detail);
        $this->db->select("tblK1purchasereturn.*");
        $this->db->from(db_prefix() . "K1purchasereturn");
        $this->db->where(db_prefix() . "K1purchasereturn.PurchID", $PurchID);
        $purchaselist = $this->db->get()->row();
        $data_array = [
            "Transdate" => $new_date,
            "PurchRtnID " => $PurchRetID,
            "Purchamt" => $PurchAmt,
            "Discamt" => $discountAMT,
            "cgstamt" => $cgstamt,
            "sgstamt" => $sgstamt,
            "igstamt" => $igstamt,
            "RoundOffAmt" => $roundoffamt,
            "Invamt" => $invoiceamt,
            "ItCount" => $ItCount,
            "Lupdate" => date("Y-m-d H:i:s"),
            "UserID2" => $this->session->userdata("username"),
        ];
        $this->db->where("PlantID", $PlantID);
        $this->db->LIKE("FY", $FY);
        $this->db->where("PurchRtnID ", $PurchRetID);
        $this->db->update(db_prefix() . "K1purchasereturn", $data_array);
        if ($this->db->affected_rows() > 0) {
            $old_pur_details = $this->GetPurchaseReturnDetail($PurchRetID);
            //Move record from tblK1history to tblK1history_audit
            foreach ($old_pur_details as $key => $value) {
                $CenterID = $value["CenterID"];
                $old_data = [
                    "PlantID" => $value["PlantID"],
                    "FY" => $value["FY"],
                    "OrderID" => $value["OrderID"],
                    "BillID" => $value["BillID"],
                    "TransID" => $value["TransID"],
                    "TransDate" => $value["TransDate"],
                    "TransDate2" => $value["TransDate2"],
                    "TType" => $value["TType"],
                    "TType2" => $value["TType2"],
                    "AccountID" => $value["AccountID"],
                    "ItemID" => $value["ItemID"],
                    "CenterID" => $value["CenterID"],
                    "GodownID" => $value["GodownID"],
                    "PartyID" => $value["PartyID"],
                    "PurchRate" => $value["PurchRate"],
                    "SaleRate" => $value["SaleRate"],
                    "BasicRate" => $value["BasicRate"],
                    "SuppliedIn" => $value["SuppliedIn"],
                    "OrderQty" => $value["OrderQty"],
                    "eOrderQty" => $value["eOrderQty"],
                    "BilledQty" => $value["BilledQty"],
                    "DiscPerc" => $value["DiscPerc"],
                    "DiscAmt" => $value["DiscAmt"],
                    "cgst" => $value["cgst"],
                    "cgstamt" => $value["cgstamt"],
                    "sgst" => $value["sgst"],
                    "sgstamt" => $value["sgstamt"],
                    "igst" => $value["igst"],
                    "igstamt" => $value["igstamt"],
                    "CaseQty" => $value["CaseQty"],
                    "Cases" => $value["Cases"],
                    "OrderAmt" => $value["OrderAmt"],
                    "ChallanAmt" => $value["ChallanAmt"],
                    "NetOrderAmt" => $value["NetOrderAmt"],
                    "NetChallanAmt" => $value["NetChallanAmt"],
                    "Ordinalno" => $value["Ordinalno"],
                    "UserID" => $value["UserID"],
                    "Lupdate" => date("Y-m-d H:i:s"),
                    "UserID2" => $_SESSION["username"],
                ];
                $this->db->insert(db_prefix() . "K1history_audit", $old_data);
            }
            //Delete Live history table record
            $this->db->where("PlantID", $PlantID);
            $this->db->where("FY", $FY);
            $this->db->where("OrderID", $PurchRetID);
            $this->db->delete(db_prefix() . "K1history");
            //Add New history detail record
            $i = 1;
            foreach ($es_detail as $value) {
                $ItemID = $value["ItemID"];
                $PurchRate = $value["PurchRate"];
                $saleunit = $value["PurchaseUnit"];
                $unit = $value["MeasuredIn"];
                $packing_qty = $value["PackingQty"];
                $packing_weight = $value["PackingWeight"];
                $BilledQty = $value["ReturnOrderQty"];
                $gst = $value["GST"];
                $BatchNo = $value["BatchNo"];
                $ExpDate = to_sql_date($value["ExpDate"]);
                $caseqty = 1;
                $ItemTotal = $BilledQty * $PurchRate;
                $TotalItemAmt += $ItemTotal;
                $TotalDisc = $value["Discount"] * $BilledQty;
                $TotalDiscAmt += $TotalDisc;
                $TaxableAmt = $ItemTotal - $TotalDisc;
                $TotalTaxableAmt += $TaxableAmt;
                $Discountperc =
                    ($value["Discount"] / $value["PurchRate"]) * 100;
                $CGST = 0;
                $CGSTAmt = 0;
                $SGST = 0;
                $SGSTAmt = 0;
                $IGST = 0;
                $IGSTAmt = 0;
                if ($State == "MH") {
                    $CGST = $gst / 2;
                    $SGST = $gst / 2;
                    $IGST = 0;
                    $CGSTAmt = $TaxableAmt * ($CGST / 100);
                    $SGSTAmt = $TaxableAmt * ($SGST / 100);
                    $IGSTAmt = 0;
                } else {
                    $CGST = 0;
                    $SGST = 0;
                    $IGST = $gst;
                    $CGSTAmt = 0;
                    $SGSTAmt = 0;
                    $IGSTAmt = $TaxableAmt * ($IGST / 100);
                }
                $TotaCGSTAmt += $CGSTAmt;
                $TotaSGSTAmt += $SGSTAmt;
                $TotaIGSTAmt += $IGSTAmt;
                $NetAmt = $TaxableAmt + $CGSTAmt + $SGSTAmt + $IGSTAmt;
                $TotalNetAmt += $NetAmt;
                $data_array_result = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "OrderID" => $PurchRetID,
                    "TransID" => $PurchRetID,
                    "BillID" => $PurchID,
                    "TransDate" => $Transdate,
                    "TransDate2" => date("Y-m-d H:i:s"),
                    "TType" => "P",
                    "TType2" => "PURCHASE RETURN",
                    "AccountID" => $AccountID,
                    "ItemID" => $ItemID,
                    "CenterID" => $CenterID,
                    "PartyID" => "KASPL",
                    "PurchRate" => $PurchRate,
                    "SaleRate" => "0",
                    "BasicRate" => $PurchRate,
                    "SuppliedIn" => $unit,
                    "OrderQty" => $BilledQty,
                    "BilledQty" => $BilledQty,
                    "DiscPerc" => $Discountperc,
                    "DiscAmt" => $TotalDisc,
                    "cgst" => $CGST,
                    "cgstamt" => $CGSTAmt,
                    "sgst" => $SGST,
                    "sgstamt" => $SGSTAmt,
                    "igst" => $IGST,
                    "igstamt" => $IGSTAmt,
                    "CaseQty" => $caseqty,
                    "Cases" => 0.0,
                    "OrderAmt" => $TaxableAmt,
                    "ChallanAmt" => $TaxableAmt,
                    "NetOrderAmt" => $NetAmt,
                    "NetChallanAmt" => $NetAmt,
                    "Ordinalno" => $i,
                    "rowid" => "0",
                    "UserID" => $_SESSION["username"],
                    "BatchNo" => $BatchNo,
                    "ExpDate" => $ExpDate,
                    "cnfid" => "",
                ];
                $this->db->insert(
                    db_prefix() . "K1history",
                    $data_array_result
                );
                $i++;
            }
            // update Purchase return table
            $roundoffamt = round($TotalNetAmt) - $TotalNetAmt;
            $data_array = [
                "Purchamt" => $TotalItemAmt,
                "Discamt" => $TotalDiscAmt,
                "cgstamt" => $TotaCGSTAmt,
                "sgstamt" => $TotaSGSTAmt,
                "igstamt" => $TotaIGSTAmt,
                "RoundOffAmt" => $roundoffamt,
                "Invamt" => round($TotalNetAmt),
            ];
            $this->db->where("PlantID", $PlantID);
            $this->db->LIKE("FY", $FY);
            $this->db->where("PurchRtnID ", $PurchRetID);
            $this->db->update(db_prefix() . "K1purchasereturn", $data_array);
            //Delete Live history table record
            $this->db->where("PlantID", $PlantID);
            $this->db->where("FY", $FY);
            $this->db->where("VoucherID", $PurchRetID);
            $this->db->delete(db_prefix() . "accountledger");
            $UserID = $_SESSION["username"];
            $narration = "Purchase Return Against " . $PurchID;
            $new_purchase_ReturnorderNumbar = $PurchRetID;
            $ord = 1;
            // Add Ledger Entry
            // Debit to Party
            $Purchase_ledger_entry = [
                "PlantID" => $PlantID,
                "FY" => $FY,
                "Transdate" => date("Y-m-d h:i:s"),
                "VoucherID" => $new_purchase_ReturnorderNumbar,
                "Transdate2" => date("Y-m-d h:i:s"),
                "PartyID" => "KASPL",
                "AccountID" => $AccountID,
                "CounterAccount" => "PURCHASER",
                "CenterID" => $CenterID,
                "EntryFor" => 2,
                "TType" => "D",
                "Amount" => $TotalNetAmt,
                "Narration" => $narration,
                "PassedFrom" => "PURCHASER RETURN",
                "OrdinalNo" => $ord,
                "UserID" => $UserID,
            ];
            $this->db->insert(
                db_prefix() . "accountledger",
                $Purchase_ledger_entry
            );
            $ord++;
            if ($TotalDiscAmt > 0) {
                // Debit to Discount Ledger
                $Purchase_ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => date("Y-m-d h:i:s"),
                    "VoucherID" => $new_purchase_ReturnorderNumbar,
                    "Transdate2" => date("Y-m-d h:i:s"),
                    "PartyID" => "KASPL",
                    "AccountID" => "DISCR",
                    "CounterAccount" => "PURCHASER",
                    "CenterID" => $CenterID,
                    "EntryFor" => 2,
                    "TType" => "D",
                    "Amount" => $TotalDiscAmt,
                    "Narration" => $narration,
                    "PassedFrom" => "PURCHASER RETURN",
                    "OrdinalNo" => $ord,
                    "UserID" => $UserID,
                ];
                $this->db->insert(
                    db_prefix() . "accountledger",
                    $Purchase_ledger_entry
                );
                $ord++;
            }
            // Cradit to purchase Return Ledger
            $Purchase_ledger_entry = [
                "PlantID" => $PlantID,
                "FY" => $FY,
                "Transdate" => date("Y-m-d h:i:s"),
                "VoucherID" => $new_purchase_ReturnorderNumbar,
                "Transdate2" => date("Y-m-d h:i:s"),
                "PartyID" => "KASPL",
                "AccountID" => "PURCHASER",
                "CounterAccount" => $AccountID,
                "CenterID" => $CenterID,
                "EntryFor" => 2,
                "TType" => "C",
                "Amount" => $TotalItemAmt,
                "Narration" => $narration,
                "PassedFrom" => "PURCHASE RETURN",
                "OrdinalNo" => $ord,
                "UserID" => $UserID,
            ];
            $this->db->insert(
                db_prefix() . "accountledger",
                $Purchase_ledger_entry
            );
            $ord++;
            if ($TotaIGSTAmt > 0) {
                // Cradit  to CGST Ledger
                $Purchase_ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => date("Y-m-d h:i:s"),
                    "VoucherID" => $new_purchase_ReturnorderNumbar,
                    "Transdate2" => date("Y-m-d h:i:s"),
                    "PartyID" => "KASPL",
                    "AccountID" => "IGST",
                    "CounterAccount" => $AccountID,
                    "CenterID" => $CenterID,
                    "EntryFor" => 2,
                    "TType" => "C",
                    "Amount" => $TotaIGSTAmt,
                    "Narration" => $narration,
                    "PassedFrom" => "PURCHASE RETURN",
                    "OrdinalNo" => $ord,
                    "UserID" => $UserID,
                ];
                $this->db->insert(
                    db_prefix() . "accountledger",
                    $Purchase_ledger_entry
                );
                $ord++;
            } else {
                // Cradit  to CGST Ledger
                $Purchase_ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => date("Y-m-d h:i:s"),
                    "VoucherID" => $new_purchase_ReturnorderNumbar,
                    "Transdate2" => date("Y-m-d h:i:s"),
                    "PartyID" => "KASPL",
                    "AccountID" => "CGST",
                    "CounterAccount" => $AccountID,
                    "CenterID" => $CenterID,
                    "EntryFor" => 2,
                    "TType" => "C",
                    "Amount" => $TotaCGSTAmt,
                    "Narration" => $narration,
                    "PassedFrom" => "PURCHASER RETURN",
                    "OrdinalNo" => $ord,
                    "UserID" => $UserID,
                ];
                $this->db->insert(
                    db_prefix() . "accountledger",
                    $Purchase_ledger_entry
                );
                $ord++;
                // Cradit  to CGST Ledger
                $Purchase_ledger_entry = [
                    "PlantID" => $PlantID,
                    "FY" => $FY,
                    "Transdate" => date("Y-m-d h:i:s"),
                    "VoucherID" => $new_purchase_ReturnorderNumbar,
                    "Transdate2" => date("Y-m-d h:i:s"),
                    "PartyID" => "KASPL",
                    "AccountID" => "SGST",
                    "CounterAccount" => $AccountID,
                    "CenterID" => $CenterID,
                    "EntryFor" => 2,
                    "TType" => "C",
                    "Amount" => $TotaSGSTAmt,
                    "Narration" => $narration,
                    "PassedFrom" => "PURCHASER RETURN",
                    "OrdinalNo" => $ord,
                    "UserID" => $UserID,
                ];
                $this->db->insert(
                    db_prefix() . "accountledger",
                    $Purchase_ledger_entry
                );
                $ord++;
            }
            return true;
        }
    }
    //=================== Get Vendor Inward List ===================================
    public function GetInwardData($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $CenterID = $data["CenterID"];
        $AccountID = $data["AccountID"];
        $sql1 =
            "(" .
            db_prefix() .
            'K1Inwardmaster.Transdate BETWEEN "' .
            $from_date .
            ' 00:00:00" AND "' .
            $to_date .
            ' 23:59:59")
		AND tblK1Inwardmaster.AccountID = "' .
            $AccountID .
            '"';
        if (!empty($CenterID)) {
            $sql1 .=
                " AND " .
                db_prefix() .
                'K1Inwardmaster.CenterID = "' .
                $CenterID .
                '"';
        }
        $join = "";
        $join .=
            " INNER JOIN tblCenterMaster ON tblCenterMaster.CenterID = tblK1Inwardmaster.CenterID ";
        $sql =
            "SELECT " .
            db_prefix() .
            'K1Inwardmaster.*,tblCenterMaster.CenterName
		FROM ' .
            db_prefix() .
            "K1Inwardmaster " .
            $join .
            "  WHERE " .
            $sql1 .
            '
		ORDER BY PurchID DESC';
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    //=================== Get Vendor Purchase Request data =========================
    public function GetPurchaseRequestData($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $CenterID = $data["CenterID"];
        $AccountID = $data["AccountID"];
        $sql1 =
            "(" .
            db_prefix() .
            'K1purchase_request_master.Transdate BETWEEN "' .
            $from_date .
            ' 00:00:00" AND "' .
            $to_date .
            ' 23:59:59")
		AND tblK1purchase_request_master.AccountID = "' .
            $AccountID .
            '"';
        if (!empty($CenterID)) {
            $sql1 .=
                " AND " .
                db_prefix() .
                'K1purchase_request_master.CenterID = "' .
                $CenterID .
                '"';
        }
        $join = "";
        $join .=
            " INNER JOIN tblCenterMaster ON tblCenterMaster.CenterID = tblK1purchase_request_master.CenterID ";
        $sql1 .= " ORDER BY PurchID DESC";
        $sql =
            "SELECT " .
            db_prefix() .
            'K1purchase_request_master.*,tblCenterMaster.CenterName
		FROM ' .
            db_prefix() .
            "K1purchase_request_master " .
            $join .
            " WHERE " .
            $sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    //====================== Get Vendor Purchase Order List ========================
    public function GetPurchaseOrderData($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $CenterID = $data["CenterID"];
        $AccountID = $data["AccountID"];
        $sql1 =
            "(" .
            db_prefix() .
            'K1purchasemaster.Transdate BETWEEN "' .
            $from_date .
            ' 00:00:00" AND "' .
            $to_date .
            ' 23:59:59")
		AND tblK1purchasemaster.AccountID = "' .
            $AccountID .
            '"';
        if (!empty($CenterID)) {
            $sql1 .=
                " AND " .
                db_prefix() .
                'K1purchasemaster.CenterID = "' .
                $CenterID .
                '"';
        }
        $join = "";
        $join .=
            " INNER JOIN tblCenterMaster ON tblCenterMaster.CenterID = tblK1purchasemaster.CenterID ";
        $join .=
            " LEFT JOIN tblK1purchase_request_master ON tblK1purchase_request_master.PurchID = tblK1purchasemaster.Pr_no ";
        $sql1 .= " ORDER BY PurchID DESC";
        $sql =
            "SELECT " .
            db_prefix() .
            'K1purchasemaster.*,tblK1purchase_request_master.Transdate as PRDate,tblCenterMaster.CenterName
		FROM ' .
            db_prefix() .
            "K1purchasemaster " .
            $join .
            " WHERE " .
            $sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    //======================= Vendor Purchase Invoice List =========================
    public function GetPurchaseInvoiceInfo($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $CenterID = $data["CenterID"];
        $AccountID = $data["AccountID"];
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        $UserID = $this->session->userdata("username");
        $sql1 =
            '(tblK1purchasemaster.Transdate BETWEEN "' .
            $from_date .
            ' 00:00:00" AND "' .
            $to_date .
            ' 23:59:59")
		AND tblK1purchasemaster.Flag = "Y" AND tblK1purchasemaster.Inv_No IS NOT NULL
		AND tblK1purchasemaster.AccountID = "' .
            $AccountID .
            '"';
        if (!empty($CenterID)) {
            $sql1 .=
                " AND " .
                db_prefix() .
                'K1purchasemaster.CenterID = "' .
                $CenterID .
                '"';
        }
        $join = "";
        $join .=
            " INNER JOIN tblCenterMaster ON tblCenterMaster.CenterID = tblK1purchasemaster.CenterID ";
        $join .=
            " LEFT JOIN tblK1purchase_request_master ON tblK1purchase_request_master.PurchID = tblK1purchasemaster.Pr_no ";
        $sql1 .= " ORDER BY Inv_No DESC";
        $sql =
            "SELECT " .
            db_prefix() .
            'K1purchasemaster.*,tblK1purchase_request_master.Transdate as PRDate,tblCenterMaster.CenterName
		FROM ' .
            db_prefix() .
            "K1purchasemaster " .
            $join .
            " WHERE " .
            $sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    public function GetInwardNoWiseData($id)
    {
        $this->db->select(
            "tblK1Inwardmaster.*,tblCenterMaster.CenterName,tblclients.company,tblclients.phonenumber,tblxx_statelist.state_name"
        );
        $this->db->from(db_prefix() . "K1Inwardmaster");
        $this->db->join(
            db_prefix() . "CenterMaster",
            "CenterMaster.CenterID = tblK1Inwardmaster.CenterID",
            "left"
        );
        $this->db->join(
            db_prefix() . "clients",
            "clients.AccountID = tblK1Inwardmaster.AccountID",
            "left"
        );
        $this->db->join(
            db_prefix() . "xx_statelist",
            "xx_statelist.short_name = tblclients.state",
            "left"
        );
        $this->db->where("tblK1Inwardmaster.PurchID", $id);
        return $this->db->get()->row();
    }
    public function GetPurchaseRequestPrNoWiseData($id)
    {
        $this->db->select(
            "tblK1purchase_request_master.*, tblCenterMaster.CenterName,tblclients.company,tblclients.phonenumber,tblxx_statelist.state_name"
        );
        $this->db->from(db_prefix() . "K1purchase_request_master");
        $this->db->join(
            db_prefix() . "CenterMaster",
            "CenterMaster.CenterID = tblK1purchase_request_master.CenterID",
            "left"
        );
        $this->db->join(
            db_prefix() . "clients",
            "clients.AccountID = tblK1purchase_request_master.AccountID",
            "left"
        );
        $this->db->join(
            db_prefix() . "xx_statelist",
            "xx_statelist.short_name = tblclients.state",
            "left"
        );
        $this->db->where("tblK1purchase_request_master.PurchID", $id);
        return $this->db->get()->row();
    }
    public function GethistoryDetails($ID)
    {
        $this->db->select(
            "tblK1history.*,tblproduct.ProductName,tblproduct.hsn_code,tblbrands.BrandName,tblproduct.unit,tblproduct.PackingQty,tblproduct.PackingWeight,tbltaxes.taxrate"
        );
        $this->db->from(db_prefix() . "K1history");
        $this->db->join(
            db_prefix() . "product",
            "product.ProductID = K1history.ItemID",
            "left"
        );
        $this->db->join(
            db_prefix() . "brands",
            "brands.id = tblproduct.BrandId",
            "left"
        );
        $this->db->join(
            db_prefix() . "taxes",
            "taxes.id = tblproduct.gst",
            "left"
        );
        $this->db->where("tblK1history.OrderID", $ID);
        return $this->db->get()->result_array();
    }
    public function GetPurchOrderDataPoWise($id)
    {
        $this->db->select(
            "tblK1purchasemaster.*, tblCenterMaster.CenterName,tblclients.company,tblclients.phonenumber,tblxx_statelist.state_name,tblK1purchase_request_master.Transdate AS PRDate"
        );
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->join(
            db_prefix() . "CenterMaster",
            "CenterMaster.CenterID = tblK1purchasemaster.CenterID",
            "left"
        );
        $this->db->join(
            db_prefix() . "clients",
            "clients.AccountID = tblK1purchasemaster.AccountID",
            "left"
        );
        $this->db->join(
            db_prefix() . "xx_statelist",
            "xx_statelist.short_name = tblclients.state",
            "left"
        );
        $this->db->join(
            db_prefix() . "K1purchase_request_master",
            "K1purchase_request_master.PurchID = tblK1purchasemaster.Pr_no",
            "left"
        );
        $this->db->where("tblK1purchasemaster.PurchID", $id);
        return $this->db->get()->row();
    }
    public function GetPurchaseInvoiceByInvoiceNo($id)
    {
        $this->db->select(
            "tblK1purchasemaster.*, tblCenterMaster.CenterName,tblclients.company,tblclients.phonenumber,tblxx_statelist.state_name,tblK1purchase_request_master.Transdate AS PRDate"
        );
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->join(
            db_prefix() . "CenterMaster",
            "CenterMaster.CenterID = tblK1purchasemaster.CenterID",
            "left"
        );
        $this->db->join(
            db_prefix() . "clients",
            "clients.AccountID = tblK1purchasemaster.AccountID",
            "left"
        );
        $this->db->join(
            db_prefix() . "xx_statelist",
            "xx_statelist.short_name = tblclients.state",
            "left"
        );
        $this->db->join(
            db_prefix() . "K1purchase_request_master",
            "K1purchase_request_master.PurchID = tblK1purchasemaster.Pr_no",
            "left"
        );
        $this->db->where("tblK1purchasemaster.Inv_No", $id);
        return $this->db->get()->row();
    }
    public function GetInvoiceHistoryDetails($ID)
    {
        $this->db->select(
            "tblK1history.*,tblproduct.ProductName,tblproduct.hsn_code,tblbrands.BrandName,tblproduct.unit,tblproduct.PackingQty,tblproduct.PackingWeight,tbltaxes.taxrate"
        );
        $this->db->from(db_prefix() . "K1history");
        $this->db->join(
            db_prefix() . "product",
            "product.ProductID = K1history.ItemID",
            "left"
        );
        $this->db->join(
            db_prefix() . "brands",
            "brands.id = tblproduct.BrandId",
            "left"
        );
        $this->db->join(
            db_prefix() . "taxes",
            "taxes.id = tblproduct.gst",
            "left"
        );
        $this->db->where("tblK1history.TransID", $ID);
        return $this->db->get()->result_array();
    }
    public function getRowData($tbl, $select = "*", $where)
    {
        $this->db->select($select);
        $this->db->from($tbl);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row();
    }
    public function getPOFilterDropdown($value, $type, $toType, $data)
    {
        $fy = $this->session->userdata("finacial_year");
        if ($type == "Center" && $toType == "Party") {
            // $this->db->distinct();
            $this->db->select(
                "
	            h.AccountID AS id,
	            MAX(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN c.company END) AS name,
	            (
                  SUM(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN h.BilledQty ELSE 0 END)
    				+ COALESCE(SUM(sm.OQty),0)
    				- SUM(CASE WHEN h.TType='P' AND h.TType2='PURCHASE RETURN' THEN h.BilledQty ELSE 0 END)
    				- SUM(CASE WHEN h.TType='O' AND h.TType2='SALE' THEN h.BilledQty ELSE 0 END)
    				+ SUM(CASE WHEN h.TType='SR' AND h.TType2='FRESH RETURN' THEN h.BilledQty ELSE 0 END)
    				+ SUM(CASE WHEN h.TType='T' AND h.TType2='IN' THEN h.BilledQty ELSE 0 END)
    				- SUM(CASE WHEN h.TType='T' AND h.TType2='OUT' THEN h.BilledQty ELSE 0 END)
    				+ SUM(CASE WHEN h.TType='I' AND h.TType2='INWARD' THEN h.BilledQty ELSE 0 END)
    				- SUM(CASE WHEN h.TType='L' AND h.TType2='LIENMARK' THEN h.BilledQty ELSE 0 END)
    				- SUM(CASE WHEN h.TType='X' THEN h.BilledQty ELSE 0 END)
                ) AS AvailableQty,
                DATE_ADD(
                    MAX(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN DATE(h.TransDate) END),
                    INTERVAL ANY_VALUE(p.PurchaseReturnDay) DAY
                ) AS LastReturnDate
            ",
                false
            );
            $this->db->from("tblK1history h");
            $this->db->join(
                "tblclients c",
                "c.AccountID = h.AccountID",
                "left"
            );
            $this->db->join("tblproduct p", "p.ProductID = h.ItemID", "left");
            $this->db->join(
                "tblK1stockmaster sm",
                "sm.BatchNo = h.BatchNo AND sm.FY=" . $fy,
                "left"
            );
            $this->db->where(["h.CenterID" => $value, "h.FY" => $fy]);
            $this->db->group_by(["h.AccountID"]);
            $this->db->having("AvailableQty >", 0);
            // $this->db->having('LastReturnDate >=', date('Y-m-d'));
            $query = $this->db->get();
            return $query->result();
        } elseif ($type == "Center" && $toType == "Item") {
            $this->db->select(
                "
	            h.ItemID AS id,
	            ANY_VALUE(p.ProductName) AS name,
	            (
                  SUM(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN h.BilledQty ELSE 0 END)
    				+ COALESCE(SUM(sm.OQty),0)
    				- SUM(CASE WHEN h.TType='P' AND h.TType2='PURCHASE RETURN' THEN h.BilledQty ELSE 0 END)
    				- SUM(CASE WHEN h.TType='O' AND h.TType2='SALE' THEN h.BilledQty ELSE 0 END)
    				+ SUM(CASE WHEN h.TType='SR' AND h.TType2='FRESH RETURN' THEN h.BilledQty ELSE 0 END)
    				+ SUM(CASE WHEN h.TType='T' AND h.TType2='IN' THEN h.BilledQty ELSE 0 END)
    				- SUM(CASE WHEN h.TType='T' AND h.TType2='OUT' THEN h.BilledQty ELSE 0 END)
    				+ SUM(CASE WHEN h.TType='I' AND h.TType2='INWARD' THEN h.BilledQty ELSE 0 END)
    				- SUM(CASE WHEN h.TType='L' AND h.TType2='LIENMARK' THEN h.BilledQty ELSE 0 END)
    				- SUM(CASE WHEN h.TType='X' THEN h.BilledQty ELSE 0 END)
                ) AS AvailableQty,
                DATE_ADD(
                    MAX(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN DATE(h.TransDate) END),
                    INTERVAL ANY_VALUE(p.PurchaseReturnDay) DAY
                ) AS LastReturnDate
            ",
                false
            );
            $this->db->from("tblK1history h");
            $this->db->join("tblproduct p", "p.ProductID = h.ItemID", "left");
            $this->db->join(
                "tblK1stockmaster sm",
                "sm.BatchNo = h.BatchNo AND sm.FY=" . $fy,
                "left"
            );
            $this->db->where([
                "h.CenterID" => $data["CenterID"],
                "h.BatchNo !=" => "",
                "h.FY" => $fy,
            ]);
            $this->db->group_by(["h.ItemID"]);
            $this->db->having("AvailableQty >", 0);
            // $this->db->having('LastReturnDate >=', date('Y-m-d'));
            $query = $this->db->get();
            return $query->result();
        } elseif ($type == "Party" && $toType == "Item") {
            $this->db->select(
                "
                MAX(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN h.ItemID END) AS id,
                MAX(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN p.ProductName END) AS name,
                (
                    SUM(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN h.BilledQty ELSE 0 END)
									+ COALESCE(SUM(sm.OQty),0)
									- SUM(CASE WHEN h.TType='P' AND h.TType2='PURCHASE RETURN' THEN h.BilledQty ELSE 0 END)
									- SUM(CASE WHEN h.TType='O' AND h.TType2='SALE' THEN h.BilledQty ELSE 0 END)
									+ SUM(CASE WHEN h.TType='SR' AND h.TType2='FRESH RETURN' THEN h.BilledQty ELSE 0 END)
									+ SUM(CASE WHEN h.TType='T' AND h.TType2='IN' THEN h.BilledQty ELSE 0 END)
									- SUM(CASE WHEN h.TType='T' AND h.TType2='OUT' THEN h.BilledQty ELSE 0 END)
									+ SUM(CASE WHEN h.TType='I' AND h.TType2='INWARD' THEN h.BilledQty ELSE 0 END)
									- SUM(CASE WHEN h.TType='L' AND h.TType2='LIENMARK' THEN h.BilledQty ELSE 0 END)
									- SUM(CASE WHEN h.TType='X' THEN h.BilledQty ELSE 0 END)
                ) AS AvailableQty,
                DATE_ADD(
                    MAX(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN DATE(h.TransDate) END),
                    INTERVAL ANY_VALUE(p.PurchaseReturnDay) DAY
                ) AS LastReturnDate
            ",
                false
            );
            $this->db->from("tblK1history h");
            $this->db->join("tblproduct p", "p.ProductID = h.ItemID", "left");
            $this->db->join(
                "tblK1stockmaster sm",
                "sm.BatchNo = h.BatchNo AND sm.FY=" . $fy,
                "left"
            );
            $this->db->where([
                "h.CenterID" => $data["CenterID"],
                "h.FY" => $fy,
            ]);
            $this->db->group_by("h.ItemID");
            // $this->db->having("SUM(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN 1 ELSE 0 END) >", 0, false);
            $this->db->having("AvailableQty >", 0);
            // $this->db->having('LastReturnDate >=', date('Y-m-d'));
            $query = $this->db->get();
            return $query->result();
        } else {
            $result = [];
            return $result;
        }
    }
    public function getFilterReturnValidityStockReport($payload)
    {
        $fy = $this->session->userdata("finacial_year");
        $CenterID = $payload["CenterID"];
        $AccountID = $payload["AccountID"];
        $ItemID = $payload["ItemID"];
        $rType = $payload["rType"];
        $this->db->select(
            "
            h.BatchNo,
            h.ItemID,
            SUM(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN h.BilledQty ELSE 0 END) AS OrderQty,
            COALESCE(
                MAX(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN DATE(h.TransDate) END),
                MAX(DATE(h.TransDate))
            ) AS TransDate,
            COALESCE(
                MAX(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN h.OrderID END),
                MAX(h.OrderID)
            ) AS OrderID,
            COALESCE(
                MAX(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN h.CenterID END),
                MAX(h.CenterID)
            ) AS CenterID,
            COALESCE(
                MAX(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN h.ExpDate END),
                MAX(h.ExpDate)
            ) AS ExpDate,
            COALESCE(
                MAX(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN h.AccountID END),
                MAX(h.AccountID)
            ) AS AccountID,
            COALESCE(
                MAX(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN c.company END),
                ANY_VALUE(c.company)
            ) AS company,
            ANY_VALUE(p.ProductName) AS ProductName,
            ANY_VALUE(p.hsn_code) AS hsn_code,
            ANY_VALUE(p.unit) AS unit,
            ANY_VALUE(p.PackingQty) AS PackingQty,
            ANY_VALUE(p.PackingWeight) AS PackingWeight,
            ANY_VALUE(b.BrandName) AS BrandName,
            ANY_VALUE(t.taxrate) AS taxrate,
            (
                SUM(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN h.BilledQty ELSE 0 END)
                + COALESCE(SUM(sm.OQty),0)
                - SUM(CASE WHEN h.TType='P' AND h.TType2='PURCHASE RETURN' THEN h.BilledQty ELSE 0 END)
                - SUM(CASE WHEN h.TType='O' AND h.TType2='SALE' THEN h.BilledQty ELSE 0 END)
                + SUM(CASE WHEN h.TType='SR' AND h.TType2='FRESH RETURN' THEN h.BilledQty ELSE 0 END)
                + SUM(CASE WHEN h.TType='T' AND h.TType2='IN' THEN h.BilledQty ELSE 0 END)
                - SUM(CASE WHEN h.TType='T' AND h.TType2='OUT' THEN h.BilledQty ELSE 0 END)
                + SUM(CASE WHEN h.TType='I' AND h.TType2='INWARD' THEN h.BilledQty ELSE 0 END)
                - SUM(CASE WHEN h.TType='L' AND h.TType2='LIENMARK' THEN h.BilledQty ELSE 0 END)
                - SUM(CASE WHEN h.TType='X' THEN h.BilledQty ELSE 0 END)
            ) AS AvailableQty,
            DATE_ADD(
                MAX(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN DATE(h.TransDate) END),
                INTERVAL ANY_VALUE(p.PurchaseReturnDay) DAY
            ) AS LastReturnDate
        ",
            false
        );
        // + COALESCE(MAX(sm.OQty),0)
        // + SUM(CASE WHEN h.TType='SR' AND h.TType2='FRESH RETURN' THEN h.OrderQty ELSE 0 END)
        // + SUM(CASE WHEN h.TType='T' AND h.TType2='IN' THEN h.OrderQty ELSE 0 END)
        // - SUM(CASE WHEN h.TType='T' AND h.TType2='OUT' THEN h.OrderQty ELSE 0 END)
        // + SUM(CASE WHEN h.TType='I' AND h.TType2='INWARD' THEN h.OrderQty ELSE 0 END)
        // - SUM(CASE WHEN h.TType='L' AND h.TType2='LIENMARK' THEN h.OrderQty ELSE 0 END)
        // - SUM(CASE WHEN h.TType='X' THEN h.OrderQty ELSE 0 END)
        $this->db->from("tblK1history h");
        $this->db->join(
            "tblK1stockmaster sm",
            "sm.BatchNo = h.BatchNo AND sm.ItemID = h.ItemID AND sm.CenterID = h.CenterID AND sm.FY=" .
                $fy,
            "left"
        );
        $this->db->join("tblproduct p", "p.ProductID = h.ItemID", "left");
        $this->db->join("tblclients c", "c.AccountID = h.AccountID", "left");
        $this->db->join("tblbrands b", "b.id = p.BrandId", "left");
        $this->db->join("tbltaxes t", "t.id = p.gst", "left");
        $this->db->where(["h.BatchNo !=" => "", "h.FY" => $fy]);
        if (!empty($CenterID)) {
            $this->db->where(["h.CenterID" => $CenterID]);
        }
        if (!empty($AccountID)) {
            $this->db->where(["h.AccountID" => $AccountID]);
        }
        if (!empty($ItemID)) {
            $this->db->where(["h.ItemID" => $ItemID]);
        }
        $this->db->group_by(["h.BatchNo", "h.ItemID"]);
        $this->db->having("AvailableQty >", 0);
        $this->db->having("OrderQty >", 0);
        if ($rType == "OutDate") {
            $this->db->having("LastReturnDate <", date("Y-m-d"));
            $this->db->order_by("LastReturnDate", "ASC");
        } elseif ($rType == "All") {
            // no contidion
            $this->db->order_by("LastReturnDate", "DESC");
        } else {
            $this->db->having("LastReturnDate >=", date("Y-m-d"));
            $this->db->order_by("LastReturnDate", "ASC");
        }
        $query = $this->db->get();
        return $query->result();
    }
    public function getPurchaseReturnReportFilter($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $fy = $this->session->userdata("finacial_year");
        $selected_company = $this->session->userdata("root_company");
        if ($data["ReportType"] == "1") {
            // Report type bill
            $this->db->select("pr.*, cm.CenterName");
            $this->db->select(
                "(SELECT GROUP_CONCAT(company SEPARATOR ',')
													FROM " .
                    db_prefix() .
                    "clients
													WHERE " .
                    db_prefix() .
                    "clients.AccountID = pr.AccountID
													AND " .
                    db_prefix() .
                    "clients.PlantID = '$selected_company'
												) AS AccountName",
                false
            );
            $this->db->from(db_prefix() . "K1purchasereturn pr");
            $this->db->join(
                db_prefix() . "CenterMaster cm",
                "cm.CenterID = pr.CenterID",
                "left"
            );
            $this->db->where("pr.Transdate >=", $from_date . " 00:00:00");
            $this->db->where("pr.Transdate <=", $to_date . " 23:59:59");
            $this->db->where("pr.FY", $fy);
            $this->db->where("pr.PurchRtnID IS NOT NULL", null, false);
            $this->db->where("pr.PlantID", $selected_company);
            if (!empty($data["CenterID"])) {
                $this->db->where("pr.CenterID", $data["CenterID"]);
            }
            if (!empty($data["AccountID"])) {
                $this->db->where("pr.AccountID", $data["AccountID"]);
            }
            $this->db->order_by("pr.PurchRtnID", "DESC");
        } else {
            // Report type item
            $this->db->select(
                "pr.*, cm.CenterName, p.ProductName, p.hsn_code, b.BrandName"
            );
            $this->db->select(
                "(SELECT GROUP_CONCAT(company SEPARATOR ',')
													FROM " .
                    db_prefix() .
                    "clients
													WHERE " .
                    db_prefix() .
                    "clients.AccountID = pr.AccountID
													AND " .
                    db_prefix() .
                    "clients.PlantID = '$selected_company'
												) AS AccountName",
                false
            );
            $this->db->from(db_prefix() . "K1history pr");
            $this->db->join(
                db_prefix() . "CenterMaster cm",
                "cm.CenterID = pr.CenterID",
                "left"
            );
            $this->db->join(
                db_prefix() . "product p",
                "p.ProductID = pr.ItemID",
                "left"
            );
            $this->db->join(
                db_prefix() . "brands b",
                "b.id = p.BrandId",
                "left"
            );
            $this->db->where("pr.TransDate >=", $from_date . " 00:00:00");
            $this->db->where("pr.TransDate <=", $to_date . " 23:59:59");
            $this->db->where("pr.FY", $fy);
            $this->db->where("pr.TType", "P");
            $this->db->where("pr.TType2", "PURCHASE RETURN");
            $this->db->where("pr.PlantID", $selected_company);
            if (!empty($data["CenterID"])) {
                $this->db->where("pr.CenterID", $data["CenterID"]);
            }
            if (!empty($data["AccountID"])) {
                $this->db->where("pr.AccountID", $data["AccountID"]);
            }
            if (!empty($data["ItemID"])) {
                $this->db->where("pr.ItemID", $data["ItemID"]);
            }
            $this->db->order_by("pr.TransDate", "DESC");
        }
        return $this->db->get()->result_array();
    }

    public function get_purchase_order_reminder_report_list($data)
    {
        $selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);

        $this->db->select(
            db_prefix() .
                "K1purchasemaster.PurchID, " .
                db_prefix() .
                "K1purchasemaster.ReminderDate, " .
                db_prefix() .
                "K1purchasemaster.ReminderRemark, " .
                db_prefix() .
                "K1purchasemaster.ReminderSent"
        );
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->where(db_prefix() . "K1purchasemaster.PlantID", $selected_company);
        $this->db->where(db_prefix() . "K1purchasemaster.FY", $fy);
        $this->db->where(db_prefix() . "K1purchasemaster.Flag", "Y");
        $this->db->where(
            db_prefix() . "K1purchasemaster.ReminderDate IS NOT NULL",
            null,
            false
        );
        $this->db->where(db_prefix() . "K1purchasemaster.ReminderDate >=", $from_date);
        $this->db->where(db_prefix() . "K1purchasemaster.ReminderDate <=", $to_date);
        $this->db->order_by(db_prefix() . "K1purchasemaster.ReminderDate", "ASC");
        $this->db->order_by(db_prefix() . "K1purchasemaster.PurchID", "ASC");
        return $this->db->get()->result_array();
    }

    public function process_purchase_order_reminders()
    {
        $today = date("Y-m-d");
        $this->db->select(
            db_prefix() .
                "K1purchasemaster.*, " .
                db_prefix() .
                "rootcompany.company_name, " .
                db_prefix() .
                "rootcompany.einvoice_email AS company_email, " .
                db_prefix() .
                "rootcompany.einvoice_email"
        );
        $this->db->from(db_prefix() . "K1purchasemaster");
        $this->db->join(
            db_prefix() . "rootcompany",
            db_prefix() . "rootcompany.id = " . db_prefix() . "K1purchasemaster.PlantID",
            "left"
        );
        $this->db->where(db_prefix() . "K1purchasemaster.ReminderDate", $today);
        $this->db->where(db_prefix() . "K1purchasemaster.ReminderSent", 0);
        $this->db->where(db_prefix() . "K1purchasemaster.Flag", "Y");
        $this->db->where(
            db_prefix() . "K1purchasemaster.ReminderDate IS NOT NULL",
            null,
            false
        );
        $orders = $this->db->get()->result_array();

        if (empty($orders)) {
            return;
        }

        $this->load->library("email");
        $this->email->initialize();
        $this->email->set_newline(config_item("newline"));
        $this->email->set_crlf(config_item("crlf"));

        foreach ($orders as $order) {
            $recipientEmail = !empty($order["company_email"])
                ? $order["company_email"]
                : (!empty($order["einvoice_email"])
                    ? $order["einvoice_email"]
                    : get_option("smtp_email"));

            if (empty($recipientEmail)) {
                log_activity(
                    "Purchase Order Reminder email skipped for PO " .
                        $order["PurchID"] .
                        " - company email not configured."
                );
                continue;
            }

            $companyName = !empty($order["company_name"])
                ? $order["company_name"]
                : get_option("companyname");
            $reminderDate = _d($order["ReminderDate"]);
            $remark = !empty($order["ReminderRemark"])
                ? nl2br(html_escape($order["ReminderRemark"]))
                : "-";
            $poDate = _d(substr($order["Transdate"], 0, 10));

            $message =
                get_option("email_header") .
                "<p>Dear Team,</p>" .
                "<p>This is a reminder for the following Purchase Order:</p>" .
                "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse;'>" .
                "<tr><td><strong>PO Number</strong></td><td>" .
                html_escape($order["PurchID"]) .
                "</td></tr>" .
                "<tr><td><strong>PO Date</strong></td><td>" .
                $poDate .
                "</td></tr>" .
                "<tr><td><strong>Reminder Date</strong></td><td>" .
                $reminderDate .
                "</td></tr>" .
                "<tr><td><strong>Remark</strong></td><td>" .
                $remark .
                "</td></tr>" .
                "<tr><td><strong>Invoice Amount</strong></td><td>" .
                app_format_money($order["Invamt"], get_base_currency()) .
                "</td></tr>" .
                "</table>" .
                "<p>Regards,<br>" .
                html_escape($companyName) .
                "</p>" .
                get_option("email_footer");

            $this->email->clear(true);
            $this->email->from(get_option("smtp_email"), $companyName);
            $this->email->to($recipientEmail);

            $systemBCC = get_option("bcc_emails");
            if ($systemBCC != "") {
                $this->email->bcc($systemBCC);
            }

            $this->email->subject(
                "Purchase Order Reminder - " . $order["PurchID"]
            );
            $this->email->message($message);

            if ($this->email->send(true)) {
                $this->db->where("PurchID", $order["PurchID"]);
                $this->db->where("PlantID", $order["PlantID"]);
                $this->db->where("FY", $order["FY"]);
                $this->db->update(db_prefix() . "K1purchasemaster", [
                    "ReminderSent" => 1,
                ]);
                log_activity(
                    "Purchase Order Reminder email sent for PO " . $order["PurchID"]
                );
            } else {
                log_activity(
                    "Purchase Order Reminder email failed for PO " .
                        $order["PurchID"] .
                        " - " .
                        $this->email->print_debugger(["headers"])
                );
            }
        }
    }

    public function generateNextVoucherIDNew($selected_date = '', $plant_id = '', $passage_from = '')
	{
		if (empty($selected_date)) {
			$selected_date = date('Y-m-d');
		} else {
			$selected_date = date('Y-m-d', strtotime($selected_date));
		}
		if (empty($plant_id)) {
			$plant_id = $this->session->userdata('root_company');
		}
		// Extract date components
		$date_parts = explode('-', $selected_date);
		$year = substr($date_parts[0], 2);
		$month = $date_parts[1];
		$day = $date_parts[2];
		$plant_id_formatted = str_pad($plant_id, 2, '0', STR_PAD_LEFT);
		switch (strtoupper($passage_from)) {
			case 'JOURNAL':
				$prefix = 'J';
				break;
			case 'RECEIPTS':
				$prefix = 'R';
				break;
			case 'PAYMENTS':
				$prefix = 'P';
				break;
			default:
				$prefix = 'C';
				break;
		}
		// Build base: J0126040300001 or C0126040300001
		$voucher_base = $prefix . $plant_id_formatted . $year . $month . $day;
		$sql = "SELECT VoucherID 
							FROM " . db_prefix() . "accountledger 
							WHERE PlantID = " . (int)$plant_id . " 
							AND PassedFrom = '" . $this->db->escape_str(strtoupper($passage_from)) . "' 
							AND DATE(Transdate) = '" . $this->db->escape_str($selected_date) . "' 
							AND VoucherID LIKE '" . $this->db->escape_like_str($voucher_base) . "%'
							ORDER BY CAST(RIGHT(VoucherID, 3) AS UNSIGNED) DESC
							LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (!empty($row['VoucherID'])) {
			$lastNumber = (int) substr($row['VoucherID'], -3);
			$nextNumber = $lastNumber + 1;
		} else {
			$nextNumber = 1;
		}
		$new_voucher_number = $voucher_base . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
		return $new_voucher_number;
	}

}
