<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:products.index')->only('index');
        $this->middleware('has.permission:products.store')->only('store');
        $this->middleware('has.permission:products.update')->only('update');
        $this->middleware('has.permission:products.destroy')->only('destroy');
    }
    
    public function index()
    {
        return Product::where('name', 'like', '%' . request()->search . '%')
                            ->paginate(7);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:90|unique:products,name',
            'vaccine' => 'nullable|boolean',
            'deworming' => 'nullable|boolean'
        ], [], [
            'name' => 'Nombre'
        ]);

        $data['user_id'] = auth()->user()->id;
        //return $data;
        return Product::create($data);
    }

    public function show(Product $product)
    {
        return $product;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|max:90|unique:products,name,' . $product->id,
            'vaccine' => 'nullable|boolean',
            'deworming' => 'nullable|boolean'
        ], [], [
            'name' => 'Nombre'
        ]);

        $product->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->clinicHistoryProducts()->exists() || $product->vaccines()->exists() || $product->dewormings()->exists()) {
            abort(422);
        }

        $product->delete();
    }
}
