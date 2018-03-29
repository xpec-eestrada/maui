<?php
namespace Transbank\Webpay\Block\Checkout\Onepage\Success;

class PaymentData extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    private $_paidResult;
    protected $context;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ){
        $this->checkoutSession = $checkoutSession;
        
        parent::__construct($context, $data);
        
        $this->context = $context;
    }
    
    protected function _toHtml()
    {
        $result = $this->checkoutSession->getHasPaidResult();
        $this->_paidResult = $result;
        
        if (!isset($result)) {
            $result = 0;
        }
        
        /*if (isset($result)) {
            $this->checkoutSession->unsHasPaidResult();
        } else {            
            $result = 0;
        }*/
        
        $this->addData(
            [
                'payment_details' => $result,
                'enable_delivery_date' => $this->context->getScopeConfig()->getValue(
                    'shipping/shipping_methods_express/active',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            ]
        );
        
        return parent::_toHtml();
    }
}