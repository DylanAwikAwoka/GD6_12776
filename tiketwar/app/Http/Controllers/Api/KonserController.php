<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KonserResource;
use App\Models\Konser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

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

    public function store(Request $request): JsonResponse
    {
        $konser = Konser::create($request->validate($this->aturan()));

        return (new KonserResource($konser))->response()->setStatusCode(201);
    }

    public function update(Request $request, Konser $konser): KonserResource
    {
        $konser->update($request->validate($this->aturan()));

        return new KonserResource($konser->load('promotor'));
    }

    public function destroy(Konser $konser): Response|JsonResponse
    {
        if ($konser->pesanans()->exists()) {
            return response()->json([
                'message' => 'Konser tidak dapat dihapus karena masih memiliki pesanan.',
            ], 409);
        }

        $konser->delete();

        return response()->noContent();
    }

    private function aturan(): array
    {
        return [
            'promotor_id' => ['required', 'exists:App\Models\Promotor,id'],
            'nama' => ['required', 'string', 'max:255'],
            'kota' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'integer', 'min:0'],
            'sisa' => ['required', 'integer', 'min:0'],
            'tanggal' => ['required', 'date'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }
}
