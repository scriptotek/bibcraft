<?php

if (!extension_loaded('http')) {
    dd("pecl_http is not installed"); // used for content negotiation
}

class UsersController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$users = User::with('loans')->get();

		return Response::view('users.index', array(
			'title' => 'Brukere',
			'users' => $users
		));
	}

	public function getShow($user_id = null)
	{

		if ($user_id) {
			$user = User::with('loans.document')->find($user_id);
		} else if (Input::has('user_id')) {
			$user_id = Input::get('user_id');
			$user = User::with('loans.document')->find($user_id);
		} else if (Input::has('phone')) {
			$phone = Input::get('phone');
			$phone = ($phone[0] == '+') ?: '+47' . $phone;
			$user = User::with('loans.document')->where('phone','=',$phone)->first();
		} else {
			return Response::JSON(array('error' => 'insufficient data'));
		}
		if (!$user) {
			return Response::JSON(array('error' => 'user not found'));
		}

        $content_types = array('application/json', 'text/html');
        $content_type = http_negotiate_content_type($content_types);

        if ($content_type == 'text/html') {

	        return View::make('users.show', array(
	        	'user' => $user
	        ));

        } else if ($content_type == 'application/json') {

			return Response::JSON( $user );
		}

	}

	/**
	 * Store a newly created user in storage.
	 *
	 * @return Response
	 */
	public function postStore()
	{
		$phone = Input::get('phone');
		$phone = ($phone[0] == '+') ?: '+47' . $phone;

		$user = User::where('phone','=',$phone)->first();
		if ($user) {
			return Response::JSON( array('error' => 'eksisterer allerede') );
		}

		$dt = new DateTime();
		date_sub($dt, date_interval_create_from_date_string('40 seconds'));
		//$dt_str = $dt->format('Y-m-d H:i:s');
		$user = User::where('created_at','>', $dt)->first();
		if ($user) {
			return Response::JSON( array('error' => 'maks én ny bruker per 40 sek.') );
		}

		$kode = '';
		for ($i = 0; $i < 4; $i++) {
			$kode .= strval(rand(0,9));
		}

		$user = new User();
		$user->phone = $phone;
		$user->name = Input::get('name');
		$user->activation_code = $kode;
		$user->save();

		// Step 1: Declare new NexmoMessage.
		$nexmo_sms = new NexmoMessage(Config::get('app.nexmo.apitoken'),
									  Config::get('app.nexmo.apisecret'));

		// Step 2: Use sendText( $to, $from, $message ) method to send a message.
		$info = $nexmo_sms->sendText(
			$phone,
			'BibCraft',
			'Hei! Din bekreftelseskode er: ' . $kode
		);

		return Response::JSON( array('error' => '', 'user_id' => $user->id, 'phone' => $phone));
	}

	/**
	 * Request a new activation code
	 *
	 * @return Response
	 */
	public function getNewActivationCode()
	{
		$phone = Input::get('phone');
		$phone = ($phone[0] == '+') ?: '+47' . $phone;

		$user = User::where('phone','=',$phone)->first();
		/*if (!is_null($user->activated_at)) {
			return Response::JSON( array('error' => 'allerede aktivert') );
		}*/

		$kode = '';
		for ($i = 0; $i < 4; $i++) {
			$kode .= strval(rand(0,9));
		}

		$user->activation_code = $kode;
		$user->activated_at = null;
		$user->save();

		// Step 1: Declare new NexmoMessage.
		$nexmo_sms = new NexmoMessage(Config::get('app.nexmo.apitoken'),
									  Config::get('app.nexmo.apisecret'));

		// Step 2: Use sendText( $to, $from, $message ) method to send a message.
		$info = $nexmo_sms->sendText(
			$phone,
			'BibCraft',
			'Hei! Din bekreftelseskode er: ' . $kode
		);

		return Response::JSON( array('error' => '', 'user_id' => $user->id, 'phone' => $phone));
	}

	/**
	 * Validate an activation code
	 * Accepts JSON POST data. Example:
	 *	 {"phone":"10000000", "code":"1234"}
	 *
	 * @return Response
	 */
	public function postActivate()
	{
		$phone = Input::get('phone');
		$phone = ($phone[0] == '+') ?: '+47' . $phone;

		$user = User::where('phone', $phone)->first();
		if (!$user) {
			return Response::JSON(array('error' => 'unknown_user'));
		}

		if (!$user->activate(Input::get('code'))) {
			return Response::JSON(array('error' => $user->activation_error));
		}

		return Response::JSON( array('error' => '', 'user_id' => $user->id) );
	}

	/**
	 * Store a set of new loans for the current user in storage.
	 * Accepts JSON POST data. Example:
	 *	 {"user_id":5,"documents":[1,2]}
	 *
	 * @return Response
	 */
	public function postAddLoans()
	{
		$items = Input::get('documents');
		$docs = array();
		foreach ($items as $item) {
			$doc = Document::find($item);
			if (!$doc) {
				return Response::JSON(array('error' => 'document ' . $item . ' does not exists'));
			}
			$docs[] = $doc;
		}

		$user_id = Input::get('user_id');

		$user = User::find($user_id);
		if (!$user) {
			return Response::JSON(array('error' => 'user does not exists'));
		}
		$response = $user->addLoans($docs);

		return Response::JSON(array('error' => '', 'user_id' => $user_id, 'documents' => $response));
	}

	public function getLoans($id)
	{
		$user = User::with('loans.document')->find($id);
		if (!$user) {
			return Response::JSON(array('error' => 'user does not exists'));
		}
		return Response::JSON(array(
			'user_id' => $id,
			'loans' => $user->loans->toArray()
		));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Show the form for deleting the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDelete($id)
	{
		$user = User::find($id);

		return View::make('users.delete')
			->with('title', 'Slette bruker?')
			->with('user', $user);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function postDestroy($id)
	{
		$user = User::find($id);
		$user->delete();
		return Redirect::action('UsersController@getIndex')
			->with('status', 'Brukeren ble slettet');
	}




	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEdit($id)
	{
		$user = User::find($id);

		return View::make('users.edit')
			->with('title', 'Rediger bruker')
			->with('formData', array(
						'action' => array('UsersController@putUpdate', $user->id),
						'method' => 'PUT',
						'class' => 'form-horizontal'))
			->with('user', $user);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function putUpdate($id)
	{
		$user = User::find($id);
		$user->name = Input::get('name');
		$user->save();
		return Redirect::action('UsersController@getIndex')
			->with('status', 'Brukeren ble lagret');
	}

}
