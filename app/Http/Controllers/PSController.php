<?php

namespace App\Http\Controllers;

use App\Models\PS_PriceList;
use App\Models\PS_PriceProduct;
use App\Models\PS_Products;
use App\Models\PS_Stock;
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

            return response()->json(['status' => true, 'response' =>'entro a funcion']);
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
                'id_warehouse' => 'required|integer',
                'name' => 'required|string|max:255',
            ]);

            $plist = new PS_PriceList();

            $plist->fill([$validated])->save();

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

            $price->fill([$validated])->save();

            return response()->json(['status' => true, 'response' => $price]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'response' => $e->getMessage()]);
        }
    }
}
