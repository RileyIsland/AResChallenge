<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class WeatherTimeForm extends AbstractType
{
    /**
     * @Assert\Regex(
     *      pattern = "/^[0-9]{5}(-[0-9]{4})?$/",
     *      message = "Invalid Zip Code"
     * )
     */
    protected $zip;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('zip', TextType::class, ['label' => 'Zip'])
            ->add('save', SubmitType::class, ['label' => 'Go']);
    }

    /**
     * accessor method for zip field
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * mutator method for zip field
     */
    public function setZip(String $zip = null)
    {
        $this->zip = $zip;
    }
}
