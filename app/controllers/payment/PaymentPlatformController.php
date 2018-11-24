<?php

class PaymentPlatformController extends AdminBaseController {

    protected $modelName = 'PaymentPlatform';

    protected function beforeRender() {
        $this->setVars('aValidStatus', PaymentPlatform::$validStatus);
        $this->setVars('aIconTypes', PaymentPlatform::$aIconTypes);
        parent::beforeRender();
    }

    public function setDefault($id) {
        $oPlatform = PaymentPlatform::find($id);
        if (!is_object($oPlatform)) {
            return $this->goBack('error', __('_paymentplatform.missing-data'));
        }
        $oPlatform->is_default = 1;
        $bSucc = $oPlatform->save();
        if ($bSucc) {
            return $this->goBackToIndex('success', __('_paymentplatform.default-success'));
        } else {
            return $this->goBack('error', __('_paymentplatform.default-failed'));
        }
    }

}
