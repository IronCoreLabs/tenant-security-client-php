<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: document_header.proto

namespace Proto\IronCoreLabs;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>proto.ironcorelabs.cmk.V3DocumentHeader</code>
 */
class V3DocumentHeader extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bytes sig = 1;</code>
     */
    protected $sig = '';
    protected $header;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $sig
     *     @type \Proto\IronCoreLabs\DataControlPlatformHeader $data_control
     *     @type \Proto\IronCoreLabs\SaaSShieldHeader $saas_shield
     * }
     */
    public function __construct($data = NULL) {
        \Proto\IronCoreLabs\Metadata\DocumentHeader::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bytes sig = 1;</code>
     * @return string
     */
    public function getSig()
    {
        return $this->sig;
    }

    /**
     * Generated from protobuf field <code>bytes sig = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setSig($var)
    {
        GPBUtil::checkString($var, False);
        $this->sig = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.proto.ironcorelabs.cmk.DataControlPlatformHeader data_control = 2;</code>
     * @return \Proto\IronCoreLabs\DataControlPlatformHeader
     */
    public function getDataControl()
    {
        return $this->readOneof(2);
    }

    public function hasDataControl()
    {
        return $this->hasOneof(2);
    }

    /**
     * Generated from protobuf field <code>.proto.ironcorelabs.cmk.DataControlPlatformHeader data_control = 2;</code>
     * @param \Proto\IronCoreLabs\DataControlPlatformHeader $var
     * @return $this
     */
    public function setDataControl($var)
    {
        GPBUtil::checkMessage($var, \Proto\IronCoreLabs\DataControlPlatformHeader::class);
        $this->writeOneof(2, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.proto.ironcorelabs.cmk.SaaSShieldHeader saas_shield = 3;</code>
     * @return \Proto\IronCoreLabs\SaaSShieldHeader
     */
    public function getSaasShield()
    {
        return $this->readOneof(3);
    }

    public function hasSaasShield()
    {
        return $this->hasOneof(3);
    }

    /**
     * Generated from protobuf field <code>.proto.ironcorelabs.cmk.SaaSShieldHeader saas_shield = 3;</code>
     * @param \Proto\IronCoreLabs\SaaSShieldHeader $var
     * @return $this
     */
    public function setSaasShield($var)
    {
        GPBUtil::checkMessage($var, \Proto\IronCoreLabs\SaaSShieldHeader::class);
        $this->writeOneof(3, $var);

        return $this;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->whichOneof("header");
    }

}
