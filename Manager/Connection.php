<?php
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 2019-03-25
 * Time: 13:34
 */

namespace Cirykpopeye\GoogleBusinessClient\Manager;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class Connection
{
    const API_ENDPOINT = 'https://google.comsa.be/google-integration/';

    /**
     * @var Client
     */
    private $client;

    /**
     * Connection constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'domain' => $_SERVER['SERVER_NAME']
            ]
        ]);
    }

    public function getAccounts()
    {
        $accounts = $this->client->request('POST', self::API_ENDPOINT . 'accounts');
        return json_decode($accounts->getBody())->accounts;
    }

    public function getLocations($account)
    {
        $response = json_decode($this->client->post(
            self::API_ENDPOINT . 'locations',
            [
                'form_params' => [
                    'name' => $account
                ]
            ]
        )->getBody());
        if (is_object($response)) {
            return new JsonResponse($response->locations);
        }
        return new Response($response);
    }

    public function getReviews($account)
    {
        $reviews = $this->client->post(
            self::API_ENDPOINT . 'reviews',
            [
                'form_params' => [
                    'name' => $account
                ]
            ]
        )->getBody();
        return json_decode($reviews)->reviews;
    }

    public function getLocation($location)
    {
        $location = $this->client->post(
            self::API_ENDPOINT . 'location',
            [
                'form_params' => [
                    'name' => $location
                ]
            ]
        )->getBody();
        return json_decode($location);
    }

    public function updateLastUpdated()
    {
        return new JsonResponse(json_decode($this->client->post(
            self::API_ENDPOINT . 'last-update'
        )->getBody()));
    }
}
