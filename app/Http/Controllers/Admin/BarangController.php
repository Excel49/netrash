<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\KategoriSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Barang::with('kategori');
        
        // Filter search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        
        // Filter kategori
        if ($request->has('kategori_id') && $request->kategori_id != '') {
            $query->where('kategori_id', $request->kategori_id);
        }
        
        // Filter status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Stats
        $totalBarang = Barang::count();
        $totalStok = Barang::sum('stok');
        $barangAktif = Barang::where('status', true)->count();
        $totalNilaiPoin = Barang::sum(DB::raw('harga_poin * stok'));
        
        // Get all categories for filter dropdown
        $kategoris = KategoriSampah::all();
        
        $barang = $query->orderBy('created_at', 'desc')->paginate(10);
        
       return view('admin.barang.index', compact(
            'barang', 
            'totalBarang', 
            'totalStok',
            'barangAktif', 
            'totalNilaiPoin',
            'kategoris'
        ));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoris = KategoriSampah::all();
        return view('admin.barang.create', compact('kategoris'));
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga_poin' => 'required|integer|min:100',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'boolean',
        ]);
        
        // Handle gambar upload
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $path = 'barang/' . $imageName;
            
            // Simpan ke storage
            Storage::disk('public')->put($path, file_get_contents($image));
            
            $validated['gambar'] = $imageName;
        }
        
        Barang::create($validated);
        
        return redirect()->route('admin.barang.index')
            ->with('success', 'Barang berhasil ditambahkan!');
    }
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $barang = Barang::with('kategori')->findOrFail($id);
        return view('admin.barang.show', compact('barang'));
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $kategoris = KategoriSampah::all();
        return view('admin.barang.edit', compact('barang', 'kategoris'));
    }
    
    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);
        
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga_poin' => 'required|integer|min:100',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'boolean',
        ]);
        
        // Handle gambar upload
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($barang->gambar) {
                $oldImagePath = 'barang/' . $barang->gambar;
                Storage::disk('public')->delete($oldImagePath);
            }
            
            // Simpan gambar baru
            $image = $request->file('gambar');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $path = 'barang/' . $imageName;
            
            // Simpan ke storage
            Storage::disk('public')->put($path, file_get_contents($image));
            
            $validated['gambar'] = $imageName;
        } else {
            // Jika tidak upload gambar baru, tetap pakai yang lama
            $validated['gambar'] = $barang->gambar;
        }
        
        $barang->update($validated);
        
        return redirect()->route('admin.barang.index')
            ->with('success', 'Barang berhasil diperbarui!');
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        
        // Hapus gambar jika ada
        if ($barang->gambar) {
            $imagePath = 'barang/' . $barang->gambar;
            Storage::disk('public')->delete($imagePath);
        }
        
        $barang->delete();
        
        return redirect()->route('admin.barang.index')
            ->with('success', 'Barang berhasil dihapus!');
    }
    
    /**
     * Toggle status barang
     */
    public function toggleStatus($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->update(['status' => !$barang->status]);
        
        return redirect()->route('admin.barang.index')
            ->with('success', 'Status barang berhasil diubah!');
    }
    
    /**
     * Show soft deleted items
     */
    public function trash()
    {
        $barang = Barang::onlyTrashed()->paginate(10);
        return view('admin.barang.trash', compact('barang'));
    }
    
    /**
     * Restore soft deleted item
     */
    public function restore($id)
    {
        $barang = Barang::onlyTrashed()->findOrFail($id);
        $barang->restore();
        
        return redirect()->route('admin.barang.trash')
            ->with('success', 'Barang berhasil dipulihkan!');
    }
    
    /**
     * Force delete item
     */
    public function forceDelete($id)
    {
        $barang = Barang::onlyTrashed()->findOrFail($id);
        
        // Hapus gambar jika ada
        if ($barang->gambar) {
            Storage::disk('public')->delete($barang->gambar);
        }
        
        $barang->forceDelete();
        
        return redirect()->route('admin.barang.trash')
            ->with('success', 'Barang berhasil dihapus permanen!');
    }
}