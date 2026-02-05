<?php

namespace Davion190510\MiniCRUDGenerator;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Davion190510\MiniCRUDGenerator\Skeleton\SkeletonClass
 */
class MiniCRUDGeneratorFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mini-curd-generator';
    }
}
