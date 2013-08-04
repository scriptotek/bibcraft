<?php

if (!extension_loaded('http')) {
    dd("pecl_http is not installed");
}


class ItemsController extends BaseController {

    private $hard_rules = array(
        'recordid' => array('required', 'regex:/^[0-9xX]{9}$/'),
        'isbn' => array('required', 'regex:/^[0-9xX]{10,13}$/'),
        'title' => array('required'),
        'year' => array('required', 'numeric'),
        'cover' => array('url'),
        'url' => array('url')
    );

    private $soft_rules = array(
        'recordid' => array('regex:/^[0-9xX]{9}$/'),
        'isbn' => array('regex:/^[0-9xX]{10,13}$/'),
        'year' => array('numeric'),
        'cover' => array('url'),
        'url' => array('url')
    );

    private $messages = array(
        'recordid' => ':recordid må inneholde et gyldig objektid'
    );

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        $items = Item::all();
        return View::make('items.index')
            ->with('title', 'Objekter')
            ->with('items', $items);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function getCreate()
    {
        return View::make('items.edit')
            ->with('formData', array(
                        'action' => 'ItemsController@postStore', 
                        'method' => 'POST',
                        'class' => 'form-horizontal' 
                    ))
            ->with('title', 'Objekter : Legg til ')
            ->with('item', new Item())
            ->with('isNew', true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postStore()
    {

        $validator = Validator::make(Input::all(), $this->hard_rules, $this->messages);

        if ($validator->fails())
        {
            return Redirect::action('ItemsController@getCreate')
                ->withErrors($validator)
                ->withInput();
        }

        $item = new Item();
        $item->recordid = Input::get('recordid');
        $item->isbn = Input::get('isbn');
        $item->title = Input::get('title');
        $item->subtitle = Input::get('subtitle');
        $item->authors = Input::get('authors');
        $item->year = Input::get('year');
        $item->cover = Input::get('cover');
        $item->body = Input::get('body');
        $item->url = Input::get('url');
        $item->publisher = Input::get('publisher');
        $item->dewey = Input::get('dewey');
        $item->save();

        return Redirect::action('ItemsController@getShow', $item->id)
            ->with('status', 'den blei lagra');
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getLinkout($id)
    {
        $item = Item::find($id);
        
        $visit = new Visit();
        $visit->user_agent = Request::server('HTTP_USER_AGENT');

        $visit = $item->visits()->save($visit);

        return Redirect::to($item->url);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getShow($id)
    {
        $item = Item::find($id);
        $itemUrl = URL::action('ItemsController@getLinkout', $id);
        return View::make('items.show')
            ->with('title', 'Objekter: #' . $item->id)
            ->with('item', $item)
            ->with('itemUrl', $itemUrl);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getEdit($id)
    {
        $item = Item::find($id);
        return View::make('items.edit')
            ->with('title', 'Objekter: #' . $item->id)
            ->with('formData', array(
                        'action' => array('ItemsController@putUpdate', $item->id), 
                        'method' => 'PUT',
                        'class' => 'form-horizontal' 
                    ))
            ->with('item', $item)
            ->with('isNew', false);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function putUpdate($id)
    {

        $validator = Validator::make(Input::all(), $this->soft_rules, $this->messages);

        if ($validator->fails())
        {
            return Redirect::action('ItemsController@getEdit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $item = Item::find($id);

        $fields = array('recordid', 'isbn', 'title', 'subtitle', 'authors',
            'year', 'cover', 'url', 'publisher', 'dewey', 'body');

        foreach ($fields as $field) {
            if (Input::has($field)) {
                $item->$field = Input::get($field);
            }
        }
        $item->save();

        $content_types = array('application/json', 'text/html');
        $content_type = http_negotiate_content_type($content_types);

        if ($content_type == 'text/html') {
            return Redirect::action('ItemsController@getShow', $item->id)
                ->with('status', 'den blei lagra');

        } else if ($content_type == 'application/json') {
            $markdownParser = new MarkdownParser();
            $html = $markdownParser->transformMarkdown($item->body);
            return Response::json(array(
                'item' => $item->toArray(),
                'html' => $html
            ));
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDelete($id)
    {
        $item = Item::find($id);
        return View::make('items.delete')
            ->with('title', 'Objekter: #' . $item->id)
            ->with('item', $item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function deleteDestroy($id)
    {
        $item = Item::find($id);
        $item->delete();

        return Redirect::action('ItemsController@getIndex')
            ->with('status', 'den blei sletta');
    }

    /**
     * Exports the specified resource as pdf.
     *
     * @param  int  $id
     * @return Response
     */
    public function getPdf($id)
    {
        $item = Item::find($id);
        $snappy = new Pdf('/usr/local/bin/xvfb-run-wkhtmltopdf');
        $snappy->setOption('page-size', 'A5');
        //$snappy->setOption('orientation', 'Landscape'); // Landscape or Portrait
        $snappy->setOption('print-media-type', true);

        $output = $snappy->getOutput( URL::action('ItemsController@getShow', $item->id) );

        header('Content-Type: application/pdf');
        //header('Content-Disposition: attachment; filename="file.pdf"');
        echo $output;
        exit();
    }

    function is_image($path)
    {
        $a = getimagesize($path);
        $image_type = $a[2];
         
        if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
        {
            return true;
        }
        return false;
    }

    function isImage($path)
    {
        $a = getimagesize($path);
        $image_type = $a[2];
         
        if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP))) {
            return true;
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

        $item = Item::find($id);
        $cached_cover = $item->cached_cover();

        $cover_path = storage_path() . '/covers/' . $cached_cover;

        if (!file_exists($cover_path)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $item->cover);
            curl_setopt($ch, CURLOPT_USERAGENT, 'UBO Scriptotek Dalek/0.1 (+http://biblionaut.net/bibsys/)');
            curl_setopt($ch, CURLOPT_HEADER, 0); // no headers in the output
            curl_setopt($ch, CURLOPT_REFERER, 'http://ask.bibsys.no');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);         
            curl_close($ch);
            file_put_contents($cover_path, $data);

            // if it's not an image, we just delete it (it might be a 404 page for instance)
            if (!$this->isImage($cover_path)) {
                unlink($cover_path);
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
