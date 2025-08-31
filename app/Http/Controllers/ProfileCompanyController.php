<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfilCompany;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Profiler\Profile;

class ProfileCompanyController extends Controller
{
    public function index()
    {
        $dataProfilCompanyActive = ProfilCompany::first();
        $dataAllProfilCompany = ProfilCompany::paginate(5);


        return view('pages.profile_company.index', compact('dataProfilCompanyActive', 'dataAllProfilCompany'));
    }

    public function create()
    {
        return view('pages.profile_company.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'telp' => 'required',
            'email' => 'required|email',
            'about' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:5048',
        ]);

        try {

            $data = [
                'name' => $request->name,
                'address' => $request->address,
                'telp' => $request->telp,
                'email' => $request->email,
                'about' => $request->about,
                'image' => $request->image->store('logocompany'),
                'status' => 'inactive',
            ];

            ProfilCompany::create($data);

            return redirect()->route('profileCompany.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('profileCompany.index')->with('error', 'Data gagal ditambahkan', $th->getMessage());
        }
    }

    public function edit($id)
    {
        $dataPC = ProfilCompany::findOrFail($id);

        return view('pages.profile_company.edit', compact('dataPC'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'telp' => 'required',
            'email' => 'required|email',
            'about' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:5048',
            // 'status' => 'required',
        ]);

        // $dataStatusAktif = ProfilCompany::where('status', 'active')->first();

        try {
            $profileCompany = ProfilCompany::findOrFail($id);
            $profileCompany->name = $request->name;
            $profileCompany->address = $request->address;
            $profileCompany->telp = $request->telp;
            $profileCompany->email = $request->email;
            $profileCompany->about = $request->about;

            if ($gambar = $request->file('image')) {
                $namaGambar = time() . '.' . $gambar->getClientOriginalExtension();
                Storage::disk('public')->put('images/' . $namaGambar, file_get_contents($gambar));
                $profileCompany->image = $namaGambar;
            }

            // $profileCompany->status = $request->status;
            $profileCompany->save();

            return redirect()->route('profileCompany.index')->with('success', 'Data berhasil diubah');
        } catch (Exception $th) {
            return redirect()->route('profileCompany.index')->with('error', 'Data gagal diubah', $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $data = ProfilCompany::findOrFail($id);

            if ($data->status == 'active') {
                return redirect()->route('profileCompany.index')->with('error', 'Data gagal dihapus, status aktif tidak bisa dihapus');
            }

            Storage::delete($data->image);
            $data->delete();

            return redirect()->route('profileCompany.index')->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            return redirect()->route('profileCompany.index')->with('error', 'Data gagal dihapus', $th->getMessage());
        }
    }
}
