<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class RajaOngkirController extends Controller
{
    // Cari lokasi berdasarkan keyword
    public function getLocation(Request $request)
    {
        $keyword = $request->input('keyword');
    
        $response = Http::withHeaders([
            'x-api-key' => env('RAJAONGKIR_API_KEY_DELIVERY'),
            'Accept' => 'application/json'
        ])->get('https://api-sandbox.collaborator.komerce.id/tariff/api/v1/destination/search', [
            'keyword' => $keyword
        ]);
    
        Log::info('Location API Response', $response->json()); // Tambahkan log
    
        return response()->json($response->json());
    }
    

    // Hitung ongkir
    public function getCost(Request $request)
    {
        $origin = $request->input('origin');
        $destination = $request->input('destination');
        $weight = $request->input('weight');
        $courier = $request->input('courier');
      
    
        try {
            $response = Http::withHeaders([
                'key' => env('RAJAONGKIR_API_KEY_COST'),
                'Accept' => 'application/json'
            ])
            -> asForm()
            ->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
                
            ]);
    
            // Mengecek apakah respons berhasil
            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Respons API:', $responseData);
                return response()->json($responseData);
            } else {
                Log::error('Permintaan API gagal', ['response' => $response->body()]);
                return response()->json(['error' => 'Gagal menghitung biaya pengiriman'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception Permintaan API', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil biaya pengiriman'], 500);
        }
    }
    
}