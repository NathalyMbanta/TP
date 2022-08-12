<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category')]
    public function index(CategoryRepository $categroryRepository ): Response
    { 
        $category=$categroryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'category' => $category,
        ]);
    }

     #[Route('/category/{slug}', name: 'category_Show')]
        public function show($slug, CategoryRepository $categoryRepository): Response
    {
         $product = $categoryRepository->findOneBy(['slug' => $slug]);
         return $this->render('category/category_Show.html.twig', [
             'category' => $product
         ]);
     }

    #[Route('/admin/category', name: 'admin_category')]
    public function adminList(CategoryRepository $CategryRepository): Response
    {
        $category = $productRepository->findAll();
        return $this->render('category/adminList_Category.html.twig', [
            'categorys' => $categorys
        ]);
    }

    #[Route('/admin/category/create', name: 'category_create')]

    public function create(Request $request, CategoryRepository $categegoryRepository, ManagerRegistry $managerRegistry): Response
    {
        $category = new Category(); // création d'une nouvelle category
        $form = $this->createForm(CategoryType::class, $category); // création d'un formulaire avec en paramètre nouvelle category
        $form->handleRequest($request); // gestionnaire de requêtes HTTP


        if ($form->isSubmitted() && $form->isValid()) { // vérifie si le formulaire a été soumis et est valide

            $category = $categoryRepository->findAll(); // récupère tous les category en base de données
            $categoryNames = []; // initialise un tableau pour les noms de category
            foreach ($categorys as $category) { // pour chaque produit récupéré
                $categoryNames[] = $category->getName(); // stocke le nom de la categorie dans le tableau
            }
            if (in_array($form['name']->getData(), $categoryNames)) { // vérifie qsi le nom de la categorie à créé n'est pas déjà utilisé en base de données
                $this->addFlash('danger', 'Le produit n\'a pas pu être créé : le nom de produit est déjà utilisé');
                return $this->redirectToRoute('admin_category');
            }

                $infoImg = $form['img']->getData(); // récupère les informations de l'image dans le formulaire
                if ($infoImg !== null) { // s'il y a bien une image donnée dans le formulaire
                    $oldImgName = $product->getImg(); // récupère le nom de l'ancienne image
                    $oldImgPath = $this->getParameter('category_image_dir') . '/' . $oldImgName; // récupère le chemin de l'ancienne image 
                    if (file_exists($oldImg1Path)) {
                        unlink($oldImg1Path); // supprime l'ancienne image 
                    }
                    $extensionImg = $infoImg->guessExtension(); // récupère l'extension de fichier de l'image 
                    $nomImg = time() . '-1.' . $extensionImg; // crée un nom de fichier unique pour l'image 
                    $infoImg->move($this->getParameter('category_image_dir'), $nomImg); // télécharge le fichier dans le dossier adéquat
                    $category->setImg1($nomImg); // définit le nom de l'image à mettre en base de données
                }

            $slugger = new AsciiSlugger();
            $category->setSlug(strtolower($slugger->slug($form['name']->getData()))); // génère un slug à partir du titre renseigné dans le formulaire
            //$category->setCreatedAt(new \DateTimeImmutable());

            $manager = $managerRegistry->getManager();
            $manager->persist($product);
            $manager->flush();

            $this->addFlash('success', 'La categorie a été crée'); // message de succès
            return $this->redirectToRoute('admin_category');
        }
            return $this->render('category/form.html.twig', [
                'productForm' => $form->createView()
            ]);

            } 


            #[Route('/admin/category/delete/{id}', name: 'category_delete')]
            public function delete(Category $product, ManagerRegistry $managerRegistry): Response
            {
                $imgpath = $this->getParameter('category_image_dir') . '/' . $product->getImg();
                if (file_exists($imgpath)) {
                    unlink($imgpath);
                }
        
        
                $manager = $managerRegistry->getManager();
                $manager->remove($product);
                $manager->flush();
        
                $this->addFlash('success', 'Le produit a bein été supprimé');
                return $this->redirectToRoute('admin_category');
            }
            
           
            
        }
            