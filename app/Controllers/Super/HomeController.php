<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class HomeController extends BaseController
{
    public function index()
    {
        return view('Back/Home/index');
    }
}
