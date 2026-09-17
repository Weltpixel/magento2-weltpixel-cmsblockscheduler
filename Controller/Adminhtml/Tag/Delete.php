<?php

namespace WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag;

/**
 * Delete Tag action
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class Delete extends \WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag implements
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * This deletes a row, so it accepts POST only. Magento's backend request validator checks the
     * form key only on a POST and otherwise falls back to the admin url secret key, so on GET the
     * action used to dispatch and delete with nothing but that key standing in the way. Core's own
     * Cms block Delete declares the same interface. The edit form's Delete button already posts:
     * core's form container builds it as deleteConfirm(message, url, {data: {}}), and
     * deleteConfirm submits through mage/dataPost when that third argument is present.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $tagId = $this->getRequest()->getParam(static::PARAM_CRUD_ID);
        try {
            $tag= $this->_tagFactory->create()->setId($tagId);
            $tag->delete();
            $this->messageManager->addSuccess(
                __('Delete successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
