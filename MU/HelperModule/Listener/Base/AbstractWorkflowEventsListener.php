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

namespace MU\HelperModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;

/**
 * Event handler implementation class for workflow events.
 *
 * @see /src/docs/Workflows/WorkflowEvents.md
 */
abstract class AbstractWorkflowEventsListener implements EventSubscriberInterface
{
    /**
     * @var PermissionApiInterface
     */
    protected $permissionApi;
    
    /**
     * WorkflowEventsListener constructor.
     *
     * @param PermissionApiInterface $permissionApi PermissionApi service instance
     */
    public function __construct(PermissionApiInterface $permissionApi)
    {
        $this->permissionApi = $permissionApi;
    }
    
    /**
     * Makes our handlers known to the event system.
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.guard' => ['onGuard', 5],
            'workflow.leave' => ['onLeave', 5],
            'workflow.transition' => ['onTransition', 5],
            'workflow.enter' => ['onEnter', 5]
        ];
    }
    
    /**
     * Listener for the `workflow.guard` event.
     *
     * Occurs just before a transition is started and when testing which transitions are available.
     * Allows to define that the transition is not allowed by calling `$event->setBlocked(true);`.
     *
     * This event is also triggered for each workflow individually, so you can react only to the events
     * of a specific workflow by listening to `workflow.<workflow_name>.guard` instead.
     * You can even listen to some specific transitions or states for a specific workflow
     * using `workflow.<workflow_name>.guard.<transition_name>`.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * Access the entity: `$entity = $event->getSubject();`
     * Access the marking: `$marking = $event->getMarking();`
     * Access the transition: `$transition = $event->getTransition();`
     * Example for preventing a transition:
     *     `if (!$event->isBlocked()) {
     *         $event->setBlocked(true);
     *     }`
     *
     * @param GuardEvent $event The event instance
     */
    public function onGuard(GuardEvent $event)
    {
        $entity = $event->getSubject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
    
        $objectType = $entity->get_objectType();
        $permissionLevel = ACCESS_READ;
        $transitionName = $event->getTransition()->getName();
        if (substr($transitionName, 0, 6) == 'update') {
            $transitionName = 'update';
        }
        
        $hasApproval = false;
    
        switch ($transitionName) {
            case 'defer':
            case 'submit':
                $permissionLevel = $hasApproval ? ACCESS_COMMENT : ACCESS_EDIT;
                break;
            case 'update':
            case 'reject':
            case 'accept':
            case 'publish':
            case 'unpublish':
            case 'archive':
            case 'trash':
            case 'recover':
                $permissionLevel = ACCESS_EDIT;
                break;
            case 'approve':
            case 'demote':
                $permissionLevel = ACCESS_ADD;
                break;
            case 'delete':
                $permissionLevel = ACCESS_DELETE;
                break;
        }
    
        if (!$this->permissionApi->hasPermission('MUHelperModule:' . ucfirst($objectType) . ':', $entity->getKey() . '::', $permissionLevel)) {
            // no permission for this transition, so disallow it
            $event->setBlocked(true);
    
            return;
        }
    }
    
    /**
     * Listener for the `workflow.leave` event.
     *
     * Occurs just after an object has left it's current state.
     * Carries the marking with the initial places.
     *
     * This event is also triggered for each workflow individually, so you can react only to the events
     * of a specific workflow by listening to `workflow.<workflow_name>.leave` instead.
     * You can even listen to some specific transitions or states for a specific workflow
     * using `workflow.<workflow_name>.leave.<state_name>`.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * Access the entity: `$entity = $event->getSubject();`
     * Access the marking: `$marking = $event->getMarking();`
     * Access the transition: `$transition = $event->getTransition();`
     *
     * @param Event $event The event instance
     */
    public function onLeave(Event $event)
    {
        $entity = $event->getSubject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
    }
    
    /**
     * Listener for the `workflow.transition` event.
     *
     * Occurs just before starting to transition to the new state.
     * Carries the marking with the current places.
     *
     * This event is also triggered for each workflow individually, so you can react only to the events
     * of a specific workflow by listening to `workflow.<workflow_name>.transition` instead.
     * You can even listen to some specific transitions or states for a specific workflow
     * using `workflow.<workflow_name>.transition.<transition_name>`.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * Access the entity: `$entity = $event->getSubject();`
     * Access the marking: `$marking = $event->getMarking();`
     * Access the transition: `$transition = $event->getTransition();`
     *
     * @param Event $event The event instance
     */
    public function onTransition(Event $event)
    {
        $entity = $event->getSubject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
    }
    
    /**
     * Listener for the `workflow.enter` event.
     *
     * Occurs just after the object has entered into the new state.
     * Carries the marking with the new places.
     *
     * This event is also triggered for each workflow individually, so you can react only to the events
     * of a specific workflow by listening to `workflow.<workflow_name>.enter` instead.
     * You can even listen to some specific transitions or states for a specific workflow
     * using `workflow.<workflow_name>.enter.<state_name>`.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * Access the entity: `$entity = $event->getSubject();`
     * Access the marking: `$marking = $event->getMarking();`
     * Access the transition: `$transition = $event->getTransition();`
     *
     * @param Event $event The event instance
     */
    public function onEnter(Event $event)
    {
        $entity = $event->getSubject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
    }
    
    /**
     * Checks whether this listener is responsible for the given entity or not.
     *
     * @param EntityAccess $entity The given entity
     *
     * @return boolean True if entity is managed by this listener, false otherwise
     */
    protected function isEntityManagedByThisBundle($entity)
    {
        if (!($entity instanceof EntityAccess)) {
            return false;
        }
    
        $entityClassParts = explode('\\', get_class($entity));
    
        return ($entityClassParts[0] == 'MU' && $entityClassParts[1] == 'HelperModule');
    }
}
