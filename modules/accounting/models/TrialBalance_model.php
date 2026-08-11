<?php



defined('BASEPATH') or exit('No direct script access allowed');



class TrialBalance_model extends App_Model

{

    public function __construct()

    {

        parent::__construct();

    }

    public function fetchAccountsData($filter_data = "")

    {

        $BalanceSheet_head['MainGroup'] = array("10000","10035","10025","10028","10010","10011","10018","10019");

        $this->db->select('tblaccountgroups.ActGroupName,tblaccountgroups.ActGroupID');

        if($filter_data["MainGroup"]){

            $this->db->where_in('tblaccountgroups.ActGroupID', $filter_data["MainGroup"]);

        }else{

            $this->db->where_in('tblaccountgroups.ActGroupID', $BalanceSheet_head["MainGroup"]);

        }

        return $this->db->get('tblaccountgroups')->result_array();

    }

    public function GetActSubGroup1ByMainGroup($BalanceSheet_head,$All = "")

    {

        $this->db->select('tblaccountgroupssub1.SubActGroupName,tblaccountgroupssub1.SubActGroupID1,tblaccountgroupssub1.ActGroupID');

        if($BalanceSheet_head["ActSubGroup1"] && $All == ""){

            $this->db->where_in('SubActGroupID1', $BalanceSheet_head["ActSubGroup1"]);

        }else{

            $this->db->where_in('ActGroupID', $BalanceSheet_head["MainGroup"]);

        }

        return $this->db->get('tblaccountgroupssub1')->result_array();

    }

    public function GetActSubGroup2ByMainGroup($BalanceSheet_head,$All = "")

    {

        $this->db->select('tblaccountgroupssub.SubActGroupName,tblaccountgroupssub.SubActGroupID,tblaccountgroupssub.SubActGroupID1');

        $this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblaccountgroupssub.SubActGroupID1');

        if($BalanceSheet_head["AccountSubGroupID2"] && $All == ""){

            $this->db->where_in('tblaccountgroupssub.SubActGroupID', $BalanceSheet_head["AccountSubGroupID2"]);

        }else if($BalanceSheet_head["ActSubGroup1"]){

            $this->db->where_in('tblaccountgroupssub.SubActGroupID1', $BalanceSheet_head["ActSubGroup1"]);

        }else{

            $this->db->where_in('tblaccountgroupssub1.ActGroupID', $BalanceSheet_head["MainGroup"]);

        }

        return $this->db->get('tblaccountgroupssub')->result_array();

    }

    public function GetAccountListByMainGroup($mainGroupID)

    {

        // Get Balence sheet account except Trade Payables - Vendor and Trade Receivables - Party

        $Trade_pay_rec = array("1000006","1000016");

        

        $this->db->select('tblclients.company,tblclients.AccountID,tblclients.SubActGroupID,tblaccountgroupssub1.ActGroupID');

        $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');

        $this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblaccountgroupssub.SubActGroupID1');

        $this->db->where_in('tblaccountgroupssub1.ActGroupID', $mainGroupID["MainGroup"]);

        //$this->db->where_not_in('tblaccountgroupssub.SubActGroupID', $Trade_pay_rec);

        return $this->db->get('tblclients')->result_array();

    }

    public function GetStaffList($mainGroupID)

    {

      $GICAccounts = array("GIC","GIC7","MAN");

      $this->db->select('tblstaff.firstname,tblstaff.lastname,tblstaff.AccountID,tblstaff.SubActGroupID,tblaccountgroupssub1.ActGroupID');

      $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblstaff.SubActGroupID');

      $this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblaccountgroupssub.SubActGroupID1');

      $this->db->where_in('tblaccountgroupssub1.ActGroupID', $mainGroupID["MainGroup"]);

      $this->db->where_not_in('tblstaff.AccountID', $GICAccounts);

      return $this->db->get('tblstaff')->result_array();

    }

    public function GetLedgerData($BalanceSheet_head)

    {

        $fy = $this->session->userdata('finacial_year');

        $selected_company = $this->session->userdata('root_company');

        // Excluding Trade Payables - Vendor and Trade Receivables - Party

        $Trade_pay_rec = array("1000006","1000016");

        //$Ledger_data = array();

        $this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,tblclients.AccountID,tblclients.SubActGroupID,tblclients.SubActGroupID1,tblclients.ActGroupID,tblaccountledger.FY');

        $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID');

        $this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head["MainGroup"]);

        //$this->db->where_not_in('tblclients.SubActGroupID', $Trade_pay_rec);

        $this->db->where('tblaccountledger.FY', $fy);

        $this->db->where('tblaccountledger.PlantID', $selected_company);

        $this->db->where('tblaccountledger.PartyID', "KASPL");

        $this->db->group_by('tblaccountledger.TType,tblclients.AccountID');

        $CurrentYrLedger_data = $this->db->get('tblaccountledger')->result_array();

        $Ledger_data->Cur_yr_ledger = $CurrentYrLedger_data;

        // Privius year ledger

        $last_fy = $fy - 1;

        $this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,tblclients.AccountID,tblclients.SubActGroupID,tblclients.SubActGroupID1,tblclients.ActGroupID,tblaccountledger.FY');

        $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID');

