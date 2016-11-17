<?php


namespace Dontdrinkandroot\UtilsBundle\Form;

use Dontdrinkandroot\Date\FlexDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FlexDateFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'year',
                IntegerType::class,
                ['label'    => false,
                 'required' => false,
                 'attr'     => ['class' => 'year', 'placeholder' => 'ddr.utils.year']
                ]
            )
            ->add(
                'month',
                ChoiceType::class,
                [
                    'label'       => false,
                    'required'    => false,
                    'choices'     => $this->getMonthChoices(),
                    'placeholder' => 'ddr.utils.month',
                    'attr'        => ['class' => 'month']
                ]
            )
            ->add(
                'day',
                ChoiceType::class,
                [
                    'label'       => false,
                    'required'    => false,
                    'choices'     => $this->getDayChoices(),
                    'placeholder' => 'ddr.utils.day',
                    'attr'        => ['class' => 'day']
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(
            [
                'data_class' => FlexDate::class,
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
