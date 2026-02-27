<?php
namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getAll($filters = [])
    {
    if (auth()->user()->role === 'admin') {
        $query = Product::withTrashed()->with(['category','location']);
    } else {
        $query = Product::with(['category','location'])
                        ->where('status', 'available');
    }

    if (!empty($filters['category_id'])) {
        $query->where('category_id', $filters['category_id']);
    }

    return $query->paginate(10);
    }


    public function find($id)
    {
        return Product::with(['seller', 'category','location'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

   public function update($id, array $data)
{
    $product = Product::findOrFail($id);

    $product->update($data);

    return $product->fresh(['seller', 'category','location']);
}

    public function delete($id)
    {
        return Product::destroy($id);
    }

    public function restore($id)
    {
    $product = Product::withTrashed()->findOrFail($id);
    $product->restore();
    return $product;
    }
}
