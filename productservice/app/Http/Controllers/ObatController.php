<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obat;
use App\Http\Resources\obatResource;

class ObatController extends Controller
{
    public function index()
    {
        $obat = Obat::all();

        return new obatResource($obat, 'berhasil', 'List data obat');
    }

    public function show($id){
        $obat = Obat::find($id);

        if (!$obat) {
            return new obatResource(null, 'gagal', 'Data obat tidak ditemukan');
        }

        return new obatResource($obat, 'berhasil', 'Detail data obat');
    }

    public function store(Request $request){
        $obat = Obat::create($request->all());

        return new obatResource($obat, 'berhasil', 'Data obat berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $obat = Obat::find($id);

        if (!$obat) {
            return new obatResource(null, 'gagal', 'Data obat tidak ditemukan');
        }

        $obat->update($request->all());

        return new obatResource($obat, 'berhasil', 'Data obat berhasil diupdate');
    }
    public function destroy($id)
    {
        $obat = Obat::find($id);

        if (!$obat) {
            return new obatResource(null, 'gagal', 'Data obat tidak ditemukan');
        }

        $obat->delete();

        return new obatResource(null, 'berhasil', 'Data obat berhasil dihapus');
    }
    public function updatestok(Request $request, $id)
    {
        $obat = Obat::find($id);
        if(!$obat){
            return new obatResource(null, 'gaga', 'Data obat tidak ditemukan');
        }
        $obat->update(['stock'=> $request->stock]);
    }
    
}
