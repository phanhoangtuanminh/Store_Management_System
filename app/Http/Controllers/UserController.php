<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */

    public function index()
    {

        return response()->json(User::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $users = new User([
            'name' => $request->input('name'),
            'email' => $request->input('email')
        ]);

        $users->save();
        return response()->json('Update user successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, Request $request)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $users = User::find($id);
        $users->update($request->all());
        return response()->json([
            'user' => $users,
            'User updated!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function viewPermission(Request $request)
    {
        if ($request->user()->can('view-users')) {
            return response()->json(User::all());
        }

        return response([
            'status' => false,
            'message' => 'You don\'t have permission to view users!'
        ], 200);
    }
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->file('image')) {
            $image = $request->file('image');
            $file_name = time() . '_' . $image->getClientOriginalName();
            $path = $request->file('image')->storeAs('profiles', $file_name, 'public');

            // Lưu đường dẫn hình ảnh vào cơ sở dữ liệu
            auth()->user()->update(['image' => $path]);

            return response()->json(['image' => $path]);
        }

        return response()->json(['error' => 'Failed to upload image.']);
    }

    public function getTotalCustomers()
{
    $totalCustomer = User::where('role_id', 3)->count();
    
    return response()->json(['totalCustomer' => $totalCustomer]);
}
}
