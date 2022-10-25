<?php

class EventsDataSource implements DbInterface
{
    use DbImplements;
    /**
     * @param string $from
     *
     * @return \Event[]
     */
    public function getEventsSince(string $from): array
    {
        return $this->generateMockEvents(100);
    }

    /**
     * @param int $count
     *
     * @return \Event[]
     */
    private function generateMockEvents(int $count): array
    {
        return $this->dbService->getEvents();;
    }
}
