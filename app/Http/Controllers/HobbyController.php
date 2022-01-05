<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\UserHobby;
use App\Models\Hobby;
use App\Models\User;

class HobbyController extends Controller
{
    /**
     * Hobby Listing
     */
    public function index()
    {
        $userhobby = auth()->user()->userhobby;
        
        foreach($userhobby AS $key => $val) {
            $hobby = Hobby::where(['id' => $val->hobby_id])->first();

            $data[$key]['id'] =  $val->id;
            $data[$key]['hobby'] =  $hobby->name;
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Uodate
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hobby_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response['message'] =  'Validation error';
            $response['error'] =  $validator->errors();
            return response()->json(['data' => $response], 422);
        }
        
        $hobby = Hobby::where(['id' => $request->hobby_id])->first();
        if($hobby) {
            $user_hobby = auth()->user()->userhobby()->where('hobby_id', $request->hobby_id)->first();
            if(!$user_hobby) {
                $hobby = new UserHobby();
                $hobby->hobby_id = $request->hobby_id;
                
                if (auth()->user()->userhobby()->save($hobby)) {
                    return response()->json([
                        'success' => true,
                        'data' => $hobby->toArray()
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hobby not added'
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Hobby already added'
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Hobby not available'
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        $hobby = auth()->user()->userhobby()->find($id);
    
        if (!$hobby) {
            return response()->json([
                'success' => false,
                'message' => 'Hobby not found'
            ], 400);
        }
    
        if ($hobby->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Hobby deleted'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Hobby can not be deleted'
            ], 500);
        }
    }
    
    public function getuser(Request $request)
    {
        $hobby = Hobby::where(['id' => $request->hobby_id])->first();
        if (!$hobby) {
            return response()->json([
                'success' => false,
                'message' => 'Hobby not found'
            ], 400);
        }
        
        $userhobby = UserHobby::where(['hobby_id' => $request->hobby_id])->get();
        $data=[];
        foreach($userhobby AS $key => $val) {
            $user = User::where(['id' => $val->user_id])->first();
            $data[$key]['id'] =  $val->id;
            $data[$key]['user'] =  $user->name;
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
