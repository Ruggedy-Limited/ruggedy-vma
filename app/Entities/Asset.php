<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use Doctrine\ORM\Mapping as ORM;


/**
 * App\Entities\Asset
 *
 * @ORM\Entity(repositoryClass="App\Repositories\AssetRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Asset extends Base\Asset implements SystemComponent
{
    /** Regular expressions used for validating the relevant Asset data fields */
    const REGEX_CPE         = '/cpe:(?\d)(?\.\d):[aho](?::(?:[a-zA-Z0-9!"#$%&\'()*+,\\\-_.\/;<=>?@\[\]^`{|}~]|\\:)+){10}$/';
    const REGEX_MAC_ADDRESS = '/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/';

    /** Valid OS Vendor values */
    const OS_VENDOR_LINUX     = 'Linux';
    const OS_VENDOR_MAC       = 'Apple';
    const OS_VENDOR_MICROSOFT = 'Microsoft';
    const OS_VENDOR_UNKNOWN   = 'Unknown';

    /** String value to use when the Asset name cannot be automatically assigned */
    const ASSET_NAME_UNNAMED = 'Unnamed Asset';
    
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
     * @return array
     */
    public static function getValidOsVendors()
    {
        return [
            self::OS_VENDOR_LINUX,
            self::OS_VENDOR_MAC,
            self::OS_VENDOR_MICROSOFT,
        ];
    }

    /**
     * Check if the given vendor name is one of the valid OS vendors
     *
     * @param string $vendorName
     * @return bool
     */
    public static function isValidOsVendor(string $vendorName)
    {
        return !empty($vendorName) && in_array($vendorName, static::getValidOsVendors());
    }
}