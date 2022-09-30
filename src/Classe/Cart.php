<?php
namespace App\Classe;


use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart {

    private $session;
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager, RequestStack $stack) {
        $this->session = $stack;
        $this->entityManager = $entityManager;
    }
    public function add($id)
    {
        $session = $this->session->getSession();
        $cart = $session->get('cart', []);
        if(!empty($cart[$id])){
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);

    }
    public function get(){
        return $this->session->get('cart');
    }
    public function remove(){
        $methodremove = $this->session->getSession();
        return $methodremove->remove('cart');
    }
    public function delete($id){
        $session = $this->session->getSession();
        $cart = $session->get('cart', []);

        unset($cart[$id]);
        return $session->set('cart', $cart);
    }

    public function decrease ($id) {
        //vérification de si la quantité = 1
        $session = $this->session->getSession();
        $cart = $session->get('cart', []);

        if($cart[$id]>1) {
            //retirer une quantité
            $cart[$id]--;

        } else {
            //supprimer le produit complétement
            unset($cart[$id]);
        }
        return $session->set('cart', $cart);

    }

    public function getFull() {
        //dd($stack->getSession()->get('cart'));
        $cartComplete = [];

         //if($this->session->getSession()->get('cart')){
        //Ajout du (aray) pour éviter le bug quand le cart est vide
        foreach ((array) $this->session->getSession()->get('cart') as $id => $quantity){
            $product_object = $this->entityManager->getRepository(Product::class)->find($id);
            //Permet de ne pas ajouter un produit inexistant par le biais de la route cart/add/764876876
            if(!$product_object) {
                $this->delete($id);
                continue;
            }
            $cartComplete[] = [
                'product' => $product_object,
                'quantity' => $quantity
            ];
        }
         //  }
        return $cartComplete;
    }
}