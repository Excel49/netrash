<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriSampah;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Query untuk semua kategori (pagination)
        $kategoris = KategoriSampah::query()
            ->when($search, function ($query, $search) {
                return $query->where('nama_kategori', 'like', "%{$search}%")
                            ->orWhere('jenis_sampah', 'like', "%{$search}%");
            })
            ->orderBy('is_locked', 'desc') // Locked dulu
            ->orderBy('nama_kategori')
            ->paginate(10);
        
        // Ambil kategori utama (locked) untuk bagian khusus
        $kategorisUtama = KategoriSampah::where('is_locked', true)
            ->orderBy('nama_kategori')
            ->get();
        
        // Ambil kategori biasa (unlocked) untuk bagian lain
        $kategorisBiasa = KategoriSampah::where('is_locked', false)
            ->when($search, function ($query, $search) {
                return $query->where('nama_kategori', 'like', "%{$search}%")
                            ->orWhere('jenis_sampah', 'like', "%{$search}%");
            })
            ->orderBy('nama_kategori')
            ->get();
        
        return view('admin.kategori.index', compact('kategoris', 'kategorisUtama', 'kategorisBiasa', 'search'));
    }
    
    public function create()
    {
        $jenisSampah = [
            'organik' => 'Organik',
            'anorganik' => 'Anorganik', 
            'b3' => 'B3 (Bahan Berbahaya)',
            'campuran' => 'Campuran',
        ];
        
        return view('admin.kategori.create', compact('jenisSampah'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|unique:kategori_sampah|max:100',
            'jenis_sampah' => 'required|in:organik,anorganik,b3,campuran',
            'poin_per_kg' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|max:500',
            'warna_label' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);
        
        KategoriSampah::create([
            'nama_kategori' => $request->nama_kategori,
            'jenis_sampah' => $request->jenis_sampah,
            'poin_per_kg' => $request->poin_per_kg,
            'deskripsi' => $request->deskripsi,
            'warna_label' => $request->warna_label ?? '#3b82f6',
            'status' => $request->has('status'),
            'is_locked' => false,
        ]);
        
        return redirect()->route('admin.kategori.index')
            ->with('success', 'Item spesifik berhasil ditambahkan!');
    }
    
    public function edit($id)
    {
        $kategori = KategoriSampah::findOrFail($id);
        
        if ($kategori->is_locked) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori utama tidak dapat diedit');
        }
        
        $jenisSampah = [
            'organik' => 'Organik',
            'anorganik' => 'Anorganik', 
            'b3' => 'B3 (Bahan Berbahaya)',
            'campuran' => 'Campuran',
        ];
        
        return view('admin.kategori.edit', compact('kategori', 'jenisSampah'));
    }
    
    public function update(Request $request, $id)
    {
        $kategori = KategoriSampah::findOrFail($id);
        
        if ($kategori->is_locked) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori utama tidak dapat diubah');
        }
        
        $request->validate([
            'nama_kategori' => 'required|unique:kategori_sampah,nama_kategori,' . $id . '|max:100',
            'jenis_sampah' => 'required|in:organik,anorganik,b3,campuran',
            'poin_per_kg' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|max:500',
            'warna_label' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);
        
        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'jenis_sampah' => $request->jenis_sampah,
            'poin_per_kg' => $request->poin_per_kg,
            'deskripsi' => $request->deskripsi,
            'warna_label' => $request->warna_label ?? '#3b82f6',
            'status' => $request->has('status'),
        ]);
        
        return redirect()->route('admin.kategori.index')
            ->with('success', 'Item spesifik berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kategori = KategoriSampah::findOrFail($id);
        
        if ($kategori->is_locked) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori utama tidak dapat dihapus');
        }
        
        // Cek apakah kategori digunakan dalam transaksi
        if ($kategori->detailTransaksi()->exists()) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena sudah digunakan dalam transaksi');
        }
        
        // HAPUS PERMANEN (Force Delete)
        $kategori->forceDelete();
        
        return redirect()->route('admin.kategori.index')
            ->with('success', 'Item spesifik berhasil dihapus permanen!');
    }
    
    public function toggleStatus($id)
    {
        $kategori = KategoriSampah::findOrFail($id);
        
        if ($kategori->is_locked) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Status kategori utama tidak dapat diubah');
        }
        
        $kategori->update([
            'status' => !$kategori->status
        ]);
        
        return redirect()->route('admin.kategori.index')
            ->with('success', 'Status kategori berhasil diubah!');
    }
    
    // Method tambahan untuk import/export/trash (bisa ditambahkan nanti)
    public function import(Request $request)
    {
        // Implementasi import Excel
        return redirect()->route('admin.kategori.index')
            ->with('success', 'Import berhasil!');
    }
    
    
    public function export()
    {
        // Implementasi export Excel
        return redirect()->route('admin.kategori.index')
            ->with('success', 'Export berhasil!');
    }
    
    public function trash()
    {
        $kategoris = KategoriSampah::onlyTrashed()->paginate(10);
        return view('admin.kategori.trash', compact('kategoris'));
    }
    
    public function restore($id)
    {
        $kategori = KategoriSampah::onlyTrashed()->findOrFail($id);
        $kategori->restore();
        
        return redirect()->route('admin.kategori.trash')
            ->with('success', 'Kategori berhasil dipulihkan!');
    }
    
    public function forceDelete($id)
    {
        $kategori = KategoriSampah::onlyTrashed()->findOrFail($id);
        $kategori->forceDelete();
        
        return redirect()->route('admin.kategori.trash')
            ->with('success', 'Kategori berhasil dihapus permanen!');
    }
}