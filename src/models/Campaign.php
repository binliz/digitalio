<?php

class Campaign
{
    /** @var  OptimizationProps $optProps */
    private $optProps;

    /** @var  int */
    private $id;

    /** @var  array */
    private $publisherBlacklist;

    public static function __setState(array $array)
    {
        $class = new Campaign;
        $class->optProps = $array['optProps'];
        $class->id = $array['id'];
        $class->publisherBlacklist = $array['publisherBlacklist'];
        return $class;
    }

    public function getOptimizationProps()
    {
        return $this->optProps;
    }

    public function getBlackList()
    {
        return $this->publisherBlacklist;
    }

    public function saveBlacklist($blacklist)
    {
        // dont implement
    }

    public function getId(): int
    {
        return $this->id;
    }


}
