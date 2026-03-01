<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());
        return $this->success($user, 'تم اضافة مستخدم بنجاح');
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->userService->updateUser($id, $request->validated());
        return $this->success($user, 'تم تعديل البيانات بنجاح');
    }

    public function destroy($id)
    {
        $result = $this->userService->deleteUser($id);
        if (!$result) {
        return $this->error('Cannot delete system admin', 403);
    }
        return $this->success(null, 'تم حذف البيانات بنجاح');
    }

    public function showUserById($id)
    {
    $user = $this->userService->findUser($id);
    return $this->success($user);
    }

    public function getByEmail($email)
    {
    $user = $this->userService->findByEmail($email);

    if (!$user) {
        return $this->error('User not found', 404);
    }

    return $this->success($user);
    }

    public function getAll()
    {
    $user = $this->userService->getAll();
    return $this->success($user);
    }

    public function login(LoginRequest $request)
    {
    $result = $this->userService->login($request->validated());

    if (!$result) {
        return $this->error('Invalid credentials', 401);
    }

    return $this->success($result, 'تم تسجيل الدخول');
    }

    public function logout(Request $request)
    {
    $this->userService->logout($request->user());
    return $this->success(null, 'تم تسجيل الخروج بنجاح');
    }

    public function searchUser(Request $request, UserService $userService)
{
    $request->validate([
        'name' => 'required|string'
    ]);

    $users = $userService->searchUserByName($request->name);

    return response()->json([
        'message' => 'تم العثور على المستخدمين',
        'data' => $users
    ]);}
}
