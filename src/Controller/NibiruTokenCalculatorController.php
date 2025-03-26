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
        // TODO: Just use the Nibiru Token full image and small images for the rest of the cards
        $fullImageDir = $this->getParameter('kernel.project_dir') . '/public/images/cards/full/';
        $smallImageDir = $this->getParameter('kernel.project_dir') . '/public/images/cards/small/';

        if (!is_dir($fullImageDir)) {
            mkdir($fullImageDir, 0777, true);
        }
        if (!is_dir($smallImageDir)) {
            mkdir($smallImageDir, 0777, true);
        }

        foreach ($cards as $card) {
            $fullImage = $client->request('GET', 'https://images.ygoprodeck.com/images/cards/' . $card->getId() . '.jpg');
            $smallImage = $client->request('GET', 'https://images.ygoprodeck.com/images/cards_small/' . $card->getId() . '.jpg');

            file_put_contents($fullImageDir . $card->getId() . '_full.jpg', $fullImage->getContent());
            file_put_contents($smallImageDir . $card->getId() . '_small.jpg', $smallImage->getContent());

            $card->setFullImageURL('/images/cards/full/' . $card->getId() . '_full.jpg');
            $card->setSmallImageURL('/images/cards/small/' . $card->getId() . '_small.jpg');
            $entityManager->flush();
        }

        return new Response('Images uploaded!');
    }

    private function createCards(HttpClientInterface $client, EntityManagerInterface $entityManager): Response
    {
        $response = $client->request('GET', 'https://db.ygoprodeck.com/api/v7/cardinfo.php');
        $data = $response->toArray();

        $cardsData = $data['data'] ?? [];

        $count = 0;
        $batchSize = 50;

        foreach ($cardsData as $cardData) {
            if ($cardData['type'] === 'Skill Card' ||
                $cardData['type'] === 'Spell Card' ||
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
