<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Models\TextWidget;

class SiteController extends Controller
{
    public function about()
    {
        $widget = TextWidget::query()->where('active', '=', true)->where('key','=','about-page')->first();
        if(!$widget){
            throw new NotFoundHttpException();
        }
        return view('about',compact('widget'));
    }
}
