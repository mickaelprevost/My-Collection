<?php

namespace App\Controller\Back;

use App\Entity\Universe;
use App\Form\UniverseType;
use App\Repository\UniverseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/universe")
 */
class UniverseController extends AbstractController
{
    /**
     * Display the universes list
     * @Route("/", name="app_back_universe_index", methods={"GET"})
     */
    public function index(UniverseRepository $universeRepository): Response
    {
        return $this->render('back/universe/index.html.twig', [
            'universes' => $universeRepository->findAll(),
        ]);
    }

    /**
     * Add a new universe
     * @Route("/new", name="app_back_universe_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UniverseRepository $universeRepository): Response
    {
        $universe = new Universe();
        $form = $this->createForm(UniverseType::class, $universe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $universeRepository->add($universe, true);

            return $this->redirectToRoute('app_back_universe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/universe/new.html.twig', [
            'universe' => $universe,
            'form' => $form,
        ]);
    }

    /**
     * Display details of an universe
     * @Route("/{id<\d+>}", name="app_back_universe_show", methods={"GET"})
     */
    public function show(Universe $universe): Response
    {
        return $this->render('back/universe/show.html.twig', [
            'universe' => $universe,
        ]);
    }

    /**
     * Modify a universe
     * @Route("/edit/{id<\d+>}", name="app_back_universe_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Universe $universe, UniverseRepository $universeRepository): Response
    {
        $form = $this->createForm(UniverseType::class, $universe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $universeRepository->add($universe, true);

            $this->addFlash('success', 'univers mis à jour.');
            return $this->redirectToRoute('app_back_universe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/universe/edit.html.twig', [
            'universe' => $universe,
            'form' => $form,
        ]);
    }

    /**
     * Remove a universe
     * @Route("/remove/{id<\d+>}", name="app_back_universe_delete", methods={"POST"})
     */
    public function delete(Request $request, Universe $universe, UniverseRepository $universeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $universe->getId(), $request->request->get('_token'))) {
            $universeRepository->remove($universe, true);
        }

        return $this->redirectToRoute('app_back_universe_index', [], Response::HTTP_SEE_OTHER);
    }
}
