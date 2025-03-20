<?php

namespace M2E\Core\Model\Magento;

class Customer extends \Magento\Framework\DataObject
{
    public const FAKE_EMAIL_POSTFIX = '@dummy.email';

    private \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory;
    private \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory;
    private \Magento\Framework\Math\Random $mathRandom;
    private \Magento\Customer\Model\CustomerFactory $customerFactory;
    private \Magento\Customer\Model\AddressFactory $addressFactory;
    private \Magento\Customer\Model\Customer $customer;

    public function __construct(
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory
    ) {
        parent::__construct();
        $this->customerDataFactory = $customerDataFactory;
        $this->addressDataFactory = $addressDataFactory;
        $this->mathRandom = $mathRandom;
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    public function buildCustomer()
    {
        $password = $this->mathRandom->getRandomString(7);

        /**
         * Magento can replace customer group to the default.
         * vendor/magento/module-customer/Observer/AfterAddressSaveObserver.php:121
         * Can be disabled here:
         * Customers -> Customer Configuration -> Create new account options -> Automatic Assignment to Customer Group
         */
        $customerData = $this->customerDataFactory->create()
                                                  ->setPrefix($this->getData('customer_prefix'))
                                                  ->setFirstname($this->getData('customer_firstname'))
                                                  ->setMiddlename($this->getData('customer_middlename'))
                                                  ->setLastname($this->getData('customer_lastname'))
                                                  ->setSuffix($this->getData('customer_suffix'))
                                                  ->setWebsiteId($this->getData('website_id'))
                                                  ->setGroupId($this->getData('group_id'))
                                                  ->setEmail($this->getData('email'))
                                                  ->setConfirmation($password);

        $this->customer = $this->customerFactory->create();
        $this->customer->updateData($customerData);
        $this->customer->setPassword($password);
        $this->customer->save();

        // Add customer address
        // ---------------------------------------
        $addressModel = $this->addressFactory->create();
        $this->_updateAddress($addressModel);

        $addressData = $this->addressDataFactory->create()
                                                ->setIsDefaultBilling(true)
                                                ->setIsDefaultShipping(true);

        $addressModel->updateData($addressData);

        $addressModel->setCustomer($this->customer);
        $addressModel->save();

        $this->customer->addAddress($addressModel);
        // ---------------------------------------
    }

    public function updateAddress(\Magento\Customer\Model\Customer $customerObject)
    {
        $this->customer = $customerObject;

        foreach ($customerObject->getPrimaryAddresses() as $addressModel) {
            $this->_updateAddress($addressModel);
            $addressModel->save();
        }
    }

    private function _updateAddress(\Magento\Customer\Model\Address $addressModel)
    {
        $street = $this->getData('street');
        if (!is_array($street)) {
            $street = explode('; ', $street);
        }

        $addressData = $this->addressDataFactory->create()
                                                ->setPrefix($this->getData('prefix'))
                                                ->setFirstname($this->getData('firstname'))
                                                ->setMiddlename($this->getData('middlename'))
                                                ->setLastname($this->getData('lastname'))
                                                ->setSuffix($this->getData('suffix'))
                                                ->setCountryId($this->getData('country_id'))
                                                ->setCity($this->getData('city'))
                                                ->setPostcode($this->getData('postcode'))
                                                ->setTelephone($this->getData('telephone'))
                                                ->setStreet($street)
                                                ->setCompany($this->getData('company'));

        $addressModel->updateData($addressData);
        /**
         * Updating 'region_id' value to null will be skipped in
         * vendor/magento/framework/Reflection/DataObjectProcessor.php::buildOutputDataArray()
         * So, we are forced to use separate setter for 'region_id' to bypass this validation
         */
        $addressModel->setRegionId($this->getData('region_id'));
    }
}
