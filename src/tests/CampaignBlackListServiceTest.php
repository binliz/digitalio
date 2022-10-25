<?php

require __DIR__ . '/../services/CampaignBlackListService.php';
require __DIR__ . '/../services/dto/PublisherEventsCounterDTO.php';
require __DIR__ . '/../models/OptimizationProps.php';
require __DIR__ . '/../models/Event.php';

use PHPUnit\Framework\TestCase;

class CampaignBlackListServiceTest extends TestCase
{

    /**
     * @dataProvider provideConstructData
     */
    public function testConstruct(
        OptimizationProps $OptimizationProps,
        array $blackList = null,
        $shouldInstance = null,
        $shouldError = null
    ) {
        try {
            $service = new CampaignBlackListService($OptimizationProps, $blackList);
            $this->assertInstanceOf($shouldInstance, $service);
        } catch (Exception $e) {
            $this->assertInstanceOf($shouldError, $e);
        }
    }

    /**
     * @dataProvider provideBlackListPublishersData
     */
    public function testResolvePublisherEventType(array $optProps, array $eventList)
    {
        $service = new CampaignBlackListService(
            OptimizationProps::__setState(
                $optProps
            ), []
        );
        foreach ($eventList as $eventCase) {
            $result = $service->resolvePublisherEventType($eventCase['publisherId'], $eventCase['event']);
            $this->assertSame($eventCase['result'], $result);
        }
    }

    /**
     * @dataProvider provideGetBlackListPublishers
     */
    public function testGetBlackListPublishers(array $optProps, array $eventList, array $containsPublishers)
    {
        $service = new CampaignBlackListService(
            OptimizationProps::__setState(
                $optProps
            ), []
        );
        foreach ($eventList as $eventCase) {
            $service->resolvePublisherEventType($eventCase['publisherId'], $eventCase['event']);
        }
        $blacklist = $service->resolveNewBlackListPublishers();
        $values = array_intersect($blacklist, $containsPublishers);

        $this->assertSame($values, $blacklist);
    }

    public function provideConstructData(): array
    {
        return [
            'goodInstanceWithoutBlackList' => [
                OptimizationProps::__setState(
                    ['threshold' => 1, 'sourceEvent' => 'test', 'measuredEvent' => 'test2', 'ratioThreshold' => 1]
                ),
                [],
                CampaignBlackListService::class
            ],
            'HasNotInstanceWithSameEvents' => [
                OptimizationProps::__setState(
                    ['threshold' => 1, 'sourceEvent' => 'test', 'measuredEvent' => 'test', 'ratioThreshold' => 1]
                ),
                [],
                null,
                InvalidArgumentException::class
            ],
            'HasNotInstanceErrorRatio1' => [
                OptimizationProps::__setState(
                    ['threshold' => 1, 'sourceEvent' => 'test', 'measuredEvent' => 'test', 'ratioThreshold' => 1.001]
                ),
                [],
                null,
                InvalidArgumentException::class
            ],
            'HasNotInstanceErrorRatio2' => [
                OptimizationProps::__setState(
                    ['threshold' => 1, 'sourceEvent' => 'test', 'measuredEvent' => 'test', 'ratioThreshold' => -0.1]
                ),
                [],
                null,
                InvalidArgumentException::class
            ]

        ];
    }

    public function provideBlackListPublishersData(): array
    {
        return [
            'measure greater source' => [
                'optProps' => [
                    'threshold' => 4,
                    'sourceEvent' => 'test',
                    'measuredEvent' => 'test2',
                    'ratioThreshold' => 0.6
                ],
                'events' => [
                    ['event' => 'test', 'publisherId' => 1, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 1, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 1, 'result' => true],
                ],
                'countPublishers' => 1
            ],
            'measure lower source' => [
                'optProps' => [
                    'threshold' => 4,
                    'sourceEvent' => 'test',
                    'measuredEvent' => 'test2',
                    'ratioThreshold' => 0.6
                ],
                'events' => [
                    ['event' => 'test', 'publisherId' => 1, 'result' => true],
                    ['event' => 'test', 'publisherId' => 1, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 1, 'result' => true],
                ]
            ],
            'measure eq source' => [
                'optProps' => [
                    'threshold' => 4,
                    'sourceEvent' => 'test',
                    'measuredEvent' => 'test2',
                    'ratioThreshold' => 0.6
                ],
                'events' => [
                    ['event' => 'test', 'publisherId' => 1, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 1, 'result' => true],
                ]
            ],
            'errorSource' => [
                'optProps' => [
                    'threshold' => 4,
                    'sourceEvent' => 'test',
                    'measuredEvent' => 'test2',
                    'ratioThreshold' => 0.6
                ],
                'events' => [
                    ['event' => 'test', 'publisherId' => 1, 'result' => true],
                    ['event' => 't est', 'publisherId' => 1, 'result' => false],
                    ['event' => 't est', 'publisherId' => 1, 'result' => false],
                ]
            ],
            'errorMeasure' => [
                'optProps' => [
                    'threshold' => 4,
                    'sourceEvent' => 'test',
                    'measuredEvent' => 'test2',
                    'ratioThreshold' => 0.6
                ],
                'events' => [
                    ['event' => 'test', 'publisherId' => 1, 'result' => true],
                    ['event' => 't est2', 'publisherId' => 1, 'result' => false],
                    ['event' => 't est2', 'publisherId' => 1, 'result' => false],
                ]
            ],
        ];
    }

    public function provideGetBlackListPublishers(): array
    {
        return [
            'measure1' => [
                'optProps' => [
                    'threshold' => 4,
                    'sourceEvent' => 'test',
                    'measuredEvent' => 'test2',
                    'ratioThreshold' => 0.6
                ],
                'events' => [
                    ['event' => 'test', 'publisherId' => 1, 'result' => true], // ignore in blacklist
                    ['event' => 'test', 'publisherId' => 1, 'result' => true],
                    ['event' => 'test', 'publisherId' => 1, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 1, 'result' => true],

                    ['event' => 'test', 'publisherId' => 2, 'result' => true], // in black list
                    ['event' => 'test', 'publisherId' => 2, 'result' => true],
                    ['event' => 'test', 'publisherId' => 2, 'result' => true],
                    ['event' => 'test', 'publisherId' => 2, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 2, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 2, 'result' => true],

                    ['event' => 'test', 'publisherId' => 3, 'result' => true], //not in black list
                    ['event' => 'test', 'publisherId' => 3, 'result' => true],
                    ['event' => 'test', 'publisherId' => 3, 'result' => true],
                    ['event' => 'test', 'publisherId' => 3, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 3, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 3, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 3, 'result' => true],

                    ['event' => 'test', 'publisherId' => 4, 'result' => true], //not in black list
                    ['event' => 'test', 'publisherId' => 4, 'result' => true],
                    ['event' => 'test', 'publisherId' => 4, 'result' => true],
                    ['event' => 'test', 'publisherId' => 4, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 4, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 4, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 4, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 4, 'result' => true],

                    ['event' => 'test', 'publisherId' => 5, 'result' => true], // in black list
                    ['event' => 'test', 'publisherId' => 5, 'result' => true],
                    ['event' => 'test', 'publisherId' => 5, 'result' => true],
                    ['event' => 'test', 'publisherId' => 5, 'result' => true],
                    ['event' => 'test', 'publisherId' => 5, 'result' => true],
                    ['event' => 'test', 'publisherId' => 5, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 5, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 5, 'result' => true],
                    ['event' => 'test2', 'publisherId' => 5, 'result' => true],
                ],
                'containsPublishers' => [2, 5]
            ],
        ];
    }
}
