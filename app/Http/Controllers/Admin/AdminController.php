<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ACCUsers;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function index()
    {
        $user = auth()->user();


        if (!$user->hasRole('Superadmin')) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        

        $branches = Branch::all();


        if ($branches->isEmpty()) {
            return view('admin.dashboard', compact('branches'))
                ->with('warning', 'No branches found in the database.');
        }

        return view('admin.dashboard', compact('branches'));
    }


    public function SVC()
    {
        return view('admin.SVC.svc');
    }

    public function storeUser(Request $request)
    {
       
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'user_role' => 'required|string',
            'user_branch' => 'required|string',
        ]);
      
        try {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'user_role' => $validated['user_role'],
                'user_branch' => $validated['user_branch'],
            ]);
            // dd('here');
            return back()->with('success', 'User created successfully');
        } catch (\Exception $e) {
            // Log the error for debugging
            dd($e->getMessage());
            \Log::error('User creation failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to create user. Please try again.');
        }
    }
}
