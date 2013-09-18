<?php

if (!extension_loaded('http')) {
    dd("pecl_http is not installed"); // used for content negotiation
}

class DocumentsController extends BaseController {

	/**
	 * Display the specified resource.
	 *
	 * @param  string  $dokid
	 * @return Response
	 */
	public function getShow($id)
	{
		$doc = Document::with('loans')->where('id','=',$id)->orWhere('bibsys_dokid','=',$id)->orWhere('bibsys_knyttid','=',$id)->first();
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

        $itemsPerPage = Input::get('itemsPerPage', 10);

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

        if ($content_type == 'text/html') {

            return Response::view('documents.index', array(
                'title' => 'Dokumenter',
                'collections' => $collections,
                'collection' => $collection,
                'documents' => $documents,
                'from' => $documents->getFrom(),
                'to' => $documents->getTo(),
                'total' => $documents->getTotal()
            ));

        } else if ($content_type == 'application/json') {

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

     /**
     * Returns the image mime type for a given file,
     * or false if the file is not an image.
     *
     * @param  string  $path
     * @return string
     */
    function getMimeType($path)
    {
        $a = getimagesize($path);
        $image_type = $a[2];

        if(in_array($image_type, array(IMAGETYPE_GIF,
                                       IMAGETYPE_JPEG,
                                       IMAGETYPE_PNG,
                                       IMAGETYPE_BMP,
                                       IMAGETYPE_TIFF_II,
                                       IMAGETYPE_TIFF_MM)))
        {
            return image_type_to_mime_type($image_type);
        }
        return false;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getCover($id)
    {

        $doc = Document::find($id);
        $cached_cover = $doc->cached_cover();

        $cover_path = storage_path() . '/covers/' . $cached_cover;

        if (!file_exists($cover_path) && $doc->cover) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $doc->cover);
            curl_setopt($ch, CURLOPT_USERAGENT, 'UBO Scriptotek Dalek/0.1 (+http://biblionaut.net/bibsys/)');
            //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.65 Safari/537.36');
            curl_setopt($ch, CURLOPT_HEADER, 0); // no headers in the output
            curl_setopt($ch, CURLOPT_REFERER, 'http://ask.bibsys.no');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            curl_close($ch);
            file_put_contents($cover_path, $data);

            $mime = $this->getMimeType($cover_path);
            if ($mime === false) {

                // if it's not an image, we just delete it
                // (might be a 404 page for instance)
                unlink($cover_path);

            } else if ($mime !== 'image/jpeg') {

                rename("$cover_path", "$cover_path.1");
                $image = new Imagick("$cover_path.1");
                $image->setImageFormat('jpg');
                $image->writeImage("$cover_path");
                unlink("$cover_path.1");

            }

        }

        if (file_exists($cover_path)) {
            $cover_data = file_get_contents($cover_path);
        } else {
            $cover_path = storage_path() . '/covers/blank.jpg';
            $cover_data = file_get_contents($cover_path);
        }

        header('Content-Type: image/jpeg');
        print $cover_data;
        exit();

    }

}
