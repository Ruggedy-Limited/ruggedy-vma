<?php

namespace App\Models;

use App\Contracts\CollectsScanOutput;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NmapModel implements CollectsScanOutput
{
    /**
     * This pattern is derived from Symfony\Component\Validator\Constraints\UrlValidator (2.7.4).
     * (c) Fabien Potencier <fabien@symfony.com> http://symfony.com
     *
     * It was necessary to create this derived version to allow for hostnames with no scheme/protocol, e.g. http://
     */
    const VALIDATION_REGEX_HOSTNAME = '~^(((aaa|aaas|about|acap|acct|acr|adiumxtra|afp|afs|aim|apt|attachment|aw|barion'
        . '|beshare|bitcoin|blob|bolo|callto|cap|chrome|chrome-extension|cid|coap|coaps|com-eventbrite-attendee|content'
        . '|crid|cvs|data|dav|dict|dlna-playcontainer|dlna-playsingle|dns|dntp|dtn|dvb|ed2k|example|facetime|fax|feed'
        . '|feedready|file|filesystem|finger|fish|ftp|geo|gg|git|gizmoproject|go|gopher|gtalk|h323|ham|hcp|http|https'
        . '|iax|icap|icon|im|imap|info|iotdisco|ipn|ipp|ipps|irc|irc6|ircs|iris|iris.beep|iris.lwz|iris.xpc|iris.xpcs'
        . '|itms|jabber|jar|jms|keyparc|lastfm|ldap|ldaps|magnet|mailserver|mailto|maps|market|message|mid|mms|modem'
        . '|ms-help|ms-settings|ms-settings-airplanemode|ms-settings-bluetooth|ms-settings-camera|ms-settings-cellular'
        . '|ms-settings-cloudstorage|ms-settings-emailandaccounts|ms-settings-language|ms-settings-location'
        . '|ms-settings-lock|ms-settings-nfctransactions|ms-settings-notifications|ms-settings-power'
        . '|ms-settings-privacy|ms-settings-proximity|ms-settings-screenrotation|ms-settings-wifi|ms-settings-workplace'
        . '|msnim|msrp|msrps|mtqp|mumble|mupdate|mvn|news|nfs|ni|nih|nntp|notes|oid|opaquelocktoken|pack|palm|paparazzi'
        . '|pkcs11|platform|pop|pres|prospero|proxy|psyc|query|redis|rediss|reload|res|resource|rmi|rsync|rtmfp|rtmp'
        . '|rtsp|rtsps|rtspu|secondlife|service|session|sftp|sgn|shttp|sieve|sip|sips|skype|smb|sms|smtp|snews|snmp'
        . '|soap.beep|soap.beeps|soldat|spotify|ssh|steam|stun|stuns|submit|svn|tag|teamspeak|tel|teliaeid|telnet|tftp'
        . '|things|thismessage|tip|tn3270|turn|turns|tv|udp|unreal|urn|ut2004|vemmi|ventrilo|videotex|view-source|wais'
        . '|webcal|ws|wss|wtai|wyciwyg|xcon|xcon-userid|xfire|xmlrpc\.beep|xmlrpc.beeps|xmpp|xri|ymsgr'
        . '|z39\.50|z39\.50r|z39\.50s))://)?' # protocol
        . '(([\pL\pN-]+:)?([\pL\pN-]+)@)?' # basic auth
        . '(([\pL\pN\pS-\.])+(\.?([\pL]|xn\-\-[\pL\pN-]+)+\.?)' # a domain name
        . '|' # or
        . '\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}' # a IP address
        . '|' # or
        . '\[(?:(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){6})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))'
        . '|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]'
        . '|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:::(?:(?:(?:[0-9a-f]{1,4})):){5})(?:(?:(?:(?:(?:[0-9a-f]{1,4}))'
        . ':(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]'
        . '|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){4})'
        . '(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]'
        . '|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))'
        . '|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,1}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){3})'
        . '(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]'
        . '|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))'
        . '|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,2}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){2})'
        . '(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]'
        . '|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))'
        . '|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,3}(?:(?:[0-9a-f]{1,4})))?::(?:(?:[0-9a-f]{1,4})):)'
        . '(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))'
        . '|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.)'
        . '{3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,4}'
        . '(?:(?:[0-9a-f]{1,4})))?::)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]'
        . '|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))'
        . '|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,5}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:[0-9a-f]{1,4})))'
        . '|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,6}(?:(?:[0-9a-f]{1,4})))?::))))\])'  # a IPv6 address
        . '(:[0-9]+)?' # a port (optional)
        . '(/?|/\S+|\?\S*|\#\S*)$~ixu'; # a /, nothing, a / with something, a query or a fragment

    /** @var string */
    protected $hostname;

    /** @var string */
    protected $osVendor;

    /** @var string */
    protected $osVersion;

    /** @var string */
    protected $cpe;

    /** @var string */
    protected $ipV4;

    /** @var string */
    protected $ipV6;

    /** @var string */
    protected $macAddress;

    /** @var string */
    protected $macVendor;

    /** @var Collection */
    protected $ports;

    /** @var Carbon */
    protected $uptime;

    /** @var Carbon */
    protected $lastBoot;

    /** @var Collection */
    protected $accuracies;

    /**
     * NmapModel constructor.
     */
    public function __construct()
    {
        $this->ports      = new Collection();
        $this->accuracies = new Collection();
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param string $hostname
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * @return string
     */
    public function getOsVendor()
    {
        return $this->osVendor;
    }

    /**
     * @param string $osVendor
     */
    public function setOsVendor($osVendor)
    {
        $this->osVendor = $osVendor;
    }

    /**
     * @return string
     */
    public function getOsVersion()
    {
        return $this->osVersion;
    }

    /**
     * @param string $osVersion
     */
    public function setOsVersion($osVersion)
    {
        $this->osVersion = $osVersion;
    }

    /**
     * @return string
     */
    public function getCpe()
    {
        return $this->cpe;
    }

    /**
     * @param string $cpe
     */
    public function setCpe($cpe)
    {
        $this->cpe = $cpe;
    }

    /**
     * @return string
     */
    public function getIpV4()
    {
        return $this->ipV4;
    }

    /**
     * @param string $ipV4
     */
    public function setIpV4($ipV4)
    {
        $this->ipV4 = $ipV4;
    }

    /**
     * @return string
     */
    public function getIpV6()
    {
        return $this->ipV6;
    }

    /**
     * @param string $ipV6
     */
    public function setIpV6($ipV6)
    {
        $this->ipV6 = $ipV6;
    }

    /**
     * @return string
     */
    public function getMacAddress()
    {
        return $this->macAddress;
    }

    /**
     * @param string $macAddress
     */
    public function setMacAddress($macAddress)
    {
        $this->macAddress = $macAddress;
    }

    /**
     * @return array
     */
    public function getMacVendor()
    {
        return $this->macVendor;
    }

    /**
     * @param string $macVendor
     */
    public function setMacVendor($macVendor)
    {
        $this->macVendor = $macVendor;
    }

    /**
     * @return Collection
     */
    public function getPorts()
    {
        return $this->ports;
    }

    /**
     * @param Collection $ports
     */
    public function setPorts($ports)
    {
        $this->ports = $ports;
    }

    /**
     * Add an open port
     *
     * @param PortModel $port
     */
    public function addPort(PortModel $port)
    {
        $this->ports->push($port);
    }

    /**
     * Remove an open port
     *
     * @param PortModel $port
     * @return bool
     */
    public function removePort(PortModel $port)
    {
        $index = $this->ports->search($port, true);
        if (empty($index)) {
            return false;
        }

        $this->ports->pull($index);
        return true;
    }

    /**
     * @return Carbon
     */
    public function getUptime()
    {
        return $this->uptime;
    }

    /**
     * @param Carbon $uptime
     */
    public function setUptime($uptime)
    {
        $this->uptime = $uptime;
    }

    /**
     * @return Carbon
     */
    public function getLastBoot()
    {
        return $this->lastBoot;
    }

    /**
     * @param Carbon $lastBoot
     */
    public function setLastBoot($lastBoot)
    {
        $this->lastBoot = $lastBoot;
    }

    /**
     * @return Collection
     */
    public function getAccuracies()
    {
        return $this->accuracies;
    }

    /**
     * Get the current accuracy for the given field
     *
     * @param string $field
     * @return int
     */
    public function getCurrentAccuracyFor(string $field): int
    {
        return $this->accuracies->get($field, 0);
    }

    /**
     * @param Collection $accuracies
     */
    public function setAccuracies($accuracies)
    {
        $this->accuracies = $accuracies;
    }

    /**
     * Set the accuracy for the given field
     *
     * @param string $field
     * @param int $accuracy
     */
    public function setCurrentAccuracyFor(string $field, int $accuracy)
    {
        $this->accuracies->put($field, $accuracy);
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    public function exportForAsset(): Collection
    {
        return new Collection([
            'hostname'      => $this->getHostname(),
            'cpe'           => $this->getCpe(),
            'vendor'        => $this->getOsVendor(),
            'os_version'    => $this->getOsVersion(),
            'ip_address_v4' => $this->getIpV4(),
            'ip_address_v6' => $this->getIpV6(),
            'mac_address'   => $this->getMacAddress(),
            'mac_vendor'    => $this->getMacVendor(),
        ]);
    }
}