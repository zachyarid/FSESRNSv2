<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAccess\NewAdditionalUserAccessRequest;
use App\User;
use Illuminate\Support\Facades\DB;

class UserAccessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'pageTitle' => 'My Additional User Access',
            'useraccess' => \Auth::user()->grantedSubscriptions,
        ];

        return view('pages.useraccess.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'Add Additional User Access'
        ];

        return view('pages.useraccess.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewAdditionalUserAccessRequest $request)
    {
        $user = User::where('username', $request->username)->first();

        if ($user->id !== \Auth::id())
        {
            $user->sharedSubscriptions()->attach($request->subscription, ['granted_by' => \Auth::id()]);

            return redirect()->route('useraccess.index')->with('success_message', 'Additional User Access has been added!');
        }
        else {
            return back()->with('fail_message', 'You cannot grant user access to yourself.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($sid, $uid)
    {
        $res = DB::table('subscription_user')
            ->where([
                ['subscription_id', '=', $sid],
                ['user_id', '=', $uid]
            ])
            ->delete();

        return redirect()->route('useraccess.index')->with('success_message', 'User access removed.');
    }
}
