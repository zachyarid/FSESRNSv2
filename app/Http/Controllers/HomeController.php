<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Libraries\FSEData;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $now = Carbon::now();

        if (empty(session('monthyear')))
        {
            session()->put('monthyear', $now->format('Y-m'));
        }
        if (empty(session('month')))
        {
            session()->put('month', $now->format('m'));
        }
        if (empty(session('year')))
        {
            session()->put('year', $now->format('Y'));
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'pageTitle' => 'Dashboard',
            'grantedAccess' => \Auth::user()->sharedSubscriptions,
            'subscriptions' => \Auth::user()->subscriptions,
        ];

        return view('home', $data);
    }

    public function profile()
    {
        $data = [
            'pageTitle' => 'My Profile',
            'user' => Auth::user(),
        ];

        return view('pages.profile', $data);
    }

    // UpdateProfileRequest needs work
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->email = $request->email; # @todo validate email
        $user->personal_key = $request->personal_key;
        $user->service_key = $request->service_key;

        if (!empty($request->password))
        {
            $user->password = bcrypt($request->password);
        }

        if (!empty($request->fse_password))
        {
            $user->fse_password = encrypt($request->fse_password);
        }

        $user->save();

        return redirect(route('profile'))->with('success_message', 'User profile updated!');
    }

    public function setReportDate(Request $request)
    {
        $split = explode('-', $request->monthyear);

        session()->put('monthyear', $request->monthyear);
        session()->put('month', $split[1]);
        session()->put('year', $split[0]);

        return response()->json([
            'success' => true,
            'message' => 'Report Date Set: ' . $request->session()->get('monthyear'),
        ]);
    }

    public function test()
    {
        $fsedata = new FSEData();

        $payments = $fsedata->getPaymentsByMonth('IIPMIJSTPW', 'WGOB990L59');

        foreach ($payments['message'] as $p)
        {
            echo $p->Id . '<br>';
        }

    }

    public function logout()
    {
        Auth::logout();

        return redirect('/login');
    }
}
