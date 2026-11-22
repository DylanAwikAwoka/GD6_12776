<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KonserResource;
use App\Models\Konser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KonserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $daftarKonser = Konser::with('promotor')
            ->when($request->query('kota'), fn ($query, $kota) => $query->where('kota', $kota))
            ->orderBy('tanggal')
            ->get();

        return KonserResource::collection($daftarKonser);
    }

    public function show(Konser $konser): KonserResource
    {
        return new KonserResource($konser->load('promotor'));
    }
}
