<?php

namespace Digia\Core;

class UserForm extends FormEntity
{
    protected $namespace = 'user';

    protected $fillable = [
        'user_id', 'user_email', 'user_password',
    ];

}
