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
             $imageContent = base64_decode($imageBase64);
             if (str_contains($imageBase64, ',')) {
                 [$header, $imageBase64] = explode(',', $imageBase64);
                }

              $fileName = Str::random(10) . '.jpg';
             $filePath = storage_path('app/temp/' . $fileName);

            if (!file_exists(storage_path('app/temp'))) {
                        mkdir(storage_path('app/temp'), 0755, true);
                    }

              file_put_contents($filePath, $imageContent);

                $apiUrl = "https://tutienda.com/api/images/products/{$productId}";
                $apiKey = 's4Z4CSJ4WN4PYMM4GKTCWGMJNYMSGRCGH';

                $response = Http::withBasicAuth($apiKey, '')
                    ->attach('image', file_get_contents($filePath), $fileName)
                    ->post($apiUrl);

                // Borrar el archivo temporal
                unlink($filePath);
            return response()->json([
                'status' => $response->successful(),
                'http_status' => $response->status(),
                'prestashop_body' => $response->body(),
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'response' => $e->getMessage()]);
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
