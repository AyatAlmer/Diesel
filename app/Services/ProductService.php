<?php
namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function createProduct(array $data)
    {
        if (isset($data['image'])) {
        $data['image'] = $data['image']->store('products', 'public');
    }
        $data['user_id'] = auth()->id();
        return $this->productRepository->create($data);
    }

    public function updateProduct($id, array $data)
    {
        $product = $this->productRepository->find($id);

        if (isset($data['image'])) {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $data['image'] = $data['image']->store('products', 'public');
    }

        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct($id)
    {
        $product = $this->productRepository->find($id);

        if (auth()->user()->role !== 'admin') {
            return false;
        }
        $this->productRepository->update($id, ['status' => 'غير متاح']);
        return $this->productRepository->delete($id);
    }

    public function getAll($filters)
    {
    return $this->productRepository->getAll($filters);
    }

    public function find($id)
    {
        return $this->productRepository->find($id);
    }

    public function restoreProduct($id)
    {
        $product = $this->productRepository->restore($id);

        $this->productRepository->update($id, ['status' => 'متاح']);

        return $product;
    }

    public function searchProductByName(string $name)
{
    return $this->productRepository->query()
        ->withTrashed() // يشمل المنتجات المحذوفة
        ->where('title', 'like', "%{$name}%")
        ->get();
}

public function getDeletedProducts()
{
    return $this->productRepository->query()
        ->onlyTrashed()  // يختار فقط المنتجات المحذوفة
        ->with(['category', 'location', 'seller'])
        ->get();
}
}
