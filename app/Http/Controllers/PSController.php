<?php

namespace App\Http\Controllers;

use App\Models\PS_PriceList;
use App\Models\PS_PriceProduct;
use App\Models\PS_Products;
use App\Models\PS_Stock;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Http\Request;

class PSController extends Controller
{
    public function getProducts()
    {
        try {
            $p = PS_Products::limit(10)->get();
            return response()->json(['status' => true, 'response' => $p]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'response' => $e->getMessage()]);
        }
    }


    public function getPriceProduct()
    {
        try {
            $price = PS_PriceProduct::all();
            return response()->json(['status' => true, 'response' => $price]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'response' => $e->getMessage()]);
        }
    }


    public function getPriceList()
    {
        try {
            $list = PS_PriceList::all();
            return response()->json(['status' => true, 'response' => $list]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'response' => $e->getMessage()]);
        }
    }


   public function productImg(Request $r)
{
    try {
        $productId = $r->product_id;
        $imageBase64 = $r->image_base64;

        // Quitar encabezado si existe
        if (str_contains($imageBase64, ',')) {
            [, $imageBase64] = explode(',', $imageBase64);
        }

        $imageContent = base64_decode($imageBase64);
        $fileName = Str::random(10) . '.jpg';

        $apiKey = '4Z4CSJ4WN4PYMM4GKTCWGMJNYMSGRCGH';
        $apiBaseUrl = "http://40.124.183.121/api";

        // 1. Verificamos si el producto ya tiene imágenes
        $imgCheck = Http::withBasicAuth($apiKey, '')
            ->get("{$apiBaseUrl}/images/products/{$productId}");

        if (!$imgCheck->successful()) {
            return response()->json(['status' => false, 'error' => 'Error al verificar imágenes.']);
        }

        // Convertir XML a SimpleXMLElement
        $xml = simplexml_load_string($imgCheck->body());

        // Obtener ID de imagen si existe
        $existingImageId = isset($xml->image['id']) ? (int) $xml->image['id'] : null;

        if ($existingImageId) {
            // 2. Si hay imagen, reemplazarla (PUT)
            $apiUrl = "{$apiBaseUrl}/images/products/{$productId}/{$existingImageId}?ps_method=PUT";
        } else {
            // 3. Si no hay imagen, crear nueva (POST)
            $apiUrl = "{$apiBaseUrl}/images/products/{$productId}";
        }

        // 4. Subir imagen (sea PUT o POST)
        $response = Http::withBasicAuth($apiKey, '')
            ->attach('image', $imageContent, $fileName)
            ->post($apiUrl); // usaremos POST en ambos casos, con ps_method=PUT si es reemplazo

        return response()->json([
            'status' => $response->successful(),
            'http_status' => $response->status(),
            'prestashop_body' => $response->body(),
            'action' => $existingImageId ? 'replaced' : 'created',
        ]);
    } catch (\Exception $e) {
        return response()->json(['status' => false, 'error' => $e->getMessage()]);
    }
}


    public function productStock(Request $r)
    {
      /*   return $r->all(); */
        try {
            $validated = $r->validate([
                'id_warehouse' => 'required|integer',
                'id_product' => 'required|integer',
                'id_product_attribute' => 'nullable|integer',
                'reference' => 'required|string|max:255',
                'physical_quantity' => 'required|integer',
                'usable_quantity' => 'required|integer',
                'price_te' => 'required|numeric'
            ]);

            $stock = new PS_Stock();

            $stock->fill($validated)->save();

            return response()->json(['status' => true, 'response' => $stock]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'response' => $e->getMessage()]);
        }
    }

    public function productPriceList(Request $r)
    {
        try {
            $validated = $r->validate([
                'id_currency' => 'required|integer',
                'name' => 'required|string|max:255',
            ]);

            $plist = new PS_PriceList();

            $plist->fill($validated)->save();

            return response()->json(['status' => true, 'response' => $plist]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'response' => $e->getMessage()]);
        }
    }

    public function productPrice(Request $r)
    {
        try {
            $validated = $r->validate([
                'id_pricelist' => 'required|integer',
                'id_product' => 'required|integer',
                'price' => 'required|integer',
            ]);

            $price = new PS_PriceProduct();

            $price->fill($validated)->save();

            return response()->json(['status' => true, 'response' => $price]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'response' => $e->getMessage()]);
        }
    }
}
