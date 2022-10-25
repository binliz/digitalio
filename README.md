## docker build all apps
```bash
  docker-compose build

```
##

## Run App
***
```bash
 docker-compose up mysql 
 
 docker-compose up backend
```
*** created test for service CampaignBlackListService
and xml to run
```bash
php phpunit.phar 
```
created testDatabase with 10k Campaigns and 10k publisherBlackList

created 10 billion events

To calculate 10billion events used 2072mb of memory.
May be idea to list all campaings and events in one cycle is not good.


