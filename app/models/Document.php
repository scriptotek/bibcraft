<?php

class Document extends Eloquent {
	protected $guarded = array();

    /**
     * Validation errors.
     *
     * @var Illuminate\Support\MessageBag
     */
    public $errors;


    /**
     * List of fields (the one source of truth!)
     *
     * @var array
     */
    public static $fields = array('bibsys_objektid', 'bibsys_dokid', 'bibsys_knyttid',
        'series', 'volume', 'isbn', 'title', 'subtitle', 'authors', 'year', 'cover', 'publisher', 'dewey');

    /**
     * Hard validation rules for humans with brains.
     *
     * @static array
     */
    public static $hard_rules = array(
        'bibsys_objektid' => 'required|regex:/^[0-9xX]{9}$/',
        'bibsys_dokid' => 'required|unique:documents,bibsys_dokid,:id:',
        'bibsys_knyttid' => 'unique:documents,bibsys_knyttid,:id:',
        'isbn' => 'regex:/^[0-9xX]{10,13}$/',
        'title' => 'required',
        'publisher' => 'required',
        'year' => 'required|numeric',
        'cover' => 'url',
    );

    /**
     * Soft validation rules for stupid machines.
     *
     * @static array
     */
    public static $soft_rules = array(
        'bibsys_objektid' => 'required|regex:/^[0-9xX]{9}$/',
        'bibsys_dokid' => 'required|unique:documents,bibsys_dokid,:id:',
        'bibsys_knyttid' => 'unique:documents,bibsys_knyttid,:id:',
        'isbn' => 'unique:objects,isbn,:id:|regex:/^[0-9xX]{10,13}$/',
        'year' => 'numeric',
        'cover' => 'url',
    );

    /**
     * Validation error messages.
     *
     * @static array
     */
    public static $messages = array(
        'isbn.required' => 'ISBN må fylles ut',
        'isbn.unique' => 'ISBN finnes allerede',
        'bibsys_objektid.regex' => 'Objektid må inneholde et gyldig objektid'
    );

	public function loans()
    {
        return $this->hasMany('Loan');
    }

    public function collections()
    {
        return $this->belongsToMany('Collection');
    }

    public function collection_ids()
    {
        $a = array();
        foreach ($this->collections as $c) {
            $a[] = $c->id;
        }
        return $a;
    }

    /*
     * cachedCover as virtual attribute
     */
    public function getCachedCoverAttribute()
    {
        $cover_url = $this->cover;
        $dir = '/covers/';
        if ($cover_url) {
            return $dir . sha1($cover_url) . '.jpg';
        }
        return $dir . 'blank.jpg';
    }

    /*
     * Override toArray to include cachedCover
     */
    public function toArray()
    {
        $array = parent::toArray();
        $array['cachedCover'] = $this->cachedCover;
        return $array;
    }

    /**
     * Process validation rules.
     *
     * @param  array  $rules
     * @return array  $rules
     */
    protected function processRules(array $rules)
    {
        $id = $this->getKey();
        array_walk($rules, function(&$item) use ($id)
        {
            // Replace placeholders
            $item = stripos($item, ':id:') !== false ? str_ireplace(':id:', $id, $item) : $item;
        });

        return $rules;
    }

    /**
     * Validate the model's attributes.
     *
     * @param  array  $rules
     * @param  array  $messages
     * @return bool
     */
    public function validate(array $rules = array(), array $messages = array())
    {
        $rules = $this->processRules($rules ?: static::$hard_rules);
        $messages = $this->processRules($messages ?: static::$messages);

        $v = Validator::make($this->attributes, $rules, $messages);

        if ($v->fails()) {
            $this->errors = $v->messages();
            return false;
        }

        $this->errors = null;
        return true;
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
     * Store cover image from url in cache
     *
     * @param  string  $url
     * @param  string  $path
     * @return boolean
     */
    public function cacheCover($url, $path)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
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
        file_put_contents($path, $data);

        $mime = $this->getMimeType($path);
        if ($mime === false) {

            // if it's not an image, we just delete it
            // (might be a 404 page for instance)
            unlink($path);
            return false;

        } else if ($mime !== 'image/jpeg') {

            rename("$path", "$path.1");
            $image = new Imagick("$path.1");
            $image->setImageFormat('jpg');
            $image->writeImage("$path");
            unlink("$path.1");

        }

        return true;
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = array())
    {
        if (!$this->validate(static::$hard_rules)) {
            return false;
        }
        if (!$this->exists) {
            //Log::info('Opprettet nytt object: ' . $this->name);
        } else {
            //Log::info('Oppdaterte tingen: ' . $this->name);
        }

        if ($this->isDirty('cover')) {
            if ($this->cover) {
                $path = public_path() . '/covers/' . sha1($this->cover) . '.jpg';
                $this->cacheCover($this->cover, $path);
            }
        }

        parent::save($options);
        return true;
    }

}
