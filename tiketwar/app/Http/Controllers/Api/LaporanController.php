<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'jumlah_pesanan' => Pesanan::count(),
                'total_pendapatan' => (int) Pesanan::where('status', Pesanan::LUNAS)->sum('total'),
                'per_status' => Pesanan::select('status', DB::raw('count(*) as jumlah'))
                    ->groupBy('status')
                    ->pluck('jumlah', 'status'),
            ],
        ]);
    }
}
