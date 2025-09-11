<?php
namespace Josequal\Override\BetterPopup\Block;

class Popup extends \Mageplaza\BetterPopup\Block\Popup
{
    /**
     * Get Html Content popup
     *
     * @return mixed
     */
    public function getPopupContent()
    {
        $htmlConfig = $this->_helperData->getWhatToShowConfig('html_content');

        $search  = [
            '{{form_url}}',
            '{{url_loader}}',
            '{{email_icon_url}}',
            '{{bg_tmp2}}',
            '{{img_tmp3}}',
            '{{tmp3_icon_button}}',
            '{{bg_tmp4}}',
            '{{img_tmp4}}',
            '{{img_content_tmp5}}',
            '{{img_cap_tmp5}}',
            '{{img_email_tmp5}}'
        ];
        $replace = [
            $this->getFormActionUrl(),
            $this->getViewFileUrl('images/loader-1.gif'),
            $this->getViewFileUrl('Mageplaza_BetterPopup::images/mail-icon.png'),
            $this->getViewFileUrl('Mageplaza_BetterPopup::images/bg-tmp2.png'),
            $this->getViewFileUrl('Mageplaza_BetterPopup::images/template3/img-content.webp'),
            $this->getViewFileUrl('Mageplaza_BetterPopup::images/template3/button-icon.png'),
            $this->getViewFileUrl('Mageplaza_BetterPopup::images/template4/bg.png'),
            $this->getViewFileUrl('Mageplaza_BetterPopup::images/template4/img-content.webp'),
            $this->getViewFileUrl('Mageplaza_BetterPopup::images/template5/img-content.webp'),
            $this->getViewFileUrl('Mageplaza_BetterPopup::images/template5/img-cap.png'),
            $this->getViewFileUrl('Mageplaza_BetterPopup::images/template5/img-email.png')
        ];

        return str_replace($search, $replace, $htmlConfig);
    }
}