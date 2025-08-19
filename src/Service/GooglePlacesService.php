<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GooglePlacesService
{
    private HttpClientInterface $client;
    private string $apiKey;

    public function __construct(HttpClientInterface $client, string $googleApiKey)
    {
        $this->client = $client;
        $this->apiKey = $googleApiKey;
    }

    public function findPlaceId(string $input): ?string
    {
        $url = 'https://maps.googleapis.com/maps/api/place/findplacefromtext/json';
        $response = $this->client->request('GET', $url, [
            'query' => [
                'input' => $input,
                'inputtype' => 'textquery',
                'fields' => 'place_id',
                'key' => $this->apiKey,
            ],
        ]);

        $data = $response->toArray();
        if (!empty($data['candidates'][0]['place_id'])) {
            return $data['candidates'][0]['place_id'];
        }

        return null;
    }

    public function getPlaceDetails(string $placeId, array $fields = ['photos']): ?array
    {
        $url = 'https://maps.googleapis.com/maps/api/place/details/json';
        $response = $this->client->request('GET', $url, [
            'query' => [
                'place_id' => $placeId,
                'fields' => implode(',', $fields),
                'key' => $this->apiKey,
            ],
        ]);

        $data = $response->toArray();
        return $data['result'] ?? null;
    }

    public function getPhotoUrl(string $photoReference, int $maxWidth = 400): string
    {
        return sprintf(
            'https://maps.googleapis.com/maps/api/place/photo?maxwidth=%d&photoreference=%s&key=%s',
            $maxWidth,
            $photoReference,
            $this->apiKey
        );
    }

}