<?php

namespace App\Http\Controllers\Auth;

use App\Address;
use App\Driver;
use App\Restaurant;
use App\User;
// use DB;
// use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Validation\Rule;
// use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [

            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'street1' => 'required|string|max:50',
            'street2' => 'nullable|string|max:50',
            'city' => 'required|string|max:50',
            'state' => 'required|string|max:50',
            'zip' => 'required|regex:/\b\d{5}\b/',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',             // must be at least 8 characters in length
                'confirmed'
            ],
            'phone_number' => 'required|string|max:50',
            'type' => [
                'required',
                Rule::in(['restaurant', 'driver'])
            ],

            // restaurant validation
            'provider' => 'required_if:type,restaurant|string|max:50',
            'CC_name' => 'required_if:type,restaurant|string|max:50',
            'CC_number' => 'required_if:type,restaurant|string|max:16',
            'CC_expiration' => 'required_if:type,restaurant|string|max:50',
            'CC_CVC' => 'required_if:type,restaurant|string|max:3',

            // driver validation
            'account_number' => 'required_if:type,driver',
            'account_routing' => 'required_if:type,driver',
            'car' => 'required_if:type,driver',
            'license_plate' => 'required_if:type,driver|string|max:10',
            'license_number' => 'required_if:type,driver|string|max:10',
            'license_expiration' => 'required_if:type,driver|string|max:50',
            'insurance_number' => 'required_if:type,driver|string|max:20',
        ]);
    }

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected function redirectTo()
    {
        if (auth()->user()->hasRole('admin')) {
                return 'admin/dashboard';
            } else if (auth()->user()->hasRole('driver')) {
                return 'driver/dashboard';
            } else if (auth()->user()->hasRole('restaurant')) {
                return 'restaurant/dashboard';
            } else return '/';
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        $address = Address::create([
            'name' => 'default',
            'street1' => $data['street1'],
            'street2' => $data['street2'],
            'city' => $data['city'],
            'state' => $data['state'],
            'postal' => $data['zip'],
        ]);

        $address = $this->addGoogleGeocode($address);
        $address->save();

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'email_verified_at' => now(),
            'password' => Hash::make($data['password']),
            'phone_number' => $data['phone_number'],
            'type' => $data['type'],
            'address_id' => $address->id,
        ]);

        if ($user->type == 'restaurant') {
                Restaurant::create([
                    'user_id' => $user->id,
                    'provider' => $data['provider'],
                    'CC_name' => $data['CC_name'],
                    'CC_number' => $data['CC_number'],
                    'CC_expiration' => $data['CC_expiration'],
                    'CC_CVC' => $data['CC_CVC'],
                ]);

                $user->assignRole('restaurant');

            } else {
                Driver::create([
                    'user_id' => $user->id,
                    'location_id' => $address->id,
                    'account_number' => $data['account_number'],
                    'account_routing' => $data['account_routing'],
                    'totalEarnings' => 0,
                    'is_available' => true,
                    'car' => $data['car'],
                    'license_plate' => $data['license_plate'],
                    'license_number' => $data['license_number'],
                    'license_expiration' => $data['license_expiration'],
                    'insurance_number' => $data['insurance_number'],
                ]);

                $user->assignRole('driver');
            }

        return $user;
    }


    //public function showRegistrationForm()
    public function showRegistrationForm()
    {
        if (request()->get('type') == 'restaurant') {
                return view('auth.registerRestaurant');
            } else if (request()->get('type') == 'driver') {
                return view('auth.registerDriver');
            } else return redirect('/');
    }

    /**
     * Get lat and lng coords of newly registered user
     *
     * @param  array  $address
     * @return lat and lng coordinates
     */
    protected function addGoogleGeocode($address)
    {
        $geocode = \GoogleMaps::load('geocoding')
            ->setParam(['address' => $address->google_formatted_address])
            ->get();

        $response = json_decode($geocode);

        if ($response->status == 'OK') {
            $address->latitude = $response->results[0]->geometry->location->lat;
            $address->longitude = $response->results[0]->geometry->location->lng;
            $address->save();
        }

        return $address;
    }
}
