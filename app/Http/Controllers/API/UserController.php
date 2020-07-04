<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Gate::allows('isAdmin') || Gate::allows('isAuthor')){
            return User::latest()->paginate(10);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if(Gate::allows('isAdmin') || Gate::allows('isAuthor')){
            if ($search = $request->get('q')){
                $user = User::where(function ($query) use ($search){
                    $query->where("name", "LIKE", "%$search%")
                          ->orWhere("email", "LIKE", "%$search%")
                          ->orWhere("type", "LIKE", "%$search%");
                })->paginate(10);
            }
            else{
                $user = User::latest()->paginate(10);
            }
            return $user;
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
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:8'
        ]);

        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'type' => $request['type'],
            'password' => Hash::make($request['password']),
            'bio' => $request['bio'],
            'photo' => $request['photo']
        ]);
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return auth('api')->user();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|min:8',
        ]);
        $current_image = $user->photo;
        if($request->photo != $current_image){
            $extension = explode('/', mime_content_type($request->photo))[1];
            $imageName = time().'.'.$extension;
            Image::make($request->photo)->save(public_path('images/profiles/').$imageName);
            $request->merge(['photo' => $imageName]);
            $userCurrentPhoto = public_path('images/profiles/').$current_image;
            if(file_exists($userCurrentPhoto)){
                @unlink($userCurrentPhoto);
            }
        }
        if(!empty($request->password)){
            $request->merge([
                'password' => Hash::make($request['password'])
            ]);
        }
        $user->update($request->all());
        return $request->photo;
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
        $this->authorize('isAdmin');
        $user = User::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'type' => 'required|string'
        ]);
        $user->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');
        $user = User::findOrFail($id);
        $user->delete();
    }
}
