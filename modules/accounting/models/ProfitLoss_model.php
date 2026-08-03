<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProfitLoss_model extends App_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  //=========================== Other Income =====================================
  public function GetOtherIncome($fromdate = "", $todate = "")
  {
    $fy = $this->session->userdata('finacial_year');
    $last_fy = $fy - 1;

    $selected_company = $this->session->userdata('root_company');

    $from_date = empty($fromdate) ? '20' . $fy . '-04-01 00:00:00' : $fromdate;
    $to_date = empty($todate) ? date('Y-m-d H:i:s') : $todate;

    $this->db->select("SUM(tblaccountledger.Amount) AS SumAmt, tblaccountledger.TType, tblaccountledger.FY, tblclients.company, tblclients.AccountID, tblclients.SubActGroupID1, tblaccountgroupssub1.SubActGroupName AS ActGrp1, tblclients.SubActGroupID, tblaccountgroupssub.SubActGroupName ");
    $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID');
    $this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblclients.SubActGroupID1');
    $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
    $this->db->where_in('tblaccountledger.FY', array($fy, $last_fy));
    $this->db->where('tblaccountledger.PlantID', $selected_company);
    $this->db->where(db_prefix() . 'accountledger.PartyID', 'KASPL');
    $this->db->where('tblclients.ActGroupID', '10011');
    $this->db->where('tblclients.AccountID !=', 'PDISC');
    $this->db->where("tblaccountledger.Transdate BETWEEN '$from_date' AND '$to_date'");
    $this->db->group_by(array('tblaccountledger.FY', 'tblaccountledger.TType', 'tblaccountledger.AccountID'));

    $OtherIncomeData = $this->db->get('tblaccountledger')->result_array();

    // Build Tree
    $tree = array();
    $ledger = array();

    foreach ($OtherIncomeData as $row) {
      $g1 = $row['SubActGroupID1'];
      $g2 = $row['SubActGroupID'];
      $acc = $row['AccountID'];

      if (!isset($tree[$g1])) {
        $tree[$g1] = array('Group1Name' => $row['ActGrp1'], 'Group1ID'   => $g1, 'SubGroups2' => array());
      }

      if (!isset($tree[$g1]['SubGroups2'][$g2])) {
        $tree[$g1]['SubGroups2'][$g2] = array('SubGroupName' => $row['SubActGroupName'], 'SubActGroupID' => $g2, 'Accounts'     => array());
      }

      if (!isset($tree[$g1]['SubGroups2'][$g2]['Accounts'][$acc])) {
        $tree[$g1]['SubGroups2'][$g2]['Accounts'][$acc] = array(
          'AccountName' => $row['company'],
          'AccountID' => $acc
        );
      }

      if (!isset($ledger[$acc])) {
        $ledger[$acc] = array(
          $fy => array('C' => 0, 'D' => 0),
          $last_fy => array('C' => 0, 'D' => 0)
        );
      }

      $ledger[$acc][$row['FY']][$row['TType']] = $row['SumAmt'];
    }

    /*
    |--------------------------------------------------------------------------
    | Calculate Balances
    |--------------------------------------------------------------------------
    */

    $nestedData = array();

    $TotalIncome = 0;
    $TotalIncomePre = 0;

    foreach ($tree as &$group1) {
      $group1Total = 0;
      $group1TotalPre = 0;

      foreach ($group1['SubGroups2'] as &$group2) {
        $group2Total = 0;
        $group2TotalPre = 0;

        foreach ($group2['Accounts'] as &$account) {
          $acc = $account['AccountID'];

          $current =
            ($ledger[$acc][$fy]['C'] ?? 0)
            -
            ($ledger[$acc][$fy]['D'] ?? 0);

          $previous =
            ($ledger[$acc][$last_fy]['C'] ?? 0)
            -
            ($ledger[$acc][$last_fy]['D'] ?? 0);

          $account['AccountClsBal'] = abs($current);
          $account['AccountClsBalPre'] = abs($previous);

          $group2Total += $current;
          $group2TotalPre += $previous;
        }

        $group2['Accounts'] = array_values($group2['Accounts']);

        $group2['Group2ClsBal'] = abs($group2Total);
        $group2['Group2ClsBalPre'] = abs($group2TotalPre);

        $group1Total += $group2Total;
        $group1TotalPre += $group2TotalPre;
      }

      $group1['SubGroups2'] = array_values($group1['SubGroups2']);

      $group1['Group1ClsBal'] = abs($group1Total);
      $group1['Group1ClsBalPre'] = abs($group1TotalPre);

      $TotalIncome += abs($group1Total);
      $TotalIncomePre += abs($group1TotalPre);

      $nestedData[] = $group1;
    }

    $OtherIncome = new stdClass();
    $OtherIncome->nestedData = $nestedData;
    $OtherIncome->CurrentYear = $TotalIncome;
    $OtherIncome->PriviousYear = $TotalIncomePre;

    return $OtherIncome;
  }

  //===================== Opening Inventory Amount ===============================
  public function GetOpeningInventoryAmt($fromdate = "", $todate = "")
  {
    $fy = $this->session->userdata('finacial_year');
    $selected_company = $this->session->userdata('root_company');
    $this->db->select('SUM(tblK1stockmaster.OQty) AS OQty,SUM(tblK1stockmaster.PurchRate) AS TotalValue,tblK1stockmaster.ItemID,tblK1stockmaster.FY,
		tblproduct.ProductID,tblproduct.ProductName,
		tblproduct.Category AS MainGrpID,tblK1ItemCategory.SubcategoryName AS MainGrpName,
		tblproduct.Subcategory AS SubGrpID1,tblK1ItemSubCategory.SubCategoryName AS Grp1Name');
    $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1stockmaster.ItemID AND tblproduct.PlantID = tblK1stockmaster.PlantID');
    $this->db->join('tblK1ItemCategory', 'tblK1ItemCategory.id = tblproduct.Category');
    $this->db->join('tblK1ItemSubCategory', 'tblK1ItemSubCategory.id = tblproduct.Subcategory');
    $this->db->where_in(db_prefix() . 'K1stockmaster.FY', $fy);
    $this->db->where(db_prefix() . 'K1stockmaster.PlantID', $selected_company);
    $this->db->group_by('tblK1stockmaster.ItemID,tblK1stockmaster.FY');
    $ItemWiseInventory =  $this->db->get(db_prefix() . 'K1stockmaster')->result_array();
    $itemWise = [];
    foreach ($ItemWiseInventory as $val) {
      $itemKey = $val['ProductID'];
      if (!isset($itemWise[$itemKey])) {
        $itemWise[$itemKey] = [
          'ItemID'      => $val['ProductID'],
          'description'   => $val['ProductName'],
          'MainGrpName'   => $val['MainGrpName'],
          'MainGrpID'   => $val['MainGrpID'],
          'Grp1Name'      => $val['Grp1Name'],
          'SubGrpID1'      => $val['SubGrpID1'],
          'OpeningQty'     => 0,
          'OpeningAmt'     => 0,
          'PurchaseAmt'       => 0,
          'PurchaseRtnAmt'    => 0,
          'IssueAmt'          => 0,
          'ProductionAmt'     => 0,
          'SaleAmt'           => 0,
          'FreshRtnAmt'       => 0,
          'DamageRtnAmt'      => 0,
          'FreeDistAmt'       => 0,
          'InwardAmt'         => 0,
          'ClosingAmt'        => 0,
          'PurchaseQty'       => 0,
          'PurchaseRtnQty'    => 0,
          'IssueQty'          => 0,
          'ProductionQty'     => 0,
          'SaleQty'           => 0,
          'FreshRtnQty'       => 0,
          'DamageRtnQty'      => 0,
          'FreeDistQty'       => 0,
          'InwardQty'         => 0,
          'ClosingQty'        => 0,
          'AvgRate'        => 0,
          'ClosingValue'        => 0,
        ];
      }
      $itemWise[$itemKey]['OpeningQty'] += $val['OQty'];
      $itemWise[$itemKey]['OpeningAmt'] += $val['TotalValue'];
    }

    $inventory = [];
    $TotalOpnAmt = 0;
    $TotalOpnQty = 0;
    // Get Transaction Before From Date
    $first_date_fy = '20' . $fy . '-04-01 00:00:00';
    $response = new stdClass();
    if ($first_date_fy != $fromdate && $first_date_fy < $fromdate) {
      $this->db->select('SUM(tblK1history.BilledQty * tblK1history.BasicRate) AS TotalAmt,SUM(tblK1history.DiscAmt) AS DiscAmt,
        SUM(tblK1history.BilledQty) AS TotalBilledQty, tblK1history.FY,TType,TType2,
        tblproduct.ProductID,tblproduct.ProductName,
    		tblproduct.Category AS MainGrpID,tblK1ItemCategory.SubcategoryName AS MainGrpName,
    		tblproduct.Subcategory AS SubGrpID1,tblK1ItemSubCategory.SubCategoryName AS Grp1Name');
      $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
      $this->db->join('tblK1ItemCategory', 'tblK1ItemCategory.id = tblproduct.Category');
      $this->db->join('tblK1ItemSubCategory', 'tblK1ItemSubCategory.id = tblproduct.Subcategory');
      $this->db->where_in(db_prefix() . 'K1history.FY', $fy);
      $this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
      $this->db->where(db_prefix() . 'K1history.BillID IS NOT NULL');
      //$this->db->where(db_prefix() . 'K1history.TransID IS NOT NULL');
      $this->db->where(db_prefix() . 'K1history.TransDate2 BETWEEN "' . $first_date_fy . '" AND "' . $fromdate . '"');
      $this->db->group_by('tblK1history.TType,tblK1history.TType2,tblK1history.ItemID');
      $TransactionList =  $this->db->get(db_prefix() . 'K1history')->result_array();

      //$inventory = [];
      foreach ($TransactionList as $val) {
        $itemKey = $val['ProductID'];
        if (!isset($itemWise[$itemKey])) {
          $itemWise[$itemKey] = [
            'ItemID'      => $val['ProductID'],
            'description'   => $val['ProductName'],
            'MainGrpName'   => $val['MainGrpName'],
            'MainGrpID'   => $val['MainGrpID'],
            'Grp1Name'      => $val['Grp1Name'],
            'SubGrpID1'      => $val['SubGrpID1'],
            'OpeningQty'     => 0,
            'OpeningAmt'     => 0,
            'PurchaseAmt'       => 0,
            'PurchaseRtnAmt'    => 0,
            'IssueAmt'          => 0,
            'ProductionAmt'     => 0,
            'SaleAmt'           => 0,
            'FreshRtnAmt'       => 0,
            'DamageRtnAmt'      => 0,
            'FreeDistAmt'       => 0,
            'InwardAmt'         => 0,
            'ClosingAmt'        => 0,
            'PurchaseQty'       => 0,
            'PurchaseRtnQty'    => 0,
            'IssueQty'          => 0,
            'ProductionQty'     => 0,
            'SaleQty'           => 0,
            'FreshRtnQty'       => 0,
            'DamageRtnQty'      => 0,
            'FreeDistQty'       => 0,
            'InwardQty'         => 0,
            'ClosingQty'        => 0,
            'AvgRate'        => 0,
            'ClosingValue'        => 0,
          ];
        }
        if ($val['TType'] == 'P' && $val['TType2'] == 'Purchase') {
          $itemWise[$itemKey]['PurchaseAmt'] += $val['TotalAmt'];
          $itemWise[$itemKey]['PurchaseQty'] += $val['TotalBilledQty'];
        } elseif ($val['TType'] == 'P' && $val['TType2'] == 'PURCHASE RETURN') {
          $itemWise[$itemKey]['PurchaseRtnAmt'] += $val['TotalAmt'];
          $itemWise[$itemKey]['PurchaseRtnQty'] += $val['TotalBilledQty'];
        } elseif ($val['TType'] == 'A' && $val['TType2'] == 'Issue') {
          $itemWise[$itemKey]['IssueAmt'] += $val['TotalAmt'];
          $itemWise[$itemKey]['IssueQty'] += $val['TotalBilledQty'];
        } elseif ($val['TType'] == 'B' && $val['TType2'] == 'Production') {
          $itemWise[$itemKey]['ProductionAmt'] += $val['TotalAmt'];
          $itemWise[$itemKey]['ProductionQty'] += $val['TotalBilledQty'];
        } elseif ($val['TType'] == 'O' && $val['TType2'] == 'SALE') {
          $itemWise[$itemKey]['SaleAmt'] += $val['TotalAmt'];
          $itemWise[$itemKey]['SaleQty'] += $val['TotalBilledQty'];
        } elseif ($val['TType'] == 'SR' && $val['TType2'] == 'FRESH RETURN') {
          $itemWise[$itemKey]['FreshRtnAmt'] += $val['TotalAmt'];
          $itemWise[$itemKey]['FreshRtnQty'] += $val['TotalBilledQty'];
        } elseif ($val['TType'] == 'SR' && $val['TType2'] == 'DAMAGE RETURN') {
          $itemWise[$itemKey]['DamageRtnAmt'] += $val['TotalAmt'];
          $itemWise[$itemKey]['DamageRtnQty'] += $val['TotalBilledQty'];
        } elseif ($val['TType'] == 'I' && $val['TType2'] == 'INWARD') {
          $itemWise[$itemKey]['InwardAmt'] += $val['TotalAmt'];
          $itemWise[$itemKey]['InwardQty'] += $val['TotalBilledQty'];
        } elseif ($val['TType'] == 'X') {
          $itemWise[$itemKey]['FreeDistAmt'] += $val['TotalAmt'];
          $itemWise[$itemKey]['FreeDistQty'] += $val['TotalBilledQty'];
        }
      }
    }
    /* Calculate Closing Amount */
    foreach ($itemWise as $itemCode => $row) {
      // Include opening stock if available
      $totalQty = $row['OpeningQty']
        + $row['PurchaseQty']
        + $row['ProductionQty'];

      $totalAmt = $row['OpeningAmt']
        + $row['PurchaseAmt']
        + $row['ProductionAmt'];
      $AvgRate = ($totalQty > 0  || $totalQty < 0) ? round($totalAmt / $totalQty, 4) : 0;
      $itemWise[$itemCode]['AvgRate'] = $AvgRate;
      $ClosingQty = $row['OpeningQty'] + $row['PurchaseQty'] - $row['PurchaseRtnQty'] - $row['IssueQty'] + $row['ProductionQty'] - $row['SaleQty'] + $row['FreshRtnQty']
        //- $row['DamageRtnQty'] 
        - $row['FreeDistQty'] + $row['InwardQty'];
      $ClosingValue = $AvgRate * $ClosingQty;
      $itemWise[$itemCode]['ClosingValue'] = $ClosingValue;
      $itemWise[$itemCode]['ClosingQty'] =
        $row['PurchaseQty']
        - $row['PurchaseRtnQty']
        - $row['IssueQty']
        + $row['ProductionQty']
        - $row['SaleQty']
        + $row['FreshRtnQty']
        //- $row['DamageRtnQty']
        - $row['FreeDistQty']
        + $row['InwardQty'];
    }
    /*$itemWise = array_filter($itemWise, function($row) {
            return ($row['ClosingAmt'] || $row['ClosingQty']) > 0;
        });*/
    $itemWise = array_values($itemWise);
    foreach ($itemWise as $row) {
      $mainGrpID = $row['MainGrpID'];
      $subGrp1ID = $row['SubGrpID1'];
      $ClosingAmt       = (float)$row['ClosingValue'];
      $TotalOpnAmt += $ClosingAmt;
      $ClosingQty       = (float)$row['ClosingQty'];
      $TotalOpnQty += $ClosingQty;
      //Main Group
      if (!isset($inventory[$mainGrpID])) {
        $inventory[$mainGrpID] = [
          'MainGroupID'       => $mainGrpID,
          'MainGroupName'     => $row['MainGrpName'],
          'MainGroupTotalQty' => 0,
          'MainGroupTotalAmt' => 0,
          'SubGroup1'         => []
        ];
      }
      $inventory[$mainGrpID]['MainGroupTotalAmt'] += $ClosingAmt;
      $inventory[$mainGrpID]['MainGroupTotalQty'] += $ClosingQty;
      // Sub Group 1
      if (!isset($inventory[$mainGrpID]['SubGroup1'][$subGrp1ID])) {
        $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID] = [
          'SubGroup1ID'       => $subGrp1ID,
          'SubGroup1Name'     => $row['Grp1Name'],
          'SubGroup1TotalQty' => 0,
          'SubGroup1TotalAmt' => 0,
        ];
      }
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['SubGroup1TotalAmt'] += $ClosingAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['SubGroup1TotalQty'] += $ClosingQty;

      // Item Details
      $ItemID = $row['ItemID'];
      if (!isset($inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID])) {
        $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID] = [
          'ItemID'       => $ItemID,
          'ItemName'     => $row['description'],
          'ItemTotalQty' => 0,
          'ItemTotalAmt' => 0
        ];
      }
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID]['ItemTotalQty'] += $ClosingQty;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID]['ItemTotalAmt'] += $ClosingAmt;
    }
    foreach ($inventory as &$mainGrp) {
      foreach ($mainGrp['SubGroup1'] as &$subGrp1) {
        $subGrp1['ItemDetails'] = array_values($subGrp1['ItemDetails']);
      }
    }
    unset($mainGrp, $subGrp1, $subGrp2);
    /* Re-index arrays to numeric keys */
    foreach ($inventory as &$mainGroup) {
      $mainGroup['SubGroup1'] = array_values($mainGroup['SubGroup1']);
    }
    $inventory = array_values($inventory);
    $response->inventory = $inventory;
    $response->TotalinventoryAmt = $TotalOpnAmt;
    //$RMRateData = $this->GetRMLastPurchaseRate($ItemList,$todate);
    //$FGRateData = $this->GetFGRateItemWise($ItemList,$todate);
    return $response;
  }

  //==================== Calculate Closing Inventory Amt =========================
  public function GetClosingInventoryAmt($fromdate = "", $todate = "")
  {
    $fy = $this->session->userdata('finacial_year');
    $selected_company = $this->session->userdata('root_company');
    $this->db->select('SUM(tblK1stockmaster.OQty) AS OQty,SUM(tblK1stockmaster.PurchRate) AS TotalValue,tblK1stockmaster.ItemID,tblK1stockmaster.FY,tblproduct.ProductID,tblproduct.ProductName,
		tblproduct.Category AS MainGrpID,tblK1ItemCategory.SubcategoryName AS MainGrpName,
		tblproduct.Subcategory AS SubGrpID1,tblK1ItemSubCategory.SubCategoryName AS Grp1Name');
    $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1stockmaster.ItemID AND tblproduct.PlantID = tblK1stockmaster.PlantID');
    $this->db->join('tblK1ItemCategory', 'tblK1ItemCategory.id = tblproduct.Category');
    $this->db->join('tblK1ItemSubCategory', 'tblK1ItemSubCategory.id = tblproduct.Subcategory');
    $this->db->where_in(db_prefix() . 'K1stockmaster.FY', $fy);
    $this->db->where(db_prefix() . 'K1stockmaster.PlantID', $selected_company);
    $this->db->group_by('tblK1stockmaster.ItemID,tblK1stockmaster.FY');
    $ItemWiseInventory =  $this->db->get(db_prefix() . 'K1stockmaster')->result_array();
    $itemWise = [];
    foreach ($ItemWiseInventory as $val) {
      $itemKey = $val['ProductID'];
      if (!isset($itemWise[$itemKey])) {
        $itemWise[$itemKey] = [
          'ItemID'      => $val['ProductID'],
          'description'   => $val['ProductName'],
          'MainGrpName'   => $val['MainGrpName'],
          'MainGrpID'   => $val['MainGrpID'],
          'Grp1Name'      => $val['Grp1Name'],
          'SubGrpID1'      => $val['SubGrpID1'],
          'OpeningQty'     => 0,
          'OpeningAmt'     => 0,
          'PurchaseAmt'       => 0,
          'PurchaseRtnAmt'    => 0,
          'IssueAmt'          => 0,
          'ProductionAmt'     => 0,
          'SaleAmt'           => 0,
          'FreshRtnAmt'       => 0,
          'DamageRtnAmt'      => 0,
          'FreeDistAmt'       => 0,
          'InwardAmt'         => 0,
          'ClosingAmt'        => 0,
          'PurchaseQty'       => 0,
          'PurchaseRtnQty'    => 0,
          'IssueQty'          => 0,
          'ProductionQty'     => 0,
          'SaleQty'           => 0,
          'FreshRtnQty'       => 0,
          'DamageRtnQty'      => 0,
          'FreeDistQty'       => 0,
          'InwardQty'         => 0,
          'ClosingQty'        => 0,
          'AvgRate'        => 0,
          'ClosingValue'        => 0,
        ];
      }
      $itemWise[$itemKey]['OpeningQty'] += $val['OQty'];
      $itemWise[$itemKey]['OpeningAmt'] += $val['TotalValue'];
    }
    $inventory = [];
    $TotalOpnAmt = 0;
    $TotalOpnQty = 0;
    // Get Transaction Before From Date
    $first_date_fy = '20' . $fy . '-04-01 00:00:00';
    $this->db->select('SUM(tblK1history.BilledQty * tblK1history.BasicRate) AS TotalAmt,SUM(tblK1history.DiscAmt) AS DiscAmt,
        SUM(tblK1history.BilledQty) AS TotalBilledQty,tblK1history.FY,TType,TType2,
        tblproduct.ProductID,tblproduct.ProductName,
		tblproduct.Category AS MainGrpID,tblK1ItemCategory.SubcategoryName AS MainGrpName,
		tblproduct.Subcategory AS SubGrpID1,tblK1ItemSubCategory.SubCategoryName AS Grp1Name');
    $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
    $this->db->join('tblK1ItemCategory', 'tblK1ItemCategory.id = tblproduct.Category');
    $this->db->join('tblK1ItemSubCategory', 'tblK1ItemSubCategory.id = tblproduct.Subcategory');
    $this->db->where_in(db_prefix() . 'K1history.FY', $fy);
    $this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
    $this->db->where(db_prefix() . 'K1history.BillID IS NOT NULL');
    //$this->db->where(db_prefix() . 'K1history.TransID IS NOT NULL');
    $this->db->where(db_prefix() . 'K1history.TransDate2 BETWEEN "' . $first_date_fy . '" AND "' . $todate . '"');
    $this->db->group_by('tblK1history.TType,tblK1history.TType2,tblK1history.ItemID');
    $TransactionList =  $this->db->get(db_prefix() . 'K1history')->result_array();
    /*echo "<pre>";
		print_r($TransactionList);
		die;*/
    foreach ($TransactionList as $val) {
      $itemKey = $val['ProductID'];
      if (!isset($itemWise[$itemKey])) {
        $itemWise[$itemKey] = [
          'ItemID'      => $val['ProductID'],
          'description'   => $val['ProductName'],
          'MainGrpName'   => $val['MainGrpName'],
          'MainGrpID'   => $val['MainGrpID'],
          'Grp1Name'      => $val['Grp1Name'],
          'SubGrpID1'      => $val['SubGrpID1'],
          'OpeningQty'     => 0,
          'OpeningAmt'     => 0,
          'PurchaseAmt'       => 0,
          'PurchaseRtnAmt'    => 0,
          'IssueAmt'          => 0,
          'ProductionAmt'     => 0,
          'SaleAmt'           => 0,
          'FreshRtnAmt'       => 0,
          'DamageRtnAmt'      => 0,
          'FreeDistAmt'       => 0,
          'InwardAmt'         => 0,
          'ClosingAmt'        => 0,
          'PurchaseQty'       => 0,
          'PurchaseRtnQty'    => 0,
          'IssueQty'          => 0,
          'ProductionQty'     => 0,
          'SaleQty'           => 0,
          'FreshRtnQty'       => 0,
          'DamageRtnQty'      => 0,
          'FreeDistQty'       => 0,
          'InwardQty'         => 0,
          'ClosingQty'        => 0,
          'AvgRate'        => 0,
          'ClosingValue'        => 0,
        ];
      }
      if ($val['TType'] == 'P' && $val['TType2'] == 'Purchase') {
        $itemWise[$itemKey]['PurchaseAmt'] += $val['TotalAmt'];
        $itemWise[$itemKey]['PurchaseQty'] += $val['TotalBilledQty'];
      } elseif ($val['TType'] == 'P' && $val['TType2'] == 'PURCHASE RETURN') {
        $itemWise[$itemKey]['PurchaseRtnAmt'] += $val['TotalAmt'];
        $itemWise[$itemKey]['PurchaseRtnQty'] += $val['TotalBilledQty'];
      } elseif ($val['TType'] == 'A' && $val['TType2'] == 'Issue') {
        $itemWise[$itemKey]['IssueAmt'] += $val['TotalAmt'];
        $itemWise[$itemKey]['IssueQty'] += $val['TotalBilledQty'];
      } elseif ($val['TType'] == 'B' && $val['TType2'] == 'Production') {
        $itemWise[$itemKey]['ProductionAmt'] += $val['TotalAmt'];
        $itemWise[$itemKey]['ProductionQty'] += $val['TotalBilledQty'];
      } elseif ($val['TType'] == 'O' && $val['TType2'] == 'SALE') {
        $itemWise[$itemKey]['SaleAmt'] += $val['TotalAmt'];
        $itemWise[$itemKey]['SaleQty'] += $val['TotalBilledQty'];
      } elseif ($val['TType'] == 'SR' && $val['TType2'] == 'FRESH RETURN') {
        $itemWise[$itemKey]['FreshRtnAmt'] += $val['TotalAmt'];
        $itemWise[$itemKey]['FreshRtnQty'] += $val['TotalBilledQty'];
      } elseif ($val['TType'] == 'SR' && $val['TType2'] == 'DAMAGE RETURN') {
        $itemWise[$itemKey]['DamageRtnAmt'] += $val['TotalAmt'];
        $itemWise[$itemKey]['DamageRtnQty'] += $val['TotalBilledQty'];
      } elseif ($val['TType'] == 'I' && $val['TType2'] == 'INWARD') {
        $itemWise[$itemKey]['InwardAmt'] += $val['TotalAmt'];
        $itemWise[$itemKey]['InwardQty'] += $val['TotalBilledQty'];
      } elseif ($val['TType'] == 'X') {
        $itemWise[$itemKey]['FreeDistAmt'] += $val['TotalAmt'];
        $itemWise[$itemKey]['FreeDistQty'] += $val['TotalBilledQty'];
      }
    }
    /* Calculate Closing Amount */
    foreach ($itemWise as $itemCode => $row) {
      // Include opening stock if available
      $totalQty = $row['OpeningQty']
        + $row['PurchaseQty']
        + $row['ProductionQty'];

      $totalAmt = $row['OpeningAmt']
        + $row['PurchaseAmt']
        + $row['ProductionAmt'];
      $AvgRate = ($totalQty > 0 || $totalQty < 0) ? round($totalAmt / $totalQty, 4) : 0;
      $itemWise[$itemCode]['AvgRate'] = $AvgRate;
      $ClosingQty = $row['OpeningQty'] + $row['PurchaseQty'] - $row['PurchaseRtnQty'] - $row['IssueQty'] + $row['ProductionQty'] - $row['SaleQty'] + $row['FreshRtnQty']
        //- $row['DamageRtnQty'] 
        - $row['FreeDistQty'] + $row['InwardQty'];
      $ClosingValue = $AvgRate * $ClosingQty;
      $itemWise[$itemCode]['ClosingValue'] = $ClosingValue;
    }
    /*$itemWise = array_filter($itemWise, function($row) {
            return $row['ClosingAmt'] > 0;
        });*/
    $itemWise = array_values($itemWise);
    foreach ($itemWise as $row) {
      $mainGrpID = $row['MainGrpID'];
      $subGrp1ID = $row['SubGrpID1'];
      $ClosingAmt       = (float)$row['ClosingValue'];
      $TotalOpnAmt += $ClosingAmt;
      //Main Group
      if (!isset($inventory[$mainGrpID])) {
        $inventory[$mainGrpID] = [
          'MainGroupID'       => $mainGrpID,
          'MainGroupName'     => $row['MainGrpName'],
          'MainGroupTotalAmt' => 0,
          'SubGroup1'         => []
        ];
      }
      $inventory[$mainGrpID]['MainGroupTotalAmt'] += $ClosingAmt;
      // Sub Group 1
      if (!isset($inventory[$mainGrpID]['SubGroup1'][$subGrp1ID])) {
        $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID] = [
          'SubGroup1ID'       => $subGrp1ID,
          'SubGroup1Name'     => $row['Grp1Name'],
          'SubGroup1TotalAmt' => 0,
        ];
      }
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['SubGroup1TotalAmt'] += $ClosingAmt;

      // Item Details
      $ItemID = $row['ItemID'];
      if (!isset($inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID])) {
        $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID] = [
          'ItemID'       => $ItemID,
          'ItemName'     => $row['description'],
          'ItemTotalAmt' => 0
        ];
      }
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID]['ItemTotalAmt']
        += $ClosingAmt;
    }
    foreach ($inventory as &$mainGrp) {
      foreach ($mainGrp['SubGroup1'] as &$subGrp1) {
        $subGrp1['ItemDetails'] = array_values($subGrp1['ItemDetails']);
      }
    }
    unset($mainGrp, $subGrp1, $subGrp2);
    /* Re-index arrays to numeric keys */
    foreach ($inventory as &$mainGroup) {
      $mainGroup['SubGroup1'] = array_values($mainGroup['SubGroup1']);
    }
    $inventory = array_values($inventory);
    $response = new stdClass();
    $response->inventory = $inventory;
    $response->TotalinventoryAmt = $TotalOpnAmt;
    //$RMRateData = $this->GetRMLastPurchaseRate($ItemList,$todate);
    //$FGRateData = $this->GetFGRateItemWise($ItemList,$todate);
    return $response;
  }

  //========================== Transaction Amount ================================
  public function GetTransactionAmt($fromdate = "", $todate = "")
  {
    $fy = $this->session->userdata('finacial_year');
    $selected_company = $this->session->userdata('root_company');
    // Get Transaction between From Date and to Date
    $first_date_fy = '20' . $fy . '-04-01 00:00:00';
    $this->db->select('(tblK1history.BilledQty * tblK1history.BasicRate) AS TotalAmt, (tblK1history.DiscAmt) AS DiscAmt, tblK1history.FY, tblK1history.TType, tblK1history.TType2, tblproduct.ProductID, tblproduct.ProductName, tblproduct.Category AS MainGrpID, tblK1ItemCategory.SubcategoryName AS MainGrpName, tblproduct.Subcategory AS SubGrpID1, tblK1ItemSubCategory.SubCategoryName AS Grp1Name, tblK1history.cgst, tblK1history.cgstamt, tblK1history.sgst, tblK1history.sgstamt, tblK1history.igst, tblK1history.igstamt, tbltaxes.taxrate');
    $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
    $this->db->join('tbltaxes', 'tbltaxes.id = tblproduct.gst');
    $this->db->join('tblK1ItemCategory', 'tblK1ItemCategory.id = tblproduct.Category');
    $this->db->join('tblK1ItemSubCategory', 'tblK1ItemSubCategory.id = tblproduct.Subcategory');
    $this->db->where(db_prefix() . 'K1history.FY', $fy);
    $this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
    $this->db->where(db_prefix() . 'K1history.PartyID', 'KASPL');
    $this->db->where(db_prefix() . 'K1history.BillID IS NOT NULL');
    $this->db->where(db_prefix() . 'K1history.TransID IS NOT NULL');
    // $this->db->where(db_prefix() . 'K1history.ItemID',"215");
    // $this->db->where(db_prefix() . 'K1history.TType',"O");
    // $this->db->where(db_prefix() . 'K1history.TType2',"SALE");
    $this->db->where(db_prefix() . 'K1history.TransDate BETWEEN "' . $fromdate . '" AND "' . $todate . '"');
    //$this->db->group_by('tblK1history.TType,tblK1history.TType2,tblK1history.ItemID');
    $TransactionList =  $this->db->get(db_prefix() . 'K1history')->result_array();
    //echo "<pre>";
    //print_r($TransactionList);
    //die;
    $itemWise = [];
    foreach ($TransactionList as $val) {
      $itemKey = $val['ProductID'];
      if (!isset($itemWise[$itemKey])) {
        $itemWise[$itemKey] = [
          'ItemID'      => $val['ProductID'],
          'description'   => $val['ProductName'],
          'MainGrpName'   => $val['MainGrpName'],
          'MainGrpID'   => $val['MainGrpID'],
          'Grp1Name'      => $val['Grp1Name'],
          'SubGrpID1'      => $val['SubGrpID1'],
          'PurchaseAmt'       => 0,
          'PurchaseRtnAmt'    => 0,
          'IssueAmt'          => 0,
          'ProductionAmt'     => 0,
          'SaleAmt'           => 0,
          'FreshRtnAmt'       => 0,
          'DamageRtnAmt'      => 0,
          'FreeDistAmt'       => 0,
          'InwardAmt'         => 0,
          'ClosingAmt'        => 0,
          'PurchCNAmt'        => 0,
          'PurchDNAmt'        => 0,
          'SaleCNAmt'        => 0,
          'SaleDNAmt'        => 0,
        ];
      }

      if ($val['TType'] == 'P' && $val['TType2'] == 'Purchase') {
        $itemWise[$itemKey]['PurchaseAmt'] += ($val['TotalAmt'] - $val['DiscAmt']);
      } elseif ($val['TType'] == 'P' && $val['TType2'] == 'PURCHASE RETURN') {
        $itemWise[$itemKey]['PurchaseRtnAmt'] += ($val['TotalAmt'] - $val['DiscAmt']);
      } elseif ($val['TType'] == 'A' && $val['TType2'] == 'Issue') {
        $itemWise[$itemKey]['IssueAmt'] += ($val['TotalAmt'] - $val['DiscAmt']);
      } elseif ($val['TType'] == 'B' && $val['TType2'] == 'Production') {
        $itemWise[$itemKey]['ProductionAmt'] += ($val['TotalAmt'] - $val['DiscAmt']);
      } elseif ($val['TType'] == 'O' && $val['TType2'] == 'SALE') {
        $OrderAmt= ($val['TotalAmt'] - $val['DiscAmt']);
        $ExGSTAmt = $val['cgstamt'] + $val['sgstamt'] + $val['igstamt'];
        if ($ExGSTAmt > 0) {
          $itemWise[$itemKey]['SaleAmt'] += $OrderAmt;
        } else {
          $GSTPer = $val['taxrate'];
          $taxableAmt= $OrderAmt / (1 + ($GSTPer / 100));
          $itemWise[$itemKey]['SaleAmt'] += $taxableAmt;
        }
      } elseif ($val['TType'] == 'SR' && $val['TType2'] == 'FRESH RETURN') {
        $itemWise[$itemKey]['FreshRtnAmt'] += ($val['TotalAmt'] - $val['DiscAmt']);
      } elseif ($val['TType'] == 'SR' && $val['TType2'] == 'DAMAGE RETURN') {
        $itemWise[$itemKey]['DamageRtnAmt'] += ($val['TotalAmt'] - $val['DiscAmt']);
      } elseif ($val['TType'] == 'I' && $val['TType2'] == 'INWARD') {
        $itemWise[$itemKey]['InwardAmt'] += ($val['TotalAmt'] - $val['DiscAmt']);
      } elseif ($val['TType'] == 'X') {
        $itemWise[$itemKey]['FreeDistAmt'] += ($val['TotalAmt'] - $val['DiscAmt']);
      }
    }
    // Credit/Debit Note Details
    $sql = 'SELECT tblcdnotehistory.billno,tblcdnotehistory.ttype, (tblcdnotehistory.rate) AS TotalTaxableAmt,tblpurchasemaster.PurchID,tblsalesmaster.SalesID,
		tblproduct.ProductID,tblproduct.ProductName,
		tblproduct.Category AS MainGrpID,tblK1ItemCategory.SubcategoryName AS MainGrpName,
		tblproduct.Subcategory AS SubGrpID1,tblK1ItemSubCategory.SubCategoryName AS Grp1Name
        FROM `tblcdnotehistory`
        LEFT JOIN tblpurchasemaster ON tblpurchasemaster.PurchID =  tblcdnotehistory.TransID
        LEFT JOIN tblsalesmaster ON 	tblsalesmaster.SalesID =  tblcdnotehistory.TransID
        INNER JOIN tblproduct ON tblproduct.ProductID = tblcdnotehistory.itemid AND tblproduct.PlantID = tblcdnotehistory.plantid
        INNER JOIN tblK1ItemCategory ON tblK1ItemCategory.id = tblproduct.Category
        INNER JOIN tblK1ItemSubCategory ON tblK1ItemSubCategory.id = tblproduct.Subcategory
        WHERE tblcdnotehistory.plantid = ' . $selected_company . ' AND tblcdnotehistory.fy = "' . $fy . '"
        AND tblcdnotehistory.transdate BETWEEN "' . $fromdate . '" AND "' . $todate . '" GROUP BY tblcdnotehistory.ttype,tblcdnotehistory.billno';
    $CDResult = $this->db->query($sql)->result_array();

    foreach ($CDResult as $val) {
      $itemKey = $val['ProductID'];
      if (!isset($itemWise[$itemKey])) {
        $itemWise[$itemKey] = [
          'ItemID'      => $val['ProductID'],
          'description'   => $val['ProductName'],
          'MainGrpName'   => $val['MainGrpName'],
          'MainGrpID'   => $val['MainGrpID'],
          'Grp1Name'      => $val['Grp1Name'],
          'SubGrpID1'      => $val['SubGrpID1'],
          'PurchaseAmt'       => 0,
          'PurchaseRtnAmt'    => 0,
          'IssueAmt'          => 0,
          'ProductionAmt'     => 0,
          'SaleAmt'           => 0,
          'FreshRtnAmt'       => 0,
          'DamageRtnAmt'      => 0,
          'FreeDistAmt'       => 0,
          'InwardAmt'         => 0,
          'ClosingAmt'        => 0,
          'PurchCNAmt'        => 0,
          'PurchDNAmt'        => 0,
          'SaleCNAmt'        => 0,
          'SaleDNAmt'        => 0,
        ];
      }
      if ($val['ttype'] == 'C' && $val['PurchID'] == NULL) {
        $itemWise[$itemKey]['SaleCNAmt'] += $val['TotalTaxableAmt'];
      } elseif ($val['ttype'] == 'D' && $val['PurchID'] == 'NULL') {
        $itemWise[$itemKey]['SaleDNAmt'] += $val['TotalTaxableAmt'];
      } elseif ($val['ttype'] == 'C' && $val['SalesID'] == NULL) {
        $itemWise[$itemKey]['PurchCNAmt'] += $val['TotalTaxableAmt'];
      } elseif ($val['ttype'] == 'D' && $val['SalesID'] == NULL) {
        $itemWise[$itemKey]['PurchDNAmt'] += ($val['TotalTaxableAmt']);
      }
    }
    $itemWise = array_values($itemWise);
    $inventory = [];
    $TotalOpnAmt = 0;
    $TotalPurchaseAmt = 0;
    $TotalPurchaseRtnAmt = 0;
    $TotalSaleAmt = 0;
    $TotalFreshRtnAmt = 0;
    $TotalDamageRtnAmt = 0;
    $TotalSaleCNAmt = $TotalSaleDNAmt = $TotalPurchCNAmt = $TotalPurchDNAmt = 0;
    foreach ($itemWise as $row) {
      $mainGrpID = $row['MainGrpID'];
      $subGrp1ID = $row['SubGrpID1'];
      $PurchaseAmt = (float)$row['PurchaseAmt'];
      $TotalPurchaseAmt += $PurchaseAmt;
      $PurchaseRtnAmt = (float)$row['PurchaseRtnAmt'];
      $TotalPurchaseRtnAmt += $PurchaseRtnAmt;
      $SaleAmt = (float)$row['SaleAmt'];
      $TotalSaleAmt += $SaleAmt;
      $FreshRtnAmt = (float)$row['FreshRtnAmt'];
      $TotalFreshRtnAmt += $FreshRtnAmt;
      $DamageRtnAmt = (float)$row['DamageRtnAmt'];
      $TotalDamageRtnAmt += $DamageRtnAmt;
      $ClosingAmt = (float)$row['ClosingAmt'];
      $TotalOpnAmt += $ClosingAmt;
      $SaleCNAmt = (float)$row['SaleCNAmt'];
      $TotalSaleCNAmt += $SaleCNAmt;
      $SaleDNAmt = (float)$row['SaleDNAmt'];
      $TotalSaleDNAmt += $SaleDNAmt;
      $PurchCNAmt = (float)$row['PurchCNAmt'];
      $TotalPurchCNAmt += $PurchCNAmt;
      $PurchDNAmt = (float)$row['PurchDNAmt'];
      $TotalPurchDNAmt += $PurchDNAmt;
      //Main Group
      if (!isset($inventory[$mainGrpID])) {
        $inventory[$mainGrpID] = [
          'MainGroupID'       => $mainGrpID,
          'MainGroupName'     => $row['MainGrpName'],
          'MainGroupPurchaseAmt' => 0,
          'MainGroupPurchaseRtnAmt' => 0,
          'MainGroupSaleAmt' => 0,
          'MainGroupFreshRtnAmt' => 0,
          'MainGroupDamageRtnAmt' => 0,
          'MainGroupTotalQty' => 0,
          'MainGroupSaleCNAmt' => 0,
          'MainGroupSaleDNAmt' => 0,
          'MainGroupPurchCNAmt' => 0,
          'MainGroupPurchDNAmt' => 0,
          'SubGroup1'         => []
        ];
      }
      $inventory[$mainGrpID]['MainGroupPurchaseAmt'] += $PurchaseAmt;
      $inventory[$mainGrpID]['MainGroupPurchaseRtnAmt'] += $PurchaseRtnAmt;
      $inventory[$mainGrpID]['MainGroupSaleAmt'] += $SaleAmt;
      $inventory[$mainGrpID]['MainGroupFreshRtnAmt'] += $FreshRtnAmt;
      $inventory[$mainGrpID]['MainGroupDamageRtnAmt'] += $DamageRtnAmt;
      $inventory[$mainGrpID]['MainGroupTotalQty'] += $ClosingAmt;
      $inventory[$mainGrpID]['MainGroupSaleCNAmt'] += $SaleCNAmt;
      $inventory[$mainGrpID]['MainGroupSaleDNAmt'] += $SaleDNAmt;
      $inventory[$mainGrpID]['MainGroupPurchCNAmt'] += $PurchCNAmt;
      $inventory[$mainGrpID]['MainGroupPurchDNAmt'] += $PurchDNAmt;
      // Sub Group 1
      if (!isset($inventory[$mainGrpID]['SubGroup1'][$subGrp1ID])) {
        $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID] = [
          'SubGroup1ID'       => $subGrp1ID,
          'SubGroup1Name'     => $row['Grp1Name'],
          'SubGroup1PurchaseAmt' => 0,
          'SubGroup1PurchaseRtnAmt' => 0,
          'SubGroup1SaleAmt' => 0,
          'SubGroup1FreshRtnAmt' => 0,
          'SubGroup1DamageRtnAmt' => 0,
          'SubGroup1TotalQty' => 0,
          'SubGroup1SaleCNAmt' => 0,
          'SubGroup1SaleDNAmt' => 0,
          'SubGroup1PurchCNAmt' => 0,
          'SubGroup1PurchDNAmt' => 0,
        ];
      }
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['SubGroup1PurchaseAmt'] += $PurchaseAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['SubGroup1PurchaseRtnAmt'] += $PurchaseRtnAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['SubGroup1SaleAmt'] += $SaleAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['SubGroup1FreshRtnAmt'] += $FreshRtnAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['SubGroup1DamageRtnAmt'] += $DamageRtnAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['SubGroup1TotalQty'] += $ClosingAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['SubGroup1SaleCNAmt'] += $SaleCNAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['SubGroup1SaleDNAmt'] += $SaleDNAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['SubGroup1PurchCNAmt'] += $PurchCNAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['SubGroup1PurchDNAmt'] += $PurchDNAmt;
      // Item Details
      $ItemID = $row['ItemID'];
      if (!isset($inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID])) {
        $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID] = [
          'ItemID'       => $ItemID,
          'ItemName'     => $row['description'],
          'PurchaseAmt'     => 0,
          'PurchaseRtnAmt'     => 0,
          'SaleAmt'     => 0,
          'FreshRtnAmt'     => 0,
          'DamageRtnAmt'     => 0,
          'ItemTotalQty' => 0,
          'SaleCNAmt'     => 0,
          'SaleDNAmt'     => 0,
          'PurchCNAmt'     => 0,
          'PurchDNAmt'     => 0,
        ];
      }
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID]['PurchaseAmt'] += $PurchaseAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID]['PurchaseRtnAmt'] += $PurchaseRtnAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID]['SaleAmt'] += $SaleAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID]['FreshRtnAmt'] += $FreshRtnAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID]['DamageRtnAmt'] += $DamageRtnAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID]['ItemTotalQty'] += $ClosingAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID]['SaleCNAmt'] += $SaleCNAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID]['SaleDNAmt'] += $SaleDNAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID]['PurchCNAmt'] += $PurchCNAmt;
      $inventory[$mainGrpID]['SubGroup1'][$subGrp1ID]['ItemDetails'][$ItemID]['PurchDNAmt'] += $PurchDNAmt;
    }
    foreach ($inventory as &$mainGrp) {
      foreach ($mainGrp['SubGroup1'] as &$subGrp1) {
        $subGrp1['ItemDetails'] = array_values($subGrp1['ItemDetails']);
      }
    }
    unset($mainGrp, $subGrp1, $subGrp2);
    /* Re-index arrays to numeric keys */
    foreach ($inventory as &$mainGroup) {
      $mainGroup['SubGroup1'] = array_values($mainGroup['SubGroup1']);
    }
    $inventory = array_values($inventory);
    $response = new stdClass();
    $response->inventory = $inventory;
    $response->TotalinventoryAmt = $TotalOpnAmt;
    $response->TotalPurchaseAmt = $TotalPurchaseAmt;
    $response->TotalPurchaseRtnAmt = $TotalPurchaseRtnAmt;
    $response->TotalSaleAmt = $TotalSaleAmt;
    $response->TotalFreshRtnAmt = $TotalFreshRtnAmt;
    $response->TotalDamageRtnAmt = $TotalDamageRtnAmt;
    $response->TotalSaleCNAmt = $TotalSaleCNAmt;
    $response->TotalSaleDNAmt = $TotalSaleDNAmt;
    $response->TotalPurchCNAmt = $TotalPurchCNAmt;
    $response->TotalPurchDNAmt = $TotalPurchDNAmt;
    //$RMRateData = $this->GetRMLastPurchaseRate($ItemList,$todate);
    //$FGRateData = $this->GetFGRateItemWise($ItemList,$todate);
    return $response;
  }

  //======================== Direct Expense ======================================
  public function GetDirectExpenses($fromdate = "", $todate = "")
  {
    $fy = $this->session->userdata('finacial_year');
    $selected_company = $this->session->userdata('root_company');
    $last_fy = $fy - 1;
    $year = array($fy, $last_fy);
    if (empty($fromdate)) {
      $from_date = '20' . $fy . '-04-01 00:00:00';
    } else {
      $from_date = $fromdate;
    }
    //$from_date = '20'.$fy.'-04-01 00:00:00';
    if (empty($todate)) {
      $to_date = date('Y-m-d H:i:s');
    } else {
      $to_date = $todate;
    }
    $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY,clients.company,
		tblclients.AccountID,tblclients.SubActGroupID1,tblaccountgroupssub1.SubActGroupName As ActGrp1,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName');
    $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
    $this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblclients.SubActGroupID1');
    $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
    $this->db->where_in(db_prefix() . 'accountledger.FY', $year);
    $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
    $this->db->where(db_prefix() . 'accountledger.PartyID', 'KASPL');
    $this->db->where(db_prefix() . 'clients.ActGroupID', '10010');
    $this->db->where_not_in(db_prefix() . 'clients.AccountID', 'DISC');
    $this->db->where(db_prefix() . 'accountledger.Transdate BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
    $this->db->group_by('tblaccountledger.TType,tblaccountledger.FY,tblaccountledger.AccountID');
    $DirectExpData =  $this->db->get(db_prefix() . 'accountledger')->result_array();
    $ActGroup1List = array();
    $ActGroup2List = array();
    $AccountList = array();
    foreach ($DirectExpData as $DEKey => $DeVal) {
      $new1 = array(
        "AccountID" => $DeVal["SubActGroupID1"],
        "AccountName" => $DeVal["ActGrp1"]
      );
      array_push($ActGroup1List, $new1);
      $new2 = array(
        "AccountID1" => $DeVal["SubActGroupID1"],
        "AccountID" => $DeVal["SubActGroupID"],
        "AccountName" => $DeVal["SubActGroupName"]
      );
      array_push($ActGroup2List, $new2);
      $new = array(
        "AccountID2" => $DeVal["SubActGroupID"],
        "AccountID" => $DeVal["AccountID"],
        "AccountName" => $DeVal["company"]
      );
      array_push($AccountList, $new);
    }
    $ActGroup1UniqueList = array_unique($ActGroup1List, SORT_REGULAR);
    $ActGroup2UniqueList = array_unique($ActGroup2List, SORT_REGULAR);
    $AccountUniqueList = array_unique($AccountList, SORT_REGULAR);
    $i = 0;
    $nestedData = [];
    $TotalExpense = 0;
    $TotalExpensePre = 0;
    foreach ($ActGroup1UniqueList as $ActGrp1) {
      $Group1Data = [
        'Group1Name' => $ActGrp1['AccountName'],
        'Group1ID' => $ActGrp1['AccountID'],
      ];
      $ClsBalGroup1 = 0;
      $ClsBalGroup1Pre = 0;
      foreach ($ActGroup2UniqueList as $val2) {
        $ClsBalGroup2 = 0;
        $ClsBalGroup2Pre = 0;
        if ($ActGrp1["AccountID"] == $val2["AccountID1"]) {
          $Group2Data = [
            'SubGroupName' => $val2['AccountName'],
            'SubActGroupID' => $val2['AccountID'],
          ];
          foreach ($AccountUniqueList as $ActList) {
            if ($ActList["AccountID2"] == $val2['AccountID']) {
              $ClsBalAccountWise = 0;
              $ClsBalAccountWisePre = 0;
              $Act_opn = 0;
              $ActCr = 0;
              $ActDr = 0;
              $Act_opnPre = 0;
              $ActCrPre = 0;
              $ActDrPre = 0;
              foreach ($DirectExpData as $key => $val) {
                if ($val["TType"] == "C" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]) {
                  $ActCr += $val["SumAmt"];
                } else if ($val["TType"] == "D" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]) {
                  $ActDr += $val["SumAmt"];
                }
                if ($val["TType"] == "C" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]) {
                  $ActCrPre += $val["SumAmt"];
                } else if ($val["TType"] == "D" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]) {
                  $ActDrPre += $val["SumAmt"];
                }
              }
              $ClsBalAccountWise =  $ActDr - $ActCr;
              $ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
              $ClsBalGroup2 += $ClsBalAccountWise;
              $ClsBalGroup2Pre += $ClsBalAccountWisePre;
              $AccountData = [
                'AccountName' => $ActList['AccountName'],
                'AccountID' => $ActList['AccountID'],
                'AccountClsBal' => $ClsBalAccountWise,
                'AccountClsBalPre' => $ClsBalAccountWisePre,
              ];
              $Group2Data['Accounts'][] = $AccountData;
            }
          }
          $Group2Data['Group2ClsBal'] = abs($ClsBalGroup2);
          $Group2Data['Group2ClsBalPre'] = abs($ClsBalGroup2Pre);
          $ClsBalGroup1 += $ClsBalGroup2;
          $ClsBalGroup1Pre += $ClsBalGroup2Pre;
          $Group1Data['SubGroups2'][] = $Group2Data;
        }
      }
      $TotalExpense += abs($ClsBalGroup1);
      $TotalExpensePre += abs($ClsBalGroup1Pre);
      $Group1Data['Group1ClsBal'] = abs($ClsBalGroup1);
      $Group1Data['Group1ClsBalPre'] = abs($ClsBalGroup1Pre);
      $nestedData[] = $Group1Data;
      $i++;
    }
    $DirectExpense = new stdClass();
    $DirectExpense->nestedData = $nestedData;
    $DirectExpense->CurrentYear = $TotalExpense;
    $DirectExpense->PriviousYear = $TotalExpensePre;
    return $DirectExpense;
  }

  //============== Employee Benefits =============================================
  public function GetEMPBen($fromdate = "", $todate = "")
  {
    $fy = $this->session->userdata('finacial_year');
    $selected_company = $this->session->userdata('root_company');
    $last_fy = $fy - 1;
    $year = array($fy, $last_fy);
    if (empty($fromdate)) {
      $from_date = '20' . $fy . '-04-01 00:00:00';
    } else {
      $from_date = $fromdate;
    }
    //$from_date = '20'.$fy.'-04-01 00:00:00';
    if (empty($todate)) {
      $to_date = date('Y-m-d H:i:s');
    } else {
      $to_date = $todate;
    }
    $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY,tblclients.company,
		tblclients.AccountID,tblclients.SubActGroupID1,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName');
    $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
    $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
    $this->db->where_in(db_prefix() . 'accountledger.FY', $year);
    $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
    $this->db->where(db_prefix() . 'accountledger.PartyID', 'KASPL');
    $this->db->where(db_prefix() . 'clients.SubActGroupID1', '100015'); // 100025 = Salarys & Staff Welfare
    $this->db->where(db_prefix() . 'accountledger.Transdate BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
    $this->db->group_by('tblaccountledger.TType,tblaccountledger.FY,tblaccountledger.AccountID');
    $EmpBenTrans =  $this->db->get(db_prefix() . 'accountledger')->result_array();
    $ActGroup2List = array();
    $AccountList = array();
    foreach ($EmpBenTrans as $DEKey => $DeVal) {
      $new2 = array(
        "AccountID1" => $DeVal["SubActGroupID1"],
        "AccountID" => $DeVal["SubActGroupID"],
        "AccountName" => $DeVal["SubActGroupName"]
      );
      array_push($ActGroup2List, $new2);
      $new = array(
        "AccountID2" => $DeVal["SubActGroupID"],
        "AccountID" => $DeVal["AccountID"],
        "AccountName" => $DeVal["company"]
      );
      array_push($AccountList, $new);
    }
    $ActGroup2UniqueList = array_unique($ActGroup2List, SORT_REGULAR);
    $AccountUniqueList = array_unique($AccountList, SORT_REGULAR);
    $i = 0;
    $nestedData = [];
    $TotalExpense = 0;
    $TotalExpensePre = 0;
    foreach ($ActGroup2UniqueList as $val2) {
      $ClsBalGroup2 = 0;
      $ClsBalGroup2Pre = 0;
      if ($ActGrp1["AccountID"] == $val2["AccountID2"]) {
        $Group2Data = [
          'SubGroupName' => $val2['AccountName'],
          'SubActGroupID' => $val2['AccountID'],
        ];
        foreach ($AccountUniqueList as $ActList) {
          if ($ActList["AccountID2"] == $val2['AccountID']) {
            $ClsBalAccountWise = 0;
            $ClsBalAccountWisePre = 0;
            $Act_opn = 0;
            $ActCr = 0;
            $ActDr = 0;
            $Act_opnPre = 0;
            $ActCrPre = 0;
            $ActDrPre = 0;
            foreach ($EmpBenTrans as $key => $val) {
              if ($val["TType"] == "C" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]) {
                $ActCr += $val["SumAmt"];
              } else if ($val["TType"] == "D" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]) {
                $ActDr += $val["SumAmt"];
              }
              if ($val["TType"] == "C" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]) {
                $ActCrPre += $val["SumAmt"];
              } else if ($val["TType"] == "D" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]) {
                $ActDrPre += $val["SumAmt"];
              }
            }
            $ClsBalAccountWise =  $ActDr - $ActCr;
            $ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
            $ClsBalGroup2 += $ClsBalAccountWise;
            $ClsBalGroup2Pre += $ClsBalAccountWisePre;
            $AccountData = [
              'AccountName' => $ActList['AccountName'],
              'AccountID' => $ActList['AccountID'],
              'AccountClsBal' => $ClsBalAccountWise,
              'AccountClsBalPre' => $ClsBalAccountWisePre,
            ];
            $Group2Data['Accounts'][] = $AccountData;
          }
        }
        $Group2Data['Group2ClsBal'] = abs($ClsBalGroup2);
        $Group2Data['Group2ClsBalPre'] = abs($ClsBalGroup2Pre);
      }
      $TotalExpense += abs($ClsBalGroup2);
      $TotalExpensePre += abs($ClsBalGroup2Pre);
      $nestedData[] = $Group2Data;
      $i++;
    }
    $EmpBenExpense = new stdClass();
    $EmpBenExpense->nestedData = $nestedData;
    $EmpBenExpense->CurrentYear = $TotalExpense;
    $EmpBenExpense->PriviousYear = $TotalExpensePre;
    return $EmpBenExpense;
  }


  //========================= Get Finance Cost ===================================
  public function GetFinanceCostData($fromdate = "", $todate = "")
  {
    $fy = $this->session->userdata('finacial_year');
    $selected_company = $this->session->userdata('root_company');
    $last_fy = $fy - 1;
    $year = array($fy, $last_fy);
    if (empty($fromdate)) {
      $from_date = '20' . $fy . '-04-01 00:00:00';
    } else {
      $from_date = $fromdate;
    }
    //$from_date = '20'.$fy.'-04-01 00:00:00';
    if (empty($todate)) {
      $to_date = date('Y-m-d H:i:s');
    } else {
      $to_date = $todate;
    }
    $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY,tblclients.company,
		tblclients.AccountID,tblclients.SubActGroupID1,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName');
    $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
    $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
    $this->db->where_in(db_prefix() . 'accountledger.FY', $year);
    $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
    $this->db->where(db_prefix() . 'accountledger.PartyID', 'KASPL');
    $this->db->where(db_prefix() . 'clients.SubActGroupID1', '100030'); // 100029 = FINANCE COST
    $this->db->where(db_prefix() . 'accountledger.Transdate BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
    $this->db->group_by('tblaccountledger.TType,tblaccountledger.FY,tblaccountledger.AccountID');
    $FinCostTrans =  $this->db->get(db_prefix() . 'accountledger')->result_array();
    //return $FinCostTrans;
    $ActGroup2List = array();
    $AccountList = array();
    foreach ($FinCostTrans as $DEKey => $DeVal) {
      $new2 = array(
        "AccountID1" => $DeVal["SubActGroupID1"],
        "AccountID" => $DeVal["SubActGroupID"],
        "AccountName" => $DeVal["SubActGroupName"]
      );
      array_push($ActGroup2List, $new2);
      $new = array(
        "AccountID2" => $DeVal["SubActGroupID"],
        "AccountID" => $DeVal["AccountID"],
        "AccountName" => $DeVal["company"]
      );
      array_push($AccountList, $new);
    }
    $ActGroup2UniqueList = array_unique($ActGroup2List, SORT_REGULAR);
    $AccountUniqueList = array_unique($AccountList, SORT_REGULAR);
    $i = 0;
    $nestedData = [];
    $TotalfinCost = 0;
    $TotalfinCostPre = 0;
    foreach ($ActGroup2UniqueList as $val2) {
      $ClsBalGroup2 = 0;
      $ClsBalGroup2Pre = 0;
      // if ($ActGrp1["AccountID"] == $val2["AccountID2"]) {
      $Group2Data = [
        'SubGroupName' => $val2['AccountName'],
        'SubActGroupID' => $val2['AccountID'],
      ];
      foreach ($AccountUniqueList as $ActList) {
        if ($ActList["AccountID2"] == $val2['AccountID']) {
          $ClsBalAccountWise = 0;
          $ClsBalAccountWisePre = 0;
          $Act_opn = 0;
          $ActCr = 0;
          $ActDr = 0;
          $Act_opnPre = 0;
          $ActCrPre = 0;
          $ActDrPre = 0;
          foreach ($FinCostTrans as $key => $val) {
            if ($val["TType"] == "C" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]) {
              $ActCr += $val["SumAmt"];
            } else if ($val["TType"] == "D" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]) {
              $ActDr += $val["SumAmt"];
            }
            if ($val["TType"] == "C" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]) {
              $ActCrPre += $val["SumAmt"];
            } else if ($val["TType"] == "D" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]) {
              $ActDrPre += $val["SumAmt"];
            }
          }
          $ClsBalAccountWise =  $ActDr - $ActCr;
          $ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
          $ClsBalGroup2 += $ClsBalAccountWise;
          $ClsBalGroup2Pre += $ClsBalAccountWisePre;
          $AccountData = [
            'AccountName' => $ActList['AccountName'],
            'AccountID' => $ActList['AccountID'],
            'AccountClsBal' => $ClsBalAccountWise,
            'AccountClsBalPre' => $ClsBalAccountWisePre,
          ];
          $Group2Data['Accounts'][] = $AccountData;
        }
      }
      $Group2Data['Group2ClsBal'] = abs($ClsBalGroup2);
      $Group2Data['Group2ClsBalPre'] = abs($ClsBalGroup2Pre);
      // }
      $TotalfinCost += abs($ClsBalGroup2);
      $TotalfinCostPre += abs($ClsBalGroup2Pre);
      $nestedData[] = $Group2Data;
      $i++;
    }
    $FinCostExpense = new stdClass();
    $FinCostExpense->nestedData = $nestedData;
    $FinCostExpense->CurrentYear = $TotalfinCost;
    $FinCostExpense->PriviousYear = $TotalfinCostPre;
    return $FinCostExpense;
  }

  //===================== DEPRECIATION AND AMORTIZATION (PLANT) ==================
  public function GetDeprecAmortData($fromdate = "", $todate = "")
  {
    $fy = $this->session->userdata('finacial_year');
    $selected_company = $this->session->userdata('root_company');
    $last_fy = $fy - 1;
    $year = array($fy, $last_fy);
    if (empty($fromdate)) {
      $from_date = '20' . $fy . '-04-01 00:00:00';
    } else {
      $from_date = $fromdate;
    }
    //$from_date = '20'.$fy.'-04-01 00:00:00';
    if (empty($todate)) {
      $to_date = date('Y-m-d H:i:s');
    } else {
      $to_date = $todate;
    }
    $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY,tblclients.company, tblclients.AccountID,tblclients.SubActGroupID1,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName');
    $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
    $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
    $this->db->where_in(db_prefix() . 'accountledger.FY', $year);
    $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
    $this->db->where(db_prefix() . 'accountledger.PartyID', 'KASPL');
    $this->db->where(db_prefix() . 'clients.SubActGroupID1', '100026'); // 100007 = DEPRECIATION AND AMORTIZATION
    $this->db->where(db_prefix() . 'accountledger.Transdate BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
    $this->db->group_by('tblaccountledger.TType,tblaccountledger.FY,tblaccountledger.AccountID');
    $DepreAmortTrans =  $this->db->get(db_prefix() . 'accountledger')->result_array();

    $ActGroup2List = array();
    $AccountList = array();
    foreach ($DepreAmortTrans as $DEKey => $DeVal) {
      $new2 = array(
        "AccountID1" => $DeVal["SubActGroupID1"],
        "AccountID" => $DeVal["SubActGroupID"],
        "AccountName" => $DeVal["SubActGroupName"]
      );
      array_push($ActGroup2List, $new2);
      $new = array(
        "AccountID2" => $DeVal["SubActGroupID"],
        "AccountID" => $DeVal["AccountID"],
        "AccountName" => $DeVal["company"]
      );
      array_push($AccountList, $new);
    }
    $ActGroup2UniqueList = array_unique($ActGroup2List, SORT_REGULAR);
    $AccountUniqueList = array_unique($AccountList, SORT_REGULAR);
    $i = 0;
    $nestedData = [];
    $TotalDeprAmort = 0;
    $TotalDeprAmortPre = 0;
    foreach ($ActGroup2UniqueList as $val2) {
      $ClsBalGroup2 = 0;
      $ClsBalGroup2Pre = 0;
      // if ($ActGrp1["AccountID"] == $val2["AccountID2"]) {
      $Group2Data = [
        'SubGroupName' => $val2['AccountName'],
        'SubActGroupID' => $val2['AccountID'],
      ];
      foreach ($AccountUniqueList as $ActList) {
        if ($ActList["AccountID2"] == $val2['AccountID']) {
          $ClsBalAccountWise = 0;
          $ClsBalAccountWisePre = 0;
          $Act_opn = 0;
          $ActCr = 0;
          $ActDr = 0;
          $Act_opnPre = 0;
          $ActCrPre = 0;
          $ActDrPre = 0;
          foreach ($DepreAmortTrans as $key => $val) {
            if ($val["TType"] == "C" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]) {
              $ActCr += $val["SumAmt"];
            } else if ($val["TType"] == "D" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]) {
              $ActDr += $val["SumAmt"];
            }
            if ($val["TType"] == "C" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]) {
              $ActCrPre += $val["SumAmt"];
            } else if ($val["TType"] == "D" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]) {
              $ActDrPre += $val["SumAmt"];
            }
          }
          $ClsBalAccountWise =  $ActDr - $ActCr;
          $ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
          $ClsBalGroup2 += $ClsBalAccountWise;
          $ClsBalGroup2Pre += $ClsBalAccountWisePre;
          $AccountData = [
            'AccountName' => $ActList['AccountName'],
            'AccountID' => $ActList['AccountID'],
            'AccountClsBal' => $ClsBalAccountWise,
            'AccountClsBalPre' => $ClsBalAccountWisePre,
          ];
          $Group2Data['Accounts'][] = $AccountData;
        }
      }
      $Group2Data['Group2ClsBal'] = abs($ClsBalGroup2);
      $Group2Data['Group2ClsBalPre'] = abs($ClsBalGroup2Pre);
      // }
      $TotalDeprAmort += abs($ClsBalGroup2);
      $TotalDeprAmortPre += abs($ClsBalGroup2Pre);
      $nestedData[] = $Group2Data;
      $i++;
    }
    $DeprAmortExpense = new stdClass();
    $DeprAmortExpense->nestedData = $nestedData;
    $DeprAmortExpense->CurrentYear = $TotalDeprAmort;
    $DeprAmortExpense->PriviousYear = $TotalDeprAmortPre;
    return $DeprAmortExpense;
  }

  //=========================== Indirect Expenses ================================
  public function GetOtherExpensesData($fromdate = "", $todate = "")
  {
    $fy = $this->session->userdata('finacial_year');
    $selected_company = $this->session->userdata('root_company');
    $last_fy = $fy - 1;
    $year = array($fy, $last_fy);
    // Get All Indirect Expenses Group1
    // 100007 = DEPRECIATION AND AMORTIZATION (PLANT) 100026
    // 100029 = FINANCE COST 100030
    // 100025 = SALARIES & STAFF WELFARE (OFFICE) 100015
    // 100064 = TAX EXPENSE 100042
    $ActSubGroup1 = array(100026, 100030, 100015, 100042);
    if (empty($fromdate)) {
      $from_date = '20' . $fy . '-04-01 00:00:00';
    } else {
      $from_date = $fromdate;
    }
    //$from_date = '20'.$fy.'-04-01 00:00:00';
    if (empty($todate)) {
      $to_date = date('Y-m-d H:i:s');
    } else {
      $to_date = $todate;
    }
    $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY,clients.company,
		tblclients.AccountID,tblclients.SubActGroupID1,tblaccountgroupssub1.SubActGroupName As ActGrp1,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName');
    $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
    $this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblclients.SubActGroupID1');
    $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
    $this->db->where_in(db_prefix() . 'accountledger.FY', $year);
    $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
    $this->db->where(db_prefix() . 'accountledger.PartyID', 'KASPL');
    $this->db->where(db_prefix() . 'clients.ActGroupID', '10018');
    $this->db->where_not_in(db_prefix() . 'accountgroupssub1.SubActGroupID1', $ActSubGroup1);
    $this->db->where(db_prefix() . 'accountledger.Transdate BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
    $this->db->group_by('tblaccountledger.TType,tblaccountledger.FY,tblaccountledger.AccountID');
    $InDirectExpData =  $this->db->get(db_prefix() . 'accountledger')->result_array();
    $ActGroup1List = array();
    $ActGroup2List = array();
    $AccountList = array();
    foreach ($InDirectExpData as $DEKey => $DeVal) {
      $new1 = array(
        "AccountID" => $DeVal["SubActGroupID1"],
        "AccountName" => $DeVal["ActGrp1"]
      );
      array_push($ActGroup1List, $new1);
      $new2 = array(
        "AccountID1" => $DeVal["SubActGroupID1"],
        "AccountID" => $DeVal["SubActGroupID"],
        "AccountName" => $DeVal["SubActGroupName"]
      );
      array_push($ActGroup2List, $new2);
      $new = array(
        "AccountID2" => $DeVal["SubActGroupID"],
        "AccountID" => $DeVal["AccountID"],
        "AccountName" => $DeVal["company"]
      );
      array_push($AccountList, $new);
    }
    $ActGroup1UniqueList = array_unique($ActGroup1List, SORT_REGULAR);
    $ActGroup2UniqueList = array_unique($ActGroup2List, SORT_REGULAR);
    $AccountUniqueList = array_unique($AccountList, SORT_REGULAR);
    $i = 0;
    $nestedData = [];
    $TotalExpense = 0;
    $TotalExpensePre = 0;
    foreach ($ActGroup1UniqueList as $ActGrp1) {
      $Group1Data = [
        'Group1Name' => $ActGrp1['AccountName'],
        'Group1ID' => $ActGrp1['AccountID'],
      ];
      $ClsBalGroup1 = 0;
      $ClsBalGroup1Pre = 0;
      foreach ($ActGroup2UniqueList as $val2) {
        $ClsBalGroup2 = 0;
        $ClsBalGroup2Pre = 0;
        if ($ActGrp1["AccountID"] == $val2["AccountID1"]) {
          $Group2Data = [
            'SubGroupName' => $val2['AccountName'],
            'SubActGroupID' => $val2['AccountID'],
          ];
          foreach ($AccountUniqueList as $ActList) {
            if ($ActList["AccountID2"] == $val2['AccountID']) {
              $ClsBalAccountWise = 0;
              $ClsBalAccountWisePre = 0;
              $Act_opn = 0;
              $ActCr = 0;
              $ActDr = 0;
              $Act_opnPre = 0;
              $ActCrPre = 0;
              $ActDrPre = 0;
              foreach ($InDirectExpData as $key => $val) {
                if ($val["TType"] == "C" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]) {
                  $ActCr += $val["SumAmt"];
                } else if ($val["TType"] == "D" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]) {
                  $ActDr += $val["SumAmt"];
                }
                if ($val["TType"] == "C" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]) {
                  $ActCrPre += $val["SumAmt"];
                } else if ($val["TType"] == "D" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]) {
                  $ActDrPre += $val["SumAmt"];
                }
              }
              $ClsBalAccountWise =  $ActDr - $ActCr;
              $ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
              $ClsBalGroup2 += $ClsBalAccountWise;
              $ClsBalGroup2Pre += $ClsBalAccountWisePre;
              $AccountData = [
                'AccountName' => $ActList['AccountName'],
                'AccountID' => $ActList['AccountID'],
                'AccountClsBal' => $ClsBalAccountWise,
                'AccountClsBalPre' => $ClsBalAccountWisePre,
              ];
              $Group2Data['Accounts'][] = $AccountData;
            }
          }
          $Group2Data['Group2ClsBal'] = abs($ClsBalGroup2);
          $Group2Data['Group2ClsBalPre'] = abs($ClsBalGroup2Pre);
          $ClsBalGroup1 += $ClsBalGroup2;
          $ClsBalGroup1Pre += $ClsBalGroup2Pre;
          $Group1Data['SubGroups2'][] = $Group2Data;
        }
      }
      $TotalExpense += abs($ClsBalGroup1);
      $TotalExpensePre += abs($ClsBalGroup1Pre);
      $Group1Data['Group1ClsBal'] = abs($ClsBalGroup1);
      $Group1Data['Group1ClsBalPre'] = abs($ClsBalGroup1Pre);
      $nestedData[] = $Group1Data;
      $i++;
    }
    $InDirectExpense = new stdClass();
    $InDirectExpense->nestedData = $nestedData;
    $InDirectExpense->CurrentYear = $TotalExpense;
    $InDirectExpense->PriviousYear = $TotalExpensePre;
    return $InDirectExpense;
  }

  //=========================== Tax Expense ======================================
  public function GetTaxExpense($fromdate = "", $todate = "")
  {
    $fy = $this->session->userdata('finacial_year');
    $selected_company = $this->session->userdata('root_company');
    $last_fy = $fy - 1;
    $year = array($fy, $last_fy);
    if (empty($fromdate)) {
      $from_date = '20' . $fy . '-04-01 00:00:00';
    } else {
      $from_date = $fromdate;
    }
    //$from_date = '20'.$fy.'-04-01 00:00:00';
    if (empty($todate)) {
      $to_date = date('Y-m-d H:i:s');
    } else {
      $to_date = $todate;
    }
    $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY,tblclients.company,
		tblclients.AccountID,tblclients.SubActGroupID1,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName');
    $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
    $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
    $this->db->where_in(db_prefix() . 'accountledger.FY', $year);
    $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
    $this->db->where(db_prefix() . 'accountledger.PartyID', 'KASPL');
    $this->db->where(db_prefix() . 'clients.SubActGroupID1', '100042');
    $this->db->where(db_prefix() . 'accountledger.Transdate BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
    $this->db->group_by('tblaccountledger.TType,tblaccountledger.FY,tblaccountledger.AccountID');
    $EmpBenTrans =  $this->db->get(db_prefix() . 'accountledger')->result_array();
    $ActGroup2List = array();
    $AccountList = array();
    foreach ($EmpBenTrans as $DEKey => $DeVal) {
      $new2 = array(
        "AccountID1" => $DeVal["SubActGroupID1"],
        "AccountID" => $DeVal["SubActGroupID"],
        "AccountName" => $DeVal["SubActGroupName"]
      );
      array_push($ActGroup2List, $new2);
      $new = array(
        "AccountID2" => $DeVal["SubActGroupID"],
        "AccountID" => $DeVal["AccountID"],
        "AccountName" => $DeVal["company"]
      );
      array_push($AccountList, $new);
    }
    $ActGroup2UniqueList = array_unique($ActGroup2List, SORT_REGULAR);
    $AccountUniqueList = array_unique($AccountList, SORT_REGULAR);
    $i = 0;
    $nestedData = [];
    $TotalExpense = 0;
    $TotalExpensePre = 0;
    foreach ($ActGroup2UniqueList as $val2) {
      $ClsBalGroup2 = 0;
      $ClsBalGroup2Pre = 0;
      if ($ActGrp1["AccountID"] == $val2["AccountID2"]) {
        $Group2Data = [
          'SubGroupName' => $val2['AccountName'],
          'SubActGroupID' => $val2['AccountID'],
        ];
        foreach ($AccountUniqueList as $ActList) {
          if ($ActList["AccountID2"] == $val2['AccountID']) {
            $ClsBalAccountWise = 0;
            $ClsBalAccountWisePre = 0;
            $Act_opn = 0;
            $ActCr = 0;
            $ActDr = 0;
            $Act_opnPre = 0;
            $ActCrPre = 0;
            $ActDrPre = 0;
            foreach ($EmpBenTrans as $key => $val) {
              if ($val["TType"] == "C" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]) {
                $ActCr += $val["SumAmt"];
              } else if ($val["TType"] == "D" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]) {
                $ActDr += $val["SumAmt"];
              }
              if ($val["TType"] == "C" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]) {
                $ActCrPre += $val["SumAmt"];
              } else if ($val["TType"] == "D" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]) {
                $ActDrPre += $val["SumAmt"];
              }
            }
            $ClsBalAccountWise =  $ActDr - $ActCr;
            $ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
            $ClsBalGroup2 += $ClsBalAccountWise;
            $ClsBalGroup2Pre += $ClsBalAccountWisePre;
            $AccountData = [
              'AccountName' => $ActList['AccountName'],
              'AccountID' => $ActList['AccountID'],
              'AccountClsBal' => $ClsBalAccountWise,
              'AccountClsBalPre' => $ClsBalAccountWisePre,
            ];
            $Group2Data['Accounts'][] = $AccountData;
          }
        }
        $Group2Data['Group2ClsBal'] = abs($ClsBalGroup2);
        $Group2Data['Group2ClsBalPre'] = abs($ClsBalGroup2Pre);
      }
      $TotalExpense += abs($ClsBalGroup2);
      $TotalExpensePre += abs($ClsBalGroup2Pre);
      $nestedData[] = $Group2Data;
      $i++;
    }
    $EmpBenExpense = new stdClass();
    $EmpBenExpense->nestedData = $nestedData;
    $EmpBenExpense->CurrentYear = $TotalExpense;
    $EmpBenExpense->PriviousYear = $TotalExpensePre;
    return $EmpBenExpense;
  }

  //=============== Get Transaction Against ItemID and Transaction Type ==========
  public function GetTransactionList($filter_data){
    if($filter_data["TransactionType"] == "Sale"){
      $TType = array("O");
      $TType2 = array("SALE");
    }elseif($filter_data["TransactionType"] == "Sale Return"){
      $TType = array("SR");
      $TType2 = array("DAMAGE RETURN","FRESH RETURN");
    }elseif($filter_data["TransactionType"] == "Purchase Return"){
      $TType = array("P");
      $TType2 = array("PURCHASE RETURN");
    }elseif($filter_data["TransactionType"] == "Purchase"){
      $TType = array("P");
      $TType2 = array("Purchase");
    }
    $fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		if($filter_data["TransactionType"] == "Sale Credit"){
      $this->db->select('tblcdnotehistory.billno AS OrderID,tblcdnotehistory.transdate AS TransDate,
      tblcdnotehistory.TransID,tblcdnotehistory.transdate AS TransDate2,
      (tblcdnotehistory.rate) AS TotalAmt,
      tblcdnotehistory.qty AS BilledQty,tblcdnotehistory.rate AS BasicRate,
      (tblcdnotehistory.cgst + tblcdnotehistory.sgst + tblcdnotehistory.igst) AS GSTPer,
      tblcdnotehistory.cgstamt,tblcdnotehistory.sgstamt,tblcdnotehistory.igstamt,
          tblproduct.ProductID,tblproduct.ProductName,tblclients.company');
      $this->db->join('tblproduct', 'tblproduct.ProductID = tblcdnotehistory.itemid AND tblproduct.PlantID = tblcdnotehistory.plantid');
      $this->db->join('tblclients', 'tblclients.AccountID = tblcdnotehistory.AccountID AND tblclients.PlantID = tblcdnotehistory.plantid');
      $this->db->join('tblsalesmaster', 'tblsalesmaster.SalesID  = tblcdnotehistory.TransID AND tblsalesmaster.PlantID = tblcdnotehistory.plantid');
      $this->db->where_in(db_prefix() . 'cdnotehistory.TType', "C");
      $this->db->where_in(db_prefix() . 'cdnotehistory.fy', $fy);
      $this->db->where(db_prefix() . 'cdnotehistory.plantid', $selected_company);
      $this->db->where(db_prefix() . 'cdnotehistory.itemid', $filter_data["ItemID"]);
      $this->db->where(db_prefix() . 'cdnotehistory.TransID IS NOT NULL');
      $this->db->where( db_prefix() . 'cdnotehistory.transdate BETWEEN "'.$filter_data["fromDate"].'" AND "'.$filter_data["toDate"].'"');
      //$this->db->group_by('tblK1history.OrderID,tblK1history.AccountID');
      $TransactionList =  $this->db->get(db_prefix() . 'cdnotehistory')->result_array();
		}elseif($filter_data["TransactionType"] == "Purchase Debit"){
      $this->db->select('tblcdnotehistory.billno AS OrderID,tblcdnotehistory.transdate AS TransDate,
      tblcdnotehistory.TransID,tblcdnotehistory.transdate AS TransDate2,
      (tblcdnotehistory.rate) AS TotalAmt,
      tblcdnotehistory.qty AS BilledQty,tblcdnotehistory.rate AS BasicRate,
      (tblcdnotehistory.cgst + tblcdnotehistory.sgst + tblcdnotehistory.igst) AS GSTPer,
      tblcdnotehistory.cgstamt,tblcdnotehistory.sgstamt,tblcdnotehistory.igstamt,
          tblproduct.ProductID,tblproduct.ProductName,tblclients.company');
      $this->db->join('tblproduct', 'tblproduct.ProductID = tblcdnotehistory.itemid AND tblproduct.PlantID = tblcdnotehistory.plantid');
      $this->db->join('tblclients', 'tblclients.AccountID = tblcdnotehistory.AccountID AND tblclients.PlantID = tblcdnotehistory.plantid');
      $this->db->join('tblpurchasemaster', 'tblpurchasemaster.PurchID  = tblcdnotehistory.TransID AND tblpurchasemaster.PlantID = tblcdnotehistory.plantid');
      $this->db->where_in(db_prefix() . 'cdnotehistory.TType', "D");
      $this->db->where_in(db_prefix() . 'cdnotehistory.fy', $fy);
      $this->db->where(db_prefix() . 'cdnotehistory.plantid', $selected_company);
      $this->db->where(db_prefix() . 'cdnotehistory.itemid', $filter_data["ItemID"]);
      $this->db->where(db_prefix() . 'cdnotehistory.TransID IS NOT NULL');
      $this->db->where( db_prefix() . 'cdnotehistory.transdate BETWEEN "'.$filter_data["fromDate"].'" AND "'.$filter_data["toDate"].'"');
      //$this->db->group_by('tblK1history.OrderID,tblK1history.AccountID');
      $TransactionList =  $this->db->get(db_prefix() . 'cdnotehistory')->result_array();
		}else{
      $this->db->select('tblK1history.*,(tblK1history.BasicRate * tblK1history.BilledQty) AS ItemTotalAmt, tblCenterMaster.CenterName,tblCenterMaster.GSTNo, tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit,tblproduct.PackingQty,tbltaxes.taxrate, tblclients.company,tblclients.state,tblCenterMaster.state As CenterState');	
      $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
      $this->db->join('tbltaxes', 'tbltaxes.id = tblproduct.gst');
      $this->db->join('tblclients', 'tblclients.AccountID = tblK1history.AccountID');
      $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
      if(!is_admin()){
        $this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblK1history.CenterID');
        $this->db->where('tblstaff_wise_center.AccountID', $UserID);
      }
      $this->db->where('tblK1history.TransDate BETWEEN "'.$filter_data["fromDate"].'" AND "'.$filter_data["toDate"].'"');	
      $this->db->where_in('tblK1history.TType', $TType);
      $this->db->where_in('tblK1history.TType2', $TType2);
      $this->db->where('tblK1history.ItemID', $filter_data["ItemID"]);
      $this->db->where('tblK1history.BillID IS NOT NULL');
      $this->db->where('tblK1history.TransID IS NOT NULL');
      $this->db->where('tblK1history.FY',$fy);
      $this->db->where('tblK1history.PartyID',"KASPL");
      $TransactionList = $this->db->get('tblK1history')->result_array();

      // $this->db->select('tblK1history.OrderID, tblK1history.TransDate, tblK1history.TransID, tblK1history.TransDate2, tblK1history.BillID, (tblK1history.BilledQty * tblK1history.BasicRate) AS TotalAmt, tblK1history.DiscPerc, tblK1history.DiscAmt, tblK1history.BilledQty, tblK1history.BasicRate, tblK1history.cgstamt, tblK1history.sgstamt,tblK1history.igstamt,
      // tblproduct.ProductID,tblproduct.ProductName,
      // tbltaxes.taxrate as GSTPer,
      // tblclients.company');
      // $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
      // $this->db->join('tbltaxes', 'tbltaxes.id = tblproduct.gst');
      // $this->db->join('tblclients', 'tblclients.AccountID = tblK1history.AccountID AND tblclients.PlantID = tblK1history.PlantID');
      // $this->db->where_in(db_prefix() . 'K1history.TType', $TType);
      // $this->db->where_in(db_prefix() . 'K1history.TType2', $TType2);
      // $this->db->where(db_prefix() . 'K1history.FY', $fy);
      // $this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
      // $this->db->where(db_prefix() . 'K1history.ItemID', $filter_data["ItemID"]);
      // $this->db->where(db_prefix() . 'K1history.BillID IS NOT NULL');
      // $this->db->where(db_prefix() . 'K1history.TransID IS NOT NULL');
      // $this->db->where( db_prefix() . 'K1history.TransDate BETWEEN "'.$filter_data["fromDate"].'" AND "'.$filter_data["toDate"].'"');
      // //$this->db->group_by('tblK1history.OrderID,tblK1history.AccountID');
      // $TransactionList =  $this->db->get(db_prefix() . 'K1history')->result_array();
		}
		return $TransactionList;
	}

  //============================ Balance Sheet Function ==========================
  //=================== Get Balance Sheet Main Group List ========================
  public function fetchAccountsData($filter_data = "")
  {
    $BalanceSheet_head['MainGroup'] = array("10000", "10035", "10025", "10028", "10010", "10011", "10018", "10019");
    $this->db->select('tblaccountgroups.ActGroupName,tblaccountgroups.ActGroupID');
    if ($filter_data["MainGroup"]) {
      $this->db->where_in('tblaccountgroups.ActGroupID', $filter_data["MainGroup"]);
    } else {
      $this->db->where_in('tblaccountgroups.ActGroupID', $BalanceSheet_head["MainGroup"]);
    }
    return $this->db->get('tblaccountgroups')->result_array();
  }
  //================ Get Balance Sheet Account SUbGroup1 List ====================
  public function GetActSubGroup1ByMainGroup($BalanceSheet_head, $All = "")
  {
    $this->db->select('tblaccountgroupssub1.SubActGroupName,tblaccountgroupssub1.SubActGroupID1,tblaccountgroupssub1.ActGroupID');
    if ($BalanceSheet_head["ActSubGroup1"] && $All == "") {
      $this->db->where_in('SubActGroupID1', $BalanceSheet_head["ActSubGroup1"]);
    } else {
      $this->db->where_in('ActGroupID', $BalanceSheet_head["MainGroup"]);
    }
    return $this->db->get('tblaccountgroupssub1')->result_array();
  }
  //================== Get Balance Sheet Account SUbGroup2 List ==================
  public function GetActSubGroup2ByMainGroup($BalanceSheet_head, $All = "")
  {
    $this->db->select('tblaccountgroupssub.SubActGroupName,tblaccountgroupssub.SubActGroupID,tblaccountgroupssub.SubActGroupID1');
    $this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblaccountgroupssub.SubActGroupID1');
    if ($BalanceSheet_head["AccountSubGroupID2"] && $All == "") {
      $this->db->where_in('tblaccountgroupssub.SubActGroupID', $BalanceSheet_head["AccountSubGroupID2"]);
    } else if ($BalanceSheet_head["ActSubGroup1"]) {
      $this->db->where_in('tblaccountgroupssub.SubActGroupID1', $BalanceSheet_head["ActSubGroup1"]);
    } else {
      $this->db->where_in('tblaccountgroupssub1.ActGroupID', $BalanceSheet_head["MainGroup"]);
    }
    return $this->db->get('tblaccountgroupssub')->result_array();
  }
  //=================== Get Balance Sheet Account List ===========================
  public function GetAccountListByMainGroup($mainGroupID)
  {
    $this->db->select('tblclients.company,tblclients.AccountID,tblclients.SubActGroupID,tblaccountgroupssub1.ActGroupID');
    $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
    $this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblaccountgroupssub.SubActGroupID1');
    $this->db->where_in('tblaccountgroupssub1.ActGroupID', $mainGroupID["MainGroup"]);
    return $this->db->get('tblclients')->result_array();
  }
  //================ Get Balance Sheet Staff Account List ========================
  public function GetStaffList($mainGroupID)
  {
    $GICAccounts = array("GIC", "GIC7", "MAN");
    $this->db->select('tblstaff.firstname,tblstaff.lastname,tblstaff.AccountID,tblstaff.SubActGroupID,tblaccountgroupssub1.ActGroupID');
    $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblstaff.SubActGroupID');
    $this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblaccountgroupssub.SubActGroupID1');
    $this->db->where_in('tblaccountgroupssub1.ActGroupID', $mainGroupID["MainGroup"]);
    $this->db->where_not_in('tblstaff.AccountID', $GICAccounts);
    return $this->db->get('tblstaff')->result_array();
  }
  //================== Get Account Ledger Data ===================================
  public function GetLedgerData($BalanceSheet_head, $todate = "")
  {
    $fy = $this->session->userdata('finacial_year');
    $selected_company = $this->session->userdata('root_company');
    $from_date = '20' . $fy . '-04-01 00:00:00';
    if (empty($todate)) {
      $to_date = date('Y-m-d H:i:s');
    } else {
      $to_date = $todate;
    }
    $this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,
			tblclients.AccountID,tblclients.SubActGroupID,tblclients.SubActGroupID1,tblclients.ActGroupID,tblaccountledger.FY');
    $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID');
    $this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head["MainGroup"]);
    $this->db->where('tblaccountledger.FY', $fy);
    $this->db->where('tblaccountledger.PlantID', $selected_company);
    $this->db->where(db_prefix() . 'accountledger.PartyID', 'KASPL');
    $this->db->where(db_prefix() . 'accountledger.Transdate BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
    $this->db->group_by('tblaccountledger.TType,tblclients.AccountID');
    $CurrentYrLedger_data = $this->db->get('tblaccountledger')->result_array();
    $Ledger_data->Cur_yr_ledger = $CurrentYrLedger_data;
    // Privius year ledger
    /*$last_fy = $fy - 1;
			$this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,tblclients.AccountID,tblclients.SubActGroupID,tblclients.SubActGroupID1,tblclients.ActGroupID,tblaccountledger.FY');
			$this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID');
			$this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head["MainGroup"]);
			$this->db->where('tblaccountledger.FY', $last_fy);
			$this->db->where('tblaccountledger.PlantID', $selected_company);
      $this->db->where(db_prefix() . 'accountledger.PartyID', 'KASPL');
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblaccountledger.TType,tblclients.AccountID');
			$lastYrLedger_data = $this->db->get('tblaccountledger')->result_array();
			$Ledger_data->Last_yr_ledger = $lastYrLedger_data;*/
    return $Ledger_data;
  }
  //================== Get Staff Account Ledger Data =============================
  public function GetStaffLedgerData($BalanceSheet_head, $todate = "")
  {
    $fy = $this->session->userdata('finacial_year');
    $selected_company = $this->session->userdata('root_company');
    $from_date = '20' . $fy . '-04-01 00:00:00';
    if (empty($todate)) {
      $to_date = date('Y-m-d H:i:s');
    } else {
      $to_date = $todate;
    }
    $this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,tblstaff.AccountID,tblstaff.SubActGroupID,tblstaff.SubActGroupID1,tblstaff.ActGroupID,tblaccountledger.FY');
    $this->db->join('tblstaff', 'tblstaff.AccountID = tblaccountledger.AccountID');
    $this->db->where('tblaccountledger.FY', $fy);
    $this->db->where('tblaccountledger.PlantID', $selected_company);
    $this->db->where(db_prefix() . 'accountledger.PartyID', 'KASPL');
    $this->db->where(db_prefix() . 'accountledger.Transdate BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
    $this->db->group_by('tblaccountledger.TType,tblstaff.AccountID');
    $CurrentYrLedger_data = $this->db->get('tblaccountledger')->result_array();
    $Ledger_data->Cur_yr_ledger = $CurrentYrLedger_data;
    // Privius year ledger
    /*$last_fy = $fy - 1;
			$this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,tblstaff.AccountID,tblaccountledger.FY');
			$this->db->join('tblstaff', 'tblstaff.AccountID = tblaccountledger.AccountID');
			$this->db->where('tblaccountledger.FY', $last_fy);
			$this->db->where('tblaccountledger.PlantID', $selected_company);
      $this->db->where(db_prefix() . 'accountledger.PartyID', 'KASPL');
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblaccountledger.TType,tblstaff.AccountID');
			$lastYrLedger_data = $this->db->get('tblaccountledger')->result_array();
			$Ledger_data->Last_yr_ledger = $lastYrLedger_data;*/
    return $Ledger_data;
  }
  //============== Get Opn Balance For All Accounts ==============================
  public function GetOpnBalData($BalanceSheet_head)
  {
    $fy = $this->session->userdata('finacial_year');
    $selected_company = $this->session->userdata('root_company');
    //$Ledger_data = array();
    $this->db->select('SUM(tblaccountbalances.BAL1) AS SUMAmt,tblaccountbalances.AccountID,tblaccountbalances.FY');
    //$this->db->join('tblclients', 'tblclients.AccountID = tblaccountbalances.AccountID');
    //$this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head["MainGroup"]);
    $this->db->where('tblaccountbalances.FY', $fy);
    $this->db->where('tblaccountbalances.PlantID', $selected_company);
    $this->db->group_by('tblaccountbalances.AccountID');
    $CurrentYrOpnBal = $this->db->get('tblaccountbalances')->result_array();
    $OpnBal_data->Cur_yr_OpnBal = $CurrentYrOpnBal;
    // Privius year ledger
    /*$last_fy = $fy - 1;
			$this->db->select('SUM(tblaccountbalances.BAL1) AS SUMAmt,tblaccountbalances.AccountID,tblaccountbalances.FY');
			$this->db->join('tblclients', 'tblclients.AccountID = tblaccountbalances.AccountID');
				$this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
				$this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head["MainGroup"]);
			$this->db->where('tblaccountbalances.FY', $last_fy);
			$this->db->where('tblaccountbalances.PlantID', $selected_company);
			$this->db->group_by('tblaccountbalances.AccountID');
			$CurrentYrOpnBal = $this->db->get('tblaccountbalances')->result_array();
			$OpnBal_data->Last_yr_OpnBal = $CurrentYrOpnBal;*/
    return $OpnBal_data;
  }

  //====================== Get Fixed Assets ======================================
  public function GetFixedAssetsLedger()
  {
    $this->db->select('SubActGroupID,AccountID,company');
    $this->db->where_in(db_prefix() . 'clients.SubActGroupID1', "100040");
    $FixedAssetsLedger =  $this->db->get(db_prefix() . 'clients')->result_array();
    return $FixedAssetsLedger;
  }
}
