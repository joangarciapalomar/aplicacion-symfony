<?php

namespace App\Controller;

use App\Entity\Autor;
use App\Entity\Canciones;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CancionController extends AbstractController
{
    /*private $canciones = [];

    /**===BUSCAR CANCIONES POR TEXTO========================================================= */
    #[Route('/canciones/buscar/{texto}', name: 'app_cancion')]
    public function buscar(ManagerRegistry $doctrine, $texto): Response
    {
        $repositorio = $doctrine->getRepository(Canciones::class);
        $resultados = $repositorio->findByName($texto);

        return $this->render('cancion/index.html.twig', ['canciones' => $resultados]);
    }
    /**====================================================================================== */

    /**===BUSCAR CANCIONES POR CÓDIGO======================================================== */
    #[Route('/canciones/{codigo}', name: 'ficha_cancion')]
    public function codigo(ManagerRegistry $doctrine, $codigo): Response
    {
        $repositorio = $doctrine->getRepository(Canciones::class);
        $cancion = $repositorio->find($codigo);
        return $this->render('ficha_cancion.html.twig', ['cancion' => $cancion]);
    }
    /**====================================================================================== */

    /**===LISTADO CANCIONES================================================================== */
    #[Route('/canciones', name: 'app_listado')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $repositorio = $doctrine->getRepository(Canciones::class);
        $canciones = $repositorio->findAll();

        return $this->render('listado.html.twig', [
            'controller_name' => 'ListadoController', "canciones" => $canciones
        ]);
    }
    /**====================================================================================== */

    /**===INSERTAR CANCIONES================================================================= */
    #[Route('/canciones/insertar', name: 'app_insertar')]
    public function insertar(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        foreach ($this->canciones as $c) {
            $cancion = new Canciones();
            $cancion->setNombre($c["nombre"]);
            $cancion->setAutor($c["autor"]);
            $cancion->setDuracion($c["duracion"]);
            $entityManager->persist($cancion);
        }

        try {
            $entityManager->flush();
            return new Response("Canciones insertadas");
        } catch (\Exception $e) {
            return new Response("Error al insertar las canciones");
        }
    }
    /**================================    if($autor)===================================================== */
    #[Route('/canciones/update/{id}/{nombre}/{autor}/{duracion}', name: 'app_update')]
    public function update(ManagerRegistry $doctrine, $id, $nombre, $autor, $duracion): Response
    {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Canciones::class);
        $cancion = $repositorio->find($id);

        if ($cancion) {
            $cancion->setNombre($nombre);
            $cancion->setAutor($autor);
            $cancion->setDuracion(floatVal($duracion));
            try {
                $entityManager->flush();
                return $this->render('ficha_cancion.html.twig', [
                    'cancion' => $cancion
                ]);
            } catch (\Exception $e) {
                return new Response("Error actualizando la cancion");
            }
        } else {
            return $this->render('ficha_cancion.html.twig', ['cancion' => null]);
        }
    }
    /**===================================================================================== */

    /**===BORRAR CANCIONES================================================================== */
    #[Route('/canciones/delete/{id}', name: 'app_delete')]
    public function delete(ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Canciones::class);
        $cancion = $repositorio->find($id);
        if ($cancion) {
            try {
                $entityManager->remove($cancion);
                $entityManager->flush();
                return new Response("Mineral eliminado");
            } catch (\Exception $e) {
                return new Response("Error eliminando el objeto");
            }
        } else {
            return $this->render('ficha_cancion.html.twig', ['cancion' => null]);
        }
    }
    /**====================================================================================== */

    /**===INSERTAR AUTOR EN LA CANCIÓN======================================================== */
    #[Route('/canciones/insertarAutor/{id}/{autorId}', name: 'app_insertarAutorACancion')]
    public function insertarAutor(ManagerRegistry $doctrine, $id, $autorId): Response
    {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Canciones::class);
        $repositorio2 = $doctrine->getRepository(Autor::class);

        $cancion = $repositorio->find($id);

        $autor = $repositorio2->find($autorId);

        $cancion->setAutor($autor);

        $entityManager->persist($cancion);

        $entityManager->flush();
        return $this->render('ficha_cancion.html.twig', ['cancion' => $cancion]);
    }
    /**====================================================================================== */

    /**===INSERTAR AUTOR===================================================================== */
    #[Route('/autores/insertar/{nombre}/{edad}', name: 'app_insertarAutor')]
    public function insertaAutor(ManagerRegistry $doctrine, $nombre, $edad): Response
    {
        $entityManager = $doctrine->getManager();

        $autor = new Autor();
        $autor->setNombre($nombre);
        $autor->setEdad($edad);

        $entityManager->persist($autor);

        try {
            $entityManager->flush();
            return new Response("Autor creado");
        } catch (\Exception $e) {
            return new Response("Error al crear el autor");
        }
    }
    /**====================================================================================== */
}
