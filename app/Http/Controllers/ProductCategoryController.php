<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $dataProductCategories = ProductCategory::query()
            ->whereNull('parent_id') 
            ->with('children') 
            ->when($request->keyword, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->keyword}%");
            })
            ->orderBy('name') // Urutkan berdasarkan nama
            ->get(); // Ambil semua data yang sesuai
    
        return view('pages.product.product_category.index', compact('dataProductCategories'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'is_parent_category' => 'nullable',
            'minimum_margin' => 'nullable',
            'type_margin' => 'nullable',
            'desc' => 'nullable',
            'parent_id' => 'nullable'
        ]);


        try {
            DB::beginTransaction();

            $productCategory = new ProductCategory;
            $productCategory->outlet_id = getOutletActive()->id;
            $productCategory->code = productCategoryCode();
            $productCategory->name = $request->name;
            $productCategory->minimum_margin = $request->minimum_margin;
            $productCategory->type_margin = $request->type_margin;
            if ($request->has('is_parent_category') && $request->parent_category_id === null) {
                return redirect()->route('productCategory.index')->with('error', 'Sub kategori tidak boleh kosong');
            }
            if ($request->has('is_parent_category')) {
                $productCategory->is_parent_category = true;
                $productCategory->parent_id = null;
            } else {
                $productCategory->is_parent_category = false;
                $productCategory->parent_id = $request->parent_category_id;
            }
            $productCategory->slug = strtolower(str_replace(' ', '-', $request->name));
            $productCategory->desc = $request->desc;
            $productCategory->save();
            DB::commit();
            return redirect()->route('productCategory.index')->with('success', 'Product category created successfully');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('productCategory.index')->with('error', 'Product category created failed');
        }
    }

    public function destroy($id)
    {
        $productCategory = ProductCategory::find($id);
        if ($productCategory->children->count() > 0 || $productCategory->products->count() > 0) {
            return redirect()->route('productCategory.index')
                ->with('error', 'Tidak bisa di hapus, karena memiliki relasi dengan data lainnya');
        }

        $productCategory->delete();
        return redirect()->route('productCategory.index')->with('success', 'Product category deleted successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'is_parent_category' => 'nullable',
            'minimum_margin' => 'nullable',
            'type_margin' => 'nullable',
            'desc' => 'nullable',
            'parent_id' => 'nullable'
        ]);

        try {
            DB::beginTransaction();
            $productCategory = ProductCategory::find($id);
            $productCategory->outlet_id = getOutletActive()->id;
            $productCategory->name = $request->name;
            $productCategory->minimum_margin = $request->minimum_margin;
            $productCategory->type_margin = $request->type_margin;
            if ($request->is_parent_category == "on") {
                $productCategory->is_parent_category = 1;
                $productCategory->parent_id = null;
            } else {
                $productCategory->is_parent_category = 0;
                $productCategory->parent_id = $request->parent_id;
            }
            $productCategory->slug = strtolower(str_replace(' ', '-', $request->name));
            $productCategory->desc = $request->desc;
            $productCategory->save();
            ProductCategory::where('parent_id', $productCategory->id)
                ->update([
                    'minimum_margin' => $request->minimum_margin,
                    'type_margin' => $request->type_margin
                ]);
            DB::commit();
            return redirect()->route('productCategory.index')->with('success', 'Sub kategori updated successfully');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('productCategory.index')->with('error', 'Sub kategori updated failed');
        }
    }

    public function updateChild(Request $request)
    {
        $subCategoryId = $request->input('sub_category_id');
        $name = $request->input('name');

        // Lakukan validasi data yang diterima jika diperlukan
        $request->validate([
            'name' => 'required',
        ]);

        // Cari kategori anak berdasarkan ID
        $subcategory = ProductCategory::find($subCategoryId);

        if (!$subcategory) {
            // Jika kategori anak tidak ditemukan, kembalikan respons dengan status error
            return response()->json(['message' => 'Sub kategori tidak ditemukan'], 404);
        }

        // Update data kategori anak
        $subcategory->name = $name;
        $subcategory->save();

        // Kembalikan respons dengan status sukses
        return response()->json(['message' => 'Sub Kategori berhasil diperbarui'], 200);
    }

    public function deleteChild(Request $request)
    {
        $subCategoryId = $request->input('sub_category_id');

        // Cari kategori anak berdasarkan ID
        $subcategory = ProductCategory::find($subCategoryId);

        if (!$subcategory) {
            // Jika kategori anak tidak ditemukan, kembalikan respons dengan status error
            return response()->json(['message' => 'Sub kategori tidak ditemukan'], 404);
        }

        //cek dulu apakah punya data products atau tidak, jika punya jangan dihapus
        if ($subcategory->products->count() > 0) {
            return response()->json([
                'message' => 'Sub kategori tidak bisa dihapus karena memiliki relasi dengan data lainnya'
            ], 404);
        }

        // Hapus kategori anak
        $subcategory->delete();

        // Kembalikan respons dengan status sukses
        return response()->json(['message' => 'Sub kategori berhasil dihapus'], 200);
    }

    public function storeChild(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $productCategory = new ProductCategory;
            $productCategory->outlet_id = getOutletActive()->id;
            $productCategory->code = productSubCategoryCode();
            $productCategory->name = $request->name;
            $productCategory->is_parent_category = false;
            $productCategory->parent_id = $request->parent_id;
            $productCategory->slug = strtolower(str_replace(' ', '-', $request->name));
            $productCategory->desc = $request->desc;
            $productCategory->save();
            DB::commit();
            return redirect()->route('productCategory.index')->with('success', 'Sub kategori created successfully');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('productCategory.index')->with('error', 'Sub kategori created failed');
        }
    }
    public function getSubCategories(Request $request)
    {
        $categoryId = $request->input('category_id');
        // Dapatkan subkategori berdasarkan kategori yang dipilih
        $subCategories = ProductCategory::where('parent_id', $categoryId)->get();

        return response()->json([
            'subCategories' => $subCategories
        ]);
    }
}
