<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchUsersController extends Controller
{
    public function index(Request $request) {
         
        header('Access-Control-Allow-Origin: *');
        $terms = explode(' ', $request->input('q'));
        
        $rows = \DB::table('user_score')->select('user_id', 'score')
            ->groupBy('user_id', 'score');
            
        foreach($terms as $term) {
            $rows->orWhere('value', 'like', $term.'%');
        }

        $highestScoreUserIds = $rows->get()->groupBy('user_id')->map(function ($row) {
            return $row->sum('score');
        })->sortDesc()->slice(0, 5)->keys();

        
        $usersNames = \DB::table('demo_profile_values')
            ->whereIn('uid', $highestScoreUserIds)
            ->where('fid', 3)
            ->pluck('value');

         return response()->json($usersNames);
    }
}
