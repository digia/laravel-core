<?php

namespace spec\Digia\Core;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UserFormSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Digia\Core\UserForm');
    }

    function it_should_load_input()
    {
        $load = ['test'];

        $this->load($load);

        $this->getInput()->shouldBe($load);
    }

    function it_should_have_attributes()
    {
        $load = ['user_email' => 'email@email.com'];

        $this->load($load);

        $this->shouldHaveAttributes();
    }

    function it_should_get_attributes()
    {
        $load = ['user_email' => 'email@email.com'];

        $this->load($load);

        $this->getAttributes()->shouldBe(['email' => 'email@email.com']);
    }

    function it_should_have_id()
    {
        $load = ['user_id' => 1];

        $this->load($load);

        $this->shouldHaveId();
    }

    function it_should_get_id()
    {
        $load = ['user_id' => 1];

        $this->load($load);

        $this->getId()->shouldBe(1);
    } 
}
