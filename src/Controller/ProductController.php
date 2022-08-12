<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'products')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{slug}', name: 'product_show')]
    public function show($slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/admin/products', name: 'admin_products')]
    public function adminList(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('product/adminList.html.twig', [
            'products' => $products
        ]);
    }
    
    #[Route('/admin/product/create', name: 'product_create')]
    public function create(Request $request, ProductRepository $productRepository, ManagerRegistry $managerRegistry): Response
    {
        $product = new Product(); // création d'un nouveau produit
        $form = $this->createForm(ProductType::class, $product); // création d'un formulaire avec en paramètre le nouveau produit
        $form->handleRequest($request); // gestionnaire de requêtes HTTP

        if ($form->isSubmitted() && $form->isValid()) { // vérifie si le formulaire a été soumis et est valide

            $products = $productRepository->findAll(); // récupère tous les produits en base de données
            $productNames = []; // initialise un tableau pour les noms de produits
            foreach ($products as $product) { // pour chaque produit récupéré
                $productNames[] = $product->getName(); // stocke le nom du produit dans le tableau
            }
            if (in_array($form['name']->getData(), $productNames)) { // vérifie qsi le nom du produit à créé n'est pas déjà utilisé en base de données
                $this->addFlash('danger', 'Le produit n\'a pas pu être créé : le nom de produit est déjà utilisé');
                return $this->redirectToRoute('admin_products');
            }

            $infoImg1 = $form['img1']->getData(); // récupère les données du champ img1 du formulaire

            if (empty($infoImg1)) { // vérifie la présence de l'image principale dans le formulaire
                $this->addFlash('danger', 'Le produit n\'a pas pu être créé : l\'image principale est obligatoire mais n\'a pas été renseignée');
                return $this->redirectToRoute('admin_products');
            }

            $extensionImg1 = $infoImg1->guessExtension(); // récupère l'extension de fichier de l'image 1
            $nomImg1 = time() . '-1.' . $extensionImg1; // crée un nom de fichier unique pour l'image 1
            $infoImg1->move($this->getParameter('product_image_dir'), $nomImg1); // télécharge le fichier dans le dossier adéquat
            $product->setImg1($nomImg1); // définit le nom de l'image à mettre ne base de données

            $infoImg2 = $form['img2']->getData();
            if ($infoImg2 !== null) {
                $extensionImg2 = $infoImg2->guessExtension();
                $nomImg2 = time() . '-2.' . $extensionImg2;
                $infoImg2->move($this->getParameter('product_image_dir'), $nomImg2);
                $product->setImg2($nomImg2);
            }

            $infoImg3 = $form['img3']->getData();
            if ($infoImg3 !== null) {
                $extensionImg3 = $infoImg3->guessExtension();
                $nomImg3 = time() . '-3.' . $extensionImg3;
                $infoImg3->move($this->getParameter('product_image_dir'), $nomImg3);
                $product->setImg3($nomImg3);
            }

            $slugger = new AsciiSlugger();
            $product->setSlug(strtolower($slugger->slug($form['name']->getData()))); // génère un slug à partir du titre renseigné dans le formulaire
            $product->setCreatedAt(new \DateTimeImmutable());

            $manager = $managerRegistry->getManager();
            $manager->persist($product);
            $manager->flush();

            $this->addFlash('success', 'Le produit a bien été créé'); // message de succès
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('product/form.html.twig', [
            'productForm' => $form->createView()
        ]);
    }

    #[Route('/admin/product/update/{id}', name: 'product_update')]
    public function update(Product $product, ProductRepository $productRepository, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $products = $productRepository->findAll(); // récupère tous les produits en base de données
            $productNames = []; // initialise un tableau pour les noms de produits
            foreach ($products as $product) { // pour chaque produit récupéré
                $productNames[] = $product->getName(); // stocke le nom du produit dans le tableau
            }
            if (in_array($form['name']->getData(), $productNames)) { // vérifie qsi le nom du produit à créé n'est pas déjà utilisé en base de données
                $this->addFlash('danger', 'Le produit n\'a pas pu être modifié : le nom de produit est déjà utilisé');
                return $this->redirectToRoute('admin_products');
            }

            $infoImg1 = $form['img1']->getData(); // récupère les informations de l'image 1 dans le formulaire
            if ($infoImg1 !== null) { // s'il y a bien une image donnée dans le formulaire
                $oldImg1Name = $product->getImg1(); // récupère le nom de l'ancienne image
                $oldImg1Path = $this->getParameter('product_image_dir') . '/' . $oldImg1Name; // récupère le chemin de l'ancienne image 1
                if (file_exists($oldImg1Path)) {
                    unlink($oldImg1Path); // supprime l'ancienne image 1
                }
                $extensionImg1 = $infoImg1->guessExtension(); // récupère l'extension de fichier de l'image 1
                $nomImg1 = time() . '-1.' . $extensionImg1; // crée un nom de fichier unique pour l'image 1
                $infoImg1->move($this->getParameter('product_image_dir'), $nomImg1); // télécharge le fichier dans le dossier adéquat
                $product->setImg1($nomImg1); // définit le nom de l'image à mettre ne base de données
            }

            $infoImg2 = $form['img2']->getData();
            if ($infoImg2 !== null) {
                $oldImg2Name = $product->getImg2(); // récupère le nom de l'ancienne image 2 (en bdd)
                if ($oldImg2Name !== null) { // vérifie s'il y a une image 2 en base de données
                    $oldImg2Path = $this->getParameter('product_image_dir') . '/' . $oldImg2Name;
                    if (file_exists($oldImg1Path)) {
                        unlink($oldImg2Path);
                    }
                }
                $extensionImg2 = $infoImg2->guessExtension();
                $nomImg2 = time() . '-2.' . $extensionImg2;
                $infoImg2->move($this->getParameter('product_image_dir'), $nomImg2);
                $product->setImg2($nomImg2);
            }

            $infoImg3 = $form['img3']->getData();
            if ($infoImg3 !== null) {
                $oldImg3Name = $product->getImg3();
                if ($oldImg3Name !== null) {
                    $oldImg3Path = $this->getParameter('product_image_dir') . '/' . $oldImg3Name;
                    if (file_exists($oldImg1Path)) {
                        unlink($oldImg3Path);
                    }
                }
                $extensionImg3 = $infoImg3->guessExtension();
                $nomImg3 = time() . '-3.' . $extensionImg3;
                $infoImg3->move($this->getParameter('product_image_dir'), $nomImg3);
                $product->setImg3($nomImg3);
            }

            $slugger = new AsciiSlugger();
            $product->setSlug(strtolower($slugger->slug($form['name']->getData())));
            $manager = $managerRegistry->getManager();
            $manager->persist($product);
            $manager->flush();

            $this->addFlash('success', 'Le produit a bien été modifié');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('product/form.html.twig', [
            'productForm' => $form->createView()
        ]);
    }

    #[Route('/admin/product/delete/{id}', name: 'product_delete')]
    public function delete(Product $product, ManagerRegistry $managerRegistry): Response
    {
        $img1path = $this->getParameter('product_image_dir') . '/' . $product->getImg1();
        if (file_exists($img1path)) {
            unlink($img1path);
        }

        if ($product->getImg2() !== null) {
            $img2path = $this->getParameter('product_image_dir') . '/' . $product->getImg2();
            if (file_exists($img2path)) {
                unlink($img2path);
            }
        }
        
        if ($product->getImg3() !== null) {
            $img3path = $this->getParameter('product_image_dir') . '/' . $product->getImg3();
            if (file_exists($img3path)) {
                unlink($img3path);
            }
        }

        $manager = $managerRegistry->getManager();
        $manager->remove($product);
        $manager->flush();

        $this->addFlash('success', 'Le produit a bein été supprimé');
        return $this->redirectToRoute('admin_products');
    }
}
