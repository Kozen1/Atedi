<?php

namespace App\Service;

use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiService
{
    private string $apiUrl;
    private string $apiUrlCustomer;
    private string $apiKey;
    private HttpClientInterface $client;

    public function __construct(string $apiUrl, string $apiKey, string $apiUrlCustomer, HttpClientInterface $client)
    {
        $this->apiUrl = $apiUrl;
        $this->apiUrlCustomer = $apiUrlCustomer;
        $this->apiKey = $apiKey;
        $this->client = $client;
    }

    public function createInvoice(string $customer_name, array $invoiceData, int $clientId): array
    {
        $clientSearch = $this->getClientByName($customer_name);
        
        if (!$clientSearch) {
            // Création d'un nouveau client s'il n'existe pas
            $newClient = [
                "name" 			=> "Noam Leroux",
                "email"			=> "nono@gmail.com",
                "client" 		=> "1",
                "code_client"	=> "-1"
            ];
            dump("Avant création du client");
            dump("apiUrlCustomer = " . $this->apiUrlCustomer);
            // $newClientResult = $this->callAPI("POST", $this->apiKey, $this->apiUrlCustomer."thirdparties", json_encode($newClient));
            $this->client->request("POST", $this->apiUrlCustomer . "thirdparties", ['json' => $newClient, 'headers' => ["DOLAPIKEY" => $this->apiKey, 'Accept' => 'application/json']]);
            dump("Après création du client");
            // $newClientResult = json_decode($newClientResult, true);
            // $clientDoliId = $newClientResult;
        } else {
            // Utilisation du client existant
            $clientDoliId = $clientSearch[0]["id"];
        }

        // Ajout de l'ID du client à la facture
        $invoiceData = [
            "socid" => $clientId,
            "type" => "0",
            "note_private" => "Facture générée par Atedi",
            "line" => [
              [
                  "desc" => "Vente d'un disque dur pour votre ordinateur sous Windows",
                  "subprice" => "50.0",
                  "qty" => "1",
                  "tva_tx" => "20.0",
                  "fk_product" => "14"
              ]   
            ]
          ];
        dump($invoiceData);
        // Envoi de la facture à Dolibarr
        dump("Avant création de la facture");
        // $createInvoiceResult = $this->callAPI("POST", $this->apiKey, $this->apiUrl."invoices", json_encode($invoiceData));
        $this->client->request("POST", $this->apiUrl . "invoices", ['json' => $invoiceData, 'headers' => ["DOLAPIKEY" => $this->apiKey, 'Accept' => 'application/json']]);
        dump("Après création de la facture");
        // $createInvoiceResult = json_decode($createInvoiceResult, true);

        // return $createInvoiceResult;
    }

    // Recherche le client par son nom
    public function getClientByName(string $customer_name): ?array
    {
        dump("Avant recherche du client");
        dump($this->apiKey);
        dump("apiUrlCustomer = " . $this->apiUrlCustomer);
        // Appelle la méthode callAPI en envoyant les paramètres nécessaires pour rechercher le client par son nom
        $clientSearch = json_decode($this->callAPI("GET", $this->apiKey, $this->apiUrlCustomer."thirdparties", [
            "sortfield" => "t.rowid",
            "sortorder" => "ASC",
            "limit" => "1",
            "mode" => "1",
            "sqlfilters" => "(t.nom:=:'".$customer_name."')"
        ]), true);
        dump("Après recherche du client");
        // Si un client est trouvé, retourne le premier résultat
        return $clientSearch ? $clientSearch[0] : null;
    }

    // Appel l'API avec une méthode HTTP donnée et renvoie la réponse en tant que chaîne de caractères
    private function callAPI(string $method, string $apiKey, string $url, $data = false): string
    {
        // Crée une instance de HttpClient avec l'API key et le Content-Type spécifiés dans les headers
        $httpClient = HttpClient::create([
            'headers' => [
                'DOLAPIKEY' => $apiKey,
                'Content-Type' => 'application/json',
            ]
        ]);

        // Définit les options de la requête avec les données passées en paramètre
        $options = [
            'query' => $data
        ];

        // Envoie la requête à l'API avec la méthode HTTP spécifiée et les options définies précédemment
        $response = $httpClient->request($method, $url, $options);
        dump($response);

        // Si le code de statut HTTP n'est pas 200, lance une exception avec un message d'erreur
        if ($response->getStatusCode() !== 200) {
            $response = 0;
        }

        // Renvoie la réponse de l'API en tant que chaîne de caractères
        return $response;
    }
}


       
