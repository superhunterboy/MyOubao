<?php
use Zizaco\FactoryMuff\Facade\FactoryMuff;

class UserTest extends TestCase
{
    /**
     * Username is required
     */
    public function testUsernameIsRequired()
    {
      // Create a new User
      $user = new User;
      $user->username = 'User_1';
      $user->email = "phil@ipbrown.com";
      $user->password = "password";
      $user->password_confirmation = "password";
      // $user = FactoryMuff::create('User');

      // User should not save
      $this->assertFalse($user->save());

      // Save the errors
      $errors = $user->errors()->all();

      // There should be 1 error
      $this->assertCount(1, $errors);

      // The username error should be set
      $this->assertEquals($errors[0], "username 已经有人使用。");
    }
}