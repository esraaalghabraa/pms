<?php

namespace App\Http\Controllers\Admin\Repositories;

use App\Http\Controllers\Controller;
use App\Models\Registration\Repository;

class RepositoryController extends Controller
{
    public function get(){
        $Pharmacies = Repository::get();
        return $this->success($Pharmacies);
    }
}
