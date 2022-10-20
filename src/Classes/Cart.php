<?php
namespace App\Classes;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
class Cart
{
    private RequestStack $requestStack;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function add($id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])){
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);
    }

    public function get()
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart');
    }

    public function remove()
    {
        $session = $this->requestStack->getSession();
        return $session->remove('cart');
    }

    public function delete($id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        unset($cart[$id]);

        return $session->set('cart', $cart);
    }

    public function decrease($id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if($cart[$id] > 1){
            $cart[$id] --;
        }else{
            unset($cart[$id]);
        }

        return $session->set('cart', $cart);
    }

    public function getfull()
    {
        $cartComplete = [];

        if ($this->get()){
        foreach ($this->get()as $id =>$quantity){
            $productObject =$this->entityManager->getRepository(Product::class)->findOneById($id);
            if(!$productObject){
                $this->delete($id);
                continue;
            }
            $cartComplete[] = [
                'product'=> $productObject ,
                'quantity'=> $quantity
                ];
            }
        }
        return $cartComplete;
    }

}