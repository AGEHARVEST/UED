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
        $fileName = $r->nombre_img;

        // Detectar y quitar encabezado base64 si existe y obtener MIME
        if (preg_match('/^data:(.*);base64,/', $imageBase64, $matches)) {
            $mimeType = $matches[1]; // tipo mime detectado
            $imageBase64 = substr($imageBase64, strpos($imageBase64, ',') + 1);
        } else {
            // Asignar mime según extension
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $mimeMap = [
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
            ];
            $mimeType = $mimeMap[strtolower($extension)] ?? 'application/octet-stream';
        }

        $imageContent = base64_decode($imageBase64);

        if (!$imageContent || strlen($imageContent) < 100) {
            return response()->json(['status' => false, 'error' => 'La imagen no pudo decodificarse correctamente']);
        }

        $apiUrl = "http://40.124.183.121/api/images/products/{$productId}";
        $apiKey = '4Z4CSJ4WN4PYMM4GKTCWGMJNYMSGRCGH';

        $response = Http::withBasicAuth($apiKey, '')
            ->attach('image', $imageContent, $fileName, ['Content-Type' => $mimeType])
            ->post($apiUrl);

        return response()->json([
            'status' => $response->successful(),
            'http_status' => $response->status(),
            'prestashop_body' => $response->body(),
        ]);
    } catch (\Exception $e) {
        return response()->json(['status' => false, 'error' => $e->getMessage()]);
    }
}



    public function productImgE(Request $r){
    try {
            $productId = $r->product_id;
            $imageBase64 = $r->image_base64;
            $imageId = $r->image_id;

       /*      return $imageId; */
            // Quitar encabezado si existe
            if (str_contains($imageBase64, ',')) {
                [, $imageBase64] = explode(',', $imageBase64);
            }

            $imageContent = base64_decode($imageBase64);
            $fileName =   $imageId;

            $apiUrl = "http://40.124.183.121/api/images/products/{$productId}/{$imageId}?ps_method=PUT";
            $apiKey = 'MKE7HBSJK621K9DI7PISIGA7VQAVGTHJ';

            $response = Http::withBasicAuth($apiKey, '')
                ->attach('image', $imageContent, $fileName)
                ->post($apiUrl);

            return response()->json([
                'status' => $response->successful(),
                'http_status' => $response->status(),
               'prestashop_body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage(), 'prestashop_body_raw' => $response->body()]);
        }
    }


    public function stringTest(Request $r){
        try{

            $cadena=explode('-',$r->cadena);

            if (isset($cadena[1]) && ctype_digit($cadena[1])) {
            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => false]);
        }
        }catch (\Exception $e) {
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
