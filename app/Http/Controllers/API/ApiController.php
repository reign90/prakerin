<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\customer;
use App\Models\barang_masuk;
use App\Models\barang_keluar;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function supplier()
    {
        $supplier = Supplier::all();
        return response()->json([
            'success' => true,
            'message' => 'Data Supplier',
            'data' => $supplier,
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customer()
    {
        $customer = Customer::all();
        return response()->json([
            'success' => true,
            'message' => 'Data Customer',
            'data' => $customer,
        ], 200);
    }

    public function barang_masuk()
    {
        $barangMasuk = DB::table('barang_masuks')
        ->join('suppliers', 'barang_masuks.id_supplier', '=', 'barang_masuk.id_supplier')
        ->join('barangs', 'barang_masuks.id_barang', '=', 'barang_masuk.id_barang')
        ->select('suppliers.nama_supplier as leveransir', 'barangs.nama_barang as barang', 'barang_masuks.jumlah_pemasukan', 'barang_masuks.tgl_masuk')
        ->get();
        return response()->json([
            'success' => true,
            'message' => 'Data Barang Masuk',
            'data' => $barangMasuk,
        ], 200);
    }

    public function barang_keluar()
    {
        $barangKeluar = DB::table('barang_keluars')
        ->join('customers', 'barang_keluars.id_customer', 'barang_keluar.id_customer')
        ->join('barangs', 'barang_keluars.id_barang', '=', 'barang_keluar.id_barang')
        ->select('customers.nama as pelanggan', 'barangs.nama_barang as barang', 'barang_keluars.jumlah_pengiriman', 'barang_keluars.harga_satuan', 'barang_keluars.tujuan', 'barang_keluars.tgl_pengirimam')
        ->get();
        return response()->json([
            'success' => true,
            'message' => 'Data Barang Keluar',
            'data' => $barangKeluar,
        ], 200);
    }
}