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

namespace MU\HelperModule\Form\Type\Field\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use MU\HelperModule\Form\DataTransformer\ArrayFieldTransformer;

/**
 * Array field type base class.
 */
abstract class AbstractArrayType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ArrayFieldTransformer();
        $builder->addModelTransformer($transformer);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return TextareaType::class;
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'muhelpermodule_field_array';
    }
}
