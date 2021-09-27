<?php

namespace Models;

use Framework\Core\Model;

class City extends Model{   
   /**
    * Table primary key
    *
    * @var string
    */
   protected static $primaryKey = "City_Id";

   // public $timestamps = false;

   /**
    * List of all table column
    *
    * @var array
    */
   protected $attributes = ['Name', 'CountryCode', 'District', 'Population'];


   public function country() {
      return $this->belongsTo(Country::class, 'CountryCode', 'Code');
   }

  
}
