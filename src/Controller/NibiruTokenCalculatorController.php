<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NibiruTokenCalculatorController extends AbstractController
{
    #[Route('/index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig');
    }
    #[Route('/create-cards')]
    public function createCards(HttpClientInterface $client, EntityManagerInterface $entityManager): Response
    {
        $response = $client->request('GET', 'https://db.ygoprodeck.com/api/v7/cardinfo.php');
        $data = $response->toArray();

        $cardsData = $data['data'] ?? [];

        $count = 0;
        $batchSize = 100;

        foreach ($cardsData as $cardData) {
            if ($cardData['type'] === 'Skill Card' ||
                $cardData['type'] === 'Spell Card' ||
                // TODO: Manually add continous trap cards that special summon themselves
                $cardData['type'] === 'Trap Card' ||
                $cardData['type'] === 'Token') {
                continue;
            }
            $card = new Card();
            // Map API data to your Card entity; adjust these setter calls based on your entity properties
            $card->setId($cardData['id']);
            $card->setName($cardData['name']);
            $card->setAtk($cardData['atk'] ?? 0);
            $card->setDef($cardData['def'] ?? 0);
            // TODO: Fetch and set the images as blobs
            $entityManager->persist($card);

            // Every 50 inserts, flush and clear
            if (($count % $batchSize) === 0) {
                $entityManager->flush();
                $entityManager->clear(); // frees memory by detaching all entities
            }
            $count++;
            printf("Inserted card %s\n", $card->getName());
            printf("Inserted number of cards %d\n", $count);
        }
        $entityManager->flush();
        $entityManager->clear();
        return new Response('Cards created!');
    }
}
