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
use App\Exceptions\Handler;
use App\InvoiceProduct;
use setasign\Fpdi\Fpdi;
use Exception;







class InvoiceController extends Controller
{
    //

    public function index(){

        $invoices = CustomerInvoice::with('customer')->orderBy('created_at','desc')->get();
        //return $invoices;
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


        //   For Fedex Calculation Start

         if($request->provider == 2)
         {
            //  Fedex Provider

            // Fedex Express

             


          if($request->class == 1)
          {


                if($request->type == 0)
                   {
                        $tntimport = [
                        'provider' => "Fedex",
                        'type' => "Export",
                        "class" => ($request->class) ? ("Express") : ("Economy"),
                        'provider_id' => $request->provider,
                        'type_id' => $request->type,
                        'class_id' => $request->class

                        ];

                          $pricelists = \App\FedexPrice::where('price_categories_id',$customerdetails->price_categories_id)
                ->where('is_import',$is_import)
                ->where('is_express',$is_express)
                ->get();

                
                $zones = \App\FedexPrice::where('is_import',$is_import)
                ->where('is_express',$is_express)
                ->orderBy('zone', 'ASC')
                ->get()
                ->unique('zone');


                     if(!$pricelists){
                    return back()->with('error','Data Not Available Right Now! Please Update Sheet');
                }


                return view('pages.invoice.newinvoice',compact(['pricelist','customerdetails','countries','zones','tntimport']));



                   }
                    

                if($request->type == 1)
                   {
                        // Fedex Import

                    return "Fedex Import";

                   } 


          }

           if($request->class == 0)
          {
                return back()->with('error','Price Sheet Not Available in This Version');
          }


            // Fedex Express End

            
         } 



       

    }

    public function store(Request $request){
       // dd($request->product_details['consignment_no']);

       $i = 0;
       $volumetricwt = 0;

        $invoice = CustomerInvoice::create([
            'customer_id' => $request->customer_id,
            'price_categories_id' => $request->price_categories_id,
            'state_code' => $request->statecode,
            'invoice_date'=> $request->invoice_date,
            'gross_amount'=> $request->gross_amount,
            'fuel_surcharge' => $request->fuel_surcharge,
            'enhance_security_charge' => $request->enhance_security_charge,
            'custom_clearance' => $request->custom_clearance,
            'oda_charge' => $request->oda_charge,
            'adc_noc_charge' => $request->adc_noc_charge,
            'do_charge'=> $request->do_charges,
            'non_conveyar_charge' => $request->non_conveyar_charge,
            'address_correction_charge' => $request->address_correction_charge,
            'war_surcharge' => $request->war_surcharge,
            'warehousing_charge' => $request->warehousing_charge,
            'ad_code_registration_charge' => $request->ad_code_registration_charge,
            'air_cargo_registration_charge' => $request->air_cargo_registration_charge,
            'gst_percentage' => 18,
            'cgst_amount' =>  round((float)$request->cgst,2),
            'sgst_amount'=> $request->sgst,
            'igst_amount'=> $request->igst,
            'is_express' => $request->class_id,
            'is_import' => $request->type_id,
            'provider' => $request->provider_id,
            'net_amount' => round((float)$request->net_amount,2),

        ]);

        if($invoice)
        {
            //dd($request->product_details);

           for($i = 0; $i < count($request->product_details["consignment_no"]); $i++){
            $productlists = new InvoiceProduct();
            $productlists->customer_invoice_id = $invoice->id;
            $productlists->consignment_no = $request->product_details["consignment_no"][$i];
            $productlists->referance_no = $request->product_details["referance_no"][$i];
            $productlists->booking_date = $request->product_details["booking_date"][$i];
            $productlists->origin = $request->product_details["origin"][$i];
            $productlists->destination = $request->product_details["destination"][$i];
            $productlists->actual_weight = $request->product_details["actual_weight"][$i];
            $productlists->l = $request->product_details["l"][$i];
            $productlists->w = $request->product_details["w"][$i];
            $productlists->h = $request->product_details["h"][$i];
            $productlists->mode = $request->product_details["mode"][$i];
            $productlists->chargable_weight = $request->product_details["chargable_weight"][$i];
            if($request->product_details["mode"][$i] == 0){
                $volumetricwt = ($request->product_details["l"][$i] +  $request->product_details["w"][$i] + $request->product_details["h"][$i])/5000;
            }
            if($request->product_details["mode"][$i] == 1){
                $volumetricwt = ($request->product_details["l"][$i] +  $request->product_details["w"][$i] + $request->product_details["h"][$i])/6000;

            }
          
            $productlists->volumetric_weight = $volumetricwt;
            $productlists->product_type = $request->product_details["product_type"][$i];
            $productlists->zone = $request->product_details["zone"][$i];
            $productlists->amount = $request->product_details["amount"][$i];
            $saved = $productlists->save();
           }
            
           if($saved)
           {
            return back()->with('success','Invoice Created Successfully.');   
           }
           else{
               $invoice = CustomerInvoice::find($invoice->id);
               $invoice->delete();
               return back()->with('errors','Unable To Save Invoice At That Movement');

             }
        }
        else{
            return back()->with('errors','Unable To Create Invoice At That Movement');
        }
        
       
       
        //return $request;
    }


    public function generateInvoicePdf(Request $request,$id=null){
        $invoice = CustomerInvoice::where('id',$id)->firstOrFail();
        $customer = Customer::findOrFail($invoice->customer_id);
        $products = InvoiceProduct::where('customer_invoice_id',$invoice->id)->get();


         $pdf = PDF::loadView('invoice.pdf');
            return $pdf->download('invoice.pdf');
     

   


    }






}


class PDF extends FPDI {

    function Header(){

    }
    function PutLink($URL, $txt)
    {
        // Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }

}