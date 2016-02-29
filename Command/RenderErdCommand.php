<?php


namespace Dontdrinkandroot\UtilsBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Graphp\GraphViz\GraphViz;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RenderErdCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ddr-utils:render-erd')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));
        /** @var EntityManagerInterface $em */
        $em = $this->getHelper('em')->getEntityManager();
        $entityClassNames = $em->getConfiguration()
            ->getMetadataDriverImpl()
            ->getAllClassNames();

        $graph = new Graph();
        $graph->setAttribute('graphviz.graph.overlap', 'false');
        $graph->setAttribute('graphviz.graph.splines', 'true');

        $vertices = [];
        $metaDatas = [];
        $classToTableNames = [];

        /* Collect vertices */
        foreach ($entityClassNames as $entityClassName) {
            $classMetaData = $em->getClassMetadata($entityClassName);
            if (!$classMetaData->isMappedSuperclass) {
                $tableName = $classMetaData->getTableName();
                //$output->writeln($tableName);
                $vertex = $graph->createVertex($tableName);
                $vertex->setAttribute('graphviz.shape', 'box');
                $vertices[$tableName] = $vertex;
                $metaDatas[$tableName] = $classMetaData;
                $classToTableNames[$classMetaData->getName()] = $tableName;
            }
        }

        /* Collect associations */
        foreach ($entityClassNames as $entityClassName) {
            $classMetaData = $em->getClassMetadata($entityClassName);
            if (!$classMetaData->isMappedSuperclass) {
                $associationMappings = $classMetaData->getAssociationMappings();
                foreach ($associationMappings as $associationMapping) {
                    if ($associationMapping['isOwningSide']) {
                        $sourceTableName = $classMetaData->getTableName();
                        $targetTableName = $classToTableNames[$associationMapping['targetEntity']];
                        /** @var Vertex $sourceVertex */
                        $sourceVertex = $vertices[$sourceTableName];
                        /** @var Vertex $targetVertex */
                        $targetVertex = $vertices[$targetTableName];
                        $edge = $sourceVertex->createEdge($targetVertex);

                        switch ($associationMapping['type']) {
                            case ClassMetadataInfo::MANY_TO_MANY:
                                $edge->setAttribute('graphviz.headlabel', '*');
                                $edge->setAttribute('graphviz.taillabel', '*');
                                break;
                            case ClassMetadataInfo::ONE_TO_MANY:
                                throw new \Exception(
                                    'One to many not supported yet: ' . $sourceTableName . ':' . $associationMapping['fieldName']
                                );
                                break;
                            case ClassMetadataInfo::MANY_TO_ONE:
                                if ($this->isNullableAssociation($associationMapping)) {
                                    $edge->setAttribute('graphviz.headlabel', '0,1');
                                } else {
                                    $edge->setAttribute('graphviz.headlabel', '1');
                                }
                                $edge->setAttribute('graphviz.taillabel', '*');
                                break;
                        }
                    }
                }
            }
        }

        $graphviz = new GraphViz();
        //$graphviz->setExecutable('neato');
        //$graphviz->display($graph);
        $output->writeln($graphviz->createScript($graph));
    }

    private function isNullableAssociation($associationMapping)
    {
        if (!array_key_exists('joinColumns', $associationMapping)) {
            var_dump($associationMapping);
        }

        $joinColumns = $associationMapping['joinColumns'];

        if (count($joinColumns) > 1) {
            throw new \Exception('More than one join Column currently not supported');
        }

        if (!array_key_exists('nullable', $joinColumns[0])) {
            return true;
        }

        return $joinColumns[0]['nullable'];
    }
}
