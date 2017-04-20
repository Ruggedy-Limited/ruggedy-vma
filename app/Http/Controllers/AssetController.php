<?php

namespace App\Http\Controllers;

use App\Commands\CreateAsset;
use App\Entities\Asset;
use App\Http\Responses\AjaxResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * @Middleware("web")
 */
class AssetController extends AbstractController
{
    /**
     * Create an Asset via ajax from the Add Vulnerability view of the Ruggedy App
     *
     * @POST("/asset/create/{assetId}", as="asset.create", where={"fileId":"[0-9]+"})
     *
     * @param $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAsset($fileId)
    {
        // Only allow ajax requests to access this endpoint
        if (!$this->request->ajax()) {
            throw new MethodNotAllowedHttpException([], "That route cannot be accessed in that way.");
        }

        $ajaxResponse = new AjaxResponse();
        // Do validation but catch the ValidationExceptions to handle them here ourselves
        // because this is a JSON response to an ajax request
        try {
            $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());
        } catch (ValidationException $e) {
            $ajaxResponse->setMessage(
                view('partials.custom-message', ['bsClass' => 'danger', 'message' => $e->getMessage()])->render()
            );

            return response()->json($ajaxResponse);
        }

        // Create a new Asset entity and populate it from the request
        $asset = new Asset();
        $asset->setName($this->request->get('asset-name'))
              ->setCpe($this->request->get('asset-cpe'))
              ->setVendor($this->request->get('asset-vendor'))
              ->setIpAddressV4($this->request->get('asset-ip_address_v4'))
              ->setIpAddressV6($this->request->get('asset-ip_address_v6'))
              ->setHostname($this->request->get('asset-hostname'))
              ->setMacAddress($this->request->get('asset-mac_address'))
              ->setOsVersion($this->request->get('asset-os_version'))
              ->setNetbios($this->request->get('asset-netbios'));

        // Send the CreateAsset command over the command bus
        $command = new CreateAsset(intval($fileId), $asset);
        $asset = $this->sendCommandToBusHelper($command);

        // Handle command errors, set the custom-message partial HTML on AjaxResponse::$html and exit early
        if ($this->isCommandError($asset)) {
            $ajaxResponse->setHtml(
                view('partials.custom-message', [
                    'bsClass' => 'danger',
                    'message' => $asset->getMessage(),
                ])->render()
            );
            return response()->json($ajaxResponse);
        }

        // Set the asset partial HTML populated with the new Asset details on AjaxResponse::$html
        $ajaxResponse->setHtml(view('partials.related-asset', ['asset' => $asset])->render());
        $ajaxResponse->setMessage(view('partials.custom-message', [
            'bsClass' => 'success',
            'message' => 'A new Asset has been created.',
        ])->render());
        $ajaxResponse->setError(false);
        return response()->json($ajaxResponse);
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'asset-' . Asset::NAME          => 'bail|filled',
            'asset-' . Asset::CPE           => 'bail|regex:' . Asset::REGEX_CPE,
            'asset-' . Asset::VENDOR        => 'bail|regex:' . Asset::getValidVendorsRegex(),
            'asset-' . Asset::IP_ADDRESS_V4 => 'bail|ipv4',
            'asset-' . Asset::IP_ADDRESS_V6 => 'bail|ipv6',
            'asset-' . Asset::HOSTNAME      => 'bail|url',
            'asset-' . Asset::MAC_ADDRESS   => 'bail|regex:' . Asset::REGEX_MAC_ADDRESS,
            'asset-' . Asset::NETBIOS       => 'bail|regex:' . Asset::REGEX_NETBIOS_NAME,
        ];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationMessages(): array
    {
        return [
            'asset-' . Asset::NAME          => 'An Asset name is required.',
            'asset-' . Asset::CPE           => 'Please enter a valid CPE.',
            'asset-' . Asset::VENDOR        => 'Please enter a valid OS vendor.',
            'asset-' . Asset::IP_ADDRESS_V4 => 'Please enter a valid IPv4 address.',
            'asset-' . Asset::IP_ADDRESS_V6 => 'Please enter a valid IPv6 address.',
            'asset-' . Asset::HOSTNAME      => 'Please enter a valid hostname.',
            'asset-' . Asset::MAC_ADDRESS   => 'Please enter a valid MAC address.',
            'asset-' . Asset::NETBIOS       => 'Please enter a valid NETBIOS name.',
        ];
    }
}