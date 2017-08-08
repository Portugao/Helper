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

namespace MU\HelperModule\Entity;

use MU\HelperModule\Entity\Base\AbstractProductClassEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the concrete entity class for product class entities.
 * @ORM\Entity(repositoryClass="MU\HelperModule\Entity\Repository\ProductClassRepository")
 * @ORM\Table(name="mu_helper_productclass",
 *     indexes={
 *         @ORM\Index(name="workflowstateindex", columns={"workflowState"})
 *     }
 * )
 */
class ProductClassEntity extends BaseEntity
{
    // feel free to add your own methods here
}
