<?php
/**
 * User: Michael Kumar <m.kumar@abv.bg>
 * Date: 19.11.16
 * Time: 14:07
 */

namespace App\Services\FootballData;


use App\Exceptions\FootballDataException;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class FootballData
{
    public $client;

    public function __construct()
    {
        $this->client = new Client(
            [
                'base_uri' => env('FBD_URL'),
                'headers'  => [
                    'X-Auth-Token'       => env('FBD_TOKEN'),
                    'X-Response-Control' => 'full',
                ],
            ]
        );
    }

    public function getCompetitions($year = null)
    {
        $endpoint = 'competitions';

        if (!is_null($year)) {
            $endpoint .= '?year='.$year;
        }
        return $this->returnResponse($this->client->get($endpoint)->getBody()->getContents());
    }


    public function getFixturesByCompetition($id)
    {
        $endpoint = 'competitions/'.$id.'/fixtures';



        return $this->returnResponse($this->client->get($endpoint)->getBody()->getContents());
    }

    public function getFixtureData($id)
    {
        $endpoint = 'fixtures/'.$id;
        //        dd($this->client->get($endpoint)->getBody()->getContents());
        return $this->returnResponse($this->client->get($endpoint)->getBody()->getContents());
    }



    protected function returnResponse($data)
    {
        $response = json_decode($data);
        if ($response === null) {
            throw new FootballDataException('Not valid response from API!');
        }

        $response = new Collection($response);
        if (!is_null($response->get('fixtures'))) {
            $response->fixtures = new Collection($response->get('fixtures'));
        }

        return $response;
    }


}