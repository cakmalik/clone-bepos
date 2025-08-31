<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Outlet;
use App\Models\UserOutlet;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProfilCompany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OutletController extends Controller
{

    public $can_create = false;
    public function __construct()
    {
        $this->can_create = ProfilCompany::canCreateOutlet();
    }

    public function index()
    {
        $userRole = auth()->user()->role->role_name;

        if (in_array($userRole, ['SUPERADMIN', 'DEVELOPER'])) {
            $outlets = Outlet::paginate(5);
        } else {
            $currentOutletId = UserOutlet::where('user_id', auth()->user()->id)->first()->outlet_id;

            $outlets = Outlet::where('id', $currentOutletId)->paginate(5);
        }

        $can_create = $this->can_create;
        return view('pages.outlet.index', compact('outlets', 'can_create'));
    }




    public function create()
    {
        if ($this->can_create == false) {
            return redirect()->route('outlet.index')->with('error', 'Anda tidak dapat menambah outlet! ');
        }
        //autocode berdasarkan jumlah dari outlet_parent_id yang login
        $autoCode = 'OUT' . sprintf('%03s', Outlet::where('outlet_parent_id', UserOutlet::where('user_id', auth()->user()->id)->first()->outlet_id)->orWhere('id', UserOutlet::where('user_id', auth()->user()->id)->first()->outlet_id)->count() + 1);
        return view('pages.outlet.create', compact('autoCode'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'desc' => 'nullable',
            'footer_notes' => 'nullable',
        ], [], [
            'code' => 'Kode Outlet',
            'name' => 'Nama Outlet',
            'phone' => 'Nomor Telp',
            'address' => 'Almat',
            'desc' => 'Deskripsi',
            'footer_notes' => 'Catatan Kaki Struk',
        ]);

        $checkNamaOutlet = Outlet::where('name', $request->nama_outlet)->first();
        $code = Outlet::where('code', $request->code)->first();
        if ($checkNamaOutlet) {
            return redirect()->back()->withWarning('Nama Outlet Sudah Terpakai!');
        }
        if ($code) {
            return redirect()->back()->withWarning('Kode Outlet Sudah Terpakai!');
        }


        try {

            $outlet = new Outlet();
            $outlet->is_main = 0;
            $outlet->code = $request->code;
            $outlet->name = $request->name;
            $outlet->slug = Str::of($request->name)->slug('-');
            $outlet->address = $request->address;
            $outlet->phone = $request->phone;
            $outlet->desc = $request->desc;
            $outlet->footer_notes = $request->footer_notes;

            if ($request->image) {
                $namaGambar = time() . '.' . $request->image->getClientOriginalExtension();
                Storage::disk('public')->put('images/' . $namaGambar, file_get_contents($request->image));
                $outlet->outlet_image = $namaGambar;
            }
            $outlet->outlet_parent_id = getOutletActive()->id;
            $outlet->save();
            DB::commit();
            return redirect('/outlet')->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }


    public function show($id)
    {
        $outlet = Outlet::find($id);
        return view('pages.outlet.show', compact('outlet'));
    }

    public function edit($id)
    {
        $outlet = Outlet::find($id);
        return view('pages.outlet.edit', compact('outlet'));
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $outlet =  $request->validate([
            'code' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'desc' => 'nullable',
            'footer_notes' => 'nullable',
        ], [], [
            'code' => 'Kode Outlet',
            'name' => 'Nama Outlet',
            'phone' => 'Nomor Telp',
            'address' => 'Almat',
            'desc' => 'Deskripsi',
            'footer_notes' => 'Catatan Kaki Struk',
        ]);


        try {
            if ($request->image) {
                $namaGambar = time() . '.' . $request->image->getClientOriginalExtension();
                Storage::disk('public')->put('images/' . $namaGambar, file_get_contents($request->image));
                $outlet['outlet_image'] = $namaGambar;
            }


            Outlet::where('id', $id)->update($outlet);
            DB::commit();
            return redirect('/outlet')->withSuccess('Sukses di Update!');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->withWarning('Gagal di Update!');
        }
    }



    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $data = Outlet::findOrFail($id);

            if ($data->is_main == 1) {
                return response()->json([
                    'message' => 'Outlet yang aktif tidak bisa dihapus',
                ], 422);
            } elseif ($data->userOutlets) {
                return response()->json([
                    'message' => 'Outlet sudah memiliki pengguna tidak bisa dihapus!',
                ], 422);
            } else {
                $count = Outlet::count('id');

                if ($count < 2) {
                    return response()->json([
                        'message' => 'Tidak bisa dihapus, minimal 1 outlet tersedia!',
                    ], 422);
                }


                $data->delete();
                DB::commit();
                return response()->json([
                    'message' => 'Sukses dihapus',
                ], 200);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal di Hapus',
            ], 422);
        }
    }


    public function setting()
    {
        $outlets = Outlet::where('is_main', 0)->get();
        $cariOutletActive = Outlet::where('is_main', 1)->first();
        return view('pages.outlet.setting', compact('outlets', 'cariOutletActive'));
    }

    public function settingUpdate(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $outlet = Outlet::where('is_main', 1)->first();
            $outlet->is_main = 0;
            $outlet->save();

            $outlet = Outlet::find($request->outlet_id);
            $outlet->is_main = 1;
            $outlet->save();

            DB::commit();
            return redirect()->route('outlet.setting')->with('success', 'Outlet Active berhasil diubah');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('outlet.setting')->with('error', 'Outlet gagal diubah');
        }
    }
}
