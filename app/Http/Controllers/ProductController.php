<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
// use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function addProduct(StoreProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());
        return $this->success($product, 'Product created');
    }

    public function updateProduct(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->updateProduct($id, $request->validated());

        if (!$product) {
            return $this->error('Unauthorized', 403);
        }

        return $this->success($product, 'Updated');
    }

    public function destroyProduct($id)
    {
        $result = $this->productService->deleteProduct($id);

        if (!$result) {
            return $this->error('Unauthorized', 403);
        }

        return $this->success(null, 'Deleted');
    }

    public function showAllProduct(Request $request)
    {
    $products = $this->productService->getAll($request->all());
    return $this->success($products);    }

    public function showProductById($id)
    {
        return $this->success($this->productService->find($id));
    }

    public function restoreProduct($id)
    {
    $product = $this->productService->restoreProduct($id);
    return $this->success($product, 'تم استعادة المنتجات المحذوفة بنجاح');
    }

    public function searchProduct(Request $request)
{
    $request->validate([
        'name' => 'required|string'
    ]);

    $products = $this->productService->searchProductByName($request->name);

    return response()->json([
        'message' => 'تم العثور على المنتجات',
        'data' => $products
    ]);
}

public function showDeletedProducts()
{
    $products = $this->productService->getDeletedProducts();

    return response()->json([
        'message' => 'تم جلب المنتجات المحذوفة بنجاح',
        'data' => $products
    ]);
}
}
