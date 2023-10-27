<?php

namespace App\Controller;

use App\Entity\Autor;
use App\Entity\Canciones;
use App\Form\CancionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class CancionController extends AbstractController
{
    private $canciones = [];

    /*===NUEVA CANCION======================================================================*/
    #[Route('/canciones/nueva', name: 'nueva_cancion')]
    public function nuevo(ManagerRegistry $doctrine, SluggerInterface $slugger, Request $request, SessionInterface $session): Response
    {
        if ($this->getUser()) {
            $cancion = new Canciones();

            $formulario = $this->createForm(CancionType::class, $cancion);
            $formulario->handleRequest($request);

            if ($formulario->isSubmitted() && $formulario->isValid()) {
                $cancion = $formulario->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($cancion);
                $entityManager->flush();
                $file = $formulario->get('file')->getData();
                if ($file) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    // This is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                    // Move the file to the directory where images are stored
                    try {
                        $file->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );

                        $filesystem = new Filesystem();
                        $filesystem->copy(
                            $this->getParameter('images_directory') . '/' . $newFilename,
                            true
                        );
                    } catch (FileException $e) {
                        // Handle the exception if something happens during file upload
                    }

                    // Update the 'file$filename' property to store the PDF file name
                    // instead of its contents
                    $cancion->setFile($newFilename);
                }

                // Flush the changes to the database
                $entityManager->flush();
                return $this->redirectToRoute('ficha_cancion');
            }
        } else {
            $session->set('redirect_to', 'ficha_cancion');
            return $this->redirectToRoute("app_login");
        }

        return $this->render('cancion/nuevo.html.twig', array('formulario' => $formulario->createView()));
    }
    /*======================================================================================*/

    /*===EDITAR CANCION=====================================================================*/
    #[Route('/canciones/editar/{codigo}', name: 'editar_cancion')]
    public function editar(ManagerRegistry $doctrine, SluggerInterface $slugger, Request $request, $codigo, SessionInterface $session): Response
    {
        if ($this->getUser()) {
            $repositorio = $doctrine->getRepository(Canciones::class);
            $cancion = $repositorio->find($codigo);
            if ($cancion) {
                $formulario = $this->createForm(CancionType::class, $cancion);

                $formulario->handleRequest($request);

                if ($formulario->isSubmitted() && $formulario->isValid()) {
                    $cancion = $formulario->getData();
                    $entityManager = $doctrine->getManager();

                    // Manejo de archivos
                    $file = $formulario->get('file')->getData();
                    if ($file) {
                        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        // Esto es necesario para incluir de manera segura el nombre del archivo como parte de la URL
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                        // Mover el archivo al directorio donde se almacenan las imágenes
                        try {
                            $file->move(
                                $this->getParameter('images_directory'),
                                $newFilename
                            );

                            $filesystem = new Filesystem();
                            $filesystem->copy(
                                $this->getParameter('images_directory') . '/' . $newFilename,
                                $this->getParameter('portfolio_directory') . '/' .  $newFilename,
                                true
                            );
                        } catch (FileException $e) {
                            // Manejar la excepción si ocurre algo durante la carga del archivo
                        }

                        // Actualizar la propiedad 'file$filename' para almacenar el nombre del archivo de la canción en lugar de su contenido
                        $cancion->setFile($newFilename);
                    }

                    // Guardar los cambios en la entidad principal
                    $entityManager->persist($cancion);
                    $entityManager->flush();

                    return $this->redirectToRoute('ficha_cancion', ["codigo" => $cancion->getId()]);
                }
            } else {
                $formulario = $this->createForm(CancionType::class, new Canciones());
            }

            return $this->render('cancion/editar.html.twig', [
                'formulario' => $formulario->createView(),
                'images' => $cancion,
            ]);
        } else {
            $session->set('redirect_to', 'editar_cancion');
            $session->set('codigo', $codigo);
            return $this->redirectToRoute("app_login");
        }
    }
    /*======================================================================================*/

    /**===BUSCAR CANCIONES POR TEXTO========================================================= */
    #[Route('/canciones/buscar/{texto}', name: 'app_cancion')]
    public function buscar(ManagerRegistry $doctrine, $texto): Response
    {
        $repositorio = $doctrine->getRepository(Canciones::class);
        $resultados = $repositorio->findByName($texto);

        return $this->render('cancion/index.html.twig', ['canciones' => $resultados]);
    }
    /**====================================================================================== */

    /**===FICHA POR CÓDIGO======================================================== */
    #[Route('/canciones/{codigo}', name: 'ficha_cancion')]
    public function codigo(ManagerRegistry $doctrine, $codigo, SessionInterface $session): Response
    {
        if ($this->getUser()) {
            $repositorio = $doctrine->getRepository(Canciones::class);
            $cancion = $repositorio->find($codigo);
            return $this->render('ficha_cancion.html.twig', ['cancion' => $cancion]);
        } else {
            $session->set('redirect_to', 'ficha_cancion');
            $session->set('codigo', $codigo);
            return $this->redirectToRoute("app_login");
        }
    }
    /**====================================================================================== */

    /**===LISTADO CANCIONES================================================================== */
    #[Route('/canciones', name: 'app_listado')]
    public function index(ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        if ($this->getUser()) {

            $repositorio = $doctrine->getRepository(Canciones::class);
            $canciones = $repositorio->findAll();

            return $this->render('listado.html.twig', [
                'controller_name' => 'ListadoController', "canciones" => $canciones
            ]);
        } else {
            $session->set('redirect_to', 'app_listado');
            return $this->redirectToRoute("app_login");
        }
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
    /**================================if($autor)===================================================== */
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
    public function delete(ManagerRegistry $doctrine, $id, SessionInterface $session): Response
    {
        if ($this->getUser()) {
            $entityManager = $doctrine->getManager();
            $repositorio = $doctrine->getRepository(Canciones::class);
            $cancion = $repositorio->find($id);
            if ($cancion) {
                try {
                    $entityManager->remove($cancion);
                    $entityManager->flush();
                    return new Response("Canción eliminada");
                } catch (\Exception $e) {
                    return new Response("Error eliminando el objeto");
                }
            } else {
                return $this->render('ficha_cancion.html.twig', ['cancion' => null]);
            }
        } else {
            $session->set('redirect_to', 'ficha_cancion');
            return $this->redirectToRoute("app_login");
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
