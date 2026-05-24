<?php

namespace App\Http\Controllers\Api;

use App\Helpers\shalat\jadwalshalat;
use App\Http\Controllers\Controller;
use App\Models\Saldo;
use App\Models\Saldoreal;
use App\Models\Transaksi\BelanjaH;
use App\Models\Transaksi\Pembayaraniuran;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class JadwalShalatController extends Controller
{
    public function today()
    {


        $kota = '7a614fd06c325499f1680b9896beedeb';
        $timezone = 'Asia/Jakarta';

        $result = jadwalshalat::jadwalToday($kota, $timezone);
        return new JsonResponse($result);
    }

    public function saldo_real(){
        $prev = Carbon::now()->subMonth();

        $bulan = $prev->month;
        $tahun = $prev->year;

       $saldoawal = Saldo::whereMonth('tgltutup', $bulan)
        ->whereYear('tgltutup', $tahun)
        ->sum('nominal');

        $iuranwarga = Pembayaraniuran::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('nominal');

        $belanjawarga = BelanjaH::whereMonth('tgl', now()->month)
        ->whereYear('tgl', now()->year)->where('jenispembayaran', 'Cash')
        ->sum('totalbelanja');


        $saldo = $saldoawal + $iuranwarga - $belanjawarga;
        return new JsonResponse(
            [
                'saldo awal'=> $saldoawal,
                'iuranwarga'=> $iuranwarga,
                'belanja warga'=> $belanjawarga,
                'saldo'=> $saldo,
            ]
        );
    }
}
