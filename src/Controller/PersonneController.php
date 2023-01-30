<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Service\Helpers;
use App\Form\PersonneType;
use Psr\Log\LoggerInterface;
use App\Service\MailerService;
use App\Service\PdfService;
use App\Service\UploaderServive;
use Symfony\Component\Mime\Email;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\DependencyInjection\Loader\Configurator\mailer;


//Prefixation des routes avec personne

#[Route('personne')]
class PersonneController extends AbstractController
{      

    
    public function __construct(private LoggerInterface $logger, private Helpers $helper)
    {}

    //Liste des personnes
    #[Route('', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine)
    {

        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();

        // dd($personnes);
        return $this->render('personne/personne.html.twig', [
            'personnes' => $personnes,
        ]);
    }

    /**
     * function pour Lister des personnes dont l'age min et l'age max sont defini 
     */
    #[Route('/all/age/{ageMin}/{ageMax}', name: 'personne.filter.age',  methods: ['GET', 'POST'])]
    public function personneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax) {

        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonneByAgeInterval($ageMin, $ageMax);

        // dd($personnes);
        return $this->render('personne/personne.html.twig', [
            'personnes' => $personnes,
        ]);
    }

    /**
     * function statistique des personnes dont l'age min et l'age max son defini 
     */
    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function statsPersonneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax) {
        
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsPersonneByAgeInterval($ageMin, $ageMax);

        // dd($stats);
        return $this->render('personne/stats.html.twig', [
            'stats' => $stats[0],
            'ageMax' => $ageMax,
            'ageMin' => $ageMin,
        ]);
    }

    /**
     * fonction de pagination
     */
    #[Route('/alls/{page?1}/{nbre?12}', name: 'personne.list.alls')]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbre) :Response
    {   
        // $helpers = new Helpers();

        // echo($this->helper->sayCc()); 

        $repository = $doctrine->getRepository(Personne::class);
        $nbrePersonne = $repository->count([]);
        $nbrePage = ceil($nbrePersonne / $nbre);
        // dd($nbrePage);

        $personnes = $repository->findBy([], [], $nbre, offset: ($page - 1) * $nbre);

        // dd($personnes);
        return $this->render('personne/personne.html.twig', [
            'personnes' => $personnes,
            'isPaginated' => true,
            'nbrePage' => $nbrePage,
            'page' => $page,
            'nbre' => $nbre
        ]);
    }


    //Detail des personnes
    /* #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(ManagerRegistry $doctrine, $id) {

        $repository = $doctrine->getRepository(Personne::class);
        $personne = $repository->find($id);

        //On verifie si la personne existe dans la base de donnée
        if (!$personne) {
            $this->addFlash(type: 'error', message:"La personne d'id $id n'existe pas dans la liste");

            return $this->redirectToRoute(route: 'personne.list');
        }

        // dd($personnes);
        return $this->render('personne/detail.html.twig', [
            'personne' => $personne,
        ]);
    } */

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null)
    {

        //On verifie si la personne existe dans la base de donnée
        if (!$personne) {
            $this->addFlash(type: 'error', message: "La personne n'existe pas dans la liste");

            return $this->redirectToRoute(route: 'personne.list');
        }

        // dd($personnes);
        return $this->render('personne/detail.html.twig', [
            'personne' => $personne,
        ]);
    }
    


    //Route pour la generation du pdf
    #[Route('/pdf/{id}', name: 'personne.pdf')]
    public function generatePdfPersonne(Personne $personne = null, PdfService $pdf)
    {   
        $html = $this->render('personne/detail.html.twig', [
            'personne' => $personne,
        ]);
        $pdf->showPdfFile($html);
    }



    /**
     * Function pour ajouter une personne
     */
    #[Route('/edit/{id?0}', name: 'personne.edit')]
    public function addPersonne(ManagerRegistry $doctrine, Personne $personne =null, Request $request, SluggerInterface $slugger,
    UploaderServive $uploaderServive,
    MailerService $mailer
    ): Response
    {
        /* $personne = new Personne();
        $personne->setFirstname('Rolande');
        $personne->setName('Adou');
        $personne->setAge('27');

        $entityManager->persist($personne);
        $entityManager->flush(); */

        $new = false;
        if (!$personne) {
            
            $new = true;
            $personne = new Personne();
        }
        // $personne est l'image de notre formulaire
        // $personne->setName('Cedric');
        $form = $this->createForm(PersonneType::class, $personne);

        $form->remove('createdAt');
        $form->remove('updatedAt');
        

        // Mon formulaire va aller traiter la requete
        $form->handleRequest($request);
        $form->getData();
        if ($form->isSubmitted() && $form->isValid()) { 
            $manager = $doctrine->getManager();
            //

            // Ajout de l'image
            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
              /*   $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('personne_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                } */

                $directory = $this->getParameter('personne_directory');

                // updates the 'photoname' property to store the PDF file name
                // instead of its contents
                $personne->setImage($uploaderServive->uploadFile($photo, $directory));
            }

            //
            $manager->persist($personne);
            // dd($personne);

            $manager->flush();  

            /* $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            // ->text('Sending emails is fun again!')
            ->html('<p> Notification </p>');  

            $mailer->send($email); */
            
            if($new){                
                $message = " a été ajouter avec succès";
            }else {
                $message = " a été mis à ajout avec succès";
            }


            $mailMessage  = $personne->getFirstname(). ' ' . $personne->getName(). ' '. $message;
            // dd($mailMessage);
            ($mailer->sendEmail(content:$mailMessage));

            $this->addFlash(type: 'success', message:$personne->getName(). $message);

            return $this->redirectToRoute(route: 'personne.list');

        }else {
            return $this->render('personne/add-personne.html.twig', [
                // createView() est une methode qui crée la vue associé à notre formulaire
                'form' => $form->createView(),
            ]);    
        }

        // dd($request);

       
        $this->addFlash(type: 'success', message: 'La personne a été mise à jour avec succès');

        return $this->redirectToRoute(route: 'personne.list.alls');
    }




    


    /**
     * function pour update une personne  
     */
    #[Route('/update/{id}/{name}/{firstname}/{age}', name: 'personne.update')]
    public function updatePersonne(ManagerRegistry $doctrine, Personne $personne = null, $name, $firstname, $age)
    {
        if ($personne) {
            $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);

            $manager = $doctrine->getManager();

            $manager->persist($personne);
            $manager->flush();

            $this->addFlash(type: 'success', message: 'La personne a été mise à jour avec succès');
        } else {

            $this->addFlash(type: 'error', message: "La personne n'existe pas dans la liste");
        }

        return $this->redirectToRoute(route: 'personne.list.alls');
    }


    /**
     * Function pour supprimer une personne
     */
    #[Route('/delete/{id}', name: 'personne.delete')]
    // null ici declanche une erreur propre à symfony lorsqu il ne trouve pas d'id propre à un element
    function deletePersonne(ManagerRegistry $doctrine, Personne $personne = null): RedirectResponse
    {
        if ($personne) {
            $manager = $doctrine->getManager();
            $manager->remove($personne);
            $manager->flush();

            $this->addFlash('success', message: 'La personne a été supprimé avec succès');
        } else {

            $this->addFlash('success', message: 'La personne est innexistante');
        }

        return $this->redirectToRoute(route: 'personne.list.alls');
    }
}
