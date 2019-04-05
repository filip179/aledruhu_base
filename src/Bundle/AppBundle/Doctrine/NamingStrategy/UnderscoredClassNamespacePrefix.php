<?php

namespace AppBundle\Doctrine\NamingStrategy;

use Doctrine\ORM\Mapping\NamingStrategy;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class UnderscoredClassNamespacePrefix implements NamingStrategy {
    /**
     * @var int
     */
    protected $case = CASE_LOWER;
    /**
     * @var bool
     */
    protected $joinTableFieldSuffix;

    /**
     * @var string
     */
    protected $projectPrefix;

    /**
     * @var array
     */
    protected $map;

    public function __construct(KernelInterface $kernel)
    {
        $this->map = $this->getNamingMap($kernel);
    }

    private function getNamingMap(KernelInterface $kernel)
    {
        $this->projectPrefix = $kernel->getContainer()->getParameter('doctrine_table_prefix');

        $map = [];
        /**
         * @var BundleInterface $bundle ;
         */
        foreach ($kernel->getBundles() as $bundle) {
            $bundleNamespace = (new \ReflectionClass(get_class($bundle)))->getNamespaceName();
            $bundleName = $bundle->getName();
            $bundleName = preg_replace('/Bundle$/', '', $bundleName);
            $map[$this->projectPrefix . $this->underscore($bundleName) . '_'] = $bundleNamespace;
        }

        return $map;
    }

    /**
     * Build underscore version of given string.
     *
     * @param string $string
     *
     * @return string
     */
    protected function underscore($string)
    {
        $string = preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $string);
        if (CASE_UPPER === $this->case) {
            return strtoupper($string);
        }

        return strtolower($string);
    }

    /**
     * {@inheritdoc}
     */
    public function embeddedFieldToColumnName(
        $propertyName,
        $embeddedColumnName,
        $className = null,
        $embeddedClassName = null
    )
    {
        return $this->underscore($propertyName) . '_' . $this->underscore($embeddedColumnName);
    }

    /**
     * {@inheritdoc}
     */
    public function joinColumnName($propertyName, $className = null)
    {
        return $this->underscore($propertyName) . '_' . $this->referenceColumnName();
    }

    /**
     * {@inheritdoc}
     */
    public function referenceColumnName()
    {
        return $this->case === CASE_UPPER ? 'ID' : 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
    {
        $tableName = $this->classToTableName($sourceEntity) . '_' . $this->classToTableName($targetEntity);

        return
            $tableName
            .
            (($this->joinTableFieldSuffix && null !== $propertyName) ? '_' . $this->propertyToColumnName(
                    $propertyName,
                    $sourceEntity
                ) : '');
    }

    /**
     * {@inheritdoc}
     */
    public function classToTableName($className)
    {
        $prefix = $this->getTableNamePrefix($className);
        if (strpos($className, '\\') !== false) {
            $className = substr($className, strrpos($className, '\\') + 1);
        }

        return $prefix . $this->underscore($className);
    }

    /**
     * Get prefix for table from map.
     *
     * @param string $className
     *
     * @return string
     */
    protected function getTableNamePrefix($className)
    {
        $className = ltrim($className, '\\');
        foreach ($this->map as $prefix => $namespace) {
            if (strpos($className, $namespace) === 0) {
                return $prefix . '_';
            }
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function propertyToColumnName($propertyName, $className = null)
    {
        return $this->underscore($propertyName);
    }

    /**
     * {@inheritdoc}
     */
    public function joinKeyColumnName($entityName, $referencedColumnName = null)
    {
        return $this->classToTableName($entityName) . '_' .
            ($referencedColumnName ? $this->underscore($referencedColumnName) : $this->referenceColumnName());
    }
}
