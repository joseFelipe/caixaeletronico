<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::latest()->paginate(10);
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
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|min:14|max:14|unique:users,cpf',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|max:5|min:5',
        ]);

        return User::create([
            'name' => $request['name'],
            'cpf' => $request['cpf'],
            'email' => $request['email'],
            'type' => $request['type'],
            'password' => Hash::make($request['password'])
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {   
        // $user = User::find($id);

        /*
        public function update(UpdateCategoryRequest $request, Category $category)
        {
            $category->update([
                'name' => $request->name
            ]);
                
            session()->flash('success', 'Category updated successfully');
            
            return redirect(route('categories.index'));
        }
        */
        
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:255|unique:users,cpf,'.$user->id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'required|string|max:5|min:5',
        ]);

        $user->update([
            'password' => $request->password
        ]);
            
        $user->update($request->all());
        
        return ['message' => 'updated'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
     
        $user = User::find($id);
        
        $user->delete();

        return ['message' => 'User has been deleted'];
    }
}
