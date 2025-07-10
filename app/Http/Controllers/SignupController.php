<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SignupController extends Controller
{
    public function showForm()
    {
        return view('SignUpPage');
    }

    public function handleSignup(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        // Create the user
        $user = User::createNewUser($validated);

        // Redirect to the login page or dashboard
        return redirect()->route('signin');
    }

    public function handleOAuth(Request $request)
    {
        $authContext = null;

        if ($request->has('google')) {
            $authContext = new AuthContext(new GoogleAuthStrategy());
        } elseif ($request->has('facebook')) {
            $authContext = new AuthContext(new FacebookAuthStrategy());
        }

        $result = $authContext->executeStrategy();

        return redirect()->route('home')->with('status', $result);
    }
}
