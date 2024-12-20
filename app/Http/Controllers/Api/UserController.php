<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Unique;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($flag)
    {
        $query  = User::select('name', 'email');

        if ($flag == 1) {

            $query->where('status', 1);
        } elseif ($flag == 0) {
        } else {
            return response()->json([
                'message' => "Please enter o or 1 anothe not allow ",

            ]);
        }

        $user =  $query->get();
        if (count($user) > 0) {

            $response = [
                'message' => count($user) . 'users found',
                'status' => 1,
                'data' => $user
            ];
            return response()->json($response, 200);
        } else {

            $response = [
                'message' => '0 user ',
                'status' => 1,
                'data' => 'No data found'
            ];

            return response()->json($response, 302);
        }
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

        // p($request->all());

        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
            'password_confirmation' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' =>  $request->password,
            ];
            DB::beginTransaction();
            try {
                $user = User::create($data);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                p($e->getMessage());
                $user = null;
            }
            if ($user != null) {

                return response()->json([
                    'message' => "User Registered Successfully."
                ], 200);
            } else {

                return response()->json([
                    'message' => "Internal Server Error Please contatct WebMaster."
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)


    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message ' => 'NO user find',
                'status' => 0,
            ];
        } else {



            $response = [
                'message' => 'User is Find' . $user,
                'Status' => 1,
                'data ' => $user
            ];
        }

        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // p($request->all());die;
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json([
                'message' => "User Not Found",
                'Status'  => 0
            ], 400);
        } else {

            DB::beginTransaction();

            try {

                $user->name = $request->name;
                $user->email = $request['email'];
                $user->contact = $request['contact'];
                $user->pincode = $request['pincode'];
                $user->address = $request['address'];
                $user->save();

                DB::commit();
            } catch (\Exception $err) {
                DB::rollBack();
                $user = null;
            }

            if (is_null($user)) {
                return response()->json([
                    'message' => "Internal Server Error",
                    'Status' => 0,
                    'Error_message' => $err->getMessage()
                ], 500);
            } else {
                return response()->json([
                    'message' => "Data Updated Successfully.",
                    'Status' => 1
                ], 200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message' => "No User Found",
                'Status' => 0
            ];
            $response_code = 200;
        } else {
            DB::beginTransaction();
            try {
                $user->delete();
                DB::commit();
                $response = [
                    'message' => 'User Deleted Successfully',
                    'Status' => 1
                ];
                $response_code = 200;
            } catch (\Exception $err) {
                DB::rollBack();
                $response = [
                    'message' => 'Internal Server Error.',
                    'Status' => 0
                ];
                $response_code = 500;
            }
        }

        return  response()->json($response, $response_code);
    }


    public function changePassword(Request $request, string $id)
    {

        $user = User::find($id);
       // p($user) ; die;
        if (is_null($user)) {
            return response()->json([
                'message' => "User Not Found",
                'Status'  => 0,
                
            ], 400);
        } else {
            if ($user->password == $request['old_passwrd']) {

                if ($request['new_password'] == $request['password_confirmation']) {

                    DB::beginTransaction();
                    try {
                        //change
                        $user->password = $request['new_password'];
                        $user->save();
                        DB::commit();
                       
                    } catch (\Exception $err) {
                        $user = null;
                        DB::rollBack();
                       

                    }

                    if (is_null($user)) {
                        return response()->json([
                            'message' => "Internal Server Error",
                            'Status' => 0,
                            'Error_message' => $err->getMessage()
                        ], 500);
                    } else {
                        return response()->json([
                            'message' => "Password  Updated Successfully.",
                            'Status' => 1
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'message' => "Password confirmation does not match.",
                        'Status' => 00,
                    ], 400);
                }
            } else {
                return response()->json([
                    'message' => "Old Password Does not Match",
                    'Status' => 0,
                ], 500);
            }

            // return response()->json([
            //     $response,$response_code
            // ]);
        }
    }
}
