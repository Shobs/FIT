<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends UserController
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the admin application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $address = \App\Address::where('id', $user->address_id)->first();

        return view('driver.pages.dashboard', compact('user','address'));
    }

    /**
     * Show the orders in table.
     *
     * @return void
     */
    public function show()
    {
        if (\Auth::user()->hasAnyRole('admin') && request()->is('driver*')) {
            return view('driver.pages.orders', ['orders' => \App\Order::orderBy('is_archived', 'asc')->paginate(10)]);
        } else {
            $driver = \App\Driver::where('user_id', auth()->id())->first();
            $driverID = $driver->id;
            return view('driver.pages.orders', ['orders' => \App\Order::where('driver_id', $driverID)->paginate(10)]);
        }
    }

    /**
     * Show the orders in table.
     *
     * @return void
     */
    public function updateLocation(Request $request)
    {

        $input = $request->all();

        $driver = \App\Driver::where('id', $request->input('driverID'))->first();

        $location = $driver->location()->first();

        // $location->latitude = $request->input(json_decode('result.lat');
        // $location->longitude = $request->input('result.lng');

        // if($location->save())
        // {
            $response =  ['success'=>'New geolocation saved!'];
            return response()->json($response, 200);
        // } else {
        //     $response =  ['error'=>'Could not save new geolocation!'];
        //     return response()->json($response, 200);
        // }
    }
}
