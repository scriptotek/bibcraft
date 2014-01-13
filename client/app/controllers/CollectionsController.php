<?php

class CollectionsController extends BaseController {

	/**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        $collections = Collection::with('documents')->get();
        return View::make('collections.index')
            ->with('title', 'Samlinger')
            ->with('collections', $collections);
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		return View::make('collections.edit')
            ->with('title', 'Ny samling')
            ->with('formData', array(
            	'action' => 'CollectionsController@postStore',
                'method' => 'POST',
                'class' => 'form-horizontal'))
            ->with('collection', new Collection);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postStore()
	{
		$col = new Collection;
		$col->name = Input::get('name');
		$col->save();
		return Redirect::action('CollectionsController@getIndex')
			->with('status', 'Samlingen ble lagret');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getShow($id)
	{
		$col = Collection::with('documents')->find($id);

        return View::make('collections.show')
            ->with('title', $col->title)
            ->with('collection', $col);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEdit($id)
	{
		$col = Collection::find($id);

		return View::make('collections.edit')
            ->with('title', 'Rediger samling')
            ->with('formData', array(
                        'action' => array('CollectionsController@putUpdate', $col->id),
                        'method' => 'PUT',
                        'class' => 'form-horizontal'))
            ->with('collection', $col);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function putUpdate($id)
	{
		$col = Collection::find($id);
		$col->name = Input::get('name');
		$col->save();
		return Redirect::action('CollectionsController@getIndex')
			->with('status', 'Samlingen ble lagret');
	}

	/**
	 * Show the form for deleting the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDelete($id)
	{
		$col = Collection::find($id);

		return View::make('collections.delete')
            ->with('title', 'Slette samling?')
            ->with('collection', $col);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function postDestroy($id)
	{
		$col = Collection::find($id);
		$col->delete();
		return Redirect::action('CollectionsController@getIndex')
			->with('status', 'Samlingen ble slettet');
	}

	/**
     * Add any number of documents to a collection
     *
     * @return Response
     */
    public function postAddToCollection()
    {
        $collection_id = Input::get('collection');
        $collection = Collection::with('documents')->find($collection_id);
        if (!$collection) {
            dd("Samlingen finnes ikke :(");
        }
        $document_ids = $collection->document_ids();

        foreach (Input::all() as $name => $val) {
            $c = explode('_', $name);
            if (count($c) == 2 && $c[0] == 'check') {
                $dok_id = $c[1];
                if (!in_array($dok_id, $document_ids)) {
	                $collection->documents()->attach($dok_id);
                }
            }
        }
        return Redirect::back()
            ->with('status', 'Dokumentet/-ne ble lagt til i samlingen');
    }

	/**
     * Remove any number of documents from a collection
     *
     * @return Response
     */
    public function postRemoveFromCollection($collection_id)
    {
        $collection = Collection::find($collection_id);
        if (!$collection) {
            dd("Samlingen finnes ikke :(");
        }
        $document_ids = $collection->document_ids();

        foreach (Input::all() as $name => $val) {
            $c = explode('_', $name);
            if (count($c) == 2 && $c[0] == 'check') {
                $dok_id = $c[1];
				if (in_array($dok_id, $document_ids)) {
	                $collection->documents()->detach($dok_id);
                }
            }
        }
        return Redirect::back()
            ->with('status', 'Dokumentet/-ne ble fjernet fra samlingen');
    }

}
