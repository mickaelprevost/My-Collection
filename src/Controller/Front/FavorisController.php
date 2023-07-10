<?php

namespace App\Controller\Front;

use App\Entity\Collectible;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FavoritesManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavorisController extends AbstractController
{
    /**
     * Dsiplay favorites list
     * @Route("/favoris/list", name="app_favoris_index", methods={"GET"})
     */
    public function index(FavoritesManager $favoritesManager): Response
    {
        $favoritesList = $favoritesManager->list();

        return $this->render(
            'front/favoris/index.html.twig',
            [
                "favorisList" => $favoritesList
            ]
        );
    }

    /**
     * Add a collectible to favorites
     * @Route("/favoris/new/{id<\d+>}", name="app_favoris_add", methods={"GET"})
     *
     * @return Response
     */
    public function add(Collectible $collectible, FavoritesManager $favoritesManager): Response
    {
        if ($collectible === null) {
            throw $this->createNotFoundException('objet non trouvé.');
        }

        $infoFlashMessage = $favoritesManager->add($collectible);

        $this->addFlash($infoFlashMessage["type"], $infoFlashMessage["message"]);

        // when the collectible is added to favorites we can redirect the user
        return $this->redirectToRoute('app_collectible_show', ['id' => $collectible->getId()]);
    }

    /**
     *
     * delete a favorite
     * @Route("/favoris/remove/{id<\d+>}",name="app_favoris_remove", methods={"POST"})
     * @return Response
     */
    public function remove(Collectible $collectible, FavoritesManager $favoritesManager, Request $request): Response
    {

        if ($collectible === null) {
            throw $this->createNotFoundException('Objet non trouvé.');
        }

        $submittedToken = $request->request->get('token');

        // 'delete-favoris' is the same value used in the template to generate the token
        if ($this->isCsrfTokenValid('delete_favoris', $submittedToken)) {

            $favoritesManager->remove($collectible);

            $this->addFlash('success', 'Favoris supprimé avec succès.');
        }

        return $this->redirectToRoute("app_favoris_index");
    }

    /**
     * Delete all favorites at once
     * @Route("/favoris/remove",name="app_favoris_remove_all", methods={"POST"})
     *  
     * @return Response
     */
    public function removeAll(FavoritesManager $favoritesManager, Request $request): Response
    {
        $submittedToken = $request->request->get('token');

        // 'delete-favoris' is the same value used in the template to generate the token
        if ($this->isCsrfTokenValid('delete_Allfavoris', $submittedToken)) {
            $favoritesManager->removeAll();

            $this->addFlash('success', 'Tous les favoris ont été supprimés avec succès.');
        }
        return $this->redirectToRoute("app_favoris_index");
    }
}
