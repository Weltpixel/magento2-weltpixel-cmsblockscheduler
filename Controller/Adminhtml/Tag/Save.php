<?php

namespace WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Save Tag action.
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class Save extends \WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag implements
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    const PARAM_CRUD_ID = 'id';

    /**
     * This writes a row, so it accepts POST only, for the same reason as Delete. The edit form
     * already posts, and the body below already did nothing without post data, so declaring the
     * interface makes the existing expectation explicit rather than changing the flow.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            $model = $this->_tagFactory->create();

            if ($id = $this->getRequest()->getParam(static::PARAM_CRUD_ID)) {
                $model->load($id);
            }

            $model->setData($data);

            try {
                $model->save();

                $this->messageManager->addSuccess(__('The tag has been saved.'));
                $this->_getSession()->setFormData(false);

                return $this->_getBackResultRedirect($resultRedirect, $model->getId());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the tag.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [static::PARAM_CRUD_ID => $this->getRequest()->getParam(static::PARAM_CRUD_ID)]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
