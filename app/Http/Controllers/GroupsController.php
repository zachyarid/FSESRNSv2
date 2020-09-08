<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\Groups\EditGroupRequest;
use App\Jobs\GetGroupInfo;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'pageTitle' => 'My Groups',
            'groups' => \Auth::user()->groups->where('is_active', 1),
        ];

        return view('pages.groups.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'Add Group'
        ];

        return view('pages.groups.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$fseData = new FSEData();
        //$groupInfo = $fseData->getGroupData($request->access_key, \Auth::user()->personal_key);

        $group = Group::updateOrCreate(
            ['user_id' => \Auth::id(),
            'type' => 'Group',
            'access_key' => $request->access_key],
            ['is_active' => 1]
        );

        // Queue job
        dispatch(new GetGroupInfo($group->id));

        return redirect()->route('groups.index')->with('success_message', 'Group Added! Group data will be populated in a few minutes.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        $this->authorize('view', $group);

        $data = [
            'pageTitle' => 'Edit Group',
            'group' => $group
        ];

        return view('pages.groups.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EditGroupRequest $request
     * @param  \App\Group $group
     * @return \Illuminate\Http\Response
     */
    public function update(EditGroupRequest $request, Group $group)
    {
        $this->authorize('update', $group);

        $group->name = $request->group_name;
        $group->access_key = $request->access_key;
        $group->owner = $request->group_owner;

        $group->save();

        return redirect()->route('groups.index')->with('success_message', 'Group has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);

        // Check for existing active subscriptions
        if (count($group->subscription) == 1)
        {
            return response()->json([
                'success' => false,
                'message' => 'Unable to remove group. There is an active subscription with this group. Please cancel the subscription.'
            ]);
            //return redirect('/groups')->with('fail_message', 'Unable to remove group. There is an active subscription with this group. Please cancel the subscription.');
        }
        else
        {
            if ($group->type == 'Group') {
                $group->is_active = 0;
                $group->save();

                //return redirect('/groups')->with('success_message', 'Group Removed!');
                return response()->json([
                    'success' => true,
                    'message' => $group->save()
                ]);
            }
        }
    }
}
