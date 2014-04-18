<?php

class DocumentsController extends BaseController {

    protected $collection;

    public function __construct(){
        $this->collection = intval(Input::get('collection', Session::get('collection', null)));
        /*$this->collections = explode(',', Input::get('collection', Session::get('collection', null)));
        $this->collections = array_map(function($c) {
            return intval($c);
        }, $this->collections);*/
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  string  $dokid
	 * @return Response
	 */
	public function getShow($id)
	{
        if (is_numeric($id)) {
            $doc = Document::with('loans')
                ->where('id','=',$id)
                ->first();
        } else {
            $doc = Document::with('loans')
                ->where('bibsys_dokid','=',$id)
                ->orWhere('bibsys_knyttid','=',$id)
                ->first();
        }
		if (!$doc) {
			return Response::JSON(array('error' => 'not_found'));
		}
		return Response::JSON($doc);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
    public function getIndex()
    {

        if (!$this->collection) {
            return Redirect::action('CollectionsController@getIndex');
        }

        $allcollections = array();
        foreach (Collection::all() as $c) {
            $allcollections[$c->id] = $c->name;
        }

        $itemsPerPage = Input::get('itemsPerPage', 9999); //, Session::get('itemsPerPage', 9999));

        if ($itemsPerPage) {
            //Session::put('itemsPerPage', $itemsPerPage);
        }

        if (Input::get('page')) {
            //Session::put('page', Input::get('page'));
        }
        if ($this->collection) {
            $collection = Collection::findOrFail($this->collection);

            //$documents = $collection->with('loans')->documents();

            //$collections = Collection::whereIn('id', $this->collections)->get();
            
            // $documents = Document::wherehas('collections', function($q) {
            //     $q->whereIn('collections.id', $this->collections);
            // });

            $documents = $collection->documents()->with('loans');
        } else {
            $documents = Document::with('loans');
        }
        //if ($itemsPerPage !== 9999) {
            $documents = $documents->paginate($itemsPerPage);
       // }

        foreach ($documents as $document) {
            $document->authors = explode(';', $document->authors);
            if (count($document->authors) > 3) {
                $document->authors = implode(', ', array_slice($document->authors, 0, 3)) . ' m.fl.';
            } else {
                $document->authors = implode(', ', $document->authors);
            }
        }

        $content_types = array('application/json', 'text/html');

        $negotiator = new \Negotiation\FormatNegotiator();
        $acceptHeader = $_SERVER['HTTP_ACCEPT'];

        $priorities = array('text/html', 'application/json');
        $format = $negotiator->getBest($acceptHeader, $priorities);

        if ($format->getValue() == 'text/html') {

            return Response::view('documents.index', array(
                'title' => 'Dokumenter',
                'collections' => $allcollections,
                'collection' => $collection,
                //'collection_ids' => join(',', $this->collections),
                'documents' => $documents,
                'from' => $documents->getFrom(),
                'to' => $documents->getTo(),
                'total' => $documents->getTotal()
            ));

        } else if ($format->getValue() == 'application/json') {

            return Response::json(array(
                'currentPage' => $documents->getCurrentPage(),
                'lastPage' => $documents->getLastPage(),
                'from' => $documents->getFrom(),
                'to' => $documents->getTo(),
                'total' => $documents->getTotal(),
                'documents' => $documents->getCollection()->toArray()
            ));

        }
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	// public function postStore()
	// {

	// 	$doc = new Document;

 //        foreach (Document::$fields as $field) {
 //            $doc->$field = Input::get($field) ?: null;
 //        }

 //        if (!$doc->save()) {
 //            return Redirect::back()
 //                ->withErrors($doc->errors)
 //                ->withInput();
 //        }

 //        $collections = Input::get('collections');
 //        $doc->collections()->sync($collections ?: array());

 //        return Redirect::back()
 //            ->with('status', 'Dokumentet ble lagret');
	// }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEdit($id)
	{
        $collections = array();
        foreach (Collection::all() as $c) {
            $collections[$c->id] = $c->name;
        }

        if (!$this->collection) {
            dd('no collection');
        }

        $args = array(
            'formData' => array(
                'action' => array('DocumentsController@putUpdate', $id),
                'method' => 'PUT',
                'class' => 'form-horizontal'
            ),
            'title' => 'Rediger dokument',
            'document' => Document::with('collections')->find($id),
            'collections' => $collections,
            'collection' => $this->collection,
            'isNew' => false
        );
        return Response::view('documents.edit', $args);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function putUpdate($id)
	{
        $doc = Document::find($id);

        foreach (Document::$fields as $field) {
            $doc->$field = Input::get($field) ?: null;
        }

		if (!$doc->save()) {
            return Redirect::back()
                ->withErrors($doc->errors)
                ->withInput();
        }

        $collections = Input::get('collections');
        $doc->collections()->sync($collections ?: array());

        return Redirect::action('DocumentsController@getIndex', array('collection' => $this->collection))
            ->with('status', 'Dokumentet ble lagret');
	}

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDelete($id)
    {
        return Response::view('documents.delete', array(
            'title' => 'Slette dokument?',
            'document' => Document::find($id)
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function deleteDestroy($id)
    {
        $doc = Document::find($id);
        $doc->delete();

        return Redirect::action('DocumentsController@getIndex')
            ->with('status', 'den blei sletta');
    }

    /**
     * Show the form to create a document
     *
     * @return Response
     */
    public function getCreate()
    {
        if ($this->collection) {
            $collection = Collection::find($this->collection);
        }

        return View::make('documents.create')
            ->with('title', 'Registrer dokument')
            ->with('collection', $collection);
    }

    /**
     * Add a document to a collection
     *
     * @param  int  $id
     * @return Response
     */
    public function postStore()
    {

        $collection = Collection::find($this->collection);
        $barcode = strtolower(Input::get('barcode'));

        $doc = Document::where('bibsys_dokid', $barcode)->orWhere('bibsys_knyttid', $barcode)->first();
        if ($doc) {
            if ($doc->collections->contains($collection->id)) {
                return Redirect::action('DocumentsController@getIndex', array('collection' => $collection->id ))
                    ->with('status', 'Dokumentet ligger allerede i samlingen');
            } else {
                $doc->collections()->attach($collection->id);
                return Redirect::action('DocumentsController@getIndex', array('collection' => $collection->id ))
                    ->with('status', 'Dokumentet ble lagt til i samlingen');
            }
        }

        // TODO: Kanskje flytte til modellen?
        $apiUrl = 'http://katapi.biblionaut.net/bibsys/' . $barcode;
        //dd($apiUrl);
        $request = Requests::get($apiUrl, array('Accept' => 'application/json'));
        if ($request->status_code != 200) {
            return Redirect::back()
                ->with('status', 'Fant ikke "' . $barcode . '"" i BIBSYS.');
        }
        $body = json_decode($request->body, true);

        if (isset($body['error'])) {
            return Redirect::back()
                ->with('status', 'Feil: ' . $body['error']);
        }

        $doc = new Document;
        $doc->bibsys_dokid = $body['ids']['dokid'];
        $doc->bibsys_objektid = $body['ids']['objektid'];
        $doc->bibsys_knyttid = $body['ids']['knyttid'] ?: null;
        if (isset($body['isbn']) && count($body['isbn']) != 0) {
            $doc->isbn = $body['isbn'][0];
        }
        if (isset($body['series']) && count($body['series']) != 0) {
            $doc->series = $body['series'][0]['title'];
        }
        $doc->publisher = $body['publisher'];
        $doc->year = intval($body['year']);
        $doc->title = $body['title'];
        $doc->subtitle = $body['subtitle'];
        $doc->cover = isset($body['cover_image']) ? $body['cover_image'] : NULL;


        $doc->authors = implode('; ', array_map(function($author) {
            $name = explode(',', $author['name'], 2);
            return (count($name) == 2) ? $name[1] . ' ' . $name[0] : $name[0];
        }, $body['authors']));
        foreach ($body['classifications'] as $c) {
            if ($c['system'] == 'dewey') {
                $doc->dewey = $c['number'];
            }
        }
        foreach ($body['holdings'] as $h) {
            if ($h['id'] == $doc->bibsys_dokid) {
                $doc->shelvingLocation = $h['shelvinglocation'];
                $doc->callcode = $h['callcode'];
            }
        }

        //dd($doc->json());

        if (!$doc->save()) {
            dd($doc->errors);
        }

        $doc->collections()->attach($collection->id);

        return Redirect::action('DocumentsController@getEdit', array('id' => $doc->id))
            ->with('status', 'Dokumentet ble lagt til i samlingen')
            ->with('collection', $collection->id);
    }

}
