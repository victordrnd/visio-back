<?php

namespace Models;

use Framework\Core\Model;

class Country extends Model
{




   /**
    * Table primary key
    *
    * @var string
    */
   protected static $primaryKey = "Country_Id";


   /**
    * List of all table column
    *
    * @var array
    */
   protected static $attributes = [
      'Code', 'Name', 'Continent', 'Region', 'SurfaceArea', 'IndepYear',
      'Population', 'LifeExpectancy', 'GNP', 'GNPOld', 'LocalName', 'GovernmentForm', 'HeadOfState', 'Capital', 'Code2', 'Image1', 'Image2'
   ];

   protected $hidden = ["Image1", "Image2"];
   
   public function cities(){
      return $this->hasMany(City::class, 'Code', 'CountryCode');
   }


   public function languages(){
      return $this->hasMany(Language::class, 'Code', 'CountryCode');
   }


   public function capital(){
      return $this->hasOne(City::class, 'Capital', 'City_Id');
   }

}
