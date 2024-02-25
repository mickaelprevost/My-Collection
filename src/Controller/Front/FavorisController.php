<?php

namespace App\Controller\Front;

use App\Entity\Collectible;
use App\Repository\CollectibleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavorisController extends AbstractController
{
  /**
     * Modify a collectible
     * @Route("/collectible/fav/{id<\d+>}", name="app_collectible_fav", methods={"GET", "POST"})
     */
    public function edit(Request $request, Collectible $collectible = null, CollectibleRepository $collectibleRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
      $object= $collectible->getUserId()->getId();
      $user = $this->getUser()->getId();
      if ($object === $user) {
        if($collectible->isFavorite() !== true) {
        $collectible->setFavorite(true);
        $entityManager->persist($collectible);
        $entityManager->flush();

        $this->addFlash('success', 'Favoris ajouté avec succés');
        return $this->redirectToRoute('app_collectible_show', ['id' => $collectible->getId()]);
      } else {
        $this->addFlash('danger', 'Ce collectible fait déjà parti de vos favoris');
        return $this->redirectToRoute('app_collectible_show', ['id' => $collectible->getId()]);
      }} else {
        $this->addFlash('danger', 'Vous ne possédez pas de collectible ayant cette référence');
        return $this->redirectToRoute('app_home');
      }
    }

    /**
     * Dsiplay favorites list
     * @Route("/favoris/list", name="app_favoris_index", methods={"GET"})
     */
    public function index(CollectibleRepository $collectibleRepository): Response
    {
        $user = $this->getuser()->getId();
        $userCollectibles = $collectibleRepository->findBy(['userId' => $user]);
        $userFavorites = [];
        foreach ($userCollectibles as $collectibles) {
          if ($collectibles->isFavorite() == '1') {
          $userFavorites[] = $collectibles;
          }
        }

        return $this->render(
            'front/favoris/index.html.twig',
            [
                'favorisList' => $userFavorites
            ]
        ); 
    }

    /**
     *
     * delete a favorite
     * @Route("/favoris/remove/{id<\d+>}",name="app_favoris_remove", methods={"POST"})
     * @return Response
     */
    public function remove(Collectible $collectible, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $collectible->setFavorite(false);
        $entityManager->persist($collectible);
        $entityManager->flush();

        return $this->redirectToRoute("app_favoris_index");
    }
  }