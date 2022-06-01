<?php

namespace App\Http\Controllers;

use App\Models\Auth;
use Illuminate\Http\Request;
use Validator;
use \Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'bail|required|string|max:255',
            'lastname' => 'bail|required|string|max:255',
            'thirdname' => 'bail|required|string|max:255',
            'birthday' => 'bail|required|date',
            'external_uuid' => 'bail|required|regex:/^[\da-z]{8}\-[\da-z]{4}\-[\da-z]{4}\-[\da-z]{4}\-[\da-z]{12}$/|unique:auths',
        ]);

        if($validator->fails()) {    
            return response()->json($validator->messages(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $auth = new Auth($request->all());
        $auth->prepare();
        $auth->save();

        return response()->json($auth->token);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $token = $request->bearerToken();
        $auth = Auth::where('token', $token)->first();

        if(!$auth || !$auth->isLive()) {
            return response()->json('Not authorized.', Response::HTTP_FORBIDDEN);
        }

        return response()->json( $auth->getUserData() );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Auth  $auth
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Auth $auth)
    {
        $token = $request->bearerToken();
        $auth = Auth::where('token', $token)->first();

        if(!$auth || !$auth->isLive()) {
            return response()->json('Not authorized.', Response::HTTP_FORBIDDEN);
        }

        $auth->updateToken();

        return response()->json($auth->token);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $token = $request->bearerToken();
        $auth = Auth::where('token', $token)->first();

        if(!$auth || !$auth->isLive()) {
            return response()->json('Not authorized.', Response::HTTP_FORBIDDEN);
        }

        $auth->disableToken();

        return response()->json('OK');
    }

    /**
     * Display uuid the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        $token = $request->bearerToken();
        $auth = Auth::where('token', $token)->first();

        if(!$auth || !$auth->isLive()) {
            return response()->json('Not authorized.', Response::HTTP_FORBIDDEN);
        }

        $external_uuid = $request->post('external_uuid') ?? null;
        if($external_uuid != $auth->external_uuid) {
            return response()->json('Wrong external uuid.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json(['uuid' => $auth->uuid]);
    }
}
