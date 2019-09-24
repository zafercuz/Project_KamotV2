<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserInfo;
use App\CheckInOut;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function index()
    {
        // $from = date('2019-05-06');
        $from = Carbon::createFromDate(2019,05,06)->format('Y-m-d');
        $to = Carbon::createFromDate(2019,05,20)->format('Y-m-d');
        // $to = date('2019-05-21');

        $logIn = UserInfo::SelectLogIn()->JoinCol()->HrisId(1)->where('checktime', '>=', $from)->where('checktime', '<=', $to)->TimeType('i')->OrderDate()->get();
        // $logOut = UserInfo::SelectLogOut()->JoinCol()->HrisId(1)->TimeType('o')->OrderDate()->take(10)->get();
        // dd($logIn);
        // $collection = collect([]);
        
        // for ($counter1 = 0, $counter2 = 0; ($counter1 < count($logIn)) && ($counter2 < count($logOut)); $counter1++, $counter2++) {
        //     $compareDate1 = explode(" ", $logIn[$counter1]->checktime);
        //     $compareDate2 = explode(" ", $logOut[$counter2]->checktime);

        //     if($compareDate1[0] == $compareDate2[0]){
        //         $this->pushCollection($collection, $logIn[$counter1]->userid, $compareDate1[0], $logIn[$counter1]->checktime,
        //             $logOut[$counter2]->checktime, $logIn[$counter1]->name);
        //     }
        //     elseif ($compareDate1[0] > $compareDate2[0]) { // EMPLOYEE DIDN'T LOG IN
        //         $this->pushCollection($collection, $logIn[$counter1]->userid, $compareDate2[0], "N/A",
        //             $logOut[$counter2]->checktime, $logIn[$counter1]->name);
        //         $counter1 -= 1;
        //     }
        //     elseif ($compareDate1[0] < $compareDate2[0]) { // EMPLOYEE DIDN'T LOG OUT
        //         $this->pushCollection($collection, $logIn[$counter1]->userid, $compareDate1[0], $logIn[$counter1]->checktime,
        //             "N/A", $logIn[$counter1]->name);
        //         $counter2 -= 1;
        //     }
        // }
        // dd($collection);

        return view('index');
    }

    public function SearchAjax(Request $request){
        $logIn = UserInfo::SelectLogIn()->JoinCol()->HrisId($request->filterInput)->where('checktime', '>=', $request->date1)
                ->where('checktime', '<=', $request->date2)->TimeType('i')->OrderDate()->get();
        // $logOut = UserInfo::SelectLogOut()->JoinCol()->HrisId($request->filterInput)->CompareDate($request->date1, $request->date2)->TimeType('o')->OrderDate()->get();
    
        return response()->json(['data' => $logIn]);
    }

    private function pushCollection($collection, $userid, $date, $logIn, $logOut, $name)
    {
        $collection->push([
            'userid' => $userid,
            'date' => $date,
            'logIn' => $logIn,
            'logOut' => $logOut,
            'name' => $name,
        ]);
    }

}
