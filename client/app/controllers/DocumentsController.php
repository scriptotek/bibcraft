<?php

class DocumentsController extends BaseController {

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
    public function getIndex($collectionId = null)
    {
        $collections = array();
        foreach (Collection::all() as $c) {
            $collections[$c->id] = $c->name;
        }


        $itemsPerPage = Input::get('itemsPerPage', Session::get('itemsPerPage', 10));

        if ($itemsPerPage) {
            Session::put('itemsPerPage', $itemsPerPage);
        }

        if (Input::get('page')) {
            Session::put('page', Input::get('page'));
        }

        if ($collectionId) {
            $collection = Collection::find($collectionId);
            if (!$collection) {
                return Response::json(array('error' => 'collection does not exists'));
            }
            $documents = $collection->documents()->with('loans')->paginate($itemsPerPage);
        } else {
            $documents = Document::with('loans')->paginate($itemsPerPage);
            $collection = null;
        }

        $content_types = array('application/json', 'text/html');
        $content_type = http_negotiate_content_type($content_types);

        $negotiator = new \Negotiation\FormatNegotiator();
        $acceptHeader = $_SERVER['HTTP_ACCEPT'];

        $priorities = array('text/html', 'application/json');
        $format = $negotiator->getBest($acceptHeader, $priorities);

        if ($format->getValue() == 'text/html') {

            return Response::view('documents.index', array(
                'title' => 'Dokumenter',
                'collections' => $collections,
                'collection' => $collection,
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
                'collection' => $collection,
                'documents' => $documents->getCollection()->toArray()
                ));

        }
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
        $collections = array();
        foreach (Collection::all() as $c) {
            $collections[$c->id] = $c->name;
        }

        $args = array(
            'formData' => array(
                'action' => 'DocumentsController@postStore',
                'method' => 'POST',
                'class' => 'form-horizontal'
            ),
            'title' => 'Dokumenter : Legg til ',
            'document' => new Document,
            'collections' => $collections,
            'collection' => Input::get('collection'),
            'isNew' => true
        );
		return Response::view('documents.edit', $args);
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postStore()
	{

		$doc = new Document;

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

        return Redirect::back()
            ->with('status', 'Dokumentet ble lagret');
	}

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

        $args = array(
            'formData' => array(
                'action' => array('DocumentsController@putUpdate', $id),
                'method' => 'PUT',
                'class' => 'form-horizontal'
            ),
            'title' => 'Rediger dokument',
            'document' => Document::with('collections')->find($id),
            'collections' => $collections,
            'collection' => Input::get('collection'),
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

        return Redirect::action('DocumentsController@getIndex')
            ->with('status', 'den blei lagra');
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

}
