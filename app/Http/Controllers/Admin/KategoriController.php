<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriSampah;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = KategoriSampah::all();
        return view('admin.kategori.index', compact('kategoris'));
    }
    
    public function create()
    {
        return view('admin.kategori.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|unique:kategori_sampah',
            'jenis_sampah' => 'required|in:organik,anorganik,berbahaya,daur_ulang,lainnya',
            'harga_per_kg' => 'required|numeric|min:0',
            'poin_per_kg' => 'required|numeric|min:0',
        ]);
        
        KategoriSampah::create([
            'nama_kategori' => $request->nama_kategori,
            'jenis_sampah' => $request->jenis_sampah,
            'harga_per_kg' => $request->harga_per_kg,
            'poin_per_kg' => $request->poin_per_kg,
            'deskripsi' => $request->deskripsi,
            'status' => 1,
        ]);
        
        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dibuat');
    }
    
    public function edit($id)
    {
        $kategori = KategoriSampah::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }
    
    public function update(Request $request, $id)
    {
        $kategori = KategoriSampah::findOrFail($id);
        
        $request->validate([
            'nama_kategori' => 'required|unique:kategori_sampah,nama_kategori,' . $id,
            'jenis_sampah' => 'required|in:organik,anorganik,berbahaya,daur_ulang,lainnya',
            'harga_per_kg' => 'required|numeric|min:0',
            'poin_per_kg' => 'required|numeric|min:0',
        ]);
        
        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'jenis_sampah' => $request->jenis_sampah,
            'harga_per_kg' => $request->harga_per_kg,
            'poin_per_kg' => $request->poin_per_kg,
            'deskripsi' => $request->deskripsi,
        ]);
        
        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }
    
    public function destroy($id)
    {
        $kategori = KategoriSampah::findOrFail($id);
        $kategori->delete();
        
        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}