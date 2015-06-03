<?php


namespace Dontdrinkandroot\UtilsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FlexDateFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year', 'integer', ['label' => false, 'required' => false, 'attr' => ['class' => 'year']])
            ->add(
                'month',
                'choice',
                [
                    'label'    => false,
                    'required' => false,
                    'choices'  => $this->getMonthChoices(),
                    'attr'     => ['class' => 'month']
                ]
            )
            ->add(
                'day',
                'choice',
                [
                    'label'    => false,
                    'required' => false,
                    'choices'  => $this->getDayChoices(),
                    'attr'     => ['class' => 'day']
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'flexdate';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Dontdrinkandroot\Date\FlexDate',
                'attr'       => ['class' => 'flexdate']
            ]
        );
    }

    private function getMonthChoices()
    {
        $choices = [];
        for ($i = 1; $i <= 12; $i++) {
            $choices[$i] = $i;
        }

        return $choices;
    }

    private function getDayChoices()
    {
        $choices = [];
        for ($i = 1; $i <= 31; $i++) {
            $choices[$i] = $i;
        }

        return $choices;
    }
}
