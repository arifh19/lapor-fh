<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transformers\UserTransformer;
use App\Transformers\UsersTransformer;
use Laratrust\LaratrustFacade as Laratrust;
use App\User;
use App\RoleUser;
use App\Role;
use App\Http\Requests\UserRequestEdit;


class UserController extends Controller
{
   
    public function __construct(User $user,UserTransformer $userTransformer, UsersTransformer $usersTransformer){
        $this->userTransformer = $userTransformer;
        $this->usersTransformer = $usersTransformer;
        $this->user = $user;

    }
    
    public function getCurrent(){
        $user = auth()->user();
        return response()->json($this->usersTransformer->transform($user));
    }
    public function show($id)
    {
        $user = $this->user->find($id);  
        if(empty($user)){
            return $this->respondNotFound();
        }
        return response()->json($this->userTransformer->transform($user));
    }
    public function setRole($id, $index){
        $change = RoleUser::find($id);
        
        $change->role_id = Role::where('name',$index)->first()->id;
        $change->save();
        
        return response()->json(['error' => false, 'message' => 'Role telah diubah']);
    }
    public function setStatus($id){
        $userstatus = $this->user->find($id);
        
        if($userstatus['is_verified']=='1'){
            $userstatus->is_verified = 0;
            $userstatus->save();
        }else{
            $userstatus->is_verified = 1;
            $userstatus->save();
        }
        return response()->json(['error' => false, 'message' => 'Status telah diubah']);
    }
    public function getUsers(){
        if (Laratrust::hasRole('admin')) {
            $users = User::all();
            return response()->json($this->userTransformer->transformCollection($users));
        }
        else{
            return response()->json(['error' => true, 'message' => 'Sorry you are not permitted']);
        }
    }
    public function destroy($id)
    {
        $user = $this->user->find($id);
        if(empty($user)){
            return $this->respondNotFound();
        }
        try{            
            $user->delete();
        }catch(\Exception $e){
             if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
            }
            return response()->json(['error' => true, 'message' => 'There is problem on server']);
        }
        

        return response()->json(['error' => false, 'message' => 'User success deleted']);
  
    }
    public function update(UserRequestEdit $request, $id)
    {
        $user = User::find($id);

        if(empty($user)){
            return $this->respondNotFound();
        }
        // $password = Hash::make($request->password);
        // if($password!=$user->password){
        //     return response()->json(['error' => true, 'message' => 'Password lama tidak sesuai']);
        // }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->unit_id = $request->unit_id;
        if($request->password!=null){
            $user->password = Hash::make($request->password);
        }
        
        // if ($request->hasFile('foto')) {
        //     // Mengambil proposal yang diupload berikut ektensinya
        //     $filename = null;
        //     $uploaded_upload = $request->file('foto');
        //     $extension = $uploaded_upload->getClientOriginalExtension();
        //     // Membuat nama file random dengan extension
        //     $filename = md5(time()) . '.' . $extension;
        //     // Menyimpan proposal ke folder public/proposal
        //     $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img/user';
        //     $uploaded_upload->move($destinationPath, $filename);
        //     // Hapus proposal lama, jika ada
        //     if ($user->avatar) {
        //         $old_upload = $user->avatar;
        //         $filepath = public_path() . DIRECTORY_SEPARATOR . 'img/user' . DIRECTORY_SEPARATOR . $user->avatar;
        //         try {
        //             File::delete($filepath);
        //         } catch (FileNotFoundException $e) {
        //             // File sudah dihapus/tidak ada
        //         }
        //     }
        //     // Ganti field proposal dengan proposal yang baru
        //     $user->avatar = $filename;
        //     $user->save();
        // }
        try{            
            $user->save();
        }catch(\Exception $e){
            if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
            }
            
            return response()->json(['error' => true, 'message' => 'There is problem on server']);
        }

        return response()->json(['error' => false, 'message' => 'User successfully updated']);
    }

    
}
