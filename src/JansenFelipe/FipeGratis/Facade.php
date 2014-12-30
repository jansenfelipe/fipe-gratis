<?php

namespace JansenFelipe\FipeGratis;

class Facade extends \Illuminate\Support\Facades\Facade {

    protected static function getFacadeAccessor() {
        return 'fipe_gratis';
    }

}
