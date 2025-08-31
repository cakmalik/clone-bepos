<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\ProfilCompany;
use Illuminate\Support\Facades\DB;

class SalesReturnReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $outlet = Outlet::all();
        return view('pages.report.sales-return.index', [
            'outlet' => $outlet,
        ]);
    }

    public function print(Request $request)
    {

        if($request->nota_type == 'SUMMARY') {
            $retur = $this->bySummary($request);
            $view   = 'pages.report.sales-return.srr-summary';

        } else {
            $retur = $this->byDetail($request);
            $view   = 'pages.report.sales-return.srr-detail';

        }

        $company = ProfilCompany::where('status','active')->first();

        $data = [
            'title'     => 'Data Return',
            'company'   => $company,
            'retur'     => $retur
        ];

        return view($view, $data);

    }

    private function bySummary($request)
    {
        return DB::table('sales as t')
            ->join('outlets as ot', 't.outlet_id', 'ot.id')
            ->select('t.*', 'ot.name as outlet_name')
            ->where('is_retur', TRUE)
            ->where(function($query) use($request) {
                return $this->queryWhere($query, $request);
            })
            ->get();
    }

    private function byDetail($request)
    {
        return DB::table('sales_details as td')
            ->join('sales as t', 'td.sales_id', 't.id')
            ->join('products as p', 'td.product_id', 'p.id')
            ->join('outlets as ot', 't.outlet_id', 'ot.id')
            ->select(
                't.ref_code', 
                't.sale_date', 
                't.sale_code', 
                'ot.name as outlet_name', 
                'p.code as product_code', 
                'td.product_name',
                'td.qty',
                DB::raw('td.qty * td.price as subtotal')
            )->where([['t.status', 'done'], ['td.qty', '>', 0], ['t.is_retur', TRUE]])
            ->where(function($query) use($request) {
                return $this->queryWhere($query, $request);
            })
            ->get();
    }

    private function queryWhere($query, $request)
    {
        if($request->start_date != '' && $request->end_date != '') {
            $query->where([
                ['t.sale_date', '>=', startOfDay($request->start_date)], ['sale_date', '<=', endOfDay($request->end_date)]]);
        }

        if($request->outlet != '' && $request->outlet != '-') {
            $query->where('t.outlet_id', $request->outlet);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
