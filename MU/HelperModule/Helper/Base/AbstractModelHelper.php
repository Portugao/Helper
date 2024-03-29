<?php
/**
 * Helper.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\HelperModule\Helper\Base;

use MU\HelperModule\Entity\Factory\EntityFactory;

/**
 * Helper base class for model layer methods.
 */
abstract class AbstractModelHelper
{
    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * ModelHelper constructor.
     *
     * @param EntityFactory $entityFactory EntityFactory service instance
     */
    public function __construct(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }

    /**
     * Determines whether creating an instance of a certain object type is possible.
     * This is when
     *     - no tree is used
     *     - it has no incoming bidirectional non-nullable relationships.
     *     - the edit type of all those relationships has PASSIVE_EDIT and auto completion is used on the target side
     *       (then a new source object can be created while creating the target object).
     *     - corresponding source objects exist already in the system.
     *
     * Note that even creation of a certain object is possible, it may still be forbidden for the current user
     * if he does not have the required permission level.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return boolean Whether a new instance can be created or not
     */
    public function canBeCreated($objectType)
    {
        $result = false;
    
        switch ($objectType) {
            case 'productClass':
                $result = true;
                break;
        }
    
        return $result;
    }

    /**
     * Determines whether there exists at least one instance of a certain object type in the database.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return boolean Whether at least one instance exists or not
     */
    protected function hasExistingInstances($objectType)
    {
        $repository = $this->entityFactory->getRepository($objectType);
        if (null === $repository) {
            return false;
        }
    
        return $repository->selectCount() > 0;
    }

    /**
     * Returns a desired sorting criteria for passing it to a repository method.
     *
     * @param string $objectType Name of treated entity type
     * @param string $sorting    The type of sorting (newest, random, default)
     *
     * @return string The order by clause
     */
    public function resolveSortParameter($objectType = '', $sorting = 'default')
    {
        if ($sorting == 'random') {
            return 'RAND()';
        }
    
        $sortParam = '';
        if ($sorting == 'newest') {
            $sortParam = $this->entityFactory->getIdField($objectType) . ' DESC';
        } elseif ($sorting == 'default') {
            $repository = $this->entityFactory->getRepository($objectType);
            $sortParam = $repository->getDefaultSortingField() . ' ASC';
        }
    
        return $sortParam;
    }
}
