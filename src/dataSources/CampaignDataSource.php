<?php

class CampaignDataSource implements DbInterface
{
    use DbImplements;
    /**
     * @return \Campaign[]
     */
    public function getCampaigns(): array
    {
        return $this->dbService->selectCampaingns();
    }
}
