<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TodoController extends AbstractController
{
    #[Route('/todo', name: 'todo')]
    public function index(Request $request): Response
    {   
        //Afficher notre tableau todo
        // sinon je l'initialise puis j'affiche
        
        $session = $request->getSession();

        if (!$session->has('todos')) {
            
            $todos = [
                'achat' => 'Un macBook',
                'cours' => 'Finaliser mon cours de Symfony',
                'correction' => 'Corrigé mes exercices',
            ];

            //Mettre le tableau dans la session
            $session->set('todos' , $todos);

            
        }

         // Si j'ai mon tableau de todo dans ma session je ne fait que l'afficher

        return $this->render('todo/index.html.twig');
    }


    #[Route('/todo/add/{name}/{content}', name: 'addTodo')]
    // #[Route('/todo/add/{name}/{content}', name: 'todo.add')]
    public function addTodo(Request $request, $name, $content){
        
        // recuperation de la session
        $session = $request->getSession();
        //On verifie si la variable todos existe dans la session
        if($session->has('todos')){

            //On verifie si on n'a une todo du même non
            $todos =  $session->get(name : 'todos');

            if (isset($todos[$name])) {

                $this->addFlash(type: 'error', message:"Le todo d'id $name existe déja dans la liste");
            
            }else{
                // Sinon on ajoute et on affiche un messsage de success;
                $todos[$name] = $content;
                // Mise à jour de la session
                $session->set('todos', $todos);
                $this->addFlash(type: 'success', message:"la liste d'id $name a été ajouté avec succès");          
                
            }

        }
        else{

            $this->addFlash(type: 'error', message:"la liste des todos n'est pas encore initialisée");
        }

        return $this->redirectToRoute(route:'todo');


    }


    #[Route('/todo/update/{name}/{content}', name: 'update.todo')]
    // #[Route('/todo/add/{name}/{content}', name: 'todo.add')]
    public function updateTodo(Request $request, $name, $content){
        
        // recuperation de la session
        $session = $request->getSession();
        //On verifie si la variable todos existe dans la session
        if($session->has('todos')){

            //On verifie si on n'a une todo du même non
            $todos =  $session->get(name : 'todos');

            if (!isset($todos[$name])) {

                $this->addFlash(type: 'error', message:"Le todo d'id $name n'existe pas dans la liste");
            
            }else{
                // Sinon on ajoute et on affiche un messsage de success;
                $todos[$name] = $content;
                // Mise à jour de la session
                $session->set('todos', $todos);
                $this->addFlash(type: 'success', message:"la liste d'id $name a été modofié avec succès");          
                
            }

        }
        else{

            $this->addFlash(type: 'error', message:"la liste des todos n'est pas encore initialisée");
        }

        return $this->redirectToRoute(route:'todo');

    }


    #[Route('/todo/delete/{name}', name: 'delete.todo')]
    // #[Route('/todo/add/{name}/{content}', name: 'todo.add')]
    public function deleteTodo(Request $request, $name){
        
        // recuperation de la session
        $session = $request->getSession();
        //On verifie si la variable todos existe dans la session
        if($session->has('todos')){

            //On verifie si on n'a une todo du même non
            $todos =  $session->get(name : 'todos');


            if (!isset($todos[$name])) {

                $this->addFlash(type: 'error', message:"Le todo d'id $name n'existe pas dans la liste");
            
            }else{
                // On supprime l'element dans la liste de todo s'il existe;
                unset($todos[$name]);

                // $todos[$name] = $content;
                // Mise à jour de la session
                $session->set('todos', $todos);
                $this->addFlash(type: 'success', message:"la liste d'id $name a été supprimé avec succès");                          
            }

        }
        else{

            $this->addFlash(type: 'error', message:"la liste des todos n'est pas encore initialisée");
        }

        return $this->redirectToRoute(route:'todo');


    }


    #[Route('/todo/reset', name: 'reset.todo')]
    // #[Route('/todo/add/{name}/{content}', name: 'todo.add')]
    public function resetTodo(Request $request){
        
        // recuperation de la session
        $session = $request->getSession();
        //On supprime de le tableau
        $session->remove(name : 'todos');

        return $this->redirectToRoute(route:'todo');
    }


}
