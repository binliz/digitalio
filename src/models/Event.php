<?php

class Event
{
    /** @var string */
    private $type;
    /** @var int */
    private $campaignId;
    /** @var int */
    private $publisherId;
    /** @var int */
    private $ts;

    /**
     * @param int $campaignId
     * @param int $publisherId
     * @param string $type
     */
    public function __construct(int $campaignId, int $publisherId, string $type)
    {
        $this->publisherId = $publisherId;
        $this->campaignId = $campaignId;
        $this->type = $type;
        $this->ts = time();
    }

    public function getType(): string
    {
        // for example "install"
        return $this->type;
    }

    public function getTs(): int
    {
        return $this->ts;
    }

    public function getCampaignId(): int
    {
        return $this->campaignId;
    }

    public function getPublisherId(): int
    {
        return $this->publisherId;
    }
}
