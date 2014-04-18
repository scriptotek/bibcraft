<?php

use Carbon\Carbon;

class LibrariansController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$users = Librarian::get();

		return Response::view('librarians.index', array(
			'title' => 'Bibliotekarer',
			'librarians' => $users
		));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		return Response::view('librarians.create', array(
			'title' => 'Opprett ny bibliotekar'
		));
    }

	public function getLogin()
	{
		if (Auth::check()) {
			return Redirect::intended('/');
		}
		return Response::view('librarians/login', array(
			'status' => Session::get('status'),
		));
	}

	public function postLogin()
	{

		$credentials = array(
			'username' => Input::get('username'),
			'password' => Input::get('password')
		);

		if (Auth::attempt($credentials, true)) {
			return Redirect::intended('/');
		} else {
			return Redirect::back()
				->withInput()
				->with('loginfailed', true);
		}
	}

	public function getLogout()
	{
		Auth::logout();
		return Redirect::to('/');
	}

	public function getEdit()
	{
		$user = Auth::user();
		return Response::view('librarians.edit', array(
			'title' => 'Konto',
			'user' => $user
		));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postStore()
	{
		$lib = new Librarian;
		$lib->name = Input::get('name');
		$lib->username = Input::get('email');
		$lib->activation_code = substr( 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand( 0 ,50 ), 1 ) .substr( md5( time() ), 1);
		$lib->save();

		Mail::send(array('emails.welcome_html', 'emails.welcome_text'), array(
			'token' => $lib->activation_code
		), function($message) use ($lib)
		{
		    $message->to($lib->username, $lib->name)->subject('Velkommen til Bibcraft');
		});

		return Redirect::action('LibrariansController@getIndex')
			->with('status', 'Bibliotekaren ble opprettet.');
	}

	public function getActivate($token='')
	{
		$user = Librarian::where('activation_code', $token)->first();
		if (!$user) {
			App::abort(400, 'Invalid activation code.');
		}
		if ($user->activated_at) {
			App::abort(400, 'Already activated.');			
		}
		return Response::view('librarians.activate', array(
			'title' => 'Aktiver konto',
			'token' => $token,
			'user' => $user
		));
	}

	public function postActivate()
	{
		$token = Input::get('activation_code');
		$user = Librarian::where('activation_code', $token)->first();
		if (!$user) {
			App::abort(400, 'Invalid activation code.');
		}
		if ($user->activated_at) {
			App::abort(400, 'Already activated.');			
		}

		$pwd = Input::get('password');
		$pwd2 = Input::get('password2');

		if ($pwd != $pwd2) {
			return Redirect::back()->with('status', 'Passordene var ikke like. Prøv igjen.');			
		}
		if (strlen($pwd) < 8) {
			return Redirect::back()->with('status', 'Minst åtte tegn daaaa. Prøv igjen');			
		}

		$user->activated_at = Carbon::now();
		$user->password_changed_at = Carbon::now();
		$user->password = Hash::make($pwd);
		$user->save();
		return Redirect::action('CollectionsController@getIndex')
			->with('status', 'Bravo – Kontoen er aktivert!');
	}

	/**
     * Show the form for deleting the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDelete($id)
    {
        # TODO
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function postDestroy($id)
    {
        # TODO
    }

}
