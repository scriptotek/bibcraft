<?php

use Illuminate\Support\MessageBag;
use Illuminate\Auth\UserInterface;

class Librarian extends Eloquent implements UserInterface {

	/**
	 * Array of user-editable attributes (excluding machine-generated stuff)
	 *
	 * @static array
	 */
	public static $editableAttributes = array('username', 'password', 'superpowers');

	protected $softDelete = true;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'librarians';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'activation_code');

    /**
     * Columns to be converted to instances of Carbon
     */
    public function getDates()
    {
        return array('created_at', 'updated_at', 'activated_at', 'deleted_at', 'password_changed_at');
    }

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value) {
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName() {
		return 'remember_token';
	}

	/**
	 * Save the model to the database.
	 *
	 * @param  array  $options
	 * @return bool
	 */
	public function save(array $options = array())
	{
		// TODO: Validate
		return parent::save($options);
	}

	/**
	 * Sets a new password. Note that it does *not store the model*.
	 *
	 * @param  string    $password
	 * @param  string    $passwordRepeated
	 * @return bool
	 */
	public function setPassword($password, $passwordRepeated)
	{
		$errors = new MessageBag;
		if (mb_strlen($password) < 8) {
			$errors->add('pwd_tooshort', "Passordet er for kort (kortere enn 8 tegn).");
		}

		if ($password != $passwordRepeated) {
			$errors->add('pwd_unequal', "Du gjentok ikke passordet likt.");
		}

		if ($errors->count() > 0) {
			$this->errors = $errors;
			return false;
		}

		$this->password = Hash::make($password);
		return true;
	}

}
