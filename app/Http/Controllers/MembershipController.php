<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::all();
        return view('pages.membership.index', compact('memberships'));
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'score_min' => 'required|numeric',
                'score_max' => 'required|numeric|gt:score_min',
                'score_loyalty' => 'required|string',
            ]);
            $membership = Membership::create([
                'name' => $validatedData['name'],
                'score_min' => $validatedData['score_min'],
                'score_max' => $validatedData['score_max'],
                'score_loyalty' => $validatedData['score_loyalty'],
            ]);
            return redirect()->route('membership.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
    public function editModal(Membership $membership)
    {
        return view('pages.membership.editmodals', compact('membership'));
    }
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'score_min' => 'required|numeric',
                'score_max' => 'required|numeric|gt:score_min',
                'score_loyalty' => 'required|string',
            ]);

            $membership = Membership::findOrFail($id);

            $membership->update([
                'name' => $validatedData['name'],
                'score_min' => $validatedData['score_min'],
                'score_max' => $validatedData['score_max'],
                'score_loyalty' => $validatedData['score_loyalty'],
            ]);

            return redirect()->route('membership.index')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
    public function destroy($id)
    {
        try {
            $membership = Membership::findOrFail($id);
            $membership->delete();
            return redirect()->route('membership.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('membership.index')->with('error', 'Data gagal dihapus');
        }
    }
}
