<?php

namespace App\Http\Controllers;

use App\Models\PS_Products;
use Exception;
use Illuminate\Http\Request;

class PSController extends Controller
{
    public function getProducts(){
        try{
                $p= PS_Products::limit(10)->get();
            return response()->json(['status'=>true,'response'=>$p]);
        }catch(Exception $e){
            return response()->json(['status'=>false,'response'=>$e->getMessage()]);
        }
    }

    public function getPrices(){
        try{

            return response()->json(['status'=>true,]);
        }catch(Exception $e){
            return response()->json(['status'=>false,'response'=>$e->getMessage()]);
        }
    }


    public function productImg(Request $r){
          try{

            return response()->json(['status'=>true,'response'=>$r->all()]);
        }catch(Exception $e){
            return response()->json(['status'=>false,'response'=>$e->getMessage()]);
        }
    }
}
