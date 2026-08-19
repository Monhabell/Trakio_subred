<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\UserResource;
use App\Http\Requests\Api\UpdateUserRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = UserResource::collection(User::select('id', 'name', 'last_name')->where('role_id', 1)->get());
        return $users;
        //return UserResource::collection(User::with('dataUser')->get());
        //return new UserCollection(User::with('dataUser')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($user->load('dataUser', 'entorno', 'subnet', 'role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        switch (strtolower($request->type_edit)){
            case 'tyc':
                $user->update([
                    'terms_accepted' => 1
                ]);
                break;

            default:
                return response()->json(['message' => 'Opción no válida'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['message' => Response::HTTP_OK]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}