<?php

namespace App\Http\Controllers\Admin\Pharmacies;

use App\Http\Controllers\Controller;
use App\Models\Registration\Pharmacy;

class PharmacyController extends Controller
{
    public function get(){
        $Pharmacies = Pharmacy::get();
        return $this->success($Pharmacies);
    }
}
