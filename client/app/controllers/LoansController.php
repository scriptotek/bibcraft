<?php


class LoansController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
    public function getIndex($collectionId = null)
    {
        $collections = array();
        foreach (Collection::all() as $c) {
            $collections[$c->id] = $c->name;
        }

        if ($collectionId) {
            $collection = Collection::find($collectionId);
            if (!$collection) {
                return Response::json(array('error' => 'collection does not exists'));
            }
            $loans = Loan::with('user', 'document', 'document.collections')
            	->orderBy('created_at')
            	->where('collection_id', $collectionId)
            	->get();
        } else {
            $loans = Loan::with('user', 'document','document.collections')
            	->orderBy('created_at')
            	->get();
        }

       

        $content_types = array('application/json', 'text/html');

        $negotiator = new \Negotiation\FormatNegotiator();
        $acceptHeader = $_SERVER['HTTP_ACCEPT'];

        $priorities = array('text/html', 'application/json');
        $format = $negotiator->getBest($acceptHeader, $priorities);

        if ($format->getValue() == 'text/html') {

            return Response::view('loans.index', array(
            	'title' => 'Utlån',
                'loans' => $loans
            ));

        } else if ($format->getValue() == 'application/json') {

            return Response::json(array(
                'loans' => $loans
              ));

        }
    }

    public function getSelect()
    {
    	$loans = array();
        foreach (Input::all() as $name => $val) {
            $c = explode('_', $name);
            if (count($c) == 2 && $c[0] == 'loan') {
                $loans[] = intval($c[1]);
            }
        }

        if (count($loans) === 0) {
            return Redirect::action('LoansController@getIndex')
                ->with('status', 'Ingen lån valgt');
        }

    	if (isset($_GET['extend'])) {
    		return Redirect::to('/loans/extend?loans=' . implode(',', $loans));
    	}

        if (isset($_GET['return'])) {
            return Redirect::to('/loans/return?loans=' . implode(',', $loans));
        }

    	if (isset($_GET['remind'])) {
    		return Redirect::to('/reminders/create?loans=' . implode(',', $loans));
    	}

    	return 'invalid request';
    }

    public function getExtend()
    {
    	$loan_ids = array_map(function($x) {
    		return intval($x);
    	}, explode(',', Input::get('loans')));

        $loans = Loan::with('user')->whereIn('id', $loan_ids)->get();

        return Response::view('loans.extend', array(
        	'title' => 'Forlenge utlån',
            'loans' => $loans,
            'loan_ids' => $loan_ids
        ));
    }

    public function postExtend()
    {
    	$loan_ids = array_map(function($x) {
    		return intval($x);
    	}, explode(',', Input::get('loans')));
        $loans = Loan::with('user')->whereIn('id', $loan_ids)->get();

        $total = count($loans);
        $renewed = 0;
        foreach ($loans as $loan) {
        	if ($loan->renew()) {
        		$renewed++;
        	}
        }

        return Redirect::action('LoansController@getIndex')
            ->with('status', "$renewed av $total lån ble fornyet.");
    }

    public function getReturn()
    {
        $loan_ids = array_map(function($x) {
            return intval($x);
        }, explode(',', Input::get('loans')));

        $loans = Loan::with('user')->whereIn('id', $loan_ids)->get();

        return Response::view('loans.return', array(
            'title' => 'Returnere utlån',
            'loans' => $loans,
            'loan_ids' => $loan_ids
        ));
    }

    public function postReturn()
    {
        $loan_ids = array_map(function($x) {
            return intval($x);
        }, explode(',', Input::get('loans')));
        $loans = Loan::with('user')->whereIn('id', $loan_ids)->get();

        $total = count($loans);
        $returned = 0;
        foreach ($loans as $loan) {
            if ($loan->delete()) {
                $returned++;
            }
        }

        return Redirect::action('LoansController@getIndex')
            ->with('status', "$returned av $total lån ble returnert.");
    }

}
