<?php
namespace App\Http\Controllers;

use App\Services\LocationService;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;

class LocationController extends Controller
{
    protected $service;

    public function __construct(LocationService $service)
    {
        $this->service = $service;
    }

    public function getAll()
    {
        return response()->json($this->service->getAll());
    }

    public function showById($id)
    {
        return response()->json($this->service->find($id));
    }

    public function addLocation(StoreLocationRequest $request)
    {
        return response()->json(
            $this->service->create($request->validated()),
            201
        );
    }

    public function updateLocation(UpdateLocationRequest $request, $id)
    {
        return response()->json(
            $this->service->update($id, $request->validated())
        );
    }


    public function destroyLocation($id)
    {
    try {
        $this->service->delete($id);

        return response()->json([
            'message' => 'Location deleted successfully'
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'message' => $e->getMessage()
        ], 400);
    }
    }
}
