<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\Bookmarks;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use Illuminate\Auth\Events\Registered;



class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['authenticated' => false]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(LoginRequest $request)
    {
        try {

            $request->authenticate();

            $token = $request->user()->createToken($request->token_name);

            $request->user()->status = 1;

            $request->user()->save();

            $request->user()->payment_methods = PaymentMethod::where('created_by', $request->user()->id)->where('archive', 0)->get();
            $request->user()->bookmarks = Bookmarks::with('resortInfo.images')->where('created_by', auth()->id())->get();

            (new NotificationController)->notifiReservation();
     
            (new AdminController)->index();
            
            return response()->json([
                'authenticated' => true,
                'response' => 'User logged in successfully',
                'token' => array_reverse(explode('|', $token->plainTextToken))[0],
                'user' => $request->user()
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'authenticated' => false,
                'response' => 'Invalid Email or Password',
                'token' => ''
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        try {

            $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'contact_no' => $request->contact_no,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 1,
            ]);
            
            (new AdminController)->index();


            // event(new Registered($user));
     
            
            return response()->json([
                'authenticated' => false,
                'response' => 'Registration Success',
                'token' => ''
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'authenticated' => false,
                'response' => $e->getMessage(),
                'token' => ''
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $request->user()->status = 0;

        $request->user()->save();

        return response()->json([
            'authenticated' => false,
            'response' => 'Logout Success',
            'token' => '',
            'user' => []
        ]);
    }
}
