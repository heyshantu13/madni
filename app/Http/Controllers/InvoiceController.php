<?php


/*

Author: Shantanu Kulkarni
Date: 13/10/2020
Github: @heyshantu13

*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustomerInvoice;
use App\PriceCategory;
use App\Customer;
use App\TntPrice;
use Khsing\World\World;



class InvoiceController extends Controller
{
    //

    public function index(){

        $invoices = CustomerInvoice::all()->sortByDesc("created_at");
        return view('pages.invoice.index',compact('invoices'));

    }

    public function create($id = null){

        // Check if customer available or not

        $customers = Customer::all()->sortByDesc("created_at");

        if($id){
            $customerbyid = Customer::findOrFail($id);
            $pricecategory = PriceCategory::findorFail($customerbyid->price_categories_id);
            return view('pages.invoice.details',compact(['customers','customerbyid','id']));

        }

        else{
            $id = 0;
            return view('pages.invoice.details',compact(['customers','id']));
        }

    }


    public function newInvoice(Request $request){

        $request->validate([
            'customer_id' => 'required',
            'type' => 'required',
            'provider' => 'required',
            'class' => 'required'
        ]);

            $customerdetails = Customer::findOrFail($request->customer_id);
            $is_express = $request->class;
            $is_import = $request->type;
            $countries = World::Countries();
        


        /*

        1 Type
            a. Import > 1
            b. Export > 2
         
         2 Provider
            a. TNT > 1
            b. Fedex > 2   

        */

 
        if($request->provider == 1) {
        //     TNT Provider
        // Check For Type

            if($request->type == 0){
                //  TNT Export

                $tntimport = [
                    'provider' => "TNT",
                    'type' => "Export",
                    "class" => ($request->class) ? ("Express") : ("Economy"),
                    'provider_id' => $request->provider,
                    'type_id' => $request->type,
                    'class_id' => $request->class

                ];
               

                $pricelists = TntPrice::where('price_categories_id',$customerdetails->price_categories_id)
                ->where('is_import',$is_import)
                ->where('is_express',$is_express)
                ->get();

                
                $zones =  TntPrice::where('is_import',$is_import)
                ->where('is_express',$is_express)
                ->orderBy('zone', 'ASC')
                ->get()
                ->unique('zone');
                

                if(!$pricelists){
                    return back()->with('error','Data Not Available Right Now! Please Update Sheet');
                }


                return view('pages.invoice.newinvoice',compact(['pricelist','customerdetails','countries','zones','tntimport']));

              //  return $pricelists;



            }

            if($request->type == 1){
                //  TNT Import

                $tntimport = [
                    'provider' => "TNT",
                    'type' => "Import",
                    "class" => ($request->class) ? ("Express") : ("Economy"),
                    'provider_id' => $request->provider,
                    'type_id' => $request->type,
                    'class_id' => $request->class
                ];
                
                $pricelists = TntPrice::where('price_categories_id',$customerdetails->price_categories_id)
                ->where('is_import',$is_import)
                ->where('is_express',$is_express)
                ->get();

              

                $zones =  TntPrice::where('is_import',$is_import)
                ->where('is_express',$is_express)
                ->orderBy('zone', 'ASC')
                ->get()
                ->unique('zone');

                if(!$pricelists){
                    return back()->with('error','Data Not Available Right Now! Please Update Sheet');
                }
                return view('pages.invoice.newinvoice',compact(['pricelist','customerdetails','countries','zones','tntimport']));
            }
            
         
          }

         if($request->provider == 2)
         {
            //  Fedex Provider

             // Check For Type

             if($request->type == 0){
                //  TNT Import

                return "FedeX IMPORT";

            }

            if($request->type == 1){
                //  TNT Export
                
                return "FedeX EXPORT";
            }
         } 



       

    }

    public function store(Request $request){
        return $request;
    }
}