<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(array $data)
    {
    $data['password'] = Hash::make($data['password']);
    $data['role'] = 'buyer';
    $data['status'] = 'active';

    return $this->userRepository->create($data);
    }

    public function updateUser($id, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->update($id, $data);
    }

    public function deleteUser($id)
    {
    $user = $this->userRepository->find($id);

    if ($user->role === 'admin') {
        return false;
    }

    return $this->userRepository->delete($id);

    }

    public function findUser($id)
    {
    return $this->userRepository->find($id);
    }

    // public function findByEmail($email)
    // {
    // return $this->userRepository->findByEmail($email);
    // }

    public function getAll()
    {
    return $this->userRepository->getAll();
    }

    public function login(array $data)
    {
    $user = $this->userRepository->findByPhone($data['phone']);

    if (!$user || !Hash::check($data['password'], $user->password)) {
        return false;
    }

    // إنشاء توكن
    $token = $user->createToken('auth_token')->plainTextToken;

    return [
        'user' => $user,
        'token' => $token
    ];
    }

    public function logout($user)
    {
    $user->currentAccessToken()->delete();
    }
}
