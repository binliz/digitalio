<?php

class OptimizationProps
{
    public $threshold, $sourceEvent, $measuredEvent, $ratioThreshold;

    public static function __setState(array $array)
    {
        $class = new OptimizationProps;
        $class->threshold = $array['threshold'];
        $class->sourceEvent = $array['sourceEvent'];
        $class->measuredEvent = $array['measuredEvent'];
        $class->ratioThreshold = $array['ratioThreshold'];;
        return $class;
    }
}

