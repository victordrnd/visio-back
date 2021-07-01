<?php

namespace Http\Controllers;

use Models\City;
use Framework\Core\Http\Request;
use Http\Requests\ShowCityRequest;

class CityController extends Controller
{


    /**
     * Display city with specified id
     * 
     * @param int $id
     * @return void
     */
    public function show(int $id)
    {
        $city = City::where("City_Id", $id)->with('country.capital')->first();
       
        return $city;
    }


    /**
     * Create a new City from form.
     *
     * @param Request $req
     * @return void
     */
    public function create(Request $req){
        $city = City::create([
            'name' => $req->name,
            'CountryCode' => $req->countrycode,
            'district' => $req->district,
            'population' => $req->population
        ]);
        return response()->json($city);
    }

    /**
     * Update city with specified id
     *
     * @param Request $req
     * @param integer $id
     * @return void
     */
    public function update(Request $req, int $id)
    {
        $city = City::find($id);
        $city->update([
            'Name' => $req->name,
            'CountryCode' => $req->countryCode,
            'District' => $req->district,
            'Population' => $req->population
        ]);
        return response()->json($city);
    }


    /**
     * Delete City with specified id
     *
     * @param [type] $id
     * @return void
     */
    public function delete(int $id)
    {
        $city = City::find($id);
        $city->remove();
        return response()->json(['success' => true]);
    }
}
