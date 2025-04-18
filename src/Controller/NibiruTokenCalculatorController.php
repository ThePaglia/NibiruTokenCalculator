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

    private function uploadImages(HttpClientInterface $client, EntityManagerInterface $entityManager): Response
    {
        $cards = $entityManager->getRepository(Card::class)->findAll();

        $smallImageDir = $this->getParameter('kernel.project_dir') . '/public/images/cards/small/';
        if (!is_dir($smallImageDir)) {
            mkdir($smallImageDir, 0777, true);
        }

        $batchSize = 50;
        $count = 0;

        foreach ($cards as $card) {
            // Check if the card already exists in the database
            if ($card->getSmallImageURL() !== null) {
                continue; // Skip if the card already has a small image
            }
            $smallImage = $client->request('GET', 'https://images.ygoprodeck.com/images/cards_small/' . $card->getId() . '.jpg');

            file_put_contents($smallImageDir . $card->getId() . '_small.jpg', $smallImage->getContent());

            $card->setSmallImageURL('/images/cards/small/' . $card->getId() . '_small.jpg');
            printf("Card %s small image set\n", $card->getName());
            if ($count % $batchSize === 0) {
                $entityManager->flush();
                $entityManager->clear(); // frees memory by detaching all entities
            }
            $count++;
        }

        $entityManager->flush();
        $entityManager->clear(); // frees memory by detaching all entities
        return new Response('Images uploaded!');
    }

    private function createCards(HttpClientInterface $client, EntityManagerInterface $entityManager): Response
    {
        $response = $client->request('GET', 'https://db.ygoprodeck.com/api/v7/cardinfo.php');
        $data = $response->toArray();

        $cardsData = $data['data'] ?? [];

        $existingCardsIds = $entityManager->getRepository(Card::class)->findAllCardIds();

        $batchSize = 50;
        $count = 0;

        foreach ($cardsData as $cardData) {
            if ($cardData['type'] === 'Skill Card' ||
                $cardData['type'] === 'Spell Card' ||
                $cardData['type'] === 'Trap Card' ||
                $cardData['type'] === 'Token') {
                continue;
            }

            // Check if the card already exists in the database
            if (in_array($cardData['id'], $existingCardsIds, true)) {
                continue; // Skip if the card already exists
            }

            $card = new Card();
            // Map API data to your Card entity; adjust these setter calls based on your entity properties
            $card->setId($cardData['id']);
            $card->setName($cardData['name']);
            $card->setAtk($cardData['atk'] ?? 0);
            $card->setDef($cardData['def'] ?? 0);
            $entityManager->persist($card);

            // Every 50 inserts, flush and clear
            if (($count % $batchSize) === 0) {
                $entityManager->flush();
                $entityManager->clear(); // frees memory by detaching all entities
            }
            $count++;
            printf("Inserted card %s\n", $card->getName());
        }
        printf("New cards inserted: %d\n", $count);
        $entityManager->flush();
        $entityManager->clear();
        return new Response('Cards created!');
    }

    #[Route('/updateDB')]
    public function updateDB(HttpClientInterface $client, EntityManagerInterface $entityManager): Response
    {
        $this->createCards($client, $entityManager);
        $this->uploadImages($client, $entityManager);
        return new Response('Database updated!');
    }
}
