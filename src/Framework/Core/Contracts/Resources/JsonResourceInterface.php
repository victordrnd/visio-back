<?php


namespace Framework\Core\Contracts\Resources;

use Framework\Core\Http\Request;

interface JsonResourceInterface{
    public function toArray(Request $req);
}
