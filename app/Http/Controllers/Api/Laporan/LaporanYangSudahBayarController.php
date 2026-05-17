<?php

namespace App\Http\Controllers\Api\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Saldo;
use App\Models\Transaksi\BelanjaH;
use App\Models\Transaksi\Pembayaraniuran;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanYangSudahBayarController extends Controller
{
    public function index()
    {
        $bulan = request('bulan');
        $tahun = request('tahun');
        // $data = Pembayaraniuran::select('iuran.*','users.name as nama')
        //     ->join('users', 'users.id', '=', 'iuran.warga_id')
        //     ->where('iuran.bulan', $bulan)
        //     ->where('iuran.tahun', $tahun)
        //     ->get();
        $data = User::select(
                    'users.id as idusers',
                    'users.name as nama',
                    'users.nokk as nokk',
                    'iuran.*'
                )
                ->leftJoin('iuran', function ($join) use ($bulan, $tahun) {
                    $join->on('users.id', '=', 'iuran.warga_id')
                        ->where('iuran.bulan', $bulan)
                        ->where('iuran.tahun', $tahun);
                })
                ->where('users.id','<>', 26)
                ->orderBy('users.nokk')
                ->get();

        return response()->json([
            'data' => $data,
        ]);
    }

    public function indexkas()
    {
        $bulan = request('bulan');
        $tahun = request('tahun');
        $bulanawal = $bulan - 1;
        if ($bulanawal == 0) {
            $bulanawal = 12;
            $tahunawal = $tahun - 1;
        } else {
            $tahunawal = $tahun;
        }
        $saldoawal = Saldo::whereMonth('tgltutup', $bulanawal)
            ->whereYear('tgltutup', $tahunawal)
            ->first();
        $masuk = Pembayaraniuran::select('iuran.*','users.name as nama')
            ->join('users', 'users.id', '=', 'iuran.warga_id')
            ->whereMonth('iuran.created_at', $bulan)
            ->whereYear('iuran.created_at', $tahun)
            ->get();
        $keluar = BelanjaH::with(
            [
                'rincian' => function ($q) {
                    $q->orderBy('id', 'desc');
                }
            ]
        )
            ->where('jenispembayaran', '!=', 'Hutang')
            ->whereMonth('tgl', $bulan)
            ->whereYear('tgl', $tahun)
            ->get();
        return new JsonResponse([
            'saldoawal' => $saldoawal,
            'masuk' => $masuk,
            'keluar' => $keluar,
        ]);
    }

    public function gethistorypembayaran()
    {
        $user = Auth::user();
        $tahun = request('tahun');
        $data = Pembayaraniuran::select('iuran.*','users.name as nama')
            ->join('users', 'users.id', '=', 'iuran.warga_id')
            ->where('warga_id', $user->id)
            ->where('iuran.tahun', $tahun)
            ->get();
        return new JsonResponse([
            'data' => $data,
        ]);
    }
}
