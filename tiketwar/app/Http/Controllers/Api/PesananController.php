<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PesananResource;
use App\Models\Konser;
use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PesananController extends Controller
{
    public function index()
    {
        return PesananResource::collection(Pesanan::latest()->get());
    }

    public function bayar(Pesanan $pesanan): PesananResource|JsonResponse
    {
        return DB::transaction(function () use ($pesanan) {
            $terkunci = Pesanan::lockForUpdate()->findOrFail($pesanan->id);

            if ($terkunci->status !== Pesanan::MENUNGGU_BAYAR) {
                return response()->json([
                    'message' => 'Hanya pesanan berstatus menunggu_bayar yang bisa dibayar.',
                ], 409);
            }

            $terkunci->update(['status' => Pesanan::LUNAS]);

            return new PesananResource($terkunci);
        });
    }

    public function batal(Pesanan $pesanan): PesananResource|JsonResponse
    {
        return DB::transaction(function () use ($pesanan) {
            $terkunci = Pesanan::lockForUpdate()->findOrFail($pesanan->id);

            if ($terkunci->status !== Pesanan::MENUNGGU_BAYAR) {
                return response()->json([
                    'message' => 'Hanya pesanan berstatus menunggu_bayar yang bisa dibatalkan.',
                ], 409);
            }

            $konser = Konser::lockForUpdate()->findOrFail($terkunci->konser_id);
            $konser->increment('sisa', $terkunci->jumlah);
            $terkunci->update(['status' => Pesanan::BATAL]);

            return new PesananResource($terkunci);
        });
    }

    public function store(Request $request, Konser $konser): JsonResponse
    {
        $data = $request->validate([
            'nama_pemesan' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $pesanan = DB::transaction(function () use ($konser, $data) {
            // Baca ulang konser sambil mengunci barisnya (sisa harus yang terbaru).
            $terkunci = Konser::lockForUpdate()->findOrFail($konser->id);

            if ($data['jumlah'] > $terkunci->sisa) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Sisa tiket hanya ' . $terkunci->sisa . '.',
                ]);
            }

            // Harga dan total selalu dihitung server, bukan diambil dari body.
            $pesanan = Pesanan::create([
                'konser_id' => $terkunci->id,
                'nama_pemesan' => $data['nama_pemesan'],
                'email' => $data['email'],
                'jumlah' => $data['jumlah'],
                'harga_satuan' => $terkunci->harga,
                'total' => $terkunci->harga * $data['jumlah'],
                'status' => Pesanan::MENUNGGU_BAYAR,
            ]);

            $terkunci->decrement('sisa', $data['jumlah']);

            return $pesanan;
        });

        return (new PesananResource($pesanan))
            ->response()
            ->setStatusCode(201);
    }
}
