<?php

namespace App\Http\Controllers;

use App\Helpers\libraries\TelegramBot;
use App\Helpers\TelegramHelpers;
use App\Models\Report;
use App\Models\User;
use App\Models\UserWaitAccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    public function index(Request $request)
    {

    }
}
