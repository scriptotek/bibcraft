<?php

class RemindersController extends BaseController {

	/**
	 * Display a form to create the resource.
	 *
	 * @return Response
	 */
	public function getCreate()
	{		
		$users = array();
		$reminders = array();
		$tmp = array();

        $loan_ids = array_map(function($x) {
            return intval($x);
        }, explode(',', Input::get('loans')));

        $loans = Loan::with('user')->whereIn('id', $loan_ids)->get();

        $users = array();

        foreach ($loans as $loan) {
        	$u = $loan->user;
        	if (isset($tmp[$u->id])) {
        		$tmp[$u->id][] = $loan;
        	} else {
        		$tmp[$u->id] = array($loan);        		
        	}
        }

        $j = 0;
        foreach ($tmp as $u => $loans) {

        	$loan_ids = array();
        	foreach ($loans as $loan) {
        		$loan_ids[] = $loan->id;
        	}
        	$loan_ids = implode(',', $loan_ids);
        	
        	$msg = 'Hei. Nå er det på tide å levere ';
 			if (count($loans) == 1) {
 				$msg .= '"' . $loans[0]->document->title . '"';
 			} else if (count($loans) == 2) {
 				$msg .= '"' . $loans[0]->document->title . '" og "' . $loans[1]->document->title . '"';
 			} else {
 				$msg .= 'de ' . count($loans) . ' dokumentene du har lånt';
 			}
 			$msg .= ' tilbake til Realfagsbiblioteket. Lurer du på noe? Spør realfagsbiblioteket@ub.uio.no';

 			$j++;
        	$reminders[] = array(
        		'idx' => $j,
        		'user' => $loans[0]->user,
        		'loans' => $loans,
        		'loan_ids' => $loan_ids,
        		'msg' => $msg
        	);
        }

		return Response::view('reminders.create', array(
			'title' => 'Forhåndsvis påminnelser',
			'reminders' => $reminders
		));
	}

	/**
	 * Stores a new reminder.
	 *
	 * @return Response
	 */
	public function postStore()
	{
		$reminders = array();

		foreach (Input::all() as $name => $val) {
            $c = explode('_', $name);

            if (count($c) == 2) {
            	$idx = intval($c[1]);
            	if (!isset($reminders[$idx])) $reminders[$idx] = array();
            	if ($c[0] == 'loans') {
	            	$reminders[$idx]['loans'] = explode(',', $val);
            	} else if ($c[0] == 'msg') {
	            	$reminders[$idx]['msg'] = $val;
            	}
            }
        }

        foreach ($reminders as $key => $reminder) {

        	$l = Loan::with('user')->find($reminder['loans'][0]);
        	$msgRecpt = $l->user->phone;
        	$msgBody = $reminder['msg'];

        	// sendSms($msgRecpt, $msgBody);
        	// Step 1: Declare new NexmoMessage.
			$nexmo_sms = new NexmoMessage(Config::get('app.nexmo.apitoken'),
										  Config::get('app.nexmo.apisecret'));

			// Step 2: Use sendText( $to, $from, $message ) method to send a message.
			$info = $nexmo_sms->sendText(
				$msgRecpt,
				'Realfagsbib',
				$msgBody
			);


        	$r = new Reminder;
        	$r->msg = $msgBody;
	        $r->save();

	        foreach ($reminder['loans'] as $loan) {
	        	$r->loans()->attach($loan);
	        }

        }

        return Redirect::action('LoansController@getIndex')
            ->with('status', 'Påminnelser ble sendt');
	}

}

