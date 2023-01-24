<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FirstController extends AbstractController
{   

    #[Route('/template' , name:'template')]
    public function template()
    {   
        

        return $this->render( view : 'template.html.twig');
    
    }


    #[Route('/first', name: 'first.app')]
    public function index(Request $request): Response
    {   
        $session = $request->getSession();

        if ($session->has('nbVisite')) {

            $nbreVisite = $session->get('nbVisite') + 1; 
            // $session->set('nbVisite', $nbreVisite);
        }else{
            $nbreVisite = 1;
        }

       $session->set('nbVisite', $nbreVisite);



       
        return $this->render('first/index.html.twig' , [
            'test1' => 'Bonjour',
            'test2' => 'Bonsoir',
        ]);

        // return new Response(
        //     content:"<head>
        //                 <title>Ma premiere page</title>
        //                 <body> <h1> Hello Drico </h1></body>
        //             </head>"
        // );
    }


    #[Route('/multiplication')]
    public function multiplication($nbre1, $nbre2)
    {   
        $resultat = nbre1 * nbre2;        
        return new Response (content : "<h1>$resultat</h1>");
    
    }




}


