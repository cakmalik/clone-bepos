<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Outlet;
use Illuminate\Http\Request;
use App\Models\CashierMachine;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CashierMachineRequest;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class CashierMachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $machine = CashierMachine::with('outlet')
            ->when(auth()->user()->role->role_name !== 'SUPERADMIN', function ($query) {
                $query->whereIn('outlet_id', getUserOutlet());
            })
            ->latest()
            ->get();
    
        return view('pages.cashier_machine.index', compact('machine'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $outlets = Outlet::whereIn('id', getUserOutlet())->get();
        $editable = false;
        return view('pages.cashier_machine.create', compact('outlets', 'editable'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        DB::beginTransaction();

        $data = $request->validate([
            'outlet_id' => 'required',
            'name' => 'required'
        ], [], [
            'outlet_id' => 'Outlet',
            'name' => 'Nama Mesin Kasir'
        ]);
        $machine = CashierMachine::count();

        // if ($machine >= 1) {
        //     return redirect('cashier_machine')->withErrors('Mesin Kasir Penuh!');
        // }
        try {
            $data['code'] = cashierMachineCode();
            CashierMachine::create($data);
            DB::commit();
            return redirect('/cashier_machine')->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CashierMachineRequest $cashier_machine)
    {
        // return view('cash')
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(CashierMachine $cashier_machine)
    {
        $editable = true;
        $outlets = Outlet::all();
        return view('pages.cashier_machine.create', compact('cashier_machine', 'editable', 'outlets'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CashierMachine $cashier_machine)
    {
        DB::beginTransaction();

        $data = $request->validate([
            'outlet_id' => 'required',
            'name' => 'required'
        ], [], [
            'outlet_id' => 'Outlet',
            'name' => 'Nama Mesin Kasir'
        ]);

        try {
            CashierMachine::where('id', $cashier_machine->id)->update($data);
            DB::commit();
            return redirect('/cashier_machine')->withSuccess('Sukses di Update!');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withWarning('Gagal di Update!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            CashierMachine::destroy($id);
            DB::commit();
            return response()->json([
                'message' => 'Sukses di Hapus',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal di Hapus',
            ], 422);
        }
    }
}
