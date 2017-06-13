<?php
namespace App\Http\ViewComposers;

use App\Handlers\PageTitlesHandler;
use App\Models\Lang\Language;
use App\Models\User\Decorators\UserCacheDecorator;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Handlers\CloudinaryImageHandler;


class AllViewsComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */

    protected $handler;

    /**
     * AllViewsComposer constructor.
     * @param $handler
     */
    public function __construct(CloudinaryImageHandler $handler)
    {
        $this->handler = $handler;
    }

    public function compose(View $view)
    {
        $view->with('currentUser', auth()->user());

        $cors_location = null;
        if(isset($_SERVER["HTTP_HOST"])){
            if (array_key_exists('REQUEST_SCHEME', $_SERVER)) {
                $cors_location = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"] .
                    dirname($_SERVER["SCRIPT_NAME"]) . "/cloudinary_cors.html";
            } else {
                $cors_location = "http://" . $_SERVER["HTTP_HOST"] . "/cloudinary_cors.html";
            }
        }

        $view->with('cors_location', $cors_location);
        $view->with('cloudinaryJsConfig', $this->handler->getJsConfig());
    }
}