        $this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head["MainGroup"]);

        //$this->db->where_not_in('tblclients.SubActGroupID', $Trade_pay_rec);

        $this->db->where('tblaccountledger.FY', $last_fy);

        $this->db->where('tblaccountledger.PlantID', $selected_company);

        $this->db->where('tblaccountledger.PartyID', "KASPL");

        $this->db->group_by('tblaccountledger.TType,tblclients.AccountID');

        $lastYrLedger_data = $this->db->get('tblaccountledger')->result_array();

        $Ledger_data->Last_yr_ledger = $lastYrLedger_data;

        return $Ledger_data;

    }

    public function GetStaffLedgerData($BalanceSheet_head)

    {

      $fy = $this->session->userdata('finacial_year');

      $selected_company = $this->session->userdata('root_company');

      

      $this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,tblstaff.AccountID,tblstaff.SubActGroupID,tblstaff.SubActGroupID1,tblstaff.ActGroupID,tblaccountledger.FY');

      $this->db->join('tblstaff', 'tblstaff.AccountID = tblaccountledger.AccountID');

      $this->db->where('tblaccountledger.FY', $fy);

      $this->db->where('tblaccountledger.PlantID', $selected_company);

      $this->db->group_by('tblaccountledger.TType,tblstaff.AccountID');

      $CurrentYrLedger_data = $this->db->get('tblaccountledger')->result_array();

      $Ledger_data->Cur_yr_ledger = $CurrentYrLedger_data;

      // Privius year ledger

      $last_fy = $fy - 1;

      $this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,tblstaff.AccountID,tblaccountledger.FY');

      $this->db->join('tblstaff', 'tblstaff.AccountID = tblaccountledger.AccountID');

      $this->db->where('tblaccountledger.FY', $last_fy);

      $this->db->where('tblaccountledger.PlantID', $selected_company);

      $this->db->group_by('tblaccountledger.TType,tblstaff.AccountID');

      $lastYrLedger_data = $this->db->get('tblaccountledger')->result_array();

      $Ledger_data->Last_yr_ledger = $lastYrLedger_data;

      return $Ledger_data;

    }

    public function GetOpnBalData($BalanceSheet_head)

    {

        $fy = $this->session->userdata('finacial_year');

        $selected_company = $this->session->userdata('root_company');

        

        //$Ledger_data = array();

        $this->db->select('SUM(tblaccountbalances.BAL1) AS SUMAmt,tblclients.AccountID,tblaccountbalances.FY');

        $this->db->join('tblclients', 'tblclients.AccountID = tblaccountbalances.AccountID');

        $this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head["MainGroup"]);

        //$this->db->where_not_in('tblclients.SubActGroupID', $Trade_pay_rec);

        $this->db->where('tblaccountbalances.FY', $fy);

        $this->db->where('tblaccountbalances.PlantID', $selected_company);

        $this->db->group_by('tblaccountbalances.AccountID');

        $CurrentYrOpnBal = $this->db->get('tblaccountbalances')->result_array();

        $OpnBal_data->Cur_yr_OpnBal = $CurrentYrOpnBal;

        // Privius year ledger

        $last_fy = $fy - 1;

        $this->db->select('SUM(tblaccountbalances.BAL1) AS SUMAmt,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName,tblaccountbalances.FY');

        $this->db->join('tblclients', 'tblclients.AccountID = tblaccountbalances.AccountID');

        $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');

        $this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head["MainGroup"]);

        //$this->db->where_not_in('tblclients.SubActGroupID', $Trade_pay_rec);

        $this->db->where('tblaccountbalances.FY', $last_fy);

        $this->db->where('tblaccountbalances.PlantID', $selected_company);

        $this->db->group_by('tblclients.SubActGroupID');

        $CurrentYrOpnBal = $this->db->get('tblaccountbalances')->result_array();

        $OpnBal_data->Last_yr_OpnBal = $CurrentYrOpnBal;

        return $OpnBal_data;

    }

    public function Getrevenue_from_opn()

    {

        $fy = $this->session->userdata('finacial_year');

        $selected_company = $this->session->userdata('root_company');

        $last_fy = $fy- 1;

		    $year = array($fy,$last_fy);

        // Get Commodity Group 

        $mainGroupID = array('1','2','3');

        $this->db->select('tblitems_sub_groups.id,tblitems_sub_groups.name');

        $this->db->join(' tblitems_main_groups', ' tblitems_main_groups.id = tblitems_sub_groups.main_group_id');

        $this->db->where_in(db_prefix() . 'items_main_groups.id', $mainGroupID);

        $ItemGroup =  $this->db->get(db_prefix() . 'items_sub_groups')->result_array();

        

        $this->db->select('tblhistory.ItemID,tblhistory.BilledQty AS QtySum,tblhistory.final_rate,tblitems.subgroup_id,tblhistory.FY');

        $this->db->join('tblitems', 'tblitems.ItemID = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID');

        $this->db->where(db_prefix() . 'history.PlantID', $selected_company);

        $this->db->where_in(db_prefix() . 'history.FY', $year);

        $this->db->where(db_prefix() . 'history.TType', "S");

        $this->db->where(db_prefix() . 'history.TType2', "Sale");

        $this->db->where(db_prefix() . 'history.PartyID', "KASPL");

        $Saledata =  $this->db->get(db_prefix() . 'history')->result_array();

        /*echo "<pre>";

        print_r($Saledata);

        die;*/

        $i = 0;

        foreach($ItemGroup as $GRPKey =>$GRPVal){

            $ItemGroup[$i]['SaleAmt'] = 0;

            $ItemGroup[$i]['SaleAmtPre'] = 0;

            $i++;

        }

        $TotalSale = 0;

        $TotalSalePre = 0;

        foreach($Saledata as $key=>$val){

            $j = 0;

            foreach($ItemGroup as $GRPKey =>$GRPVal){

                if($GRPVal['id']==$val['subgroup_id']){

                    $Amt = $val['QtySum'] * $val['final_rate'];

				    if($val["FY"] == $fy){

				        $SaleOldAmt = $GRPVal['SaleAmt'];

						$SaleNewAmt = $SaleOldAmt + $Amt;

						$TotalSale += $Amt;

						$ItemGroup[$j]["SaleAmt"] = $SaleNewAmt;

				    }else{

				        $SaleOldAmt = $GRPVal['SaleAmtPre'];

						$SaleNewAmt = $SaleOldAmt + $Amt;

						$TotalSalePre += $Amt;

						$ItemGroup[$j]["SaleAmtPre"] = $SaleNewAmt;

				    }

                }

                $j++;

            }

        }

			$revenueData->CurrentYear = $TotalSale;

			$revenueData->PriviousYear = $TotalSalePre;

			$revenueData->TotalSaleGroupWise = $ItemGroup;

			return $revenueData;

    }

    public function GetOtherIncome()

    {

        $fy = $this->session->userdata('finacial_year');

      $selected_company = $this->session->userdata('root_company');

      $last_fy = $fy - 1;

      $year = array($fy,$last_fy);

      $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');

      $this->db->where_in(db_prefix() . 'accountledger.FY', $year);

      $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);

      $this->db->where(db_prefix() . 'clients.ActGroupID', '10019');

      $this->db->where('tblaccountledger.PartyID', "KASPL");

      $this->db->group_by('tblaccountledger.TType,tblaccountledger.FY');

      $OtherIncomeData =  $this->db->get(db_prefix() . 'accountledger')->result_array();

      

      $cr = 0;

	    $dr = 0;

	    $Bal = 0;

	    $crPre = 0;

	    $drPre = 0;

	    $BalPre = 0;

      foreach($OtherIncomeData as $key=>$val){

        if($val["TType"] == "C" && $val["FY"] == $fy){

          $cr += $val["SumAmt"];

        }else if($val["TType"] == "D" && $val["FY"] == $fy){

          $dr += $val["SumAmt"];

        }

        if($val["TType"] == "C" && $val["FY"] == $last_fy){

          $crPre += $val["SumAmt"];

        }else if($val["TType"] == "D" && $val["FY"] == $last_fy){

          $drPre += $val["SumAmt"];

        }

      }

      $Income = $cr - $dr;

      $IncomePre = $crPre - $drPre;

      $OtherIncome->CurrentYear = $Income;

      $OtherIncome->PriviousYear = $IncomePre;

      return $OtherIncome;

    }

    public function GetOtherIncomeSubgroup2Wise()

    {

        $fy = $this->session->userdata('finacial_year');

        $selected_company = $this->session->userdata('root_company');

        // Ledger Entry

        $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblclients.SubActGroupID');

        $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID');

        $this->db->where(db_prefix() . 'accountledger.FY', $fy);

        $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);

        $this->db->where_in(db_prefix() . 'clients.ActGroupID', '10019');

        $this->db->where('tblaccountledger.PartyID', "KASPL");

        $this->db->group_by('tblaccountledger.TType,tblclients.SubActGroupID');

        $CRDRActGroupWise2 =  $this->db->get(db_prefix() . 'accountledger')->result_array();

        

        // Opening Balance

        $this->db->select('SUM(tblaccountbalances.BAL1) AS OpnTotal,tblclients.SubActGroupID');

        $this->db->join('tblclients', 'tblclients.AccountID = tblaccountbalances.AccountID AND tblclients.PlantID = tblaccountbalances.PlantID');

        $this->db->where(db_prefix() . 'accountbalances.FY', $fy);

        $this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);

        $this->db->where_in(db_prefix() . 'clients.ActGroupID', '10019');

        $this->db->where('tblaccountbalances.PartyID', "KASPL");

        $this->db->group_by('tblclients.SubActGroupID');

        $OpnActGroupWise2 =  $this->db->get(db_prefix() . 'accountbalances')->result_array();

		

        //return $CRDRGroupWise;

        $this->db->select('tblaccountgroupssub1.SubActGroupID1,tblaccountgroupssub1.SubActGroupName AS SubActGroupName1');

        $this->db->where_in(db_prefix() . 'accountgroupssub1.ActGroupID', '10019');

        $OtherIncomeGroup1 =  $this->db->get(db_prefix() . 'accountgroupssub1')->result_array();

        $ActSubGroup1ID = array();

        

        foreach($OtherIncomeGroup1 as $key=>$val){

            array_push($ActSubGroup1ID,$val["SubActGroupID1"]);

        }

        //return $OtherIncomeGroup1;

        

        $this->db->select('tblaccountgroupssub.SubActGroupID,tblaccountgroupssub.SubActGroupName,tblaccountgroupssub.SubActGroupID1');

        $this->db->where_in(db_prefix() . 'accountgroupssub.SubActGroupID1', $ActSubGroup1ID);

        $OtherIncomeGroup2 =  $this->db->get(db_prefix() . 'accountgroupssub')->result_array();

        

        $i = 0;

      foreach($OtherIncomeGroup1 as $key=>$val){

        $subGrp2Array = array();

        $Group1Opn = 0;

        $Group1Cr = 0;

        $Group1Dr = 0;

        foreach($OtherIncomeGroup2 as $key1=>$val1){

          //$newArray = array();

          if($val1["SubActGroupID1"]==$val["SubActGroupID1"]){

            $data = array(

                          "ActSubGroupID2"=>$val1["SubActGroupID"],

                          "ActSubGroupName2"=>$val1["SubActGroupName"]

            );

            $CR = 0;

            $DR = 0;

            $Opn = 0;

            foreach($CRDRActGroupWise2 as $lKey=>$Lval){

              if($Lval["SubActGroupID"]==$val1["SubActGroupID"] && $Lval["TType"] == "C"){

                $CR += $Lval["SumAmt"];

              }else if($Lval["SubActGroupID"]==$val1["SubActGroupID"] && $Lval["TType"] == "D"){

                $DR += $Lval["SumAmt"];

              }

            }

            

            foreach($OpnActGroupWise2 as $OKey=>$Oval){

              if($Oval["SubActGroupID"]==$val1["SubActGroupID"]){

                $Opn += $Oval["OpnTotal"];

              }

            }

            $bal =  $Opn + $CR - $DR;

            $data['Balance'] = $bal;

            $data['CR'] = $CR;

            $data['DR'] = $DR;

            $data['Opn'] = $Opn;

            $Group1Opn += $Opn;

            $Group1Cr += $CR;

            $Group1Dr += $DR;

            array_push($subGrp2Array,$data);

          }

        }

        $OtherIncomeGroup1[$i]['SubGroup2'] = $subGrp2Array;

        $OtherIncomeGroup1[$i]['Opn'] = $Group1Opn;

        $OtherIncomeGroup1[$i]['CR'] = $Group1Cr;

        $OtherIncomeGroup1[$i]['DR'] = $Group1Dr;

        $i++;

      }

        return $OtherIncomeGroup1;

    }

    public function GetDirectExpenses()

    {

      $fy = $this->session->userdata('finacial_year');

      $last_fy = $fy - 1;

      $year = array($fy,$last_fy);

      $selected_company = $this->session->userdata('root_company');

      // Direct Expenses 

      $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID');

      $this->db->where_in(db_prefix() . 'accountledger.FY', $year);

      $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);

      $this->db->where(db_prefix() . 'accountledger.PartyID', "KASPL");

      $this->db->where(db_prefix() . 'clients.ActGroupID', '10010');

      $this->db->group_by('tblaccountledger.TType,tblaccountledger.FY');

      $DirectExpCR_DR =  $this->db->get(db_prefix() . 'accountledger')->result_array();

      

      $cr = 0;

      $dr = 0;

      $bal = 0;

      $crPre = 0;

      $drPre = 0;

      $balPre = 0;

      foreach($DirectExpCR_DR as $DExpkey=>$DExpval){

        if($DExpval["TType"] == "C" && $DExpval["FY"] == $fy){

          $cr = $DExpval["SumAmt"];

        }

        if($DExpval["TType"] == "D" && $DExpval["FY"] == $fy){

          $dr = $DExpval["SumAmt"];

        }

        

        if($DExpval["TType"] == "C" && $DExpval["FY"] == $last_fy){

          $crPre = $DExpval["SumAmt"];

        }

        if($DExpval["TType"] == "D" && $DExpval["FY"] == $last_fy){

          $drPre = $DExpval["SumAmt"];

        }

      }

      $bal = $dr - $cr;

      $balPre = $drPre - $crPre;

      $DirectExp->CurrentYear = $bal;

      $DirectExp->PriviousYear = $balPre;

      return $DirectExp;

    }

    public function GetDirectExpSubgroup2Wise()

    {

      $fy = $this->session->userdata('finacial_year');

      $selected_company = $this->session->userdata('root_company');

      // Ledger Entry

      $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblclients.SubActGroupID');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID');

      $this->db->where(db_prefix() . 'accountledger.FY', $fy);

      $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);

      $this->db->where_in(db_prefix() . 'clients.ActGroupID', '10010');

      $this->db->group_by('tblaccountledger.TType,tblclients.SubActGroupID');

      $CRDRActGroupWise2 =  $this->db->get(db_prefix() . 'accountledger')->result_array();

      

      // Opening Balance

      $this->db->select('SUM(tblaccountbalances.BAL1) AS OpnTotal,tblclients.SubActGroupID');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountbalances.AccountID AND tblclients.PlantID = tblaccountbalances.PlantID');

      $this->db->where(db_prefix() . 'accountbalances.FY', $fy);

      $this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);

      $this->db->where_in(db_prefix() . 'clients.ActGroupID', '10010');

      $this->db->group_by('tblclients.SubActGroupID');

      $OpnActGroupWise2 =  $this->db->get(db_prefix() . 'accountbalances')->result_array();

      

      

      $this->db->select('tblaccountgroupssub1.SubActGroupID1,tblaccountgroupssub1.SubActGroupName AS SubActGroupName1');

      $this->db->where_in(db_prefix() . 'accountgroupssub1.ActGroupID', '10010');

      $DirectExpGroup1 =  $this->db->get(db_prefix() . 'accountgroupssub1')->result_array();

      $ActSubGroup1ID = array();

      

      foreach($DirectExpGroup1 as $key=>$val){

        array_push($ActSubGroup1ID,$val["SubActGroupID1"]);

      }

      //return $OtherIncomeGroup1;

      

      $this->db->select('tblaccountgroupssub.SubActGroupID,tblaccountgroupssub.SubActGroupName,tblaccountgroupssub.SubActGroupID1');

      $this->db->where_in(db_prefix() . 'accountgroupssub.SubActGroupID1', $ActSubGroup1ID);

      $DirectExpGroup2 =  $this->db->get(db_prefix() . 'accountgroupssub')->result_array();

      $i = 0;

      foreach($DirectExpGroup1 as $key=>$val){

        $subGrp2Array = array();

        $Group1Opn = 0;

        $Group1Cr = 0;

        $Group1Dr = 0;

        foreach($DirectExpGroup2 as $key1=>$val1){

          //$newArray = array();

          if($val1["SubActGroupID1"]==$val["SubActGroupID1"]){

            $data = array(

                          "ActSubGroupID2"=>$val1["SubActGroupID"],

                          "ActSubGroupName2"=>$val1["SubActGroupName"]

            );

            $CR = 0;

            $DR = 0;

            $Opn = 0;

            foreach($CRDRActGroupWise2 as $lKey=>$Lval){

              if($Lval["SubActGroupID"]==$val1["SubActGroupID"] && $Lval["TType"] == "C"){

                $CR += $Lval["SumAmt"];

              }else if($Lval["SubActGroupID"]==$val1["SubActGroupID"] && $Lval["TType"] == "D"){

                $DR += $Lval["SumAmt"];

              }

            }

            

            foreach($OpnActGroupWise2 as $OKey=>$Oval){

              if($Oval["SubActGroupID"]==$val1["SubActGroupID"]){

                $Opn += $Oval["OpnTotal"];

              }

            }

            $bal =  $Opn + $DR - $CR;

            $data['Balance'] = $bal;

            $data['CR'] = $CR;

            $data['DR'] = $DR;

            $data['Opn'] = $Opn;

            $Group1Opn += $Opn;

            $Group1Cr += $CR;

            $Group1Dr += $DR;

            array_push($subGrp2Array,$data);

          }

        }

        $DirectExpGroup1[$i]['SubGroup2'] = $subGrp2Array;

        $DirectExpGroup1[$i]['Opn'] = $Group1Opn;

        $DirectExpGroup1[$i]['CR'] = $Group1Cr;

        $DirectExpGroup1[$i]['DR'] = $Group1Dr;

        $i++;

      }

      return $DirectExpGroup1;

    }

    public function GetCurrentInventoryValue($CenterID = "")

    {

        $fy = $this->session->userdata('finacial_year');

        $selected_company = $this->session->userdata('root_company');

        $last_fy = $fy - 1;

		    $year = array($fy,$last_fy);

        // Get Commodity Group 

        $mainGroupID = array('1','2','3');

        $this->db->select('tblitems_sub_groups.id,tblitems_sub_groups.name');

        $this->db->join(' tblitems_main_groups', ' tblitems_main_groups.id = tblitems_sub_groups.main_group_id');

        $this->db->where_in(db_prefix() . 'items_main_groups.id', $mainGroupID);

        $ItemGroup =  $this->db->get(db_prefix() . 'items_sub_groups')->result_array();

        // Beginning Inventory

        $this->db->select('SUM(tblstockmaster.OQty * tblstockmaster.AvgRate) AS OpnCost,tblitems.subgroup_id,tblstockmaster.FY');

        $this->db->join('tblitems', 'tblitems.ItemID = tblstockmaster.ItemID AND tblitems.PlantID = tblstockmaster.PlantID');

        $this->db->where(db_prefix() . 'stockmaster.PlantID', $selected_company);

        $this->db->where(db_prefix() . 'stockmaster.TypeID', "SP");

        $this->db->where(db_prefix() . 'stockmaster.PartyID', "KASPL");

        if($CenterID){

            $this->db->where(db_prefix() . 'stockmaster.CenterID', $CenterID);

        }

        $this->db->where_in(db_prefix() . 'stockmaster.FY', $year);

        $this->db->group_by(db_prefix() . 'items.subgroup_id,tblstockmaster.FY');

        $OpnStock =  $this->db->get(db_prefix() . 'stockmaster')->result_array();

        

        $TotalOpnBal = 0;

        $TotalOpnBalPre = 0;

        $i = 0;

        foreach($ItemGroup as $key=>$val){

            $OpnBal = 0;

            $OpnBalPre = 0;

            foreach($OpnStock as $okey=>$oval){

                if($val['id']==$oval['subgroup_id']){

                    if($oval == $fy){

                        $OpnBal += $oval['OpnCost'];

                    }else{

                        $OpnBalPre += $oval['OpnCost'];

                    }

                }

            }

            $TotalOpnBal += $OpnBal;

            $TotalOpnBalPre += $OpnBalPre;

            $ItemGroup[$i]['OpnBal'] = $OpnBal;

            $ItemGroup[$i]['CurrentValue'] = $OpnBal;

            $ItemGroup[$i]['SaleAmt'] = 0;

            $ItemGroup[$i]['PurchAmt'] = 0;

            $ItemGroup[$i]['OpnBalPre'] = $OpnBalPre;

            $ItemGroup[$i]['CurrentValuePre'] = $OpnBalPre;

            $ItemGroup[$i]['SaleAmtPre'] = 0;

            $ItemGroup[$i]['PurchAmtPre'] = 0;

            $i++;

        }

        

        // Get Purchase entry Item wise , PO wise FIFO Base

        $this->db->select('tblhistory.ItemID,tblhistory.final_rate,tblhistory.BilledQty,tblhistory.cgst,tblhistory.sgst,tblhistory.igst,tblitems.subgroup_id,tblhistory.FY');

        $this->db->join('tblitems', 'tblitems.ItemID = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID');

        $this->db->where(db_prefix() . 'history.PlantID', $selected_company);

        $this->db->where_in(db_prefix() . 'history.FY', $year);

        $this->db->where(db_prefix() . 'history.TType', "P");

        $this->db->where(db_prefix() . 'history.TType2', "Purchase");

        $this->db->where(db_prefix() . 'history.PartyID', "KASPL");

        if($CenterID){

            $this->db->where(db_prefix() . 'history.CenterID', $CenterID);

        }

        $this->db->order_by(db_prefix() . 'history.TransDate', 'ASC');

        $Purhasedata =  $this->db->get(db_prefix() . 'history')->result_array();

        

        // Get Sale entry Group By Item 

        

        $this->db->select('tblhistory.ItemID,SUM(tblhistory.BilledQty) AS QtySum,tblhistory.BasicRate,tblitems.subgroup_id,tblhistory.FY');

        $this->db->join('tblitems', 'tblitems.ItemID = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID');

        $this->db->where(db_prefix() . 'history.PlantID', $selected_company);

        $this->db->where_in(db_prefix() . 'history.FY', $year);

        $this->db->where(db_prefix() . 'history.TType', "S");

        $this->db->where(db_prefix() . 'history.TType2', "Sale");

        $this->db->where(db_prefix() . 'history.PartyID', "KASPL");

        if($CenterID){

            $this->db->where(db_prefix() . 'history.CenterID', $CenterID);

        }

        $this->db->group_by(db_prefix() . 'history.ItemID,tblhistory.FY');

        $this->db->order_by(db_prefix() . 'history.ItemID', 'ASC');

        $Saledata =  $this->db->get(db_prefix() . 'history')->result_array();

        

        /*echo "<pre>";

        print_r($Purhasedata);

        print_r($Saledata);

        die;*/

        //return $Saledata;

        // Purchase Amt is Purchase Quantity minus purchase return Quantity and minus (sale - sale return) Quantity with FIFO based 

        //then remaining qty multiply by final rate with respenctive order

        $ClosingInvAmt = 0;

        $ClosingInvAmtPre = 0;

        $TotalPurchValue = 0;

        $TotalPurchValuePre = 0;

        foreach($Purhasedata as $Pkey=>$PVal){

            $Match = 0;

            $i = 0;

            //echo $PVal["BilledQty"];

            //echo "<br>";

            if($PVal["FY"] == $fy){

                $TotalPurchValue += $PVal["BilledQty"] * $PVal["final_rate"];

            }else{

                $TotalPurchValuePre += $PVal["BilledQty"] * $PVal["final_rate"];

            }

            foreach($Saledata as $SKey=>$SVal){

                if($PVal["ItemID"] == $SVal["ItemID"] && $PVal["FY"] == $SVal["FY"]){

                    if($SVal["QtySum"] > 0){

                        /*echo $SVal["QtySum"];

                        echo "<br>";*/

                        $Match++;

                        $SaleQty = $SVal["QtySum"];

                        $PurchQty = $PVal["BilledQty"];

                        if($SaleQty <= $PurchQty){

                            $balQty = $PurchQty - $SaleQty;

                            if($PVal["FY"] == $fy){

                                $ClosingInvAmt += $balQty * $PVal["final_rate"];

                            }else{

                                $ClosingInvAmtPre += $balQty * $PVal["final_rate"];

                            }

                            

                            $PVal["BilledQty"] = $balQty;

                            $SaleAmt = ($SVal["QtySum"]) * $SVal["final_rate"];

                            $Saledata[$i]["QtySum"] = 0;

                            $ii = 0;

                            foreach($ItemGroup as $key=>$val){

                                if($val['id']==$SVal['subgroup_id'] && $val['id']==$PVal['subgroup_id']){

                                    

                                    if($PVal["FY"] == $fy){

                                        $SaleOldAmt = $val['SaleAmt'];

                                        $SaleNewAmt = $SaleOldAmt + $SaleAmt;

                                        $ItemGroup[$ii]["SaleAmt"] = $SaleNewAmt;

                                        

                                        $CurrentOldAmt = $val['CurrentValue'];

                                        $CurrentNewAmt = $CurrentOldAmt + ($balQty * $PVal["final_rate"]);

                                        $ItemGroup[$ii]["CurrentValue"] = $CurrentNewAmt;

                            

                                    }else{

                                        $SaleOldAmt = $val['SaleAmtPre'];

                                        $SaleNewAmt = $SaleOldAmt + $SaleAmt;

                                        $ItemGroup[$ii]["SaleAmtPre"] = $SaleNewAmt;

                                        

                                        $CurrentOldAmt = $val['CurrentValuePre'];

                                        $CurrentNewAmt = $CurrentOldAmt + ($balQty * $PVal["final_rate"]);

                                        $ItemGroup[$ii]["CurrentValuePre"] = $CurrentNewAmt;

                                    }

                                    

                                    if($PVal["FY"] == $fy){

                                        $PurchOldAmt = $val['PurchAmt'];

                                        $PurchNewAmt = $PurchOldAmt + ($PurchQty * $PVal["final_rate"]);

                                        $ItemGroup[$ii]["PurchAmt"] = $PurchNewAmt;

                                    }else{

                                        $PurchOldAmt = $val['PurchAmtPre'];

                                        $PurchNewAmt = $PurchOldAmt + ($PurchQty * $PVal["final_rate"]);

                                        $ItemGroup[$ii]["PurchAmtPre"] = $PurchNewAmt;

                                    }

                                }

                                $ii++;

                            }

                        }else{

                            $balQty = $SaleQty - $PurchQty;

                            $Saledata[$i]["QtySum"] = $balQty;

                            $Amt = ($PVal["BilledQty"]) * $PVal["final_rate"];

                            $SaleAmt = ($PVal["BilledQty"]) * $SVal["final_rate"];

                            $PVal["BilledQty"] = 0;

                            $ii = 0;

                            foreach($ItemGroup as $key=>$val){

                                if($val['id']==$SVal['subgroup_id'] && $val['id']==$PVal['subgroup_id']){

                                    if($PVal["FY"] == $fy){

                                        $SaleOldAmt = $val['SaleAmt'];

                                        $SaleNewAmt = $SaleOldAmt + $SaleAmt;

                                        $ItemGroup[$ii]["SaleAmt"] = $SaleNewAmt;

                                    }else{

                                        $SaleOldAmt = $val['SaleAmtPre'];

                                        $SaleNewAmt = $SaleOldAmt + $SaleAmt;

                                        $ItemGroup[$ii]["SaleAmtPre"] = $SaleNewAmt;

                                    }

                                    if($PVal["FY"] == $fy){

                                        $PurchOldAmt = $val['PurchAmt'];

                                        $PurchNewAmt = $PurchOldAmt + $Amt;

                                        $ItemGroup[$ii]["PurchAmt"] = $PurchNewAmt;

                                    }else{

                                        $PurchOldAmt = $val['PurchAmtPre'];

                                        $PurchNewAmt = $PurchOldAmt + $Amt;

                                        $ItemGroup[$ii]["PurchAmtPre"] = $PurchNewAmt;

                                    }

                                }

                                $ii++;

                            }

                        }

                    }

                }

                $i++;

            }

            if($Match == "0"){

                $Amt = ($PVal["BilledQty"]) * $PVal["final_rate"];

                if($PVal["FY"] == $fy){

                    $ClosingInvAmt += $Amt;

                }else{

                    $ClosingInvAmtPre += $Amt;

                }

                $ii = 0;

                foreach($ItemGroup as $key=>$val){

                    if($val['id']==$PVal['subgroup_id']){

                        if($PVal["FY"] == $fy){

                            $PurchOldAmt = $val['PurchAmt'];

                            $PurchNewAmt = $PurchOldAmt + $Amt;

                            $ItemGroup[$ii]["PurchAmt"] = $PurchNewAmt;

                            

                            $CurrentOldAmt = $val['CurrentValue'];

                            $CurrentNewAmt = $CurrentOldAmt + $Amt;

                            $ItemGroup[$ii]["CurrentValue"] = $CurrentNewAmt;

                        }else{

                            $PurchOldAmt = $val['PurchAmtPre'];

                            $PurchNewAmt = $PurchOldAmt + $Amt;

                            $ItemGroup[$ii]["PurchAmtPre"] = $PurchNewAmt;

                            

                            $CurrentOldAmt = $val['CurrentValuePre'];

                            $CurrentNewAmt = $CurrentOldAmt + $Amt;

                            $ItemGroup[$ii]["CurrentValuePre"] = $CurrentNewAmt;

                        }

                    }

                    $ii++;

                }

            }

        }

        //return $ItemGroup; 

        // Purchase minus sale quantity with FIFO basis

        // Cls = (OQty * AvgRate) + (Purchase - Purchase return) - (Sale - Sale return)

        

        $ClsAmt = $TotalOpnBal + $ClosingInvAmt;

        $ClsAmtPre = $TotalOpnBalPre + $ClosingInvAmtPre;

        

        $inventory_result["TotalOpnValue"] = $TotalOpnBal;

        $inventory_result["TotalPurchValue"] = $TotalPurchValue;

        $inventory_result["AllInventoryAmt"] = $ClsAmt;

        

        $inventory_result["TotalOpnValuePre"] = $TotalOpnBalPre;

        $inventory_result["TotalPurchValuePre"] = $TotalPurchValuePre;

        $inventory_result["AllInventoryAmtPre"] = $ClsAmtPre;

        

        $inventory_result["CommodityWiseInventoryAmt"] = $ItemGroup;

        

        return $inventory_result;

    }

    public function GetEMPBen()

    {

        $fy = $this->session->userdata('finacial_year');

        $selected_company = $this->session->userdata('root_company');

        $last_fy = $fy - 1;

        $year = array($fy,$last_fy);

        $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY');

        $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID');

        $this->db->where_in(db_prefix() . 'accountledger.FY', $year);

        $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);

        $this->db->where_in(db_prefix() . 'clients.SubActGroupID1', '100015');

        $this->db->group_by('tblaccountledger.TType,tblaccountledger.FY');

        $EmpBenTrans =  $this->db->get(db_prefix() . 'accountledger')->result_array();

        $cr = 0;

        $dr = 0;

        $bal = 0;

        $crPre = 0;

        $drPre = 0;

        $balPre = 0;

        foreach($EmpBenTrans as $Empkey=>$Empval){

          if($Empval["TType"] == "C" && $Empval["FY"] == $fy){

            $cr = $Empval["SumAmt"];

          }

          if($Empval["TType"] == "D" && $Empval["FY"] == $fy){

            $dr = $Empval["SumAmt"];

          }

          

          if($Empval["TType"] == "C" && $Empval["FY"] == $last_fy){

            $crPre = $Empval["SumAmt"];

          }

          if($Empval["TType"] == "D" && $Empval["FY"] == $last_fy){

            $drPre = $Empval["SumAmt"];

          }

        }

        $bal = $dr - $cr;

        $balPre = $drPre - $crPre;

        $EmpBenExp->CurrentYear = $bal;

        $EmpBenExp->PriviousYear = $balPre;

        return $EmpBenExp;

    }

    public function GetEMPBenSubgroup2Wise()

    {

      $fy = $this->session->userdata('finacial_year');

      $selected_company = $this->session->userdata('root_company');

      // Ledger Entry

      $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblclients.SubActGroupID');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID');

      $this->db->where(db_prefix() . 'accountledger.FY', $fy);

      $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);

      $this->db->where_in(db_prefix() . 'clients.SubActGroupID1', '100015');

      $this->db->group_by('tblaccountledger.TType,tblclients.SubActGroupID');

      $CRDRActGroupWise2 =  $this->db->get(db_prefix() . 'accountledger')->result_array();

      // Opening Balance

      $this->db->select('SUM(tblaccountbalances.BAL1) AS OpnTotal,tblclients.SubActGroupID');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountbalances.AccountID AND tblclients.PlantID = tblaccountbalances.PlantID');

      $this->db->where(db_prefix() . 'accountbalances.FY', $fy);

      $this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);

      $this->db->where_in(db_prefix() . 'clients.SubActGroupID1', '100015');

      $this->db->group_by('tblclients.SubActGroupID');

      $OpnActGroupWise2 =  $this->db->get(db_prefix() . 'accountbalances')->result_array();

      

      //return $CRDRGroupWise;

      $this->db->select('tblaccountgroupssub1.SubActGroupID1,tblaccountgroupssub1.SubActGroupName AS SubActGroupName1');

      $this->db->where_in(db_prefix() . 'accountgroupssub1.SubActGroupID1', '100015');

      $EmpBenGroup1 =  $this->db->get(db_prefix() . 'accountgroupssub1')->result_array();

      $ActSubGroup1ID = array();

      

      foreach($EmpBenGroup1 as $key=>$val){

        array_push($ActSubGroup1ID,$val["SubActGroupID1"]);

      }

      //return $OtherIncomeGroup1;

      

      $this->db->select('tblaccountgroupssub.SubActGroupID,tblaccountgroupssub.SubActGroupName,tblaccountgroupssub.SubActGroupID1');

      $this->db->where_in(db_prefix() . 'accountgroupssub.SubActGroupID1', $ActSubGroup1ID);

      $EmpBenGroup2 =  $this->db->get(db_prefix() . 'accountgroupssub')->result_array();

      $i = 0;

      foreach($EmpBenGroup1 as $key=>$val){

        $subGrp2Array = array();

        $Group1Opn = 0;

          $Group1Cr = 0;

          $Group1Dr = 0;

        foreach($EmpBenGroup2 as $key1=>$val1){

          //$newArray = array();

          if($val1["SubActGroupID1"]==$val["SubActGroupID1"]){

            $data = array(

                          "ActSubGroupID2"=>$val1["SubActGroupID"],

                          "ActSubGroupName2"=>$val1["SubActGroupName"]

            );

            $Opn = 0;

            $CR = 0;

            $DR = 0;

            foreach($CRDRActGroupWise2 as $lKey=>$Lval){

              if($Lval["SubActGroupID"]==$val1["SubActGroupID"] && $Lval["TType"] == "C"){

                $CR += $Lval["SumAmt"];

              }else if($Lval["SubActGroupID"]==$val1["SubActGroupID"] && $Lval["TType"] == "D"){

                $DR += $Lval["SumAmt"];

              }

            }

            foreach($OpnActGroupWise2 as $OKey=>$Oval){

              if($Oval["SubActGroupID"]==$val1["SubActGroupID"]){

                $Opn += $Oval["OpnTotal"];

              }

            }

            $bal =  $Opn + $DR - $CR;

            $data['Balance'] = $bal;

            $data['CR'] = $CR;

            $data['DR'] = $DR;

            $data['Opn'] = $Opn;

            array_push($subGrp2Array,$data);

          }

        }

        $EmpBenGroup1[$i]['SubGroup2'] = $subGrp2Array;

        $EmpBenGroup1[$i]['Opn'] = $Group1Opn;

          $EmpBenGroup1[$i]['CR'] = $Group1Cr;

          $EmpBenGroup1[$i]['DR'] = $Group1Dr;

        $i++;

      }

      return $EmpBenGroup1;

    }

    public function GetFinanceCostData()

    {

      $fy = $this->session->userdata('finacial_year');

      $selected_company = $this->session->userdata('root_company');

      $last_fy = $fy - 1;

      $year = array($fy,$last_fy);

      $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID');

      $this->db->where_in(db_prefix() . 'accountledger.FY', $year);

      $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);

      $this->db->where_in(db_prefix() . 'clients.SubActGroupID1', '100030');

      $this->db->group_by('tblaccountledger.TType,tblaccountledger.FY');

      $FinanceCostTrans =  $this->db->get(db_prefix() . 'accountledger')->result_array();

      $cr = 0;

      $dr = 0;

      $bal = 0;

      $crPre = 0;

      $drPre = 0;

      $balPre = 0;

      foreach($FinanceCostTrans as $FCkey=>$FCval){

        if($FCval["TType"] == "C" && $FCval["FY"] == $fy){

          $cr = $FCval["SumAmt"];

        }

        if($FCval["TType"] == "D" && $FCval["FY"] == $fy){

          $dr = $FCval["SumAmt"];

        }

        

        if($FCval["TType"] == "C" && $FCval["FY"] == $last_fy){

          $crPre = $FCval["SumAmt"];

        }

        if($FCval["TType"] == "D" && $FCval["FY"] == $last_fy){

          $drPre = $FCval["SumAmt"];

        }

      }

      $bal = $dr - $cr;

      $balPre = $drPre - $crPre;

      $FinanceCostExp->CurrentYear = $bal;

      $FinanceCostExp->PriviousYear = $balPre;

      return $FinanceCostExp;

    }

    public function GetFinCostSubgroup2Wise()

    {

      $fy = $this->session->userdata('finacial_year');

      $selected_company = $this->session->userdata('root_company');

      // Ledger Entry

      $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblclients.SubActGroupID');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID');

      $this->db->where(db_prefix() . 'accountledger.FY', $fy);

      $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);

      $this->db->where_in(db_prefix() . 'clients.SubActGroupID1', '100030');

      $this->db->group_by('tblaccountledger.TType,tblclients.SubActGroupID');

      $CRDRActGroupWise2 =  $this->db->get(db_prefix() . 'accountledger')->result_array();

      

      // Opening Balance

      $this->db->select('SUM(tblaccountbalances.BAL1) AS OpnTotal,tblclients.SubActGroupID');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountbalances.AccountID AND tblclients.PlantID = tblaccountbalances.PlantID');

      $this->db->where(db_prefix() . 'accountbalances.FY', $fy);

      $this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);

      $this->db->where_in(db_prefix() . 'clients.SubActGroupID1', '100030');

      $this->db->group_by('tblclients.SubActGroupID');

      $OpnActGroupWise2 =  $this->db->get(db_prefix() . 'accountbalances')->result_array();

      

      //return $CRDRGroupWise;

      $this->db->select('tblaccountgroupssub1.SubActGroupID1,tblaccountgroupssub1.SubActGroupName AS SubActGroupName1');

      $this->db->where_in(db_prefix() . 'accountgroupssub1.SubActGroupID1', '100030');

      $FinCostGroup1 =  $this->db->get(db_prefix() . 'accountgroupssub1')->result_array();

      $ActSubGroup1ID = array();

      

      foreach($FinCostGroup1 as $key=>$val){

        array_push($ActSubGroup1ID,$val["SubActGroupID1"]);

      }

      //return $OtherIncomeGroup1;

      

      $this->db->select('tblaccountgroupssub.SubActGroupID,tblaccountgroupssub.SubActGroupName,tblaccountgroupssub.SubActGroupID1');

      $this->db->where_in(db_prefix() . 'accountgroupssub.SubActGroupID1', $ActSubGroup1ID);

      $FinCostGroup2 =  $this->db->get(db_prefix() . 'accountgroupssub')->result_array();

      $i = 0;

      foreach($FinCostGroup1 as $key=>$val){

        $subGrp2Array = array();

        $Group1Opn = 0;

          $Group1Cr = 0;

          $Group1Dr = 0;

        foreach($FinCostGroup2 as $key1=>$val1){

          //$newArray = array();

          if($val1["SubActGroupID1"]==$val["SubActGroupID1"]){

            $data = array(

                      "ActSubGroupID2"=>$val1["SubActGroupID"],

                      "ActSubGroupName2"=>$val1["SubActGroupName"]

            );

            $CR = 0;

            $DR = 0;

            $Opn = 0;

            foreach($CRDRActGroupWise2 as $lKey=>$Lval){

              if($Lval["SubActGroupID"]==$val1["SubActGroupID"] && $Lval["TType"] == "C"){

                $CR += $Lval["SumAmt"];

              }else if($Lval["SubActGroupID"]==$val1["SubActGroupID"] && $Lval["TType"] == "D"){

                $DR += $Lval["SumAmt"];

              }

            }

            foreach($OpnActGroupWise2 as $OKey=>$Oval){

              if($Oval["SubActGroupID"]==$val1["SubActGroupID"]){

                $Opn += $Oval["OpnTotal"];

              }

            }

            $bal =  $Opn + $DR - $CR;

            $data['Balance'] = $bal;

            $data['CR'] = $CR;

            $data['DR'] = $DR;

            $data['Opn'] = $Opn;

            $Group1Opn += $Opn;

            $Group1Cr += $CR;

            $Group1Dr += $DR;

            array_push($subGrp2Array,$data);

          }

        }

        $FinCostGroup1[$i]['SubGroup2'] = $subGrp2Array;

        $FinCostGroup1[$i]['Opn'] = $Group1Opn;

          $FinCostGroup1[$i]['CR'] = $Group1Cr;

          $FinCostGroup1[$i]['DR'] = $Group1Dr;

        $i++;

      }

      return $FinCostGroup1;

    }

    public function GetOtherExpensesData()

    {

      $fy = $this->session->userdata('finacial_year');

      $selected_company = $this->session->userdata('root_company');

      $last_fy = $fy - 1;

      $year = array($fy,$last_fy);

      // Get All Indirect Expenses Group1

      

      // 100026 = Depreciation

      // 100030 = Finance Cost

      // 100015 - Salaries & Staff welfare

      $ActSubGroup1 = array("100026","100030","100015");

      $this->db->select('tblaccountgroupssub1.SubActGroupID1');

      $this->db->where('tblaccountgroupssub1.ActGroupID', '10018');

      $this->db->where_not_in(db_prefix() . 'accountgroupssub1.SubActGroupID1', $ActSubGroup1);

      $GetActGroup1 =  $this->db->get(db_prefix() . 'accountgroupssub1')->result_array();

      $IndExpActSubGroup1 = array();

      foreach($GetActGroup1 as $key=>$val){

        array_push($IndExpActSubGroup1,$val["SubActGroupID1"]);

      }

      

      $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID');

      $this->db->where_in(db_prefix() . 'accountledger.FY', $year);

      $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);

      $this->db->where_in(db_prefix() . 'clients.SubActGroupID1', $IndExpActSubGroup1);

      $this->db->group_by('tblaccountledger.TType,tblaccountledger.FY');

      $IndirectExpTrans =  $this->db->get(db_prefix() . 'accountledger')->result_array();

      $cr = 0;

      $dr = 0;

      $bal = 0;

      $crPre = 0;

      $drPre = 0;

      $balPre = 0;

      foreach($IndirectExpTrans as $IndExpkey=>$IndExpval){

        if($IndExpval["TType"] == "C" && $IndExpval["FY"] == $fy){

          $cr = $IndExpval["SumAmt"];

        }

        if($IndExpval["TType"] == "D" && $IndExpval["FY"] == $fy){

          $dr = $IndExpval["SumAmt"];

        }

        

        if($IndExpval["TType"] == "C" && $IndExpval["FY"] == $last_fy){

          $crPre = $IndExpval["SumAmt"];

        }

        if($IndExpval["TType"] == "D" && $IndExpval["FY"] == $last_fy){

          $drPre = $IndExpval["SumAmt"];

        }

      }

      $bal = $dr - $cr;

      $balPre = $drPre - $crPre;

      $IndDirectExp->CurrentYear = $bal;

      $IndDirectExp->PriviousYear = $balPre;

      return $IndDirectExp;

    }

    public function GetIndirectExpSubgroup2Wise()

    {

      // Get All Indirect Expenses Group1

      // 100026 = Depreciation

      // 100030 = Finance Cost

      // 100015 - Salaries & Staff welfare

      $ActSubGroup1 = array("100026","100030","100015");

      

      $fy = $this->session->userdata('finacial_year');

      $selected_company = $this->session->userdata('root_company');

      // Ledger Entry

      $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblclients.SubActGroupID');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID');

      $this->db->where(db_prefix() . 'accountledger.FY', $fy);

      $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);

      $this->db->where_in(db_prefix() . 'clients.ActGroupID', '10018');

      $this->db->group_by('tblaccountledger.TType,tblclients.SubActGroupID');

      $CRDRActGroupWise2 =  $this->db->get(db_prefix() . 'accountledger')->result_array();

      // Opening Balance

      $this->db->select('SUM(tblaccountbalances.BAL1) AS SumAmt,tblclients.SubActGroupID');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountbalances.AccountID AND tblclients.PlantID = tblaccountbalances.PlantID');

      $this->db->where(db_prefix() . 'accountbalances.FY', $fy);

      $this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);

      $this->db->where_in(db_prefix() . 'clients.ActGroupID', '10018');

      $this->db->group_by('tblclients.SubActGroupID');

      $OpnActGroupWise2 =  $this->db->get(db_prefix() . 'accountbalances')->result_array();

      

      $this->db->select('tblaccountgroupssub1.SubActGroupID1,tblaccountgroupssub1.SubActGroupName AS SubActGroupName1');

      $this->db->where_in(db_prefix() . 'accountgroupssub1.ActGroupID', '10018');

      $this->db->where_not_in(db_prefix() . 'accountgroupssub1.SubActGroupID1', $ActSubGroup1);

      $IndiectExpGroup1 =  $this->db->get(db_prefix() . 'accountgroupssub1')->result_array();

      $ActSubGroup1ID = array();

      

      foreach($IndiectExpGroup1 as $key=>$val){

        array_push($ActSubGroup1ID,$val["SubActGroupID1"]);

      }

      //return $OtherIncomeGroup1;

      

      $this->db->select('tblaccountgroupssub.SubActGroupID,tblaccountgroupssub.SubActGroupName,tblaccountgroupssub.SubActGroupID1');

      $this->db->where_in(db_prefix() . 'accountgroupssub.SubActGroupID1', $ActSubGroup1ID);

      $IndirectExpGroup2 =  $this->db->get(db_prefix() . 'accountgroupssub')->result_array();

      $i = 0;

      foreach($IndiectExpGroup1 as $key=>$val){

        $subGrp2Array = array();

        $Group1Opn = 0;

          $Group1Cr = 0;

          $Group1Dr = 0;

        foreach($IndirectExpGroup2 as $key1=>$val1){

          //$newArray = array();

          if($val1["SubActGroupID1"]==$val["SubActGroupID1"]){

            $data = array(

                          "ActSubGroupID2"=>$val1["SubActGroupID"],

                          "ActSubGroupName2"=>$val1["SubActGroupName"]

            );

            $Opn = 0;

            $CR = 0;

            $DR = 0;

            foreach($CRDRActGroupWise2 as $lKey=>$Lval){

              if($Lval["SubActGroupID"]==$val1["SubActGroupID"] && $Lval["TType"] == "C"){

                $CR += $Lval["SumAmt"];

              }else if($Lval["SubActGroupID"]==$val1["SubActGroupID"] && $Lval["TType"] == "D"){

                $DR += $Lval["SumAmt"];

              }

            }

            foreach($OpnActGroupWise2 as $OKey=>$Oval){

              if($Oval["SubActGroupID"]==$val1["SubActGroupID"]){

                $Opn += $Oval["OpnTotal"];

              }

            }

            $bal =  $Opn + $DR - $CR;

            $data['Balance'] = $bal;

            $data['CR'] = $CR;

            $data['DR'] = $DR;

            $data['Opn'] = $Opn;

            $Group1Opn += $Opn;

              $Group1Cr += $CR;

              $Group1Dr += $DR;

            array_push($subGrp2Array,$data);

          }

        }

        $IndiectExpGroup1[$i]['SubGroup2'] = $subGrp2Array;

        $IndiectExpGroup1[$i]['Opn'] = $Group1Opn;

          $IndiectExpGroup1[$i]['CR'] = $Group1Cr;

          $IndiectExpGroup1[$i]['DR'] = $Group1Dr;

        $i++;

      }

      return $IndiectExpGroup1;

    }

    public function GetDeprecData()

    {

      $fy = $this->session->userdata('finacial_year');

      $selected_company = $this->session->userdata('root_company');

      $last_fy = $fy - 1;

      $year = array($fy,$last_fy);

      // 100026 = Depreciation

      $ActSubGroup1 = array("100026");

      $this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID');

      $this->db->where_in(db_prefix() . 'accountledger.FY', $year);

      $this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);

      $this->db->where_in(db_prefix() . 'clients.SubActGroupID1', $ActSubGroup1);

      $this->db->group_by('tblaccountledger.TType,tblaccountledger.FY');

      $DeprTrans =  $this->db->get(db_prefix() . 'accountledger')->result_array();

      $cr = 0;

      $dr = 0;

      $bal = 0;

      $crPre = 0;

      $drPre = 0;

      $balPre = 0;

      foreach($DeprTrans as $Dkey=>$Dval){

        if($Dval["TType"] == "C" && $Dval["FY"] == $fy){

          $cr = $Dval["SumAmt"];

        }

        if($Dval["TType"] == "D" && $Dval["FY"] == $fy){

          $dr = $Dval["SumAmt"];

        }

        

        if($Dval["TType"] == "C" && $Dval["FY"] == $last_fy){

          $crPre = $Dval["SumAmt"];

        }

        if($Dval["TType"] == "D" && $Dval["FY"] == $last_fy){

          $drPre = $Dval["SumAmt"];

        }

      }

      $bal = $dr - $cr;

      $balPre = $drPre - $crPre;

      $DeprecData->CurrentYear = $bal;

      $DeprecData->PriviousYear = $balPre;

      return $DeprecData;

    }

    public function GetDeferredTax()

    {

      $fy = $this->session->userdata('finacial_year');

      $selected_company = $this->session->userdata('root_company');

      $last_fy = $fy - 1;

      $year = array($fy,$last_fy);

      $DefTaxGroup = array("1000065","1000058");

      $this->db->select('(tblaccountledger.Amount) AS SumPayments,tblaccountledger.FY');

      $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID');

      $this->db->where_in(db_prefix() . 'clients.SubActGroupID', $DefTaxGroup);

      $this->db->where_in(db_prefix() . 'accountledger.FY', $year);

      $this->db->where(db_prefix() . 'accountledger.PassedFrom', "PAYMENTS");

      $this->db->group_by(db_prefix() . 'accountledger.FY');

      $data =  $this->db->get(db_prefix() . 'accountledger')->row();

      $bal = 0;

      $balPre = 0;

      foreach($data as $key=>$val){

          if($val['FY'] == $fy){

              $bal += $val["SumPayments"];

          }elseif($val['FY'] == $last_fy){

              $balPre += $val["SumPayments"];

          }

      }

      $DeferredTax->CurrentYear = $bal;

      $DeferredTax->PriviousYear = $balPre;

      return $DeferredTax;

    }

//======================== Veryfied Code =======================================

    

}

