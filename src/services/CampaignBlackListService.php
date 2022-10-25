<?php

class CampaignBlackListService
{
    private OptimizationProps $optimizationProps;
    private array $blacklist;
    /** @var PublisherEventsCounterDTO[] */
    private array $publisherEventsCounter = [];

    /**
     * @param \OptimizationProps $optimizationProps
     * @param array|null $blackList
     */
    public function __construct(OptimizationProps $optimizationProps, array $blackList = null)
    {
        if (!$this->checkOptimizationProps($optimizationProps)) {
            throw new InvalidArgumentException('Optimization props error');
        }
        $this->optimizationProps = $optimizationProps;

        if (is_null($blackList)) {
            $blackList = [];
        }
        $this->blacklist = $blackList;
    }

    /**
     * @return array
     */
    public function resolveNewBlackListPublishers(): array
    {
        $newBlackList = [];
        foreach ($this->publisherEventsCounter as $publisherId => $publisherEventsCount) {
            if ($this->isBlackListValue($publisherEventsCount)) {
                $newBlackList[] = $publisherId;
            }
        }

        return array_merge($this->blacklist, $newBlackList);
    }

    /**
     * @param int $publisherId
     * @param string $eventType
     *
     * @return bool
     */
    public function resolvePublisherEventType(int $publisherId, string $eventType): bool
    {
        if ($this->isSourceEvent($eventType)) {
            $this->incSourceEvent($publisherId);

            return true;
        }

        if ($this->isMeasuredEvent($eventType)) {
            $this->incMeasuredEvent($publisherId);

            return true;
        }

        return false;
    }

    /**
     * @param int $publisherId
     *
     * @return bool
     */
    public function isPublisherInBlackList(int $publisherId): bool
    {
        if (!in_array($publisherId, $this->blacklist)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function isSourceEvent(string $type): bool
    {
        if ($type !== $this->optimizationProps->sourceEvent) {
            return false;
        }

        return true;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function isMeasuredEvent(string $type): bool
    {
        if ($type !== $this->optimizationProps->measuredEvent) {
            return false;
        }

        return true;
    }

    /**
     * @param int $publisherId
     *
     * @return void
     */
    private function incSourceEvent(int $publisherId)
    {
        $publisherData = $this->getPublisherData($publisherId);
        $publisherData->sourceEvent += 1;
        $this->setPublisherData($publisherId, $publisherData);
    }

    /**
     * @param int $publisherId
     *
     * @return void
     */
    private function incMeasuredEvent(int $publisherId)
    {
        $publisherData = $this->getPublisherData($publisherId);
        $publisherData->measuredEvent += 1;
        $this->setPublisherData($publisherId, $publisherData);
    }

    /**
     * @param int $publisherId
     *
     * @return bool
     */
    private function isPublisherDataCreated(int $publisherId): bool
    {
        if (!array_key_exists($publisherId, $this->publisherEventsCounter)) {
            return false;
        }

        return true;
    }

    /**
     * @param int $publisherId
     * @param \PublisherEventsCounterDTO $publisherData
     *
     * @return void
     */
    private function setPublisherData(int $publisherId, PublisherEventsCounterDTO $publisherData): void
    {
        $this->publisherEventsCounter[$publisherId] = $publisherData;
    }

    /**
     * @param int $publisherId
     *
     * @return \PublisherEventsCounterDTO
     */
    private function getPublisherData(int $publisherId): PublisherEventsCounterDTO
    {
        if (!$this->isPublisherDataCreated($publisherId)) {
            $this->setPublisherData($publisherId, new PublisherEventsCounterDTO());
        }

        return $this->publisherEventsCounter[$publisherId];
    }

    /**
     * @param \PublisherEventsCounterDTO $publisherEventsCount
     *
     * @return bool
     */
    private function isBlackListValue(PublisherEventsCounterDTO $publisherEventsCount): bool
    {
        if ($publisherEventsCount->sourceEvent === 0) {
            return false;
        }

        if ($publisherEventsCount->sourceEvent < $this->optimizationProps->threshold) {
            return false;
        }

        return ($publisherEventsCount->measuredEvent / $publisherEventsCount->sourceEvent) < $this->optimizationProps->ratioThreshold;
    }

    /**
     * @param \OptimizationProps $optimizationProps
     *
     * @return bool
     */
    private function checkOptimizationProps(OptimizationProps $optimizationProps): bool
    {
        if ($optimizationProps->ratioThreshold < 0) {
            return false;
        }

        if ($optimizationProps->ratioThreshold > 1) {
            return false;
        }
        if ($optimizationProps->sourceEvent === $optimizationProps->measuredEvent) {
            return false;
        }

        return true;
    }

}
