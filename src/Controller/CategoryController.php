<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category')]
    public function index(CategoryRepository $categoryRepository ): Response
    { 
        $categorys=$categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'categorys' => $categorys,
        ]);
    }

     #[Route('/category/{slug}', name: 'category_Show')]
        public function show($slug, CategoryRepository $categoryRepository): Response
    {
         $product = $categoryRepository->findOneBy(['slug' => $slug]);
         return $this->render('category/category_Show.html.twig', [
             'category' => $category,
         ]);
     }

    #[Route('/admin/category', name: 'admin_category')]
    public function adminList(CategoryRepository $categoryRepository): Response
    {
        $categorys = $categoryRepository->findAll();
        return $this->render('category/adminList_Category.html.twig', [
            'categorys' => $categorys
        ]);
    }

    #[Route('/admin/category/create', name: 'category_create')]
    public function create(Request $request, CategoryRepository $categoryRepository, ManagerRegistry $managerRegistry): Response
    {
        $category = new Category(); // création d'une nouvelle category
        $form = $this->createForm(CategoryType::class, $category); // création d'un formulaire avec en paramètre nouvelle category
        $form->handleRequest($request); // gestionnaire de requêtes HTTP

        if ($form->isSubmitted() && $form->isValid()) { // vérifie si le formulaire a été soumis et est valide

            $categorys = $categoryRepository->findAll(); // récupère tous les category en base de données
            $categoryNames = []; // initialise un tableau pour les noms de category
            foreach ($categorys as $category) { // pour chaque categorie récupéré
                $categoryNames[] = $category->getName(); // stocke le nom de la categorie dans le tableau
            }
            if (in_array($form['name']->getData(), $categoryNames)) { // vérifie qsi le nom de la categorie à créé n'est pas déjà utilisé en base de données
                $this->addFlash('danger', 'Le produit n\'a pas pu être créé : le nom de produit est déjà utilisé');
                return $this->redirectToRoute('admin_category');
            }

            $infoImg = $form['img']->getData(); // récupère les informations de l'image dans le formulaire
            if ($infoImg !== null) { // s'il y a bien une image donnée dans le formulaire
                $oldImgName = $category->getImg(); // récupère le nom de l'ancienne image
                $oldImgPath = $this->getParameter('category_image_dir') . '/' . $oldImgName; // récupère le chemin de l'ancienne image 
                if (file_exists($oldImgPath)) {
                    unlink($oldImgPath); // supprime l'ancienne image 
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
            'categoryForm' => $form->createView()
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

        $this->addFlash('success', 'La category a bein été supprimé');
        return $this->redirectToRoute('admin_category');
    }


    #[Route('/admin/category/update/{id}', name: 'category_update')]
    public function update(Category $category, CategoryRepository $categoryRepository, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(CategoryType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categorys = $productRepository->findAll(); // récupère tous les categorie en base de données
            $categoryNames = []; // initialise un tableau pour les noms de categorie
            foreach ($categorys as $category) { // pour chaque categorie récupéré
                $categoryNames[] = $category->getName(); // stocke le nom de categorie  dans le tableau
            }   
            if (in_array($form['name']->getData(), $categoryNames)) { // vérifie qsi le nom de la categorie à créé n'est pas déjà utilisé en base de données
                $this->addFlash('danger', 'La categorie n\'a pas pu être modifié : le nom de produit est déjà utilisé');
                return $this->redirectToRoute('admin_category');
            }

            $infoImg = $form['img']->getData(); // récupère les informations de l'image 1 dans le formulaire
            if ($infoImg !== null) { // s'il y a bien une image donnée dans le formulaire
                $oldImgName = $category->getImg1(); // récupère le nom de l'ancienne image
                $oldImgPath = $this->getParameter('product_image_dir') . '/' . $oldImgName; // récupère le chemin de l'ancienne image 1
                if (file_exists($oldImgPath)) {
                    unlink($oldImgPath); // supprime l'ancienne image 
                }
                $extensionImg = $infoImg->guessExtension(); // récupère l'extension de fichier de l'image 
                $nomImg = time() . '-1.' . $extensionImg; // crée un nom de fichier unique pour l'image 
                $infoImg->move($this->getParameter('category_image_dir'), $nomImg); // télécharge le fichier dans le dossier adéquat
                $category->setImg1($nomImg); // définit le nom de l'image à mettre ne base de données
            }

          
        

                $slugger = new AsciiSlugger();
                $category->setSlug(strtolower($slugger->slug($form['name']->getData())));
                $manager = $managerRegistry->getManager();
                $manager->persist($category);
                $manager->flush();
    
                $this->addFlash('success', 'La category a bien été modifié');
                return $this->redirectToRoute('admi_category');
        }
    
            return $this->render('category/form.html.twig', [
                'categoryForm' => $form->createView()
            ]);
        
            
}
}
