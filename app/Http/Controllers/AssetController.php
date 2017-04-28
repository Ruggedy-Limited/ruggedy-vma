<?php

namespace App\Http\Controllers;

use App\Commands\CreateAsset;
use App\Commands\GetAsset;
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
            $message = "<ul><li>" . implode("</li><li>", $e->validator->getMessageBag()->all()) . "</li></ul>";
            $ajaxResponse->setMessage(
                view('partials.custom-message', ['bsClass' => 'danger', 'message' => $message])->render()
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
              ->setMacAddress($this->request->get('asset-mac_address', ''))
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
     * Show a single Asset
     *
     * @GET("/file/asset/{assetId}", as="asset.view", where={"assetId":"[0-9]+"})
     *
     * @param $assetId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showAsset($assetId)
    {
        $command = new GetAsset(intval($assetId));
        $asset   = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($assetId)) {
            return redirect()->back();
        }

        return view('workspaces.asset', ['asset' => $asset]);
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'asset-name'          => 'bail|required',
            'asset-cpe'           => 'bail|nullable|regex:' . Asset::REGEX_CPE,
            'asset-vendor'        => 'bail|nullable|in:' . Asset::getValidOsVendors()->implode(","),
            'asset-ip_address_v4' => 'bail|nullable|ipv4',
            'asset-ip_address_v6' => 'bail|nullable|ipv6',
            'asset-hostname'      => 'bail|nullable|url',
            'asset-mac_address'   => 'bail|nullable|regex:' . Asset::REGEX_MAC_ADDRESS,
            'asset-netbios'       => 'bail|nullable|regex:' . Asset::REGEX_NETBIOS_NAME,
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
            'asset-name.required'      => 'An Asset name is required but it does not seem like you entered one. '
                .'Please try again.',
            'asset-cpe.regex'          => 'The CPE you entered does not seem valid. Please try again.',
            'asset-vendor.in'          => 'The OS vendor you entered does not seem valid. Please try again.',
            'asset-ip_address_v4.ipv4' => 'The IP address v4 you entered does not seem valid. Please try again.',
            'asset-ip_address_v6.ipv6' => 'The IP address v6 you entered does not seem valid. Please try again..',
            'asset-hostname.url'       => 'The hostname you entered does not seem to be a valid URL. Please try again.',
            'asset-mac_address.regex'  => 'The MAC address you entered does not seem valid. Please try again.',
            'asset-netbios.regex'      => 'The NETBIOS name you entered does not seem valid. Please try again.',
        ];
    }
}