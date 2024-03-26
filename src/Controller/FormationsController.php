<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\EntityManagerInterface;
/**
 * Controleur des formations
 *
 * @author emds
 */
class FormationsController extends AbstractController {

    /**
     * 
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * 
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    const FORMATION = "pages/formations.html.twig";
    
    
    function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * @Route("/formations", name="formations")
     * @return Response
     */
    public function index(): Response{
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/formations/tri/{champ}/{ordre}/{table}", name="formations.sort")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sort($champ, $ordre, $table=""): Response{
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }     
    
    /**
     * @Route("/formations/recherche/{champ}/{table}", name="formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATION, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }  
    
    /**
     * @Route("/formations/formation/{id}", name="formations.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $formation = $this->formationRepository->find($id);
        return $this->render(self::FORMATION, [
            'formation' => $formation
        ]);        
    }
    
    /**
     * @Route("/formations/delete/{id}", name="formations.delete", methods={"POST"})
     */
    public function delete(Request $request, Formation $formation): Response{
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($formation);
    $entityManager->flush();
    return $this->redirectToRoute('formations');
    }
    
    /**
     * @Route("/formations/new", name="formations.new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();

            // Rediriger vers la page des formations après l'ajout d'une nouvelle formation
            return $this->redirectToRoute('formations');
        }

        return $this->render('pages/formulaire.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
    * @Route("/formation/modifier/{id}", name="modifierFormation")
    */
    public function modifierFormation(Request $request,EntityManagerInterface $entityManager, FormationRepository $formationRepository, $id){
        $formation = $formationRepository->findById($id);
        $form = $this->createForm(FormationType::class, $formation);
        $form->add('published_at', DateType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(), // Valeur par défaut
                'required' => false, // Rendre le champ non requis
                'disabled' => true,
        ]);

            // Désactiver le champ 'video_id'
        $form->add('video_id', FileType::class, [
                'label' => 'Vidéo (non modifiable)',
                'required' => false,
                'mapped' => false,
                'attr' => ['accept' => 'video/mp4,video/x-matroska'],
                'disabled' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formation);
            $entityManager->flush();
            return $this->redirectToRoute('formations');
        }
        return $this->render('pages/formulaire.html.twig', [
                'form' => $form->createView(),
        ]);
    }
    
    
}
//empty new line
