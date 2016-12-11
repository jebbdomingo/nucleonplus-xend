<?php
/**
 * Nucleon Plus - Xend
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

/**
 * Xend Controller Behavior.
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Xend\Controller\Behavior
 */
class ComXendControllerBehaviorShippable extends KControllerBehaviorAbstract
{
    /**
     * List of actions
     *
     * @var array
     */
    protected $_actions;

    /**
     * List of columns
     *
     * @var array
     */
    protected $_columns;

    /**
     * Email subject template
     *
     * @var string
     */
    protected $_email_subject_tmpl;

    /**
     * Email body template
     *
     * @var string
     */
    protected $_email_body_tmpl;

    /**
     * Failed email template
     *
     * @var string
     */
    protected $_email_failed_tmpl;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_actions            = KObjectConfig::unbox($config->actions);
        $this->_columns            = KObjectConfig::unbox($config->columns);
        $this->_email_subject_tmpl = $config->email_subject_tmpl;
        $this->_email_body_tmpl    = $config->email_body_tmpl;
        $this->_email_failed_tmpl  = $config->email_failed_tmpl;
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config Configuration options.
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'actions' => array(),
            'columns' => array(
                'shippers_reference' => 'id',
                'receiver_name'      => 'name',
                'receiver_email'     => '_user_email',
                'tracking_no'        => 'tracking_reference',
            ),
            'email_subject_tmpl' => 'COM_NUCLEONPLUS_ORDER_EMAIL_SHIPPED_SUBJECT',
            'email_body_tmpl'    => 'COM_NUCLEONPLUS_ORDER_EMAIL_SHIPPED_BODY',
            'email_failed_tmpl'  => 'COM_NUCLEONPLUS_EMAIL_SEND_MAIL_FAILED',
        ));

        // Append the default action if none is set.
        if (!count($config->actions)) {
            $config->append(array('actions' => array('after.ship')));
        }

        parent::_initialize($config);
    }

    /**
     * Command handler.
     *
     * @param KCommandInterface      $command The command.
     * @param KCommandChainInterface $chain   The chain executing the command.
     * 
     * @return mixed If a handler breaks, returns the break condition. Returns the result of the handler otherwise.
     */
    final public function execute(KCommandInterface $command, KCommandChainInterface $chain)
    {
        $action = $command->getName();

        if (in_array($action, $this->_actions))
        {
            $result = false;

            try
            {
                $entities = $this->getEntity($command);

                foreach ($entities as $entity)
                {
                    $data = $this->getData($entity);

                    // Send email notification
                    $config       = JFactory::getConfig();
                    $emailSubject = sprintf(JText::_($this->_email_subject_tmpl), $data['shippers_reference']);
                    $trackingLink = "http://tracker.xend.com.ph/?waybill={$data['tracking_no']}";
                    $orderLink    = JUri::root() . 'home/my-orders?view=order&id=' . $data['shippers_reference'];
                    $emailBody    = sprintf(JText::_($this->_email_body_tmpl),
                        $data['receiver_name'],
                        $orderLink,
                        $trackingLink,
                        JUri::root()
                    );

                    $mail = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $data['receiver_email'], $emailSubject, $emailBody);
                    // Check for an error.
                    if ($mail !== true) {
                        $context->response->addMessage(JText::_($this->_email_failed_tmpl), 'error');
                    }
                }
            }
            catch(Exception $e)
            {
                $this->getContext()->response->addMessage($e->getMessage(), 'exception');
            }
        
            return $result;
        }
    }

    /**
     * Get the entity.
     *
     * @param KCommandInterface $command The command.
     *
     * @return KModelEntityInterface
     */
    public function getEntity(KCommandInterface $command)
    {
        $parts = explode('.', $command->getName());

        // Properly fetch data for the event.
        if ($parts[0] == 'before') {
            $entity = $command->getSubject()->getModel()->fetch();
        } else {
            $entity = $command->result;
        }

        return $entity;
    }

    /**
     * Get the data.
     *
     * @param KModelEntityInterface $entity
     * 
     * @return array data.
     */
    public function getData(KModelEntityInterface $entity)
    {
        $data = array();

        foreach ($this->_columns as $name => $column)
        {
            if ($entity->{$column}) {
                $data[$name] = $entity->{$column};
            }
        }

        return $data;
    }

    /**
     * Get the behavior name.
     *
     * Hardcode the name to 'xend.shippable'.
     *
     * @return string
     */
    final public function getName()
    {
        return 'xend.shippable';
    }

    /**
     * Get an object handle.
     *
     * Force the object to be enqueued in the command chain.
     *
     * @see execute()
     *
     * @return string A string that is unique, or NULL.
     */
    final public function getHandle()
    {
        return KObjectMixinAbstract::getHandle();
    }
}
