<?php

namespace App\Imports;

use App\TntPrice;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class TntExpressNonDocImport implements ToCollection
{

    private $pricecategoryid;

    public function __construct($pricecategoryid)
    {
        $this->pricecategoryid = $pricecategoryid;
    }
  
    public function collection(Collection $rows)
    {
        for($i = 1; $i < count($rows); $i++){
            // echo count($rows[$i]);
            for($j = 1; $j < count($rows[$i]); $j++){

                $price = new \App\TntPrice();
                $price->price = $rows[$i][$j];
                $price->weight = $rows[$i][0];
                $price->zone = $rows[0][$j];
                $price->is_doc =0;
                $price->is_express = 1;
                $price->is_import = 1;
                $price->price_categories_id =  $this->pricecategoryid;
                $price->save();
            }
        }
    }
}
