<?php

namespace App\Http\Controllers;
// use Illuminate\Http\Request;

use Illuminate\Http\Request;
use app\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(Request $request)
    {
        //get users with pagination
        $users = DB::table('users')
            ->when($request->input('name'), function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(4);
        return view('pages.user.index', compact('users'));

    }

    //create
    public function create()
    {
        return view('pages.user.create');
    }

   public function store(Request $request)
{
    // Validate the request inputs
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'phone' => 'required|string|max:15',
        'roles' => 'required|string',
    ]);

    // Create a new user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password), // Hash the password
        'phone' => $request->phone,
        'roles' => $request->roles,
    ]);

    // Redirect to user index with success message
    return redirect()->route('user.index')->with('success', 'User created successfully.');
}

// Show method
public function show($id)
{
    // Assuming you want to show a specific user’s dashboard or data, fetch the user by ID
    $user = User::findOrFail($id);

    return view('pages.dashboard', compact('user'));
}

// Edit method
public function edit($id)
{
    // Find the user by ID and pass it to the view for editing
    $user = User::findOrFail($id);

    return view('pages.user.edit', compact('user'));
}
       

    //update
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $user = User::findOrFail($id);

        //check if password is not empty
        if ($request->input('password')) {
            $data['password'] = Hash::make($request->input('password'));
        } else {
            //if password is empty, then use the old password
            $data['password'] = $user->password;
        }
        $user->update($data);
        return redirect()->route('user.index')->with('success', 'User updated successfully');

    }
    //destroy
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User deleted successfully');
    }
}
