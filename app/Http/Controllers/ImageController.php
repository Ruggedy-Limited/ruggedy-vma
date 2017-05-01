<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Storage;

/**
 * @Middleware("web")
 */
class ImageController extends Controller
{
    /**
     * Get a POC Image
     *
     * @GET("/poc-images/{slug}", as="get.poc-image", where={"slug":"[A-Za-z0-9_\.\-/]+"})
     *
     * @param $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $imagePath = ImageService::pocPath() . $slug;
        $disk      = ImageService::disk();
        if (!Storage::disk($disk)->exists($imagePath)) {
            return response()->make("Image not found.", 404);
        }

        $mimeType = Storage::disk($disk)->mimeType($imagePath);
        $contents = Storage::disk($disk)->get($imagePath);
        return response()->make($contents, 200, ['content-type' => $mimeType]);
    }
}