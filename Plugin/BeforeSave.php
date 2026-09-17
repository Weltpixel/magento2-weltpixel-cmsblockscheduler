<?php

namespace WeltPixel\CmsBlockScheduler\Plugin;

class BeforeSave
{
    /**
     * End of the open ended window given to a block that has never been scheduled.
     */
    const DEFAULT_VALID_TO = '2099-12-31 23:00:00';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * BeforeSave constructor.
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    )
    {
        $this->_date = $date;
    }

    /**
     * Give a newly created block an open ended schedule, and keep an existing one as it is.
     *
     * The default used to be applied on every save, and whether to apply it was decided from the
     * current http post rather than from the block being saved. Any save whose request did not
     * carry valid_from and valid_to therefore rewrote the stored window to "now until 2099",
     * which silently published a block that was scheduled for the future or had already expired.
     * The grid's inline edit posts only title, identifier and is_active, so renaming a scheduled
     * block was enough to trigger it.
     *
     * Nothing is read from the request now. The block being saved is the only source, and an
     * existing block keeps the window it already has: a field the submission did not carry is
     * restored from the loaded original before the write. That restore is necessary because
     * cms_block is persisted through Magento\Framework\EntityManager\EntityManager, which writes
     * the whole declared column set, so a field that is merely missing from the model is written
     * as null rather than left out of the statement.
     *
     * @param \Magento\Cms\Model\BlockRepository $subject
     * @param $block
     * @return void
     */
    public function beforeSave(\Magento\Cms\Model\BlockRepository $subject, $block) {
        /** A block that does not exist yet is the only one that gets a default window. */
        $isNew = !$block->getId();

        $this->_normalizeDate(
            $subject,
            $block,
            'valid_from',
            $isNew ? $this->_date->gmtDate() : null
        );
        $this->_normalizeDate(
            $subject,
            $block,
            'valid_to',
            $isNew ? $this->_date->gmtDate('Y-m-d H:i:s', self::DEFAULT_VALID_TO) : null
        );
    }

    /**
     * @param \Magento\Cms\Model\BlockRepository $subject
     * @param $block
     * @param string $field
     * @param string|null $default
     * @return void
     */
    protected function _normalizeDate($subject, $block, $field, $default)
    {
        if (!$block->hasData($field)) {
            if ($default !== null) {
                $block->setData($field, $default);

                return;
            }

            /**
             * An existing block whose submission did not carry this field: put the stored value
             * back so the save writes it unchanged.
             */
            $stored = $this->_getStoredDate($subject, $block, $field);
            if ($stored !== null) {
                $block->setData($field, $stored);
            }

            return;
        }

        if (!$this->_isBlankDate($block->getData($field))) {
            return;
        }

        /**
         * Blank or unparseable: there is no schedule. For an existing block that means null
         * rather than an invented window, and it also keeps the literal 'Invalid date' the date
         * picker submits for a cleared field from reaching a datetime column.
         */
        $block->setData($field, $default);
    }

    /**
     * The loaded original is what the two admin save paths both provide, since each of them loads
     * the block before applying the submitted values. The repository is only consulted when there
     * is no original to read, which is the case for a model that was built rather than loaded.
     *
     * @param \Magento\Cms\Model\BlockRepository $subject
     * @param $block
     * @param string $field
     * @return string|null
     */
    protected function _getStoredDate($subject, $block, $field)
    {
        $original = $block->getOrigData($field);
        if ($original !== null && $original !== '') {
            return $original;
        }

        if ($block->getOrigData()) {
            /** The block was loaded and genuinely has no window stored. */
            return null;
        }

        try {
            return $subject->getById($block->getId())->getData($field);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * An empty window means the block is always visible, which is how isActive() reads it:
     * both of its date guards short circuit on an empty value. 'Invalid date' is what the date
     * picker submits for a field that was cleared.
     *
     * @param $value
     * @return bool
     */
    protected function _isBlankDate($value)
    {
        if (!is_scalar($value)) {
            return true;
        }

        $value = trim((string)$value);

        return $value === '' || $value === 'Invalid date';
    }
}
