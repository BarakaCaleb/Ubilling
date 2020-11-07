<?php

/**
 * Renders events which happens with some switch devices
 */
class SwitchHistory {

    /**
     * Contains existing switch ID
     *
     * @var int
     */
    protected $switchId = '';

    /**
     * Weblogs data model placeholder
     *
     * @var object
     */
    protected $weblogs = '';

    /**
     * System message helper object placeholder
     *
     * @var object
     */
    protected $messages = '';

    /**
     * Contains current instance render type
     *
     * @var bool
     */
    protected $ajaxFlag = false;

    /**
     * Switch profile back URL
     */
    const URL_SWPROFILE = '?module=switches&edit=';

    /**
     * Default controller module URL
     */
    const URL_ME = '?module=switchhistory';

    /**
     * Creates new report instance
     * 
     * @param int $swithId
     * 
     * @return void
     */
    public function __construct($switchId = '') {
        $this->setSwitchId($switchId);
        $this->setRenderType();
        $this->initMessages();
        $this->initWeblogs();
    }

    /**
     * Sets current instance render type
     * 
     * @return void
     */
    protected function setRenderType() {
        if (ubRouting::checkGet('ajax')) {
            $this->ajaxFlag = true;
        }
    }

    /**
     * Inits system messages object instance for further usage
     * 
     * @return void
     */
    protected function initMessages() {
        $this->messages = new UbillingMessageHelper();
    }

    /**
     * Inits weblogs database model
     * 
     * @return void
     */
    protected function initWeblogs() {
        $this->weblogs = new NyanORM('weblogs');
    }

    /**
     * Sets required switchId to protected prop
     * 
     * @param int $switchId
     * 
     * @return void
     */
    protected function setSwitchId($switchId) {
        $switchId = ubRouting::filters($switchId, 'int');
        if (!empty($switchId)) {
            $this->switchId = $switchId;
        }
    }

    /**
     * Renders report about all events which happens with some switch
     * 
     * @return string
     */
    public function renderReport() {
        $result = '';
        if ($this->ajaxFlag) {
            $result .= wf_tag('div', false, '', 'style="height:200px; overflow:auto;"');
            $result .= wf_tag('strong') . __('History of switch life') . wf_tag('strong', true).' ';
            $result .= wf_Link(self::URL_ME.'&switchid='.$this->switchId, wf_img('skins/arrow_right_green.png',__('View full')));
        }

        if (!empty($this->switchId)) {
            $eventMask = '%SWITCH%';
            $switchMask = '%[' . $this->switchId . ']%';

            $this->weblogs->where('event', 'LIKE', $eventMask);
            $this->weblogs->where('event', 'LIKE', $switchMask);
            $this->weblogs->orderBy('id', 'desc');
            $allEvents = $this->weblogs->getAll();

            if (!empty($allEvents)) {
                $cells = wf_TableCell(__('ID'));
                $cells .= wf_TableCell(__('Date'));
                $cells .= wf_TableCell(__('Admin'));
                $cells .= wf_TableCell(__('IP'));
                $cells .= wf_TableCell(__('Event'));
                $rows = wf_TableRow($cells, 'row1');

                foreach ($allEvents as $io => $each) {
                    $cells = wf_TableCell($each['id']);
                    $cells .= wf_TableCell($each['date']);
                    $cells .= wf_TableCell($each['admin']);
                    $cells .= wf_TableCell($each['ip']);
                    $cells .= wf_TableCell($each['event']);
                    $rows .= wf_TableRow($cells, 'row5');
                }
                $result .= wf_TableBody($rows, '100%', 0, 'sortable');
            } else {
                $result .= $this->messages->getStyledMessage(__('Nothing to show'), 'warning');
            }
        } else {
            $result .= $this->messages->getStyledMessage(__('Something went wrong') . ': EX_NO_SWITCHID', 'error');
        }

        //no additional formatting required
        if ($this->ajaxFlag) {
            $result .= wf_tag('div', true);
            die($result);
        }

        $result .= wf_delimiter();
        $result .= wf_BackLink(self::URL_SWPROFILE . $this->switchId);

        return($result);
    }

}
