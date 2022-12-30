<?php

namespace Ridouchire\RadioMetrics;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use voku\helper\HtmlDomParser;

class DataCollector
{
    public function __construct(
        private string $url = 'http://kugi.club:8000/'
    ) {
        $this->client = new Client([
            'timeout'  => 3.0
        ]);
    }

    public function getData(): array
    {
        /** @var Response */
        $response = $this->client->request('GET', $this->url);

        if ($response->getStatusCode() == 200) {
            return $this->parseData((string) $response->getBody());
        }

        return ['error' => 'no data'];
    }

    private function parseData(string $html): array
    {
        $dom = HtmlDomParser::str_get_html($html);

        /** @var string*/
        $listeners = $dom->find('.yellowkeys tbody tr', 4)->text;
        /** @var string */
        $track     = $dom->find('.yellowkeys tbody tr', 7)->text;
        $listeners = substr($listeners, 20);
        $track     = substr($track, 17);

        //TODO: имя плейлиста из имени потока, когда куги сурс-клиентом будет его устанавливать

        return [
            'listeners' => $listeners,
            'track'     => $track
        ];
    }
}
