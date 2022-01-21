<?php

namespace App\Search;

use App\Search\SearchType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\RouterInterface;

class SearchFormGenerator
{
    private FormFactory $formFactory;
    private RouterInterface  $router;

    /**
     * @param FormFactory $formFactory
     * @param RouterInterface $router
     */
    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
    }


    public function getSearchForm(): FormView
    {
        $form = $this->formFactory->create(SearchType::class, new Search(), ["action"=>$this->router->generate('search')]);
        return $form->createView();
    }
}