<?php

namespace App\Http\Controllers\Admin\Drugs\Classifications;

use App\Http\Controllers\Controller;
use App\Models\Drug\Category;
use App\Models\Drug\Drug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    public function get(){
        $categories = Category::get();
        return $this->success($categories);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:categories,name',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Category::create([
            'name' => $request->name
        ]);

        return $this->success();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:categories',
            'name' => 'required|string|max:50|unique:categories,name,' . $request->id,
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $category = Category::where('id',$request->id)->first();
        $category->update([
            'name' => $request->name
        ]);

        return $this->success();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:categories',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Category::where('id',$request->id)->first()->delete();
        return $this->success();
    }

    public function getDrugs(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:categories',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $drugs = Drug::where('category_id',$request->id)->get();
        return $this->success($drugs);
    }

}
