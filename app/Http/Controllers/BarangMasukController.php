<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Barang_masuk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use PDF;
use Session;

class BarangMasukController extends Controller
{

    public function index()
    {
        $supplier = Supplier::orderBy('nama_supplier')->get()
                               ->pluck('nama_supplier');

        $barang = Barang::orderBy('nama_barang')->get()
                               ->pluck('nama_barang');

        $barangMasuk = Barang_masuk::all();
        return view('barang-masuk.index', compact('barangMasuk', 'supplier', 'barang'));
    }

    public function laporanBarangMasukAll()
    {
        $supplier = Supplier::orderBy('nama_supplier')->get()
                               ->pluck('nama_supplier');

        $barang = Barang::orderBy('nama_barang')->get()
                               ->pluck('nama_barang')

        $barangMasuk = Barang_masuk::all();
        $no = 1;
        return view('barang-masuk.laporanBarangMasukAll', compact('barangMasuk', 'supplier', 'barang',
            'no'));
    }

    public function laporanBarangMasuk($id)
    {
        $supplier = Supplier::orderBy('nama_supplier')->get()
                               ->pluck('nama_supplier');

        $barang = Barang::orderBy('nama_barang')->get()
                               ->pluck('nama_barang')

        $barangMasuk = Barang_masuk::find($id);
        $no = 1;
        return view('barang-masuk.laporanBarangMasuk', compact('barangMasuk', 'supplier', 'barang', 'no'));
    }

    public function cetakPDF_all()
    {
        $supplier = Supplier::orderBy('nama_supplier')->get()
                               ->pluck('nama_supplier');

        $barang = Barang::orderBy('nama_barang')->get()
                               ->pluck('nama_barang')

        $barangMasuk = Barang_masuk::all();
        $no = 1;
        $pdf = PDF::loadview('barang-masuk.laporanBarangMasukAll', compact('barangMasuk', 'supplier', 'barang', 'no'));
        return $pdf->download('laporan-pemasukan-barang-semua.pdf');
    }

    public function cetakPDF($id)
    {
        $supplier = Supplier::orderBy('nama_supplier')->get()
                               ->pluck('nama_supplier');

        $barang = Barang::orderBy('nama_barang')->get()
                               ->pluck('nama_barang')

        $barangMasuk = Barang_masuk::findOrFail($id);
        $no = 1;
        $pdf = PDF::loadview('barang-masuk.laporanBarangMasukAll', compact('barangMasuk', 'supplier', 'barang', 'no'));
        return $pdf->download('laporan-pemasukan-barang-satuan.pdf');
    }

    public function ApiIn()
    {
        $barangKeluar = Barang_masuk::all();

        return Datatables::of($barangMasuk)
            ->addColumn('nama_barang', function ($barangMasuk){
                return $barangMasuk->barang->nama_barang;
            })
            ->addColumn('nama_supplier', function ($barangMasuk){
                return $barangMasuk->supplier->nama_supplier;
            })
            ->addColumn('action', function($barangMasuk){
                return '<a href="#" class="btn btn-info"><i class="glyphicon glyphicon-eye-open"></i> Show</a> ' .
                    '<a onclick="editForm('. $barangMasuk->id .')" class="btn btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
                    '<a onclick="deleteData('. $barangMasuk->id .')" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
            })
            ->rawColumns(['nama_barang', 'nama_supplier', 'action'])->make(true);

          return view('barang-masuk.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_supplier' => 'required',
            'id_barang' => 'required',
            'jumlah' => 'required',
            'tgl_masuk' => 'required',
        ]);

        $masuk = new Barang_masuk();
        $masuk->id_supplier = $request->id_supplier;
        $masuk->id_barang = $request->id_barang;
        $masuk->jumlah_pemasukan = $request->jumlah;
        $masuk->tgl_masuk = $request->tgl_masuk;
        $masuk->save();

        $barang = Barang::findOrFail($request->id_barang);
        $barang->stok_barang += $request->jumlah;
        $barang->save();

        return redirect('barang-masuk')->withSuccess('Barang diterima!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_supplier' => 'required',
            'id_barang' => 'required',
            'jumlah' => 'required',
            'tgl_masuk' => 'required',
        ]);

        $barangMasuk = Barang_masuk::findOrFail($id);
        $reset = Barang::findOrFail($request->id_barang);
        $reset['stok_barang'] -= $barangMasuk['jumlah_pemasukan'];
        $reset->save();
        
        $barangMasuk->jumlah_pemasukan = $request->jumlah;
        $barangMasuk->tgl_masuk = $request->tgl_masuk;
        $barangMasuk->update();

        $barang = Barang::find($request->id_barang);
        $barang['stok_barang'] += $request->jumlah;
        $barang->update();

        return redirect('barang-masuk')->withInfo('Data telah diubah!');

    }

    public function destroy($id)
    {
        $barangMasuk = Barang_masuk::find($id);
        $barang = Barang::where('id', $barangMasuk->id_barang)->firstOrFail();
        $barang->stok_barang -= $barangKeluar->jumlah_pemasukan;
        $barang->save();

        if (!Barang_masuk::destroy($id)) {
            return redirect()->back();
        }
        Session::flash("flash_notification", [
            "level" => "success",
            "message" => "Data berhasil dihapus",
        ]);
        return redirect('barang-masuk')->withSuccess('Data telah dihapus');
    }
}
