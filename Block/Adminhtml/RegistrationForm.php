<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml;

class RegistrationForm extends \Magento\Backend\Block\Widget\Form\Generic
{
    private const WEBSITE_PRIVACY_URL = 'https://m2epro.com/privacy';
    private const WEBSITE_TERMS_URL = 'https://m2epro.com/terms-and-conditions';

    private \M2E\Core\Helper\Magento\Country $countryHelper;
    private \M2E\Core\Helper\Magento\Admin $magentoAdminHelper;
    private \M2E\Core\Model\RegistrationService $registrationService;

    public function __construct(
        \M2E\Core\Helper\Magento\Country $countryHelper,
        \M2E\Core\Model\RegistrationService $registrationService,
        \M2E\Core\Helper\Magento\Admin $magentoAdminHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->countryHelper = $countryHelper;
        $this->registrationService = $registrationService;
        $this->magentoAdminHelper = $magentoAdminHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getUserForm(): \Magento\Framework\Data\Form
    {
        $userInfo = $this->getUserInfo();
        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'edit_form',
            ],
        ]);

        $fieldset = $form->addFieldset(
            'block_registration_form',
            [
                'legend' => '',
            ]
        );

        $fieldset->addField(
            'form_email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'value' => $userInfo['email'] ?? '',
                'class' => 'validate-email validate-length maximum-length-80',
                'required' => true,
                'disabled' => false,
            ]
        );

        $fieldset->addField(
            'first_name',
            'text',
            [
                'name' => 'firstname',
                'label' => __('First Name'),
                'value' => $userInfo['firstname'] ?? '',
                'class' => 'validate-length maximum-length-40',
                'required' => true,
                'disabled' => false,
            ]
        );

        $fieldset->addField(
            'last_name',
            'text',
            [
                'name' => 'lastname',
                'label' => __('Last Name'),
                'value' => $userInfo['lastname'] ?? '',
                'class' => 'validate-length maximum-length-40',
                'required' => true,
                'disabled' => false,
            ]
        );

        $fieldset->addField(
            'phone',
            'text',
            [
                'name' => 'phone',
                'label' => __('Phone'),
                'value' => $userInfo['phone'] ?? '',
                'class' => 'validate-length maximum-length-40',
                'required' => true,
                'disabled' => false,
            ]
        );

        $countries = $this->countryHelper->asOptions();
        unset($countries[0]);
        $fieldset->addField(
            'country',
            'select',
            [
                'name' => 'country',
                'label' => __('Country'),
                'value' => $userInfo['country'] ?? '',
                'class' => 'validate-length maximum-length-40',
                'values' => $countries,
                'required' => true,
                'disabled' => false,
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'value' => $userInfo['city'] ?? '',
                'class' => 'validate-length maximum-length-40',
                'required' => true,
                'disabled' => false,
            ]
        );

        $fieldset->addField(
            'postal_code',
            'text',
            [
                'name' => 'postal_code',
                'label' => __('Postal Code'),
                'value' => $userInfo['postal_code'] ?? '',
                'class' => 'validate-length maximum-length-40',
                'required' => true,
                'disabled' => false,
            ]
        );

        $privacyUrl = self::WEBSITE_PRIVACY_URL;
        $termsUrl = self::WEBSITE_TERMS_URL;

        $fieldset->addField(
            'licence_agreement',
            'checkbox',
            [
                'name' => 'licence_agreement',
                'class' => 'admin__control-checkbox',
                'label' => __('Terms and Privacy'),
                'checked' => false,
                'value' => 1,
                'required' => true,
                'after_element_html' => __(
                    "&nbsp; I agree to  <a href=\"{$termsUrl}\" target=\"_blank\">terms</a> and
<a href=\"{$privacyUrl}\" target=\"_blank\">privacy policy</a>"
                ),
            ]
        );

        return $form;
    }

    private function getUserInfo(): array
    {
        $userInfo = $this->magentoAdminHelper->getCurrentInfo();
        $user = $this->registrationService->findUser();

        if ($user === null) {
            return $userInfo;
        }

        $earlierFormData = [];

        $earlierFormData['email'] = $user->getEmail();
        $earlierFormData['first_name'] = $user->getFirstname();
        $earlierFormData['last_name'] = $user->getLastname();
        $earlierFormData['phone'] = $user->getPhone();
        $earlierFormData['country'] = $user->getCountry();
        $earlierFormData['city'] = $user->getCity();
        $earlierFormData['postal_code'] = $user->getPostalCode();

        return array_merge($userInfo, $earlierFormData);
    }
}
