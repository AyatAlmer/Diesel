<?php
namespace App\Repositories;

use App\Models\Location;

class LocationRepository
{
    public function getAll()
    {
        return Location::withCount('products')->paginate(10);
    }

    public function find($id)
    {
        return Location::with('products')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Location::create($data);
    }

    public function update($id, array $data)
    {
        $location = Location::findOrFail($id);
        $location->update($data);
        return $location;
    }

   public function delete($id)
{
    $location = Location::withCount('products')->findOrFail($id);

    if ($location->products_count > 0) {
        throw new \Exception('Cannot delete this location because it contains products.');
    }

    return $location->delete();
}
}
