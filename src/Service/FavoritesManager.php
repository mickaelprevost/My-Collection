<?php

namespace App\Service;

use App\Entity\Collectible;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FavoritesManager
{
    /**
     * Service which handle request
     *
     * @var RequestStack
     */
    private $requestStack;  

    /**
     * constructor
     *
     * @param RequestStack $requestStack 
     */
    public function __construct(RequestStack $requestStack)
    {   
        // it is stocked in a property, in ordered to be used everywhere in the service
        $this->requestStack = $requestStack;
    }

    public function list()
    {
        $session = $this->requestStack->getSession();

        $favoris = $session->get("favoris");

        $favorisList = [];

        if ($favoris !== null) {
        foreach($favoris as $favori) {
        $favorisList[] = $favori;
        }
        } 

        return $favorisList;
    }

    /**
     * add a collectible in the favorite list
     *
     * @param Collectible $collectible
     * @return array ["type", "message"] to be used in a flashMessage
     */
    public function add(Collectible $collectible): array
    {
        $session = $this->requestStack->getSession();
        
        // we get the favorites list or an empty array if it is the first added to the list
        $favoris = $session->get("favoris", []);

        // if favorite already exists
        if (array_key_exists($collectible->getId(), $favoris)) {
            // error message
            return [
                "type" => "warning",
                "message" => 'Le favori est déjà présent dans la liste.'
            ];

        } else {

            // collectible added to favorite
            $favoris[$collectible->getId()] = $collectible;

            // session updated with the new array
            $session->set('favoris', $favoris);
            
            // success message for the user
            return [
                "type" => "success",
                "message" => $collectible->getName() . " a été ajouté à votre liste de favoris."
            ];
        }
    }

    /**
     * delete a favorite
     *
     * @param Collectible $collectible
     * @return array ["type", "message"] to be used in a flashMessage
     */
    public function remove(Collectible $collectible): array
    {
        $session = $this->requestStack->getSession();
        // we get the favorites list
        $favoris = $session->get("favoris", []);

        // favorite deleted at the id given
        unset($favoris[$collectible->getId()]);

        // update the session
        $session->set('favoris', $favoris);

        // success message for the user
        return [
            "type" => "success",
            "message" => $collectible->getName() . " a bien été supprimé"
        ];
    }
    /**
     * delete all favorites
     *
     * @return array ["type", "message"] to be used in a flashMessage
     */
    public function removeAll(): array
    {
        $session = $this->requestStack->getSession();
        $session->set("favoris", []);

        return [
            "type" => "success",
            "message" => "Votre liste de favoris a bien été vidée"
        ];
    }   
}