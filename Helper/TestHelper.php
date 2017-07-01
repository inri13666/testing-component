<?php

namespace Akuma\Component\Testing\Helper;

abstract class TestHelper
{
    /**
     * @param string $className
     * @param string $annotationName
     *
     * @return bool
     */
    public static function isAnnotationExists($className, $annotationName)
    {
        $annotations = \PHPUnit_Util_Test::parseTestMethodAnnotations($className);

        return isset($annotations['class'][$annotationName]);
    }
}
