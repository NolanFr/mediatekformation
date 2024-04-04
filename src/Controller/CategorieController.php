<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class CategorieController extends AbstractController
{
    private $playlistRepository;
    private $formationRepository;
    private $categorieRepository;
    private $session;

    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRepository,
        SessionInterface $session
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
        $this->session = $session;
    }

    /**
     * @Route("/categorie", name="categorie")
     */
    public function categorie(): Response
    {
        $categories = $this->categorieRepository->findAll();

        return $this->render('pages/Categorie.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="suprrcategorie", methods={"POST"})
     */
    public function delete(Request $request, Categorie $categorie): Response
    {
        if ($this->categorieRepository->hasNoFormation($categorie)) {
            $this->categorieRepository->remove($categorie, true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        } else {
            $this->session->getFlashBag()->add('warning', 'La catégorie possède des formations, suppression annulée.');
        }
        return $this->redirectToRoute('categorie');
    }

    /**
     * @Route("/categories/ajouter", name="ajouter.categorie")
     */
    public function new(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categorie');
        }

        return $this->render('pages/formulaireCategorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
