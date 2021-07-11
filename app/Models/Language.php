<?php

namespace Models;

use Framework\Core\Model;

class Language extends Model
{

    /**
     * Table name
     *
     * @var string
     */
    protected static $table = "countrylanguage";


    /**
     * Table primary key
     *
     * @var string
     */
    protected static $primaryKey = "CountryLanguage_Id";

    /**
     * List of all table column
     *
     * @var array
     */
    protected static $attributes = [
        'CountryLanguage_Id', 'CountryCode', 'Language', 'IsOfficial', 'Percentage'
    ];
  
}
