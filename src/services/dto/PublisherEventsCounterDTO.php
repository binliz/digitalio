<?php

class PublisherEventsCounterDTO
{
    public int $sourceEvent;
    public int $measuredEvent;

    public function __construct(int $sourceEvent = 0, int $measuredEvent = 0)
    {
        $this->sourceEvent = $sourceEvent;
        $this->measuredEvent = $measuredEvent;
    }
}
