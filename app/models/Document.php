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
        parent::save($options);
        return true;
    }


    public function cached_cover() {
        $cover_url = $this->cover;
        $ext = '.jpg';
        return sha1($cover_url) . $ext;
    }

}
