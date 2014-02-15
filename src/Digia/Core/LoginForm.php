<?php

namespace Digia\Core;

class LoginForm extends FormEntity 
{
    protected $validationRules = [
        'login_email' => 'required|email',
        'login_password' => 'required',
    ];

    protected $fillable = ['login_email', 'login_password'];

    protected $namespace = 'login';
}
