<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Controller\Adminhtml\Template;

use Yosto\InstagramConnect\Controller\Adminhtml\Template;

/**
 * Class MassDelete
 * @package Yosto\InstagramConnect\Controller\Adminhtml\Template
 */
class MassDelete extends Template
{
    /**
     * Execute when mass deleting
     */
    public function execute()
    {
        $templateIds = $this->getRequest()->getParam('template');
        $templateModel = $this->_templateFactory->create();
        foreach ($templateIds as $templateId) {
            try {
                $templateModel->load($templateId)->delete();
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        if (count($templateIds)) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', count($templateIds))
            );
        }

        $this->_redirect('*/*/index');
    }

}