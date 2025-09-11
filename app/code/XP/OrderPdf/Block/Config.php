<?php
namespace XP\OrderPdf\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;

class Config extends Template
{
    /**
     * @var Renderer
     */
    private Renderer $addressRenderer;

    /**
     * @var Order
     */
    private Order $order;

    /**
     * @param Renderer $addressRenderer
     * @param Order $order
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Renderer $addressRenderer,
        Order $order,
        Template\Context $context,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->order = $order;
        parent::__construct($context, $data);
    }

    /**
     * @param mixed $address
     * @param string $type
     * @return array|string|string[]|null
     */
    public function getFormattedAddressString($address, $type)
    {
        return preg_replace('/(?:^\R*(?:<br\s*\/?>)*|(?:<br\s*\/?>)*\R*$)/iu', "",
            str_replace("|", "<br />", $this->addressRenderer->format($address, $type))
        );
    }

    /**
     * @param string|int|null $id
     * @return Order
     */
    public function loadOrder($id): order
    {
        return $this->order->loadByAttribute('entity_id', $id);
    }
}
