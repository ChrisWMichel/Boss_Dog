<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\User;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sortField', 'updated_at');
        $sortDirection = request('sortDirection', 'asc');

        $query = User::query()
            ->orderBy($sortField, $sortDirection)
            ->where('firstname', 'like', "%{$search}%")
            ->orWhere('lastname', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->paginate($perPage);

        return UserResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $data['email_verified_at'] = now();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $user = User::create($data);

        return new UserResource($user);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User      $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {

        $data = $request->validated();

        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        if (isset($data['is_admin'])) {
            $data['is_admin'] = (bool)$data['is_admin'];
        } else {
            unset($data['is_admin']);
        }
        $data['updated_by'] = $request->user()->id;

        $user->update($data);

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }

}
