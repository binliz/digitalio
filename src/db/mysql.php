<?php

class mysql implements \ConnectionInterface
{
    /** @var \mysqli */
    private $mysqli;

    public function __construct()
    {
        $host = getenv('WAIT_HOSTS')?getenv('WAIT_HOSTS'):'localhost:3308';
        $user = getenv('APP_MYSQL_USER')?getenv('APP_MYSQL_USER'):'testcase1';
        $password = getenv('APP_MYSQL_PASSWORD')?getenv('APP_MYSQL_PASSWORD'):'testcase1';
        $database = getenv('APP_MYSQL_DATABASE')?getenv('APP_MYSQL_DATABASE'):'testcase1';
        $this->mysqli = new mysqli($host, $user, $password, $database);
        $this->mysqli->set_charset('utf8mb4');
        $this->mysqli->select_db('testcase1');
    }

    public function selectCampaingns()
    {
        $resultArray = [];
        $resultData = $this->mysqli->query(
            "SELECT campaign.id,oP.threshold,oP.sourceEvent,oP.measuredEvent,oP.ratioThreshold,group_concat(pB.publisher_id) publisherBlacklist from campaign
left join optProps oP on oP.id = campaign.optPropsId
left join publisherBlacklist pB on campaign.id = pB.campaign_id
group by campaign.id limit 100000",
            MYSQLI_USE_RESULT
        );

        while ($object = $resultData->fetch_assoc()) {
            $props = OptimizationProps::__setState(
                [
                    'threshold' => (int)$object['threshold'],
                    'sourceEvent' => $object['sourceEvent'],
                    'measuredEvent' => $object['measuredEvent'],
                    'ratioThreshold' => (float)$object['ratioThreshold']
                ]
            );
            $publisherBlacklist = [];
            $blackList = explode(',',$object['publisherBlacklist']);
            if(is_array($blackList)){
                foreach($blackList as $item){
                    if(!is_numeric($item)){
                        continue;
                    }
                    $publisherBlacklist[] = (int)$item;
                }
            }
            $campaign = Campaign::__setState(['id'=>(int)$object['id'],'optProps'=>$props,'publisherBlacklist'=>$publisherBlacklist]);

            $resultArray[] = $campaign;
        }

        $resultData->free();

        return $resultArray;
    }

    public function getList(string $tablename, string $classname)
    {

        return [];
    }

    public function getEvents()
    {
        $resultArray = [];
        $resultData = $this->mysqli->query(
            "SELECT * from Event",
            MYSQLI_USE_RESULT
        );

        while ($object = $resultData->fetch_assoc()) {

            $resultArray[] = new Event($object['campaign_id'],$object['publisher_id'],$object['type']);
        }
        $resultData->free();

        return $resultArray;
    }
}
