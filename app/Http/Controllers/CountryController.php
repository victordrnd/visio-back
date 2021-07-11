<?php

namespace Http\Controllers;

use Models\Country;
use Framework\Core\Http\Request;
// use Renderer;

class CountryController extends Controller
{

    /**
     * Show Country with specified id
     *
     * @param integer $id
     * @return void
     */
    public function show(int $id): void
    {
        $country = Country::find($id);
        $capital = $country->capital();
        $cities = $country->cities();
        $languages = $country->languages();
        $languageLabels = [];
        $percentages = [];
        foreach ($languages as $language) {
            $languageLabels[] = $language->getLanguage();
            $percentages[] = $language->getPercentage();
        }
        // echo Renderer::render('country/country.php', compact('country', 'cities', 'capital', 'languageLabels', 'percentages'));
    }

    /**
     * Display all country 
     * TODO: pagination
     *
     * @return void
     */
    public function showAll()
    {
        $title = "Pays";
        //TODO finish
        $countries = Country::where(1, 1)->with('capital')->get();
        return response()->json($countries);
        //echo Renderer::render('country/countries.php', compact("countries", "title"));
    }


    
    /**
     * List all countries from specified continent
     *
     * @param string $continent
     * @return void
     */
    public function findFromContinent(string $continent)
    {
        $title = $continent;
        $countries = Country::where('Continent',$continent)->with('capital')->get(5);
        return response()->json($countries);
    }


    public function create(Request $req){
        
        $country = Country::create([
            'Code' => $req->code,
            'Name' => $req->name,
            'Continent' => $req->continent,
            'Region' => $req->region,
            'SurfaceArea' => $req->surface,
            'IndepYear' => $req->dateindep,
            'Population' => $req->population,
            'LocalName' => $req->localname,
            'GovernmentForm' => $req->governmentform,
            'Code2' => $req->code2,
            'Image1' => $req->flag
        ]);
        header('location:/country/show/'.$country->Country_Id);

    }


    /**
     * Update country with specified id
     *
     * @param integer $id
     * @return void
     */
    public function update(Request $req, int $id): void
    {
        $country = Country::find($id);
        $country->update([
            'Name' => $req->name,
            'Continent' => $req->continent,
            'Region' => $req->region,
            'IndepYear' => $req->indepyear,
            'Population' => $req->population,
            'Capital' => $req->capital
        ]);

        header('location:/country/show/'.$country->Country_Id);
    }


    /**
     * Delete country with specified id and it's foreign key
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id): void
    {

        $country = Country::find($id);
        $cities = $country->cities();
        foreach ($cities as $city) {
            $city->remove();
        }
        $languages = $country->languages();
        foreach ($languages as $language) {
            $language->remove();
        }
        $country->remove();
        header('location:/');
    }




    /**
     * Display create country view
     *
     * @return void
     */
    public function createCountryView(){
        //echo Renderer::render('country/create.php');
    }
}
