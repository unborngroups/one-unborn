<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Company;

class InvoiceReportController extends Controller
{
    public function stateReport(Request $request)
    {
        $months = [4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December',1=>'January',2=>'February',3=>'March'];
        $quarters = [1=>'Q1 (Apr-Jun)',2=>'Q2 (Jul-Sep)',3=>'Q3 (Oct-Dec)',4=>'Q4 (Jan-Mar)'];
        $year = $request->input('year', date('Y'));

        $states = Company::select('state')->distinct()->get();
        $stateData = [];
        foreach ($states as $stateObj) {
            $state = $stateObj->state;
            $monthly = [];
            foreach ($months as $num => $name) {
                $monthly[$name] = Invoice::stateMonth($state, $num, $year)->count();
            }
            $quarterly = [];
            foreach ($quarters as $q => $label) {
                $quarterly[$q] = Invoice::stateQuarter($state, $q, $year)->count();
            }
            $stateData[] = (object)[
                'name' => $state,
                'monthly_invoices' => $monthly,
                'quarterly_invoices' => $quarterly
            ];
        }
        return view('finance.invoices.state_report', [
            'states' => $stateData,
            'months' => array_values($months)
        ]);
    }
}
