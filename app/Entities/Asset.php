<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection;

/**
 * App\Entities\Asset
 *
 * @ORM\Entity(repositoryClass="App\Repositories\AssetRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Asset extends Base\Asset implements SystemComponent
{
    /** Regular expressions used for validating the relevant Asset data fields */
    const REGEX_CPE         = '~(cpe:(\d)?(\.\d)?(/[aho])(([:]{1,3})([\pL\pN\pS_])+)*)~i';
    const REGEX_MAC_ADDRESS = '/^([0-9A-Fa-f]{2}[:-]{1}){5}([0-9A-Fa-f]{2})$/';
    const REGEX_OS_VERSION  = '/(Linux|Mac|Windows)/';

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

    const REGEX_BASIC_AUTH  = '(([\pL\pN-]+:)?([\pL\pN-]+)@)';
    const REGEX_PORT_NUMBER = '(:[0-9]+)';

    /**
     * This pattern is derived from Symfony\Component\Validator\Constraints\UrlValidator (2.7.4).
     * (c) Fabien Potencier <fabien@symfony.com> http://symfony.com
     *
     * It was necessary to create this derived version to allow for hostnames with no scheme/protocol, e.g. http://
     */
    const REGEX_HOSTNAME    = '~^' . self::REGEX_PROTOCOL . '?' # protocol
        . self::REGEX_BASIC_AUTH . '?' # basic auth
        . '([\pL\pN\pS-\.])+(\.?([\pL]|xn\-\-[\pL\pN-]+)+\.?)' # a domain name
        . self::REGEX_PORT_NUMBER . '?' # a port (optional)
        . '(/?|/\S+|\?\S*|\#\S*)$~ixu'; # a /, nothing, a / with something, a query or a fragment

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
     * Get the parent Entity of this Entity
     *
     * @return Base\Workspace
     */
    public function getParent()
    {
        return $this->getWorkspace();
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
}