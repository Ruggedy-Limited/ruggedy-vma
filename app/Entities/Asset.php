<?php

namespace App\Entities;

use App\Contracts\GeneratesUniqueHash;
use App\Contracts\HasIdColumn;
use App\Contracts\RelatesToFiles;
use App\Contracts\SystemComponent;
use App\Entities\Base\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection;

/**
 * App\Entities\Asset
 *
 * @ORM\Entity(repositoryClass="App\Repositories\AssetRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Asset extends Base\Asset implements SystemComponent, HasIdColumn, RelatesToFiles, GeneratesUniqueHash
{
    /** Regular expressions used for validating the relevant Asset data fields */
    const REGEX_CPE         = '~(cpe:(\d)?(\.\d)?(/[aho])(([:]{1,3})([\pL\pN\pS_])+)*)~i';
    const REGEX_MAC_ADDRESS = '/^([0-9A-Fa-f]{2}[:-]{1}){5}([0-9A-Fa-f]{2})$/';
    const REGEX_OS_VERSION  = '/(Linux|Mac|Microsoft)/i';

    const REGEX_PROTOCOL    = '(((aaa|aaas|about|acap|acct|acr|adiumxtra|afp|afs|aim|apt|attachment|aw|barion'
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
        . '|z39\.50|z39\.50r|z39\.50s))://)';

    const REGEX_BASIC_AUTH  = '(([\pL\pN\-]+:)?([\pL\pN\-]+)@)';
    const REGEX_DOMAIN_NAME = '([\pL\pN\pS\-\.])+(\.([\pL]|xn\-\-[\pL\pN\-]+)+\.?)';
    const REGEX_PORT_NUMBER = '(:[0-9]+)';

    /**
     * This pattern is derived from Symfony\Component\Validator\Constraints\UrlValidator (2.7.4).
     * (c) Fabien Potencier <fabien@symfony.com> http://symfony.com
     *
     * It was necessary to create this derived version to allow for hostnames with no scheme/protocol, e.g. http://
     */
    const REGEX_HOSTNAME    = '~^' . self::REGEX_PROTOCOL . '?' # protocol
        . self::REGEX_BASIC_AUTH . '?' # basic auth
        . self::REGEX_DOMAIN_NAME # a domain name
        . self::REGEX_PORT_NUMBER . '?' # a port (optional)
        . '(/?|/\S+|\?\S*|\#\S*)$~ixu'; # a /, nothing, a / with something, a query or a fragment

    const REGEX_NETBIOS_NAME = "%^[^\\/:\*\?\"<>\|]+$%";

    /** Valid OS Vendor values */
    const OS_VENDOR_LINUX     = 'Linux';
    const OS_VENDOR_APPLE     = 'Apple';
    const OS_VENDOR_MICROSOFT = 'Microsoft';
    const OS_VENDOR_UNKNOWN   = 'Unknown';

    /** String value to use when the Asset name cannot be automatically assigned */
    const ASSET_NAME_UNNAMED = 'Unnamed Asset';

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="assets", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    /**
     * @ORM\ManyToMany(targetEntity="SoftwareInformation", inversedBy="assets")
     * @ORM\JoinTable(name="asset_software_information")
     */
    protected $relatedSoftwareInformation;

    /**
     * @ORM\ManyToMany(targetEntity="Vulnerability", inversedBy="assets")
     * @ORM\JoinTable(name="assets_vulnerabilities")
     */
    protected $vulnerabilities;

    /**
     * @ORM\ManyToMany(targetEntity="Audit", inversedBy="assets")
     * @ORM\JoinTable(name="assets_audits")
     */
    protected $audits;

    /**
     * @ORM\ManyToMany(targetEntity="File", mappedBy="assets", indexBy="id")
     */
    protected $files;

    /**
     * Asset constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->relatedSoftwareInformation = new ArrayCollection();
        $this->vulnerabilities            = new ArrayCollection();
        $this->audits                     = new ArrayCollection();
        $this->files                      = new ArrayCollection();
    }

    /**
     * @param string $ip_address_v4
     * @return Base\Asset
     */
    public function setIpAddressV4($ip_address_v4)
    {
        parent::setIpAddressV4($ip_address_v4);
        return $this->conditionallySetName();
    }

    /**
     * @param string $hostname
     * @return Base\Asset
     */
    public function setHostname($hostname)
    {
        parent::setHostname(
            $this->sanitiseHostname($hostname)
        );

        return $this->conditionallySetName();
    }

    /**
     * If the name is not already set and one of hostname or IPv4 address is set, then assign a name
     *
     * @return Base\Asset
     */
    protected function conditionallySetName()
    {
        if (isset($this->id)) {
            return $this;
        }

        return $this->setName($this->getHostname() ?? $this->getIpAddressV4());
    }

    /**
     * Strip the given value down to the base hostname
     *
     * @param string $hostname
     * @return mixed|null
     */
    protected function sanitiseHostname(string $hostname) {
        if (!isset($hostname)) {
            return null;
        }

        if (preg_match("~^" . Asset::REGEX_DOMAIN_NAME . "$~", $hostname)) {
            return $hostname;
        }

        $hostname = parse_url($hostname, PHP_URL_HOST);
        return $hostname;
    }

    /**
     * Override the parent method to include a sanitising of this field
     *
     * @param string $mac_address
     * @return Base\Asset
     */
    public function setMacAddress($mac_address)
    {
        return parent::setMacAddress(
            $this->sanitiseMacAddress($mac_address)
        );
    }

    /**
     * Override the parent method to attempt to extract a valid vendor name from the given string
     *
     * @param string $vendor
     * @return Base\Asset
     */
    public function setVendor($vendor)
    {
        // Parameter is a valid vendor name
        if (static::isValidOsVendor($vendor)) {
            return parent::setVendor($vendor);
        }

        // Attempt to match a valid vendor name in the given parameter value
        preg_match(static::getValidVendorsRegex(), $vendor, $matches);

        // No match was found, set the OS vendor to 'Unknown'
        if (empty($matches[1]) || !static::isValidOsVendor($matches[1])) {
            return parent::setVendor(self::OS_VENDOR_UNKNOWN);
        }

        // Set the OS vendor to the matched value
        return parent::setVendor($matches[1]);
    }

    /**
     * Override the parent method to sanitise and convert any non-date objects into a Carbon instance
     *
     * @param \DateTime $lastBoot
     * @return Base\Asset
     */
    public function setLastBoot($lastBoot)
    {
        if (empty($this->sanitiseDate($lastBoot))) {
            return $this;
        }

        return parent::setLastBoot($this->sanitiseDate($lastBoot));
    }

    /**
     * Get the parent Entity of this Entity
     *
     * @return Base\Workspace
     */
    public function getParent()
    {
        return $this->workspace;
    }

    /**
     * Convenience method for setting the parent relation
     *
     * @param Base\Workspace $workspace
     * @return Base\Asset
     */
    public function setParent(Base\Workspace $workspace)
    {
        return parent::setWorkspace($workspace);
    }

    /**
     * @return ArrayCollection
     */
    public function getRelatedSoftwareInformation()
    {
        return $this->relatedSoftwareInformation;
    }

    /**
     * @param SoftwareInformation $softwareInformation
     * @return $this
     */
    public function addSoftwareInformation(SoftwareInformation $softwareInformation)
    {
        $softwareInformation->addAsset($this);
        $this->relatedSoftwareInformation[] = $softwareInformation;

        return $this;
    }

    /**
     * @param SoftwareInformation $softwareInformation
     * @return $this
     */
    public function removeSoftwareInformation(SoftwareInformation $softwareInformation)
    {
        $softwareInformation->removeAsset($this);
        $this->relatedSoftwareInformation->removeElement($softwareInformation);

        return $this;
    }

    /**
     * Relate the given Vulnerability to this Asset instance and create the relation on the inverse Vulnerability
     * entity
     *
     * @param Vulnerability $vulnerability
     * @return Base\Asset
     */
    public function addVulnerability(Vulnerability $vulnerability)
    {
        if ($this->vulnerabilities->contains($vulnerability)) {
            return $this;
        }

        $vulnerability->addAsset($this);

        $vulnerabilityKey = $vulnerability->getId() ?? $vulnerability->getHash();
        $this->vulnerabilities[$vulnerabilityKey] = $vulnerability;

        return $this;
    }

    /**
     * Remove the relation between this Asset and given Vulnerability and remove the relation on the inverse
     * Vulnerability entity
     *
     * @param Vulnerability $vulnerability
     * @return $this
     */
    public function removeVulnerability(Vulnerability $vulnerability)
    {
        $vulnerability->removeAsset($this);
        $this->vulnerabilities->removeElement($vulnerability);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getVulnerabilities()
    {
        return $this->vulnerabilities;
    }

    /**
     * Relate the given Audit to this Asset instance and create the relation on the inverse Audit entity
     *
     * @param Audit $audit
     * @return Base\Asset
     */
    public function addAudit(Audit $audit)
    {
        if ($this->audits->contains($audit)) {
            return $this;
        }

        $audit->addAsset($this);

        $auditKey = $audit->getId() ?? $audit->getHash();
        $this->audits[$auditKey] = $audit;

        return $this;
    }

    /**
     * Remove the relation between this Asset and given Audit and remove the relation on the inverse Audit entity
     *
     * @param Audit $audit
     * @return $this
     */
    public function removeAudit(Audit $audit)
    {
        $audit->removeAsset($this);
        $this->audits->removeElement($audit);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAudits()
    {
        return $this->audits;
    }

    /**
     * Override the parent method to make the change to the inverse OpenPort relation
     *
     * @param Base\OpenPort $openPort
     * @return Base\Asset
     */
    public function addOpenPort(Base\OpenPort $openPort)
    {
        $openPort->setAsset($this);
        return parent::addOpenPort($openPort);
    }

    /**
     * @param File $file
     * @return $this
     */
    public function addFile(File $file)
    {
        $this->files[$file->getId()] = $file;

        return $this;
    }

    /**
     * @param File $file
     * @return $this
     */
    public function removeFile(File $file)
    {
        $this->files->removeElement($file);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Get an array of valid OS vendors
     *
     * @return Collection
     */
    public static function getValidOsVendors(): Collection
    {
        return new Collection([
            self::OS_VENDOR_LINUX,
            self::OS_VENDOR_APPLE,
            self::OS_VENDOR_MICROSOFT,
        ]);
    }

    /**
     * Check if the given vendor name is one of the valid OS vendors
     *
     * @param string $vendorName
     * @return bool
     */
    public static function isValidOsVendor(string $vendorName): bool
    {
        return !empty($vendorName) && static::getValidOsVendors()->contains($vendorName);
    }

    /**
     * Get a regex that will check a string for a valid OS Vendor
     *
     * @return string
     */
    public static function getValidVendorsRegex(): string
    {
        return "/(" . static::getValidOsVendors()->implode("|") . ")/";
    }

    /**
     * Get a SHA1 hash of the unique key of hostname, IPv4 address and netbios address
     *
     * @return string
     */
    public function getUniqueAssetHash()
    {
        return sha1($this->getHostname() . $this->getIpAddressV4() . $this->getNetbios());
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getHash(): string
    {
        return AbstractEntity::generateUniqueHash($this->getUniqueKeyColumns());
    }

    /**
     * @inheritdoc
     * @return Collection
     */
    public function getUniqueKeyColumns(): Collection
    {
        return collect([
            parent::HOSTNAME      => $this->hostname,
            parent::IP_ADDRESS_V4 => $this->ip_address_v4,
            parent::NETBIOS       => $this->netbios,
        ]);
    }

    /**
     * Sanitise the mac addresses found in the Nexpose scan by adding colons after every second character
     *
     * @param string $macAddress
     * @return string|null
     */
    protected function sanitiseMacAddress(string $macAddress)
    {
        if (empty($macAddress) || preg_match(Asset::REGEX_MAC_ADDRESS, $macAddress)) {
            return $macAddress;
        }

        // Split the string into an array where each elements contains two characters and create a Collection
        $macAddressChars = new Collection(
            str_split($macAddress, 2)
        );

        // Implode the Collection with a colon as glue
        $sanitisedMacAddress = $macAddressChars->implode(":");

        // Validate the sanitised MAC address against the regex
        if (!preg_match(Asset::REGEX_MAC_ADDRESS, $sanitisedMacAddress)) {
            return null;
        }

        return $sanitisedMacAddress;
    }
}